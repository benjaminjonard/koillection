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

    public function __construct(
        string $name = null, EntityManagerInterface $em, ThumbnailGenerator $thumbnailGenerator,
        TranslatorInterface $translator, string $publicPath)
    {
        $this->em = $em;
        $this->publicPath = $publicPath;
        $this->thumbnailGenerator = $thumbnailGenerator;
        $this->translator = $translator;
        
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

        $counter = 0;
        foreach ($objects as $object) {
            $imagePath = $publicPath.'/'.$object->getImage();
            $ext = pathinfo($imagePath, PATHINFO_EXTENSION);
            $file = basename($imagePath,"." . $ext);
            $dir = pathinfo($imagePath, PATHINFO_DIRNAME);
            $smallThumbnailFileName = $file . '_small.' . $ext;
            @unlink($dir.'/'.$smallThumbnailFileName);
            $resultSmall = $this->thumbnailGenerator->generate($imagePath, $dir.'/'.$smallThumbnailFileName, 300);

            if ($resultSmall) {
                $object->setImageSmallThumbnail('uploads/'.$object->getOwner()->getId().'/'.$smallThumbnailFileName);
                $counter++;
            } else {
                $object->setImageSmallThumbnail(null);
            }

            $this->em->flush();
        }

        $output->writeln($this->translator->trans('message.thumbnails_regenerated', ['%count%' => $counter]));

        return 0;
    }
}
