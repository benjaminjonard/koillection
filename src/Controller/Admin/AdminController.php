<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Album;
use App\Entity\Collection;
use App\Entity\Datum;
use App\Entity\Item;
use App\Entity\Log;
use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Wish;
use App\Entity\Wishlist;
use App\Enum\LogTypeEnum;
use App\Service\DatabaseDumper;
use App\Service\ThumbnailGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use ZipStream\Option\Archive;
use ZipStream\ZipStream;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    /**
     * @Route({
     *     "en": "/admin",
     *     "fr": "/admin"
     * }, name="app_admin_index", methods={"GET"})
     *
     * @return Response
     */
    public function index() : Response
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('App/Admin/Admin/index.html.twig', [
            'freeSpace' => disk_free_space('/'),
            'totalSpace' => disk_total_space('/'),
            'counters' => [
                'users' => $em->getRepository(User::class)->count([]),
                'collections' => $em->getRepository(Collection::class)->count([]),
                'items' => $em->getRepository(Item::class)->count([]),
                'tags' => $em->getRepository(Tag::class)->count([]),
                'wishlists' => $em->getRepository(Wishlist::class)->count([]),
                'wishes' => $em->getRepository(Wish::class)->count([]),
            ],
            'symfonyVersion' => Kernel::VERSION,
            'phpVersion' => phpversion(),
            'isOpcacheAvailable' => function_exists('opcache_get_status') && opcache_get_status() && opcache_get_status()['opcache_enabled']
        ]);
    }

    /**
     * @Route({
     *     "en": "/admin/clean",
     *     "fr": "/admin/nettoyer"
     * }, name="app_admin_clean", methods={"GET"})
     *
     * @param string $publicPath
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function clean(string $publicPath, TranslatorInterface $translator) : Response
    {
        $em = $this->getDoctrine()->getManager();

        //Get all paths in database (image + image thumbnails)
        $sql = "
            SELECT image AS image FROM koi_collection WHERE image IS NOT NULL UNION

            SELECT image AS image FROM koi_album WHERE image IS NOT NULL UNION
            
            SELECT image AS image FROM koi_wishlist WHERE image IS NOT NULL UNION
            
            SELECT avatar AS image FROM koi_user WHERE avatar IS NOT NULL UNION
            
            SELECT image AS image FROM koi_tag WHERE image IS NOT NULL UNION
            SELECT image_small_thumbnail AS image FROM koi_tag WHERE image_small_thumbnail IS NOT NULL UNION
            
            SELECT image AS image FROM koi_photo WHERE image IS NOT NULL UNION
            SELECT image_small_thumbnail AS image FROM koi_photo WHERE image_small_thumbnail IS NOT NULL UNION
            
            SELECT image AS image FROM koi_item WHERE image IS NOT NULL UNION
            SELECT image_small_thumbnail AS image FROM koi_item WHERE image_small_thumbnail IS NOT NULL UNION
            SELECT image_medium_thumbnail AS image FROM koi_item WHERE image_medium_thumbnail IS NOT NULL UNION
            
            SELECT image AS image FROM koi_datum WHERE image IS NOT NULL UNION
            SELECT image_small_thumbnail AS image FROM koi_datum WHERE image_small_thumbnail IS NOT NULL UNION
            SELECT image_medium_thumbnail AS image FROM koi_datum WHERE image_medium_thumbnail IS NOT NULL UNION
            
            SELECT image AS image FROM koi_wish WHERE image IS NOT NULL UNION
            SELECT image_small_thumbnail AS image FROM koi_wish WHERE image_small_thumbnail IS NOT NULL UNION
            SELECT image_medium_thumbnail AS image FROM koi_wish WHERE image_medium_thumbnail IS NOT NULL;
        ";

        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        $dbPaths = array_map(function ($row) { return $row['image']; }, $stmt->fetchAll());

        //Get all paths on disk
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($publicPath.'/uploads'));
        $diskPaths = [];
        foreach ($rii as $file) {
            if (!$file->isDir() && $file->getFileName() !== '.gitkeep') {
                $diskPaths[] = str_replace($publicPath. '/', '', $file->getPathname());
            }
        }

        //Compute the diff and delete the diff
        $diff = \array_diff($diskPaths, $dbPaths);
        foreach ($diff as $path) {
            if (file_exists($publicPath.'/'.$path)) {
                unlink($publicPath.'/'.$path);
            }
        }

        $this->addFlash('notice', $translator->trans('message.files_deleted', ['%count%' => \count($diff)]));

        return $this->redirectToRoute('app_admin_index');
    }

    /**
     * @Route({
     *     "en": "/admin/backup",
     *     "fr": "/admin/sauvegarde"
     * }, name="app_admin_backup", methods={"GET"})
     *
     * @param DatabaseDumper $databaseDumper
     * @return StreamedResponse
     */
    public function backup(DatabaseDumper $databaseDumper) : StreamedResponse
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return new StreamedResponse(function () use ($databaseDumper, $users) {
            $options = new Archive();
            $options->setContentType('text/event-stream');
            $options->setFlushOutput(true);
            $options->setSendHttpHeaders(true);

            $zipFilename = (new \DateTime())->format('Ymd') . '-koillection-backup.zip';
            $zip = new ZipStream($zipFilename, $options);

            foreach ($users as $user) {
                $path = $this->getParameter('kernel.project_dir').'/public/uploads/'. $user->getId();

                $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::LEAVES_ONLY);
                foreach ($files as $name => $file) {
                    if (!$file->isDir()) {
                        $zip->addFileFromStream('/public/uploads/'. $user->getId() . '/' . $file->getFilename(), fopen($file->getRealPath(), 'r'));
                    }
                }
            }

            $fh = fopen('php://memory', 'r+');
            foreach ($databaseDumper->dump() as $row) {
                fwrite($fh, $row);
            }
            rewind($fh);
            $zip->addFileFromStream((new \DateTime())->format('Ymd') . '-koillection-export.sql', $fh);
            fclose($fh);

            $zip->finish();
        });
    }

    /**
     * @Route({
     *     "en": "/admin/tmp",
     *     "fr": "/admin/tmp"
     * }, name="app_admin_tmp", methods={"GET"})
     *
     * @return Response
     */
    public function tmp() : Response
    {
        $wishlists = $this->getDoctrine()->getRepository(Wishlist::class)->findAll();
        foreach ($wishlists as $wishlist) {
            $log = new Log();
            $log
                ->setType(LogTypeEnum::TYPE_CREATE)
                ->setObjectId($wishlist->getId())
                ->setObjectLabel($wishlist->__toString())
                ->setObjectClass(Wishlist::class)
                ->setOwner($wishlist->getOwner())
                ->setPayload(json_encode([]))
                ->setLoggedAt($wishlist->getCreatedAt())
            ;
            $this->getDoctrine()->getManager()->persist($log);
            $this->getDoctrine()->getManager()->flush();
        }

        $albums = $this->getDoctrine()->getRepository(Album::class)->findAll();
        foreach ($albums as $album) {
            $log = new Log();
            $log
                ->setType(LogTypeEnum::TYPE_CREATE)
                ->setObjectId($album->getId())
                ->setObjectLabel($album->__toString())
                ->setObjectClass(Album::class)
                ->setOwner($album->getOwner())
                ->setPayload(json_encode([]))
                ->setLoggedAt($album->getCreatedAt())
            ;
            $this->getDoctrine()->getManager()->persist($log);
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirectToRoute('app_admin_index');
    }
}
