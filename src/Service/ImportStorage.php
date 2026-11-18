<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Collection;
use App\Model\Import;
use Symfony\Component\HttpFoundation\RequestStack;

class ImportStorage
{
    private const string SESSION_PREFIX = 'import_';

    private const string FILE_PREFIX = 'koillection_import_';

    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

    public function create(Import $import): void
    {
        $path = tempnam(sys_get_temp_dir(), self::FILE_PREFIX);
        $import->getFileFile()->move(\dirname($path), basename($path));

        $import
            ->setPath($path)
            ->setFileFile(null)
        ;
    }

    public function save(Import $import): void
    {
        $this->requestStack->getSession()->set(self::SESSION_PREFIX . $import->getId(), $import);
    }

    public function find(string $id, Collection $collection): ?Import
    {
        $import = $this->requestStack->getSession()->get(self::SESSION_PREFIX . $id);

        if (!$import instanceof Import || $import->getCollectionId() !== $collection->getId()) {
            return null;
        }

        return $import->setCollection($collection);
    }

    public function remove(Import $import): void
    {
        if ($import->getPath() !== null && is_file($import->getPath())) {
            unlink($import->getPath());
        }

        $this->requestStack->getSession()->remove(self::SESSION_PREFIX . $import->getId());
    }
}
