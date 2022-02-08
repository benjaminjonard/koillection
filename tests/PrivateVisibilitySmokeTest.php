<?php

declare(strict_types=1);

namespace App\Tests;

use App\Enum\VisibilityEnum;

class PrivateVisibilitySmokeTest extends LoggedWebTestCase
{
    /**
     * @dataProvider anonymousCantSeePrivateItemsProvider
     */
    public function testAnonymousCantSeePrivateItems(string $url)
    {
        $this->setUser('user@koillection.com');
        $this->setVisibilities([VisibilityEnum::VISIBILITY_PRIVATE]);

        $url = $this->replaceUrlParameters($url);
        $this->client->request('GET', $url);
        $this->assertTrue($this->client->getResponse()->isNotFound());
    }

    public function anonymousCantSeePrivateItemsProvider(): \Generator
    {
        yield ["/user/User/{{collection}}"];
        yield ["/user/User/{{collection}}/items"];
        yield ["/user/User/items/{{item}}"];
        yield ["/user/User/albums/{{album}}"];
        yield ["/user/User/wishlists/{{wishlist}}"];
        yield ["/user/User/tags/{{tag}}"];
    }
}
