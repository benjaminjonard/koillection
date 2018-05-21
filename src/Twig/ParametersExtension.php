<?php

namespace App\Twig;

/**
 * Class ParametersExtension
 *
 * @package App\Twig
 */
class ParametersExtension extends \Twig_Extension
{
    /**
     * @var bool
     */
    private $showAdminTools;

    /**
     * ParametersExtension constructor.
     * @param bool $showAdminTools
     */
    public function __construct(bool $showAdminTools)
    {
        $this->showAdminTools = $showAdminTools;
    }

    /**
     * @return array
     */
    public function getFunctions() : array
    {
        return [
            new \Twig_SimpleFunction('showAdminTools', [$this, 'showAdminTools']),
        ];
    }

    /**
     * @return bool
     */
    public function showAdminTools() : bool
    {
        return $this->showAdminTools;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return 'parameters_extension';
    }
}
