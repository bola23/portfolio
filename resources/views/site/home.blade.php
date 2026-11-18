<x-layouts.site :is-home="true" type="profile">
    <x-slot:head>
        <script type="application/ld+json">{!! json_encode(array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => $profile->name,
            'jobTitle' => $profile->headline,
            'description' => $profile->short_intro,
            'url' => route('home'),
            'image' => $profile->avatar ? Storage::disk('public')->url($profile->avatar) : null,
            'address' => $profile->location ? ['@type' => 'PostalAddress', 'addressCountry' => $profile->location] : null,
            'knowsAbout' => $profile->expertise_areas ?: null,
            'sameAs' => $socialLinks->filter(fn ($link) => str_starts_with($link->url, 'http'))->pluck('url')->values()->all() ?: null,
        ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
    </x-slot:head>

    <x-site.sections.hero :profile="$profile" :settings="$settings" :social-links="$socialLinks" :stats="$stats" :highlight-skills="$highlightSkills" />
    <x-site.sections.about :profile="$profile" :education="$credentials->first(fn ($credential) => $credential->type === \App\Enums\CredentialType::Education)" />

    @if ($skillCategories->isNotEmpty())
        <x-site.sections.skills :categories="$skillCategories" />
    @endif

    <x-site.sections.projects :projects="$featuredProjects" :total="$stats['projects']" />

    @if ($experiences->isNotEmpty())
        <x-site.sections.experience :experiences="$experiences" />
    @endif

    @if ($services->isNotEmpty())
        <x-site.sections.services :services="$services" />
    @endif

    @if ($credentials->isNotEmpty())
        <x-site.sections.credentials :credentials="$credentials" />
    @endif

    <x-site.sections.contact :profile="$profile" :settings="$settings" :social-links="$socialLinks" />
</x-layouts.site>
