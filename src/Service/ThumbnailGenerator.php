<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Class ThumbnailGenerator
 *
 * @package App\Service
 */
class ThumbnailGenerator
{
    /**
     * @param string $path
     * @param string $thumbnailPath
     * @param int $thumbnailWidth
     * @throws \Exception
     */
    public function generateThumbnail(string $path, string $thumbnailPath, int $thumbnailWidth) : void
    {
        $mimetype = mime_content_type($path);

        switch ($mimetype) {
            case 'image/gif':
                $image = imagecreatefromgif($path);
                break;
            case 'image/jpg':
            case 'image/jpeg':
                $image = imagecreatefromjpeg($path);
                break;
            case 'image/png':
                $image = imagecreatefrompng($path);
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($path);
                break;
            default:
                throw new \Exception('Your image cannot be processed, please use another one.');
        }

        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);

        $thumbnailHeight = (int) floor($imageHeight * ($thumbnailWidth / $imageWidth));
        $thumbnail = imagecreatetruecolor($thumbnailWidth, $thumbnailHeight);

        //Transparency
        if ($mimetype === 'image/png' || $mimetype === 'image/webp') {
            imagecolortransparent($thumbnail, imagecolorallocate($thumbnail, 0, 0, 0));
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
        } elseif ($mimetype === 'image/gif') {
            imagecolortransparent($thumbnail, imagecolorallocate($thumbnail, 0, 0, 0));
        }

        imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $thumbnailWidth, $thumbnailHeight, $imageWidth, $imageHeight);

        $dir = explode('/', $thumbnailPath);
        array_pop($dir);
        $dir = implode('/', $dir);

        if (!is_dir($dir) && !mkdir($dir) && !is_dir($dir)) {
            throw new \Exception('There was a problem while uploading the image. Please try again!');
        }

        switch ($mimetype) {
            case 'image/gif':
                imagegif($thumbnail, $thumbnailPath);
                break;
            case 'image/jpg':
            case 'image/jpeg':
                imagejpeg($thumbnail, $thumbnailPath, 100);
                break;
            case 'image/png':
                imagepng($thumbnail, $thumbnailPath, 9);
                break;
            case 'image/webp':
                imagewebp($thumbnail, $thumbnailPath, 100);
                break;
            default:
                break;
        }
    }
}
