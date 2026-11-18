<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Services\PortfolioContent;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(PortfolioContent $content): View
    {
        return view('site.home', [
            ...$content->home(),
            'profile' => $content->profile(),
            'settings' => $content->settings(),
            'socialLinks' => $content->socialLinks(),
        ]);
    }
}
