<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\DatumRepository;
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
    public function index(DatumRepository $datumRepository) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['signs']);

        return $this->render('App/Sign/index.html.twig', [
            'signs' => $datumRepository->findSigns(),
        ]);
    }
}
