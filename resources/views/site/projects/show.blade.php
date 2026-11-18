@php
    $sections = collect([
        'overview' => ['Overview', $project->overview, 'ui-code'],
        'challenge' => ['The Challenge', $project->problem, 'ui-gauge'],
        'solution' => ['The Solution', $project->solution, 'ui-sparkles'],
        'responsibilities' => ['My Responsibilities', $project->responsibilities, 'ui-briefcase'],
        'architecture' => ['Technical Details', $project->architecture, 'ui-layers'],
    ])->filter(fn ($section) => filled(strip_tags((string) $section[1])));

    $facts = array_filter([
        'My role' => $project->role,
        'Project type' => $project->type->getLabel(),
        'Industry' => $project->category?->name,
        'Region' => $project->region,
    ]);
@endphp
<x-layouts.site
    :title="$project->meta_title ?: $project->title"
    :description="$project->meta_description ?: $project->excerpt"
    :image="$project->thumbnail"
    type="article"
    :noindex="$isPreview"
>
    <x-slot:head>
        <script type="application/ld+json">{!! json_encode(array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'CreativeWork',
            'name' => $project->title,
            'description' => $project->excerpt,
            'url' => route('projects.show', $project),
            'image' => $project->thumbnail ? Storage::disk('public')->url($project->thumbnail) : null,
            'genre' => $project->category?->name,
            'keywords' => $project->technologies->pluck('name')->implode(', ') ?: null,
            'datePublished' => $project->published_at?->toIso8601String(),
        ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
    </x-slot:head>

    @if ($isPreview)
        <div class="fixed inset-x-0 bottom-5 z-50 mx-auto w-fit rounded-full bg-amber-500 px-5 py-2 text-xs font-semibold text-white shadow-lg" role="status">
            Draft preview — only visible to administrators
        </div>
    @endif

    <article>
        {{-- Header --}}
        <header class="relative isolate overflow-hidden pt-32 pb-12 sm:pt-36">
            <div class="blob -top-24 -left-20 -z-10 size-[380px] bg-primary" aria-hidden="true"></div>
            <div class="blob top-20 -right-20 -z-10 size-[320px] bg-accent" aria-hidden="true"></div>

            <div class="container-page">
                <a href="{{ route('projects.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-muted transition hover:text-primary">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>
                    Back to all projects
                </a>

                <div class="mt-8 grid items-center gap-10 lg:grid-cols-2">
                    <div data-reveal>
                        <div class="flex flex-wrap gap-2">
                            @if ($project->category)
                                <span class="tag">{{ $project->category->name }}</span>
                            @endif
                            <span class="tag-neutral">{{ $project->type->getLabel() }}</span>
                        </div>
                        <h1 class="mt-5 text-4xl font-extrabold tracking-tight text-balance sm:text-5xl">{{ $project->title }}</h1>
                        <p class="mt-5 text-lg leading-relaxed text-muted">{{ $project->excerpt }}</p>

                        @if ($project->live_url || $project->repo_url)
                            <div class="mt-8 flex flex-wrap gap-3">
                                @if ($project->live_url)
                                    <a href="{{ $project->live_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                                        <x-site.icon name="ui-globe" class="size-4" />
                                        Visit live project
                                    </a>
                                @endif
                                @if ($project->repo_url)
                                    <a href="{{ $project->repo_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline">
                                        <x-site.icon name="github" class="size-4" />
                                        View source code
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="overflow-hidden rounded-3xl border border-line shadow-xl" data-reveal>
                        @if ($project->thumbnail)
                            <x-site.picture :path="$project->thumbnail" :alt="$project->title" :eager="true" sizes="(min-width: 1024px) 600px, 100vw" class="aspect-[16/10] w-full object-cover" />
                        @else
                            <x-site.project-cover :project="$project" class="aspect-[16/10] w-full" />
                        @endif
                    </div>
                </div>

                @if ($facts)
                    <dl @class(['mt-12 grid gap-4 sm:grid-cols-2', 'lg:grid-cols-3' => count($facts) === 3, 'lg:grid-cols-4' => count($facts) === 4]) data-reveal>
                        @foreach ($facts as $label => $value)
                            <div class="card p-5">
                                <dt class="text-xs font-semibold tracking-wider text-subtle uppercase">{{ $label }}</dt>
                                <dd class="mt-1 font-semibold text-fg">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                @endif
            </div>
        </header>

        {{-- Body --}}
        <div class="container-page pb-20">
            <div class="grid gap-10 lg:grid-cols-12">
                <div class="space-y-6 lg:col-span-8">
                    @forelse ($sections as $anchor => [$heading, $html, $icon])
                        <section id="{{ $anchor }}" class="card p-7 sm:p-8" data-reveal>
                            <h2 class="flex items-center gap-3 text-2xl font-bold">
                                <span class="icon-tile !size-10"><x-site.icon :name="$icon" class="size-5" /></span>
                                {{ $heading }}
                            </h2>
                            <x-site.rich-text :html="$html" class="mt-5" />
                        </section>
                    @empty
                        <p class="card p-8 text-muted">A detailed case study for this project is coming soon.</p>
                    @endforelse

                    @if (filled($project->features))
                        <section id="features" class="card p-7 sm:p-8" data-reveal>
                            <h2 class="flex items-center gap-3 text-2xl font-bold">
                                <span class="icon-tile !size-10"><x-site.icon name="ui-check-circle" class="size-5" /></span>
                                Main Features
                            </h2>
                            <ul class="mt-6 grid gap-3 sm:grid-cols-2">
                                @foreach ($project->features as $feature)
                                    <li class="flex items-start gap-3 rounded-xl bg-elevated p-4 text-sm font-medium text-fg">
                                        <svg class="mt-0.5 size-4 shrink-0 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" aria-hidden="true"><path d="m5 12 5 5L20 7"/></svg>
                                        {{ $feature }}
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endif

                    @if ($project->images->isNotEmpty())
                        <section id="gallery" class="card p-7 sm:p-8" x-data="gallery({{ $project->images->count() }})" @keydown.escape.window="close" @keydown.arrow-right.window="next" @keydown.arrow-left.window="prev" data-reveal>
                            <h2 class="text-2xl font-bold">Screenshots</h2>
                            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                                @foreach ($project->images as $image)
                                    <figure>
                                        <button type="button" @click="open({{ $loop->index }})" class="group block w-full overflow-hidden rounded-xl border border-line" aria-label="Enlarge screenshot {{ $loop->iteration }}">
                                            <x-site.picture :path="$image->path" :alt="$image->caption ?: $project->title.' screenshot '.$loop->iteration" sizes="(min-width: 1024px) 380px, 100vw" class="aspect-[16/10] w-full object-cover transition-transform duration-500 group-hover:scale-105" />
                                        </button>
                                        @if ($image->caption)
                                            <figcaption class="mt-2 text-xs text-muted">{{ $image->caption }}</figcaption>
                                        @endif
                                    </figure>
                                @endforeach
                            </div>

                            <template x-teleport="body">
                                <div x-show="active !== null" x-cloak x-transition.opacity class="fixed inset-0 z-[70] flex items-center justify-center bg-black/85 p-4" role="dialog" aria-modal="true" aria-label="Screenshot viewer" @click.self="close">
                                    @foreach ($project->images as $image)
                                        <img x-show="active === {{ $loop->index }}" src="{{ Storage::disk('public')->url($image->path) }}" alt="{{ $image->caption ?: $project->title }}" class="max-h-[85vh] max-w-full rounded-xl object-contain" loading="lazy">
                                    @endforeach
                                    <button type="button" @click="close" class="icon-btn absolute top-5 right-5" aria-label="Close">✕</button>
                                    <button type="button" @click="prev" class="icon-btn absolute left-5" aria-label="Previous">←</button>
                                    <button type="button" @click="next" class="icon-btn absolute right-5" aria-label="Next">→</button>
                                </div>
                            </template>
                        </section>
                    @endif
                </div>

                <aside class="lg:col-span-4">
                    <div class="space-y-6 lg:sticky lg:top-24">
                        @if ($project->technologies->isNotEmpty())
                            <div class="card p-6" data-reveal>
                                <h2 class="text-lg font-bold">Technologies Used</h2>
                                <ul class="mt-4 grid grid-cols-2 gap-2">
                                    @foreach ($project->technologies as $technology)
                                        <li>
                                            <a href="{{ route('projects.index', ['tech' => $technology->slug]) }}" class="flex items-center gap-2.5 rounded-xl border border-line px-3 py-2.5 text-sm font-medium text-fg transition hover:border-primary hover:text-primary">
                                                <x-site.icon :name="$technology->icon" :fallback="$technology->name" class="size-4" style="color: {{ $technology->color ?: 'var(--color-primary)' }}" />
                                                {{ $technology->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="bg-gradient-brand rounded-2xl p-6 text-white shadow-lg" data-reveal>
                            <h2 class="text-lg font-bold">Interested in working together?</h2>
                            <p class="mt-2 text-sm text-white/85">I’m open to new roles and projects.</p>
                            <a href="{{ route('home') }}#contact" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-slate-900 transition hover:-translate-y-0.5">Get in touch</a>
                        </div>
                    </div>
                </aside>
            </div>
        </div>

        @if ($previous || $next)
            <nav class="border-t border-line bg-surface" aria-label="More projects">
                <div class="container-page grid gap-4 py-10 sm:grid-cols-2">
                    @if ($previous)
                        <a href="{{ route('projects.show', $previous) }}" class="card card-hover p-6">
                            <span class="text-xs font-semibold tracking-wider text-subtle uppercase">← Previous project</span>
                            <span class="mt-2 block text-lg font-bold">{{ $previous->title }}</span>
                        </a>
                    @endif
                    @if ($next)
                        <a href="{{ route('projects.show', $next) }}" class="card card-hover p-6 text-right sm:col-start-2">
                            <span class="text-xs font-semibold tracking-wider text-subtle uppercase">Next project →</span>
                            <span class="mt-2 block text-lg font-bold">{{ $next->title }}</span>
                        </a>
                    @endif
                </div>
            </nav>
        @endif
    </article>
</x-layouts.site>
