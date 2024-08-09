<?php

use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector;
use Rector\DeadCode\Rector\Property\RemoveUselessVarTagRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromAssignsRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;

return RectorConfig::configure()
    // register single rule
    ->withRules([
        TypedPropertyFromStrictConstructorRector::class,
        DeclareStrictTypesRector::class,
        RemoveUselessVarTagRector::class,
        RemoveUselessReturnTagRector::class,
    ])
    ->withConfiguredRule(TypedPropertyFromAssignsRector::class, [
        'inline_public' => false,
    ])
    // here we can define, what prepared sets of rules will be applied
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        earlyReturn: true
    )
    ->withPhpSets(php83: true)
;
