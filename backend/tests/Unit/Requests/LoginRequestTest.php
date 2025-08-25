<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class LoginRequestTest extends TestCase
{
    private function validate(array $data)
    {
        $request = new LoginRequest();
        return Validator::make($data, $request->rules(), $request->messages());
    }

    public function test_login_request_requires_fields()
    {
        $validator = $this->validate([]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    public function test_login_request_valid_data_passes()
    {
        $validator = $this->validate([
            'email' => 'user@test.com',
            'password' => '123456',
        ]);

        $this->assertFalse($validator->fails());
    }
}