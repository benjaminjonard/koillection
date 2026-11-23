<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Import;
use App\Enum\ImportStatusEnum;
use App\Message\ProcessImportMessage;
use App\Service\ImportDraftManager;
use App\Service\ImportHandler;
use App\Service\RefreshCachedValuesQueue;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

#[AsMessageHandler]
final readonly class ProcessImportHandler
{
    private const int BATCH_SIZE = 20;

    public function __construct(
        private ManagerRegistry $managerRegistry,
        private ImportHandler $importHandler,
        private ImportDraftManager $importDraftManager,
        private TokenStorageInterface $tokenStorage,
        private RefreshCachedValuesQueue $refreshCachedValuesQueue,
    ) {
    }

    public function __invoke(ProcessImportMessage $message): void
    {
        $import = $this->managerRegistry->getRepository(Import::class)->find($message->getImportId());
        if (!$import instanceof Import) {
            $this->importDraftManager->removeFiles($message->getDraft());

            return;
        }

        $owner = $import->getOwner();
        $previousToken = $this->tokenStorage->getToken();
        $filters = $this->managerRegistry->getManager()->getFilters();
        $isOwnershipFilterEnabled = $filters->isEnabled('ownership');

        $this->tokenStorage->setToken(new UsernamePasswordToken($owner, 'main', $owner->getRoles()));
        $filters->enable('ownership')->setParameter('id', $owner->getId(), 'string');

        try {
            $this->process($import, $message);
            $this->refreshCachedValuesQueue->process();
        } finally {
            $this->importDraftManager->removeFiles($message->getDraft());
            $this->tokenStorage->setToken($previousToken);

            if (!$isOwnershipFilterEnabled && $this->managerRegistry->getManager()->getFilters()->isEnabled('ownership')) {
                $this->managerRegistry->getManager()->getFilters()->disable('ownership');
            }
        }
    }

    private function process(Import $import, ProcessImportMessage $message): void
    {
        $manager = $this->managerRegistry->getManager();
        $import->setStatus(ImportStatusEnum::PROCESSING);
        $manager->flush();

        $draft = $message->getDraft()->setCollection($import->getCollection());

        foreach (array_chunk($message->getSelectedRows(), self::BATCH_SIZE) as $rows) {
            try {
                $items = $this->importHandler->createItems($draft, false, $rows);
                foreach ($items as $item) {
                    $item->setCollection($import->getCollection());
                    $manager->persist($item);
                }

                $import
                    ->setProcessedRows($import->getProcessedRows() + \count($rows))
                    ->setImportedItems($import->getImportedItems() + \count($items))
                ;
                $manager->flush();
            } catch (\Throwable $exception) {
                $this->fail($import->getId(), $rows, $exception);

                return;
            }
        }

        $import
            ->setStatus(ImportStatusEnum::DONE)
            ->setFinishedAt(new \DateTimeImmutable())
        ;
        $manager->flush();
    }

    private function fail(string $importId, array $rows, \Throwable $exception): void
    {
        $this->managerRegistry->resetManager();
        $import = $this->managerRegistry->getRepository(Import::class)->find($importId);
        if (!$import instanceof Import) {
            return;
        }

        $import
            ->setStatus(ImportStatusEnum::FAILED)
            ->setErrors([...$import->getErrors(), [
                'lines' => array_map(static fn (int $row): int => $row + 2, $rows),
                'message' => $exception->getMessage(),
            ]])
            ->setFinishedAt(new \DateTimeImmutable())
        ;
        $this->managerRegistry->getManager()->flush();
    }
}
