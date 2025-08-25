<?php

namespace Tests\Unit\Services;

use App\Http\Services\ProductService;
use App\Repositories\ProductRepositoryInterface;
use App\Models\Product;
use Mockery;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    private $repositoryMock;
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repositoryMock = Mockery::mock(ProductRepositoryInterface::class);
        $this->service = new ProductService($this->repositoryMock);
    }

    public function test_get_all_returns_products()
    {
        $products = collect([
            new Product(['id'=>1,'name'=>'P1','price'=>10,'stock'=>5]),
            new Product(['id'=>2,'name'=>'P2','price'=>20,'stock'=>3]),
        ]);

        $this->repositoryMock->shouldReceive('getAll')->once()->andReturn($products);

        $result = $this->service->getAll();
        $this->assertCount(2, $result);
    }

    public function test_create_product()
    {
        $data = ['name'=>'Teste','description'=>'D','price'=>30,'stock'=>10];
        $product = new Product($data);

        $this->repositoryMock->shouldReceive('create')
            ->with($data)
            ->once()
            ->andReturn($product);

        $result = $this->service->create($data);
        $this->assertEquals('Teste', $result->name);
    }

    public function test_update_product()
    {
        $product = new Product(['id'=>1,'name'=>'Old','price'=>10,'stock'=>5]);
        $data = ['name'=>'New','price'=>50,'stock'=>20];

        $updated = new Product(array_merge($product->toArray(), $data));

        $this->repositoryMock->shouldReceive('update')
            ->with($product, $data)
            ->once()
            ->andReturn($updated);

        $result = $this->service->update($product, $data);
        $this->assertEquals('New', $result->name);
    }

    public function test_delete_product()
    {
        $product = new Product(['id'=>1,'name'=>'Delete','price'=>10,'stock'=>1]);

        $this->repositoryMock->shouldReceive('delete')
            ->with($product)
            ->once();

        $this->service->delete($product);
        $this->assertTrue(true); // se não deu erro, passou
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}