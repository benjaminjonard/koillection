<?php

namespace App\Tests;

use App\Entity\User;
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
                    $url = str_replace($match[0], $this->user->getCollections()->first()->getId(), $url);
                    break;
                case '{{item}}':
                    $url = str_replace($match[0], $this->user->getItems()->first()->getId(), $url);
                    break;
                case '{{album}}':
                    $url = str_replace($match[0], $this->user->getAlbums()->first()->getId(), $url);
                    break;
                case '{{photo}}':
                    $url = str_replace($match[0], $this->user->getPhotos()->first()->getId(), $url);
                    break;
                case '{{wishlist}}':
                    $url = str_replace($match[0], $this->user->getWishlists()->first()->getId(), $url);
                    break;
                case '{{wish}}':
                    $url = str_replace($match[0], $this->user->getWishes()->first()->getId(), $url);
                    break;
                case '{{template}}':
                    $url = str_replace($match[0], $this->user->getTemplates()->first()->getId(), $url);
                    break;
                case '{{tag}}':
                    $url = str_replace($match[0], $this->user->getTags()->first()->getId(), $url);
                    break;
                default:
                    break;
            }
        }

        return $url;
    }
}
