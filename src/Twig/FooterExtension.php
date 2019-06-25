<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class BreadcrumbExtension
 *
 * @package App\Twig
 */
class FooterExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFunctions() : array
    {
        return [
            new TwigFunction('renderFooter', [$this, 'renderFooter'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

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

            return $environment->render('App/_partials/footer.html.twig', [
                'object' => $object,
                'class' => $class
            ]);
        }

        return '';
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return 'footer_extension';
    }
}
