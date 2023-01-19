<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ChoiceList;
use App\Form\Type\Entity\ChoiceListType;
use App\Repository\ChoiceListRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChoiceListController extends AbstractController
{
    #[Route(path: '/choice-lists', name: 'app_choice_list_index', methods: ['GET'])]
    public function index(ChoiceListRepository $choiceListRepository): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['templates']);

        return $this->render('App/ChoiceList/index.html.twig', [
            'choiceLists' => $choiceListRepository->findAll(),
        ]);
    }

    #[Route(path: '/choice-lists/add', name: 'app_choice_list_add', methods: ['GET', 'POST'])]
    public function add(Request $request, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['templates']);

        $choiceList = new ChoiceList();
        $form = $this->createForm(ChoiceListType::class, $choiceList);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->persist($choiceList);
            $managerRegistry->getManager()->flush();

            $this->addFlash('notice', $translator->trans('message.choice_list_added', ['list' => '&nbsp;<strong>'.$choiceList->getName().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_choice_list_index');
        }

        return $this->render('App/ChoiceList/add.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route(path: '/choice-lists/{id}/edit', name: 'app_choice_list_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ChoiceList $choiceList, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['templates']);

        $form = $this->createForm(ChoiceListType::class, $choiceList);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.choice_list_edited', ['list' => '&nbsp;<strong>'.$choiceList->getName().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_choice_list_index');
        }

        return $this->render('App/ChoiceList/edit.html.twig', [
            'form' => $form,
            'choiceList' => $choiceList,
        ]);
    }

    #[Route(path: '/choice-lists/{id}/delete', name: 'app_choice_list_delete', methods: ['POST'])]
    public function delete(Request $request, ChoiceList $choiceList, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['templates']);

        $form = $this->createDeleteForm('app_choice_list_delete', $choiceList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->remove($choiceList);
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.choice_list_deleted', ['list' => '&nbsp;<strong>'.$choiceList->getName().'</strong>&nbsp;']));
        }

        return $this->redirectToRoute('app_choice_list_index');
    }
}
