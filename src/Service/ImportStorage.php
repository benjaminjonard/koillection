<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Collection;
use App\Model\ImportDraft;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\RequestStack;

class ImportStorage
{
    private const string SESSION_PREFIX = 'import_';

    public const string FILE_PREFIX = 'koillection_import_';

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly ImportArchiveReader $archiveReader,
    ) {
    }

    public function create(ImportDraft $import): void
    {
        $import
            ->setFilename($import->getFileFile()->getClientOriginalName())
            ->setPath($this->moveToTempDir($import->getFileFile()))
            ->setFileFile(null)
        ;

        if ($import->getArchiveFile() instanceof File) {
            $import
                ->setArchiveEntries($this->archiveReader->extract($import->getArchiveFile()->getPathname()))
                ->setArchiveFile(null)
            ;
        }
    }

    public function save(ImportDraft $import): void
    {
        $this->requestStack->getSession()->set(self::SESSION_PREFIX . $import->getId(), $import);
    }

    public function find(string $id, Collection $collection): ?ImportDraft
    {
        $import = $this->requestStack->getSession()->get(self::SESSION_PREFIX . $id);

        if (!$import instanceof ImportDraft || $import->getCollectionId() !== $collection->getId()) {
            return null;
        }

        return $import->setCollection($collection);
    }

    public function forget(ImportDraft $import): void
    {
        $this->requestStack->getSession()->remove(self::SESSION_PREFIX . $import->getId());
    }

    public function removeFiles(ImportDraft $import): void
    {
        $paths = [$import->getPath(), ...array_column($import->getArchiveEntries(), 'path')];
        foreach ($paths as $path) {
            if ($path !== null && is_file($path)) {
                unlink($path);
            }
        }
    }

    private function moveToTempDir(File $file): string
    {
        $path = tempnam(sys_get_temp_dir(), self::FILE_PREFIX);
        $file->move(\dirname($path), basename($path));

        return $path;
    }
}
