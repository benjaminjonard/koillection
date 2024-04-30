<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Configuration;

class ConfigurationAdmin
{
    public function __construct(
        private readonly Configuration $thumbnailsFormat,
        private readonly Configuration $customLightThemeCss,
        private readonly Configuration $customDarkThemeCss,
        private readonly Configuration $enableMetrics
    ) {
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

    public function getEnableMetrics(): Configuration
    {
        return $this->enableMetrics;
    }
}
