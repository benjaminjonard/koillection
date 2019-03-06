<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Log;
use App\Enum\LogTypeEnum;
use App\Service\Log\LoggerChain;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class LogExtension
 *
 * @package App\Twig
 */
class LogExtension extends \Twig_Extension
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var LoggerChain
     */
    private $loggerChain;

    /**
     * LogExtension constructor.
     * @param TranslatorInterface $translator
     * @param RouterInterface $router
     * @param LoggerChain $loggerChain
     */
    public function __construct(TranslatorInterface $translator, RouterInterface $router, LoggerChain $loggerChain)
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->loggerChain = $loggerChain;
    }

    /**
     * @return array
     */
    public function getFunctions() : array
    {
        return [
            new \Twig_SimpleFunction('getLogMessages', [$this, 'getLogMessages']),
        ];
    }

    /**
     * @param Log $log
     * @return array
     */
    public function getLogMessages(Log $log) : array
    {
        $messages = [];

        $explodedNamespace = explode('\\', $log->getObjectClass());
        $class = array_pop($explodedNamespace);
        $class = strtolower($class);

        switch ($log->getType()) {
            case LogTypeEnum::TYPE_CREATE:
                $route = $this->router->generate('app_'.$class.'_show', ['id' => $log->getObjectId()]);
                $label = "<strong><a href='$route'>".$log->getObjectLabel()."</a></strong>";
                $messages[] = $this->translator->trans('log.'.$class.'.created', ['%label%' => $label]);
                break;
            case LogTypeEnum::TYPE_DELETE:
                $label = "<strong>".$log->getObjectLabel()."</strong>";
                $messages[] = $this->translator->trans('log.'.$class.'.deleted', ['%label%' => $label]);
                break;
            case LogTypeEnum::TYPE_UPDATE:
                foreach (json_decode($log->getPayload(), true) as $payload) {
                    $messages[] = $this->loggerChain->getFormattedPayload($log->getObjectClass(), $payload);
                }
                break;
            default:
                break;
        }

        return $messages;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return 'log_extension';
    }
}
