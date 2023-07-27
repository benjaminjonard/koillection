<?php

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class ScrapperImporter
{
    #[Assert\File(mimeTypes: ['application/json'])]
    private File $file;

    public function getFile(): File
    {
        return $this->file;
    }

    public function setFile(File $file): ScrapperImporter
    {
        $this->file = $file;

        return $this;
    }
}
