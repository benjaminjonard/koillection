<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Log;
use App\Form\Type\Model\SearchHistoryType;
use App\Model\Search\SearchHistory;
use App\Service\PaginatorFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HistoryController extends AbstractController
{
    /**
     * @Route({
     *     "en": "/history",
     *     "fr": "/historique"
     * }, name="", name="app_history_index", methods={"GET"})
     *
     * @param Request $request
     * @param PaginatorFactory $paginatorFactory
     * @param int $paginationItemsPerPage
     * @return Response
     */
    public function index(Request $request, PaginatorFactory $paginatorFactory, int $paginationItemsPerPage) : Response
    {
        $search = new SearchHistory($request->query->getInt('page', 1), $paginationItemsPerPage);
        $form = $this->createForm(SearchHistoryType::class, $search, [
            'method' => 'GET',
        ]);
        $form->handleRequest($request);

        $count = $this->getDoctrine()->getRepository(Log::class)->countForSearch($search);
        $logs = $this->getDoctrine()->getRepository(Log::class)->findForSearch($search);

        if ($request->isXmlHttpRequest()) {
            return $this->render('App/History/_logs_table.html.twig', [
                'logs' => $logs,
                'paginator' => $paginatorFactory->generate($count)
            ]);
        }

        return $this->render('App/History/index.html.twig', [
            'form' => $form->createView(),
            'logs' => $logs,
            'paginator' => $paginatorFactory->generate($count)
        ]);
    }
}
