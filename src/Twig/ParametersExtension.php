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
     * @var string
     */
    private $version;

    /**
     * ParametersExtension constructor.
     * @param bool $showAdminTools
     */
    public function __construct(bool $showAdminTools, string $version)
    {
        $this->showAdminTools = $showAdminTools;
        $this->version = $version;
    }

    /**
     * @return array
     */
    public function getFunctions() : array
    {
        return [
            new \Twig_SimpleFunction('showAdminTools', [$this, 'showAdminTools']),
            new \Twig_SimpleFunction('getVersion', [$this, 'getVersion']),
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
    public function getVersion() : string
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return 'parameters_extension';
    }
}
