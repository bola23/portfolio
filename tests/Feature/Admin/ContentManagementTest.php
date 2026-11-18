<?php

namespace Tests\Feature\Admin;

use App\Enums\MessageStatus;
use App\Filament\Pages\ManageProfile;
use App\Filament\Pages\ManageSiteSettings;
use App\Filament\Resources\ContactMessages\Pages\ListContactMessages;
use App\Filament\Resources\ContactMessages\Pages\ViewContactMessage;
use App\Filament\Resources\Experiences\Pages\CreateExperience;
use App\Filament\Resources\Services\Pages\ManageServices;
use App\Filament\Resources\SkillCategories\Pages\ManageSkillCategories;
use App\Filament\Resources\Skills\Pages\ManageSkills;
use App\Filament\Widgets\PortfolioStatsOverview;
use App\Models\ContactMessage;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use App\Models\SkillCategory;
use App\Models\Technology;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->signInAsAdmin();
    }

    public function test_dashboard_stats_are_computed_from_the_database(): void
    {
        Project::factory()->count(2)->create();
        Project::factory()->draft()->create();
        Skill::factory()->count(4)->create();
        ContactMessage::factory()->count(2)->create();
        ContactMessage::factory()->read()->create();

        Livewire::test(PortfolioStatsOverview::class)
            ->assertSeeInOrder(['Total projects', '3', 'Published projects', '2', 'Draft projects', '1', 'Skills', '4'])
            ->assertSeeInOrder(['Unread messages', '2']);
    }

    public function test_profile_changes_update_the_hero_and_about_sections(): void
    {
        $this->get('/')->assertOk();

        Livewire::test(ManageProfile::class)
            ->fillForm([
                'name' => 'Poula Adel',
                'headline' => 'Principal Backend Engineer',
                'short_intro' => 'Updated introduction from the dashboard.',
                'years_of_experience' => 6,
                'availability_status' => 'freelance',
                'expertise_areas' => ['Hospital management systems'],
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $this->assertSame('Principal Backend Engineer', Profile::query()->sole()->headline);

        $this->get('/')
            ->assertSee('Principal Backend Engineer')
            ->assertSee('Updated introduction from the dashboard.')
            ->assertSee('Available for freelance work')
            ->assertSee('Hospital management systems');
    }

    public function test_site_settings_update_branding_and_seo(): void
    {
        Livewire::test(ManageSiteSettings::class)
            ->fillForm([
                'site_name' => 'Demo Brand',
                'primary_color' => '#22C55E',
                'accent_color' => '#3B82F6',
                'meta_title' => 'Demo SEO Title',
                'meta_description' => 'Demo meta description.',
                'show_contact_form' => true,
                'default_theme' => 'dark',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->get('/')
            ->assertSee('<title>Demo SEO Title</title>', false)
            ->assertSee('content="Demo meta description."', false)
            ->assertSee('--brand-primary: #22C55E', false)
            ->assertSee("var fallback = 'dark'", false);
    }

    public function test_site_settings_reject_invalid_colors(): void
    {
        Livewire::test(ManageSiteSettings::class)
            ->fillForm(['site_name' => 'Demo', 'primary_color' => 'red; } body { display:none', 'accent_color' => '#3B82F6'])
            ->call('save')
            ->assertHasFormErrors(['primary_color']);
    }

    public function test_experience_records_can_be_created_with_technologies(): void
    {
        $technology = Technology::factory()->create(['name' => 'Laravel']);

        Livewire::test(CreateExperience::class)
            ->fillForm([
                'company' => 'Demo Company',
                'position' => 'Backend Developer',
                'start_date' => '2024-01-01',
                'is_current' => true,
                'responsibilities' => [['responsibility' => 'Built APIs']],
                'technologies' => [$technology->id],
                'is_visible' => true,
                'sort_order' => 0,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $experience = Experience::query()->sole();
        $this->assertNull($experience->end_date);
        $this->assertTrue($experience->technologies->contains($technology));

        $this->get('/')->assertSee('Demo Company')->assertSee('Built APIs')->assertSee('Present');
    }

    public function test_experience_end_date_is_required_unless_current(): void
    {
        Livewire::test(CreateExperience::class)
            ->fillForm(['company' => 'Demo', 'position' => 'Dev', 'start_date' => '2024-01-01', 'is_current' => false, 'end_date' => null])
            ->call('create')
            ->assertHasFormErrors(['end_date' => 'required']);
    }

    public function test_skills_can_be_created_hidden_and_deleted(): void
    {
        $category = SkillCategory::factory()->create(['name' => 'Backend Development']);

        Livewire::test(ManageSkills::class)
            ->callAction(TestAction::make('create'), [
                'name' => 'Demo Eloquent',
                'skill_category_id' => $category->id,
                'icon' => 'laravel',
                'proficiency' => 90,
                'sort_order' => 1,
                'is_visible' => true,
            ])
            ->assertHasNoFormErrors();

        $skill = Skill::query()->sole();
        $this->get('/')->assertSee('Demo Eloquent');

        $skill->update(['is_visible' => false]);
        $this->get('/')->assertDontSee('Demo Eloquent');

        Livewire::test(ManageSkills::class)->callAction(TestAction::make('delete')->table($skill));
        $this->assertModelMissing($skill);
    }

    public function test_skill_categories_and_services_crud(): void
    {
        Livewire::test(ManageSkillCategories::class)
            ->callAction(TestAction::make('create'), ['name' => 'Integrations', 'slug' => 'integrations', 'sort_order' => 0])
            ->assertHasNoFormErrors();
        $this->assertDatabaseHas('skill_categories', ['slug' => 'integrations']);

        Livewire::test(ManageServices::class)
            ->callAction(TestAction::make('create'), [
                'title' => 'REST API Development',
                'slug' => 'rest-api-development',
                'summary' => 'Versioned APIs for web and mobile.',
                'deliverables' => ['API design'],
                'icon' => 'ui-api',
                'sort_order' => 0,
                'is_visible' => true,
            ])
            ->assertHasNoFormErrors();

        $service = Service::query()->sole();
        $this->get('/')->assertSee('REST API Development')->assertSee('API design');

        Livewire::test(ManageServices::class)
            ->callAction(TestAction::make('edit')->table($service), ['title' => 'API Engineering'])
            ->assertHasNoFormErrors();
        $this->assertSame('API Engineering', $service->fresh()->title);
    }

    public function test_icon_picker_rejects_unknown_keys(): void
    {
        Livewire::test(ManageServices::class)
            ->callAction(TestAction::make('create'), [
                'title' => 'Demo', 'slug' => 'demo', 'summary' => 'Demo summary', 'icon' => 'definitely-not-an-icon', 'sort_order' => 0,
            ])
            ->assertHasFormErrors(['icon']);
    }

    public function test_opening_a_message_marks_it_as_read(): void
    {
        $message = ContactMessage::factory()->create(['subject' => 'Demo inquiry']);

        Livewire::test(ViewContactMessage::class, ['record' => $message->getRouteKey()])
            ->assertOk()
            ->assertSee('Demo inquiry');

        $this->assertFalse($message->fresh()->isUnread());
        $this->assertSame(MessageStatus::Read, $message->fresh()->status);
    }

    public function test_messages_can_be_marked_unread_status_changed_and_deleted(): void
    {
        $message = ContactMessage::factory()->read()->create();

        Livewire::test(ListContactMessages::class)
            ->callAction(TestAction::make('markUnread')->table($message));
        $this->assertTrue($message->fresh()->isUnread());

        Livewire::test(ListContactMessages::class)
            ->callAction(TestAction::make('changeStatus')->table($message), ['status' => MessageStatus::Replied->value]);
        $this->assertSame(MessageStatus::Replied, $message->fresh()->status);
        $this->assertFalse($message->fresh()->isUnread());

        Livewire::test(ListContactMessages::class)
            ->callAction(TestAction::make('delete')->table($message));
        $this->assertModelMissing($message);
    }

    public function test_messages_cannot_be_created_from_the_admin(): void
    {
        $this->get('/admin/contact-messages/create')->assertNotFound();
    }
}
