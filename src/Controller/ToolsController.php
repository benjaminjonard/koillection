<?php

namespace App\Controller;

use App\Entity\Collection;
use App\Entity\User;
use App\Http\CsvResponse;
use App\Service\Graph\CalendarBuilder;
use App\Service\Graph\ChartBuilder;
use App\Service\Graph\TreeBuilder;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ToolsController
 *
 * @package App\Controller
 */
class ToolsController extends AbstractController
{
    /**
     * @Route("/tools", name="app_tools_index")
     * @Method({"GET"})
     *
     * @return Response
     */
    public function index() : Response
    {
        return $this->render('App/Tools/index.html.twig', []);
    }

    /**
     * @Route("/tools/export/printable-list", name="app_tools_export_printable_list")
     * @Method({"GET"})
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
     * @Route("/tools/export/csv", name="app_tools_export_csv")
     * @Method({"GET"})
     *
     * @return Response
     */
    public function exportCsv() : Response
    {
        $collections = $this->getDoctrine()->getRepository(Collection::class)->findAllWithItems();

        $rows = [];
        foreach ($collections as $collection) {
            foreach ($collection->getItems() as $item) {
                $rows[] = [$item->getId(), $item->getName(), $collection->getTitle()];
            }
        }

        return new CsvResponse($rows);
    }
}
