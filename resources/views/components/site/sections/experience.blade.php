@props(['experiences'])
<section id="experience" class="section" aria-labelledby="experience-title">
    <div class="container-page">
        <x-site.section-heading label="Experience" title="Professional Experience" intro="Where I have worked and what I was responsible for." id="experience-title" />

        <ol class="relative">
            {{-- Timeline line: left on mobile, centered on desktop --}}
            <span class="absolute top-0 bottom-0 left-4 w-0.5 rounded-full bg-gradient-to-b from-primary via-accent to-transparent opacity-40 lg:left-1/2 lg:-translate-x-1/2" aria-hidden="true"></span>

            @foreach ($experiences as $experience)
                @php($isRight = $loop->odd === false)
                <li class="relative mb-10 pl-12 last:mb-0 lg:grid lg:grid-cols-2 lg:gap-16 lg:pl-0" data-reveal>
                    {{-- Dot --}}
                    <span class="absolute top-7 left-4 grid size-5 -translate-x-1/2 place-items-center rounded-full border-4 border-bg bg-primary shadow lg:left-1/2" aria-hidden="true">
                        @if ($experience->is_current)
                            <span class="size-full animate-pulse-dot rounded-full text-primary"></span>
                        @endif
                    </span>

                    <div @class(['card card-hover p-6 sm:p-7', 'lg:col-start-2' => $isRight])>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="tag">{{ $experience->period }}</span>
                            @if ($experience->is_current)
                                <span class="inline-flex items-center rounded-full bg-success/10 px-3 py-1 text-xs font-semibold text-success-ink">Current</span>
                            @endif
                        </div>

                        <h3 class="mt-4 text-xl font-bold">{{ $experience->position }}</h3>
                        <p class="mt-1 font-semibold text-accent-ink">
                            @if ($experience->company_url)
                                <a href="{{ $experience->company_url }}" target="_blank" rel="noopener noreferrer" class="hover:underline">{{ $experience->company }}</a>
                            @else
                                {{ $experience->company }}
                            @endif
                            @if ($meta = collect([$experience->location, $experience->work_mode, $experience->employment_type])->filter()->implode(' · '))
                                <span class="font-normal text-muted"> · {{ $meta }}</span>
                            @endif
                        </p>

                        @if ($experience->description)
                            <p class="mt-4 text-sm leading-relaxed text-muted">{{ $experience->description }}</p>
                        @endif

                        @if (filled($experience->responsibilities))
                            <ul class="mt-4 space-y-2">
                                @foreach ($experience->responsibilities as $responsibility)
                                    <li class="flex gap-3 text-sm leading-relaxed text-muted">
                                        <svg class="mt-1 size-3.5 shrink-0 text-primary" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
                                        {{ $responsibility }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if ($experience->technologies->isNotEmpty())
                            <ul class="mt-5 flex flex-wrap gap-2" aria-label="Technologies">
                                @foreach ($experience->technologies as $technology)
                                    <li class="tag-neutral">{{ $technology->name }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
</section>
