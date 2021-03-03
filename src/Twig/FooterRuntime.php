<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\RuntimeExtensionInterface;

class FooterRuntime implements RuntimeExtensionInterface
{
    public function renderFooter(Environment $environment, $object)
    {
        if (property_exists($object, 'createdAt') && property_exists($object, 'updatedAt') && property_exists($object, 'seenCounter')) {
            $class = \get_class($object);
            $class = strtolower(substr($class, strrpos($class, '\\') + 1));

            return $environment->render('App/_partials/_footer.html.twig', [
                'object' => $object,
                'class' => $class
            ]);
        }

        return '';
    }
}