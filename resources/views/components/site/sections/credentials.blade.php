@props(['credentials'])
<section id="education" class="section" aria-labelledby="education-title">
    <div class="container-page">
        <x-site.section-heading label="Education" title="Education & Certifications" id="education-title" />

        <div class="mx-auto grid max-w-5xl gap-5 md:grid-cols-2">
            @foreach ($credentials as $credential)
                <article class="card card-hover flex gap-5 p-6" data-reveal>
                    <span class="icon-tile">
                        <x-site.icon :name="$credential->type === \App\Enums\CredentialType::Education ? 'ui-graduation' : 'ui-certificate'" class="size-6" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-semibold tracking-wider text-subtle uppercase">{{ $credential->type->getLabel() }}</span>
                            @if ($credential->period)
                                <span class="tag">{{ $credential->period }}</span>
                            @endif
                        </div>
                        <h3 class="mt-2 text-lg font-bold leading-snug">
                            @if ($credential->url)
                                <a href="{{ $credential->url }}" target="_blank" rel="noopener noreferrer" class="transition hover:text-primary">{{ $credential->title }} ↗</a>
                            @else
                                {{ $credential->title }}
                            @endif
                        </h3>
                        <p class="mt-1 text-sm font-medium text-accent-ink">{{ $credential->issuer }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
