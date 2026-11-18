<?php

declare(strict_types=1);

namespace App\Message;

use App\Model\ImportDraft;

final readonly class ProcessImportMessage
{
    public function __construct(
        private string $importId,
        private ImportDraft $draft,
        private array $selectedRows,
    ) {
    }

    public function getImportId(): string
    {
        return $this->importId;
    }

    public function getDraft(): ImportDraft
    {
        return $this->draft;
    }

    public function getSelectedRows(): array
    {
        return $this->selectedRows;
    }
}
