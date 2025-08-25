<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_fillable_attributes()
    {
        $user = new User();
        $this->assertEquals(
            ['name', 'email', 'password'],
            $user->getFillable()
        );
    }

    public function test_user_password_is_hashed()
    {
        $user = User::factory()->create([
            'password' => '123456'
        ]);

        $this->assertNotEquals('123456', $user->password);
        $this->assertTrue(password_verify('123456', $user->password));
    }

    public function test_user_can_generate_token()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test_token');

        $this->assertNotNull($token->plainTextToken);
    }
}