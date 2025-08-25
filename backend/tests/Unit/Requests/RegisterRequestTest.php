<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class RegisterRequestTest extends TestCase
{
    /**
     * Remove a regra "unique" das validações para não depender de DB.
     */
    private function sanitizeRules(array $rules): array
    {
        foreach ($rules as $field => &$rule) {
            $ruleArray = is_string($rule) ? explode('|', $rule) : (array) $rule;

            // remove qualquer regra "unique:users,email"
            $ruleArray = array_filter($ruleArray, fn($r) => !str_starts_with($r, 'unique:'));

            $rule = implode('|', $ruleArray);
        }

        return $rules;
    }

    private function validate(array $data)
    {
        $request = new RegisterRequest();

        // pega as regras e remove "unique"
        $rules = $this->sanitizeRules($request->rules());

        return Validator::make($data, $rules, $request->messages());
    }

    public function test_register_request_requires_fields()
    {
        $validator = $this->validate([]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    public function test_register_request_valid_data_passes()
    {
        $validator = $this->validate([
            'name' => 'User Test',
            'email' => 'user@test.com',
            'password' => '123456',
        ]);

        $this->assertFalse($validator->fails());
    }

    public function test_register_request_invalid_email_fails()
    {
        $validator = $this->validate([
            'name' => 'User Test',
            'email' => 'invalid-email',
            'password' => '123456',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }
}