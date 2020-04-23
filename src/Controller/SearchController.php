<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Collection;
use App\Entity\Item;
use App\Entity\Tag;
use App\Form\Type\Model\SearchType;
use App\Model\Search\Search;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @Route({
     *     "en": "/search",
     *     "fr": "/recherche"
     * }, name="app_search_index", methods={"GET", "POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request) : Response
    {
        $results = [];

        $search = new Search();
        $form = $this->createForm(SearchType::class, $search, [
            'method' => 'GET',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if (true === $search->getSearchInCollections()) {
                $collections = $em->getRepository(Collection::class)->findForSearch($search);
                if (!empty($collections)) {
                    $results['collections'] = $collections;
                }
            }
            if (true === $search->getSearchInItems()) {
                $items = $em->getRepository(Item::class)->findForSearch($search);
                if (!empty($items)) {
                    $results['items'] = $items;
                }
            }
            if (true === $search->getSearchInTags()) {
                $tags = $em->getRepository(Tag::class)->findForSearch($search);
                if (!empty($tags)) {
                    $results['tags'] = $tags;
                }
            }
        }

        $form = $this->createForm(SearchType::class, $search);

        return $this->render('App/Search/index.html.twig', [
            'form' => $form->createView(),
            'results' => $results
        ]);
    }
}
