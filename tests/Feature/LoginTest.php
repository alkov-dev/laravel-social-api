<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_successful()
    {
        $password = 'password123';
        $user = User::factory()->create([
            'email' => 'user1@gmail.com',
            'password' => Hash::make($password),
        ]);

        $body = [
            'email' => 'user1@gmail.com',
            'password' => $password,
        ];

        $response = $this->postJson('/api/login', $body);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'user' => ['id','name','email'],
                'token',
            ]);
    }

    public function test_login_validation_error()
    {
        $response = $this->postJson('/api/login', ['email' => 'not-an-email']);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'errors',
            ]);
    }

    public function test_login_wrong_credentials()
    {
        $password = 'password123';
        $user = User::factory()->create([
            'email' => 'user1@gmail.com',
            'password' => Hash::make($password),
        ]);

        $body = [
            'email' => 'user1@gmail.com',
            'password' => 'wrongpass',
        ];

        $response = $this->postJson('/api/login', $body);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
            ]);
    }
}
