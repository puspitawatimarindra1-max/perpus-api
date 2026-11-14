<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'BookRequest',
    type: 'object',
    required: ['title', 'author', 'isbn', 'published_year', 'stock'],
    properties: [
        new OA\Property(property: 'title', type: 'string', maxLength: 255, example: 'Bumi Manusia'),
        new OA\Property(property: 'author', type: 'string', maxLength: 255, example: 'Pramoedya Ananta Toer'),
        new OA\Property(property: 'publisher', type: 'string', nullable: true, maxLength: 255),
        new OA\Property(property: 'isbn', type: 'string', maxLength: 20, example: '9789799731234'),
        new OA\Property(property: 'published_year', type: 'integer', minimum: 1000, example: 1980),
        new OA\Property(property: 'stock', type: 'integer', minimum: 0, example: 12),
        new OA\Property(property: 'description', type: 'string', nullable: true),
    ]
)]
class StoreBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'max:20', 'unique:books,isbn'],
            'published_year' => ['required', 'integer', 'min:1000', 'max:'.date('Y')],
            'stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
        ];
    }
}
