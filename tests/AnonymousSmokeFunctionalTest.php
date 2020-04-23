<?php

declare(strict_types=1);

namespace App\Tests;

/**
 * Class AnonymousSmokeFunctionalTest
 *
 * @package App\Tests
 */
class AnonymousSmokeFunctionalTest extends LoggedWebTestCase
{
    /**
     * Check an anonymous user has access to this pages
     *
     * @dataProvider isSuccessfulUrlProvider
     */
    public function testPageIsSuccessful(string $url)
    {
        $this->client->request('GET', $this->replaceUrlParameters($url));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function isSuccessfulUrlProvider()
    {
        //Main
        yield ["/"];
    }
}
