<?php

declare(strict_types=1);

namespace App\Annotation;

use Doctrine\Common\Annotations\Reader;

class UploadAnnotationReader
{
    private Reader $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

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