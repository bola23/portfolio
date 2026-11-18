@props(['profile', 'settings', 'socialLinks', 'stats', 'highlightSkills'])
@php
    $nameParts = str($profile->name)->explode(' ');
    $lastName = $nameParts->count() > 1 ? $nameParts->pop() : null;
    $firstNames = $nameParts->implode(' ');
@endphp
<section id="home" class="relative isolate overflow-hidden pt-28 pb-16 sm:pt-32 lg:flex lg:min-h-svh lg:items-center lg:pb-24" aria-labelledby="hero-title">
    <div class="blob -top-24 -left-24 -z-10 size-[420px] bg-primary" aria-hidden="true"></div>
    <div class="blob -right-20 bottom-0 -z-10 size-[380px] bg-accent" aria-hidden="true"></div>

    <div class="container-page grid items-center gap-14 lg:grid-cols-12">
        {{-- Text: first-viewport content animates with CSS only, so it never waits for JavaScript. --}}
        <div class="order-2 text-center lg:order-1 lg:col-span-7 lg:text-left">
            @if ($profile->availability_status)
                <span class="hero-rise inline-flex items-center gap-2 rounded-full border border-line bg-surface px-4 py-1.5 text-sm font-medium text-muted shadow-sm">
                    <span @class(['size-2 rounded-full', 'animate-pulse-dot bg-success text-success' => $profile->availability_status->isAvailable(), 'bg-subtle' => ! $profile->availability_status->isAvailable()])></span>
                    {{ $profile->availability_status->getLabel() }}
                </span>
            @endif

            <p class="hero-rise mt-7 text-lg font-medium text-muted [--rise-delay:60ms] sm:text-xl">Hi, I’m</p>
            <h1 id="hero-title" class="hero-rise mt-1 text-5xl font-extrabold tracking-tight [--rise-delay:120ms] sm:text-6xl lg:text-7xl">
                {{ $firstNames }} @if ($lastName)<span class="text-gradient">{{ $lastName }}</span>@endif
            </h1>

            <p class="hero-rise mt-5 text-xl font-bold text-fg [--rise-delay:180ms] sm:text-2xl">{{ $profile->headline }}</p>

            @if ($profile->short_intro)
                <p class="hero-rise mx-auto mt-5 max-w-xl text-pretty text-base leading-relaxed text-muted [--rise-delay:240ms] sm:text-lg lg:mx-0">{{ $profile->short_intro }}</p>
            @endif

            <div class="hero-rise mt-9 flex flex-wrap items-center justify-center gap-3 [--rise-delay:300ms] lg:justify-start">
                <a href="#projects" class="btn btn-primary">
                    View My Work
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
                <a href="#contact" class="btn btn-outline">
                    <x-site.icon name="ui-mail" class="size-4" />
                    Contact Me
                </a>
                @if ($profile->cv_path)
                    <a href="{{ route('cv') }}" class="btn btn-outline">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 4v11M7 10l5 5 5-5M5 20h14"/></svg>
                        Download CV
                    </a>
                @endif
            </div>

            <x-site.social-links :links="$socialLinks" :hero-only="true" class="hero-rise mt-7 justify-center [--rise-delay:360ms] lg:justify-start" aria-label="Social profiles" />

            {{-- Quick facts computed from the database --}}
            @php($facts = array_filter([
                ['value' => $stats['years'], 'label' => 'Years experience'],
                ['value' => $stats['projects'], 'label' => 'Projects'],
                ['value' => $stats['technologies'], 'label' => 'Technologies'],
            ], fn ($fact) => $fact['value'] > 0))
            @if ($facts)
                <dl class="hero-rise mx-auto mt-12 flex max-w-md justify-center gap-10 [--rise-delay:420ms] lg:mx-0 lg:justify-start">
                    @foreach ($facts as $fact)
                        <div class="flex flex-col-reverse text-center lg:text-left">
                            <dt class="mt-1 text-xs font-medium tracking-wide text-muted uppercase">{{ $fact['label'] }}</dt>
                            <dd class="text-gradient font-display text-3xl font-extrabold tabular-nums sm:text-4xl"><span data-counter="{{ $fact['value'] }}">{{ $fact['value'] }}</span>+</dd>
                        </div>
                    @endforeach
                </dl>
            @endif
        </div>

        {{-- 3D scene: photo inside a tilted, slowly spinning orbit of technology logos (CSS transforms only). --}}
        <div class="hero-stage order-1 flex justify-center py-6 lg:order-2 lg:col-span-5">
            <div class="hero-scene size-64 [--orbit-r:150px] sm:size-80 sm:[--orbit-r:190px] lg:size-[400px] lg:[--orbit-r:235px]" data-hero-scene>
                <div class="hero-photo relative size-full">
                    <div class="bg-gradient-brand absolute -inset-3 rounded-full" aria-hidden="true"></div>
                    <div class="relative size-full overflow-hidden rounded-full border-[6px] border-bg bg-surface">
                        <x-site.avatar :profile="$profile" sizes="(min-width: 1024px) 400px, (min-width: 640px) 320px, 256px" :eager="true" />
                    </div>
                </div>

                @if ($highlightSkills->isNotEmpty())
                    <div class="orbit" aria-hidden="true">
                        <div class="orbit-ring"></div>
                        <div class="orbit-track">
                            @foreach ($highlightSkills as $skill)
                                <div class="orbit-item" style="--angle: {{ round(360 / $highlightSkills->count() * $loop->index, 2) }}deg">
                                    <div class="orbit-face card grid size-12 place-items-center !rounded-2xl sm:size-14" title="{{ $skill->name }}">
                                        <x-site.icon :name="$skill->icon" :fallback="$skill->name" class="size-6 sm:size-7" style="color: {{ $skill->color ?: 'var(--color-primary)' }}" />
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($profile->years_of_experience)
                    <div class="hero-depth absolute top-4 -left-8 hidden sm:block">
                        <div class="card flex animate-float items-center gap-3 px-4 py-3">
                            <span class="bg-gradient-brand grid size-10 place-items-center rounded-xl font-display text-lg font-extrabold text-white">{{ $profile->years_of_experience }}+</span>
                            <span class="text-left text-xs font-semibold leading-tight text-fg">Years of<br>experience</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <a href="#about" class="absolute bottom-6 left-1/2 hidden -translate-x-1/2 flex-col items-center gap-2 text-xs font-medium text-muted transition hover:text-primary lg:flex">
        <span class="grid size-11 place-items-center rounded-full border border-line-strong bg-surface" aria-hidden="true">
            <svg class="size-4 animate-bounce" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M6 13l6 6 6-6"/></svg>
        </span>
        Scroll down
    </a>
</section>
