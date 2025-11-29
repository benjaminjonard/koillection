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

class ImportController extends AbstractController
{
    #[Route(path: '/collections/{id}/import', name: 'app_collection_import', methods: ['GET', 'POST'])]
    public function import(Request $request, Collection $collection, ManagerRegistry $managerRegistry): Response
    {
        //$this->denyAccessUnlessFeaturesEnabled(['imports']);

        $import = new Import()
            ->setCollection($collection)
            ->setOwner($this->getUser())
            ->setStatus(ImportStatusEnum::NEW)
        ;

        $form = $this->createForm(ImportUploadType::class, $import);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
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
    ): Response
    {
        //$this->denyAccessUnlessFeaturesEnabled(['imports']);

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

    #[Route(path: '/collections/{id}/import/{importId}/preview', name: 'app_collection_import_preview', methods: ['GET', 'POST'])]
    public function preview(
        Collection $collection,
        #[MapEntity(expr: 'repository.find(importId)')] Import $import,
        ImportHandler $importHandler
    ): Response
    {
        //$this->denyAccessUnlessFeaturesEnabled(['imports']);

        $items = $importHandler->createItems($import, true);

        return $this->render('App/Import/import-preview.html.twig', [
            'collection' => $collection,
            'import' => $import,
            'items' => $items
        ]);
    }

    #[Route(path: '/collections/{id}/import/{importId}/import', name: 'app_collection_import_import', methods: ['POST'])]
    public function executeImport(
        Collection $collection,
        #[MapEntity(expr: 'repository.find(importId)')] Import $import,
        ImportHandler $importHandler,
        ManagerRegistry $managerRegistry
    ): Response
    {
        //$this->denyAccessUnlessFeaturesEnabled(['imports']);

        $items = $importHandler->createItems($import, false);
        foreach ($items as $item) {
            $item->setCollection($collection);
            $managerRegistry->getManager()->persist($item);
        }

        $managerRegistry->getManager()->flush();

        return $this->redirectToRoute('app_collection_show', ['id' => $collection->getId()]);
    }
}
