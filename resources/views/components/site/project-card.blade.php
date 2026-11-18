@props(['project', 'eager' => false, 'headingLevel' => 3])
@php($heading = 'h'.(int) $headingLevel)
<article data-tilt {{ $attributes->merge(['class' => 'card card-hover group flex flex-col overflow-hidden']) }} data-reveal>
    <div class="relative aspect-[16/10] overflow-hidden bg-elevated">
        @if ($project->thumbnail)
            <x-site.picture
                :path="$project->thumbnail"
                :alt="$project->title"
                :eager="$eager"
                sizes="(min-width: 1024px) 380px, (min-width: 640px) 50vw, 100vw"
                class="size-full object-cover transition-transform duration-700 group-hover:scale-105"
            />
        @else
            <x-site.project-cover :project="$project" class="size-full transition-transform duration-700 group-hover:scale-105" />
        @endif
        @if ($project->category)
            <span class="absolute top-4 left-4 rounded-full bg-surface/90 px-3 py-1 text-xs font-semibold text-primary shadow-sm backdrop-blur">{{ $project->category->name }}</span>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-6">
        <p class="text-xs font-semibold tracking-wider text-subtle uppercase">{{ $project->type->getLabel() }}</p>
        <{{ $heading }} class="mt-2 text-xl font-bold leading-snug text-balance">
            <a href="{{ route('projects.show', $project) }}" class="stretched-link transition group-hover:text-primary focus:outline-none">{{ $project->title }}</a>
        </{{ $heading }}>
        <p class="mt-3 line-clamp-3 text-sm leading-relaxed text-muted">{{ $project->excerpt }}</p>

        @if ($project->technologies->isNotEmpty())
            <ul class="mt-5 flex flex-wrap gap-2" aria-label="Technologies">
                @foreach ($project->technologies->take(4) as $technology)
                    <li class="tag">{{ $technology->name }}</li>
                @endforeach
                @if ($project->technologies->count() > 4)
                    <li class="tag-neutral">+{{ $project->technologies->count() - 4 }}</li>
                @endif
            </ul>
        @endif

        <div class="mt-auto flex items-center justify-between gap-3 pt-6">
            <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary">
                View details
                <svg class="size-4 transition-transform group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </span>
            @if ($project->live_url || $project->repo_url)
                <div class="relative z-10 flex items-center gap-2">
                    @if ($project->live_url)
                        <a href="{{ $project->live_url }}" target="_blank" rel="noopener noreferrer" class="icon-btn size-9" aria-label="Open live site of {{ $project->title }}" title="Live site">
                            <x-site.icon name="ui-globe" class="size-4" />
                        </a>
                    @endif
                    @if ($project->repo_url)
                        <a href="{{ $project->repo_url }}" target="_blank" rel="noopener noreferrer" class="icon-btn size-9" aria-label="Source code of {{ $project->title }}" title="Source code">
                            <x-site.icon name="github" class="size-4" />
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</article>
