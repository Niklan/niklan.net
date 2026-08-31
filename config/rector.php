<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Attribute\SortAttributeNamedArgsRector;
use Rector\CodeQuality\Rector\CallLike\AddNameToBooleanArgumentRector;
use Rector\CodeQuality\Rector\CallLike\AddNameToNullArgumentRector;
use Rector\CodeQuality\Rector\ClassMethod\ExplicitReturnNullRector;
use Rector\CodeQuality\Rector\Equal\UseIdenticalOverEqualWithSameTypeRector;
use Rector\CodeQuality\Rector\FuncCall\SortCallLikeNamedArgsRector;
use Rector\CodeQuality\Rector\If_\SimplifyIfReturnBoolRector;
use Rector\CodeQuality\Rector\Ternary\UnnecessaryTernaryExpressionRector;
use Rector\CodingStyle\Rector\ArrowFunction\ArrowFunctionDelegatingCallToFirstClassCallableRector;
use Rector\CodingStyle\Rector\Closure\ClosureDelegatingCallToFirstClassCallableRector;
use Rector\CodingStyle\Rector\FuncCall\ClosureFromCallableToFirstClassCallableRector;
use Rector\CodingStyle\Rector\FuncCall\FunctionFirstClassCallableRector;
use Rector\Config\RectorConfig;
use Rector\Php81\Rector\Array_\ArrayToFirstClassCallableRector;
use Rector\Php85\Rector\Property\AddOverrideAttributeToOverriddenPropertiesRector;

return RectorConfig::configure()
  ->withPaths([__DIR__ . '/../app'])
  ->withPhpSets(php84: TRUE)
  ->withRules([
    // Named args.
    AddNameToBooleanArgumentRector::class,
    AddNameToNullArgumentRector::class,
    SortCallLikeNamedArgsRector::class,
    SortAttributeNamedArgsRector::class,
    // Code quality: safe transforms.
    ExplicitReturnNullRector::class,
    SimplifyIfReturnBoolRector::class,
    UnnecessaryTernaryExpressionRector::class,
    UseIdenticalOverEqualWithSameTypeRector::class,
  ])
  ->withSkip([
    // First-class callable syntax breaks PHP CS Fixer native_function_invocation.
    ArrayToFirstClassCallableRector::class,
    ArrowFunctionDelegatingCallToFirstClassCallableRector::class,
    ClosureDelegatingCallToFirstClassCallableRector::class,
    ClosureFromCallableToFirstClassCallableRector::class,
    FunctionFirstClassCallableRector::class,
    // #[\Override] on a property requires an exact match with the parent
    // declaration (RFC override_properties); Drupal base classes often
    // leave properties untyped, so adding a native type in a subclass
    // (e.g. ContentEntityBase::$validationRequired) is incompatible.
    AddOverrideAttributeToOverriddenPropertiesRector::class,
  ]);
