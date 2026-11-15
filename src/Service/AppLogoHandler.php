<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Configuration;
use App\Enum\ConfigurationEnum;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly class AppLogoHandler
{
    private const string DIRECTORY = 'uploads/branding/';

    public function __construct(
        private RandomStringGenerator $randomStringGenerator,
        private ThumbnailGenerator $thumbnailGenerator,
        #[Autowire('%kernel.project_dir%/public')] private string $publicPath,
        #[Autowire(param: 'kernel.environment')] private string $env
    ) {
    }

    public function upload(Configuration $appLogo, UploadedFile $file): void
    {
        $this->remove($appLogo);

        $relativePath = $this->env === 'test' ? sys_get_temp_dir() . '/koillection-branding/' : self::DIRECTORY;
        $absolutePath = $this->env === 'test' ? $relativePath : $this->publicPath . '/' . $relativePath;

        if (!is_dir($absolutePath) && !mkdir($absolutePath, 0o755, true) && !is_dir($absolutePath)) {
            throw new \RuntimeException('There was a problem while uploading the logo. Please try again!');
        }

        $generatedName = $this->randomStringGenerator->generate(20);
        $fileName = $generatedName . '.' . $file->guessExtension();
        $file->move($absolutePath, $fileName);

        $paths = ['original' => $relativePath . $fileName];
        foreach (ConfigurationEnum::APP_LOGO_SIZES as $size) {
            $sizeFileName = $generatedName . '_' . $size . '.png';
            $generated = $this->thumbnailGenerator->generate($absolutePath . $fileName, $absolutePath . $sizeFileName, $size, 'png');
            $paths[(string) $size] = $generated ? $relativePath . $sizeFileName : $paths['original'];
        }

        $appLogo->setValue(json_encode($paths));
    }

    public function remove(Configuration $appLogo): void
    {
        $paths = json_decode($appLogo->getValue() ?? '[]', true);

        if (\is_array($paths)) {
            foreach (array_unique($paths) as $path) {
                @unlink(str_starts_with($path, '/') ? $path : $this->publicPath . '/' . $path);
            }
        }

        $appLogo->setValue(null);
    }
}
