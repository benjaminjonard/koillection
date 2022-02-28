<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\CsvResponse;
use App\Http\FileResponse;
use App\Repository\CollectionRepository;
use App\Repository\InventoryRepository;
use App\Service\DatabaseDumper;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use ZipStream\Option\Archive;
use ZipStream\ZipStream;

class ToolsController extends AbstractController
{
    #[Route(
        path: ['en' => '/tools', 'fr' => '/outils'],
        name: 'app_tools_index',
        methods: ['GET']
    )]
    public function index(InventoryRepository $inventoryRepository): Response
    {
        return $this->render('App/Tools/index.html.twig', [
            'inventories' => $inventoryRepository->findAll()
        ]);
    }

    #[Route(
        path: ['en' => '/tools/export/printable-list', 'fr' => '/outils/export/liste-imprimable'],
        name: 'app_tools_export_printable_list',
        methods: ['GET']
    )]
    public function exportPrintableList(CollectionRepository $collectionRepository): Response
    {
        $collections = $collectionRepository->findAllWithItems();

        return $this->render('App/Tools/printable_list.html.twig', [
            'collections' => $collections,
            'user' => $this->getUser()
        ]);
    }

    #[Route(
        path: ['en' => '/tools/export/csv', 'fr' => '/outils/export/csv'],
        name: 'app_tools_export_csv',
        methods: ['GET']
    )]
    public function exportCsv(CollectionRepository $collectionRepository): CsvResponse
    {
        $collections = $collectionRepository->findAllWithItems();

        $rows = [];
        foreach ($collections as $collection) {
            foreach ($collection->getItems() as $item) {
                $rows[] = [$item->getId(), $item->getName(), $collection->getTitle()];
            }
        }

        return new CsvResponse($rows, (new \DateTime())->format('YmdHis') . '-koillection-export.csv');
    }

    #[Route(
        path: ['en' => '/tools/export/sql', 'fr' => '/outils/export/sql'],
        name: 'app_tools_export_sql',
        methods: ['GET']
    )]
    public function exportSql(DatabaseDumper $databaseDumper): FileResponse
    {
        return new FileResponse($databaseDumper->dump(), (new \DateTime())->format('YmdHis') . '-koillection-export.sql');
    }

    #[Route(
        path: ['en' => '/tools/export/images', 'fr' => '/outils/export/images'],
        name: 'app_tools_export_images',
        methods: ['GET']
    )]
    public function exportImages(): StreamedResponse
    {
        return new StreamedResponse(function () {
            $options = new Archive();
            $options->setContentType('text/event-stream');
            $options->setFlushOutput(true);
            $options->setSendHttpHeaders(true);

            $zipFilename = (new \DateTime())->format('YmdHis') . '-koillection-images.zip';
            $zip = new ZipStream($zipFilename, $options);

            $path = $this->getParameter('kernel.project_dir').'/public/uploads/'. $this->getUser()->getId();
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::LEAVES_ONLY);
            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $zip->addFileFromStream($file->getFilename(), fopen($file->getRealPath(), 'r'));
                }
            }

            $zip->finish();
        });
    }
}
