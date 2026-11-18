<?php

namespace Tests\Feature;

use App\Http\Requests\ContactMessageRequest;
use App\Models\ContactMessage;
use App\Models\SiteSetting;
use App\Notifications\NewContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return [
            'name' => 'Jane Visitor',
            'email' => 'jane@example.com',
            'subject' => 'Project inquiry',
            'message' => 'Hello! I would like to discuss a Laravel API project.',
            ContactMessageRequest::TIMESTAMP => Crypt::encryptString((string) now()->subMinute()->timestamp),
            ...$overrides,
        ];
    }

    public function test_a_valid_message_is_stored_and_the_admin_is_notified(): void
    {
        Notification::fake();
        SiteSetting::current()->update(['contact_notification_email' => 'owner@example.com']);

        $this->postJson('/contact', $this->payload())
            ->assertOk()
            ->assertJsonPath('message', fn (string $message) => str_contains($message, 'received'));

        $message = ContactMessage::query()->sole();
        $this->assertSame('Jane Visitor', $message->name);
        $this->assertTrue($message->isUnread());

        Notification::assertSentTo(
            new AnonymousNotifiable,
            NewContactMessage::class,
            fn (NewContactMessage $notification, array $channels, AnonymousNotifiable $notifiable) => $notifiable->routes['mail'] === 'owner@example.com'
                && $notification->contactMessage->is($message),
        );
    }

    public function test_no_notification_is_sent_when_no_recipient_is_configured(): void
    {
        Notification::fake();

        $this->postJson('/contact', $this->payload())->assertOk();

        $this->assertDatabaseCount('contact_messages', 1);
        Notification::assertNothingSent();
    }

    public function test_regular_form_posts_redirect_back_to_the_contact_section(): void
    {
        $this->from('/')
            ->post('/contact', $this->payload())
            ->assertRedirect(route('home').'#contact')
            ->assertSessionHas('contact_status');
    }

    public function test_invalid_submissions_are_rejected(): void
    {
        $this->postJson('/contact', $this->payload(['name' => '', 'email' => 'not-an-email', 'message' => 'short']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'message']);

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_html_is_stripped_from_submissions(): void
    {
        $this->postJson('/contact', $this->payload(['message' => '<b>Bold</b> request <script>x</script> for a project']))->assertOk();

        $this->assertSame('Bold request x for a project', ContactMessage::query()->sole()->message);
    }

    public function test_honeypot_submissions_are_silently_discarded(): void
    {
        $this->postJson('/contact', $this->payload([ContactMessageRequest::HONEYPOT => 'https://spam.example']))->assertOk();

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_instant_submissions_are_treated_as_bots(): void
    {
        $this->postJson('/contact', $this->payload([ContactMessageRequest::TIMESTAMP => Crypt::encryptString((string) now()->timestamp)]))->assertOk();

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_tampered_tokens_are_treated_as_bots(): void
    {
        $this->postJson('/contact', $this->payload([ContactMessageRequest::TIMESTAMP => 'forged']))->assertOk();

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_submissions_are_rate_limited(): void
    {
        for ($attempt = 1; $attempt <= 3; $attempt++) {
            $this->postJson('/contact', $this->payload())->assertOk();
        }

        $this->postJson('/contact', $this->payload())->assertTooManyRequests();
        $this->assertDatabaseCount('contact_messages', 3);
    }

    public function test_the_form_can_be_disabled_from_settings(): void
    {
        SiteSetting::current()->update(['show_contact_form' => false]);

        $this->get('/')->assertDontSee('action="'.route('contact.store').'"', false);
        $this->postJson('/contact', $this->payload())->assertNotFound();
    }

    public function test_the_form_is_protected_by_csrf_middleware(): void
    {
        $this->assertContains('web', app('router')->getRoutes()->getByName('contact.store')->gatherMiddleware());
    }
}
