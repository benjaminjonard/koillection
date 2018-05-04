<?php

namespace App\Twig;

use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Interfaces\BreabcrumbableInterface;
use App\Model\BreadcrumbElement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class AppExtension
 *
 * @package App\Twig
 */
class AppExtension extends \Twig_Extension
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * AppExtension constructor.
     *
     * @param TranslatorInterface $translator
     * @param RequestStack $requestStack
     * @param RouterInterface $router
     * @param EntityManagerInterface $em
     */
    public function __construct(TranslatorInterface $translator, RequestStack $requestStack, RouterInterface $router, EntityManagerInterface $em)
    {
        $this->translator = $translator;
        $this->requestStack = $requestStack;
        $this->router = $router;
        $this->em = $em;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('safeContent', [$this, 'safeContent'], ['is_safe' => ['html']]),
            new \Twig_SimpleFilter('bytes', [$this, 'bytes']),
            new \Twig_SimpleFilter('timeAgo', [$this, 'timeAgo']),
            new \Twig_SimpleFilter('timeDiff', [$this, 'timeDiff']),
            new \Twig_SimpleFilter('highlightTags', [$this, 'highlightTags'], ['is_safe' => ['html']])
        ];
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('buildBreadcrumb', [$this, 'buildBreadcrumb']),
            new \Twig_SimpleFunction('renderBreadcrumb', [$this, 'renderBreadcrumb'], ['needs_environment' => true, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('renderTitle', [$this, 'renderTitle']),
        ];
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function safeContent(string $string) : string
    {
        return $string;
    }

    /**
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    public function bytes(int $bytes, int $precision = 2) : string
    {
        $base = $bytes > 0 ? log($bytes, 1024) : $bytes;

        $suffixes = array('', 'Ki', 'Mi', 'Gi', 'Ti', 'Pi', 'Ei', 'Zi', 'Yi');

        return round(pow(1024, $base - floor($base)), $precision).' '. $suffixes[floor($base)] . $this->translator->trans('global.byte_abbreviation');
    }

    /**
     * @param \DateTime $ago
     * @return string
     */
    public function timeAgo(\DateTime $ago) : string
    {
        $now = new \DateTime();
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $this->translator->transChoice("global.time.$v", $diff->$k);
            } else {
                unset($string[$k]);
            }
        }
        $string = \array_slice($string, 0, 1);

        return $string ?
            $this->translator->trans('global.time.ago', ['%time%' => implode(', ', $string)]) :
            $this->translator->trans('global.time.just_now');
    }

    /**
     * @param \DateTime $start
     * @param \DateTime $end
     * @return string
     */
    public function timeDiff(\DateTime $start, \DateTime $end) : string
    {
        $diff = $start->diff($end);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $this->translator->transChoice("global.time.$v", $diff->$k);
            } else {
                unset($string[$k]);
            }
        }
        $string = \array_slice($string, 0, 1);

        return $string ? implode(', ', $string) : '';
    }

    /**
     * @param array $root
     * @param null $entity
     * @param string|null $action
     * @return mixed|string
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

        if ($entity instanceof BreabcrumbableInterface) {
            $element = $entity->getBreadcrumb($context);
            $breadcrumb = array_merge($breadcrumb, $element);
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
     */
    public function renderBreadcrumb(\Twig_Environment $environment, array $breadcrumb)
    {
        return $environment->render('Breadcrumb/breadcrumb-base.html.twig', [
            'breadcrumb' => $breadcrumb,
        ]);
    }

    /**
     * @param array $breadcrumb
     * @return string
     */
    public function renderTitle(array $breadcrumb)
    {
        $element = array_shift($breadcrumb);
        if ($element instanceof BreadcrumbElement) {
            if (isset($element->getParams()['username'])) {
                return $this->translator->trans($element->getLabel(), ['%username%' => $element->getParams()['username']]);
            }
        }

        $element = \count($breadcrumb) === 0 ? $element : array_pop($breadcrumb);
        if ($element instanceof BreadcrumbElement) {
            if ($element->getType() === 'action') {
                $entityElement = array_pop($breadcrumb);
                if ($entityElement instanceof BreadcrumbElement) {
                    $class = (new \ReflectionClass($entityElement->getEntity()))->getShortName();
                    return $this->translator->trans('global.entities.'.strtolower($class)).' · '.$entityElement->getLabel() .' · '. $this->translator->trans($element->getLabel());
                }

                return $this->translator->trans($element->getLabel());
            }

            if ($element->getType() === 'entity') {
                $class = (new \ReflectionClass($element->getEntity()))->getShortName();
                return $this->translator->trans('global.entities.'.strtolower($class)).' · '.$element->getLabel();
            }

            if ($element->getType() === 'root') {
                return $this->translator->trans($element->getLabel());
            }
        }

        return $this->translator->trans('global.koillection');
    }

    /**
     * @param null|string $text
     * @return null|string|string[]
     */
    public function highlightTags(?string $text)
    {
        if (null === $text) {
            return $text;
        }

        $tags = $this->em->getRepository(Tag::class)->findAllForHighlight();

        $words = [];
        foreach ($tags as $tag) {
            $id = is_string($tag['id']) ? $tag['id'] : $tag['id']->toString();
            $words[$id] = preg_quote($tag['label'], '/');
        }

        return preg_replace_callback(
            "/\b(".implode('|', $words).")\b/ui",
            function($matches) use ($words) {
                $id = array_search(preg_quote(strtolower($matches[1]), '/'), array_map('strtolower', $words));
                $route = $this->router->generate('app_tag_show', ['id' => $id]);

                return "<a href='$route'>$matches[1]</a>";
            },
            $text
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_extension';
    }
}
