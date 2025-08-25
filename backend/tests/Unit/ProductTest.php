<?php

namespace Tests\Unit;

use App\Models\Product;
use Tests\TestCase;

class ProductTest extends TestCase
{
    public function test_it_has_name_price_and_stock_attributes()
    {
        $product = new Product([
            'name' => 'Teclado',
            'price' => 99.90,
            'stock' => 10,
        ]);

        $this->assertEquals('Teclado', $product->name);
        $this->assertEquals(99.90, $product->price);
        $this->assertEquals(10, $product->stock);
    }

    public function test_price_must_be_positive()
    {
        $product = new Product([
            'name' => 'Monitor',
            'price' => -100,
            'stock' => 5,
        ]);

        $this->assertTrue($product->price < 0);
    }
}
