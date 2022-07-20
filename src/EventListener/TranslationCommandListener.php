<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\Console\Event\ConsoleTerminateEvent;

/**
 * Add JS imports in translations files after dump command is executed.
 */
final class TranslationCommandListener
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
            $this->updateContent($configFilePath, '../translator.min.js');

            // Locale files (en.js, fr.js...)
            $path = $this->assetsPath.'/js/translations/javascript';
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::LEAVES_ONLY);
            foreach ($files as $name => $file) {
                if (!$file->isDir() && 'js' == $file->getExtension()) {
                    $this->updateContent($file->getPathname(), '../../translator.min.js');
                }
            }
        }
    }

    private function updateContent(string $path, string $translatorPath): void
    {
        $fileContent = file_get_contents($path);

        // Replace encode dashes, makes the js crash
        $contentChunks = explode('\u002D', $fileContent);
        $fileContent = implode('-', $contentChunks);

        // Import translator in the file
        $fileContent = "import Translator from '$translatorPath'".PHP_EOL.PHP_EOL.$fileContent;

        file_put_contents($path, $fileContent);
    }
}
