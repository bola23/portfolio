<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Credential;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\SkillCategory;
use App\Models\SocialLink;
use App\Models\Technology;
use App\Support\PortfolioCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Cached, read-only queries that power the public website.
 * Cache entries are invalidated by FlushesPortfolioCache on any content change.
 */
class PortfolioContent
{
    private ?SiteSetting $settings = null;

    private ?Profile $profile = null;

    /** @var Collection<int, SocialLink>|null */
    private ?Collection $socialLinks = null;

    public function settings(): SiteSetting
    {
        return $this->settings ??= SiteSetting::current();
    }

    public function profile(): Profile
    {
        return $this->profile ??= Profile::current();
    }

    /**
     * @return Collection<int, SocialLink>
     */
    public function socialLinks(): Collection
    {
        return $this->socialLinks ??= PortfolioCache::remember('social-links', fn (): Collection => SocialLink::query()->visible()->ordered()->get());
    }

    /**
     * @return array<string, mixed>
     */
    public function home(): array
    {
        return PortfolioCache::remember('home', function (): array {
            $skillCategories = SkillCategory::query()
                ->ordered()
                ->with(['skills' => fn ($query) => $query->visible()->ordered()])
                ->whereHas('skills', fn ($query) => $query->visible())
                ->get();

            return [
                'featuredProjects' => $this->publishedProjectsQuery()->featured()->limit(6)->get(),
                'skillCategories' => $skillCategories,
                // Brand-logo skills shown as floating badges around the hero photo.
                'highlightSkills' => $skillCategories->flatMap->skills
                    ->filter(fn ($skill): bool => filled($skill->icon) && ! str_starts_with($skill->icon, 'ui-'))
                    ->unique('icon')
                    ->take(6)
                    ->values(),
                'experiences' => Experience::query()->visible()->ordered()->with('technologies')->get(),
                'services' => Service::query()->visible()->ordered()->get(),
                'credentials' => Credential::query()->visible()->ordered()->get(),
                'stats' => $this->stats(),
            ];
        });
    }

    /**
     * All published projects with everything a card needs.
     *
     * @return Collection<int, Project>
     */
    public function publishedProjects(): Collection
    {
        return PortfolioCache::remember('projects', fn (): Collection => $this->publishedProjectsQuery()->get());
    }

    /**
     * Categories and technologies that are used by at least one published project.
     *
     * @return array{categories: Collection<int, Category>, technologies: Collection<int, Technology>}
     */
    public function projectFilters(): array
    {
        return PortfolioCache::remember('project-filters', fn (): array => [
            'categories' => Category::query()->ordered()
                ->whereHas('projects', fn ($query) => $query->published())
                ->get(),
            'technologies' => Technology::query()->ordered()
                ->whereHas('projects', fn ($query) => $query->published())
                ->get(),
        ]);
    }

    /**
     * Find a project by slug (including drafts; callers enforce visibility).
     */
    public function project(string $slug): ?Project
    {
        return PortfolioCache::remember("project:{$slug}", fn (): ?Project => Project::query()
            ->where('slug', $slug)
            ->with(['category', 'technologies', 'images'])
            ->first());
    }

    /**
     * Previous and next published projects relative to the given one.
     *
     * @return array{previous: Project|null, next: Project|null}
     */
    public function adjacentProjects(Project $project): array
    {
        $projects = $this->publishedProjects()->values();
        $index = $projects->search(fn (Project $item): bool => $item->is($project));

        if ($index === false) {
            return ['previous' => null, 'next' => null];
        }

        return [
            'previous' => $projects->get($index - 1),
            'next' => $projects->get($index + 1),
        ];
    }

    /**
     * Counters derived from real data — never hardcoded.
     *
     * @return array{years: int, projects: int, technologies: int, regions: int}
     */
    private function stats(): array
    {
        return [
            'years' => $this->profile()->years_of_experience,
            'projects' => Project::query()->published()->count(),
            'technologies' => Technology::query()->whereHas('projects', fn ($query) => $query->published())->count(),
            'regions' => Project::query()->published()->whereNotNull('region')->where('region', '!=', '')->distinct()->count('region'),
        ];
    }

    /**
     * @return Builder<Project>
     */
    private function publishedProjectsQuery(): Builder
    {
        return Project::query()
            ->published()
            ->ordered()
            ->with(['category', 'technologies']);
    }
}
