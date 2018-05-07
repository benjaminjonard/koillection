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
 * Class UserController
 *
 * @package App\Controller
 *
 * @Route("/user/{username}")
 */
class UserController extends AbstractController
{
    /**
     * @Route("", name="app_user_collections")
     * @Method({"GET"})
     * @Entity("user", expr="repository.findOneByUsername(username)")
     *
     * @param User $user
     * @param CounterCalculator $counterCalculator
     * @return Response
     */
    public function collections(User $user, CounterCalculator $counterCalculator) : Response
    {
        $collections = $this->getDoctrine()->getRepository(Collection::class)->findAllParent();

        return $this->render('App/User/collections.html.twig', [
            'collections' => $collections,
            'counters' => $counterCalculator->collectionsCounters($collections),
            'user' => $user
        ]);
    }

    /**
     * @Route("/albums", name="app_user_albums")
     * @Method({"GET"})
     * @Entity("user", expr="repository.findOneByUsername(username)")
     *
     * @param User $user
     * @return Response
     */
    public function albums(User $user) : Response
    {
        $albums = $this->getDoctrine()->getRepository(Album::class)->findAll();
        $photosCounter = 0;
        foreach ($albums as $album) {
            $photosCounter += \count($album->getPhotos());
        }

        return $this->render('App/User/albums.html.twig', [
            'albums' => $albums,
            'photosCounter' => $photosCounter,
            'user' => $user
        ]);
    }

    /**
     * @Route("/albums/{id}", name="app_user_album", requirements={"id"="%uuid_regex%"})
     * @Method({"GET"})
     * @Entity("user", expr="repository.findOneByUsername(username)")
     *
     * @param User $user
     * @return Response
     */
    public function album(User $user, Album $album) : Response
    {
        return $this->render('App/User/album.html.twig', [
            'album' => $album,
            'user' => $user
        ]);
    }

    /**
     * @Route("/item/{id}", name="app_user_item", requirements={"id"="%uuid_regex%"})
     * @Method({"GET"})
     * @Entity("user", expr="repository.findOneByUsername(username)")
     * @Entity("item", expr="repository.findById(id)")
     *
     * @param User $user
     * @param Item $item
     * @return Response
     */
    public function item(User $user, Item $item) : Response
    {
        $nextAndPrevious = $this->getDoctrine()->getRepository(Item::class)->findNextAndPrevious($item);

        return $this->render('App/User/item.html.twig', [
            'item' => $item,
            'previousItem' => $nextAndPrevious['previous'],
            'nextItem' => $nextAndPrevious['next'],
            'user' => $user
        ]);
    }

