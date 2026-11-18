<?php

namespace Tests\Feature;

use App\Services\ImageOptimizer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageOptimizerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    public function test_large_images_get_a_small_responsive_variant(): void
    {
        $path = app(ImageOptimizer::class)->store(UploadedFile::fake()->image('cover.jpg', 1600, 1000), 'projects');

        Storage::disk('public')->assertExists([$path, ImageOptimizer::smallVariant($path)]);
        $this->assertSame(ImageOptimizer::SMALL_WIDTH, getimagesize(Storage::disk('public')->path(ImageOptimizer::smallVariant($path)))[0]);
    }

    public function test_small_images_are_stored_without_a_duplicate_variant(): void
    {
        $path = app(ImageOptimizer::class)->store(UploadedFile::fake()->image('avatar.jpg', 500, 500), 'profile');

        Storage::disk('public')->assertExists($path);
        Storage::disk('public')->assertMissing(ImageOptimizer::smallVariant($path));
    }
}
