<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\ProductController;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Services\ProductService;
use App\Models\Product;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Mockery;

class ProductControllerTest extends TestCase
{
    use WithFaker;

    private $serviceMock;
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serviceMock = Mockery::mock(ProductService::class);
        $this->controller = new ProductController($this->serviceMock);
    }

    public function test_index_returns_products()
    {
        $products = collect([
            new Product(['id' => 1, 'name' => 'P1', 'price' => 10, 'stock' => 5]),
            new Product(['id' => 2, 'name' => 'P2', 'price' => 20, 'stock' => 3]),
        ]);

        $this->serviceMock->shouldReceive('getAll')->once()->andReturn($products);

        $response = $this->controller->index();
        $this->assertNotEmpty($response);
    }

    public function test_store_creates_product()
    {
        // Mock do request
        $request = Mockery::mock(ProductStoreRequest::class);
        $request->shouldReceive('validated')->andReturn([
            'name' => 'Novo Produto',
            'description' => 'Desc',
            'price' => 99.90,
            'stock' => 10,
        ]);

        $product = new Product([
            'id' => 1,
            'name' => 'Novo Produto',
            'price' => 99.90,
            'stock' => 10,
        ]);

        $this->serviceMock->shouldReceive('create')
            ->with([
                'name' => 'Novo Produto',
                'description' => 'Desc',
                'price' => 99.90,
                'stock' => 10,
            ])
            ->once()
            ->andReturn($product);

        $response = $this->controller->store($request);
        $data = $response->getData(true);

        $this->assertEquals('Produto criado com sucesso', $data['message']);
        $this->assertEquals('Novo Produto', $data['data']['name']);
    }

    public function test_show_returns_product()
    {
        $product = new Product(['id' => 1, 'name' => 'Teste', 'price' => 50, 'stock' => 5]);

        $response = $this->controller->show($product);
        $data = $response->toArray(request());

        $this->assertEquals('Teste', $data['name']);
    }

    public function test_update_updates_product()
    {
        $product = new Product(['id' => 1, 'name' => 'Old', 'price' => 10, 'stock' => 2]);

        // Mock do request
        $request = Mockery::mock(ProductUpdateRequest::class);
        $request->shouldReceive('validated')->andReturn([
            'name' => 'Updated',
            'price' => 30,
            'stock' => 15,
        ]);

        $updated = new Product(['id' => 1, 'name' => 'Updated', 'price' => 30, 'stock' => 15]);

        $this->serviceMock->shouldReceive('update')
            ->with($product, [
                'name' => 'Updated',
                'price' => 30,
                'stock' => 15,
            ])
            ->once()
            ->andReturn($updated);

        $response = $this->controller->update($request, $product);
        $data = $response->toArray(request());

        $this->assertEquals('Updated', $data['name']);
    }

    public function test_destroy_deletes_product()
    {
        $product = new Product(['id' => 1, 'name' => 'DeleteMe', 'price' => 20, 'stock' => 1]);

        $this->serviceMock->shouldReceive('delete')
            ->with($product)
            ->once();

        $response = $this->controller->destroy($product);
        $data = $response->getData(true);

        $this->assertEquals('Produto removido com sucesso', $data['message']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}