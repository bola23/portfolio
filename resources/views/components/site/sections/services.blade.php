@props(['services'])
<section id="services" class="section bg-surface" aria-labelledby="services-title">
    <div class="container-page">
        <x-site.section-heading label="Services" title="How I Can Help" intro="From a single API integration to a complete Laravel platform." id="services-title" />

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($services as $service)
                <article class="card card-hover flex flex-col p-7" data-reveal>
                    <span class="icon-tile"><x-site.icon :name="$service->icon" :fallback="$service->title" class="size-6" /></span>
                    <h3 class="mt-5 text-lg font-bold">{{ $service->title }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-muted">{{ $service->summary }}</p>
                    @if ($service->description)
                        <p class="mt-2 text-sm leading-relaxed text-muted">{{ $service->description }}</p>
                    @endif
                    @if (filled($service->deliverables))
                        <ul class="mt-5 space-y-2 border-t border-line pt-5">
                            @foreach ($service->deliverables as $deliverable)
                                <li class="flex items-center gap-2.5 text-sm text-fg">
                                    <svg class="size-4 shrink-0 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="m5 12 5 5L20 7"/></svg>
                                    {{ $deliverable }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</section>
