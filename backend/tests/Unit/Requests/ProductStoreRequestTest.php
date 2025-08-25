<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\ProductStoreRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ProductStoreRequestTest extends TestCase
{
    /**
     * Remove a regra unique de forma segura
     */
    private function sanitizeRules(array $rules): array
    {
        foreach ($rules as $field => &$rule) {
            // transforma em array sempre
            $ruleArray = is_string($rule) ? explode('|', $rule) : (array) $rule;

            // remove qualquer item que comece com "unique:"
            $ruleArray = array_filter($ruleArray, fn($r) => !str_starts_with($r, 'unique:'));

            // volta para string
            $rule = implode('|', $ruleArray);
        }

        return $rules;
    }

    private function validate(array $data)
    {
        $request = new ProductStoreRequest();

        // pega regras e remove unique
        $rules = $this->sanitizeRules($request->rules());

        return Validator::make($data, $rules, $request->messages());
    }

    public function test_product_store_requires_fields()
    {
        $validator = $this->validate([]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
        $this->assertArrayHasKey('price', $validator->errors()->toArray());
        $this->assertArrayHasKey('stock', $validator->errors()->toArray());
    }

    public function test_product_store_valid_data_passes()
    {
        $validator = $this->validate([
            'name' => 'Produto Teste',
            'description' => 'Descrição',
            'price' => 100,
            'stock' => 10,
        ]);

        $this->assertFalse($validator->fails());
    }

    public function test_product_store_negative_price_fails()
    {
        $validator = $this->validate([
            'name' => 'Produto Teste',
            'price' => -10,
            'stock' => 5,
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('price', $validator->errors()->toArray());
    }
}