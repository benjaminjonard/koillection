<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Collection;
use App\Entity\Item;
use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Wish;
use App\Entity\Wishlist;
use App\Http\FileResponse;
use App\Service\DatabaseDumper;
use App\Service\DiskUsageCalculator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use ZipStream\Option\Archive;
use ZipStream\ZipStream;

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
     * @param string $publicPath
     * @param TranslatorInterface $translator
     * @param DiskUsageCalculator $diskUsageCalculator
     * @return Response
     */
    public function clean(string $publicPath, TranslatorInterface $translator, DiskUsageCalculator $diskUsageCalculator) : Response
    {
        $em = $this->getDoctrine()->getManager();

        //Get all paths in database (image + image thumbnail)
        $sql = "SELECT m.path as path, m.thumbnail_path as thumbnailPath FROM koi_image m;";
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

        //Update users disk usage
        $users = $em->getRepository(User::class)->findAll();
        foreach ($users as $user) {
            $user->setDiskSpaceUsed($diskUsageCalculator->getSpaceUsedByUser($user));
        }
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('app_admin_index');
    }

    /**
     * @Route("/backup", name="app_admin_backup", methods={"GET"})
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
}
