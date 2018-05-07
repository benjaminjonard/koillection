<?php
namespace App\Controller;

use App\Entity\Log;
use App\Enum\LogTypeEnum;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @Route("", name="app_history_index")
     * @Method({"GET"})
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request) : Response
    {
        $page = $request->query->get('page', 1);
        $classes = array_map(
            function($type) { return 'App\Entity\\'.ucfirst($type); },
            $request->query->get('types', [])
        );

        $count = $this->getDoctrine()->getRepository(Log::class)->count([
            'type' => [LogTypeEnum::TYPE_CREATED, LogTypeEnum::TYPE_DELETED],
            'objectClass' => $classes
        ]);

        return $this->render('App/History/index.html.twig', [
            'logs' => $this->getDoctrine()->getRepository(Log::class)->findBy([
                'user' => $this->getUser(),
                'type' => [LogTypeEnum::TYPE_CREATED, LogTypeEnum::TYPE_DELETED],
                'objectClass' => $classes
            ], [
                'loggedAt' => 'DESC'
            ], 10, ($page - 1) * 10),
            'currentPage' => $page,
            'count' => $count
        ]);
    }
}
