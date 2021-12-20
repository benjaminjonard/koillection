<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\Type\Model\ProfileType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProfileController extends AbstractController
{
    #[Route(
        path: ['en' => '/profile', 'fr' => '/profil'],
        name: 'app_profile_index', methods: ['GET', 'POST']
    )]
    public function index(Request $request, TranslatorInterface $translator, ManagerRegistry $managerRegistry) : Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.profile_updated'));

            return $this->redirectToRoute('app_profile_index');
        }

        return $this->render('App/Profile/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
