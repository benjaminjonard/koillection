<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\CodingStyle\Rector\FuncCall\ConsistentPregDelimiterRector;
use Rector\CodingStyle\Rector\ClassConst\VarConstantCommentRector;
use Rector\Config\RectorConfig;
use Rector\Doctrine\Rector\Property\ImproveDoctrineCollectionDocTypeInEntityRector;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Rector\Class_\EventListenerToEventSubscriberRector;
use Rector\Symfony\Set\SymfonySetList;
use Rector\Transform\Rector\Attribute\AttributeKeyToClassConstFetchRector;
use Rector\Php80\Rector\Switch_\ChangeSwitchToMatchRector;
use Rector\Php80\Rector\FuncCall\ClassOnObjectRector;
use Rector\Php80\Rector\NotIdentical\StrContainsRector;
use Rector\Php80\Rector\Identical\StrEndsWithRector;
use Rector\Php80\Rector\Identical\StrStartsWithRector;
use Rector\Php80\Rector\Class_\StringableForToStringRector;
use Rector\Php81\Rector\ClassMethod\NewInInitializerRector;
use \Rector\Php81\Rector\Property\ReadOnlyPropertyRector;


return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/api',
        __DIR__ . '/tests'
    ]);

    $rectorConfig->ruleWithConfiguration(ConsistentPregDelimiterRector::class, [
        ConsistentPregDelimiterRector::DELIMITER => '/',
    ]);

    $rectorConfig->symfonyContainerXml(__DIR__ . '/var/cache/dev/App_KernelDevDebugContainer.xml');

    $rectorConfig->rules([
        // PHP 8.0
        ChangeSwitchToMatchRector::class,
        ClassOnObjectRector::class,
        StrContainsRector::class,
        StrEndsWithRector::class,
        StrStartsWithRector::class,
        StringableForToStringRector::class,
        // PHP 8.1
        NewInInitializerRector::class,
        ReadOnlyPropertyRector::class
    ]);

    $rectorConfig->sets([
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::DEAD_CODE,

        SymfonySetList::SYMFONY_60,
        SymfonySetList::SYMFONY_CODE_QUALITY,
        SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,

        PHPUnitSetList::PHPUNIT_91,
        PHPUnitSetList::PHPUNIT_CODE_QUALITY,

        DoctrineSetList::DOCTRINE_CODE_QUALITY,
    ]);

    $rectorConfig->skip([
        EventListenerToEventSubscriberRector::class,
        AttributeKeyToClassConstFetchRector::class,
        ImproveDoctrineCollectionDocTypeInEntityRector::class,
        EncapsedStringsToSprintfRector::class,
        VarConstantCommentRector::class
    ]);
};
