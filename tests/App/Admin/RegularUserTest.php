<?php

declare(strict_types=1);

namespace App\Tests\App\Admin;

use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class RegularUserTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function test_regular_user_cant_access_dashboard(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);

        // Act
        $this->client->request('GET', '/admin');

        // Assert
        $this->assertTrue($this->client->getResponse()->isForbidden());
    }

    public function test_regular_user_cant_access_users_list(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);

        // Act
        $this->client->request('GET', '/admin/users');

        // Assert
        $this->assertTrue($this->client->getResponse()->isForbidden());
    }

    public function test_regular_user_cant_access_add_user(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);

        // Act
        $this->client->request('GET', '/admin/users/add');

        // Assert
        $this->assertTrue($this->client->getResponse()->isForbidden());
    }

    public function test_regular_user_cant_post_user(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);

        // Act
        $this->client->request('POST', '/admin/users/add');

        // Assert
        $this->assertTrue($this->client->getResponse()->isForbidden());
    }

    public function test_regular_user_cant_access_edit_user(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);

        // Act
        $this->client->request('GET', '/admin/users/'.$user->getId().'/edit');

        // Assert
        $this->assertTrue($this->client->getResponse()->isForbidden());
    }

    public function test_regular_user_cant_edit_user(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);

        // Act
        $this->client->request('POST', '/admin/users/'.$user->getId().'/edit');

        // Assert
        $this->assertTrue($this->client->getResponse()->isForbidden());
    }

    public function test_regular_user_cant_delete_user(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);

        // Act
        $this->client->request('POST', '/admin/users/'.$user->getId().'/delete');

        // Assert
        $this->assertTrue($this->client->getResponse()->isForbidden());
    }

    public function test_regular_user_cant_access_config(): void
    {
        // Arrange
        $user = UserFactory::createOne()->object();
        $this->client->loginUser($user);

        // Act
        $this->client->request('POST', '/admin/configuration');

        // Assert
        $this->assertTrue($this->client->getResponse()->isForbidden());
    }
}
