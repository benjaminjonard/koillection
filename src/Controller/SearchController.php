<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\Type\Model\SearchType;
use App\Model\Search\Search;
use App\Repository\AlbumRepository;
use App\Repository\CollectionRepository;
use App\Repository\ItemRepository;
use App\Repository\TagRepository;
use App\Repository\WishlistRepository;
use App\Service\Autocompleter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route(
        path: ['en' => '/search', 'fr' => '/recherche'],
        name: 'app_search_index', methods: ['GET', 'POST']
    )]
    public function index(Request $request, CollectionRepository $collectionRepository, ItemRepository $itemRepository,
                          TagRepository $tagRepository, AlbumRepository $albumRepository, WishlistRepository $wishlistRepository
    ) : Response
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
                $collections = $collectionRepository->findForSearch($search);
                if (!empty($collections)) {
                    $results['collections'] = $collections;
                }
            }
            if (true === $search->getSearchInItems()) {
                $items = $itemRepository->findForSearch($search);
                if (!empty($items)) {
                    $results['items'] = $items;
                }
            }
            if ($this->featureChecker->isFeatureEnabled('tags') && true === $search->getSearchInTags()) {
                $tags = $tagRepository->findForSearch($search);
                if (!empty($tags)) {
                    $results['tags'] = $tags;
                }
            }
            if ($this->featureChecker->isFeatureEnabled('albums') && true === $search->getSearchInAlbums()) {
                $albums = $albumRepository->findForSearch($search);
                if (!empty($albums)) {
                    $results['albums'] = $albums;
                }
            }
            if ($this->featureChecker->isFeatureEnabled('wishlists') && true === $search->getSearchInWishlists()) {
                $wishlists = $wishlistRepository->findForSearch($search);
                if (!empty($wishlists)) {
                    $results['wishlists'] = $wishlists;
                }
            }
        }

        $form = $this->createForm(SearchType::class, $search);

        return $this->render('App/Search/index.html.twig', [
            'form' => $form->createView(),
            'results' => $results
        ]);
    }

    #[Route(
        path: ['en' => '/search/autocomplete/{term}', 'fr' => '/recherche/autocompletion/{term}'],
        name: 'app_search_autocomplete', methods: ['GET', 'POST']
    )]
    public function autocomplete(Autocompleter $autocompleter, string $term) : Response
    {
        $results = $autocompleter->findForAutocomplete($term);

        return new JsonResponse($results);
    }
}
