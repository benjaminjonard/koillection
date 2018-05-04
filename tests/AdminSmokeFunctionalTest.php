<?php

namespace App\Tests;

/**
 * Class AdminSmokeFunctionalTest
 *
 * @package App\Tests
 */
class AdminSmokeFunctionalTest extends LoggedWebTestCase
{
    /**
     * Check if the admin has access to all the pages
     *
     * @dataProvider isSuccessfulUrlProvider
     */
    public function testPageIsSuccessful($url)
    {
        $this->login('khorne@koillection.com');

        $this->client->request('GET', $this->replaceUrlParameters($url));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function isSuccessfulUrlProvider()
    {
        //Main
        yield ["/collections"];
        yield ["/collections/add"];
        yield ["/collections/{{collection}}"];
        yield ["/collections/{{collection}}/edit"];
        yield ["/collections/{{collection}}/batch-tagging"];
        yield ["/collections/{{collection}}/history"];
        yield ["/collections/{{collection}}/items"];

        yield ["/items/{{item}}"];
        yield ["/items/add?collection={{collection}}"];
        yield ["/items/{{item}}/edit"];
        yield ["/items/{{item}}/loan"];
        yield ["/items/{{item}}/history"];

        yield ["/tags"];
        yield ["/tags/{{tag}}"];
        yield ["/tags/{{tag}}/edit"];
        yield ["/tags/{{tag}}/history"];

        yield ["/wishlists"];
        yield ["/wishlists/add"];
        yield ["/wishlists/{{wishlist}}"];
        yield ["/wishlists/{{wishlist}}/edit"];

        yield ["/wishes/add?wishlist={{wishlist}}"];
        yield ["/wishes/{{wish}}/edit"];
        yield ["/wishes/{{wish}}/transfer-to-collection"];

        yield ["/albums"];
        yield ["/albums/add"];
        yield ["/albums/{{album}}"];
        yield ["/albums/{{album}}/edit"];

        yield ["/photos/add?album={{album}}"];
        yield ["/photos/{{photo}}/edit"];

        yield ["/statistics"];
        yield ["/loans"];

        yield ["/templates"];
        yield ["/templates/add"];
        yield ["/templates/{{template}}"];
        yield ["/templates/{{template}}/edit"];

        yield ["/settings"];
        yield ["/profile"];
        yield ["/history"];

        yield ["/admin"];
        yield ["/admin/users"];
        yield ["/admin/users/{{user}}"];
        yield ["/admin/analytics"];
        yield ["/admin/_trans"];

        yield ["/about"];

        //Preview
        yield ["/preview"];
        yield ["/preview/{{collection}}"];
        yield ["/preview/{{collection}}/items"];
        yield ["/preview/item/{{item}}"];
        yield ["/preview/albums"];
        yield ["/preview/albums/{{album}}"];
        yield ["/preview/wishlists"];
        yield ["/preview/wishlists/{{wishlist}}"];
        yield ["/preview/tags"];
        yield ["/preview/tags/{{tag}}"];
        yield ["/preview/statistics"];

        //User
        yield ["/user/{{username}}"];
        yield ["/user/{{username}}/{{collection}}"];
        yield ["/user/{{username}}/{{collection}}/items"];
        yield ["/user/{{username}}/item/{{item}}"];
        yield ["/user/{{username}}/albums"];
        yield ["/user/{{username}}/albums/{{album}}"];
        yield ["/user/{{username}}/wishlists"];
        yield ["/user/{{username}}/wishlists/{{wishlist}}"];
        yield ["/user/{{username}}/tags"];
        yield ["/user/{{username}}/tags/{{tag}}"];
        yield ["/user/{{username}}/statistics"];
    }
}
