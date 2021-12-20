<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Enum\DatumTypeEnum;
use App\Http\FileResponse;
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


    #[Route(
        path: ['en' => '/admin/export/sql', 'fr' => '/admin/export/sql'],
        name: 'app_admin_export_sql', methods: ['GET']
    )]
    public function exportSql(DatabaseDumper $databaseDumper) : FileResponse
    {
        return new FileResponse($databaseDumper->dump(), (new \DateTime())->format('YmdHis') . '-koillection-database.sql');
    }

    #[Route(
        path: ['en' => '/admin/export/images', 'fr' => '/admin/export/images'],
        name: 'app_admin_export_images', methods: ['GET']
    )]
    public function exportImages(DatabaseDumper $databaseDumper, UserRepository $userRepository) : StreamedResponse
    {
        $users = $userRepository->findAll();

        return new StreamedResponse(function () use ($users) {
            $options = new Archive();
            $options->setContentType('text/event-stream');
            $options->setFlushOutput(true);
            $options->setSendHttpHeaders(true);

            $zipFilename = (new \DateTime())->format('YmdHis') . '-koillection-images.zip';
            $zip = new ZipStream($zipFilename, $options);

            foreach ($users as $user) {
                $path = $this->getParameter('kernel.project_dir').'/public/uploads/'. $user->getId();

                $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::LEAVES_ONLY);
                foreach ($files as $name => $file) {
                    if (!$file->isDir()) {
                        $zip->addFileFromStream($user->getId() . '/' . $file->getFilename(), fopen($file->getRealPath(), 'r'));
                    }
                }
            }

            $zip->finish();
        });
    }
}
