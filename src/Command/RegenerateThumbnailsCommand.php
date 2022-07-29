<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Album;
use App\Entity\Collection;
use App\Entity\Datum;
use App\Entity\Item;
use App\Entity\Photo;
use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Wish;
use App\Entity\Wishlist;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCommand(
    name: 'app:regenerate-thumbnails',
    description: 'Regenerate thumbnails',
)]
class RegenerateThumbnailsCommand extends Command
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly TranslatorInterface $translator,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly string $publicPath
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('This action can be dangerous, please do a backup of both your database and /uploads folder. Are you sure you want to continue ? (y/N)', false);

        if (!$helper->ask($input, $output, $question)) {
            return Command::SUCCESS;
        }

        $counter = 0;
        $classes = [Album::class, Collection::class, Wishlist::class, Item::class, Datum::class, Wish::class, Photo::class, Tag::class];
        $users = $this->managerRegistry->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            // Login user, needed for uploads
            $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
            $this->tokenStorage->setToken($token);

            $this->processAvatar($user);
            $this->processImage($user);

            foreach ($classes as $class) {
                $results = $this->managerRegistry->getRepository($class)->createQueryBuilder('o')
                    ->where('o.image IS NOT NULL')
                    ->andWhere('o.owner = :user')
                    ->setParameter('user', $user)
                    ->getQuery()
                    ->toIterable();

                foreach ($results as $entity) {
                    if (in_array($class, [Album::class, Collection::class, Wishlist::class])) {
                        $this->processAvatar($entity);
                    }

                    $this->processImage($entity);


                    ++$counter;

                    if ($counter % 100 === 0) {
                        $this->managerRegistry->getManager()->flush();
                        $output->writeln($this->translator->trans('message.thumbnails_regenerated', ['%count%' => $counter]));
                    }
                }

                $this->managerRegistry->getManager()->flush();
                $this->managerRegistry->getManager()->clear();
            }
        }

        $this->managerRegistry->getManager()->flush();
        $output->writeln($this->translator->trans('message.thumbnails_regenerated', ['%count%' => $counter]));

        return Command::SUCCESS;
    }

    private function processImage($entity)
    {
        if ($entity instanceof User) {
            $imagePath = $this->publicPath.'/'.$entity->getAvatar();
        } else {
            $imagePath = $this->publicPath.'/'.$entity->getImage();
        }

        if (is_file($imagePath)) {
            $filename = basename($imagePath);
            $mime = mime_content_type($imagePath);
            $file = new UploadedFile($imagePath, $filename, $mime, null, true);

            if ($entity instanceof Datum) {
                $entity->setFileImage($file);
            } else {
                $entity->setFile($file);
            }
        }
    }

    private function processAvatar(Album|Collection|Wishlist|User $entity)
    {
        if ($entity instanceof User) {
            $imagePath = $this->publicPath.'/'.$entity->getAvatar();
        } else {
            $imagePath = $this->publicPath.'/'.$entity->getImage();
        }

        if (is_file($imagePath)) {
            $image = imagecreatefrompng($imagePath);
            $thumbnail = imagecreatetruecolor(200, 200);
            imagecolortransparent($thumbnail, imagecolorallocate($thumbnail, 0, 0, 0));
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
            imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, 200, 200, 200, 200);
            imagepng($thumbnail, $imagePath, 9);
        }
    }
}
