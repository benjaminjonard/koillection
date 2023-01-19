<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\Console\Event\ConsoleTerminateEvent;

/**
 * Add JS imports in translations files after dump command is executed.
 */
final readonly class TranslationCommandListener
{
    public function __construct(
        private readonly string $assetsPath
    ) {
    }

    public function onConsoleTerminate(ConsoleTerminateEvent $event): void
    {
        if ('bazinga:js-translation:dump' === $event->getCommand()->getName()) {
            // Config file
            $configFilePath = $this->assetsPath.'/js/translations/config.js';
            $this->updateContent($configFilePath);

            // Locale files (en.js, fr.js...)
            $path = $this->assetsPath.'/js/translations/javascript+intl-icu';
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::LEAVES_ONLY);
            foreach ($files as $file) {
                if (!$file->isDir() && 'js' == $file->getExtension()) {
                    $this->updateContent($file->getPathname());
                }
            }
        }
    }

    private function updateContent(string $path): void
    {
        $fileContent = file_get_contents($path);

        // Replace encode dashes, makes the js crash
        $contentChunks = explode('\u002D', $fileContent);
        $fileContent = implode('-', $contentChunks);

        // Import translator in the file
        $fileContent = "import Translator from 'bazinga-translator'".PHP_EOL.PHP_EOL.$fileContent;

        file_put_contents($path, $fileContent);
    }
}
