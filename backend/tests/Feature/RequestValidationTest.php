<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RequestValidationTest extends TestCase
{
    use RefreshDatabase;

    /** ---------------------------
     *  Cenários inválidos (422)
     * ---------------------------*/

    public function test_register_requires_fields()
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_login_requires_fields()
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_products_store_requires_fields()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/products', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'price', 'stock']);
    }

    public function test_products_store_negative_price_fails()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/products', [
                             'name'  => 'Produto Teste',
                             'price' => -10,
                             'stock' => 5,
                         ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['price']);
    }

    public function test_products_store_negative_stock_fails()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/products', [
                             'name'  => 'Produto Teste',
                             'price' => 100,
                             'stock' => -1,
                         ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['stock']);
    }

    /** ---------------------------
     *  Cenários válidos (200/201)
     * ---------------------------*/

    public function test_register_valid_data_passes()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'User Test',
            'email' => 'user@test.com',
            'password' => '123456',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'user' => ['id', 'name', 'email']]);
    }

    public function test_login_valid_data_passes()
    {
        $user = User::factory()->create([
            'email' => 'login@test.com',
            'password' => bcrypt('123456'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'login@test.com',
            'password' => '123456',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['message', 'token', 'user']);
    }

    public function test_products_store_valid_data_passes()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/products', [
                             'name'  => 'Produto Válido',
                             'price' => 150,
                             'stock' => 10,
                         ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['data' => ['id', 'name', 'price', 'stock'], 'message']);
    }

    public function test_logout_valid_data_passes()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/logout');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Logout realizado com sucesso']);
    }
}