<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Datum;
use App\Entity\Import;
use App\Entity\Item;
use App\Entity\Tag;

class ImportHandler
{
    public function createPreview(Import $import): array
    {
        $items = [];
        foreach ($import->getRows() as $row) {
            $item = new Item()
                ->setName($row[$import->getNameIndex()])
            ;

            if ($import->getImageIndex()) {
                $item->setImage($this->createThumbnailBase64($row[$import->getImageIndex()]));
            }

            foreach ($import->getMapping() as $mapping) {
                $datum = new Datum()
                    ->setValue($row[$mapping['columnIndex']])
                    ->setLabel($mapping['datumLabel'])
                    ->setType($mapping['datumType'])
                    ->setVisibility($mapping['datumVisibility'])
                    ->setPosition($mapping['datumPosition'])
                ;
                $item->addData($datum);

                if ($mapping['createCorrespondingTags']) {
                    $values = explode(',', $datum->getValue());
                    foreach ($values as $value) {
                        $tag = new Tag()
                            ->setLabel($value)
                        ;

                        $item->addTag($tag);
                    }
                }
            }

            $items[] = $item;
        }

        return $items;
    }

    public function createThumbnailBase64(string $url, int $maxWidth = 250, int $maxHeight = 250): string
    {
        // 1. Download the image
        $data = @file_get_contents($url);
        if ($data === false) {
            throw new \RuntimeException("Failed to download image from $url");
        }

        // 2. Create image resource
        $src = @imagecreatefromstring($data);
        if ($src === false) {
            throw new \RuntimeException("Invalid image data from $url");
        }

        $origWidth  = imagesx($src);
        $origHeight = imagesy($src);

        // 3. Calculate scale ratio
        $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);

        $newWidth  = (int)($origWidth * $ratio);
        $newHeight = (int)($origHeight * $ratio);

        // 4. Create the thumbnail canvas
        $thumb = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG/GIF
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);

        // 5. Resize
        imagecopyresampled(
            $thumb,
            $src,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $origWidth, $origHeight
        );

        // 6. Output to memory
        ob_start();
        imagepng($thumb); // Save as PNG for base64 output
        $imageData = ob_get_clean();

        // 7. Cleanup
        imagedestroy($src);
        imagedestroy($thumb);

        // 8. Return base64 string (with data URI prefix for <img>)
        return 'data:image/png;base64,' . base64_encode($imageData);
    }
}
