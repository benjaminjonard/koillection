<?php

declare(strict_types=1);

namespace App\Tests\App;

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

        $this->admin = UserFactory::createOne(['username' => 'admin', 'email' => 'admin@test.com', 'roles' => [RoleEnum::ROLE_ADMIN]])->object();
        $this->user = UserFactory::createOne(['username' => 'user', 'email' => 'user@test.com','roles' => [RoleEnum::ROLE_USER]])->object();
    }

    public function test_can_access_dashboard(): void
    {
        $this->client->loginUser($this->admin);

        $this->client->request('GET', '/admin');
        $this->assertResponseIsSuccessful();
    }

    public function test_can_access_users_list(): void
    {
        $this->client->loginUser($this->admin);

        $this->client->request('GET', '/admin/users');
        $this->assertResponseIsSuccessful();
    }

    public function test_can_post_a_user(): void
    {
        $this->client->loginUser($this->admin);

        $this->client->request('GET', '/admin/users/add');
        $this->assertResponseIsSuccessful();

        $this->client->submitForm('submit', [
            'user[username]' => 'test',
            'user[email]' => 'test@test.com',
            'user[plainPassword][first]' => 'password1234',
            'user[plainPassword][second]' => 'password1234',
            'user[diskSpaceAllowed]' => 536870912,
            'user[timezone]' => 'Europe/Paris'
        ]);

        $this->assertResponseRedirects('/admin/users');
        UserFactory::assert()->exists([
            'username' => 'test',
            'email' => 'test@test.com',
        ]);
    }

    public function test_can_edit_a_user(): void
    {
        $this->client->loginUser($this->admin);

        $this->client->request('GET', '/admin/users/' . $this->user->getId() . '/edit');
        $this->assertResponseIsSuccessful();
    }

    public function test_regular_user_cant_access_admin_feature(): void
    {
        $this->client->loginUser($this->user);

        $this->client->request('GET', '/admin');
        $this->assertTrue($this->client->getResponse()->isNotFound());

        $this->client->request('GET', '/admin/users');
        $this->assertTrue($this->client->getResponse()->isNotFound());

        $this->client->request('GET', '/admin/add');
        $this->assertTrue($this->client->getResponse()->isNotFound());

        $this->client->request('POST', '/admin/add');
        $this->assertTrue($this->client->getResponse()->isNotFound());

        $this->client->request('GET', '/admin/users/' . $this->user->getId() . '/edit');
        $this->assertTrue($this->client->getResponse()->isNotFound());

        $this->client->request('POST', '/admin/users/' . $this->user->getId() . '/edit');
        $this->assertTrue($this->client->getResponse()->isNotFound());
    }
}
