<?php

declare(strict_types=1);

namespace App\Twig;

use App\Enum\ConfigurationEnum;
use App\Service\ConfigurationHelper;
use Twig\Attribute\AsTwigFunction;

readonly class BrandingExtension
{
    public function __construct(
        private ConfigurationHelper $configurationHelper
    ) {
    }

    #[AsTwigFunction('appName')]
    public function appName(): string
    {
        $appName = trim($this->configurationHelper->getValue(ConfigurationEnum::APP_NAME) ?? '');

        return $appName !== '' ? $appName : ConfigurationEnum::DEFAULT_APP_NAME;
    }

    #[AsTwigFunction('appLogo')]
    public function appLogo(int $size): ?string
    {
        $paths = json_decode($this->configurationHelper->getValue(ConfigurationEnum::APP_LOGO) ?? '', true);

        if (!\is_array($paths)) {
            return null;
        }

        return $paths[(string) $size] ?? $paths['original'] ?? null;
    }
}
