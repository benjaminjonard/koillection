<?php

declare(strict_types=1);

namespace App\Service;

class ThumbnailGenerator
{
    public function __construct(
        private GifResizer $gifResizer
    )
    {
    }

    public function generate(string $path, string $thumbnailPath, int $thumbnailWidth): bool
    {
        if (!is_file($path)) {
            return false;
        }

        list($width, $height, $mime) = getimagesize($path);
        $originalSize = filesize($path);
        $thumbnailHeight = (int)floor($height * ($thumbnailWidth / $width));

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

        if (IMAGETYPE_GIF === $mime) {
            $this->gifResizer->resize($path, $thumbnailPath, $thumbnailWidth, $thumbnailHeight);
        } else {
            $image = match ($mime) {
                IMAGETYPE_JPEG, IMAGETYPE_JPEG2000 => imagecreatefromjpeg($path),
                IMAGETYPE_PNG => imagecreatefrompng($path),
                IMAGETYPE_WEBP => imagecreatefromwebp($path),
                default => throw new \Exception('Your image cannot be processed, please use another one.'),
            };

            $thumbnail = imagecreatetruecolor($thumbnailWidth, $thumbnailHeight);

            // Transparency
            if (IMAGETYPE_PNG === $mime || IMAGETYPE_WEBP === $mime) {
                imagecolortransparent($thumbnail, imagecolorallocate($thumbnail, 0, 0, 0));
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
            }

            imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $thumbnailWidth, $thumbnailHeight, $width, $height);
            $deg = $this->guessRotation($path);
            $thumbnail = imagerotate($thumbnail, $deg, 0);

            switch ($mime) {
                case IMAGETYPE_JPEG:
                case IMAGETYPE_JPEG2000:
                    imagejpeg($thumbnail, $thumbnailPath, 100);
                    break;
                case IMAGETYPE_PNG:
                    imagepng($thumbnail, $thumbnailPath);
                    break;
                case IMAGETYPE_WEBP:
                    imagewebp($thumbnail, $thumbnailPath, 100);
                    break;
                default:
                    break;
            }
        }

        $thumbnailSize = filesize($thumbnailPath);
        if ($thumbnailSize >= $originalSize) {
            unlink($thumbnailPath);

            return false;
        }

        return true;
    }

    public function crop(string $path, int $maxWidth, int $maxHeight): void
    {
        list($width, $height, $mime) = getimagesize($path);
        $ratio = $width / $height;

        if ($width > $height) {
            $width = (int)ceil($width - ($width * abs($ratio - $maxWidth / $maxHeight)));
        } else {
            $height = (int)ceil($height - ($height * abs($ratio - $maxWidth / $maxHeight)));
        }
        $newWidth = $maxWidth;
        $newHeight = $maxHeight;

        $image = match ($mime) {
            IMAGETYPE_JPEG, IMAGETYPE_JPEG2000 => imagecreatefromjpeg($path),
            IMAGETYPE_PNG => imagecreatefrompng($path),
            IMAGETYPE_WEBP => imagecreatefromwebp($path),
            default => throw new \Exception('Your image cannot be processed, please use another one.'),
        };

        $resized = imagecreatetruecolor($newWidth, $newHeight);

        // Transparency
        if (IMAGETYPE_PNG === $mime || IMAGETYPE_WEBP === $mime) {
            imagecolortransparent($resized, imagecolorallocate($resized, 0, 0, 0));
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
        }

        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $deg = $this->guessRotation($path);
        imagerotate($resized, $deg, 0);

        switch ($mime) {
            case IMAGETYPE_JPEG:
            case IMAGETYPE_JPEG2000:
                imagejpeg($resized, $path, 100);
                break;
            case IMAGETYPE_PNG:
                imagepng($resized, $path);
                break;
            case IMAGETYPE_WEBP:
                imagewebp($resized, $path, 100);
                break;
            default:
                break;
        }
    }

    function guessRotation(string $path): int
    {
        $deg = 0;

        if (function_exists('exif_read_data')) {
            $exif = exif_read_data($path);
            if ($exif && isset($exif['Orientation'])) {
                $orientation = $exif['Orientation'];
                if ($orientation != 1) {
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
