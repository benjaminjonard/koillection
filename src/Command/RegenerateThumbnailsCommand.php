<?php

namespace App\Command;

use App\Entity\Datum;
use App\Entity\Item;
use App\Entity\Photo;
use App\Entity\Tag;
use App\Entity\Wish;
use App\Service\ThumbnailGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RegenerateThumbnailsCommand extends Command
{
    private $em;
    private string $publicPath;
    /**
     * @var ThumbnailGenerator
     */
    private ThumbnailGenerator $thumbnailGenerator;

    public function __construct(string $name = null, EntityManagerInterface $em, ThumbnailGenerator $thumbnailGenerator, string $publicPath)
    {
        $this->em = $em;
        $this->publicPath = $publicPath;
        $this->thumbnailGenerator = $thumbnailGenerator;
        
        parent::__construct($name);
    }

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:regenerate-thumbnails';

    protected function configure()
    {
        $this
            ->setDescription('Regenerate thumbnails')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $publicPath = $this->publicPath;
        $classes = [Item::class, Datum::class, Wish::class, Photo::class, Tag::class];
        $objects = [];

        foreach ($classes as $class) {
            $result = $this->em->getRepository($class)->createQueryBuilder('o')
                ->where('o.image IS NOT NULL')
                ->getQuery()
                ->getResult();
            ;
            $objects = array_merge($objects, $result);
        }

        foreach ($objects as $object) {
            $output->writeln($object->getId());
            $imagePath = $publicPath.'/'.$object->getImage();
            $ext = pathinfo($imagePath, PATHINFO_EXTENSION);
            $file = basename($imagePath,"." . $ext);
            $dir = pathinfo($imagePath, PATHINFO_DIRNAME);
            $smallThumbnailFileName = $file . '_small.' . $ext;
            @unlink($dir.'/'.$smallThumbnailFileName);
            $resultSmall = $this->thumbnailGenerator->generate($imagePath, $dir.'/'.$smallThumbnailFileName, 300);

            $object->setImageSmallThumbnail($resultSmall ? 'uploads/'.$object->getOwner()->getId().'/'.$smallThumbnailFileName : null);
            $this->em->flush();
        }

        return 0;
    }
}
