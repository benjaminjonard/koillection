<?php

declare(strict_types=1);

namespace App\Tests;

use App\Enum\VisibilityEnum;

class PublicVisibilitySmokeTest extends LoggedWebTestCase
{
    /**
     * @dataProvider anonymousCanSeeProvider
     */
    public function testAnonymousCanSee(string $url)
    {
        $this->setUser('user@koillection.com');
        $this->setVisibilities([VisibilityEnum::VISIBILITY_PUBLIC]);

        $this->client->request('GET', $this->replaceUrlParameters($url));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function anonymousCanSeeProvider(): \Generator
    {
        yield ["/user/User"];
        yield ["/user/User/{{collection}}"];
        yield ["/user/User/{{collection}}/items"];
        yield ["/user/User/items/{{item}}"];
        yield ["/user/User/albums"];
        yield ["/user/User/albums/{{album}}"];
        yield ["/user/User/wishlists"];
        yield ["/user/User/wishlists/{{wishlist}}"];
        yield ["/user/User/tags"];
        yield ["/user/User/tags/{{tag}}"];
        yield ["/user/User/statistics"];
    }
}
