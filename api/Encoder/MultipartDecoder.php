<?php

declare(strict_types=1);

namespace Api\Encoder;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

final readonly class MultipartDecoder implements DecoderInterface
{
    public const FORMAT = 'multipart';

    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function decode(string $data, string $format, array $context = []): ?array
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request instanceof Request) {
            return null;
        }

        $content = $request->getContent() ? json_decode($request->getContent(), true) : [];

        return array_map(static function (string $element): array|string {
            $decoded = json_decode($element, true);

            return \is_array($decoded) ? $decoded : $element;
        }, $request->request->all()) + $request->files->all() + $content;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDecoding(string $format): bool
    {
        return self::FORMAT === $format;
    }
}
