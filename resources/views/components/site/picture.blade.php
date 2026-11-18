@props(['path', 'alt' => '', 'sizes' => '100vw', 'eager' => false, 'width' => 1600, 'height' => 1000])
@php
    $disk = Storage::disk('public');
    $small = \App\Services\ImageOptimizer::smallVariant($path);
    // Older uploads may have a "-sm" copy identical to the original; skip srcset then.
    $hasSmall = $small !== $path && $disk->exists($small) && $disk->size($small) !== $disk->size($path);
@endphp
<img
    src="{{ $disk->url($path) }}"
    @if ($hasSmall) srcset="{{ $disk->url($small) }} 640w, {{ $disk->url($path) }} 1920w" sizes="{{ $sizes }}" @endif
    alt="{{ $alt }}"
    width="{{ $width }}"
    height="{{ $height }}"
    loading="{{ $eager ? 'eager' : 'lazy' }}"
    decoding="async"
    @if ($eager) fetchpriority="high" @endif
    {{ $attributes }}
>
