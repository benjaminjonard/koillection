<?php

namespace App\Twig;

use App\Entity\User;
use App\Model\BreadcrumbElement;
use App\Service\BreadcrumbBuilder;
use Symfony\Component\HttpFoundation\RequestStack;

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
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * BreadcrumbExtension constructor.
     * @param BreadcrumbBuilder $breadcrumbBuilder
     * @param RequestStack $requestStack
     */
    public function __construct(BreadcrumbBuilder $breadcrumbBuilder, RequestStack $requestStack)
    {
        $this->breadcrumbBuilder = $breadcrumbBuilder;
        $this->requestStack = $requestStack;
    }

    /**
     * @return array
     */
    public function getFunctions() : array
    {
        return [
            new \Twig_SimpleFunction('buildBreadcrumb', [$this, 'buildBreadcrumb']),
            new \Twig_SimpleFunction('renderBreadcrumb', [$this, 'renderBreadcrumb'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    /**
     * @param array $root
     * @param null $entity
     * @param string|null $action
     * @param User|null $user
     * @return array
     */
    public function buildBreadcrumb(array $root = [], $entity = null, string $action = null, User $user = null)
    {
        preg_match("/(?<=^app_)(.*)(?=_)/", $this->requestStack->getCurrentRequest()->get('_route'), $matches);
        $context = $matches[0] ?? 'homepage';

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

        $breadcrumb = array_merge($breadcrumb, $this->breadcrumbBuilder->build($entity, $context));

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
