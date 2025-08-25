<?php

namespace Tests\Unit\Models;

use App\Models\Product;
use Tests\TestCase;

class ProductTest extends TestCase
{
    public function test_fillable_attributes()
    {
        $product = new Product([
            'name'=>'Teste',
            'description'=>'D',
            'price'=>100,
            'stock'=>5
        ]);

        $this->assertEquals('Teste', $product->name);
        $this->assertEquals(100, $product->price);
    }

    public function test_price_must_be_numeric()
    {
        $product = new Product(['price' => 10.5]);
        $this->assertIsFloat($product->price);
    }
}