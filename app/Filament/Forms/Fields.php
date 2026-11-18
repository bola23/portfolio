<?php

namespace App\Filament\Forms;

use App\Services\ImageOptimizer;
use App\Support\Icon;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Reusable, pre-configured admin form fields.
 */
final class Fields
{
    /**
     * Allowed raster image types. SVG is intentionally excluded (script injection risk).
     *
     * @var list<string>
     */
    public const IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

    /**
     * Image upload that stores a resized WebP (+ small variant) on the public disk.
     */
    public static function image(string $name, string $directory, int $maxWidth = 1920, int $maxKilobytes = 5120): FileUpload
    {
        return FileUpload::make($name)
            ->image()
            ->acceptedFileTypes(self::IMAGE_TYPES)
            ->maxSize($maxKilobytes)
            ->disk('public')
            ->directory($directory)
            ->visibility('public')
            ->imageEditor()
            ->saveUploadedFileUsing(
                fn (TemporaryUploadedFile $file): string => app(ImageOptimizer::class)->store($file, $directory, $maxWidth),
            )
            ->helperText('JPG, PNG or WebP up to '.round($maxKilobytes / 1024).' MB. Converted to optimized WebP automatically.');
    }

    /**
     * Upload for small brand assets (logo, favicon) kept in their original format.
     */
    public static function brandAsset(string $name, string $directory, int $maxKilobytes = 1024): FileUpload
    {
        return FileUpload::make($name)
            ->image()
            ->acceptedFileTypes(['image/png', 'image/webp', 'image/jpeg', 'image/x-icon', 'image/vnd.microsoft.icon'])
            ->maxSize($maxKilobytes)
            ->disk('public')
            ->directory($directory)
            ->visibility('public');
    }

    /**
     * Searchable picker for Simple Icons slugs and built-in UI glyphs, with live preview.
     */
    public static function icon(string $name = 'icon'): Select
    {
        return Select::make($name)
            ->searchable()
            ->getSearchResultsUsing(fn (string $search): array => collect(Icon::options())
                ->filter(fn (string $label, string $key): bool => str_contains(strtolower($key.' '.$label), strtolower($search)))
                ->take(50)
                ->all())
            ->getOptionLabelUsing(fn (?string $value): ?string => Icon::options()[$value] ?? $value)
            ->rule(fn () => function (string $attribute, mixed $value, \Closure $fail): void {
                if (filled($value) && ! Icon::exists($value)) {
                    $fail('Unknown icon key.');
                }
            })
            ->helperText(new HtmlString('Search <a href="https://simpleicons.org" target="_blank" rel="noopener" class="underline">Simple Icons</a> slugs (e.g. <code>laravel</code>) or built-in <code>ui-*</code> glyphs.'));
    }

    /**
     * Title input that fills the slug while creating a record.
     */
    public static function titleWithSlug(string $titleField = 'title', string $slugField = 'slug'): TextInput
    {
        return TextInput::make($titleField)
            ->required()
            ->maxLength(255)
            ->live(onBlur: true)
            ->afterStateUpdated(function (Set $set, ?string $state, string $operation) use ($slugField): void {
                if ($operation === 'create') {
                    $set($slugField, Str::slug((string) $state));
                }
            });
    }

    public static function slug(string $name = 'slug'): TextInput
    {
        return TextInput::make($name)
            ->required()
            ->maxLength(255)
            ->alphaDash()
            ->unique(ignoreRecord: true)
            ->helperText('Used in URLs. Lowercase letters, numbers and dashes.');
    }

    /**
     * Rich text editor without file attachments (content is sanitized on output).
     */
    public static function richText(string $name): RichEditor
    {
        return RichEditor::make($name)
            ->toolbarButtons([
                ['bold', 'italic', 'underline', 'strike', 'link'],
                ['h2', 'h3'],
                ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                ['undo', 'redo'],
            ])
            ->columnSpanFull();
    }

    public static function sortOrder(): TextInput
    {
        return TextInput::make('sort_order')
            ->label('Display order')
            ->numeric()
            ->integer()
            ->minValue(0)
            ->default(0)
            ->required();
    }
}
