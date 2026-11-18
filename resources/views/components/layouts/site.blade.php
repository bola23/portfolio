<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $pageTitle() }}</title>
    <meta name="description" content="{{ $metaDescription() }}">
    <link rel="canonical" href="{{ url()->current() }}">
    @if ($noindex)
        <meta name="robots" content="noindex, nofollow">
    @endif

    <meta property="og:type" content="{{ $type }}">
    <meta property="og:site_name" content="{{ $settings->site_name }}">
    <meta property="og:title" content="{{ $pageTitle() }}">
    <meta property="og:description" content="{{ $metaDescription() }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if ($shareImage())
        <meta property="og:image" content="{{ $shareImage() }}">
        <meta name="twitter:image" content="{{ $shareImage() }}">
    @endif
    <meta name="twitter:card" content="{{ $shareImage() ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $pageTitle() }}">
    <meta name="twitter:description" content="{{ $metaDescription() }}">

    @if ($settings->favicon)
        <link rel="icon" href="{{ Storage::disk('public')->url($settings->favicon) }}">
    @else
        <link rel="icon" href="data:image/svg+xml,{{ rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="'.$color($settings->primary_color, '#4F46E5').'"/><stop offset="1" stop-color="'.$color($settings->accent_color, '#14B8A6').'"/></linearGradient></defs><rect width="32" height="32" rx="9" fill="url(#g)"/><text x="16" y="22" font-family="Arial" font-weight="700" font-size="16" fill="#fff" text-anchor="middle">'.e($initials()).'</text></svg>') }}">
    @endif

    <style>
        :root {
            --brand-primary: {{ $color($settings->primary_color, '#4F46E5') }};
            --brand-accent: {{ $color($settings->accent_color, '#14B8A6') }};
        }
    </style>

    {{-- Apply the theme before first paint to avoid a flash. --}}
    <script>
        (function () {
            var root = document.documentElement;
            var fallback = @js($settings->default_theme ?? 'light');
            var stored = null;
            try { stored = localStorage.getItem('theme'); } catch (e) {}
            var theme = stored || (fallback === 'system' ? (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light') : fallback);
            root.classList.toggle('dark', theme === 'dark');
        })();
    </script>
    <meta name="theme-color" content="#f7f8fc" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#0a0e1a" media="(prefers-color-scheme: dark)">

    {{-- Preload the two Latin font files so first paint uses the final fonts (no swap re-layout). --}}
    @foreach (['inter/files/inter', 'plus-jakarta-sans/files/plus-jakarta-sans'] as $font)
        <link rel="preload" href="{{ Vite::asset("node_modules/@fontsource-variable/{$font}-latin-wght-normal.woff2") }}" as="font" type="font/woff2" crossorigin>
    @endforeach
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{ $head ?? '' }}
</head>
<body class="min-h-svh overflow-x-clip">
    <a href="#main" class="skip-link">Skip to content</a>

    <x-site.nav :settings="$settings" :profile="$profile" :is-home="$isHome" />
    <x-site.social-rail :links="$socialLinks" />

    <main id="main">
        {{ $slot }}
    </main>

    <x-site.footer :settings="$settings" :profile="$profile" :social-links="$socialLinks" :is-home="$isHome" />

    <a href="#main" x-data="{ show: false }" @scroll.window.throttle.200ms="show = window.scrollY > 700"
       x-show="show" x-transition.opacity x-cloak
       class="bg-gradient-brand fixed right-5 bottom-5 z-40 grid size-12 place-items-center rounded-full text-white shadow-lg transition hover:-translate-y-1"
       aria-label="Back to top">
        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
    </a>

    {{ \App\Support\Icon::sprite() }}
</body>
</html>
