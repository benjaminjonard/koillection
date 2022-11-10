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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route(path: '/search', name: 'app_search_index', methods: ['GET', 'POST'])]
    #[Route(path: '/user/{username}/search', name: 'app_shared_search_index', methods: ['GET'])]
    public function index(
        Request $request,
        CollectionRepository $collectionRepository,
        ItemRepository $itemRepository,
        TagRepository $tagRepository,
        AlbumRepository $albumRepository,
        WishlistRepository $wishlistRepository
    ): Response {
        $results = [];

        $search = new Search();
        $form = $this->createForm(SearchType::class, $search, [
            'method' => 'GET',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $results['collections'] = $collectionRepository->findForSearch($search);
            $results['items'] = $itemRepository->findForSearch($search);

            if ($this->featureChecker->isFeatureEnabled('tags')) {
                $results['tags'] = $tagRepository->findForSearch($search);
            }

            if ($this->featureChecker->isFeatureEnabled('albums')) {
                $results['albums'] = $albumRepository->findForSearch($search);
            }

            if ($this->featureChecker->isFeatureEnabled('wishlists')) {
                $results['wishlists'] = $wishlistRepository->findForSearch($search);
            }
        }

        $form = $this->createForm(SearchType::class, $search);

        return $this->render('App/Search/index.html.twig', [
            'form' => $form->createView(),
            'results' => $results,
        ]);
    }

    #[Route(path: '/search/autocomplete/{term}', name: 'app_search_autocomplete', methods: ['GET', 'POST'])]
    #[Route(path: '/user/{username}/search/autocomplete/{term}', name: 'app_shared_search_autocomplete', methods: ['GET'])]
    public function autocomplete(Autocompleter $autocompleter, string $term): Response
    {
        $results = $autocompleter->findForAutocomplete($term);

        return new JsonResponse($results);
    }
}
