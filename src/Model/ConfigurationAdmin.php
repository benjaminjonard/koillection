<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Configuration;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class ConfigurationAdmin
{
    #[Assert\Image(
        mimeTypes: ['image/png', 'image/jpeg', 'image/webp'],
        minWidth: 512,
        minHeight: 512,
        allowLandscape: false,
        allowPortrait: false
    )]
    private ?UploadedFile $appLogoFile = null;

    private bool $deleteAppLogo = false;

    public function __construct(
        private readonly Configuration $thumbnailsFormat,
        private readonly Configuration $customLightThemeCss,
        private readonly Configuration $customDarkThemeCss,
        private readonly Configuration $enableMetrics,
        private readonly Configuration $appName,
        private readonly Configuration $appLogo
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

    public function getAppName(): Configuration
    {
        return $this->appName;
    }

    public function getAppLogo(): Configuration
    {
        return $this->appLogo;
    }

    public function getImage(): ?string
    {
        $paths = json_decode($this->appLogo->getValue() ?? '', true);

        return \is_array($paths) ? ($paths['192'] ?? $paths['original'] ?? null) : null;
    }

    public function getAppLogoFile(): ?UploadedFile
    {
        return $this->appLogoFile;
    }

    public function setAppLogoFile(?UploadedFile $appLogoFile): self
    {
        $this->appLogoFile = $appLogoFile;

        return $this;
    }

    public function getDeleteAppLogo(): bool
    {
        return $this->deleteAppLogo;
    }

    public function setDeleteAppLogo(bool $deleteAppLogo): self
    {
        $this->deleteAppLogo = $deleteAppLogo;

        return $this;
    }
}
