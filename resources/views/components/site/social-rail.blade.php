@props(['links'])
{{-- Fixed vertical social bar on large screens. --}}
@if ($links->isNotEmpty())
    <aside class="fixed top-1/2 left-5 z-40 hidden -translate-y-1/2 flex-col items-center gap-3 2xl:flex" aria-label="Social links">
        @foreach ($links as $link)
            <a href="{{ $link->href }}"
               class="icon-btn shadow-sm"
               @if ($link->platform !== \App\Enums\SocialPlatform::Email) target="_blank" rel="noopener noreferrer me" @endif
               aria-label="{{ $link->display_label }}" title="{{ $link->display_label }}">
                <x-site.icon :name="$link->platform->icon()" :fallback="$link->display_label" class="size-[18px]" />
            </a>
        @endforeach
        <span class="mt-1 h-20 w-px bg-line-strong" aria-hidden="true"></span>
    </aside>
@endif
