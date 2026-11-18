<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Collection;
use App\Entity\Import;
use App\Enum\ImportStatusEnum;
use App\Form\Type\Entity\ImportMappingType;
use App\Form\Type\Entity\ImportUploadType;
use App\Service\ImportHandler;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImportController extends AbstractController
{
    #[Route(path: '/collections/{id}/import', name: 'app_collection_import', methods: ['GET', 'POST'])]
    public function import(Request $request, Collection $collection, ImportHandler $importHandler, ManagerRegistry $managerRegistry): Response
    {
        $import = new Import()
            ->setCollection($collection)
            ->setOwner($this->getUser())
            ->setStatus(ImportStatusEnum::NEW)
        ;

        $form = $this->createForm(ImportUploadType::class, $import);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $importHandler->prefillMapping($import);
            $managerRegistry->getManager()->persist($import);
            $managerRegistry->getManager()->flush();

            return $this->redirectToRoute('app_collection_import_mapping', [
                'id' => $collection->getId(),
                'importId' => $import->getId()
            ]);
        }

        return $this->render('App/Import/import.html.twig', [
            'form' => $form,
            'collection' => $collection,
        ]);
    }

    #[Route(path: '/collections/{id}/import/{importId}/mapping', name: 'app_collection_import_mapping', methods: ['GET', 'POST'])]
    public function mapping(
        Request $request,
        Collection $collection,
        #[MapEntity(expr: 'repository.find(importId)')] Import $import,
        ManagerRegistry $managerRegistry
    ): Response {
        $form = $this->createForm(ImportMappingType::class, $import);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->flush();

            return $this->redirectToRoute('app_collection_import_preview', [
                'id' => $collection->getId(),
                'importId' => $import->getId()
            ]);
        }

        return $this->render('App/Import/import-mapping.html.twig', [
            'form' => $form,
            'collection' => $collection,
            'import' => $import,
        ]);
    }

    #[Route(path: '/collections/{id}/import/{importId}/preview', name: 'app_collection_import_preview', methods: ['GET'])]
    public function preview(
        Collection $collection,
        #[MapEntity(expr: 'repository.find(importId)')] Import $import,
        ImportHandler $importHandler
    ): Response {
        $errors = $importHandler->validate($import);

        return $this->render('App/Import/import-preview.html.twig', [
            'collection' => $collection,
            'import' => $import,
            'errors' => $errors,
            'items' => $errors === [] ? $importHandler->createItems($import, true) : [],
            'duplicates' => $importHandler->findDuplicateRows($import),
        ]);
    }

    #[Route(path: '/collections/{id}/import/{importId}/import', name: 'app_collection_import_import', methods: ['POST'])]
    public function executeImport(
        Request $request,
        Collection $collection,
        #[MapEntity(expr: 'repository.find(importId)')] Import $import,
        ImportHandler $importHandler,
        ManagerRegistry $managerRegistry,
        TranslatorInterface $translator
    ): Response {
        if ($import->getStatus() === ImportStatusEnum::DONE) {
            $this->addFlash('error', $translator->trans('error.import.already_done'));

            return $this->redirectToRoute('app_collection_show', ['id' => $collection->getId()]);
        }

        if ($importHandler->validate($import) !== []) {
            return $this->redirectToRoute('app_collection_import_preview', [
                'id' => $collection->getId(),
                'importId' => $import->getId()
            ]);
        }

        $selectedRows = array_map(intval(...), $request->request->all('rows'));
        if ($selectedRows === []) {
            $this->addFlash('error', $translator->trans('error.import.no_rows_selected'));

            return $this->redirectToRoute('app_collection_import_preview', [
                'id' => $collection->getId(),
                'importId' => $import->getId()
            ]);
        }

        $totalNumberOfItems = \count($import->getRows());
        $duplicates = $importHandler->findDuplicateRows($import);

        $items = $importHandler->createItems($import, false, $selectedRows);
        foreach ($items as $item) {
            $item->setCollection($collection);
            $managerRegistry->getManager()->persist($item);
        }

        $import
            ->setStatus(ImportStatusEnum::DONE)
            ->setTotalNumberOfItems($totalNumberOfItems)
            ->setNumberOfImportedItems(\count($items))
            ->setNumberOfSkippedItems($totalNumberOfItems - \count($items))
            ->setNumberOfDuplicatedItems(\count($duplicates))
        ;

        $managerRegistry->getManager()->flush();

        $this->addFlash('notice', $translator->trans('message.items_imported', ['count' => \count($items)]));

        return $this->redirectToRoute('app_collection_show', ['id' => $collection->getId()]);
    }
}
