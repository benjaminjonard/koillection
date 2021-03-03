<?php

declare(strict_types=1);

namespace App\Service;

class ThumbnailGenerator
{
    private GifResizer $gifResizer;

    public function __construct(GifResizer $gifResizer)
    {
        $this->gifResizer = $gifResizer;
    }

    public function generate(string $path, string $thumbnailPath, int $thumbnailWidth) : bool
    {
        if (!is_file($path)) {
            return false;
        }

        list($width, $height, $mime) = getimagesize($path);
        $originalSize = filesize($path);
        $thumbnailHeight = (int) floor($height * ($thumbnailWidth / $width));

        if ($width <= $thumbnailWidth) {
            return false;
        }

        // Create user directory in uploads
        $dir = explode('/', $thumbnailPath);
        \array_pop($dir);
        $dir = implode('/', $dir);

        if (!is_dir($dir) && !mkdir($dir) && !is_dir($dir)) {
            throw new \Exception('There was a problem while uploading the image. Please try again!');
        }

        if ($mime === IMAGETYPE_GIF) {
            $this->gifResizer->resize($path, $thumbnailPath, $thumbnailWidth, $thumbnailHeight);
        } else {
            switch ($mime) {
                case IMAGETYPE_JPEG:
                case IMAGETYPE_JPEG2000:
                    $image = imagecreatefromjpeg($path);
                    break;
                case IMAGETYPE_PNG:
                    $image = imagecreatefrompng($path);
                    break;
                case IMAGETYPE_WEBP:
                    $image = imagecreatefromwebp($path);
                    break;
                default:
                    throw new \Exception('Your image cannot be processed, please use another one.');
            }


            $thumbnail = imagecreatetruecolor($thumbnailWidth, $thumbnailHeight);

            //Transparency
            if ($mime === IMAGETYPE_PNG || $mime === IMAGETYPE_WEBP) {
                imagecolortransparent($thumbnail, imagecolorallocate($thumbnail, 0, 0, 0));
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
            }

            imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $thumbnailWidth, $thumbnailHeight, $width, $height);

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
}
