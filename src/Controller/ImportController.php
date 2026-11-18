<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Collection;
use App\Form\Type\Model\ImportMappingType;
use App\Form\Type\Model\ImportUploadType;
use App\Model\Import;
use App\Service\ImportHandler;
use App\Service\ImportStorage;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImportController extends AbstractController
{
    #[Route(path: '/collections/{id}/import', name: 'app_collection_import', methods: ['GET', 'POST'])]
    public function import(Request $request, Collection $collection, ImportHandler $importHandler, ImportStorage $importStorage): Response
    {
        $import = new Import()->setCollection($collection);

        $form = $this->createForm(ImportUploadType::class, $import);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $importStorage->create($import);
            $importHandler->prefillMapping($import);
            $importStorage->save($import);

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
    public function mapping(Request $request, Collection $collection, string $importId, ImportStorage $importStorage): Response
    {
        $import = $importStorage->find($importId, $collection) ?? throw $this->createNotFoundException();

        $form = $this->createForm(ImportMappingType::class, $import);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $importStorage->save($import);

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
    public function preview(Collection $collection, string $importId, ImportHandler $importHandler, ImportStorage $importStorage): Response
    {
        $import = $importStorage->find($importId, $collection) ?? throw $this->createNotFoundException();

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
        string $importId,
        ImportHandler $importHandler,
        ImportStorage $importStorage,
        ManagerRegistry $managerRegistry,
        TranslatorInterface $translator
    ): Response {
        $import = $importStorage->find($importId, $collection) ?? throw $this->createNotFoundException();

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

        $items = $importHandler->createItems($import, false, $selectedRows);
        foreach ($items as $item) {
            $item->setCollection($collection);
            $managerRegistry->getManager()->persist($item);
        }

        $managerRegistry->getManager()->flush();
        $importStorage->remove($import);

        $this->addFlash('notice', $translator->trans('message.items_imported', ['count' => \count($items)]));

        return $this->redirectToRoute('app_collection_show', ['id' => $collection->getId()]);
    }
}
