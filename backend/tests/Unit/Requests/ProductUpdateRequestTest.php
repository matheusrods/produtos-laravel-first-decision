<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\ProductUpdateRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ProductUpdateRequestTest extends TestCase
{
    /**
     * Remove a regra unique e injeta um ID fake para não depender de rota/banco.
     */
    private function sanitizeRules(array $rules): array
    {
        foreach ($rules as $field => &$rule) {
            $ruleArray = is_string($rule) ? explode('|', $rule) : (array) $rule;

            // remove qualquer "unique:products,name,<id>"
            $ruleArray = array_filter($ruleArray, fn($r) => !str_starts_with($r, 'unique:'));

            $rule = implode('|', $ruleArray);
        }

        return $rules;
    }

    private function validate(array $data)
    {
        $request = new ProductUpdateRequest();

        // Mock da rota que simula route('product')->id
        $fakeRoute = new class {
            public function parameter($key)
            {
                if ($key === 'product') {
                    return (object)['id' => 1];
                }
                return null;
            }
        };

        $request->setRouteResolver(function () use ($fakeRoute) {
            return $fakeRoute;
        });

        $rules = $this->sanitizeRules($request->rules());

        return Validator::make($data, $rules, $request->messages());
    }

    public function test_product_update_valid_data_passes()
    {
        $validator = $this->validate([
            'name' => 'Produto Atualizado',
            'price' => 200,
            'stock' => 15,
        ]);

        $this->assertFalse($validator->fails());
    }

    public function test_product_update_negative_stock_fails()
    {
        $validator = $this->validate([
            'name' => 'Produto Atualizado',
            'price' => 200,
            'stock' => -1,
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('stock', $validator->errors()->toArray());
    }
}