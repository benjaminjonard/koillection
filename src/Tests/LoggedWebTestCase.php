<?php

namespace App\Tests;

use App\Entity\Album;
use App\Entity\Collection;
use App\Entity\Item;
use App\Entity\Photo;
use App\Entity\Tag;
use App\Entity\Template;
use App\Entity\User;
use App\Entity\Wish;
use App\Entity\Wishlist;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class SmokeFunctionalTest
 *
 * @package App\Tests
 */
class LoggedWebTestCase extends WebTestCase
{
    protected $client;

    protected $user;

    public function setUp()
    {
        $this->client = self::createClient();
    }

    /**
     * @param User $user
     */
    public function login($email)
    {
        $user = $this->client->getContainer()->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['email' => $email]);
        $this->user = $user;

        $session = $this->client->getContainer()->get('session');
        $firewallContext = 'main';
        $token = new UsernamePasswordToken($user, null, $firewallContext, $user->getRoles());

        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    public function replaceUrlParameters($url)
    {
        preg_match_all('/{{[a-zA-Z]+}}/', $url, $matches, PREG_SET_ORDER, 0);

        foreach ($matches as $match) {
            switch ($match[0]) {
                case '{{user}}':
                    $url = str_replace($match[0], $this->user->getId(), $url);
                    break;
                case '{{username}}':
                    $url = str_replace($match[0], $this->user->getUsername(), $url);
                    break;
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
                default:
                    break;
            }
        }

        return $url;
    }

    private function getRelationFirstElement($class)
    {
        return $this->client->getContainer()->get('doctrine')->getManager()->getRepository($class)
            ->findBy(['owner' => $this->user->getId()], null, 1)[0]->getId();
    }
}
