@props(['html'])
@if (filled(strip_tags((string) $html)))
    <div {{ $attributes->merge(['class' => 'prose-portfolio']) }}>
        {!! \Illuminate\Support\Str::sanitizeHtml((string) $html) !!}
    </div>
@endif
