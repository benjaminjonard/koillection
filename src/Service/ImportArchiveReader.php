<?php

declare(strict_types=1);

namespace App\Service;

class ImportArchiveReader
{
    public const int MAX_DIMENSION = 8000;

    private const array MIME_TYPES = ['image/png', 'image/jpeg', 'image/webp', 'image/avif'];

    private const array EXTENSIONS = ['png', 'jpg', 'jpeg', 'webp', 'avif'];

    private const int MAX_DECOMPRESSION_RATIO = 2;

    private const int CHUNK_SIZE = 8192;

    public function extract(string $archivePath): array
    {
        $zip = new \ZipArchive();
        if ($zip->open($archivePath, \ZipArchive::RDONLY) !== true) {
            return [];
        }

        $maxBytes = filesize($archivePath) * self::MAX_DECOMPRESSION_RATIO;
        $bytesRead = 0;
        $entries = [];

        for ($index = 0; $index < $zip->numFiles; ++$index) {
            $name = $zip->getNameIndex($index);
            if ($name === false || str_ends_with($name, '/') || !$this->isSafeEntry($name)) {
                continue;
            }

            if (!\in_array(strtolower(pathinfo($name, \PATHINFO_EXTENSION)), self::EXTENSIONS, true)) {
                $entries[$name] = $this->createEntry(null, 'error.import.invalid_image_type');
                continue;
            }

            if ($bytesRead > $maxBytes) {
                $entries[$name] = $this->createEntry(null, 'error.import.archive_too_large');
                continue;
            }

            $stream = $zip->getStreamIndex($index);
            if ($stream === false) {
                continue;
            }

            $path = tempnam(sys_get_temp_dir(), ImportStorage::FILE_PREFIX);
            $output = fopen($path, 'w');
            $error = null;

            while (!feof($stream)) {
                $chunk = fread($stream, self::CHUNK_SIZE);
                $bytesRead += \strlen($chunk);

                if ($bytesRead > $maxBytes) {
                    $error = 'error.import.archive_too_large';
                    break;
                }

                fwrite($output, $chunk);
            }

            fclose($output);
            fclose($stream);

            $error ??= $this->getImageError($path);
            if ($error !== null) {
                unlink($path);
                $entries[$name] = $this->createEntry(null, $error);
                continue;
            }

            $entries[$name] = $this->createEntry($path, null);
        }

        $zip->close();

        return $entries;
    }

    private function createEntry(?string $path, ?string $error): array
    {
        return [
            'path' => $path,
            'error' => $error,
        ];
    }

    private function getImageError(string $path): ?string
    {
        if (!\in_array(mime_content_type($path), self::MIME_TYPES, true)) {
            return 'error.import.invalid_image_type';
        }

        $size = getimagesize($path);
        if ($size === false) {
            return 'error.import.invalid_image_type';
        }

        if ($size[0] > self::MAX_DIMENSION || $size[1] > self::MAX_DIMENSION) {
            return 'error.import.image_too_large';
        }

        return null;
    }

    private function isSafeEntry(string $entry): bool
    {
        if ($entry === '' || str_starts_with($entry, '/') || str_contains($entry, '\\') || str_contains($entry, "\0")) {
            return false;
        }

        return !\in_array('..', explode('/', $entry), true);
    }
}
