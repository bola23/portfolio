@props(['profile', 'education' => null])
@php
    $facts = array_filter([
        ['icon' => 'ui-pin', 'label' => 'Location', 'value' => $profile->location],
        ['icon' => 'ui-briefcase', 'label' => 'Experience', 'value' => $profile->years_of_experience ? $profile->years_of_experience.'+ years' : null],
        ['icon' => 'ui-code', 'label' => 'Specialization', 'value' => $profile->specialization],
        ['icon' => 'ui-graduation', 'label' => 'Education', 'value' => $education?->title],
        ['icon' => 'ui-mail', 'label' => 'Email', 'value' => $profile->email, 'href' => $profile->email ? 'mailto:'.$profile->email : null],
        ['icon' => 'ui-check-circle', 'label' => 'Availability', 'value' => $profile->availability_status?->getLabel()],
    ], fn ($fact) => filled($fact['value']));
@endphp
<section id="about" class="section bg-surface" aria-labelledby="about-title">
    <div class="container-page">
        <x-site.section-heading label="About Me" title="Professional Profile" :intro="$profile->specialization ? 'Focused on '.$profile->specialization.', building reliable software for real businesses.' : null" id="about-title" />

        <div class="grid items-center gap-14 lg:grid-cols-12">
            {{-- Photo --}}
            <div class="lg:col-span-5" data-reveal>
                <div class="relative mx-auto max-w-sm lg:max-w-none">
                    <div class="bg-gradient-brand absolute -top-4 -left-4 h-full w-full rounded-3xl opacity-20" aria-hidden="true"></div>
                    <div class="relative aspect-[4/5] overflow-hidden rounded-3xl border border-line bg-elevated shadow-xl">
                        <x-site.avatar :profile="$profile" sizes="(min-width: 1024px) 460px, 90vw" />
                    </div>
                    @if ($profile->years_of_experience)
                        <div class="bg-gradient-brand absolute -right-4 -bottom-6 rounded-2xl px-6 py-4 text-center text-white shadow-xl sm:-right-6">
                            <p class="font-display text-4xl font-extrabold">{{ $profile->years_of_experience }}+</p>
                            <p class="text-xs font-semibold tracking-wider uppercase">Years experience</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Text --}}
            <div class="lg:col-span-7" data-reveal>
                <h3 class="text-2xl font-bold sm:text-3xl">{{ $profile->headline }}</h3>
                <x-site.rich-text :html="$profile->bio" class="mt-5 text-[1.05rem]" />

                @if ($facts)
                    <dl class="mt-8 grid gap-3 sm:grid-cols-2">
                        @foreach ($facts as $fact)
                            {{-- dt/dd are direct children of the group so the list stays valid; the icon lives inside dt. --}}
                            <div class="relative flex min-h-[4.75rem] flex-col justify-center rounded-2xl border border-line bg-bg py-4 pr-4 pl-[4.75rem]">
                                <dt class="text-xs font-semibold tracking-wider text-subtle uppercase">
                                    <span class="icon-tile absolute top-1/2 left-4 !size-11 -translate-y-1/2" aria-hidden="true"><x-site.icon :name="$fact['icon']" class="size-5" /></span>
                                    {{ $fact['label'] }}
                                </dt>
                                <dd class="min-w-0 font-semibold break-words text-fg">
                                    @if (! empty($fact['href']))
                                        <a href="{{ $fact['href'] }}" class="transition hover:text-primary">{{ $fact['value'] }}</a>
                                    @else
                                        {{ $fact['value'] }}
                                    @endif
                                </dd>
                            </div>
                        @endforeach
                    </dl>
                @endif

                @if (filled($profile->expertise_areas))
                    <div class="mt-8">
                        <p class="text-sm font-semibold text-fg">Areas of experience</p>
                        <ul class="mt-3 flex flex-wrap gap-2">
                            @foreach ($profile->expertise_areas as $area)
                                <li class="tag">{{ $area }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mt-9 flex flex-wrap gap-3">
                    @if ($profile->cv_path)
                        <a href="{{ route('cv') }}" class="btn btn-primary">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 4v11M7 10l5 5 5-5M5 20h14"/></svg>
                            Download CV
                        </a>
                    @endif
                    <a href="#contact" class="btn btn-outline">Let’s talk</a>
                </div>
            </div>
        </div>

        @if (filled(strip_tags((string) $profile->approach)))
            <div class="card mx-auto mt-20 max-w-4xl p-8 sm:p-10" data-reveal>
                <div class="flex flex-col gap-6 sm:flex-row">
                    <span class="icon-tile"><x-site.icon name="ui-sparkles" class="size-6" /></span>
                    <div>
                        <h3 class="text-xl font-bold">My approach to software development</h3>
                        <x-site.rich-text :html="$profile->approach" class="mt-3" />
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
