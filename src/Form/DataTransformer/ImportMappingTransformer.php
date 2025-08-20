<?php

declare(strict_types=1);

namespace App\Form\DataTransformer;

use App\Model\ImportMapperElement;
use Symfony\Component\Form\DataTransformerInterface;

class ImportMappingTransformer implements DataTransformerInterface
{
    public function transform($value): array
    {
        if ($value === null) {
            return [];
        }

        $elements = [];

        foreach ($value as $data) {
            $elements[] = new ImportMapperElement()
                ->setColumnIndex($data['columnIndex'])
                ->setDatumType($data['datumType'])
                ->setDatumLabel($data['datumLabel'])
                ->setDatumVisibility($data['datumVisibility'])
                ->setCreateCorrespondingTags($data['createCorrespondingTags'])
                ->setDatumPosition($data['datumPosition'])
            ;
        }

        return $elements;
    }

    public function reverseTransform($value): array
    {
        if ($value === null) {
            return [];
        }

        $array = [];
        foreach ($value as $importMapperElement) {
            if ($importMapperElement instanceof ImportMapperElement) {
                $array[] = [
                    'columnIndex' => $importMapperElement->getColumnIndex(),
                    'datumType' => $importMapperElement->getDatumType(),
                    'datumLabel' => $importMapperElement->getDatumLabel(),
                    'datumVisibility' => $importMapperElement->getDatumVisibility(),
                    'datumPosition' => $importMapperElement->getDatumPosition(),
                    'createCorrespondingTags' => $importMapperElement->getCreateCorrespondingTags(),
                ];
            }
        }

        return $array;
    }
}