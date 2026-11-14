<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'BookUpdate',
    type: 'object',
    description: 'Kirim hanya field yang ingin diubah. Field yang dikirim tetap harus valid.',
    properties: [
        new OA\Property(property: 'title', type: 'string', maxLength: 255),
        new OA\Property(property: 'author', type: 'string', maxLength: 255),
        new OA\Property(property: 'publisher', type: 'string', nullable: true, maxLength: 255),
        new OA\Property(property: 'isbn', type: 'string', maxLength: 20),
        new OA\Property(property: 'published_year', type: 'integer', minimum: 1000),
        new OA\Property(property: 'stock', type: 'integer', minimum: 0),
        new OA\Property(property: 'description', type: 'string', nullable: true),
    ]
)]
class UpdateBookRequest extends FormRequest
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
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'author' => ['sometimes', 'required', 'string', 'max:255'],
            'publisher' => ['sometimes', 'nullable', 'string', 'max:255'],
            'isbn' => [
                'sometimes', 'required', 'string', 'max:20',
                Rule::unique('books', 'isbn')->ignore($this->route('book')),
            ],
            'published_year' => ['sometimes', 'required', 'integer', 'min:1000', 'max:'.date('Y')],
            'stock' => ['sometimes', 'required', 'integer', 'min:0'],
            'description' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
