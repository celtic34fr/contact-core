<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\ContactCoreRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

class ContactCoreExtension extends AbstractExtension
{
    const SAFE = ['is_safe' => ['html']];

    public function getFilters(): array
    {
        return [
            new TwigFilter('force_to_int', fn ($value) => intval($value), self::SAFE),
            new TwigFilter('html_entity_decode', [ContactCoreRuntime::class, 'tFilter_html_entity_decode'], self::SAFE),
            new TwigFilter('bool', [ContactCoreRuntime::class, 'tFilter_boolRtn'], self::SAFE),
            new TwigFilter('xor', [ContactCoreRuntime::class, 'tFilter_xor'], self::SAFE),
            new TwigFilter('parseInt', fn ($value) => intval($value), self::SAFE),
            new TwigFilter('parseFloat', fn ($value) => floatval($value), self::SAFE),
            new TwigFilter('json_decode',
                fn ($value) => json_decode(str_replace('\\', '', $value), true), self::SAFE),
            new TwigFilter('toString', [ContactCoreRuntime::class, 'tFilter_toString'], self::SAFE),
            ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('assetTheme', [ContactCoreRuntime::class, 'tFunction_assetTheme']),

            new TwigFunction('isExtensionInstall', [ContactCoreRuntime::class, 'tFunction_isExtensionInstall'], self::SAFE),
            new TwigFunction('mailError', [ContactCoreRuntime::class, 'tFunction_mailError'], self::SAFE),
            new TwigFunction('template_exist', [ContactCoreRuntime::class, 'tFunction_template_exist', self::SAFE]),
            new TwigFunction('extConfig_get', [ContactCoreRuntime::class, 'tFunction_extConfig_get', self::SAFE]),

            new TwigFunction('strpos', [ContactCoreRuntime::class, 'tFunction_strpos'], self::SAFE),
            new TwigFunction('str_replace', [ContactCoreRuntime::class, 'tFunction_str_replace'], self::SAFE),
            new TwigFunction('printf', [ContactCoreRuntime::class, 'tFunction_printf'], self::SAFE),
            new TwigFunction('sprintf', [ContactCoreRuntime::class, 'tFunction_sprintf'], self::SAFE),
            new TwigFunction('is_bool', [ContactCoreRuntime::class, 'tFunction_is_bool'], self::SAFE),
            new TwigFunction('array_to_string', [ContactCoreRuntime::class, 'tFunction_array_to_string'], self::SAFE),
            new TwigFunction('preg_replace', [ContactCoreRuntime::class, 'tFunction_preg_replace'], self::SAFE),
            new TwigFunction('implode', [ContactCoreRuntime::class, 'tFunction_implode'], self::SAFE),
            new TwigFunction('is_numeric', [ContactCoreRuntime::class, 'tFunction_is_numeric'], self::SAFE),
            new TwigFunction('array_unique', [ContactCoreRuntime::class, 'tFunction_array_unique'], self::SAFE),
            new TwigFunction('array_filter', [ContactCoreRuntime::class, 'tFunction_array_filter'], self::SAFE),
            new TwigFunction('function_exists', [ContactCoreRuntime::class, 'tFunction_function_exists'], self::SAFE),
            new TwigFunction('ob_start', [ContactCoreRuntime::class, 'tFunction_ob_start'], self::SAFE),
            new TwigFunction('ob_get_clean', [ContactCoreRuntime::class, 'tFunction_ob_get_clean'], self::SAFE),
            new TwigFunction('end', [ContactCoreRuntime::class, 'tFunction_end'], self::SAFE),
            new TwigFunction('getStatic', [ContactCoreRuntime::class, 'tFunction_getStatic'], self::SAFE),
            new TwigFunction('setStatic', [ContactCoreRuntime::class, 'tFunction_setStatic'], self::SAFE),
            new TwigFunction('execStatic', [ContactCoreRuntime::class, 'tFunction_execStatic'], self::SAFE),
            new TwigFunction('gettype', [ContactCoreRuntime::class, 'tFunction_gettype'], self::SAFE),
            new TwigFunction('isRouteDefined', [ContactCoreRuntime::class, 'tFunction_isRouteDefined'], self::SAFE),

            new TwigFunction('buildBreadcrumbs', [ContactCoreRuntime::class, 'tFunction_buildBreadcrumbs']),
            new TwigFunction('buildArrayBreadcrumbs', [ContactCoreRuntime::class, 'tFunction_buildArrayBreadcrumbs']),

            new TwigFunction('phpversion', [ContactCoreRuntime::class, 'tFunction_phpversion']),
        ];
    }

    public function getTests(): array
    {
        return [
            new TwigTest('instanceOf', [ContactCoreRuntime::class, 'tTest_instanceOf']),
            new TwigTest('typeOf', [ContactCoreRuntime::class, 'tTest_typeOf']),
            new TwigTest('startWith', [ContactCoreRuntime::class, 'tTest_startWith']),
        ];
    }
}
