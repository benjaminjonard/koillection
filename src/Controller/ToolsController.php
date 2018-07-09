<?php

namespace App\Controller;

use App\Entity\Collection;
use App\Entity\User;
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
     * @Route("/tools/export", name="app_tools_export")
     * @Method({"GET"})
     *
     * @return Response
     */
    public function export() : Response
    {
        $collections = $this->getDoctrine()->getRepository(Collection::class)->findAllWithItems();

        return $this->render('App/Tools/export.html.twig', [
            'collections' => $collections,
            'user' => $this->getUser()
        ]);
    }
}
