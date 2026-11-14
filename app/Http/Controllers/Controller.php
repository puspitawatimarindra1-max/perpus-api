<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Perpustakaan API',
    description: 'REST API Laravel untuk autentikasi Sanctum dan pengelolaan katalog buku.',
    contact: new OA\Contact(name: 'Admin Perpustakaan', email: 'admin@example.com')
)]
#[OA\Server(url: L5_SWAGGER_CONST_HOST, description: 'Server API')]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Sanctum token',
    description: 'Gunakan token dari endpoint login dengan format Bearer <token>.'
)]
#[OA\Tag(name: 'Authentication', description: 'Registrasi dan autentikasi pengguna')]
#[OA\Tag(name: 'Books', description: 'Pengelolaan katalog buku')]
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
abstract class Controller
{
}
