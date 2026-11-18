<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Collection;
use App\Entity\Import;
use App\Form\Type\Model\ImportMappingType;
use App\Form\Type\Model\ImportUploadType;
use App\Message\ProcessImportMessage;
use App\Model\ImportDraft;
use App\Repository\ImportRepository;
use App\Service\ImportHandler;
use App\Service\ImportStorage;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImportController extends AbstractController
{
    #[Route(path: '/collections/{id}/import', name: 'app_collection_import', methods: ['GET', 'POST'])]
    public function import(Request $request, Collection $collection, ImportHandler $importHandler, ImportStorage $importStorage): Response
    {
        $draft = new ImportDraft()->setCollection($collection);

        $form = $this->createForm(ImportUploadType::class, $draft);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $importStorage->create($draft);
            $importHandler->prefillMapping($draft);
            $importStorage->save($draft);

            return $this->redirectToRoute('app_collection_import_mapping', [
                'id' => $collection->getId(),
                'importId' => $draft->getId()
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
        $draft = $importStorage->find($importId, $collection) ?? throw $this->createNotFoundException();

        $form = $this->createForm(ImportMappingType::class, $draft);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $importStorage->save($draft);

            return $this->redirectToRoute('app_collection_import_preview', [
                'id' => $collection->getId(),
                'importId' => $draft->getId()
            ]);
        }

        return $this->render('App/Import/import-mapping.html.twig', [
            'form' => $form,
            'collection' => $collection,
            'import' => $draft,
        ]);
    }

    #[Route(path: '/collections/{id}/import/{importId}/preview', name: 'app_collection_import_preview', methods: ['GET'])]
    public function preview(Collection $collection, string $importId, ImportHandler $importHandler, ImportStorage $importStorage): Response
    {
        $draft = $importStorage->find($importId, $collection) ?? throw $this->createNotFoundException();

        $errors = $importHandler->validate($draft);

        return $this->render('App/Import/import-preview.html.twig', [
            'collection' => $collection,
            'import' => $draft,
            'errors' => $errors,
            'items' => $errors === [] ? $importHandler->createItems($draft, true) : [],
            'duplicates' => $importHandler->findDuplicateRows($draft),
        ]);
    }

    #[Route(path: '/collections/{id}/import/{importId}/image', name: 'app_collection_import_image', methods: ['GET'])]
    public function image(Request $request, Collection $collection, string $importId, ImportStorage $importStorage): Response
    {
        $draft = $importStorage->find($importId, $collection) ?? throw $this->createNotFoundException();

        $path = $draft->getArchiveEntries()[(string) $request->query->get('name')]['path'] ?? throw $this->createNotFoundException();

        return new BinaryFileResponse($path, Response::HTTP_OK, [
            'Content-Type' => mime_content_type($path),
            'X-Content-Type-Options' => 'nosniff',
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
        MessageBusInterface $messageBus,
        TranslatorInterface $translator
    ): Response {
        $draft = $importStorage->find($importId, $collection) ?? throw $this->createNotFoundException();

        if ($importHandler->validate($draft) !== []) {
            return $this->redirectToRoute('app_collection_import_preview', [
                'id' => $collection->getId(),
                'importId' => $draft->getId()
            ]);
        }

        $selectedRows = array_map(intval(...), $request->request->all('rows'));
        if ($selectedRows === []) {
            $this->addFlash('error', $translator->trans('error.import.no_rows_selected'));

            return $this->redirectToRoute('app_collection_import_preview', [
                'id' => $collection->getId(),
                'importId' => $draft->getId()
            ]);
        }

        $import = new Import()
            ->setCollection($collection)
            ->setOwner($this->getUser())
            ->setFilename($draft->getFilename())
            ->setTotalRows(\count($selectedRows))
        ;
        $managerRegistry->getManager()->persist($import);
        $managerRegistry->getManager()->flush();

        $importStorage->forget($draft);
        $messageBus->dispatch(new ProcessImportMessage($import->getId(), $draft, $selectedRows));

        $this->addFlash('notice', $translator->trans('message.import_started'));

        return $this->redirectToRoute('app_import_index');
    }

    #[Route(path: '/imports', name: 'app_import_index', methods: ['GET'])]
    public function index(ImportRepository $importRepository): Response
    {
        return $this->render('App/Import/index.html.twig', [
            'imports' => $importRepository->findBy([], ['createdAt' => 'DESC']),
        ]);
    }
}
