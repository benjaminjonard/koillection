<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Tag;
use App\Model\BreadcrumbElement;
use App\Service\ItemNameGuesser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Class AppExtension
 *
 * @package App\Twig
 */
class AppExtension extends AbstractExtension
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

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
     * @param RouterInterface $router
     * @param EntityManagerInterface $em
     */
    public function __construct(TranslatorInterface $translator, RouterInterface $router, EntityManagerInterface $em)
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->em = $em;
    }

    /**
     * @return array
     */
    public function getFilters() : array
    {
        return [
            new TwigFilter('safeContent', [$this, 'safeContent'], ['is_safe' => ['html']]),
            new TwigFilter('bytes', [$this, 'bytes']),
            new TwigFilter('highlightTags', [$this, 'highlightTags'], ['is_safe' => ['html']])
        ];
    }

    /**
     * @return array
     */
    public function getFunctions() : array
    {
        return [
            new TwigFunction('renderTitle', [$this, 'renderTitle'])
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

        $suffixes = ['', 'Ki', 'Mi', 'Gi', 'Ti', 'Pi', 'Ei', 'Zi', 'Yi'];

        return round(pow(1024, $base - floor($base)), $precision).' '.$suffixes[floor($base)].$this->translator->trans('global.byte_abbreviation');
    }

    /**
     * @param array $breadcrumb
     * @return string
     * @throws \ReflectionException
     */
    public function renderTitle(array $breadcrumb)
    {
        $element = array_shift($breadcrumb);

        if ($element instanceof BreadcrumbElement && isset($element->getParams()['username'])) {
            return $this->translator->trans($element->getLabel(), ['%username%' => $element->getParams()['username']]);
        }

        $element = \count($breadcrumb) === 0 ? $element : array_pop($breadcrumb);

        if ($element instanceof BreadcrumbElement) {
            if ($element->getType() === 'action') {
                $entityElement = array_pop($breadcrumb);

                if ($entityElement instanceof BreadcrumbElement && $entityElement->getEntity() !== null) {
                    $class = (new \ReflectionClass($entityElement->getEntity()))->getShortName();
                    return $this->translator->trans('global.entities.'.strtolower($class)).' · '.$entityElement->getLabel().' · '.$this->translator->trans($element->getLabel());
                } elseif (strpos($element->getLabel(), 'breadcrumb.') !== false) {
                    return $this->translator->trans($element->getLabel());
                }

                return $this->translator->trans('label.search').' · '.$element->getLabel();
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
    public function getName() : string
    {
        return 'app_extension';
    }
}
