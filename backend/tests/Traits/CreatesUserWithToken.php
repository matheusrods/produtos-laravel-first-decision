<?php

namespace Tests\Traits;

use App\Models\User;

trait CreatesUserWithToken
{
    protected function createUserWithToken(): array
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        return ['Authorization' => 'Bearer ' . $token];
    }
}
