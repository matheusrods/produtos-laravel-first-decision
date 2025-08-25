<?php

namespace Tests\Unit\Repositories;

use App\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new UserRepository();
    }

    public function test_create_user()
    {
        $data = [
            'name' => 'User Repo',
            'email' => 'repo@example.com',
            'password' => bcrypt('123456'),
        ];

        $user = $this->repository->create($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', ['email' => 'repo@example.com']);
    }

    public function test_find_by_email_returns_user()
    {
        $user = User::factory()->create(['email' => 'findme@example.com']);

        $found = $this->repository->findByEmail('findme@example.com');

        $this->assertEquals($user->id, $found->id);
    }

    public function test_find_by_email_returns_null_when_not_found()
    {
        $result = $this->repository->findByEmail('notfound@example.com');
        $this->assertNull($result);
    }
}