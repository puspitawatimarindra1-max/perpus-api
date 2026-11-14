<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

class BookController extends Controller
{
    #[OA\Get(
        path: '/api/books',
        operationId: 'listBooks',
        summary: 'Daftar buku',
        description: 'Mengembalikan buku terbaru terlebih dahulu dengan paginasi opsional dan pencarian judul atau penulis.',
        tags: ['Books'],
        parameters: [
            new OA\Parameter(
                name: 'search',
                in: 'query',
                description: 'Kata kunci judul atau penulis',
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'per_page',
                in: 'query',
                description: 'Jumlah buku per halaman (1-100, default 10)',
                schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, default: 10)
            ),
            new OA\Parameter(
                name: 'page',
                in: 'query',
                description: 'Nomor halaman',
                schema: new OA\Schema(type: 'integer', minimum: 1, default: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Daftar buku berhasil diambil',
                content: new OA\JsonContent(ref: '#/components/schemas/BookCollection')
            ),
            new OA\Response(response: 401, description: 'Token tidak ada atau tidak valid'),
        ],
        security: [['bearerAuth' => []]]
    )]
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(max($request->integer('per_page', 10), 1), 100);
        $search = $request->string('search')->trim()->toString();

        $books = Book::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        return BookResource::collection($books);
    }

    #[OA\Post(
        path: '/api/books',
        operationId: 'createBook',
        summary: 'Tambah buku',
        tags: ['Books'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/BookInput')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Buku berhasil dibuat',
                content: new OA\JsonContent(ref: '#/components/schemas/BookResource')
            ),
            new OA\Response(
                response: 422,
                description: 'Validasi gagal atau ISBN sudah digunakan',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
            ),
            new OA\Response(response: 401, description: 'Token tidak ada atau tidak valid'),
        ],
        security: [['bearerAuth' => []]]
    )]
    public function store(StoreBookRequest $request): JsonResponse
    {
        $book = Book::create($request->validated());

        return (new BookResource($book))->response()->setStatusCode(201);
    }

    #[OA\Get(
        path: '/api/books/{book}',
        operationId: 'getBook',
        summary: 'Detail buku',
        tags: ['Books'],
        parameters: [
            new OA\Parameter(
                name: 'book',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', minimum: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Detail buku',
                content: new OA\JsonContent(ref: '#/components/schemas/BookResource')
            ),
            new OA\Response(response: 401, description: 'Token tidak ada atau tidak valid'),
            new OA\Response(response: 404, description: 'Buku tidak ditemukan'),
        ],
        security: [['bearerAuth' => []]]
    )]
    public function show(Book $book): BookResource
    {
        return new BookResource($book);
    }

    #[OA\Put(
        path: '/api/books/{book}',
        operationId: 'replaceBookFields',
        summary: 'Perbarui buku',
        description: 'Field dapat dikirim sebagian; field yang disediakan harus memenuhi validasi.',
        tags: ['Books'],
        parameters: [
            new OA\Parameter(
                name: 'book',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', minimum: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/BookUpdate')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Buku berhasil diperbarui',
                content: new OA\JsonContent(ref: '#/components/schemas/BookResource')
            ),
            new OA\Response(
                response: 422,
                description: 'Validasi gagal atau ISBN sudah digunakan',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
            ),
            new OA\Response(response: 401, description: 'Token tidak ada atau tidak valid'),
            new OA\Response(response: 404, description: 'Buku tidak ditemukan'),
        ],
        security: [['bearerAuth' => []]]
    )]
    #[OA\Patch(
        path: '/api/books/{book}',
        operationId: 'partiallyUpdateBook',
        summary: 'Perbarui sebagian field buku',
        tags: ['Books'],
        parameters: [
            new OA\Parameter(
                name: 'book',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', minimum: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/BookUpdate')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Buku berhasil diperbarui',
                content: new OA\JsonContent(ref: '#/components/schemas/BookResource')
            ),
            new OA\Response(
                response: 422,
                description: 'Validasi gagal atau ISBN sudah digunakan',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')
            ),
            new OA\Response(response: 401, description: 'Token tidak ada atau tidak valid'),
            new OA\Response(response: 404, description: 'Buku tidak ditemukan'),
        ],
        security: [['bearerAuth' => []]]
    )]
    public function update(UpdateBookRequest $request, Book $book): BookResource
    {
        $book->update($request->validated());

        return new BookResource($book);
    }

    #[OA\Delete(
        path: '/api/books/{book}',
        operationId: 'deleteBook',
        summary: 'Hapus buku',
        tags: ['Books'],
        parameters: [
            new OA\Parameter(
                name: 'book',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', minimum: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Buku berhasil dihapus',
                content: new OA\JsonContent(ref: '#/components/schemas/MessageResponse')
            ),
            new OA\Response(response: 401, description: 'Token tidak ada atau tidak valid'),
            new OA\Response(response: 404, description: 'Buku tidak ditemukan'),
        ],
        security: [['bearerAuth' => []]]
    )]
    public function destroy(Book $book): JsonResponse
    {
        $book->delete();

        return response()->json(['message' => 'Buku berhasil dihapus']);
    }
}
