<?php

namespace App\Support;

use BladeUI\Icons\Exceptions\SvgNotFound;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;

/**
 * Resolves icon keys stored in the database to trusted, server-side SVG markup.
 *
 * Keys are either a Simple Icons slug (e.g. "laravel") or one of the
 * built-in "ui-*" glyphs. Unknown keys resolve to null so the UI can fall
 * back to a monogram. Uploaded SVGs are never rendered inline.
 */
final class Icon
{
    /**
     * Minimal glyphs that are not part of Simple Icons (LinkedIn was removed upstream).
     *
     * @var array<string, string>
     */
    private const INLINE = [
        'linkedin' => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.45 20.45h-3.55v-5.57c0-1.33-.03-3.04-1.85-3.04-1.86 0-2.14 1.45-2.14 2.94v5.67H9.35V9h3.41v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28ZM5.34 7.43a2.06 2.06 0 1 1 0-4.12 2.06 2.06 0 0 1 0 4.12ZM7.12 20.45H3.56V9h3.56v11.45ZM22.22 0H1.77C.79 0 0 .77 0 1.73v20.54C0 23.23.79 24 1.77 24h20.45c.98 0 1.78-.77 1.78-1.73V1.73C24 .77 23.2 0 22.22 0Z"/></svg>',
        'ui-globe' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 3.8 5.7 3.8 9s-1.3 6.3-3.8 9c-2.5-2.7-3.8-5.7-3.8-9S9.5 5.7 12 3Z"/></svg>',
        'ui-mail' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg>',
        'ui-phone' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2Z"/></svg>',
        'ui-server' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="3" y="4" width="18" height="7" rx="1.5"/><rect x="3" y="13" width="18" height="7" rx="1.5"/><path d="M7 7.5h.01M7 16.5h.01"/></svg>',
        'ui-api' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="m8 7-5 5 5 5M16 7l5 5-5 5M14 4l-4 16"/></svg>',
        'ui-database' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><ellipse cx="12" cy="5.5" rx="8" ry="2.5"/><path d="M4 5.5v13c0 1.4 3.6 2.5 8 2.5s8-1.1 8-2.5v-13M4 12c0 1.4 3.6 2.5 8 2.5s8-1.1 8-2.5"/></svg>',
        'ui-plug' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M9 3v5M15 3v5M6 8h12v3a6 6 0 0 1-12 0V8ZM12 17v4"/></svg>',
        'ui-gauge' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M4 18a9 9 0 1 1 16 0"/><path d="m12 14 4-5"/><circle cx="12" cy="14" r="1.2"/></svg>',
        'ui-layers' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 13 9 5 9-5"/></svg>',
        'ui-code' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="m9 10-2 2 2 2M15 10l2 2-2 2M13 9l-2 6"/></svg>',
        'ui-shield' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M12 3 4 6v6c0 4.5 3.4 8.3 8 9 4.6-.7 8-4.5 8-9V6l-8-3Z"/><path d="m9 12 2 2 4-4"/></svg>',
        'ui-dashboard' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>',
        'ui-cart' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M3 4h2l2.4 11h10.8L21 8H6.3"/><circle cx="9" cy="19.5" r="1.3"/><circle cx="17" cy="19.5" r="1.3"/></svg>',
        'ui-graduation' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="m2 9 10-5 10 5-10 5L2 9Z"/><path d="M6 11v5c3 2.5 9 2.5 12 0v-5"/></svg>',
        'ui-truck' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M3 6h11v10H3zM14 10h4l3 3v3h-7"/><circle cx="7" cy="17.5" r="1.8"/><circle cx="17" cy="17.5" r="1.8"/></svg>',
        'ui-heart-pulse' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M20.5 12.5 12 21l-8.5-8.5A5 5 0 0 1 12 6a5 5 0 0 1 8.5 6.5Z"/><path d="M3.5 12h4l2-3 3 6 2-3h6"/></svg>',
        'ui-pin' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.5"/></svg>',
        'ui-briefcase' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M3 13h18"/></svg>',
        'ui-check-circle' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/></svg>',
        'ui-certificate' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="3" y="4" width="18" height="13" rx="2"/><path d="M7 8h10M7 11h6"/><circle cx="16" cy="16" r="3"/><path d="m14.5 18.5-1 3.5 2.5-1.2 2.5 1.2-1-3.5"/></svg>',
        'ui-send' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m22 2-11 11M22 2l-7 20-4-9-9-4 20-7Z"/></svg>',
        'ui-sparkles' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M12 3v4M12 17v4M3 12h4M17 12h4M6 6l2.5 2.5M15.5 15.5 18 18M6 18l2.5-2.5M15.5 8.5 18 6"/></svg>',
    ];

