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
    protected static $defaultName = 'app:thumbnails:regenerate';

    protected function configure()
    {
        // ...
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $publicPath = $this->publicPath;
        
        $items = $this->em->getRepository(Item::class)->createQueryBuilder('i')
            ->where('i.image IS NOT NULL')
            ->getQuery()
            ->getResult();
        ;

        foreach ($items as $item) {
            $imagePath = $publicPath.'/'.$item->getImage();
            $ext = pathinfo($imagePath, PATHINFO_EXTENSION);
            $file = basename($imagePath,"." . $ext);
            $dir = pathinfo($imagePath, PATHINFO_DIRNAME);
            $mediumThumbnailFileName = $file . '_medium.' . $ext;
            $smallThumbnailFileName = $file . '_small.' . $ext;
            @unlink($dir.'/'.$mediumThumbnailFileName);
            @unlink($dir.'/'.$smallThumbnailFileName);
            $resultMedium = $this->thumbnailGenerator->generate($imagePath, $dir.'/'.$mediumThumbnailFileName, 600);
            $resultSmall = $this->thumbnailGenerator->generate($imagePath, $dir.'/'.$smallThumbnailFileName, 300);

            $item->setImageMediumThumbnail($resultMedium ? 'uploads/'.$item->getOwner()->getId().'/'.$mediumThumbnailFileName : null);
            $item->setImageSmallThumbnail($resultSmall ? 'uploads/'.$item->getOwner()->getId().'/'.$smallThumbnailFileName : null);
            $this->em->flush();
        }

        $data = $this->em->getRepository(Datum::class)->createQueryBuilder('d')
            ->where('d.image IS NOT NULL')
            ->getQuery()
            ->getResult();
        ;

        foreach ($data as $datum) {
            $imagePath = $publicPath.'/'.$datum->getImage();
            $ext = pathinfo($imagePath, PATHINFO_EXTENSION);
            $file = basename($imagePath,"." . $ext);
            $dir = pathinfo($imagePath, PATHINFO_DIRNAME);
            $mediumThumbnailFileName = $file . '_medium.' . $ext;
            $smallThumbnailFileName = $file . '_small.' . $ext;
            @unlink($dir.'/'.$mediumThumbnailFileName);
            @unlink($dir.'/'.$smallThumbnailFileName);
            $resultMedium = $this->thumbnailGenerator->generate($imagePath, $dir.'/'.$mediumThumbnailFileName, 600);
            $resultSmall = $this->thumbnailGenerator->generate($imagePath, $dir.'/'.$smallThumbnailFileName, 300);

            $datum->setImageMediumThumbnail($resultMedium ? 'uploads/'.$datum->getOwner()->getId().'/'.$mediumThumbnailFileName : null);
            $datum->setImageSmallThumbnail($resultSmall ? 'uploads/'.$datum->getOwner()->getId().'/'.$smallThumbnailFileName : null);
            $this->em->flush();
        }


        $wishes = $this->em->getRepository(Wish::class)->createQueryBuilder('w')
            ->where('w.image IS NOT NULL')
            ->getQuery()
            ->getResult();
        ;

        foreach ($wishes as $wish) {
            $imagePath = $publicPath.'/'.$wish->getImage();
            $ext = pathinfo($imagePath, PATHINFO_EXTENSION);
            $file = basename($imagePath,"." . $ext);
            $dir = pathinfo($imagePath, PATHINFO_DIRNAME);
            $mediumThumbnailFileName = $file . '_medium.' . $ext;
            $smallThumbnailFileName = $file . '_small.' . $ext;
            @unlink($dir.'/'.$mediumThumbnailFileName);
            @unlink($dir.'/'.$smallThumbnailFileName);
            $resultMedium = $this->thumbnailGenerator->generate($imagePath, $dir.'/'.$mediumThumbnailFileName, 600);
            $resultSmall = $this->thumbnailGenerator->generate($imagePath, $dir.'/'.$smallThumbnailFileName, 300);

            $wish->setImageMediumThumbnail($resultMedium ? 'uploads/'.$wish->getOwner()->getId().'/'.$mediumThumbnailFileName : null);
            $wish->setImageSmallThumbnail($resultSmall ? 'uploads/'.$wish->getOwner()->getId().'/'.$smallThumbnailFileName : null);
            $this->em->flush();
        }

        $photos = $this->em->getRepository(Photo::class)->createQueryBuilder('p')
            ->where('p.image IS NOT NULL')
            ->getQuery()
            ->getResult();
        ;

        foreach ($photos as $photo) {
            $imagePath = $publicPath.'/'.$photo->getImage();
            $ext = pathinfo($imagePath, PATHINFO_EXTENSION);
            $file = basename($imagePath,"." . $ext);
            $dir = pathinfo($imagePath, PATHINFO_DIRNAME);
            $smallThumbnailFileName = $file . '_small.' . $ext;
            @unlink($dir.'/'.$smallThumbnailFileName);
            $resultSmall = $this->thumbnailGenerator->generate($imagePath, $dir.'/'.$smallThumbnailFileName, 300);

            $photo->setImageSmallThumbnail($resultSmall ? 'uploads/'.$photo->getOwner()->getId().'/'.$smallThumbnailFileName : null);
            $this->em->flush();
        }

        $tags = $this->em->getRepository(Tag::class)->createQueryBuilder('t')
            ->where('t.image IS NOT NULL')
            ->getQuery()
            ->getResult();
        ;

        foreach ($tags as $tag) {
            $imagePath = $publicPath.'/'.$tag->getImage();
            $ext = pathinfo($imagePath, PATHINFO_EXTENSION);
            $file = basename($imagePath,"." . $ext);
            $dir = pathinfo($imagePath, PATHINFO_DIRNAME);
            $smallThumbnailFileName = $file . '_small.' . $ext;
            @unlink($dir.'/'.$smallThumbnailFileName);
            $resultSmall = $this->thumbnailGenerator->generate($imagePath, $dir.'/'.$smallThumbnailFileName, 300);

            $tag->setImageSmallThumbnail($resultSmall ? 'uploads/'.$tag->getOwner()->getId().'/'.$smallThumbnailFileName : null);
            $this->em->flush();
        }

        return 0;
    }
}
