<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Album;
use App\Entity\ChoiceList;
use App\Entity\Collection;
use App\Entity\Inventory;
use App\Entity\Item;
use App\Entity\Log;
use App\Entity\Photo;
use App\Entity\Tag;
use App\Entity\TagCategory;
use App\Entity\Template;
use App\Entity\Wish;
use App\Entity\Wishlist;
use App\Enum\LogTypeEnum;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCommand(
    name: 'app:regenerate-logs',
    description: 'Regenerate missing create logs',
)]
class RegenerateLogsCommand extends Command
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly TranslatorInterface $translator
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $counter = 0;
        $classes = [
            Collection::class, Item::class,
            Wishlist::class, Wish::class,
            Album::class, Photo::class,
            Tag::class, TagCategory::class,
            Template::class, ChoiceList::class,
            Inventory::class,
        ];

        foreach ($classes as $class) {
            $results = $this->managerRegistry
                ->getRepository($class)
                ->createQueryBuilder('o')
                ->getQuery()
                ->toIterable()
            ;

            foreach ($results as $result) {
                $log = $this->managerRegistry->getRepository(Log::class)->findOneBy([
                    'type' => LogTypeEnum::TYPE_CREATE,
                    'objectId' => $result->getId(),
                ]);

                if (!$log instanceof Log) {
                    $log = (new Log())
                        ->setType(LogTypeEnum::TYPE_CREATE)
                        ->setLoggedAt($result->getCreatedAt())
                        ->setObjectClass($class)
                        ->setObjectId($result->getId())
                        ->setObjectLabel($result->__toString())
                        ->setOwner($result->getOwner())
                    ;
                    $this->managerRegistry->getManager()->persist($log);
                    ++$counter;
                }
            }

            $this->managerRegistry->getManager()->flush();
            $this->managerRegistry->getManager()->clear();
        }

        $output->writeln($this->translator->trans('message.logs_generated', ['%count%' => $counter]));

        return Command::SUCCESS;
    }
}
