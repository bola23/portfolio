<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use Throwable;

/**
 * Stores uploaded raster images as resized WebP files plus a small
 * responsive variant ("-sm.webp") on the public disk.
 */
class ImageOptimizer
{
    public const SMALL_WIDTH = 640;

    private const QUALITY = 82;

    public function __construct(private readonly ImageManager $images) {}

    /**
     * Store an uploaded image in the given directory and return its path.
     * Falls back to storing the original file if it cannot be processed.
     */
    public function store(UploadedFile $file, string $directory, int $maxWidth = 1920): string
    {
        $disk = Storage::disk('public');
        $path = trim($directory, '/').'/'.Str::ulid()->toBase32().'.webp';

        try {
            // Decode once; the small variant is derived from the already scaled image.
            $image = $this->images->decodeBinary($file->get());

            $image->scaleDown(width: $maxWidth);
            $disk->put($path, $this->encode($image));

            // Small uploads need no variant: it would be an identical copy that
            // browsers could download twice through srcset.
            if ($image->width() > self::SMALL_WIDTH) {
                $image->scaleDown(width: self::SMALL_WIDTH);
                $disk->put(self::smallVariant($path), $this->encode($image));
            }

            return $path;
        } catch (Throwable $exception) {
            Log::warning('Image optimization skipped; storing original upload.', ['error' => $exception->getMessage()]);

            return $file->store($directory, 'public');
        }
    }

    public function delete(?string $path): void
    {
        if (blank($path)) {
            return;
        }

        Storage::disk('public')->delete(array_unique([$path, self::smallVariant($path)]));
    }

    public static function smallVariant(string $path): string
    {
        return preg_replace('/\.webp$/', '-sm.webp', $path) ?? $path;
    }

    private function encode(ImageInterface $image): string
    {
        return (string) $image->encode(new WebpEncoder(quality: self::QUALITY, strip: true));
    }
}
