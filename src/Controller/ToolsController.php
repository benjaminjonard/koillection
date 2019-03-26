<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Collection;
use App\Entity\Inventory;
use App\Http\CsvResponse;
use App\Http\FileResponse;
use App\Service\DatabaseDumper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use ZipStream\ZipStream;

/**
 * Class ToolsController
 *
 * @package App\Controller
 */
class ToolsController extends AbstractController
{
    /**
     * @Route("/tools", name="app_tools_index", methods={"GET"})
     *
     * @return Response
     */
    public function index() : Response
    {
        return $this->render('App/Tools/index.html.twig', [
            'inventories' => $this->getDoctrine()->getRepository(Inventory::class)->findAll()
        ]);
    }

    /**
     * @Route("/tools/export/printable-list", name="app_tools_export_printable_list", methods={"GET"})
     *
     * @return Response
     */
    public function exportPrintableList() : Response
    {
        $collections = $this->getDoctrine()->getRepository(Collection::class)->findAllWithItems();

        return $this->render('App/Tools/printable-list.html.twig', [
            'collections' => $collections,
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/tools/export/csv", name="app_tools_export_csv", methods={"GET"})
     *
     * @return CsvResponse
     */
    public function exportCsv() : CsvResponse
    {
        $collections = $this->getDoctrine()->getRepository(Collection::class)->findAllWithItems();

        $rows = [];
        foreach ($collections as $collection) {
            foreach ($collection->getItems() as $item) {
                $rows[] = [$item->getId(), $item->getName(), $collection->getTitle()];
            }
        }

        return new CsvResponse($rows, (new \DateTime())->format('Ymd').'-koillection-export.csv');
    }

    /**
     * @Route("/tools/export/sql", name="app_tools_export_sql", methods={"GET"})
     *
     * @param DatabaseDumper $databaseDumper
     * @return FileResponse
     * @throws \Doctrine\DBAL\DBALException
     */
    public function exportSql(DatabaseDumper $databaseDumper) : FileResponse
    {
        return new FileResponse($databaseDumper->dump(), (new \DateTime())->format('Ymd').'-koillection-export.sql');
    }

    /**
     * @Route("/tools/export/images", name="app_tools_export_images", methods={"GET"})
     *
     * @return StreamedResponse
     */
    public function exportImages() : StreamedResponse
    {
        $response = new StreamedResponse(function() {
            $zipFilename = (new \DateTime())->format('Ymd').'-koillection-export.zip';
            $path = $this->getParameter('kernel.project_dir').'/public/uploads/'.$this->getUser()->getId();

            $zip = new ZipStream($zipFilename);

            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::LEAVES_ONLY);
            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $zip->addFileFromStream($file->getFilename(), fopen($file->getRealPath(), 'r'));
                }
            }

            $zip->finish();
        });

        $response->headers->set('X-Accel-Buffering', 'no');

        return $response;
    }

}
