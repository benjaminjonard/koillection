<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\Album;
use App\Entity\Collection;
use App\Entity\Inventory;
use App\Entity\Item;
use App\Entity\Photo;
use App\Entity\Tag;
use App\Entity\TagCategory;
use App\Entity\Template;
use App\Entity\User;
use App\Entity\Wish;
use App\Entity\Wishlist;
use App\Enum\VisibilityEnum;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AppTestCase extends WebTestCase
{
    use RefreshDatabaseTrait;

    protected KernelBrowser $client;

    private ?User $user = null;

    private array $visibilities = VisibilityEnum::VISIBILITIES;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    public function login(string $email): void
    {
        $user = $this->client->getContainer()->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['email' => $email]);
        $this->user = $user;

        $this->client->loginUser($user);
    }

    public function setUser(string $email): void
    {
        $this->user = $this->client->getContainer()->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['email' => $email]);
    }

    public function setVisibilities(array $visibilities): void
    {
        $this->visibilities = $visibilities;
    }

    public function replaceUrlParameters(string $url): string
    {
        preg_match_all('/{{[a-zA-Z]+}}/', $url, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {
            switch ($match[0]) {
                case '{{collection}}':
                    $url = str_replace($match[0], $this->getRelationFirstElement(Collection::class), $url);
                    break;
                case '{{item}}':
                    $url = str_replace($match[0], $this->getRelationFirstElement(Item::class), $url);
                    break;
                case '{{album}}':
                    $url = str_replace($match[0], $this->getRelationFirstElement(Album::class), $url);
                    break;
                case '{{photo}}':
                    $url = str_replace($match[0], $this->getRelationFirstElement(Photo::class), $url);
                    break;
                case '{{wishlist}}':
                    $url = str_replace($match[0], $this->getRelationFirstElement(Wishlist::class), $url);
                    break;
                case '{{wish}}':
                    $url = str_replace($match[0], $this->getRelationFirstElement(Wish::class), $url);
                    break;
                case '{{template}}':
                    $url = str_replace($match[0], $this->getRelationFirstElement(Template::class), $url);
                    break;
                case '{{tag}}':
                    $url = str_replace($match[0], $this->getRelationFirstElement(Tag::class), $url);
                    break;
                case '{{category}}':
                    $url = str_replace($match[0], $this->getRelationFirstElement(TagCategory::class), $url);
                    break;
                case '{{inventory}}':
                    $url = str_replace($match[0], $this->getRelationFirstElement(Inventory::class), $url);
                    break;
                default:
                    break;
            }
        }

        return $url;
    }

    private function getRelationFirstElement(string $class)
    {
        $params = ['owner' => $this->user->getId()];
        if (property_exists($class, 'finalVisibility')) {
            $params['finalVisibility'] = $this->visibilities;
        } elseif (property_exists($class, 'visibility')) {
            $params['visibility'] = $this->visibilities;
        }

        return $this->client->getContainer()->get('doctrine')->getManager()->getRepository($class)
            ->findBy($params, null, 1)[0]->getId();
    }
}
