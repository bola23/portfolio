@props(['categories'])
<section id="skills" class="section" aria-labelledby="skills-title">
    <div class="container-page">
        <x-site.section-heading label="My Skills" title="Technical Expertise" intro="Technologies and tools I work with every day." id="skills-title" />

        <div x-data="skillTabs">
            {{-- Category tabs: identical directive strings on every button (the category comes from data-tab). --}}
            <div class="mb-10 flex flex-wrap justify-center gap-2" role="tablist" aria-label="Skill categories" data-reveal>
                @foreach ([['slug' => 'all', 'name' => 'All'], ...$categories->map(fn ($category) => ['slug' => $category->slug, 'name' => $category->name])] as $tab)
                    <button type="button" role="tab" data-tab="{{ $tab['slug'] }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                            @click="select($el.dataset.tab)" :aria-selected="isActive($el.dataset.tab)"
                            @class(['skill-tab rounded-full border px-5 py-2 text-sm font-semibold transition', 'is-active' => $loop->first])
                            :class="{ 'is-active': isActive($el.dataset.tab) }">{{ $tab['name'] }}</button>
                @endforeach
            </div>

            {{-- Colored logo cards --}}
            <ul x-ref="grid" class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6" data-reveal>
                @foreach ($categories as $category)
                    @foreach ($category->skills as $skill)
                        @php($brand = $skill->color ?: 'var(--color-primary)')
                        <li data-tilt data-category="{{ $category->slug }}"
                            class="card card-hover group flex flex-col items-center gap-3 px-3 pt-6 pb-5 text-center"
                            style="--brand: {{ $brand }}; box-shadow: inset 0 -3px 0 var(--brand), 0 8px 24px -14px color-mix(in oklab, var(--brand) 55%, transparent);">
                            <span class="grid size-14 place-items-center rounded-2xl transition duration-300 group-hover:scale-110" style="color: var(--brand); background: color-mix(in oklab, var(--brand) 10%, transparent)">
                                @if ($skill->icon_path)
                                    <img src="{{ Storage::disk('public')->url($skill->icon_path) }}" alt="" class="size-8 object-contain" loading="lazy" width="32" height="32">
                                @else
                                    <x-site.icon :name="$skill->icon" :fallback="$skill->name" class="size-8" />
                                @endif
                            </span>
                            <span class="text-sm font-semibold leading-snug text-fg">{{ $skill->name }}</span>
                            @if ($skill->proficiency !== null)
                                <span class="block h-1.5 w-full overflow-hidden rounded-full bg-elevated" role="meter" aria-valuenow="{{ $skill->proficiency }}" aria-valuemin="0" aria-valuemax="100" aria-label="{{ $skill->name }} proficiency">
                                    <span class="block h-full rounded-full" style="width: {{ $skill->proficiency }}%; background: var(--brand)"></span>
                                </span>
                            @endif
                        </li>
                    @endforeach
                @endforeach
            </ul>
        </div>
    </div>
</section>
