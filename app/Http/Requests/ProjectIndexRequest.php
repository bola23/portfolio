<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Invalid filter values are discarded instead of failing the page.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'category' => $this->sanitizeSlug($this->query('category')),
            'tech' => $this->sanitizeSlug($this->query('tech')),
        ]);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'category' => ['nullable', 'string', 'max:100'],
            'tech' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function category(): ?string
    {
        return $this->validated('category');
    }

    public function technology(): ?string
    {
        return $this->validated('tech');
    }

    private function sanitizeSlug(mixed $value): ?string
    {
        return is_string($value) && preg_match('/^[a-z0-9-]{1,100}$/', $value) ? $value : null;
    }
}
