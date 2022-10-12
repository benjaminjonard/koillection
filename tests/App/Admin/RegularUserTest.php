<?php

declare(strict_types=1);

namespace App\Tests\App\Admin;

use App\Enum\RoleEnum;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class RegularUserTest extends WebTestCase
{
    use Factories;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->user = UserFactory::createOne(['username' => 'user', 'email' => 'user@test.com','roles' => [RoleEnum::ROLE_USER]])->object();
    }

    public function test_regular_user_cant_access_dashboard(): void
    {
        // Arrange
        $this->client->loginUser($this->user);

        // Act
        $this->client->request('GET', '/admin');

        // Assert
        $this->assertTrue($this->client->getResponse()->isNotFound());
    }

    public function test_regular_user_cant_access_users_list(): void
    {
        // Arrange
        $this->client->loginUser($this->user);

        // Act
        $this->client->request('GET', '/admin/users');

        // Assert
        $this->assertTrue($this->client->getResponse()->isNotFound());
    }

    public function test_regular_user_cant_access_add_user(): void
    {
        // Arrange
        $this->client->loginUser($this->user);

        // Act
        $this->client->request('GET', '/admin/add');

        // Assert
        $this->assertTrue($this->client->getResponse()->isNotFound());
    }

    public function test_regular_user_cant_post_user(): void
    {
        // Arrange
        $this->client->loginUser($this->user);

        // Act
        $this->client->request('POST', '/admin/add');

        // Assert
        $this->assertTrue($this->client->getResponse()->isNotFound());
    }

    public function test_regular_user_cant_access_edit_user(): void
    {
        // Arrange
        $this->client->loginUser($this->user);

        // Act
        $this->client->request('GET', '/admin/users/' . $this->user->getId() . '/edit');

        // Assert
        $this->assertTrue($this->client->getResponse()->isNotFound());
    }

    public function test_regular_user_cant_edit_user(): void
    {
        // Arrange
        $this->client->loginUser($this->user);

        // Act
        $this->client->request('POST', '/admin/users/' . $this->user->getId() . '/edit');

        // Assert
        $this->assertTrue($this->client->getResponse()->isNotFound());
    }
}
