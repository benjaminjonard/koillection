<?php

declare(strict_types=1);

namespace App\Service;

class ThumbnailGenerator
{
    /**
     * @param string $path
     * @param string $thumbnailPath
     * @param int $thumbnailWidth
     * @throws \Exception
     */
    public function generate(string $path, string $thumbnailPath, int $thumbnailWidth) : ?string
    {
        list($width, $height, $mime) = getimagesize($path);

        switch ($mime) {
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($path);
                break;
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

        $thumbnailHeight = (int) floor($height * ($thumbnailWidth / $width));
        $thumbnail = imagecreatetruecolor($thumbnailWidth, $thumbnailHeight);

        //Transparency
        if ($mime === IMAGETYPE_PNG || $mime === IMAGETYPE_WEBP) {
            imagecolortransparent($thumbnail, imagecolorallocate($thumbnail, 0, 0, 0));
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
        } elseif ($mime === IMAGETYPE_GIF) {
            imagecolortransparent($thumbnail, imagecolorallocate($thumbnail, 0, 0, 0));
        }

        imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $thumbnailWidth, $thumbnailHeight, $width, $height);

        $dir = explode('/', $thumbnailPath);
        \array_pop($dir);
        $dir = implode('/', $dir);

        if (!is_dir($dir) && !mkdir($dir) && !is_dir($dir)) {
            throw new \Exception('There was a problem while uploading the image. Please try again!');
        }

        switch ($mime) {
            case IMAGETYPE_GIF:
                imagegif($thumbnail, $thumbnailPath);
                break;
            case IMAGETYPE_JPEG:
            case IMAGETYPE_JPEG2000:
                imagejpeg($thumbnail, $thumbnailPath, 90);
                break;
            case IMAGETYPE_PNG:
                imagepng($thumbnail, $thumbnailPath);
                break;
            case IMAGETYPE_WEBP:
                imagewebp($thumbnail, $thumbnailPath, 90);
                break;
            default:
                break;
        }

        return $thumbnailPath;
    }
}
