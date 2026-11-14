<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Book',
    type: 'object',
    required: ['id', 'title', 'author', 'isbn', 'published_year', 'stock'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'title', type: 'string', example: 'Bumi Manusia'),
        new OA\Property(property: 'author', type: 'string', example: 'Pramoedya Ananta Toer'),
        new OA\Property(property: 'publisher', type: 'string', nullable: true, example: 'Lentera Dipantara'),
        new OA\Property(property: 'isbn', type: 'string', example: '9789799731234'),
        new OA\Property(property: 'published_year', type: 'integer', example: 1980),
        new OA\Property(property: 'stock', type: 'integer', example: 12),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Tetralogi Buru bagian pertama.'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'BookResource',
    type: 'object',
    properties: [
        new OA\Property(property: 'data', ref: '#/components/schemas/Book'),
    ]
)]
#[OA\Schema(
    schema: 'BookCollection',
    type: 'object',
    properties: [
        new OA\Property(
            property: 'data',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/Book')
        ),
        new OA\Property(property: 'links', type: 'object', additionalProperties: true),
        new OA\Property(property: 'meta', type: 'object', additionalProperties: true),
    ]
)]
class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'publisher' => $this->publisher,
            'isbn' => $this->isbn,
            'published_year' => $this->published_year,
            'stock' => $this->stock,
            'description' => $this->description,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
