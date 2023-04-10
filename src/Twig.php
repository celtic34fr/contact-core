<?php

declare(strict_types=1);

namespace Celtic34fr\ContactCore;

use Celtic34fr\ContactCore\Service\ExtensionConfig;
use Celtic34fr\ContactCore\Service\CourrielsDbInfos;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

class Twig extends AbstractExtension
{
    public function __construct(private ExtensionConfig $extensionConfig, private RouterInterface $router,
                                private CourrielsDbInfos $courrielsDbInfos)
    {
    }

    /**
     * Register Twig functions.
     */
    public function getFunctions(): array
    {
        $safe = [
            'is_safe' => ['html'],
        ];

        return [
            new TwigFunction('strpos', [$this, 'twigFunction_strpos'], $safe),
            new TwigFunction('str_replace', [$this, 'twigFunction_str_replace']),
            new TwigFunction('printf', [$this, 'twigFunction_printf']),
            new TwigFunction('sprintf', [$this, 'twigFunction_sprintf']),
            new TwigFunction('is_bool', [$this, 'twigFunction_is_bool']),
            new TwigFunction('array_to_string', [$this, 'twigFunction_array_to_string']),
            new TwigFunction('preg_replace', [$this, 'twigFunction_preg_replace']),
//            new TwigFunction('template_exist', [$this, 'twigFunction_template_exist']), -> à récréer par rapport à Bolt CMS
            new TwigFunction('implode', [$this, 'twigFunction_implode']),
            new TwigFunction('is_numeric', [$this, 'twigFunction_is_numeric']),
            new TwigFunction('array_unique', [$this, 'twigFunction_array_unique']),
            new TwigFunction('array_filter', [$this, 'twigFunction_array_filter']),
            new TwigFunction('function_exists', [$this, 'twigFunction_function_exists']),
            new TwigFunction('ob_start', [$this, 'twigFunction_ob_start']),
            new TwigFunction('ob_get_clean', [$this, 'twigFunction_ob_get_clean']),
            new TwigFunction('end', [$this, 'twigFunction_end']),
            new TwigFunction('getStatic', [$this, 'twigFunction_getStatic']),
            new TwigFunction('setStatic', [$this, 'twigFunction_setStatic']),
            new TwigFunction('execStatic', [$this, 'twigFunction_execStatic']),
            new TwigFunction('gettype', [$this, 'twigFunction_gettype']),
            new TwigFunction('isExtensionInstall', [$this, 'twigFunction_isExtensionInstall']),
            new TwigFunction('isRouteDefined', [$this, 'twigFunction_isRouteDefined']),

            new TwigFunction('mailError', [$this, 'twigFunction_mailError'], $safe),
        ];
    }

    /**
     * Register Twig filters.
     */
    public function getFilters(): array
    {
        $safe = [
            'is_safe' => ['html'],
        ];

        return [
            new TwigFilter('force_to_int', fn($value) => intval($value)),
            new TwigFilter('html_entity_decode', [$this, 'twigFilter_html_entity_decode']),
            new TwigFilter('bool', [$this, 'twigFilter_boolRtn']),
            new TwigFilter('xor', [$this, 'twigFilter_xor']),
            new TwigFilter('parseInt', fn($value) => intval($value)),
            new TwigFilter('parseFloat', fn($value) => floatval($value)),
            new TwigFilter('json_decode',
                fn($value) => json_decode(str_replace('\\', '', $value), true)),
        ];
    }

    public function getTests(): array
    {
        return array(
            new TwigTest('instanceOf', [$this, 'twigTest_instanceOf']),
            new TwigTest('typeOf', [$this, 'twigTest_typeOf']),
            new TwigTest('startWith', [$this, 'twigTest_startWith']),
        );
    }

    /** Twig Functions */

    /**
     * méthodes d'encapsulation de fonction ou traitements en Php
     */
    public function twigFunction_strpos($chaine, $part, $offset = 0): bool|int
    {
        return strpos($chaine, $part, $offset);
    }

    public function twigFunction_str_replace($search, $replace, $subject, $count = ''): array|string
    {
        if (!empty($count)) {
            return str_replace($search, $replace, $subject, $count);
        }
        return str_replace($search, $replace, $subject);
    }

    public function twigFunction_printf($format, ...$values): int
    {
        return printf($format, ...$values);
    }

    public function twigFunction_sprintf($format, ...$values): string
    {
        return sprintf($format, ...$values);
    }

    public function twigFunction_is_bool($param): bool
    {
        return is_bool($param);
    }

    public function twigFunction_array_to_string($array = []): string
    {
        $str = "";
        foreach ($array as $key => $item) {
            $str .= $key . '= "' . $item . '";';
        }
        return $str;
    }

    public function twigFunction_preg_replace($pattern, $remplacement, $subject, $limit = -1, $count = null): array|string|null
    {
        return preg_replace($pattern, $remplacement, $subject, $limit, $count);
    }

    public function twigFunction_implode($separator = "", ?array $array = null): string
    {
        return implode($separator, $array);
    }

    public function twigFunction_is_numeric($value): bool
    {
        return is_numeric($value);
    }

    public function twigFunction_array_unique(array $array, int $flags = SORT_STRING): array
    {
        return array_unique($array, $flags);
    }

    public function twigFunction_array_filter(array $args): array
    {
        if (array_key_exists('callback', $args) && $args['callback']) {
            if (array_key_exists('mode', $args)) {
                return array_filter($args['array'], $args['callback'], $args['mode']);
            } else {
                return array_filter($args['array'], $args['callback']);
            }
        } else {
            if (array_key_exists('mode', $args)) {
                return array_filter($args['array'], null, $args['mode']);
            } else {
                return array_filter($args['array']);
            }
        }
    }

