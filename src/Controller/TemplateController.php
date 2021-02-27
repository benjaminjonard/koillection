<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Template;
use App\Form\Type\Entity\TemplateType;
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
    public function index() : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['templates']);

        return $this->render('App/Template/index.html.twig', [
            'results' => $this->getDoctrine()->getRepository(Template::class)->findAllWithCounters(),
        ]);
    }

    #[Route(
        path: ['en' => '/templates/add', 'fr' => '/modeles/ajouter'],
        name: 'app_template_add', methods: ['GET', 'POST']
    )]
    public function add(Request $request, TranslatorInterface $translator) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['templates']);

        $template = new Template();
        $form = $this->createForm(TemplateType::class, $template);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($template);
            $em->flush();

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
    #[Entity(expr: 'repository.findById(id)', class: 'template')]
    public function edit(Request $request, Template $template, TranslatorInterface $translator) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['templates']);

        $form = $this->createForm(TemplateType::class, $template);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
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
    #[Entity(expr: 'repository.findWithItems(id)', class: 'template')]
    public function show(Template $template) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['templates']);

        return $this->render('App/Template/show.html.twig', [
            'template' => $template,
        ]);
    }

    #[Route(
        path: ['en' => '/templates/{id}/delete', 'fr' => '/modeles/{id}/supprimer'],
        name: 'app_template_delete', requirements: ['id' => '%uuid_regex%'], methods: ['GET', 'POST']
    )]
    public function delete(Template $template, TranslatorInterface $translator) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['templates']);

        $em = $this->getDoctrine()->getManager();
        $em->remove($template);
        $em->flush();

        $this->addFlash('notice', $translator->trans('message.template_deleted', ['%template%' => '&nbsp;<strong>'.$template->getName().'</strong>&nbsp;']));

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
        foreach ($template->getFields() as $field) {
            $data = [];
            $data['type'] = $field->getType();
            $data['html'] = $fields[$field->getName()] = $this->render('App/Datum/_datum.html.twig', [
                'entity' => '__entity_placeholder__',
                'iteration' => '__placeholder__',
                'type' => $field->getType(),
                'label' => $field->getName(),
                'template' => $template,
            ])->getContent();
            $fields[$field->getName()] = $data;
        }

        return new JsonResponse(['fields' => $fields]);
    }
}
