<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Template;
use App\Enum\DatumTypeEnum;
use App\Form\Type\Entity\TemplateType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TemplateController
 *
 * @package App\Controller
 *
 * @Route("/templates")
 */
class TemplateController extends AbstractController
{
    /**
     * @Route("", name="app_template_index", methods={"GET"})
     *
     * @return Response
     */
    public function index() : Response
    {
        return $this->render('App/Template/index.html.twig', [
            'results' => $this->getDoctrine()->getRepository(Template::class)->findAllWithCounters(),
        ]);
    }

    /**
     * @Route("/add", name="app_template_add", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function add(Request $request, TranslatorInterface $translator) : Response
    {
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

    /**
     * @Route("/{id}/edit", name="app_template_edit", requirements={"id"="%uuid_regex%"}, methods={"GET", "POST"})
     * @Entity("template", expr="repository.findById(id)")
     *
     * @param Request $request
     * @param Template $template
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function edit(Request $request, Template $template, TranslatorInterface $translator) : Response
    {
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

    /**
     * @Route("/{id}", name="app_template_show", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     * @Entity("template", expr="repository.findByIdWithItems(id)")
     *
     * @param Template $template
     * @return Response
     */
    public function show(Template $template) : Response
    {
        return $this->render('App/Template/show.html.twig', [
            'template' => $template,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="app_template_delete", requirements={"id"="%uuid_regex%"}, methods={"GET", "POST"})
     *
     * @param Template $template
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function delete(Template $template, TranslatorInterface $translator) : Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($template);
        $em->flush();

        $this->addFlash('notice', $translator->trans('message.template_deleted', ['%template%' => '&nbsp;<strong>'.$template->getName().'</strong>&nbsp;']));

        return $this->redirectToRoute('app_template_index');
    }

    /**
     * @Route("/{id}/fields", name="app_template_fields", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     *
     * @param Template $template
     * @return JsonResponse
     */
    public function getFields(Template $template) : JsonResponse
    {
        $fields = [];
        foreach ($template->getFields() as $field) {
            $data = [];
            $data['type'] = $field->getType();
            $data['html'] = $fields[$field->getName()] = $this->render('App/Datum/datum.html.twig', [
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
