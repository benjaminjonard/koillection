<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Collection;
use App\Entity\Item;
use App\Entity\Medium;
use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Wish;
use App\Entity\Wishlist;
use App\Service\DiskUsageCalculator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @Route("", name="app_admin_index", methods={"GET"})
     *
     * @return Response
     */
    public function index() : Response
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

    /**
     * @Route("/clean", name="app_admin_clean", methods={"GET"})
     *
     * @return Response
     */
    public function clean(string $publicPath, TranslatorInterface $translator, DiskUsageCalculator $diskUsageCalculator) : Response
    {
        $em = $this->getDoctrine()->getManager();

        //Get all paths in database (image + image thumbnail)
        $sql = "SELECT m.path as path, m.thumbnail_path as thumbnailPath FROM koi_medium m;";
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();

        $dbPaths = [];
        while ($row = $stmt->fetch()) {
            $dbPaths[] = $row['path'];
            if ($row['thumbnailpath'] !== null) {
                $dbPaths[] = $row['thumbnailpath'];
            }
        }

        //Get all paths on disk
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($publicPath.'/uploads'));
        $diskPaths = array();
        foreach ($rii as $file) {
            if (!$file->isDir() && $file->getFileName() !== '.gitkeep') {
                $diskPaths[] = str_replace($publicPath. '/', '', $file->getPathname());
            }
        }

        //Compute the diff and delete the diff
        $diff = array_diff($diskPaths, $dbPaths);
        foreach ($diff as $path) {
            unlink($publicPath.'/'.$path);
        }

        $this->addFlash('notice', $translator->trans('message.files_deleted', ['%count%' => \count($diff)]));

        //Update users disk usage
        $users = $em->getRepository(User::class)->findAll();
        foreach ($users as $user) {
            $user->setDiskSpaceUsed($diskUsageCalculator->getSpaceUsedByUser($user));
        }
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('app_admin_index');
    }
}
