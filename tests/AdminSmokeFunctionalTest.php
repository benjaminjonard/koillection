<?php

declare(strict_types=1);

namespace App\Tests;

class AdminSmokeFunctionalTest extends LoggedWebTestCase
{
    /**
     * @dataProvider isSuccessfulUrlProvider
     */
    public function testPageIsSuccessful(string $url)
    {
        $this->login('admin@koillection.com');

        $this->client->request('GET', $this->replaceUrlParameters($url));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function isSuccessfulUrlProvider(): \Generator
    {
        yield ["/admin"];
        yield ["/admin/users"];
    }
}
