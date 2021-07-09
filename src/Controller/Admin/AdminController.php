<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Enum\DatumTypeEnum;
use App\Repository\AlbumRepository;
use App\Repository\CollectionRepository;
use App\Repository\DatumRepository;
use App\Repository\ItemRepository;
use App\Repository\PhotoRepository;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use App\Repository\WishlistRepository;
use App\Repository\WishRepository;
use App\Service\DatabaseDumper;
use App\Service\LatestReleaseChecker;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Annotation\Route;
use ZipStream\Option\Archive;
use ZipStream\ZipStream;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    #[Route(path: ['en' => '/admin', 'fr' => '/admin'], name: 'app_admin_index', methods: ['GET'])]
    public function index(LatestReleaseChecker $latestVersionChecker, UserRepository $userRepository,
                          CollectionRepository $collectionRepository, ItemRepository $itemRepository, TagRepository $tagRepository,
                          WishlistRepository $wishlistRepository, WishRepository $wishRepository, AlbumRepository $albumRepository,
                          PhotoRepository $photoRepository, DatumRepository $datumRepository
    ) : Response
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('App/Admin/Admin/index.html.twig', [
            'freeSpace' => disk_free_space('/'),
            'totalSpace' => disk_total_space('/'),
            'counters' => [
                'users' => $userRepository->count([]),
                'collections' => $collectionRepository->count([]),
                'items' => $itemRepository->count([]),
                'tags' => $tagRepository->count([]),
                'wishlists' => $wishlistRepository->count([]),
                'wishes' => $wishRepository->count([]),
                'albums' => $albumRepository->count([]),
                'photos' => $photoRepository->count([]),
                'signs' => $datumRepository->count(['type' => DatumTypeEnum::TYPE_SIGN]),
            ],
            'currentRelease' => $latestVersionChecker->getCurrentRelease(),
            'latestRelease' => $latestVersionChecker->getLatestRelease(),
            'requiredPhpVersionForLatestRelease' => $latestVersionChecker->getRequiredPhpVersionForLatestRelease(),
            'isRequiredPhpVersionForLatestReleaseOk' => $latestVersionChecker->isRequiredPhpVersionForLatestReleaseOk(),
            'symfonyVersion' => Kernel::VERSION,
            'phpVersion' => phpversion(),
            'isOpcacheAvailable' => function_exists('opcache_get_status') && opcache_get_status() && opcache_get_status()['opcache_enabled']
        ]);
    }

    #[Route(path: ['en' => '/admin/backup', 'fr' => '/admin/sauvegarde'], name: 'app_admin_backup', methods: ['GET'])]
    public function backup(DatabaseDumper $databaseDumper, UserRepository $userRepository) : StreamedResponse
    {
        $users = $userRepository->findAll();

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
