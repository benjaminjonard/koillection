<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class AppTestCase  extends WebTestCase
{
    protected function createFile(string $type): UploadedFile
    {
        $filesystem = new Filesystem();
        $uniqId = uniqid();
        $filesystem->copy(__DIR__."/../assets/fixtures/nyancat.{$type}", "/tmp/{$uniqId}.{$type}");

        return new UploadedFile("/tmp/{$uniqId}.{$type}", "{$uniqId}.{$type}", test: true);
    }
}
