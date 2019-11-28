<?php

declare(strict_types=1);

namespace App\Twig;

use App\Model\BreadcrumbElement;
use App\Service\BreadcrumbBuilder;
use Twig\Extension\RuntimeExtensionInterface;

class BreadcrumbRuntime implements RuntimeExtensionInterface
{
    /**
     * @var BreadcrumbBuilder
     */
    private $breadcrumbBuilder;

    /**
     * BreadcrumbExtension constructor.
     * @param BreadcrumbBuilder $breadcrumbBuilder
     */
    public function __construct(BreadcrumbBuilder $breadcrumbBuilder)
    {
        $this->breadcrumbBuilder = $breadcrumbBuilder;
    }

    /**
     * @param array $root
     * @param object $entity
     * @param string|null $action
     * @return array
     */
    public function buildBreadcrumb(array $root = [], object $entity = null, string $action = null)
    {
        $breadcrumb = [];

        if (!empty($root)) {
            foreach ($root as $element) {
                $rootElement = new BreadcrumbElement();
                $rootElement
                    ->setType(BreadcrumbElement::TYPE_ROOT)
                    ->setRoute($element['route'])
                    ->setLabel($element['trans'])
                    ->setParams([])
                ;

                $breadcrumb[] = $rootElement;
            }
        }

        if ($entity) {
            $breadcrumb = \array_merge($breadcrumb, $this->breadcrumbBuilder->build($entity));
        }

        if (null !== $action) {
            $actionElement = new BreadcrumbElement();
            $actionElement
                ->setType(BreadcrumbElement::TYPE_ACTION)
                ->setLabel($action)
            ;
            $breadcrumb[] = $actionElement;
        }

        $last = \array_pop($breadcrumb);
        $last->setClass('last');
        $breadcrumb[] = $last;

        return $breadcrumb;
    }

    /**
     * @param \Twig_Environment $environment
     * @param array $breadcrumb
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function renderBreadcrumb(\Twig_Environment $environment, array $breadcrumb)
    {
        return $environment->render('App/_partials/_breadcrumb_base.html.twig', [
            'breadcrumb' => $breadcrumb,
        ]);
    }
}