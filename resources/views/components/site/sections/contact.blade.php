@props(['profile', 'settings', 'socialLinks'])
@php
    $honeypot = \App\Http\Requests\ContactMessageRequest::HONEYPOT;
    $tokenField = \App\Http\Requests\ContactMessageRequest::TIMESTAMP;
    $linkedin = $socialLinks->first(fn ($link) => $link->platform === \App\Enums\SocialPlatform::LinkedIn);

    $channels = array_filter([
        $profile->email ? ['icon' => 'ui-mail', 'label' => 'Email', 'value' => $profile->email, 'href' => 'mailto:'.$profile->email, 'action' => 'Send email'] : null,
        $profile->show_phone && $profile->phone ? ['icon' => 'ui-phone', 'label' => 'Phone', 'value' => $profile->phone, 'href' => 'tel:'.preg_replace('/[^0-9+]/', '', $profile->phone), 'action' => 'Call or chat'] : null,
        $linkedin ? ['icon' => 'linkedin', 'label' => 'LinkedIn', 'value' => $linkedin->display_label, 'href' => $linkedin->href, 'action' => 'Connect', 'external' => true] : null,
        $profile->location ? ['icon' => 'ui-pin', 'label' => 'Location', 'value' => $profile->location, 'href' => null, 'action' => $profile->availability_note] : null,
    ]);
@endphp
<section id="contact" class="section bg-surface" aria-labelledby="contact-title">
    <div class="container-page">
        <x-site.section-heading label="Contact" title="Get In Touch" intro="Have a role, a project or a question? I’d be happy to hear from you." id="contact-title" />

        <div class="grid gap-8 lg:grid-cols-12">
            <div class="space-y-4 lg:col-span-5">
                @foreach ($channels as $channel)
                    <div class="card card-hover flex items-center gap-4 p-5" data-reveal>
                        <span class="icon-tile"><x-site.icon :name="$channel['icon']" class="size-5" /></span>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold tracking-wider text-subtle uppercase">{{ $channel['label'] }}</p>
                            <p class="truncate font-semibold text-fg">{{ $channel['value'] }}</p>
                            @if ($channel['action'])
                                <p class="text-xs text-muted">{{ $channel['action'] }}</p>
                            @endif
                        </div>
                        @if ($channel['href'])
                            <a href="{{ $channel['href'] }}" class="stretched-link" @if (! empty($channel['external'])) target="_blank" rel="noopener noreferrer" @endif aria-label="{{ $channel['action'] }}: {{ $channel['value'] }}"></a>
                            <svg class="size-5 shrink-0 text-subtle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M7 17 17 7M8 7h9v9"/></svg>
                        @endif
                    </div>
                @endforeach

                @if ($profile->email)
                    <div x-data="{ copied: false }" class="flex justify-center lg:justify-start" data-reveal>
                        <button type="button" class="btn-ghost inline-flex items-center gap-2 text-sm font-medium"
                                @click="navigator.clipboard?.writeText(@js($profile->email)); copied = true; setTimeout(() => copied = false, 2000)">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/></svg>
                            <span x-text="copied ? 'Email copied ✓' : 'Copy email address'">Copy email address</span>
                        </button>
                    </div>
                @endif

                <div class="pt-2" data-reveal>
                    <x-site.social-links :links="$socialLinks" class="justify-center lg:justify-start" />
                </div>
            </div>

            <div class="lg:col-span-7">
                @if ($settings->show_contact_form)
                    <form method="POST" action="{{ route('contact.store') }}" x-data="contactForm" @submit.prevent="submit" class="card p-6 sm:p-9" data-reveal novalidate>
                        @csrf
                        <input type="hidden" name="{{ $tokenField }}" value="{{ \App\Http\Requests\ContactMessageRequest::freshToken() }}">
                        <div class="absolute -left-[9999px] h-0 w-0 overflow-hidden" aria-hidden="true">
                            <label for="contact-{{ $honeypot }}">Leave this field empty</label>
                            <input type="text" id="contact-{{ $honeypot }}" name="{{ $honeypot }}" tabindex="-1" autocomplete="off">
                        </div>

                        <h3 class="text-xl font-bold">Send me a message</h3>
                        <p class="mt-1 text-sm text-muted">I usually reply within one or two business days.</p>

                        @if (session('contact_status'))
                            <div class="mt-6 rounded-xl border border-success/30 bg-success/10 px-4 py-3 text-sm font-medium text-success-ink" role="status">{{ session('contact_status') }}</div>
                        @endif
                        <div x-show="success" x-cloak x-transition class="mt-6 rounded-xl border border-success/30 bg-success/10 px-4 py-3 text-sm font-medium text-success-ink" role="status" x-text="success"></div>
                        <div x-show="failure" x-cloak x-transition class="mt-6 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm font-medium text-red-600 dark:text-red-400" role="alert" x-text="failure"></div>

                        <div class="mt-6 grid gap-5 sm:grid-cols-2">
                            @foreach ([
                                ['name', 'Your name', 'text', 'name', 'John Smith'],
                                ['email', 'Email address', 'email', 'email', 'john@company.com'],
                            ] as [$field, $label, $type, $autocomplete, $placeholder])
                                <div>
                                    <label for="contact-{{ $field }}" class="field-label">{{ $label }} <span class="text-primary">*</span></label>
                                    <input id="contact-{{ $field }}" name="{{ $field }}" type="{{ $type }}" autocomplete="{{ $autocomplete }}" required maxlength="{{ $field === 'name' ? 100 : 255 }}"
                                           value="{{ old($field) }}" placeholder="{{ $placeholder }}" class="field" aria-describedby="contact-{{ $field }}-error">
                                    <p id="contact-{{ $field }}-error" class="mt-1.5 text-xs text-red-600 dark:text-red-400" x-text="errors.{{ $field }}?.[0]">@error($field){{ $message }}@enderror</p>
                                </div>
                            @endforeach

                            <div class="sm:col-span-2">
                                <label for="contact-subject" class="field-label">Subject</label>
                                <input id="contact-subject" name="subject" type="text" maxlength="150" value="{{ old('subject') }}" placeholder="Job opportunity, project, question…" class="field" aria-describedby="contact-subject-error">
                                <p id="contact-subject-error" class="mt-1.5 text-xs text-red-600 dark:text-red-400" x-text="errors.subject?.[0]">@error('subject'){{ $message }}@enderror</p>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="contact-message" class="field-label">Message <span class="text-primary">*</span></label>
                                <textarea id="contact-message" name="message" rows="5" required minlength="10" maxlength="5000" placeholder="How can I help?" class="field resize-y" aria-describedby="contact-message-error">{{ old('message') }}</textarea>
                                <p id="contact-message-error" class="mt-1.5 text-xs text-red-600 dark:text-red-400" x-text="errors.message?.[0]">@error('message'){{ $message }}@enderror</p>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-7 w-full sm:w-auto" :disabled="sending" :class="sending && 'opacity-70 cursor-wait'">
                            <x-site.icon name="ui-send" class="size-4" />
                            <span x-text="sending ? 'Sending…' : 'Send Message'">Send Message</span>
                        </button>
                    </form>
                @elseif ($profile->email)
                    <div class="card p-10 text-center" data-reveal>
                        <p class="text-lg text-muted">The fastest way to reach me is by email.</p>
                        <a href="mailto:{{ $profile->email }}" class="btn btn-primary mt-6">Write an email</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
