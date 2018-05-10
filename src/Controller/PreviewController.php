<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Collection;
use App\Entity\Datum;
use App\Entity\Item;
use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Wishlist;
use App\Repository\TagRepository;
use App\Service\CounterCalculator;
use App\Service\Graph\CalendarBuilder;
use App\Service\Graph\ChartBuilder;
use App\Service\Graph\TreeBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PreviewController
 *
 * @package App\Controller
 *
 * @Route("/preview")
 */
class PreviewController extends AbstractController
{
    /**
     * @Route("/item/{id}", name="app_preview_item", requirements={"id"="%uuid_regex%"})
     * @Method({"GET"})
     * @Entity("item", expr="repository.findById(id)")
     *
     * @param Item $item
     * @return Response
     */
    public function item(Item $item) : Response
    {
        $nextAndPrevious = $this->getDoctrine()->getRepository(Item::class)->findNextAndPrevious($item);

        return $this->render('App/Preview/item.html.twig', [
            'item' => $item,
            'previousItem' => $nextAndPrevious['previous'],
            'nextItem' => $nextAndPrevious['next']
        ]);
    }

    /**
     * @Route("/signs", name="app_preview_signs")
     * @Method({"GET"})
     *
     * @return Response
     */
    public function signs() : Response
    {
        return $this->render('App/Preview/signs.html.twig', [
            'signs' => $this->getDoctrine()->getRepository(Datum::class)->findSigns()
        ]);
    }

    /**
     * @Route("/statistics", name="app_preview_statistics")
     * @Method({"GET"})
     *
     * @param TreeBuilder $treeBuilder
     * @param ChartBuilder $chartBuilder
     * @param CalendarBuilder $calendarBuilder
     * @return Response
     */
    public function statistics(TreeBuilder $treeBuilder, ChartBuilder $chartBuilder, CalendarBuilder $calendarBuilder) : Response
    {
        return $this->render('App/Preview/statistics.html.twig', [
            'counters' => $this->getDoctrine()->getRepository(User::class)->getCounters($this->getUser()),
            'treeJson' => json_encode($treeBuilder->buildCollectionTree()),
            'calendarJson' => json_encode($calendarBuilder->buildItemCalendar($this->getUser())),
            'hoursChartJson' => json_encode($chartBuilder->buildActivityByHour($this->getUser())),
            'monthsChartJson' => json_encode($chartBuilder->buildActivityByMonth($this->getUser())),
            'monthDaysChartJson' => json_encode($chartBuilder->buildActivityByMonthDay($this->getUser())),
            'weekDaysChartJson' => json_encode($chartBuilder->buildActivityByWeekDay($this->getUser()))
        ]);
    }

    /**
     * @Route("/albums", name="app_preview_albums")
     * @Method({"GET"})
     *
     * @return Response
     */
    public function albums() : Response
    {
        $albums = $this->getDoctrine()->getRepository(Album::class)->findAll();
        $photosCounter = 0;
        foreach ($albums as $album) {
            $photosCounter += \count($album->getPhotos());
        }

        return $this->render('App/Preview/albums.html.twig', [
            'albums' => $albums,
            'photosCounter' => $photosCounter
        ]);
    }

    /**
     * @Route("/albums/{id}", name="app_preview_album", requirements={"id"="%uuid_regex%"})
     * @Method({"GET"})
     *
     * @param Album $album
     * @return Response
     */
    public function album(Album $album) : Response
    {
        return $this->render('App/Preview/album.html.twig', [
            'album' => $album,
        ]);
    }

    /**
     * @Route("/tags", name="app_preview_tags")
     * @Method({"GET"})
     *
     * @param Request $request
     * @return Response
     */
    public function tags(Request $request) : Response
    {
        $page = $request->query->get('page', 1);
        $search = $request->query->get('search', null);
        $itemsCount = $this->getDoctrine()->getRepository(Item::class)->count([]);
        $tagsCount = $this->getDoctrine()->getRepository(Tag::class)->countTags($search, true);

        return $this->render('App/Preview/tags.html.twig', [
            'results' => $this->getDoctrine()->getRepository(Tag::class)->countItemsByTag($itemsCount, $page, $search, true),
            'search' => $search,
            'tagsCount' => $tagsCount,
            'currentPage' => $page,
        ]);
    }

    /**
     * @Route("/tags/{id}", name="app_preview_tag", requirements={"id"="%uuid_regex%"})
     * @Method({"GET"})
     * @Entity("tag", expr="repository.findById(id)")
     *
     * @param Tag $tag
     * @return Response
     */
    public function tag(Tag $tag) : Response
    {
        return $this->render('App/Preview/tag.html.twig', [
            'tag' => $tag,
            'relatedTags' => $this->getDoctrine()->getRepository(Tag::class)->findRelatedTags($tag)
        ]);
    }

    /**
     * @Route("/wishlists", name="app_preview_wishlists")
     * @Method({"GET"})
     *
     * @param CounterCalculator $counterCalculator
     * @return Response
     */
    public function wishlists(CounterCalculator $counterCalculator) : Response
    {
        $wishlists = $this->getDoctrine()->getRepository(Wishlist::class)->findAllParent();

        return $this->render('App/Preview/wishlists.html.twig', [
            'wishlists' => $wishlists,
            'counters' => $counterCalculator->wishlistsCounters($wishlists)
        ]);
    }

    /**
     * @Route("/wishlists/{id}", name="app_preview_wishlist", requirements={"id"="%uuid_regex%"})
     * @Method({"GET"})
     * @Entity("wishlist", expr="repository.findById(id)")
     *
     * @param Wishlist $wishlist
     * @param CounterCalculator $counterCalculator
     * @return Response
     */
    public function wishlist(Wishlist $wishlist, CounterCalculator $counterCalculator) : Response
    {
        return $this->render('App/Preview/wishlist.html.twig', [
            'wishlist' => $wishlist,
            'counters' => $counterCalculator->wishlistCounters($wishlist)
        ]);
    }

    /**
     * @Route("/{id}/items", name="app_preview_items", requirements={"id"="%uuid_regex%"})
     * @Method({"GET"})
     *
     * @param Collection $collection
     * @return Response
     */
    public function items(Collection $collection) : Response
    {
        return $this->render('App/Preview/items.html.twig', [
            'collection' => $collection,
            'items' => $this->getDoctrine()->getRepository(Item::class)->findAllByCollection($collection),
        ]);
    }

    /**
     * @Route("/{id}", name="app_preview_collection", requirements={"id"="%uuid_regex%"})
     * @Method({"GET"})
     * @Entity("collection", expr="repository.findById(id)")
     *
     * @param Collection $collection
     * @param CounterCalculator $counterCalculator
     * @return Response
     */
    public function collection(Collection $collection, CounterCalculator $counterCalculator) : Response
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('App/Preview/collection.html.twig', [
            'collection' => $collection,
            'children' => $em->getRepository(Collection::class)->findChildrenByCollectionId($collection->getId()),
            'items' => $em->getRepository(Item::class)->findItemsByCollectionId($collection->getId()),
            'counters' => $counterCalculator->collectionCounters($collection)
        ]);
    }
}
