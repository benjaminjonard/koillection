<?php

namespace App\Controller;

use App\Entity\Datum;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SignController
 *
 * @package App\Controller
 *
 * @Route("/signs")
 */
class SignController extends AbstractController
{
    /**
     * @Route("", name="app_sign_index")
     * @Method({"GET"})
     *
     * @return Response
     */
    public function index() : Response
    {
        return $this->render('App/Sign/index.html.twig', [
            'signs' => $this->getDoctrine()->getRepository(Datum::class)->findSigns(),
        ]);
    }
}
