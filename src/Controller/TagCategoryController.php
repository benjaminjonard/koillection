<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\TagCategory;
use App\Form\Type\Entity\TagCategoryType;
use App\Repository\TagCategoryRepository;
use App\Service\PaginatorFactory;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class TagCategoryController extends AbstractController
{
    #[Route(path: '/tag-categories', name: 'app_tag_category_index', methods: ['GET'])]
    public function index(Request $request, PaginatorFactory $paginatorFactory, TagCategoryRepository $tagCategoryRepository): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['tags']);

        $page = $request->query->get('page', 1);
        $search = $request->query->get('search', null);
        $categoriesCount = $tagCategoryRepository->count([]);

        return $this->render('App/TagCategory/index.html.twig', [
            'categories' => $tagCategoryRepository->findBy([], [], 10, ($page - 1) * 10),
            'search' => $search,
            'categoriesCount' => $categoriesCount,
            'paginator' => $paginatorFactory->generate($categoriesCount),
        ]);
    }

    #[Route(path: '/tag-categories/{id}', name: 'app_tag_category_show', methods: ['GET'])]
    #[Entity('category', expr: 'repository.findOneWithTags(id)', class: TagCategory::class)]
    public function show(TagCategory $category): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['tags']);

        return $this->render('App/TagCategory/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route(path: '/tag-categories/add', name: 'app_tag_category_add', methods: ['GET', 'POST'])]
    public function add(Request $request, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['tags']);

        $category = new TagCategory();
        $form = $this->createForm(TagCategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->persist($category);
            $managerRegistry->getManager()->flush();

            $this->addFlash('notice', $translator->trans('message.tag_category_added', ['%category%' => '&nbsp;<strong>'.$category->getLabel().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_tag_category_index');
        }

        return $this->render('App/TagCategory/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/tag-categories/{id}/edit', name: 'app_tag_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TagCategory $category, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['tags']);

        $form = $this->createForm(TagCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.tag_category_edited', ['%category%' => '&nbsp;<strong>'.$category->getLabel().'</strong>&nbsp;']));

            return $this->redirectToRoute('app_tag_category_show', ['id' => $category->getId()]);
        }

        return $this->render('App/TagCategory/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    #[Route(path: '/tag-categories/{id}/delete', name: 'app_tag_category_delete', methods: ['POST'])]
    public function delete(Request $request, TagCategory $category, TranslatorInterface $translator, ManagerRegistry $managerRegistry): Response
    {
        $this->denyAccessUnlessFeaturesEnabled(['tags']);

        $form = $this->createDeleteForm('app_tag_category_delete', $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $managerRegistry->getManager()->remove($category);
            $managerRegistry->getManager()->flush();
            $this->addFlash('notice', $translator->trans('message.tag_category_deleted', ['%category%' => '&nbsp;<strong>'.$category->getLabel().'</strong>&nbsp;']));
        }

        return $this->redirectToRoute('app_tag_category_index');
    }
}
