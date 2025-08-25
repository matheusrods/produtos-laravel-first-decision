<?php

namespace Tests\Unit\Services;

use App\Http\Services\AuthService;
use App\Repositories\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    private $userRepositoryMock;
    private $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepositoryMock = Mockery::mock(UserRepositoryInterface::class);
        $this->authService = new AuthService($this->userRepositoryMock);
    }

    public function test_register_creates_user_with_hashed_password()
    {
        $data = [
            'name' => 'User Test',
            'email' => 'test@example.com',
            'password' => '123456',
        ];

        $user = new User([
            'id' => 1,
            'name' => 'User Test',
            'email' => 'test@example.com',
            'password' => Hash::make('123456'),
        ]);

        $this->userRepositoryMock
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(fn($arg) => Hash::check('123456', $arg['password'])))
            ->andReturn($user);

        $result = $this->authService->register($data);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('User Test', $result->name);
    }

    public function test_login_success_returns_token_and_user()
    {
        $user = \Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->email = 'test@example.com';
        $user->password = Hash::make('123456');

        $user->shouldReceive('createToken')
            ->once()
            ->with('auth_token')
            ->andReturn((object)['plainTextToken' => 'fake-token']);

        $this->userRepositoryMock
            ->shouldReceive('findByEmail')
            ->once()
            ->with('test@example.com')
            ->andReturn($user);

        $result = $this->authService->login('test@example.com', '123456');

        $this->assertEquals('fake-token', $result['token']);
        $this->assertEquals($user->email, $result['user']->email);
    }


    public function test_login_fails_with_invalid_password()
    {
        $user = User::factory()->make([
            'email' => 'test@example.com',
            'password' => Hash::make('correctpass')
        ]);

        $this->userRepositoryMock
            ->shouldReceive('findByEmail')
            ->with('test@example.com')
            ->andReturn($user);

        $result = $this->authService->login('test@example.com', 'wrongpass');

        $this->assertNull($result);
    }

    public function test_logout_deletes_tokens()
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('tokens->delete')->once();

        $this->authService->logout($user);
        $this->assertTrue(true); // só para validar execução
    }
}