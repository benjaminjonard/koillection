<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Form\Type\Model\ConfigurationAdminType;
use App\Service\ConfigurationHelper;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted('ROLE_ADMIN')]
class ConfigurationController extends AbstractController
{
    #[Route(path: '/admin/configuration', name: 'app_admin_configuration_index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        TranslatorInterface $translator,
        ConfigurationHelper $configurationHelper,
        ManagerRegistry $managerRegistry
    ): Response {
        $form = $this->createForm(ConfigurationAdminType::class, $configurationHelper->getAdminConfiguration());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->flush();
            $configurationHelper->clearCache();

            $this->addFlash('notice', $translator->trans('message.configuration_updated'));

            return $this->redirectToRoute('app_admin_configuration_index');
        }

        return $this->render('App/Admin/Configuration/index.html.twig', [
            'form' => $form
        ]);
    }
}
