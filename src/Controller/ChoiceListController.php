<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ChoiceList;
use App\Entity\Template;
use App\Form\Type\Entity\ChoiceListType;
use App\Repository\ChoiceListRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChoiceListController extends AbstractController
{
    #[Route(
        path: ['en' => '/choice-lists', 'fr' => '/listes-de-choix'],
        name: 'app_choice_list_index',
        methods: ['GET']
    )]
    public function index(ChoiceListRepository $choiceListRepository): Response
    {
        return $this->render('App/ChoiceList/index.html.twig', [
            'choiceLists' => $choiceListRepository->findAll(),
        ]);
    }

    #[Route(
        path: ['en' => '/choice-lists/add', 'fr' => '/listes-de-choix/ajouter'],
        name: 'app_choice_list_add',
        methods: ['GET', 'POST']
    )]
    public function add(Request $request, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $choiceList = new ChoiceList();
        $form = $this->createForm(ChoiceListType::class, $choiceList);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->persist($choiceList);
            $managerRegistry->getManager()->flush();

            $this->addFlash('notice', $translator->trans('message.choice_list_added', ['%list%' => '&nbsp;<strong>'.$choiceList->getName().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_choice_list_show', ['id' => $choiceList->getId()]);
        }

        return $this->render('App/ChoiceList/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(
        path: ['en' => '/choice-lists/{id}/edit', 'fr' => '/listes-de-choix/{id}/editer'],
        name: 'app_choice_list_edit',
        requirements: ['id' => '%uuid_regex%'],
        methods: ['GET', 'POST']
    )]
    #[Entity('template', expr: 'repository.findById(id)', class: Template::class)]
    public function edit(Request $request, ChoiceList $choiceList, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['templates']);

        $form = $this->createForm(ChoiceListType::class, $choiceList);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.template_edited', ['%list%' => '&nbsp;<strong>'.$choiceList->getName().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_choice_list_show', ['id' => $choiceList->getId()]);
        }

        return $this->render('App/ChoiceList/edit.html.twig', [
            'form' => $form->createView(),
            'choiceList' => $choiceList,
        ]);
    }

    #[Route(
        path: ['en' => '/choice-lists/{id}/delete', 'fr' => '/listes-de-choix/{id}/supprimer'],
        name: 'app_template_delete',
        requirements: ['id' => '%uuid_regex%'],
        methods: ['POST']
    )]
    public function delete(Request $request, ChoiceList $choiceList, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $form = $this->createDeleteForm('app_choice_list_delete', $choiceList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->remove($choiceList);
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.choice_list_deleted', ['%template%' => '&nbsp;<strong>'.$choiceList->getName().'</strong>&nbsp;']));
        }

        return $this->redirectToRoute('app_choice_list_index');
    }
}
