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
     * @throws \Exception
     */
    public function generateThumbnail(string $path, string $thumbnailPath) : void
    {
        $info = pathinfo($path);
        $extension = strtolower($info['extension']);
        switch ($extension) {
            case 'gif':
                $im = imagecreatefromgif($path);
                break;
            case 'jpg':
            case 'jpeg':
                $im = imagecreatefromjpeg($path);
                break;
            case 'png':
                $im = imagecreatefrompng($path);
                break;
            default:
                throw new \Exception('Your image cannot be processed, please use another one.');
        }

        $ox = imagesx($im);
        $oy = imagesy($im);

        $final_width_of_image = 150;
        $nx = $final_width_of_image;
        $ny = (int) floor($oy * ($final_width_of_image / $ox));

        $nm = imagecreatetruecolor($nx, $ny);

        //Transparency
        if ($extension === 'gif' || ($extension === 'png')) {
            imagealphablending($nm, false);
            imagesavealpha($nm, true);
            $transparent = imagecolorallocatealpha($nm, 255, 255, 255, 127);
            imagefilledrectangle($nm, 0, 0, $nx, $ny, $transparent);
        }

        imagecopyresampled($nm, $im, 0, 0, 0, 0, $nx, $ny, $ox, $oy);

        $dir = explode('/', $thumbnailPath);
        array_pop($dir);
        $dir = implode('/', $dir);

        if (!is_dir($dir) && !mkdir($dir) && !is_dir($dir)) {
            throw new \Exception('There was a problem while uploading the image. Please try again!');
        }

        switch ($extension) {
            case 'gif':
                imagegif($nm, $thumbnailPath);
                break;
            case 'jpg':
            case 'jpeg':
                imagejpeg($nm, $thumbnailPath, 100);
                break;
            case 'png':
                imagepng($nm, $thumbnailPath, 9);
                break;
            default:
                break;
        }
    }
}
