<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\PathTraversalDetected;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Serves uploaded files from the public disk when the web server cannot,
 * e.g. on hosts where the `public/storage` symlink does not exist (Wasmer Edge).
 * Where the symlink exists the web server serves the file directly and this never runs.
 */
class PublicFileController extends Controller
{
    public function __invoke(string $path): StreamedResponse
    {
        $disk = Storage::disk('public');

        try {
            abort_unless($disk->fileExists($path), 404);
        } catch (PathTraversalDetected) {
            abort(404);
        }

        return $disk->response($path, headers: [
            // Uploads get unique (ULID) names, so a stored file never changes.
            'Cache-Control' => 'public, max-age=31536000, immutable',
            // Never let an uploaded file (e.g. an SVG) run scripts on this origin.
            'Content-Security-Policy' => "default-src 'none'; style-src 'unsafe-inline'; sandbox",
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
