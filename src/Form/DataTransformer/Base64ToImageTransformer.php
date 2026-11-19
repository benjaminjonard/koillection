<?php

declare(strict_types=1);

namespace App\Form\DataTransformer;

use App\Service\TemporaryFileManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\Service\ResetInterface;

class Base64ToImageTransformer implements DataTransformerInterface, ResetInterface
{
    private ?string $originalBase64 = null;

    public function __construct(
        private readonly TemporaryFileManager $temporaryFileManager,
    ) {
    }

    #[\Override]
    public function transform($file): ?string
    {
        if ($file instanceof File && $file->getRealPath()) {
            $type = pathinfo($file->getRealPath(), PATHINFO_EXTENSION);
            $data = file_get_contents($file->getRealPath());
            $this->originalBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

            return $this->originalBase64;
        }

        return null;
    }

    #[\Override]
    public function reverseTransform($base64): ?UploadedFile
    {
        if (null === $base64 || $base64 === $this->originalBase64) {
            return null;
        }

        preg_match('/data:(image\/([\w]+));base64,(.*)/', $base64, $matches);
        $data = base64_decode($matches[3]);
        $name = uniqid('col_') . '.' . $matches[2];

        $path = $this->temporaryFileManager->createFilePath();
        file_put_contents($path, $data);

        return new UploadedFile($path, $name, $matches[1], null, true);
    }

    public function reset(): void
    {
        $this->originalBase64 = null;
    }
}
