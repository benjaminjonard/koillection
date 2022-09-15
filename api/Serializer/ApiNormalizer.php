<?php

declare(strict_types=1);

namespace Api\Serializer;

use App\Entity\Album;
use App\Entity\Collection;
use App\Entity\Wishlist;
use App\Service\CountersCache;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class ApiNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface
{
    private readonly DenormalizerInterface|NormalizerInterface $decorated;

    public function __construct(NormalizerInterface $decorated, private readonly CountersCache $countersCache)
    {
        if (!$decorated instanceof DenormalizerInterface) {
            throw new \InvalidArgumentException(sprintf('The decorated normalizer must implement the %s.', DenormalizerInterface::class));
        }

        $this->decorated = $decorated;
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $this->decorated->supportsNormalization($data, $format, $context);
    }

    public function normalize(mixed $object, $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $data = $this->decorated->normalize($object, $format, $context);
        if (\is_array($data) && \in_array($object::class, [Album::class, Collection::class, Wishlist::class])) {
            $counters = $this->countersCache->getCounters($object);
            $data['childrenCounter'] = $counters['children'];
            $data['itemsCounter'] = $counters['items'];
        }

        return $data;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return $this->decorated->supportsDenormalization($data, $type, $format, $context);
    }

    public function denormalize(mixed $data, string $class, string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        return $this->decorated->denormalize($data, $class, $format, $context);
    }

    public function setSerializer(SerializerInterface $serializer): void
    {
        if ($this->decorated instanceof SerializerAwareInterface) {
            $this->decorated->setSerializer($serializer);
        }
    }
}
