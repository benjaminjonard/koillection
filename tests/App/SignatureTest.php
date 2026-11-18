<?php

declare(strict_types=1);

namespace App\Tests\App;

use App\Enum\DatumTypeEnum;
use App\Tests\AppTestCase;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\DatumFactory;
use App\Tests\Factory\ItemFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class SignatureTest extends AppTestCase
{
    use Factories;
    use ResetDatabase;

    private KernelBrowser $client;

    #[\Override]
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    public function test_can_see_signature_list(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $items = ItemFactory::createMany(3, [
            'owner' => $user,
            'collection' => CollectionFactory::createOne(['owner' => $user]),
        ]);
        foreach ($items as $item) {
            $item->addData(DatumFactory::createOne(['owner' => $user, 'type' => DatumTypeEnum::TYPE_SIGN]));
        }

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/signatures');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Signatures', $crawler->filter('h1')->text());
        $this->assertCount(3, $crawler->filter('.collection-item'));
    }

    public function test_signature_lightbox_caption_is_not_interpreted_as_html(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $item = ItemFactory::createOne([
            'name' => '<img src=x onerror=alert(1)>',
            'owner' => $user,
            'collection' => CollectionFactory::createOne(['owner' => $user]),
        ]);
        $item->addData(DatumFactory::createOne(['owner' => $user, 'type' => DatumTypeEnum::TYPE_SIGN, 'label' => '<b>Frieren</b> & co']));

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/signatures');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('&lt;img src=x onerror=alert(1)&gt; - &lt;b&gt;Frieren&lt;/b&gt; &amp; co', $crawler->filter('.collection-item')->attr('data-sub-html'));
    }
}
