<?php

namespace App\Models;

use App\Enums\MessageStatus;
use Database\Factories\ContactMessageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'email', 'subject', 'message', 'status', 'read_at', 'ip_address', 'user_agent'])]
class ContactMessage extends Model
{
    /** @use HasFactory<ContactMessageFactory> */
    use HasFactory;

    public function isUnread(): bool
    {
        return $this->read_at === null;
    }

    public function markAsRead(): void
    {
        if ($this->isUnread()) {
            $this->forceFill([
                'read_at' => now(),
                'status' => $this->status === MessageStatus::New ? MessageStatus::Read : $this->status,
            ])->save();
        }
    }

    public function markAsUnread(): void
    {
        $this->forceFill(['read_at' => null, 'status' => MessageStatus::New])->save();
    }

    /**
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function unread(Builder $query): void
    {
        $query->whereNull('read_at');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => MessageStatus::class,
            'read_at' => 'datetime',
        ];
    }
}
