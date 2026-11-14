<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'Perpustakaan API',
    version: '1.0.0',
    description: 'REST API Laravel untuk autentikasi Sanctum dan pengelolaan katalog buku.',
    contact: new OA\Contact(name: 'Admin Perpustakaan', email: 'admin@example.com')
)]
#[OA\Server(url: L5_SWAGGER_CONST_HOST, description: 'Server API')]
#[OA\Tag(name: 'Authentication', description: 'Registrasi dan autentikasi pengguna')]
#[OA\Tag(name: 'Books', description: 'Pengelolaan katalog buku')]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Sanctum token'
)]
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
    schema: 'BookInput',
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
#[OA\Schema(
    schema: 'ValidationError',
    type: 'object',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'The given data was invalid.'),
        new OA\Property(
            property: 'errors',
            type: 'object',
            additionalProperties: new OA\AdditionalProperties(
                type: 'array',
                items: new OA\Items(type: 'string')
            )
        ),
    ]
)]
#[OA\Schema(
    schema: 'MessageResponse',
    type: 'object',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Buku berhasil dihapus'),
    ]
)]
#[OA\Schema(
    schema: 'User',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Nama Pengguna'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'UserResource',
    type: 'object',
    properties: [
        new OA\Property(property: 'data', ref: '#/components/schemas/User'),
    ]
)]
#[OA\Schema(
    schema: 'RegisterInput',
    type: 'object',
    required: ['name', 'email', 'password', 'password_confirmation'],
    properties: [
        new OA\Property(property: 'name', type: 'string', maxLength: 100, example: 'Nama Pengguna'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
        new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 8),
        new OA\Property(property: 'password_confirmation', type: 'string', format: 'password'),
    ]
)]
#[OA\Schema(
    schema: 'LoginInput',
    type: 'object',
    required: ['email', 'password'],
    properties: [
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
        new OA\Property(property: 'password', type: 'string', format: 'password'),
        new OA\Property(property: 'device_name', type: 'string', nullable: true, maxLength: 100),
    ]
)]
#[OA\Schema(
    schema: 'TokenResponse',
    type: 'object',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Login berhasil'),
        new OA\Property(property: 'token', type: 'string', example: '1|your-personal-access-token'),
        new OA\Property(property: 'token_type', type: 'string', example: 'Bearer'),
        new OA\Property(property: 'user', ref: '#/components/schemas/User'),
    ]
)]
class ApiDocumentation
{
}
