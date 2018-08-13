<?php

namespace App\Controller;

use App\Entity\Log;
use App\Enum\LogTypeEnum;
use App\Service\PaginatorFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HistoryController
 *
 * @package App\Controller
 *
 * @Route("/history")
 */
class HistoryController extends AbstractController
{
    /**
     * @Route("", name="app_history_index", methods={"GET"})
     *
     * @param Request $request
     * @param PaginatorFactory $paginatorFactory
     * @return Response
     */
    public function index(Request $request, PaginatorFactory $paginatorFactory) : Response
    {
        $page = $request->query->get('page', 1);
        $classes = array_map(
            function($type) { return 'App\Entity\\'.ucfirst($type); },
            $request->query->get('types', [])
        );

        $count = $this->getDoctrine()->getRepository(Log::class)->count([
            'type' => [LogTypeEnum::TYPE_CREATE, LogTypeEnum::TYPE_DELETE],
            'objectClass' => $classes
        ]);

        return $this->render('App/History/index.html.twig', [
            'logs' => $this->getDoctrine()->getRepository(Log::class)->findBy([
                'user' => $this->getUser(),
                'type' => [LogTypeEnum::TYPE_CREATE, LogTypeEnum::TYPE_DELETE],
                'objectClass' => $classes
            ], [
                'loggedAt' => 'DESC'
            ], 10, ($page - 1) * 10),
            'paginator' => $paginatorFactory->generate($count, 10)
        ]);
    }
}
