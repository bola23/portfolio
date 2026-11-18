<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicFileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    public function test_uploaded_files_are_served_when_the_storage_symlink_is_missing(): void
    {
        Storage::disk('public')->put('profile/avatar.webp', 'image-bytes');

        $response = $this->get('/storage/profile/avatar.webp');

        $response->assertOk()
            ->assertHeader('Content-Type', 'image/webp')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertCookieMissing(config('session.cookie'));
        $this->assertStringContainsString('immutable', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('sandbox', $response->headers->get('Content-Security-Policy'));
        $this->assertSame('image-bytes', $response->streamedContent());
    }

    public function test_missing_files_return_not_found(): void
    {
        $this->get('/storage/profile/missing.webp')->assertNotFound();
    }

    public function test_paths_cannot_escape_the_public_disk(): void
    {
        $this->get('/storage/..%2F..%2F.env')->assertNotFound();
        $this->get('/storage/profile/../../../.env')->assertNotFound();
    }
}
