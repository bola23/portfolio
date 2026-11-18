@props(['project'])
{{-- Deterministic, on-brand cover art used until a real screenshot is uploaded. --}}
@php
    $seed = crc32($project->slug);
    $variant = $seed % 3;
    $rotation = ($seed % 40) - 20;
    $initials = str($project->title)->replaceMatches('/^demo\s*[—–-]\s*/iu', '')->explode(' ')->filter()->take(2)->map(fn ($word) => mb_substr($word, 0, 1))->implode('');
    $id = 'cover-'.$project->id;
@endphp
<div {{ $attributes->merge(['class' => 'relative overflow-hidden bg-elevated text-fg']) }} aria-hidden="true">
    <svg class="absolute inset-0 size-full" viewBox="0 0 800 500" preserveAspectRatio="xMidYMid slice">
        <defs>
            <radialGradient id="{{ $id }}-glow" cx="{{ 30 + $seed % 40 }}%" cy="{{ 30 + $seed % 30 }}%" r="70%">
                <stop offset="0" stop-color="var(--color-primary)" stop-opacity=".35" />
                <stop offset=".55" stop-color="var(--color-accent)" stop-opacity=".08" />
                <stop offset="1" stop-color="var(--color-primary)" stop-opacity="0" />
            </radialGradient>
            <pattern id="{{ $id }}-grid" width="40" height="40" patternUnits="userSpaceOnUse">
                <path d="M40 0H0v40" fill="none" stroke="currentColor" stroke-opacity=".06" />
            </pattern>
        </defs>
        <rect width="800" height="500" fill="url(#{{ $id }}-grid)" />
        <rect width="800" height="500" fill="url(#{{ $id }}-glow)" />
        <g transform="translate(560 250) rotate({{ $rotation }})" fill="none" stroke-width="2">
            @if ($variant === 0)
                @foreach ([0, 1, 2, 3] as $layer)
                    <rect x="-150" y="{{ -110 + $layer * 50 }}" width="300" height="34" rx="8" stroke="var(--color-primary)" stroke-opacity="{{ 0.75 - $layer * 0.15 }}" />
                @endforeach
            @elseif ($variant === 1)
                @foreach ([60, 105, 150, 195] as $radius)
                    <circle r="{{ $radius }}" stroke="var(--color-accent)" stroke-opacity="{{ 0.7 - $loop->index * 0.14 }}" stroke-dasharray="{{ $loop->even ? '4 8' : 'none' }}" />
                @endforeach
                <circle r="10" fill="var(--color-primary)" stroke="none" />
            @else
                <path d="M-160 80 L-60 -40 L40 30 L160 -100" stroke="var(--color-primary)" stroke-opacity=".8" />
                @foreach ([[-160, 80], [-60, -40], [40, 30], [160, -100]] as [$x, $y])
                    <circle cx="{{ $x }}" cy="{{ $y }}" r="7" fill="var(--color-surface)" stroke="var(--color-accent)" />
                @endforeach
            @endif
        </g>
    </svg>
    <span class="absolute bottom-4 left-6 font-display text-6xl leading-none font-extrabold tracking-tighter text-fg/10">{{ $initials }}</span>
</div>
