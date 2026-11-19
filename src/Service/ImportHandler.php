<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Datum;
use App\Entity\Item;
use App\Entity\Tag;
use App\Enum\DatumTypeEnum;
use App\Enum\VisibilityEnum;
use App\Form\DataTransformer\UrlToImageTransformer;
use App\Model\ImportDraft;
use App\Repository\TagRepository;
use App\Service\Scraper\ScrapingUrlGuard;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ImportHandler
{
    public function __construct(
        private readonly TagRepository $tagRepository,
        private readonly UrlToImageTransformer $urlToImageTransformer,
        private readonly ScrapingUrlGuard $urlGuard,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly TemporaryFileManager $temporaryFileManager,
    ) {
    }

    public function createItems(ImportDraft $import, bool $isPreview, ?array $selectedRows = null): array
    {
        $items = [];
        $tags = [];
        foreach ($import->getRows() as $index => $row) {
            if ($selectedRows !== null && !\in_array($index, $selectedRows, true)) {
                continue;
            }

            $item = new Item()
                ->setName($row[$import->getNameIndex()])
                ->setVisibility($import->getCollection()?->getVisibility() ?? VisibilityEnum::VISIBILITY_PUBLIC)
            ;

            $quantity = $this->getCell($row, $import->getQuantityIndex());
            if ($quantity !== '') {
                $item->setQuantity((int) $quantity);
            }

            $visibility = $this->getCell($row, $import->getVisibilityIndex());
            if ($visibility !== '') {
                $item->setVisibility(strtolower($visibility));
            }

            $this->addTags($item, $this->getCell($row, $import->getTagsIndex()), $tags);

            $image = $this->getCell($row, $import->getImageIndex());
            if ($image !== '' && !$this->isUrl($image)) {
                if ($isPreview) {
                    $item->setImage($this->urlGenerator->generate('app_collection_import_image', [
                        'id' => $import->getCollection()?->getId(),
                        'importId' => $import->getId(),
                        'name' => $image,
                    ]));
                } else {
                    $path = $this->temporaryFileManager->createFilePath();
                    copy($import->getArchiveEntries()[$image]['path'], $path);
                    $item->setFile(new UploadedFile($path, basename($image), mime_content_type($path), null, true));
                }
            } elseif ($image !== '') {
                if ($isPreview) {
                    $item->setImage($this->isSupportedUrl($image) ? $image : null);
                } else {
                    $file = $this->getFile($image);
                    if ($file instanceof UploadedFile) {
                        $item->setFile($file);
                    }
                }
            }

            foreach ($import->getMapping() as $mapping) {
                $value = trim((string) ($row[$mapping['columnIndex']] ?? ''));
                $datum = new Datum()
                    ->setValue($value === '' ? null : $value)
                    ->setLabel($mapping['datumLabel'])
                    ->setType($mapping['datumType'])
                    ->setVisibility($mapping['datumVisibility'])
                    ->setPosition($mapping['datumPosition'])
                ;

                if ($mapping['datumType'] === DatumTypeEnum::TYPE_PRICE) {
                    $datum->setCurrency($import->getCollection()?->getOwner()?->getCurrency());

                    if ($value !== '') {
                        [$amount, $currency] = explode(' ', $value);
                        $datum
                            ->setValue($amount)
                            ->setCurrency(strtoupper($currency))
                        ;
                    }
                }

                if ($mapping['datumType'] === DatumTypeEnum::TYPE_COUNTRY && $value !== '') {
                    $datum->setValue(strtoupper($value));
                }

                $item->addData($datum);

                if ($mapping['createCorrespondingTags']) {
                    $this->addTags($item, (string) $datum->getValue(), $tags);
                }
            }

            $items[$index] = $item;
        }

        return $items;
    }

    public function prefillMapping(ImportDraft $import): void
    {
        $headers = array_map(static fn ($header): string => trim((string) $header), $import->getHeaders() ?: []);
        $normalizedHeaders = array_map(strtolower(...), $headers);

        $import
            ->setNameIndex($this->findHeaderIndex($normalizedHeaders, 'name'))
            ->setImageIndex($this->findHeaderIndex($normalizedHeaders, 'image'))
            ->setQuantityIndex($this->findHeaderIndex($normalizedHeaders, 'quantity'))
            ->setTagsIndex($this->findHeaderIndex($normalizedHeaders, 'tags'))
            ->setVisibilityIndex($this->findHeaderIndex($normalizedHeaders, 'visibility'))
        ;

        $baseIndexes = [
            $import->getNameIndex(),
            $import->getImageIndex(),
            $import->getQuantityIndex(),
            $import->getTagsIndex(),
            $import->getVisibilityIndex(),
        ];

        $mapping = [];
        foreach ($headers as $index => $header) {
            if ($header === '' || \in_array($index, $baseIndexes, true)) {
                continue;
            }

            $mapping[] = [
                'columnIndex' => $index,
                'datumType' => DatumTypeEnum::TYPE_TEXT,
                'datumLabel' => $header,
                'datumVisibility' => VisibilityEnum::VISIBILITY_PUBLIC,
                'datumPosition' => \count($mapping) + 1,
                'createCorrespondingTags' => false,
            ];
        }

        $import->setMapping($mapping);
    }

    public function findDuplicateRows(ImportDraft $import): array
    {
        $existingNames = [];
        foreach ($import->getCollection()?->getItems() ?? [] as $item) {
            $existingNames[$item->getName()] = true;
        }

        $duplicates = [];
        foreach ($import->getRows() as $index => $row) {
            if (isset($existingNames[$this->getCell($row, $import->getNameIndex())])) {
                $duplicates[] = $index;
            }
        }

        return $duplicates;
    }

    public function validate(ImportDraft $import): array
    {
        $headers = $import->getHeaders();
        $errors = [];
        foreach ($import->getRows() as $index => $row) {
            $line = $index + 2;

            if (trim((string) ($row[$import->getNameIndex()] ?? '')) === '') {
                $errors[] = $this->createError($line, $headers[$import->getNameIndex()] ?? '', '', 'error.import.empty_name');
            }

            $quantity = $this->getCell($row, $import->getQuantityIndex());
            if ($quantity !== '' && (!ctype_digit($quantity) || (int) $quantity < 1)) {
                $errors[] = $this->createError($line, $headers[$import->getQuantityIndex()] ?? '', $quantity, 'error.import.invalid_quantity');
            }

            $visibility = $this->getCell($row, $import->getVisibilityIndex());
            if ($visibility !== '' && !\in_array(strtolower($visibility), VisibilityEnum::VISIBILITIES, true)) {
                $errors[] = $this->createError($line, $headers[$import->getVisibilityIndex()] ?? '', $visibility, 'error.import.invalid_visibility');
            }

            $image = $this->getCell($row, $import->getImageIndex());
            if ($image !== '' && !$this->isUrl($image)) {
                $entry = $import->getArchiveEntries()[$image] ?? null;
                $error = $entry === null ? 'error.import.image_not_found' : $entry['error'];
                if ($error !== null) {
                    $errors[] = $this->createError($line, $headers[$import->getImageIndex()] ?? '', $image, $error);
                }
            }

            foreach ($import->getMapping() as $mapping) {
                $value = trim((string) ($row[$mapping['columnIndex']] ?? ''));
                if ($value === '') {
                    continue;
                }

                $error = $this->getValueError($mapping['datumType'], $value);
                if ($error !== null) {
                    $errors[] = $this->createError($line, $headers[$mapping['columnIndex']] ?? '', $value, $error);
                }
            }
        }

        return $errors;
    }

    private function findHeaderIndex(array $headers, string $property): ?int
    {
        $index = array_search($property, $headers, true);

        return $index === false ? null : $index;
    }

    private function getCell(array $row, ?int $index): string
    {
        return $index === null ? '' : trim((string) ($row[$index] ?? ''));
    }

    private function addTags(Item $item, string $values, array &$tags): void
    {
        foreach (explode(',', $values) as $value) {
            $label = trim($value);
            if ($label === '') {
                continue;
            }

            $tag = $tags[$label] ??= $this->tagRepository->findOneBy(['label' => $label]) ?? new Tag()->setLabel($label);
            $item->addTag($tag);
        }
    }

    private function getValueError(string $type, string $value): ?string
    {
        $isValid = match ($type) {
            DatumTypeEnum::TYPE_NUMBER => is_numeric($value),
            DatumTypeEnum::TYPE_RATING => ctype_digit($value) && (int) $value >= 1 && (int) $value <= 10,
            DatumTypeEnum::TYPE_CHECKBOX => \in_array($value, ['0', '1'], true),
            DatumTypeEnum::TYPE_COUNTRY => Countries::exists(strtoupper($value)),
            DatumTypeEnum::TYPE_DATE => (\DateTimeImmutable::createFromFormat('!Y-m-d', $value) ?: null)?->format('Y-m-d') === $value,
            DatumTypeEnum::TYPE_PRICE => preg_match('/^(\d+(?:\.\d+)?) ([A-Za-z]{3})$/', $value, $matches) === 1 && Currencies::exists(strtoupper($matches[2])),
            default => true,
        };

        return $isValid ? null : 'error.import.invalid_' . str_replace('-', '_', $type);
    }

    private function createError(int $line, string $column, string $value, string $message): array
    {
        return [
            'line' => $line,
            'column' => $column,
            'value' => $value,
            'message' => $message,
        ];
    }

    private function isUrl(string $value): bool
    {
        return str_starts_with($value, '//') || preg_match('#^[a-z][a-z0-9+.-]*://#i', $value) === 1;
    }

    private function isSupportedUrl(string $url): bool
    {
        try {
            $this->urlGuard->assertSchemeIsSupported($url);
        } catch (\RuntimeException) {
            return false;
        }

        return true;
    }

    private function getFile(string $url): ?UploadedFile
    {
        if (!$this->isSupportedUrl($url)) {
            return null;
        }

        try {
            $file = $this->urlToImageTransformer->reverseTransform($url);
        } catch (\Throwable) {
            return null;
        }

        if (!$file instanceof UploadedFile || !str_starts_with((string) $file->getMimeType(), 'image/')) {
            return null;
        }

        return $file;
    }
}
