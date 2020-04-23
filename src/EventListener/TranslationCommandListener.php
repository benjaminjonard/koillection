<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;

/**
 * Add JS imports in translations files after dump command is executed
 */
final class TranslationCommandListener
{
    private string $assetsPath;

    public function __construct(string $assetsPath)
    {
        $this->assetsPath = $assetsPath;
    }

    /**
     * @param ConsoleTerminateEvent $event
     */
    public function onConsoleTerminate(ConsoleTerminateEvent $event)
    {
        if ($event->getCommand()->getName() === 'bazinga:js-translation:dump') {
            //Config file
            $configFilePath = $this->assetsPath.'/js/translations/config.js';
            $fileContent = file_get_contents($configFilePath);
            $string = "import Translator from '../translator.min.js'";
            file_put_contents ($configFilePath, $string . PHP_EOL . PHP_EOL . $fileContent);

            //Locale files (en.js, fr.js...)
            $path = $this->assetsPath.'/js/translations/javascript';
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::LEAVES_ONLY);
            foreach ($files as $name => $file) {
                if (!$file->isDir() && $file->getExtension() == 'js') {
                    $fileContent = file_get_contents($file->getPathname());
                    $string = "import Translator from '../../translator.min.js'";
                    file_put_contents ($file->getPathname(), $string . PHP_EOL . PHP_EOL . $fileContent);
                }
            }
        }
    }
}
