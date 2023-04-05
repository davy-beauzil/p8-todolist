<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Alias\MbStrFunctionsFixer;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ClassNotation\ProtectedToPrivateFixer;
use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use PhpCsFixer\Fixer\ConstantNotation\NativeConstantInvocationFixer;
use PhpCsFixer\Fixer\ControlStructure\NoSuperfluousElseifFixer;
use PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer;
use PhpCsFixer\Fixer\Import\GlobalNamespaceImportFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\CombineConsecutiveIssetsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\CombineConsecutiveUnsetsFixer;
use PhpCsFixer\Fixer\Phpdoc\AlignMultilineCommentFixer;
use PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocOrderFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTrimConsecutiveBlankLineSeparationFixer;
use PhpCsFixer\Fixer\PhpTag\LinebreakAfterOpeningTagFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestAnnotationFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestCaseStaticMethodCallsFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestClassRequiresCoversFixer;
use PhpCsFixer\Fixer\ReturnNotation\SimplifiedNullReturnFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\Strict\StrictComparisonFixer;
use PhpCsFixer\Fixer\Strict\StrictParamFixer;
use PhpCsFixer\Fixer\Whitespace\ArrayIndentationFixer;
use PhpCsFixer\Fixer\Whitespace\CompactNullableTypehintFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $header = '';

    $ecsConfig->sets([
        SetList::PSR_12,
        SetList::CLEAN_CODE,
        SetList::DOCTRINE_ANNOTATIONS,
        SetList::SPACES,
        SetList::PHPUNIT,
        SetList::SYMPLIFY,
        SetList::ARRAY,
        SetList::COMMON,
        SetList::COMMENTS,
        SetList::CONTROL_STRUCTURES,
        SetList::DOCBLOCK,
        SetList::NAMESPACES,
        SetList::STRICT,
    ]);

    $services = $ecsConfig->services();
    $services->set(StrictParamFixer::class);
    $services->set(StrictComparisonFixer::class);
    $services->set(ArraySyntaxFixer::class)
        ->call('configure', [[
            'syntax' => 'short',
        ]])
    ;
    $services->set(ArrayIndentationFixer::class);
    $services->set(NoUnusedImportsFixer::class);
    $services->set(OrderedImportsFixer::class);
    $services->set(ProtectedToPrivateFixer::class);
    $services->set(DeclareStrictTypesFixer::class);
    $services->set(NativeConstantInvocationFixer::class);
    $services->set(NativeFunctionInvocationFixer::class)
        ->call('configure', [[
            'include' => ['@compiler_optimized'],
            'scope' => 'namespaced',
            'strict' => true,
        ]])
    ;
    $services->set(MbStrFunctionsFixer::class);
    $services->set(LinebreakAfterOpeningTagFixer::class);
    $services->set(CombineConsecutiveIssetsFixer::class);
    $services->set(CombineConsecutiveUnsetsFixer::class);
    $services->set(CompactNullableTypehintFixer::class);
    $services->set(NoSuperfluousElseifFixer::class);
    $services->set(NoSuperfluousPhpdocTagsFixer::class);
    $services->set(PhpdocTrimConsecutiveBlankLineSeparationFixer::class);
    $services->set(PhpdocOrderFixer::class);
    $services->set(SimplifiedNullReturnFixer::class);
    $services->set(HeaderCommentFixer::class)
        ->call('configure', [[
            'header' => $header,
        ]])
    ;
    $services->set(AlignMultilineCommentFixer::class)
        ->call('configure', [[
            'comment_type' => 'all_multiline',
        ]])
    ;
    $services->set(PhpUnitTestAnnotationFixer::class)
        ->call('configure', [[
            'style' => 'annotation',
        ]])
    ;
    $services->set(PhpUnitTestCaseStaticMethodCallsFixer::class);
    $services->set(GlobalNamespaceImportFixer::class)
        ->call('configure', [[
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ]])
    ;

    $services->remove(PhpUnitTestClassRequiresCoversFixer::class);

    $parameters = $ecsConfig->parameters();
    $parameters
        ->set(Option::PARALLEL, true)
        ->set(Option::PATHS, [__DIR__])
        ->set(Option::SKIP, [
            __DIR__ . '/src/Kernel.php',
            __DIR__ . '/assets',
            __DIR__ . '/bin',
            __DIR__ . '/config',
            __DIR__ . '/heroku',
            __DIR__ . '/public',
            __DIR__ . '/var',
        ])
    ;
};
