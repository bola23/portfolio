<x-layouts.site title="Projects" description="All published projects — platforms, APIs and dashboards.">
    <section class="relative isolate overflow-hidden pt-32 pb-12 text-center sm:pt-40">
        <div class="blob -top-24 left-1/4 -z-10 size-[380px] bg-primary" aria-hidden="true"></div>
        <div class="blob top-10 right-0 -z-10 size-[300px] bg-accent" aria-hidden="true"></div>
        <div class="container-page" data-reveal>
            <span class="section-label">Portfolio</span>
            <h1 class="mt-5 text-4xl font-extrabold tracking-tight sm:text-5xl">All <span class="text-gradient">Projects</span></h1>
            <p class="mx-auto mt-4 max-w-xl text-lg text-muted">Browse every project, or filter by industry and technology.</p>
        </div>
    </section>

    <section class="pb-24" x-data="projectFilter" aria-label="Project list">
        <div class="container-page">
            {{-- Filters are real links (work without JS); Alpine swaps results in place. --}}
            <div data-filters class="mb-10 flex flex-col items-center gap-5" data-reveal>
                <nav aria-label="Filter by category" class="flex flex-wrap justify-center gap-2">
                    @foreach ([null => 'All', ...$categories->pluck('name', 'slug')->all()] as $slug => $name)
                        @php($active = ($slug ?: null) === $activeCategory)
                        <a href="{{ route('projects.index', array_filter(['category' => $slug ?: null, 'tech' => $activeTechnology])) }}" @click="navigate"
                           @class([
                               'rounded-full border px-5 py-2 text-sm font-semibold transition',
                               'bg-gradient-brand border-transparent text-white shadow-md' => $active,
                               'border-line bg-surface text-muted hover:text-fg' => ! $active,
                           ])
                           @if ($active) aria-current="true" @endif>{{ $name }}</a>
                    @endforeach
                </nav>

                @if ($technologies->isNotEmpty())
                    <label class="flex items-center gap-3 text-sm text-muted">
                        <span class="font-medium">Technology</span>
                        <select class="field !w-auto !py-2 pr-10" @change="load($event.target.value, true)" aria-label="Filter by technology">
                            <option value="{{ route('projects.index', array_filter(['category' => $activeCategory])) }}">Any technology</option>
                            @foreach ($technologies as $technology)
                                <option value="{{ route('projects.index', array_filter(['category' => $activeCategory, 'tech' => $technology->slug])) }}" @selected($activeTechnology === $technology->slug)>{{ $technology->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <noscript>
                        <p class="text-xs text-subtle">Enable JavaScript to filter by technology.</p>
                    </noscript>
                @endif
            </div>

            <div id="project-results" :class="loading && 'opacity-50 transition-opacity'" aria-live="polite">
                <p class="mb-6 text-sm font-medium text-muted">
                    Showing {{ $projects->count() }} {{ str('project')->plural($projects->count()) }}
                </p>

                @if ($projects->isEmpty())
                    <div class="card flex flex-col items-center gap-4 p-14 text-center">
                        <p class="text-xl font-bold">No projects match these filters.</p>
                        <a href="{{ route('projects.index') }}" @click="navigate" class="btn btn-outline">Reset filters</a>
                    </div>
                @else
                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($projects as $project)
                            <x-site.project-card :project="$project" :eager="$loop->index < 3" :heading-level="2" />
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-layouts.site>
