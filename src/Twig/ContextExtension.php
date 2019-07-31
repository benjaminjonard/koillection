<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\ContextHandler;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class ContextExtension
 *
 * @package App\Twig
 */
class ContextExtension extends AbstractExtension
{
    /**
     * @var ContextHandler
     */
    private $contextHandler;

    /**
     * ContextExtension constructor.
     * @param ContextHandler $contextHandler
     */
    public function __construct(ContextHandler $contextHandler)
    {
        $this->contextHandler = $contextHandler;
    }

    /**
     * @return array
     */
    public function getFilters() : array
    {
        return [
            new TwigFilter('applyContext', [$this, 'applyContext']),
            new TwigFilter('applyContextTrans', [$this, 'applyContextTrans'])
        ];
    }

    /**
     * @param string $route
     * @return string
     */
    public function applyContext(string $route) : string
    {
        $context = $this->contextHandler->getContext();

        if (\in_array($context, ['user', 'preview'])) {
            $route = str_replace('app_', 'app_'.$context.'_', $route);
        }

        return $route;
    }

    /**
     * @param string $trans
     * @return string
     */
    public function applyContextTrans(string $trans) : string
    {
        $context = $this->contextHandler->getContext();

        if (\in_array($context, ['user', 'preview'])) {
            $trans .= '_'.$context;
        }

        return $trans;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return 'context_extension';
    }
}
