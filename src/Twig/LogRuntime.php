<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Log;
use App\Enum\LogTypeEnum;
use App\Service\Log\LoggerChain;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class LogRuntime implements RuntimeExtensionInterface
{
    private TranslatorInterface $translator;

    private RouterInterface $router;

    private LoggerChain $loggerChain;

    public function __construct(TranslatorInterface $translator, RouterInterface $router, LoggerChain $loggerChain)
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->loggerChain = $loggerChain;
    }

    public function getLogMessages(Log $log) : array
    {
        $messages = [];

        $explodedNamespace = explode('\\', $log->getObjectClass());
        $class = array_pop($explodedNamespace);
        $class = strtolower($class);
        $objectLabel = $log->getObjectLabel();

        switch ($log->getType()) {
            case LogTypeEnum::TYPE_CREATE:
                if ($log->isObjectDeleted()) {
                    $label = "<strong class='deleted'>$objectLabel</strong>";
                } else {
                    $route = $this->router->generate('app_'.$class.'_show', ['id' => $log->getObjectId()]);
                    $label = "<strong><a href='$route'>$objectLabel</a></strong>";
                }

                $messages[] = $this->translator->trans('log.'.$class.'.created', ['%label%' => $label]);
                break;
            case LogTypeEnum::TYPE_DELETE:
                $label = "<strong class='deleted'>$objectLabel</strong>";
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
}