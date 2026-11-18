@props(['settings', 'profile', 'isHome' => false])
@php
    $base = $isHome ? '' : route('home');
    $links = [
        'home' => 'Home',
        'about' => 'About',
        'skills' => 'Skills',
        'projects' => 'Projects',
        'experience' => 'Experience',
        'services' => 'Services',
        'contact' => 'Contact',
    ];
@endphp
<header
    x-data="{ open: false, dark: document.documentElement.classList.contains('dark') }"
    @keydown.escape.window="open = false"
    data-nav
    class="fixed inset-x-0 top-0 z-50 border-b border-line bg-bg/80 backdrop-blur-xl transition-colors"
>
    <nav class="container-page flex h-16 items-center justify-between gap-4" aria-label="Primary">
        <a href="{{ $isHome ? '#home' : route('home') }}" class="flex items-center gap-2.5" aria-label="{{ $settings->site_name }} — home">
            @if ($settings->logo)
                <img src="{{ Storage::disk('public')->url($settings->logo) }}" alt="{{ $settings->site_name }}" class="h-8 w-auto" height="32">
            @else
                <span class="text-gradient font-display text-xl font-extrabold tracking-tight">{{ $settings->site_name }}</span>
            @endif
        </a>

        <ul class="hidden items-center gap-1 lg:flex">
            @foreach ($links as $id => $label)
                <li>
                    <a href="{{ $base }}#{{ $id }}" data-nav-link="{{ $id }}"
                       class="relative px-3 py-2 text-sm font-medium text-muted transition hover:text-fg data-[active]:text-fg">
                        {{ $label }}
                        <span class="bg-gradient-brand absolute inset-x-3 -bottom-0.5 h-0.5 origin-left scale-x-0 rounded-full transition-transform duration-300 [[data-active]>&]:scale-x-100"></span>
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="flex items-center gap-2">
            <button type="button" class="icon-btn" aria-label="Toggle dark mode" :aria-label="dark ? 'Switch to light theme' : 'Switch to dark theme'"
                    @click="dark = !dark; document.documentElement.classList.toggle('dark', dark); try { localStorage.setItem('theme', dark ? 'dark' : 'light') } catch (e) {}">
                <svg x-show="!dark" class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z"/></svg>
                <svg x-show="dark" x-cloak class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
            </button>
            @if ($profile->cv_path)
                <a href="{{ route('cv') }}" class="btn btn-primary hidden !px-4 !py-2.5 sm:inline-flex">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 4v11M7 10l5 5 5-5M5 20h14"/></svg>
                    Resume
                </a>
            @endif
            <button type="button" class="icon-btn lg:hidden" @click="open = !open" :aria-expanded="open" aria-controls="mobile-menu" aria-label="Toggle menu">
                <svg x-show="!open" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                <svg x-show="open" x-cloak class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
            </button>
        </div>
    </nav>

    {{-- Mobile menu --}}
    <div id="mobile-menu" x-show="open" x-cloak x-transition.opacity.duration.200ms
         x-init="$watch('open', value => value && $nextTick(() => $el.querySelector('a')?.focus()))"
         @click.outside="open = false"
         class="border-t border-line bg-surface lg:hidden">
        <ul class="container-page grid gap-1 py-4">
            @foreach ($links as $id => $label)
                <li>
                    <a href="{{ $base }}#{{ $id }}" @click="open = false"
                       class="block rounded-xl px-4 py-3 text-base font-medium text-fg transition hover:bg-elevated hover:text-primary">{{ $label }}</a>
                </li>
            @endforeach
            @if ($profile->cv_path)
                <li class="pt-2">
                    <a href="{{ route('cv') }}" class="btn btn-primary w-full">Download Resume</a>
                </li>
            @endif
        </ul>
    </div>
</header>