    public static function svg(?string $key, string $class = ''): ?HtmlString
    {
        if (blank($key)) {
            return null;
        }

        if (isset(self::INLINE[$key])) {
            $svg = self::INLINE[$key];

            return new HtmlString($class === '' ? $svg : str_replace('<svg ', '<svg class="'.e($class).'" ', $svg));
        }

        try {
            return new HtmlString(svg('si-'.$key, $class, ['aria-hidden' => 'true', 'fill' => 'currentColor'])->toHtml());
        } catch (SvgNotFound) {
            return null;
        }
    }

    /**
     * Icons referenced on the current page, rendered once by sprite().
     *
     * @var array<string, array{viewBox: string, body: string, paint: string}>
     */
    private static array $used = [];

    /**
     * Lightweight <use> reference; the symbol is emitted once per page by sprite().
     */
    public static function use(?string $key, string $class = ''): ?HtmlString
    {
        if (! isset(self::$used[$key ?? ''])) {
            $svg = self::svg($key);

            if ($svg === null) {
                return null;
            }

            preg_match('/viewBox="([^"]+)"/', $svg->toHtml(), $viewBox);
            preg_match('/<svg[^>]*>(.*)<\/svg>/s', $svg->toHtml(), $body);
            $isStroked = str_contains($svg->toHtml(), 'stroke="currentColor"');

            self::$used[$key] = [
                'viewBox' => $viewBox[1] ?? '0 0 24 24',
                'body' => $body[1] ?? '',
                'paint' => $isStroked ? 'fill="none" stroke="currentColor" stroke-width="1.6"' : 'fill="currentColor"',
            ];
        }

        $symbol = self::$used[$key];

        return new HtmlString(sprintf(
            '<svg class="%s" viewBox="%s" %s aria-hidden="true"><use href="#icon-%s"></use></svg>',
            e($class),
            e($symbol['viewBox']),
            $symbol['paint'],
            e($key),
        ));
    }

    /**
     * Hidden SVG sprite containing every icon referenced via use(); resets the registry.
     */
    public static function sprite(): HtmlString
    {
        $symbols = collect(self::$used)
            ->map(fn (array $icon, string $key): string => sprintf('<symbol id="icon-%s" viewBox="%s">%s</symbol>', e($key), e($icon['viewBox']), $icon['body']))
            ->implode('');

        self::$used = [];

        return new HtmlString($symbols === '' ? '' : '<svg xmlns="http://www.w3.org/2000/svg" style="display:none" aria-hidden="true">'.$symbols.'</svg>');
    }

    public static function exists(?string $key): bool
    {
        return filled($key) && (isset(self::INLINE[$key]) || in_array($key, self::brandKeys(), true));
    }

    /**
     * Options for admin icon pickers: built-in UI glyphs followed by all Simple Icons slugs.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $ui = collect(array_keys(self::INLINE))
            ->mapWithKeys(fn (string $key): array => [$key => str($key)->replace('ui-', 'UI: ')->replace('-', ' ')->title()->toString()]);

        $brands = collect(self::brandKeys())->mapWithKeys(fn (string $key): array => [$key => $key]);

        return $ui->merge($brands)->all();
    }

    /**
     * @return list<string>
     */
    private static function brandKeys(): array
    {
        return Cache::rememberForever('icons:simple-icons', function (): array {
            $directory = base_path('vendor/codeat3/blade-simple-icons/resources/svg');

            return collect(glob($directory.'/*.svg') ?: [])
                ->map(fn (string $file): string => basename($file, '.svg'))
                ->values()
                ->all();
        });
    }
}
