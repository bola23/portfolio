<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactMessageRequest;
use App\Models\ContactMessage;
use App\Notifications\NewContactMessage;
use App\Services\PortfolioContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    public const SUCCESS_MESSAGE = 'Thanks for reaching out — your message has been received. I will get back to you soon.';

    public function __invoke(ContactMessageRequest $request, PortfolioContent $content): JsonResponse|RedirectResponse
    {
        abort_unless($content->settings()->show_contact_form, 404);

        // Spam submissions receive the same response so bots learn nothing.
        if (! $request->isLikelySpam()) {
            $message = ContactMessage::query()->create([
                ...$request->messageAttributes(),
                'ip_address' => $request->ip(),
                'user_agent' => Str::limit((string) $request->userAgent(), 500, ''),
            ]);

            if (filled($recipient = $content->settings()->contact_notification_email)) {
                Notification::route('mail', $recipient)->notify(new NewContactMessage($message));
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => self::SUCCESS_MESSAGE]);
        }

        return redirect()->to(route('home').'#contact')->with('contact_status', self::SUCCESS_MESSAGE);
    }
}
