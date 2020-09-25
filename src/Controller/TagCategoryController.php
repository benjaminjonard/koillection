<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\TagCategory;
use App\Form\Type\Entity\TagCategoryType;
use App\Service\PaginatorFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class TagCategoryController extends AbstractController
{
    /**
     * @Route({
     *     "en": "/tag-categories",
     *     "fr": "/categories-de-tag"
     * }, name="app_tag_category_index", methods={"GET"})
     *
     * @param Request $request
     * @param PaginatorFactory $paginatorFactory
     * @return Response
     */
    public function index(Request $request, PaginatorFactory $paginatorFactory) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['tags']);

        $page = $request->query->get('page', 1);
        $search = $request->query->get('search', null);
        $categoriesCount = $this->getDoctrine()->getRepository(TagCategory::class)->count([]);

        return $this->render('App/TagCategory/index.html.twig', [
            'categories' => $this->getDoctrine()->getRepository(TagCategory::class)->findBy([], [], 10, ($page - 1) * 10),
            'search' => $search,
            'categoriesCount' => $categoriesCount,
            'paginator' => $paginatorFactory->generate($categoriesCount)
        ]);
    }

    /**
     * @Route({
     *     "en": "/tag-categories/{id}",
     *     "fr": "/categories-de-tag/{id}"
     * }, name="app_tag_category_show", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     *
     * @Entity("tag", expr="repository.findOneWithTags(id)")
     *
     * @param TagCategory $category
     * @return Response
     */
    public function show(TagCategory $category) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['tags']);

        return $this->render('App/TagCategory/show.html.twig', [
            'category' => $category
        ]);
    }

    /**
     * @Route({
     *     "en": "/tag-categories/add",
     *     "fr": "/categories-de-tag/ajouter"
     * }, name="app_tag_category_add", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function add(Request $request, TranslatorInterface $translator) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['tags']);

        $category = new TagCategory();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(TagCategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();

            $this->addFlash('notice', $translator->trans('message.tag_category_added', ['%category%' => '&nbsp;<strong>'.$category->getLabel().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_tag_category_index');
        }

        return $this->render('App/TagCategory/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route({
     *     "en": "/tag-categories/{id}/edit",
     *     "fr": "/categories-de-tag/{id}/editer"
     * }, name="app_tag_category_edit", requirements={"id"="%uuid_regex%"}, methods={"GET", "POST"})
     *
     * @param Request $request
     * @param TagCategory $category
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function edit(Request $request, TagCategory $category, TranslatorInterface $translator) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['tags']);

        $form = $this->createForm(TagCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.tag_category_edited', ['%category%' => '&nbsp;<strong>'.$category->getLabel().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_tag_category_show', ['id' => $category->getId()]);
        }

        return $this->render('App/TagCategory/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    /**
     * @Route({
     *     "en": "/tag-categories/{id}/delete",
     *     "fr": "/categories-de-tag/{id}/supprimer"
     * }, name="app_tag_category_delete", requirements={"id"="%uuid_regex%"}, methods={"GET", "POST"})
     *
     * @param TagCategory $category
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function delete(TagCategory $category, TranslatorInterface $translator) : Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['tags']);

        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();

        $this->addFlash('notice', $translator->trans('message.tag_category_deleted', ['%category%' => '&nbsp;<strong>'.$category->getLabel().'</strong>&nbsp;']));

        return $this->redirectToRoute('app_tag_category_index');
    }
}
