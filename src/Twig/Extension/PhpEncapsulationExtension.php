<?php

namespace Bolt\Extension\Celtic34fr\ContactCore\Twig\Extension;

use Bolt\Extension\Celtic34fr\ContactCore\Twig\Runtime\PhpEncapsulationRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

/** ensembles d'extensions d'encapsulation de fonctions et méthode Php pour accès direct sous TWIG */
class PhpEncapsulationExtension extends AbstractExtension
{
    public const SAFE = ['is_safe' => ['html']];

    public function getFilters(): array
    {
        return [
            new TwigFilter('force_to_int', fn ($value) => intval($value), self::SAFE),
            new TwigFilter('html_entity_decode', [PhpEncapsulationRuntime::class, 'twigFilter_html_entity_decode'], self::SAFE),
            new TwigFilter('bool', [PhpEncapsulationRuntime::class, 'twigFilter_boolRtn'], self::SAFE),
            new TwigFilter('xor', [PhpEncapsulationRuntime::class, 'twigFilter_xor'], self::SAFE),
            new TwigFilter('parseInt', fn ($value) => intval($value), self::SAFE),
            new TwigFilter('parseFloat', fn ($value) => floatval($value), self::SAFE),
            new TwigFilter('json_decode',
                fn ($value) => json_decode(str_replace('\\', '', $value), true), self::SAFE),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('strpos', [PhpEncapsulationRuntime::class, 'twigFunction_strpos'], self::SAFE),
            new TwigFunction('str_replace', [PhpEncapsulationRuntime::class, 'twigFunction_str_replace'], self::SAFE),
            new TwigFunction('printf', [PhpEncapsulationRuntime::class, 'twigFunction_printf'], self::SAFE),
            new TwigFunction('sprintf', [PhpEncapsulationRuntime::class, 'twigFunction_sprintf'], self::SAFE),
            new TwigFunction('is_bool', [PhpEncapsulationRuntime::class, 'twigFunction_is_bool'], self::SAFE),
            new TwigFunction('array_to_string', [PhpEncapsulationRuntime::class, 'twigFunction_array_to_string'], self::SAFE),
            new TwigFunction('preg_replace', [PhpEncapsulationRuntime::class, 'twigFunction_preg_replace'], self::SAFE),
            new TwigFunction('implode', [PhpEncapsulationRuntime::class, 'twigFunction_implode'], self::SAFE),
            new TwigFunction('is_numeric', [PhpEncapsulationRuntime::class, 'twigFunction_is_numeric'], self::SAFE),
            new TwigFunction('array_unique', [PhpEncapsulationRuntime::class, 'twigFunction_array_unique'], self::SAFE),
            new TwigFunction('array_filter', [PhpEncapsulationRuntime::class, 'twigFunction_array_filter'], self::SAFE),
            new TwigFunction('function_exists', [PhpEncapsulationRuntime::class, 'twigFunction_function_exists'], self::SAFE),
            new TwigFunction('ob_start', [PhpEncapsulationRuntime::class, 'twigFunction_ob_start'], self::SAFE),
            new TwigFunction('ob_get_clean', [PhpEncapsulationRuntime::class, 'twigFunction_ob_get_clean'], self::SAFE),
            new TwigFunction('end', [PhpEncapsulationRuntime::class, 'twigFunction_end'], self::SAFE),
            new TwigFunction('getStatic', [PhpEncapsulationRuntime::class, 'twigFunction_getStatic'], self::SAFE),
            new TwigFunction('setStatic', [PhpEncapsulationRuntime::class, 'twigFunction_setStatic'], self::SAFE),
            new TwigFunction('execStatic', [PhpEncapsulationRuntime::class, 'twigFunction_execStatic'], self::SAFE),
            new TwigFunction('gettype', [PhpEncapsulationRuntime::class, 'twigFunction_gettype'], self::SAFE),
            new TwigFunction('isRouteDefined', [PhpEncapsulationRuntime::class, 'twigFunction_isRouteDefined'], self::SAFE),
        ];
    }

    public function getTests(): array
    {
        return [
            new TwigTest('instanceOf', [PhpEncapsulationRuntime::class, 'twigTest_instanceOf']),
            new TwigTest('typeOf', [PhpEncapsulationRuntime::class, 'twigTest_typeOf']),
            new TwigTest('startWith', [PhpEncapsulationRuntime::class, 'twigTest_startWith']),
        ];
    }
}
