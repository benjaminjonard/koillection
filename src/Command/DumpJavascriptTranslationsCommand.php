<?php

namespace App\Command;

use App\Service\JavascriptTranslationsDumper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:translations:dump',
    description: 'Dumps javascripts translations',
)]
class DumpJavascriptTranslationsCommand extends Command
{
    public function __construct(
        private readonly JavascriptTranslationsDumper $dumper,
        private readonly string $kernelProjectDir
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $targetPath = $this->kernelProjectDir . '/assets/js';
        if (!is_dir($dir = dirname($targetPath))) {
            $output->writeln('<info>[dir+]</info>  ' . $dir);
            if (false === @mkdir($dir, 0777, true)) {
                throw new \RuntimeException('Unable to create directory ' . $dir);
            }
        }

        $output->writeln(sprintf(
            'Installing translation files in <comment>%s</comment> directory',
            $targetPath
        ));

        $this->dumper->dump($targetPath);

        return Command::SUCCESS;
    }
}
