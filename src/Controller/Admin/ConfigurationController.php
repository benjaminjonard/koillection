<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Enum\ConfigurationEnum;
use App\Form\Type\Entity\Admin\ConfigurationType;
use App\Repository\ConfigurationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted('ROLE_ADMIN')]
class ConfigurationController extends AbstractController
{
    #[Route(path: '/admin/configuration', name: 'app_admin_configuration_index', methods: ['GET', 'POST'])]
    public function index(Request $request, TranslatorInterface $translator, ConfigurationRepository $configurationRepository, ManagerRegistry $managerRegistry): Response
    {
        $thumbnailsFormat = $configurationRepository->findOneBy(['label' => ConfigurationEnum::THUMBNAILS_FORMAT]);
        $form = $this->createForm(ConfigurationType::class, null, ['thumbnailsFormat' => $thumbnailsFormat->getValue()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $thumbnailsFormat->setValue($form->get('thumbnailsFormat')->getData());
            $managerRegistry->getManager()->flush();

            $this->addFlash('notice', $translator->trans('message.configuration_updated'));

            return $this->redirectToRoute('app_admin_configuration_index');
        }

        return $this->render('App/Admin/Configuration/index.html.twig', [
            'form' => $form
        ]);
    }
}
