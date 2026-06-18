<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_successful()
    {
        $body = [
            'name' => 'Test User',
            'email' => 'newuser@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ];

        $response = $this->postJson('/api/register', $body);

        $response->assertStatus(201)
            ->assertJsonStructure(['success', 'message', 'user', 'token']);

        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
    }

    public function test_register_validation_error()
    {
        $response = $this->postJson('/api/register', [
            'name' => '',
            'email' => 'invalid',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['success', 'errors']);
    }

    public function test_me_requires_auth_and_returns_user()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // unauthenticated
        $this->getJson('/api/me')->assertStatus(401);

        // authenticated
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/me');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'user' => ['id', 'email']]);
    }

    public function test_logout_revokes_token()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // token record should be deleted from database
        $user->refresh();
        $this->assertCount(0, $user->tokens);
    }
}
