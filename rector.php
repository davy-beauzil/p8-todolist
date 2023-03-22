<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Core\Configuration\Option;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([__DIR__ . '/config', __DIR__ . '/public', __DIR__ . '/src', __DIR__ . '/tests']);

    // register a single rule
    $rectorConfig->rule(InlineConstructorDefaultToPropertyRector::class);

    // define sets of rules
    $rectorConfig->import(SetList::DEAD_CODE);
    $rectorConfig->import(SetList::PHP_74);
    $rectorConfig->import(SymfonySetList::SYMFONY_54);
    $rectorConfig->import(SymfonySetList::SYMFONY_CODE_QUALITY);
    $rectorConfig->import(SymfonySetList::SYMFONY_52_VALIDATOR_ATTRIBUTES);
    $rectorConfig->import(SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION);
    //    $rectorConfig->import(SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES);
    $rectorConfig->import(DoctrineSetList::DOCTRINE_CODE_QUALITY);
    //    $rectorConfig->import(DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES);
    $rectorConfig->import(PHPUnitSetList::PHPUNIT_EXCEPTION);
    $rectorConfig->import(PHPUnitSetList::PHPUNIT_SPECIFIC_METHOD);
    $rectorConfig->import(PHPUnitSetList::PHPUNIT_80);
    $rectorConfig->import(PHPUnitSetList::PHPUNIT_YIELD_DATA_PROVIDER);
    $parameters = $rectorConfig->parameters();
    $parameters->set(Option::PATHS, [__DIR__ . '/src', __DIR__ . '/tests']);
    $parameters->set(Option::AUTO_IMPORT_NAMES, true);
    $parameters->set(Option::IMPORT_SHORT_CLASSES, true);

    $services = $rectorConfig->services();
    //    $rectorConfig->sets([
    //        LevelSetList::UP_TO_PHP_71
    //    ]);
};
