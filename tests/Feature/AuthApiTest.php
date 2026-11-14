<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_log_in_and_use_the_returned_token(): void
    {
        User::factory()->create([
            'email' => 'reader@example.com',
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'reader@example.com',
            'password' => 'password123',
            'device_name' => 'test-device',
        ])->assertOk()
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.email', 'reader@example.com')
            ->assertJsonMissingPath('user.password');

        $this->withToken($response->json('token'))
            ->getJson('/api/me')
            ->assertOk()
            ->assertJsonPath('data.email', 'reader@example.com');
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'reader@example.com',
            'password' => 'password123',
        ]);

        $this->postJson('/api/login', [
            'email' => 'reader@example.com',
            'password' => 'incorrect',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_registration_validates_unique_email_and_password_confirmation(): void
    {
        User::factory()->create(['email' => 'reader@example.com']);

        $this->postJson('/api/register', [
            'name' => 'Another Reader',
            'email' => 'reader@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different-password',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password']);

        $this->assertDatabaseCount('users', 1);
    }
}
