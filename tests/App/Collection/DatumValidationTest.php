<?php

declare(strict_types=1);

namespace App\Tests\App\Collection;

use App\Enum\DatumTypeEnum;
use App\Enum\VisibilityEnum;
use App\Tests\AppTestCase;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\DatumFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class DatumValidationTest extends AppTestCase
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

    public function test_invalid_datum_type_is_rejected_on_collection_edit(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $datum = DatumFactory::createOne([
            'owner' => $user,
            'collection' => $collection,
            'type' => DatumTypeEnum::TYPE_TEXT,
            'label' => 'Field',
            'position' => 1,
            'value' => 'v',
        ]);

        // Act: submit the edit form forcing an out-of-enum datum type
        $this->client->request(Request::METHOD_GET, '/collections/' . $collection->getId() . '/edit');
        $this->client->submitForm('Submit', [
            'collection[data][0][type]' => 'not-a-real-type',
        ]);

        // Assert: the form is rejected (Assert\Valid -> Assert\Choice), the stored type is untouched
        $connection = self::getContainer()->get('doctrine')->getConnection();
        $storedType = $connection->fetchOne('SELECT type FROM koi_datum WHERE id = :id', ['id' => $datum->getId()]);
        $this->assertSame(DatumTypeEnum::TYPE_TEXT, $storedType);
    }
}
