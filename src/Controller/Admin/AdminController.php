<?php

namespace App\Controller\Admin;

use App\Entity\Collection;
use App\Entity\Item;
use App\Entity\Medium;
use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Wish;
use App\Entity\Wishlist;
use App\Service\Graph\ChartBuilder;
use App\Service\RandomStringGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdminController
 *
 * @package App\Controller
 *
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("", name="app_admin_index")
     * @Method({"GET"})
     *
     * @return Response
     */
    public function index(ChartBuilder $chartBuilder) : Response
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('App/Admin/Admin/index.html.twig', [
            'totalSpaceUsed' => $em->getRepository(User::class)->getTotalSpaceUsed(),
            'freeSpace' => disk_free_space('/'),
            'totalSpace' => disk_total_space('/'),
            'counters' => [
                'users' => $em->getRepository(User::class)->countAll(),
                'collections' => $em->getRepository(Collection::class)->countAll(),
                'items' => $em->getRepository(Item::class)->countAll(),
                'tags' => $em->getRepository(Tag::class)->countAll(),
                'wishlists' => $em->getRepository(Wishlist::class)->countAll(),
                'wishes' => $em->getRepository(Wish::class)->countAll(),
            ]
        ]);
    }
}
