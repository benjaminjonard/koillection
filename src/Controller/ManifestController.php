<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\ConfigurationEnum;
use App\Twig\BrandingExtension;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ManifestController extends AbstractController
{
    private const array DEFAULT_ICON_SIZES = [48, 72, 96, 144, 168, 192, 256, 512, 1024];

    #[Route(path: '/manifest.json', name: 'app_manifest', methods: ['GET'])]
    public function manifest(BrandingExtension $brandingExtension, Packages $packages): Response
    {
        $icons = [];
        if ($brandingExtension->appLogo(512) !== null) {
            foreach (ConfigurationEnum::APP_LOGO_SIZES as $size) {
                if ($size >= 48) {
                    $path = $brandingExtension->appLogo($size);
                    $icons[] = ['src' => $packages->getUrl($path), 'sizes' => "{$size}x{$size}", 'type' => $this->getMimeType($path)];
                }
            }
        } else {
            foreach (self::DEFAULT_ICON_SIZES as $size) {
                $icons[] = ['src' => $packages->getUrl("icons/square/{$size}.png"), 'sizes' => "{$size}x{$size}", 'type' => 'image/png'];
            }
            $icons[] = ['src' => $packages->getUrl('icons/maskable.png'), 'sizes' => '1024x1024', 'type' => 'image/png', 'purpose' => 'maskable'];
        }

        $appName = $brandingExtension->appName();

        return new JsonResponse([
            'name' => $appName,
            'short_name' => $appName,
            'description' => 'A single place to manage all your collections',
            'start_url' => '/',
            'icons' => $icons,
            'theme_color' => '#009688',
            'background_color' => '#009688',
            'display' => 'standalone',
        ], Response::HTTP_OK, ['Content-Type' => 'application/manifest+json']);
    }

    private function getMimeType(string $path): string
    {
        return match (strtolower(pathinfo($path, \PATHINFO_EXTENSION))) {
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            default => 'image/png',
        };
    }
}
