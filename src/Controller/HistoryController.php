<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\Type\Model\SearchHistoryType;
use App\Model\Search\SearchHistory;
use App\Repository\LogRepository;
use App\Service\PaginatorFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HistoryController extends AbstractController
{
    #[Route(path: '/history', name: 'app_history_index', methods: ['GET'])]
    public function index(Request $request, PaginatorFactory $paginatorFactory, LogRepository $logRepository): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['history']);

        $search = new SearchHistory($request->query->getInt('page', 1), 15);
        $form = $this->createForm(SearchHistoryType::class, $search, [
            'method' => 'GET',
        ]);
        $form->handleRequest($request);

        $count = $logRepository->countForSearch($search);
        $logs = $logRepository->findForSearch($search);

        if ($request->isXmlHttpRequest()) {
            return $this->render('App/History/_logs_table.html.twig', [
                'logs' => $logs,
                'paginator' => $paginatorFactory->generate($count),
            ]);
        }

        return $this->render('App/History/index.html.twig', [
            'form' => $form->createView(),
            'logs' => $logs,
            'paginator' => $paginatorFactory->generate($count),
        ]);
    }
}
