<?php

namespace Celtic34fr\ContactCore\Twig\Extension;

use Celtic34fr\ContactCore\Twig\Runtime\ContactCoreRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

class ContactCoreExtension extends AbstractExtension
{
    const SAFE = ['is_safe' => ['html']];

    public function __construct(
        private ContactCoreRuntime $runtime
    )
    {}

    public function getFilters(): array
    {
        return [
            new TwigFilter('force_to_int', fn ($value) => intval($value), self::SAFE),
            new TwigFilter('html_entity_decode', [$this->runtime, 'tFilter_html_entity_decode'], self::SAFE),
            new TwigFilter('bool', [$this->runtime, 'tFilter_boolRtn'], self::SAFE),
            new TwigFilter('xor', [$this->runtime, 'tFilter_xor'], self::SAFE),
            new TwigFilter('parseInt', fn ($value) => intval($value), self::SAFE),
            new TwigFilter('parseFloat', fn ($value) => floatval($value), self::SAFE),
            new TwigFilter('json_decode',
                fn ($value) => json_decode(str_replace('\\', '', $value), true), self::SAFE),
            new TwigFilter('toString', [$this->runtime, 'tFilter_toString'], self::SAFE),
            ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('assetTheme', [$this->runtime, 'tFunction_assetTheme']),

            new TwigFunction('isExtensionInstall', [$this->runtime, 'tFunction_isExtensionInstall'], self::SAFE),
            new TwigFunction('mailError', [$this->runtime, 'tFunction_mailError'], self::SAFE),
            new TwigFunction('template_exist', [$this->runtime, 'tFunction_template_exist', self::SAFE]),
            new TwigFunction('extConfig_get', [$this->runtime, 'tFunction_extConfig_get', self::SAFE]),

            new TwigFunction('strpos', [$this->runtime, 'tFunction_strpos'], self::SAFE),
            new TwigFunction('str_replace', [$this->runtime, 'tFunction_str_replace'], self::SAFE),
            new TwigFunction('printf', [$this->runtime, 'tFunction_printf'], self::SAFE),
            new TwigFunction('sprintf', [$this->runtime, 'tFunction_sprintf'], self::SAFE),
            new TwigFunction('is_bool', [$this->runtime, 'tFunction_is_bool'], self::SAFE),
            new TwigFunction('array_to_string', [$this->runtime, 'tFunction_array_to_string'], self::SAFE),
            new TwigFunction('preg_replace', [$this->runtime, 'tFunction_preg_replace'], self::SAFE),
            new TwigFunction('implode', [$this->runtime, 'tFunction_implode'], self::SAFE),
            new TwigFunction('is_numeric', [$this->runtime, 'tFunction_is_numeric'], self::SAFE),
            new TwigFunction('array_unique', [$this->runtime, 'tFunction_array_unique'], self::SAFE),
            new TwigFunction('array_filter', [$this->runtime, 'tFunction_array_filter'], self::SAFE),
            new TwigFunction('function_exists', [$this->runtime, 'tFunction_function_exists'], self::SAFE),
            new TwigFunction('ob_start', [$this->runtime, 'tFunction_ob_start'], self::SAFE),
            new TwigFunction('ob_get_clean', [$this->runtime, 'tFunction_ob_get_clean'], self::SAFE),
            new TwigFunction('end', [$this->runtime, 'tFunction_end'], self::SAFE),
            new TwigFunction('getStatic', [$this->runtime, 'tFunction_getStatic'], self::SAFE),
            new TwigFunction('setStatic', [$this->runtime, 'tFunction_setStatic'], self::SAFE),
            new TwigFunction('execStatic', [$this->runtime, 'tFunction_execStatic'], self::SAFE),
            new TwigFunction('gettype', [$this->runtime, 'tFunction_gettype'], self::SAFE),
            new TwigFunction('isRouteDefined', [$this->runtime, 'tFunction_isRouteDefined'], self::SAFE),

            new TwigFunction('buildBreadcrumbs', [$this->runtime, 'tFunction_buildBreadcrumbs']),
            new TwigFunction('buildArrayBreadcrumbs', [$this->runtime, 'tFunction_buildArrayBreadcrumbs']),

            new TwigFunction('phpversion', [$this->runtime, 'tFunction_phpversion']),
        ];
    }

    public function getTests(): array
    {
        return [
            new TwigTest('instanceOf', [$this->runtime, 'tTest_instanceOf']),
            new TwigTest('typeOf', [$this->runtime, 'tTest_typeOf']),
            new TwigTest('startWith', [$this->runtime, 'tTest_startWith']),
        ];
    }
}
