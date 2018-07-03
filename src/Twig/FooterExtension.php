<?php

namespace App\Twig;

use App\Entity\User;
use App\Model\BreadcrumbElement;
use App\Service\BreadcrumbBuilder;

/**
 * Class BreadcrumbExtension
 *
 * @package App\Twig
 */
class FooterExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFunctions() : array
    {
        return [
            new \Twig_SimpleFunction('renderFooter', [$this, 'renderFooter'], ['needs_environment' => true, 'is_safe' => ['html']]),
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
            return $environment->render('App/footer.html.twig', [
                'object' => $object,
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
