<?php

declare(strict_types=1);

namespace App\Service;

class ThumbnailGenerator
{
    public function generate(string $path, string $thumbnailPath, int $thumbnailWidth, string $thumbnailFormat): bool
    {
        if (!is_file($path)) {
            return false;
        }

        [$width, $height] = getimagesize($path);
        $mime = mime_content_type($path);

        $originalSize = filesize($path);
        $thumbnailHeight = (int) floor($height * ($thumbnailWidth / $width));

        if ($width <= $thumbnailWidth) {
            return false;
        }

        // Create user directory in uploads
        $dir = explode('/', $thumbnailPath);
        array_pop($dir);
        $dir = implode('/', $dir);

        if (!is_dir($dir) && !mkdir($dir) && !is_dir($dir)) {
            throw new \Exception('There was a problem while uploading the image. Please try again!');
        }

        $image = match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($path),
            'image/png' => imagecreatefrompng($path),
            'image/webp' => imagecreatefromwebp($path),
            'image/avif' => imagecreatefromavif($path),
            default => throw new \Exception("Mime type $mime isn't supported"),
        };

        $thumbnail = imagecreatetruecolor($thumbnailWidth, $thumbnailHeight);

        // Transparency
        if (in_array($mime, ['image/png', 'image/webp', 'image/avif'])) {
            imagecolortransparent($thumbnail, imagecolorallocate($thumbnail, 0, 0, 0));
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
        }

        imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $thumbnailWidth, $thumbnailHeight, $width, $height);
        $deg = $this->guessRotation($path);
        $thumbnail = imagerotate($thumbnail, $deg, 0);

        match ($thumbnailFormat) {
            'jpeg' => imagejpeg($thumbnail, $thumbnailPath),
            'png' => imagepng($thumbnail, $thumbnailPath),
            'webp' => imagewebp($thumbnail, $thumbnailPath),
            'avif' => imageavif($thumbnail, $thumbnailPath)
        };

        $thumbnailSize = filesize($thumbnailPath);
        if ($thumbnailSize >= $originalSize) {
            unlink($thumbnailPath);

            return false;
        }

        return true;
    }

    public function crop(string $path, int $maxWidth, int $maxHeight): void
    {
        [$width, $height] = getimagesize($path);
        $mime = mime_content_type($path);
        $ratio = $width / $height;

        if ($width > $height) {
            $width = (int) ceil($width - ($width * abs($ratio - $maxWidth / $maxHeight)));
        } else {
            $height = (int) ceil($height - ($height * abs($ratio - $maxWidth / $maxHeight)));
        }

        $newWidth = $maxWidth;
        $newHeight = $maxHeight;

        $image = match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($path),
            'image/png' => imagecreatefrompng($path),
            'image/webp' => imagecreatefromwebp($path),
            'image/avif' => imagecreatefromavif($path),
            default => throw new \Exception('Your image cannot be processed, please use another one.'),
        };

        $resized = imagecreatetruecolor($newWidth, $newHeight);

        // Transparency
        if (in_array($mime, ['image/png', 'image/webp', 'image/avif'])) {
            imagecolortransparent($resized, imagecolorallocate($resized, 0, 0, 0));
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
        }

        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $deg = $this->guessRotation($path);
        imagerotate($resized, $deg, 0);

        match ($mime) {
            'image/jpeg' => imagejpeg($resized, $path),
            'image/png' => imagepng($resized, $path),
            'image/webp' => imagewebp($resized, $path),
            'image/avif' => imageavif($resized, $path)
        };
    }

    public function guessRotation(string $path): int
    {
        $deg = 0;

        if (\function_exists('exif_read_data')) {
            $exif = @exif_read_data($path);
            if ($exif && isset($exif['Orientation'])) {
                $orientation = $exif['Orientation'];
                if (1 != $orientation) {
                    switch ($orientation) {
                        case 3:
                            $deg = 180;
                            break;
                        case 6:
                            $deg = 270;
                            break;
                        case 8:
                            $deg = 90;
                            break;
                    }
                }
            }
        }

        return $deg;
    }
}
