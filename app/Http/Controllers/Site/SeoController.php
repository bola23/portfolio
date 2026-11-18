<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Services\PortfolioContent;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SeoController extends Controller
{
    public function sitemap(PortfolioContent $content): Response
    {
        return response()
            ->view('site.sitemap', ['projects' => $content->publishedProjects()])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /livewire',
            'Allow: /',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode("\n", $lines)."\n")->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function cv(PortfolioContent $content): StreamedResponse
    {
        $profile = $content->profile();
        $disk = Storage::disk('public');

        abort_if(blank($profile->cv_path) || ! $disk->exists($profile->cv_path), 404);

        return $disk->download($profile->cv_path, Str::slug($profile->name).'-cv.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
