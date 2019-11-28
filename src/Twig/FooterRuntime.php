<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\RuntimeExtensionInterface;

class FooterRuntime implements RuntimeExtensionInterface
{
    /**
     * @param \Twig_Environment $environment
     * @param array $breadcrumb
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function renderFooter(\Twig_Environment $environment, $object)
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