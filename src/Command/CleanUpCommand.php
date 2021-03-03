<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Contracts\Translation\TranslatorInterface;

class CleanUpCommand extends Command
{
    private EntityManagerInterface $em;

    private string $publicPath;

    private TranslatorInterface $translator;

    public function __construct(string $name = null, EntityManagerInterface $em, TranslatorInterface $translator, string $publicPath)
    {
        $this->em = $em;
        $this->publicPath = $publicPath;
        $this->translator = $translator;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('app:clean-up')
            ->setDescription('Delete unused images')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('This action can be dangerous, please do a backup of both your database and /uploads folder. Are you sure you want to continue ? (y/N)', false);

        if (!$helper->ask($input, $output, $question)) {
            return Command::SUCCESS;
        }

        //Get all paths in database (images + thumbnails)
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
            SELECT image_large_thumbnail AS image FROM koi_item WHERE image_large_thumbnail IS NOT NULL UNION
            
            SELECT image AS image FROM koi_datum WHERE image IS NOT NULL UNION
            SELECT image_small_thumbnail AS image FROM koi_datum WHERE image_small_thumbnail IS NOT NULL UNION
            SELECT image_large_thumbnail AS image FROM koi_datum WHERE image_large_thumbnail IS NOT NULL UNION
            SELECT file AS image FROM koi_datum WHERE file IS NOT NULL UNION
            
            SELECT image AS image FROM koi_wish WHERE image IS NOT NULL UNION
            SELECT image_small_thumbnail AS image FROM koi_wish WHERE image_small_thumbnail IS NOT NULL;
        ";

        $stmt = $this->em->getConnection()->prepare($sql);
        $stmt->execute();
        $dbPaths = array_map(function ($row) { return $row['image']; }, $stmt->fetchAll());

        //Get all paths on disk
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->publicPath.'/uploads'));
        $diskPaths = [];
        foreach ($rii as $file) {
            if (!$file->isDir() && $file->getFileName() !== '.gitkeep') {
                $diskPaths[] = str_replace($this->publicPath. '/', '', $file->getPathname());
            }
        }

        //Compute the diff and delete the diff
        $diff = \array_diff($diskPaths, $dbPaths);
        foreach ($diff as $path) {
            if (file_exists($this->publicPath.'/'.$path)) {
                unlink($this->publicPath.'/'.$path);
            }
        }

        $output->writeln($this->translator->trans('message.files_deleted', ['%count%' => \count($diff)]));

        return Command::SUCCESS;
    }
}
