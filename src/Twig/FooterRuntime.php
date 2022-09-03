<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Environment;
use Twig\Extension\RuntimeExtensionInterface;

class FooterRuntime implements RuntimeExtensionInterface
{
    public function renderFooter(Environment $environment, $object): string
    {
        if (property_exists($object, 'createdAt') && property_exists($object, 'updatedAt') && property_exists($object, 'seenCounter')) {
            $class = $object::class;
            $class = strtolower(substr($class, strrpos($class, '\\') + 1));

            return $environment->render('App/_partials/_footer.html.twig', [
                'object' => $object,
                'class' => $class,
            ]);
        }

        return '';
    }
}
