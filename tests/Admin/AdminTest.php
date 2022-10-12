<?php

declare(strict_types=1);

namespace App\Tests\Admin;

use App\Enum\RoleEnum;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;

class AdminTest extends WebTestCase
{
    use Factories;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();

        $this->admin = UserFactory::createOne(['username' => 'admin', 'email' => 'admin@test.com', 'roles' => [RoleEnum::ROLE_ADMIN]])->object();
    }

    public function test_admin_can_access_dashboard(): void
    {
        // Arrange
        $this->client->loginUser($this->admin);

        // Act
        $this->client->request('GET', '/admin');

        // Assert
        $this->assertResponseIsSuccessful();
    }

    public function test_admin_can_access_users_list(): void
    {
        // Arrange
        $this->client->loginUser($this->admin);

        // Act
        $this->client->request('GET', '/admin/users');

        // Assert
        $this->assertResponseIsSuccessful();
    }

    public function test_admin_can_post_a_user(): void
    {
        // Arrange
        $this->client->loginUser($this->admin);

        // Act
        $this->client->request('GET', '/admin/users/add');
        $this->client->submitForm('submit', [
            'user[username]' => 'test',
            'user[email]' => 'test@test.com',
            'user[plainPassword][first]' => 'password1234',
            'user[plainPassword][second]' => 'password1234',
            'user[diskSpaceAllowed]' => 536870912,
            'user[timezone]' => 'Europe/Paris'
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        UserFactory::assert()->exists(['username' => 'test', 'email' => 'test@test.com']);
    }

    public function test_admin_can_edit_a_user(): void
    {
        // Arrange
        $this->client->loginUser($this->admin);

        // Act
        $this->client->request('GET', '/admin/users/' . $this->admin->getId() . '/edit');
        $this->client->submitForm('submit', [
            'user[username]' => 'admin',
            'user[email]' => 'admin-new-email@test.com',
            'user[plainPassword][first]' => 'password1234',
            'user[plainPassword][second]' => 'password1234',
            'user[diskSpaceAllowed]' => 536870912,
            'user[timezone]' => 'Europe/Paris'
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        UserFactory::assert()->exists(['username' => 'admin', 'email' => 'admin-new-email@test.com']);
    }
}
