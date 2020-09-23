<?php

declare(strict_types=1);

namespace App\Twig;

use App\Model\BreadcrumbElement;
use App\Service\BreadcrumbBuilder;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\RuntimeExtensionInterface;

class BreadcrumbRuntime implements RuntimeExtensionInterface
{
    /**
     * @var BreadcrumbBuilder
     */
    private BreadcrumbBuilder $breadcrumbBuilder;

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
     * @param Environment $environment
     * @param array $breadcrumb
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderBreadcrumb(Environment $environment, array $breadcrumb)
    {
        return $environment->render('App/_partials/_breadcrumb/_base.html.twig', [
            'breadcrumb' => $breadcrumb,
        ]);
    }
}