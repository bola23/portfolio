<?php

namespace App\View\Components\Layouts;

use App\Models\Profile;
use App\Models\SiteSetting;
use App\Models\SocialLink;
use App\Services\PortfolioContent;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Component;

/**
 * Public website layout: SEO tags, theme tokens, navigation and footer.
 */
class Site extends Component
{
    public SiteSetting $settings;

    public Profile $profile;

    /** @var Collection<int, SocialLink> */
    public Collection $socialLinks;

    public function __construct(
        PortfolioContent $content,
        public ?string $title = null,
        public ?string $description = null,
        public ?string $image = null,
        public string $type = 'website',
        public bool $isHome = false,
        public bool $noindex = false,
    ) {
        $this->settings = $content->settings();
        $this->profile = $content->profile();
        $this->socialLinks = $content->socialLinks();
    }

    public function pageTitle(): string
    {
        $default = $this->settings->meta_title ?: $this->settings->site_name;

        return $this->title ? "{$this->title} — {$this->settings->site_name}" : $default;
    }

    public function metaDescription(): string
    {
        return str($this->description ?: $this->settings->meta_description ?: (string) $this->profile->short_intro)
            ->stripTags()->squish()->limit(160)->toString();
    }

    public function shareImage(): ?string
    {
        $path = $this->image ?: $this->settings->og_image ?: $this->profile->avatar;

        return $path ? Storage::disk('public')->url($path) : null;
    }

    public function initials(): string
    {
        return str($this->profile->name)->explode(' ')->filter()->take(2)
            ->map(fn (string $word): string => mb_strtoupper(mb_substr($word, 0, 1)))
            ->implode('');
    }

    public function color(?string $value, string $fallback): string
    {
        return preg_match('/^#[0-9a-fA-F]{6}$/', (string) $value) ? $value : $fallback;
    }

    public function render(): View
    {
        return view('components.layouts.site');
    }
}
