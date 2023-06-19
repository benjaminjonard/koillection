<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Configuration;

class ConfigurationAdmin
{
    private readonly Configuration $thumbnailsFormat;

    private readonly Configuration $customLightThemeCss;

    private readonly Configuration $customDarkThemeCss;

    public function __construct(Configuration $thumbnailsFormat, Configuration $customLightThemeCss, Configuration $customDarkThemeCss)
    {
        $this->thumbnailsFormat = $thumbnailsFormat;
        $this->customLightThemeCss = $customLightThemeCss;
        $this->customDarkThemeCss = $customDarkThemeCss;
    }

    public function getThumbnailsFormat(): Configuration
    {
        return $this->thumbnailsFormat;
    }

    public function getCustomLightThemeCss(): Configuration
    {
        return $this->customLightThemeCss;
    }

    public function getCustomDarkThemeCss(): Configuration
    {
        return $this->customDarkThemeCss;
    }
}