    public function twigFunction_function_exists(string $function): bool
    {
        return function_exists($function);
    }

    public function twigFunction_ob_start(): void
    {
        ob_start();
    }

    public function twigFunction_ob_get_clean(): bool|string
    {
        return ob_get_clean();
    }

    public function twigFunction_end(array $tableau)
    {
        return end($tableau);
    }

    public function twigFunction_getStatic($object, $var_name)
    {
        $obj = new \ReflectionClass($object);
        return $obj->getStaticPropertyValue($var_name);
    }

    public function twigFunction_setStatic($object, $var_name, $var_value): void
    {
        $object::$var_name = $var_value;
    }

    public function twigFunction_execStatic($object, $method_name, $methord_args = null)
    {
        return call_user_func_array($object::$method_name, $methord_args);
    }

    public function twigFunction_gettype($var)
    {
        return gettype($var);
    }

    #[Pure] public function twigFunction_isExtensionInstall($name): bool
    {
        return $this->extensionConfig->isExtnsionInstall($name);
    }

    public function twigFunction_isRouteDefined(string $route): bool
    {
        $routes = $this->router->getRouteCollection();
        return (bool)$routes->get($route);
    }

    public function twigFunction_mailError()
    {
        return $this->courrielsDbInfos->countCourrielsToSend();
    }

    /** Twig Filters */

    /**
     * @param $str
     * @return mixed
     */
    public function twigFilter_html_entity_decode($str): mixed
    {

        $utf8_ansi2 = array(
            "u00c0" => "À",
            "u00c1" => "Á",
            "u00c2" => "Â",
            "u00c3" => "Ã",
            "u00c4" => "Ä",
            "u00c5" => "Å",
            "u00c6" => "Æ",
            "u00c7" => "Ç",
            "u00c8" => "È",
            "u00c9" => "É",
            "u00ca" => "Ê",
            "u00cb" => "Ë",
            "u00cc" => "Ì",
            "u00cd" => "Í",
            "u00ce" => "Î",
            "u00cf" => "Ï",
            "u00d1" => "Ñ",
            "u00d2" => "Ò",
            "u00d3" => "Ó",
            "u00d4" => "Ô",
            "u00d5" => "Õ",
            "u00d6" => "Ö",
            "u00d8" => "Ø",
            "u00d9" => "Ù",
            "u00da" => "Ú",
            "u00db" => "Û",
            "u00dc" => "Ü",
            "u00dd" => "Ý",
            "u00df" => "ß",
            "u00e0" => "à",
            "u00e1" => "á",
            "u00e2" => "â",
            "u00e3" => "ã",
            "u00e4" => "ä",
            "u00e5" => "å",
            "u00e6" => "æ",
            "u00e7" => "ç",
            "u00e8" => "è",
            "u00e9" => "é",
            "u00ea" => "ê",
            "u00eb" => "ë",
            "u00ec" => "ì",
            "u00ed" => "í",
            "u00ee" => "î",
            "u00ef" => "ï",
            "u00f0" => "ð",
            "u00f1" => "ñ",
            "u00f2" => "ò",
            "u00f3" => "ó",
            "u00f4" => "ô",
            "u00f5" => "õ",
            "u00f6" => "ö",
            "u00f8" => "ø",
            "u00f9" => "ù",
            "u00fa" => "ú",
            "u00fb" => "û",
            "u00fc" => "ü",
            "u00fd" => "ý",
            "u00ff" => "ÿ");

        foreach ($utf8_ansi2 as $key => $val) {
            $pos = strpos($str, $key);
            if ($pos !== false) {
                $str = $this->str_replace($key, $val, $str);
            }
        }
        return $str;
    }

    /**
     * @param $val
     * @return bool
     */
    public function twigFilter_boolRtn($val): bool
    {
        return (bool)$val;
    }

    /**
     * @param $val1
     * @param $val2
     * @return bool
     */
    public function twigFilter_xor($val1, $val2): bool
    {
        return $val1 xor $val2;
    }

    /** Twig Tests */

    /**
     * @param $object
     * @param $class
     * @return bool
     */
    public function twigTest_instanceOf($object, $class): bool
    {
        $reflectionClass = new \ReflectionClass($class);
        return $reflectionClass->isInstance($object);
    }

    /**
     * Description of KALANTwigExtension
     * => permet le test sur le type d'un champs dans un template
     *
     * @param $var
     * @param null $type_test
     * @return bool
     * @author LAURE
     */
    public function twigTest_typeOf($var, $type_test = null): bool
    {

        switch ($type_test) {
            default:
                return false;
                break;
            case 'array':
                return is_array($var);
                break;
            case 'bool':
                return is_bool($var);
                break;
            case 'float':
                return is_float($var);
                break;
            case 'int':
                return is_int($var);
                break;
            case 'numeric':
                return is_numeric($var);
                break;
            case 'object':
                return is_object($var);
                break;
            case 'scalar':
                return is_scalar($var);
                break;
            case 'string':
                return is_string($var);
                break;
            case 'datetime':
                return ($var instanceof \DateTime);
                break;
        }
    }

    /**
     * @param $field
     * @param $string
     * @return bool
     */
    public function twigTest_startWith($field, $string): bool
    {
        return (strpos($field, $string) == 0 && strpos($field, $string) !== false);
    }
}
