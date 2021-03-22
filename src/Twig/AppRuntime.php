<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Tag;
use App\Model\BreadcrumbElement;
use App\Service\ContextHandler;
use App\Service\FeatureChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class AppRuntime implements RuntimeExtensionInterface
{
    private TranslatorInterface $translator;
    private RouterInterface $router;
    private EntityManagerInterface $em;
    private ContextHandler $contextHandler;
    private FeatureChecker $featureChecker;
    private FormFactoryInterface $formFactory;

    public function __construct(TranslatorInterface $translator, RouterInterface $router, EntityManagerInterface $em,
        ContextHandler $contextHandler, FeatureChecker $featureChecker, FormFactoryInterface $formFactory
    )
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->em = $em;
        $this->contextHandler = $contextHandler;
        $this->featureChecker = $featureChecker;
        $this->formFactory = $formFactory;
    }

    public function safeContent(string $string) : string
    {
        return $string;
    }

    public function bytes(float $bytes, int $precision = 2) : string
    {
        $base = $bytes > 0 ? log($bytes, 1024) : $bytes;

        $suffixes = ['', 'Ki', 'Mi', 'Gi', 'Ti', 'Pi', 'Ei', 'Zi', 'Yi'];

        return round(pow(1024, $base - floor($base)), $precision).' '.$suffixes[floor($base)].$this->translator->trans('global.byte_abbreviation');
    }

    public function renderTitle(array $breadcrumb): string
    {
        $element = \array_shift($breadcrumb);

        if ($element instanceof BreadcrumbElement && isset($element->getParams()['username'])) {
            return $this->translator->trans($element->getLabel(), ['%username%' => $element->getParams()['username']]);
        }

        $element = \count($breadcrumb) === 0 ? $element : \array_pop($breadcrumb);

        if ($element instanceof BreadcrumbElement) {
            if ($element->getType() === 'action') {
                $entityElement = \array_pop($breadcrumb);

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
                $pieces = preg_split('/(?=[A-Z])/', lcfirst($class));
                $class = implode('_', $pieces);
                $class = strtolower($class);

                return $this->translator->trans('global.entities.'.strtolower($class)) .' · '. $element->getLabel();
            }

            if ($element->getType() === 'root') {
                if ($this->contextHandler->getContext() === 'user') {
                    return $this->translator->trans($element->getLabel().'_user', ['%username%' => $this->contextHandler->getUsername()]);
                }
                return $this->translator->trans($element->getLabel());
            }
        }

        return $this->translator->trans('global.koillection');
    }

    public function highlightTags(?string $text): array|string|null
    {
        if (null === $text) {
            return null;
        }

        $tags = $this->em->getRepository(Tag::class)->findAllForHighlight();

        $words = [];
        foreach ($tags as $tag) {
            $id = \is_string($tag['id']) ? $tag['id'] : $tag['id']->toString();
            $words[$id] = preg_quote($tag['label'], '/');
        }

        return preg_replace_callback(
            "/\b(".implode('|', $words).")\b/ui",
            function ($matches) use ($words) {
                $id = \array_search(preg_quote(strtolower($matches[1]), '/'), array_map('strtolower', $words));

                $route = $this->contextHandler->getRouteContext('app_tag_show');
                $route = $this->router->generate($route, ['id' => $id]);

                return "<a href='$route'>$matches[1]</a>";
            },
            $text
        );
    }

    public function getUnderlinedTags(?iterable $data): array
    {
        if ($this->isFeatureEnabled('tags') === false || empty($data)) {
            return [];
        }

        $texts = [];
        foreach ($data as $datum) {
            if ($datum->getValue() !== null) {
                $texts = array_merge($texts, explode(',', $datum->getValue()));
            }
        }
        $texts = array_map(function ($text) { return trim($text); }, $texts);
        $tags = $this->em->getRepository(Tag::class)->findBy(['label' => $texts]);

        $results = [];
        foreach ($texts as $text) {
            $matchingTag = null;
            foreach ($tags as $tag) {
                if ($text === $tag->getLabel()) {
                    $matchingTag = $tag;
                    break;
                }
            }

            if ($matchingTag instanceof Tag) {
                $route = $this->contextHandler->getRouteContext('app_tag_show');
                $url = $this->router->generate($route, ['id' => $matchingTag->getId()]);
                $results[$text] = '<a href="' . $url . '">' . $text . '</a>';
            } else {
                $results[$text] = $text;
            }
        }

        return $results;
    }

    public function isFeatureEnabled(string $feature): bool
    {
        return $this->featureChecker->isFeatureEnabled($feature);
    }

    public function createDeleteForm($url): FormView
    {
        return $this->formFactory->createBuilder(FormType::class)
            ->setAction($url)
            ->setMethod('DELETE')
            ->getForm()
            ->createView()
        ;
    }
}