    /**
     * @Route("/signs", name="app_user_signs")
     * @Method({"GET"})
     * @Entity("user", expr="repository.findOneByUsername(username)")
     *
     * @param User $user
     * @return Response
     */
    public function signs(User $user) : Response
    {
        return $this->render('App/User/signs.html.twig', [
            'signs' => $this->getDoctrine()->getRepository(Datum::class)->findSigns(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/statistics", name="app_user_statistics")
     * @Method({"GET"})
     * @Entity("user", expr="repository.findOneByUsername(username)")
     *
     * @param User $user
     * @param TreeBuilder $treeBuilder
     * @param CalendarBuilder $calendarBuilder
     * @param ChartBuilder $chartBuilder
     * @return Response
     */
    public function statistics(User $user, TreeBuilder $treeBuilder, CalendarBuilder $calendarBuilder, ChartBuilder $chartBuilder) : Response
    {
        return $this->render('App/User/statistics.html.twig', [
            'counters' => $this->getDoctrine()->getRepository(User::class)->getCounters($user),
            'treeJson' => json_encode($treeBuilder->buildCollectionTree()),
            'calendarJson' => json_encode($calendarBuilder->buildItemCalendar($user)),
            'hoursChartJson' => json_encode($chartBuilder->buildActivityByHour($user)),
            'monthsChartJson' => json_encode($chartBuilder->buildActivityByMonth($user)),
            'monthDaysChartJson' => json_encode($chartBuilder->buildActivityByMonthDay($user)),
            'weekDaysChartJson' => json_encode($chartBuilder->buildActivityByWeekDay($user)),
            'user' => $user
        ]);
    }

    /**
     * @Route("/tags", name="app_user_tags")
     * @Method({"GET"})
     * @Entity("user", expr="repository.findOneByUsername(username)")
     *
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function tags(Request $request, User $user) : Response
    {
        $page = $request->query->get('page', 1);
        $search = $request->query->get('search', null);
        $itemsCount = $this->getDoctrine()->getRepository(Item::class)->count(['owner' => $user]);
        $tagsCount = $this->getDoctrine()->getRepository(Tag::class)->countTags($search, true);

        return $this->render('App/User/tags.html.twig', [
            'results' => $this->getDoctrine()->getRepository(Tag::class)->countItemsByTag($itemsCount, $page, $search, true),
            'search' => $search,
            'tagsCount' => $tagsCount,
            'currentPage' => $page,
            'user' => $user
        ]);
    }

    /**
     * @Route("/tags/{id}", name="app_user_tag", requirements={"id"="%uuid_regex%"})
     * @Method({"GET"})
     * @Entity("user", expr="repository.findOneByUsername(username)")
     * @Entity("tag", expr="repository.findById(id)")
     *
     * @param User $user
     * @param Tag $tag
     * @return Response
     */
    public function tag(User $user, Tag $tag) : Response
    {
        return $this->render('App/User/tag.html.twig', [
            'tag' => $tag,
            'relatedTags' => $this->getDoctrine()->getRepository(Tag::class)->findRelatedTags($tag),
            'user' => $user
        ]);
    }

    /**
     * @Route("/wishlists", name="app_user_wishlists")
     * @Method({"GET"})
     * @Entity("user", expr="repository.findOneByUsername(username)")
     *
     * @param User $user
     * @param CounterCalculator $counterCalculator
     * @return Response
     */
    public function wishlists(User $user, CounterCalculator $counterCalculator) : Response
    {
        $wishlists = $this->getDoctrine()->getRepository(Wishlist::class)->findAllParent();

        return $this->render('App/User/wishlists.html.twig', [
            'wishlists' => $wishlists,
            'counters' => $counterCalculator->wishlistsCounters($wishlists),
            'user' => $user
        ]);
    }

    /**
     * @Route("/wishlists/{id}", name="app_user_wishlist", requirements={"id"="%uuid_regex%"})
     * @Method({"GET"})
     * @Entity("user", expr="repository.findOneByUsername(username)")
     * @Entity("wishlist", expr="repository.findById(id)")
     *
     * @param User $user
     * @param Wishlist $wishlist
     * @param CounterCalculator $counterCalculator
     * @return Response
     */
    public function wishlist(User $user, Wishlist $wishlist, CounterCalculator $counterCalculator) : Response
    {
        return $this->render('App/User/wishlist.html.twig', [
            'wishlist' => $wishlist,
            'counters' => $counterCalculator->wishlistCounters($wishlist),
            'user' => $user
        ]);
    }

    /**
     * @Route("/{id}/items", name="app_user_items", requirements={"id"="%uuid_regex%"})
     * @Method({"GET"})
     * @Entity("user", expr="repository.findOneByUsername(username)")
     *
     * @param User $user
     * @param Collection $collection
     * @return Response
     */
    public function items(User $user, Collection $collection) : Response
    {
        return $this->render('App/User/items.html.twig', [
            'collection' => $collection,
            'items' => $this->getDoctrine()->getRepository(Item::class)->findAllByCollection($collection),
            'user' => $user
        ]);
    }

    /**
     * @Route("/{id}", name="app_user_collection", requirements={"id"="%uuid_regex%"})
     * @Method({"GET"})
     * @Entity("user", expr="repository.findOneByUsername(username)")
     * @Entity("collection", expr="repository.findById(id)")
     *
     * @param User $user
     * @param Collection $collection
     * @param CounterCalculator $counterCalculator
     * @return Response
     */
    public function collection(User $user, Collection $collection, CounterCalculator $counterCalculator) : Response
    {
        return $this->render('App/User/collection.html.twig', [
            'collection' => $collection,
            'counters' => $counterCalculator->collectionCounters($collection),
            'user' => $user
        ]);
    }
}
