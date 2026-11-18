<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectIndexRequest;
use App\Models\Project;
use App\Services\PortfolioContent;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(ProjectIndexRequest $request, PortfolioContent $content): View
    {
        $category = $request->category();
        $technology = $request->technology();

        $projects = $content->publishedProjects()
            ->when($category, fn ($projects) => $projects->filter(
                fn (Project $project): bool => $project->category?->slug === $category,
            ))
            ->when($technology, fn ($projects) => $projects->filter(
                fn (Project $project): bool => $project->technologies->contains('slug', $technology),
            ))
            ->values();

        return view('site.projects.index', [
            ...$content->projectFilters(),
            'projects' => $projects,
            'activeCategory' => $category,
            'activeTechnology' => $technology,
        ]);
    }

    public function show(string $slug, PortfolioContent $content): View
    {
        $project = $content->project($slug);

        abort_if($project === null, 404);

        $isPreview = ! $project->is_published;

        abort_if($isPreview && Gate::denies('previewDrafts'), 404);

        return view('site.projects.show', [
            'project' => $project,
            'isPreview' => $isPreview,
            ...$content->adjacentProjects($project),
        ]);
    }
}
