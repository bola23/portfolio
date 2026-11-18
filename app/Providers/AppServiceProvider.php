<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Credential;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Skill;
use App\Models\SkillCategory;
use App\Models\SocialLink;
use App\Models\Technology;
use App\Models\User;
use App\Policies\AdminContentPolicy;
use App\Policies\ContactMessagePolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ImageManager::class, fn (): ImageManager => new ImageManager(GdDriver::class));
        $this->app->scoped(PortfolioContent::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        $this->configureRateLimiting();
    }

    private function registerPolicies(): void
    {
        $contentModels = [
            Category::class, Credential::class, Experience::class, Profile::class, Project::class,
            ProjectImage::class, Service::class, SiteSetting::class, Skill::class, SkillCategory::class,
            SocialLink::class, Technology::class, User::class,
        ];

        foreach ($contentModels as $model) {
            Gate::policy($model, AdminContentPolicy::class);
        }

        Gate::policy(ContactMessage::class, ContactMessagePolicy::class);

        Gate::define('previewDrafts', fn (User $user): bool => $user->isAdmin());
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('contact', fn (Request $request): array => [
            Limit::perMinute(3)->by('contact:minute:'.$request->ip()),
            Limit::perDay(10)->by('contact:day:'.$request->ip()),
        ]);
    }
}
