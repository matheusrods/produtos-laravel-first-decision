<?php

namespace Tests\Feature\Products;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesUserWithToken;

class ProductApiTest extends TestCase
{
    use RefreshDatabase, CreatesUserWithToken;

    private function authUser()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        return ['Authorization' => 'Bearer ' . $token];
    }

    public function test_an_authenticated_user_can_create_a_product()
    {
        $headers = $this->authUser();

        $response = $this->withHeaders($headers)->postJson('/api/products', [
            'name' => 'Produto Teste',
            'price' => 99.90,
            'stock' => 10,
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'Produto Teste']);
    }

    public function test_it_can_list_products()
    {
        Product::factory()->count(3)->create();

        $headers = $this->authUser();

        $response = $this->withHeaders($headers)->getJson('/api/products');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_an_authenticated_user_can_update_a_product()
    {
        $headers = $this->authUser();

        $product = Product::factory()->create();

        $response = $this->withHeaders($headers)->putJson("/api/products/{$product->id}", [
            'name' => 'Produto Atualizado',
            'price' => 199.90,
            'stock' => 20,
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Produto Atualizado']);
    }

    public function test_it_returns_validation_error_when_updating_invalid_product()
    {
        $headers = $this->authUser();

        $product = Product::factory()->create();

        $response = $this->withHeaders($headers)->putJson("/api/products/{$product->id}", [
            'price' => -50, // preço inválido
            'stock' => -10, // estoque inválido
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['price', 'stock']);
    }

    public function test_it_returns_validation_error_when_creating_duplicate_name()
    {
        $headers = $this->authUser();

        Product::factory()->create(['name' => 'Duplicado']);

        $response = $this->withHeaders($headers)->postJson('/api/products', [
            'name' => 'Duplicado',
            'price' => 50,
            'stock' => 5,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name']);
    }

    public function test_an_authenticated_user_can_delete_a_product()
    {
        $headers = $this->authUser();

        $product = Product::factory()->create();

        $response = $this->withHeaders($headers)->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Produto removido com sucesso']);
    }

    public function test_unauthenticated_user_cannot_access_products()
    {
        $response = $this->getJson('/api/products');

        $response->assertStatus(401);
    }

    public function test_user_cannot_access_with_invalid_token()
    {
        $response = $this->withHeader('Authorization', 'Bearer tokeninvalido')
                         ->getJson('/api/products');

        $response->assertStatus(401);
    }
}