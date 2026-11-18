<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{string}>
     */
    public static function adminPages(): array
    {
        return [
            'dashboard' => ['/admin'],
            'projects' => ['/admin/projects'],
            'create project' => ['/admin/projects/create'],
            'experiences' => ['/admin/experiences'],
            'skills' => ['/admin/skills'],
            'skill categories' => ['/admin/skill-categories'],
            'technologies' => ['/admin/technologies'],
            'categories' => ['/admin/categories'],
            'services' => ['/admin/services'],
            'credentials' => ['/admin/credentials'],
            'social links' => ['/admin/social-links'],
            'messages' => ['/admin/contact-messages'],
            'profile' => ['/admin/profile'],
            'settings' => ['/admin/settings'],
        ];
    }

    #[DataProvider('adminPages')]
    public function test_guests_are_redirected_to_the_login_page(string $uri): void
    {
        $this->get($uri)->assertRedirect('/admin/login');
    }

    #[DataProvider('adminPages')]
    public function test_non_admin_users_are_forbidden(string $uri): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get($uri)
            ->assertForbidden();
    }

    #[DataProvider('adminPages')]
    public function test_admins_can_open_every_admin_page(string $uri): void
    {
        $this->signInAsAdmin();

        $this->get($uri)->assertOk();
    }

    public function test_login_page_is_available_and_not_indexed(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_public_registration_and_password_reset_are_not_exposed(): void
    {
        $this->assertFalse(Route::has('filament.admin.auth.register'));
        $this->assertFalse(Route::has('filament.admin.auth.password-reset.request'));
        $this->get('/admin/register')->assertNotFound();
    }

    public function test_admin_command_creates_an_administrator(): void
    {
        $this->artisan('portfolio:make-admin', [
            '--name' => 'Owner',
            '--email' => 'owner@example.com',
            '--password' => 'correct-horse-battery-9',
        ])->assertSuccessful();

        $this->assertTrue(User::query()->where('email', 'owner@example.com')->value('is_admin'));
    }

    public function test_admin_command_rejects_weak_passwords(): void
    {
        $this->artisan('portfolio:make-admin', [
            '--name' => 'Owner',
            '--email' => 'owner@example.com',
            '--password' => 'short',
        ])->assertFailed();

        $this->assertDatabaseMissing('users', ['email' => 'owner@example.com']);
    }
}
