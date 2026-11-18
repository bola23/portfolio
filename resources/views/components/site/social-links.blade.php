@props(['links', 'heroOnly' => false])
@php($links = $heroOnly ? $links->where('show_in_hero', true) : $links)
@if ($links->isNotEmpty())
    <ul {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-2.5']) }}>
        @foreach ($links as $link)
            <li>
                <a href="{{ $link->href }}"
                   class="icon-btn"
                   @if ($link->platform !== \App\Enums\SocialPlatform::Email) target="_blank" rel="noopener noreferrer me" @endif
                   aria-label="{{ $link->display_label }}"
                   title="{{ $link->display_label }}">
                    <x-site.icon :name="$link->platform->icon()" :fallback="$link->display_label" class="size-[18px]" />
                </a>
            </li>
        @endforeach
    </ul>
@endif
