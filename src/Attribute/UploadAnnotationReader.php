<?php

declare(strict_types=1);

namespace App\Attribute;

class UploadAnnotationReader
{
    public function getUploadFields(object $entity): array
    {
        $reflection = new \ReflectionClass($entity::class);

        $properties = [];
        foreach ($reflection->getProperties() as $property) {
            foreach ($property->getAttributes() as $attribute) {
                if (Upload::class === $attribute->getName()) {
                    $properties[$property->getName()] = Upload::fromReflectionAttribute($attribute);
                }
            }
        }

        return $properties;
    }
}
