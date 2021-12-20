<?php

declare(strict_types=1);

namespace App\Annotation;

use Doctrine\Common\Annotations\Reader;

class UploadAnnotationReader
{
    public function __construct(
        private Reader $reader
    ) {}

    public function getUploadFields($entity) : array
    {
        $reflection = new \ReflectionClass(get_class($entity));
        $properties = [];
        foreach($reflection->getProperties() as $property) {
            $annotation = $this->reader->getPropertyAnnotation($property, Upload::class);
            if ($annotation !== null) {
                $properties[$property->getName()] = $annotation;
            }
        }

        return $properties;
    }
}