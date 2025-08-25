<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesUserWithToken;

class AuthApiTest extends TestCase
{
    use RefreshDatabase, CreatesUserWithToken;

    public function test_a_user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Matheus Rodrigues',
            'email' => 'matheus@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ]);

        $response->assertStatus(201)
                 ->assertJson(['message' => 'Usuário registrado com sucesso']);
    }

    public function test_a_user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'matheus@example.com',
            'password' => bcrypt('12345678'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'matheus@example.com',
            'password' => '12345678',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['token']);
    }

    public function test_a_user_can_logout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/logout');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Logout realizado com sucesso']);
    }

    public function test_a_user_cannot_login_with_wrong_password()
    {
        $user = User::factory()->create([
            'email' => 'teste@example.com',
            'password' => bcrypt('12345678'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'teste@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401) // agora bate com o AuthController
                 ->assertJson(['message' => 'Credenciais inválidas']);
    }
}