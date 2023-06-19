<?php

declare(strict_types=1);

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class ApiTestCase extends \ApiPlatform\Symfony\Bundle\Test\ApiTestCase
{
    protected function createClientWithCredentials(User $user): Client
    {
        $encoder = $this->getContainer()->get(JWTEncoderInterface::class);
        $payload = [
            'username' => $user->getUsername(),
            'roles' => $user->getRoles()
        ];

        return static::createClient([], ['headers' => ['Authorization' => 'Bearer '.$encoder->encode($payload)]]);
    }

    protected function createFile(string $type): UploadedFile
    {
        $filesystem = new Filesystem();
        $uniqId = uniqid();
        $filesystem->copy(__DIR__."/../assets/fixtures/nyancat.{$type}", "/tmp/{$uniqId}.{$type}");

        return new UploadedFile("/tmp/{$uniqId}.{$type}", "{$uniqId}.{$type}");
    }
}
