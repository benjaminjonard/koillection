<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Datum;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SignController extends AbstractController
{
    #[Route(
        path: ['en' => '/signs', 'fr' => '/dedicaces'],
        name: 'app_sign_index', methods: ['GET']
    )]
    #[Route(
        path: ['en' => '/user/{username}/signs', 'fr' => '/utilisateur/{username}/dedicaces'],
        name: 'app_user_sign_index', methods: ['GET']
    )]
    #[Route(
        path: ['en' => '/preview/signs', 'fr' => '/apercu/dedicaces'],
        name: 'app_preview_sign_index', methods: ['GET']
    )]
    public function index() : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['signs']);

        return $this->render('App/Sign/index.html.twig', [
            'signs' => $this->getDoctrine()->getRepository(Datum::class)->findSigns(),
        ]);
    }
}
