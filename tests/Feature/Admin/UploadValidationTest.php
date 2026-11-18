<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\ManageProfile;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Skills\Pages\ManageSkills;
use App\Models\Profile;
use App\Models\SkillCategory;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UploadValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->signInAsAdmin();
    }

    /**
     * @return array<string, array{\Closure(): UploadedFile}>
     */
    public static function rejectedImages(): array
    {
        return [
            'svg (script injection risk)' => [fn () => UploadedFile::fake()->createWithContent('logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>')],
            'php file' => [fn () => UploadedFile::fake()->createWithContent('shell.php', '<?php echo "pwned";')],
            'php disguised as jpg' => [fn () => UploadedFile::fake()->createWithContent('shell.jpg', '<?php echo "pwned";')],
            'pdf' => [fn () => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf')],
            'oversized image' => [fn () => UploadedFile::fake()->image('huge.jpg')->size(6000)],
        ];
    }

    /**
     * @param  \Closure(): UploadedFile  $file
     */
    #[DataProvider('rejectedImages')]
    public function test_project_thumbnails_only_accept_safe_raster_images(\Closure $file): void
    {
        Livewire::test(CreateProject::class)
            ->fillForm([
                'title' => 'Demo Upload',
                'slug' => 'demo-upload',
                'excerpt' => 'Upload validation',
                'thumbnail' => $file(),
            ])
            ->call('create')
            ->assertHasFormErrors(['thumbnail']);

        $this->assertDatabaseMissing('projects', ['slug' => 'demo-upload']);
    }

    public function test_valid_images_are_accepted(): void
    {
        Livewire::test(CreateProject::class)
            ->fillForm([
                'title' => 'Demo Upload',
                'slug' => 'demo-upload',
                'excerpt' => 'Upload validation',
                'thumbnail' => UploadedFile::fake()->image('cover.png', 800, 500),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('projects', ['slug' => 'demo-upload']);
    }

    public function test_skill_icons_reject_svg_uploads(): void
    {
        $category = SkillCategory::factory()->create();

        Livewire::test(ManageSkills::class)
            ->callAction(TestAction::make('create'), [
                'name' => 'Demo Skill',
                'skill_category_id' => $category->id,
                'icon_path' => UploadedFile::fake()->createWithContent('icon.svg', '<svg xmlns="http://www.w3.org/2000/svg"></svg>'),
                'sort_order' => 0,
            ])
            ->assertHasFormErrors(['icon_path']);
    }

    public function test_cv_must_be_a_pdf(): void
    {
        Livewire::test(ManageProfile::class)
            ->fillForm(['cv_path' => UploadedFile::fake()->create('cv.docx', 100, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')])
            ->call('save')
            ->assertHasFormErrors(['cv_path']);
    }

    public function test_cv_pdf_upload_is_downloadable_from_the_public_site(): void
    {
        Livewire::test(ManageProfile::class)
            ->fillForm([
                'name' => 'Poula Adel',
                'headline' => 'Senior Full Stack Laravel Developer',
                'years_of_experience' => 5,
                'availability_status' => 'open',
                'cv_path' => UploadedFile::fake()->create('my-resume.pdf', 120, 'application/pdf'),
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $profile = Profile::query()->sole();
        Storage::disk('public')->assertExists($profile->cv_path);

        $this->get('/cv')
            ->assertOk()
            ->assertDownload('poula-adel-cv.pdf');

        $this->get('/')->assertSee(route('cv'), false);
    }
}
