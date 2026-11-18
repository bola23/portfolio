@props(['profile', 'sizes' => '480px', 'eager' => false])
{{-- Profile photo, or an on-brand monogram until a photo is uploaded in the dashboard. --}}
@if ($profile->avatar)
    <x-site.picture :path="$profile->avatar" :alt="$profile->name" :sizes="$sizes" :eager="$eager" :width="800" :height="800"
        {{ $attributes->merge(['class' => 'size-full object-cover']) }} />
@else
    <div {{ $attributes->merge(['class' => 'bg-gradient-brand grid size-full place-items-center']) }} role="img" aria-label="{{ $profile->name }}">
        <span class="font-display text-[clamp(3rem,10vw,7rem)] font-extrabold tracking-tight text-white/95">
            {{ str($profile->name)->explode(' ')->filter()->take(2)->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))->implode('') }}
        </span>
    </div>
@endif
