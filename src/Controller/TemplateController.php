<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Template;
use App\Enum\DatumTypeEnum;
use App\Form\Type\Entity\TemplateType;
use App\Repository\TemplateRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class TemplateController extends AbstractController
{
    #[Route(path: '/templates', name: 'app_template_index', methods: ['GET'])]
    public function index(TemplateRepository $templateRepository): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['templates']);

        return $this->render('App/Template/index.html.twig', [
            'results' => $templateRepository->findAllWithCounters(),
        ]);
    }

    #[Route(path: '/templates/add', name: 'app_template_add', methods: ['GET', 'POST'])]
    public function add(Request $request, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['templates']);

        $template = new Template();
        $form = $this->createForm(TemplateType::class, $template);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->persist($template);
            $managerRegistry->getManager()->flush();

            $this->addFlash('notice', $translator->trans('message.template_added', ['template' => $template->getName()]));

            return $this->redirectToRoute('app_template_show', ['id' => $template->getId()]);
        }

        return $this->render('App/Template/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/templates/{id}/edit', name: 'app_template_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity(expr: 'repository.findById(id)')] Template $template,
        TranslatorInterface $translator,
        ManagerRegistry $managerRegistry
    ): Response {
        $this->denyAccessUnlessFeaturesEnabled(['templates']);

        $form = $this->createForm(TemplateType::class, $template);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.template_edited', ['template' => $template->getName()]));

            return $this->redirectToRoute('app_template_show', ['id' => $template->getId()]);
        }

        return $this->render('App/Template/edit.html.twig', [
            'form' => $form->createView(),
            'template' => $template,
        ]);
    }

    #[Route(path: '/templates/{id}', name: 'app_template_show', methods: ['GET'])]
    public function show(
        #[MapEntity(expr: 'repository.findWithItems(id)')] Template $template
    ): Response {
        $this->denyAccessUnlessFeaturesEnabled(['templates']);

        return $this->render('App/Template/show.html.twig', [
            'template' => $template,
        ]);
    }

    #[Route(path: '/templates/{id}/delete', name: 'app_template_delete', methods: ['POST'])]
    public function delete(Request $request, Template $template, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['templates']);

        $form = $this->createDeleteForm('app_template_delete', $template);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->remove($template);
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.template_deleted', ['template' => $template->getName()]));
        }

        return $this->redirectToRoute('app_template_index');
    }

    #[Route(path: '/templates/{id}/fields', name: 'app_template_fields', methods: ['GET'])]
    public function getFields(Template $template): JsonResponse
    {
        $this->denyAccessUnlessFeaturesEnabled(['templates']);

        $fields = [];
        foreach ($template->getFields() as $i => $field) {
            $fields[$i][] = \in_array($field->getType(), [DatumTypeEnum::TYPE_IMAGE, DatumTypeEnum::TYPE_SIGN]) ? 'image' : 'text';
            $fields[$i][] = $field->getName();
            $fields[$i][] = $this->render('App/Datum/_datum.html.twig', [
                'entity' => '__entity_placeholder__',
                'iteration' => '__placeholder__',
                'type' => $field->getType(),
                'label' => $field->getName(),
                'choiceList' => $field->getChoiceList(),
                'template' => $template,
            ])->getContent();
        }

        return new JsonResponse($fields);
    }
}
