<?php

namespace App\Controller;

use App\Entity\Collection;
use App\Entity\Item;
use App\Entity\Tag;
use App\Form\Type\Model\SearchType;
use App\Model\Search;
use App\Service\CounterCalculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SearchController
 *
 * @package App\Controller
 *
 * @Route("/search")
 */
class SearchController extends AbstractController
{
    /**
     * @Route("", name="app_search_index", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param CounterCalculator $counterCalculator
     * @return Response
     */
    public function index(Request $request, CounterCalculator $counterCalculator) : Response
    {
        $params = [];
        $params['collections'] = $params['items'] = $params['tags'] = [];

        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);
        $form->handleRequest($request);

        if (!isset($request->request->get('search')['submit'])) {
            $search
                ->setSearchInCollections(true)
                ->setSearchInItems(true)
                ->setSearchInTags(true)
            ;
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if (true === $search->getSearchInCollections()) {
                $params['collections'] = $em->getRepository(Collection::class)->findForSearch($search);
                $params['counters'] = $counterCalculator->collectionsCounters($params['collections']);
            }
            if (true === $search->getSearchInItems()) {
                $params['items'] = $em->getRepository(Item::class)->findForSearch($search);
            }
            if (true === $search->getSearchInTags()) {
                $params['tags'] = $em->getRepository(Tag::class)->findForSearch($this->getUser(), $search);
            }
        }

        $form = $this->createForm(SearchType::class, $search);
        $params['form'] = $form->createView();

        return $this->render('App/Search/index.html.twig', $params);
    }
}
