<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;

class ContactMessageRequest extends FormRequest
{
    /**
     * Name of the hidden honeypot input. Humans never see or fill it.
     */
    public const HONEYPOT = 'website';

    /**
     * Name of the hidden, encrypted "form rendered at" timestamp input.
     */
    public const TIMESTAMP = 'form_token';

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'string', 'email:rfc', 'max:255'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            self::HONEYPOT => ['nullable', 'string', 'max:255'],
            self::TIMESTAMP => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'message.min' => 'Please write a little more about what you need (at least :min characters).',
        ];
    }

    /**
     * Bots typically fill every field or submit instantly after the page loads.
     */
    public function isLikelySpam(): bool
    {
        if (filled($this->input(self::HONEYPOT))) {
            return true;
        }

        try {
            $renderedAt = (int) Crypt::decryptString((string) $this->input(self::TIMESTAMP));
        } catch (DecryptException) {
            return true;
        }

        return now()->timestamp - $renderedAt < config('portfolio.contact_min_seconds');
    }

    /**
     * @return array{name: string, email: string, subject: string|null, message: string}
     */
    public function messageAttributes(): array
    {
        /** @var array{name: string, email: string, subject: string|null, message: string} */
        return [
            'name' => strip_tags((string) $this->validated('name')),
            'email' => (string) $this->validated('email'),
            'subject' => filled($subject = $this->validated('subject')) ? strip_tags((string) $subject) : null,
            'message' => strip_tags((string) $this->validated('message')),
        ];
    }

    /**
     * Encrypted timestamp embedded in the contact form when it is rendered.
     */
    public static function freshToken(): string
    {
        return Crypt::encryptString((string) now()->timestamp);
    }
}
