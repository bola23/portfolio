<?php

namespace App\Notifications;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContactMessage extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public ContactMessage $contactMessage) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = $this->contactMessage;

        return (new MailMessage)
            ->subject('New portfolio message: '.($message->subject ?: 'from '.$message->name))
            ->replyTo($message->email, $message->name)
            ->greeting('New message from '.$message->name)
            ->line('**Email:** '.$message->email)
            ->line('**Subject:** '.($message->subject ?: '—'))
            ->line(str($message->message)->limit(1500)->toString())
            ->action('Open in dashboard', ContactMessageResource::getUrl('view', ['record' => $message], panel: 'admin'));
    }
}
