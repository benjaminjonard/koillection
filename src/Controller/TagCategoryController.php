<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\TagCategory;
use App\Form\Type\Entity\TagCategoryType;
use App\Service\PaginatorFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class TagCategoryController extends AbstractController
{
    /**
     * @Route("/tag-categories", name="app_tag_category_index", methods={"GET"})
     *
     * @param Request $request
     * @param PaginatorFactory $paginatorFactory
     * @return Response
     */
    public function index(Request $request, PaginatorFactory $paginatorFactory) : Response
    {
        $page = $request->query->get('page', 1);
        $search = $request->query->get('search', null);
        $categoriesCount = $this->getDoctrine()->getRepository(TagCategory::class)->count([]);

        return $this->render('App/TagCategory/index.html.twig', [
            'categories' => $this->getDoctrine()->getRepository(TagCategory::class)->findBy([], [], 10, ($page - 1) * 10),
            'search' => $search,
            'categoriesCount' => $categoriesCount,
            'paginator' => $paginatorFactory->generate($categoriesCount, 10)
        ]);
    }

    /**
     * @Route("/tag-categories/{id}", name="app_tag_category_show", requirements={"id"="%uuid_regex%"}, methods={"GET"})
     * @Entity("tag", expr="repository.findOneWithTags(id)")
     *
     * @param TagCategory $category
     * @return Response
     */
    public function show(TagCategory $category) : Response
    {
        return $this->render('App/TagCategory/show.html.twig', [
            'category' => $category
        ]);
    }

    /**
     * @Route("/tag-categories/add", name="app_tag_category_add", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function add(Request $request, TranslatorInterface $translator) : Response
    {
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
     * @Route("/tag-categories/{id}/edit", name="app_tag_category_edit", requirements={"id"="%uuid_regex%"}, methods={"GET", "POST"})
     *
     * @param Request $request
     * @param TagCategory $category
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function edit(Request $request, TagCategory $category, TranslatorInterface $translator) : Response
    {
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
     * @Route("/tag-categories/{id}/delete", name="app_tag_category_delete", requirements={"id"="%uuid_regex%"}, methods={"GET", "POST"})
     *
     * @param TagCategory $category
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function delete(TagCategory $category, TranslatorInterface $translator) : Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();

        $this->addFlash('notice', $translator->trans('message.tag_category_deleted', ['%category%' => '&nbsp;<strong>'.$category->getLabel().'</strong>&nbsp;']));

        return $this->redirectToRoute('app_tag_category_index');
    }
}
