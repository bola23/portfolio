@props(['projects', 'total'])
<section id="projects" class="section bg-surface" aria-labelledby="projects-title">
    <div class="container-page">
        <x-site.section-heading label="Portfolio" title="Featured Projects" intro="A selection of platforms, APIs and dashboards I have built." id="projects-title" />

        @if ($projects->isEmpty())
            <p class="card p-10 text-center text-muted">New projects are on the way.</p>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($projects as $project)
                    <x-site.project-card :project="$project" />
                @endforeach
            </div>
        @endif

        @if ($total > $projects->count())
            <div class="mt-12 text-center" data-reveal>
                <a href="{{ route('projects.index') }}" class="btn btn-outline">
                    Explore all {{ $total }} projects
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
            </div>
        @endif
    </div>
</section>
