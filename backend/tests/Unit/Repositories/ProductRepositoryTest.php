<?php

namespace Tests\Unit\Repositories;

use App\Repositories\ProductRepository;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ProductRepository();
    }

    public function test_create_and_find_product()
    {
        $product = $this->repository->create([
            'name' => 'Produto DB',
            'description' => 'Teste',
            'price' => 100,
            'stock' => 10,
        ]);

        $this->assertDatabaseHas('products', ['name'=>'Produto DB']);
        $this->assertEquals('Produto DB', $product->name);
    }

    public function test_get_all_products()
    {
        Product::factory()->count(3)->create();

        $result = $this->repository->getAll();
        $this->assertCount(3, $result);
    }

    public function test_update_product()
    {
        $product = Product::factory()->create(['name'=>'Old']);

        $updated = $this->repository->update($product, ['name'=>'New']);
        $this->assertEquals('New', $updated->name);
    }

    public function test_delete_product()
    {
        $product = Product::factory()->create();
        $this->repository->delete($product);

        $this->assertDatabaseMissing('products', ['id'=>$product->id]);
    }
}