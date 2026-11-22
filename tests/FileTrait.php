<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Component\HttpFoundation\File\UploadedFile;

trait FileTrait
{
    protected function createFile(string $type, int $width = 1200, int $height = 800): UploadedFile
    {
        $path = '/tmp/' . uniqid() . '.' . $type;
        file_put_contents($path, $this->createFileContent($type, $width, $height));

        return new UploadedFile($path, basename($path), test: true);
    }

    protected function createFileContent(string $type, int $width = 1200, int $height = 800): string
    {
        return match ($type) {
            'png', 'jpeg', 'webp', 'avif' => $this->createImageContent($type, $width, $height),
            'mp4' => pack('N', 24) . 'ftypisom' . pack('N', 512) . 'isomiso2',
            'json' => json_encode([
                'name' => 'Nyancat',
                'namePath' => '#//h1/text()]#',
                'imagePath' => '#//h1/img/@src]#',
                'dataPaths' => [['name' => 'Length', 'path' => '#//span/text()]#', 'type' => 'text']],
            ]),
            'txt' => 'Frieren',
        };
    }

    private function createImageContent(string $type, int $width, int $height): string
    {
        $image = imagecreatetruecolor($width, $height);
        ob_start();
        match ($type) {
            'png' => imagepng($image),
            'jpeg' => imagejpeg($image),
            'webp' => imagewebp($image),
            'avif' => imageavif($image),
        };

        return ob_get_clean();
    }
}
