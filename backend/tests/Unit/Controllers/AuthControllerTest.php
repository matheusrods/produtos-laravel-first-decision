<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\AuthController;
use App\Http\Services\AuthService;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;
use App\Models\User;

class AuthControllerTest extends TestCase
{
    use WithFaker;

    private $authServiceMock;
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();

        // Cria mock do AuthService
        $this->authServiceMock = Mockery::mock(AuthService::class);
        $this->controller = new AuthController($this->authServiceMock);
    }

    public function test_register_success()
    {
        $request = Mockery::mock(RegisterRequest::class);
        $request->shouldReceive('validated')->once()->andReturn([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '123456',
        ]);

        $userFake = new User([
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->authServiceMock
            ->shouldReceive('register')
            ->once()
            ->with([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => '123456',
            ])
            ->andReturn($userFake);

        $response = $this->controller->register($request);
        $data = $response->getData(true);

        $this->assertEquals(201, $response->status());
        $this->assertEquals('Usuário registrado com sucesso', $data['message']);
        $this->assertEquals('Test User', $data['user']['name']);
    }

    public function test_login_success()
    {
        $request = new LoginRequest([
            'email' => 'test@example.com',
            'password' => '123456',
        ]);

        $userFake = (object) [
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
        ];

        $this->authServiceMock
            ->shouldReceive('login')
            ->with('test@example.com', '123456')
            ->once()
            ->andReturn([
                'token' => 'fake-token',
                'user' => $userFake
            ]);

        $response = $this->controller->login($request);
        $data = $response->getData(true);

        $this->assertEquals(200, $response->status());
        $this->assertEquals('Login realizado com sucesso', $data['message']);
        $this->assertEquals('fake-token', $data['token']);
    }

    public function test_login_invalid_credentials()
    {
        $request = new LoginRequest([
            'email' => 'wrong@example.com',
            'password' => 'wrongpass',
        ]);

        $this->authServiceMock
            ->shouldReceive('login')
            ->with('wrong@example.com', 'wrongpass')
            ->once()
            ->andReturn(null);

        $response = $this->controller->login($request);
        $data = $response->getData(true);

        $this->assertEquals(401, $response->status());
        $this->assertEquals('Credenciais inválidas', $data['message']);
    }

    public function test_logout_success()
    {
        $user = new User([
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'hashed-pass'
        ]);

        $request = new Request();
        $request->setUserResolver(fn() => $user);

        $this->authServiceMock
            ->shouldReceive('logout')
            ->with($user)
            ->once();

        $response = $this->controller->logout($request);
        $data = $response->getData(true);

        $this->assertEquals(200, $response->status());
        $this->assertEquals('Logout realizado com sucesso', $data['message']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}