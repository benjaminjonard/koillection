<?php

namespace App\Command;

use App\Entity\Datum;
use App\Entity\Item;
use App\Entity\Photo;
use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Wish;
use App\Service\ThumbnailGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegenerateThumbnailsCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var ThumbnailGenerator
     */
    private ThumbnailGenerator $thumbnailGenerator;

    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * @var string
     */
    private string $publicPath;

    /**
     * @var TokenStorageInterface
     */
    private TokenStorageInterface $tokenStorage;

    public function __construct(
        string $name = null, EntityManagerInterface $em, ThumbnailGenerator $thumbnailGenerator,
        TranslatorInterface $translator, TokenStorageInterface $tokenStorage, string $publicPath)
    {
        $this->em = $em;
        $this->publicPath = $publicPath;
        $this->thumbnailGenerator = $thumbnailGenerator;
        $this->translator = $translator;
        $this->tokenStorage = $tokenStorage;
        
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('app:regenerate-thumbnails')
            ->setDescription('Regenerate thumbnails')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $counter = 0;
        $classes = [Item::class, Datum::class, Wish::class, Photo::class, Tag::class];
        $objects = [];
        $users = $this->em->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            //Login user, needed for uploads
            $token = new UsernamePasswordToken($user,null, 'main', $user->getRoles());
            $this->tokenStorage->setToken($token);

            foreach ($classes as $class) {
                $result = $this->em->getRepository($class)->createQueryBuilder('o')
                    ->where('o.image IS NOT NULL')
                    ->andWhere('o.owner = :user')
                    ->setParameter('user', $user)
                    ->getQuery()
                    ->getResult();
                ;
                $objects = array_merge($objects, $result);
            }


            foreach ($objects as $object) {
                $imagePath = $this->publicPath . '/' . $object->getImage();

                if (is_file($imagePath)) {
                    $filename = basename($imagePath);
                    $mime = mime_content_type($imagePath);
                    $file = new UploadedFile($imagePath, $filename, $mime, null, true);

                    $object->setFile($file);
                    $counter++;
                }

                if ($counter % 100) {
                    $this->em->flush();
                }
            }

            $this->em->flush();
        }

        $output->writeln($this->translator->trans('message.thumbnails_regenerated', ['%count%' => $counter]));

        return 0;
    }
}
