<?php

declare(strict_types=1);

namespace App\Tests;

class UserSmokeFunctionnalTest extends LoggedWebTestCase
{
    public function testPageIsSuccessful(string $url)
    {
        $this->login('cthulhu@koillection.com');
        $this->client->request('GET', $this->replaceUrlParameters($url));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testPageIsNotFound(string $url)
    {
        $this->login('cthulhu@koillection.com');
        $this->client->request('GET', $this->replaceUrlParameters($url));
        $this->assertTrue($this->client->getResponse()->isNotFound());
    }

    public function isSuccessfulUrlProvider(): \Generator
    {
        yield ["/collections"];
        yield ["/collections/add"];
        yield ["/tags"];
        yield ["/tag-categories"];
        yield ["/wishlists"];
        yield ["/albums"];
        yield ["/albums/add"];
        yield ["/statistics"];
        yield ["/loans"];
        yield ["/templates"];
        yield ["/templates/add"];
        yield ["/profile"];
        yield ["/history"];
        yield ["/preview"];
        yield ["/user/{{username}}"];
        yield ["/tools"];
        yield ["/inventories/add"];
        yield ["/inventories/{{inventory}}"];
    }

    public function isNotFoundUrlProvider(): \Generator
    {
        yield ["/admin"];
        yield ["/admin/users"];
        yield ["/admin/_trans"];
    }
}
