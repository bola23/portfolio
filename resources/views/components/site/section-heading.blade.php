@props(['label', 'title', 'intro' => null])
<div {{ $attributes->merge(['class' => 'mx-auto mb-14 max-w-2xl text-center']) }} data-reveal>
    <span class="section-label">{{ $label }}</span>
    <h2 class="mt-5 text-3xl font-extrabold tracking-tight text-balance sm:text-4xl lg:text-[2.75rem]">{{ $title }}</h2>
    @if ($intro)
        <p class="mt-4 text-pretty text-base text-muted sm:text-lg">{{ $intro }}</p>
    @endif
    {{ $slot }}
</div>
