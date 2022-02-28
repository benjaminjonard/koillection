<?php

declare(strict_types=1);

namespace App\Attribute;

use App\Entity\Collection;

class UploadAnnotationReader
{
    public function getUploadFields($entity): array
    {
        $reflection = new \ReflectionClass(\get_class($entity));

        $properties = [];
        foreach ($reflection->getProperties() as $property) {
            foreach ($property->getAttributes() as $attribute) {
                if ($attribute->getName() === Upload::class) {
                    $properties[$property->getName()] = Upload::fromReflectionAttribute($attribute);
                }
            }
        }

        return $properties;
    }
}
