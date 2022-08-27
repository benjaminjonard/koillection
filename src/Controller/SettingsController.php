<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\Type\Model\SettingsType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class SettingsController extends AbstractController
{
    #[Route(path: '/settings', name: 'app_settings_index', methods: ['GET', 'POST'])]
    public function index(Request $request, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(SettingsType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.settings_updated'));

            return $this->redirectToRoute('app_settings_index');
        }

        return $this->render('App/Settings/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
