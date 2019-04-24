<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\User;
use App\Model\BreadcrumbElement;
use App\Service\BreadcrumbBuilder;
use Twig\TwigFunction;

/**
 * Class BreadcrumbExtension
 *
 * @package App\Twig
 */
class BreadcrumbExtension extends \Twig_Extension
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
     * @return array
     */
    public function getFunctions() : array
    {
        return [
            new TwigFunction('buildBreadcrumb', [$this, 'buildBreadcrumb']),
            new TwigFunction('renderBreadcrumb', [$this, 'renderBreadcrumb'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    /**
     * @param array $root
     * @param object $entity
     * @param string|null $action
     * @param User|null $user
     * @return array
     */
    public function buildBreadcrumb(array $root = [], object $entity = null, string $action = null, User $user = null)
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

                if ($user instanceof User) {
                    $rootElement->setParams(['username' => $user->getUsername()]);
                }

                $breadcrumb[] = $rootElement;
            }
        }

        if ($entity) {
            $breadcrumb = array_merge($breadcrumb, $this->breadcrumbBuilder->build($entity));
        }

        if (null !== $action) {
            $actionElement = new BreadcrumbElement();
            $actionElement
                ->setType(BreadcrumbElement::TYPE_ACTION)
                ->setLabel($action)
            ;
            $breadcrumb[] = $actionElement;
        }

        $last = array_pop($breadcrumb);
        $last->setClass('last');
        $breadcrumb[] = $last;

        return $breadcrumb;
    }

    /**
     * @param \Twig_Environment $environment
     * @param array $breadcrumb
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function renderBreadcrumb(\Twig_Environment $environment, array $breadcrumb)
    {
        return $environment->render('Breadcrumb/breadcrumb-base.html.twig', [
            'breadcrumb' => $breadcrumb,
        ]);
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return 'breadcrumb_extension';
    }
}
