@props(['name' => null, 'fallback' => null])
@php($svg = \App\Support\Icon::use($name, (string) $attributes->get('class')))
@if ($svg && $attributes->has('style'))
    <span class="inline-flex" style="{{ $attributes->get('style') }}">{{ $svg }}</span>
@elseif ($svg)
    {{ $svg }}
@elseif ($fallback)
    <span {{ $attributes->merge(['class' => 'inline-flex items-center justify-center font-display text-[0.7em] font-bold uppercase']) }} aria-hidden="true">{{ str($fallback)->substr(0, 2) }}</span>
@endif
