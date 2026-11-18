@props(['settings', 'profile', 'socialLinks', 'isHome' => false])
@php($base = $isHome ? '' : route('home'))
<footer class="border-t border-line bg-surface">
    <div class="container-page py-12">
        <div class="flex flex-col items-center gap-6 text-center">
            <a href="{{ route('home') }}" class="text-gradient font-display text-2xl font-extrabold tracking-tight">{{ $settings->site_name }}</a>
            @if ($settings->tagline)
                <p class="max-w-md text-sm text-muted">{{ $settings->tagline }}</p>
            @endif

            <nav aria-label="Footer">
                <ul class="flex flex-wrap justify-center gap-x-6 gap-y-2 text-sm text-muted">
                    @foreach (['about' => 'About', 'skills' => 'Skills', 'projects' => 'Projects', 'experience' => 'Experience', 'services' => 'Services', 'contact' => 'Contact'] as $id => $label)
                        <li><a href="{{ $base }}#{{ $id }}" class="transition hover:text-primary">{{ $label }}</a></li>
                    @endforeach
                </ul>
            </nav>

            <x-site.social-links :links="$socialLinks" class="justify-center" />
        </div>

        <div class="mt-10 flex flex-col items-center justify-between gap-2 border-t border-line pt-6 text-xs text-subtle sm:flex-row">
            <p>© {{ now()->year }} {{ $profile->name }}. All rights reserved.</p>
            @if ($settings->footer_text)
                <p>{{ $settings->footer_text }}</p>
            @endif
        </div>
    </div>
</footer>
