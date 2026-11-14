<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_endpoints_require_a_valid_token(): void
    {
        $this->getJson('/api/books')->assertUnauthorized();
        $this->postJson('/api/books', [])->assertUnauthorized();
    }

    public function test_authenticated_user_can_create_list_update_and_delete_books(): void
    {
        $this->authenticate();

        $this->postJson('/api/books', $this->bookData())
            ->assertCreated()
            ->assertJsonPath('data.title', 'Dune');

        $book = Book::query()->firstOrFail();

        $this->getJson('/api/books?search=Dune&per_page=1')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $book->id);

        $this->getJson("/api/books/{$book->id}")
            ->assertOk()
            ->assertJsonPath('data.isbn', '9780441013593');

        $this->patchJson("/api/books/{$book->id}", ['stock' => 7])
            ->assertOk()
            ->assertJsonPath('data.stock', 7);

        $this->deleteJson("/api/books/{$book->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Buku berhasil dihapus');

        $this->getJson("/api/books/{$book->id}")->assertNotFound();
    }

    public function test_book_validation_rejects_duplicate_isbns_and_negative_stock(): void
    {
        $this->authenticate();
        Book::factory()->create(['isbn' => '9780441013593']);

        $this->postJson('/api/books', $this->bookData(['stock' => -1]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['isbn', 'stock']);
    }

    public function test_update_allows_its_own_isbn_but_rejects_another_books_isbn(): void
    {
        $this->authenticate();
        $book = Book::factory()->create();
        $otherBook = Book::factory()->create();

        $this->patchJson("/api/books/{$book->id}", ['isbn' => $book->isbn])
            ->assertOk();

        $this->patchJson("/api/books/{$book->id}", ['isbn' => $otherBook->isbn])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['isbn']);
    }

    public function test_registration_token_authenticates_and_logout_revokes_it(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertCreated();

        $token = $response->json('token');
        $tokenId = explode('|', $token)[0];
        $this->withToken($token)
            ->getJson('/api/me')
            ->assertOk()
            ->assertJsonPath('data.email', 'test@example.com');

        $this->postJson('/api/logout')->assertOk();
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $tokenId]);
        Auth::forgetGuards();
        $this->getJson('/api/me')->assertUnauthorized();
    }

    private function authenticate(): void
    {
        $user = User::factory()->create();
        $this->withToken($user->createToken('test-token')->plainTextToken);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function bookData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Dune',
            'author' => 'Frank Herbert',
            'publisher' => 'Chilton Books',
            'isbn' => '9780441013593',
            'published_year' => 1965,
            'stock' => 3,
            'description' => 'A science fiction novel.',
        ], $overrides);
    }
}
