<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Template;
use App\Enum\DatumTypeEnum;
use App\Form\Type\Entity\TemplateType;
use App\Repository\TemplateRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class TemplateController extends AbstractController
{
    #[Route(
        path: ['en' => '/templates', 'fr' => '/modeles'],
        name: 'app_template_index', methods: ['GET']
    )]
    public function index(TemplateRepository $templateRepository) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['templates']);

        return $this->render('App/Template/index.html.twig', [
            'results' => $templateRepository->findAllWithCounters(),
        ]);
    }

    #[Route(
        path: ['en' => '/templates/add', 'fr' => '/modeles/ajouter'],
        name: 'app_template_add', methods: ['GET', 'POST']
    )]
    public function add(Request $request, TranslatorInterface $translator, ManagerRegistry $managerRegistry) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['templates']);

        $template = new Template();
        $form = $this->createForm(TemplateType::class, $template);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->persist($template);
            $managerRegistry->getManager()->flush();

            $this->addFlash('notice', $translator->trans('message.template_added', ['%template%' => '&nbsp;<strong>'.$template->getName().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_template_show', ['id' => $template->getId()]);
        }

        return $this->render('App/Template/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(
        path: ['en' => '/templates/{id}/edit', 'fr' => '/modeles/{id}/editer'],
        name: 'app_template_edit', requirements: ['id' => '%uuid_regex%'], methods: ['GET', 'POST']
    )]
    #[Entity('template', expr: 'repository.findById(id)', class: Template::class)]
    public function edit(Request $request, Template $template, TranslatorInterface $translator, ManagerRegistry $managerRegistry) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['templates']);

        $form = $this->createForm(TemplateType::class, $template);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.template_edited', ['%template%' => '&nbsp;<strong>'.$template->getName().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_template_show', ['id' => $template->getId()]);
        }

        return $this->render('App/Template/edit.html.twig', [
            'form' => $form->createView(),
            'template' => $template,
        ]);
    }

    #[Route(
        path: ['en' => '/templates/{id}', 'fr' => '/modeles/{id}'],
        name: 'app_template_show', requirements: ['id' => '%uuid_regex%'], methods: ['GET']
    )]
    #[Entity('template', expr: 'repository.findWithItems(id)', class: Template::class)]
    public function show(Template $template) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['templates']);

        return $this->render('App/Template/show.html.twig', [
            'template' => $template,
        ]);
    }

    #[Route(
        path: ['en' => '/templates/{id}/delete', 'fr' => '/modeles/{id}/supprimer'],
        name: 'app_template_delete', requirements: ['id' => '%uuid_regex%'], methods: ['POST']
    )]
    public function delete(Request $request, Template $template, TranslatorInterface $translator, ManagerRegistry $managerRegistry) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['templates']);

        $form = $this->createDeleteForm('app_template_delete', $template);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->remove($template);
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.template_deleted', ['%template%' => '&nbsp;<strong>'.$template->getName().'</strong>&nbsp;']));
        }

        return $this->redirectToRoute('app_template_index');
    }

    #[Route(
        path: ['en' => '/templates/{id}/fields', 'fr' => '/modeles/{id}/champs'],
        name: 'app_template_fields', requirements: ['id' => '%uuid_regex%'], methods: ['GET']
    )]
    public function getFields(Template $template) : JsonResponse
    {
        $this->denyAccessUnlessFeaturesEnabled(['templates']);

        $fields = [];
        foreach ($template->getFields() as $i => $field) {
            $fields[$i][] = in_array($field->getType(), [DatumTypeEnum::TYPE_IMAGE, DatumTypeEnum::TYPE_SIGN]) ? 'image' : 'text';
            $fields[$i][] = $field->getName();
            $fields[$i][] = $this->render('App/Datum/_datum.html.twig', [
                'entity' => '__entity_placeholder__',
                'iteration' => '__placeholder__',
                'type' => $field->getType(),
                'label' => $field->getName(),
                'template' => $template,
            ])->getContent();
        }

        return new JsonResponse($fields);
    }
}
