<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Datum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SignController extends AbstractController
{
    /**
     * @Route("/signs", name="app_sign_index", methods={"GET"})
     * @Route("/user/{username}/signs", name="app_user_sign_index", methods={"GET"})
     * @Route("/preview/signs", name="app_preview_sign_index", methods={"GET"})
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
