<?php

namespace Celtic34fr\ContactCore\Twig\Runtime;

use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\RuntimeExtensionInterface;

/** ensembles d'extensions d'encapsulation de fonctions et méthode Php pour accès direct sous TWIG */
class PhpEncapsulationRuntime implements RuntimeExtensionInterface
{
    public function __construct(private RouterInterface $router)
    {
    }

    /**
     * méthodes d'encapsulation de fonction ou traitements en Php.
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
        $str = '';
        foreach ($array as $key => $item) {
            $str .= $key.'= "'.$item.'";';
        }
        return $str;
    }

    public function twigFunction_preg_replace($pattern, $remplacement, $subject, $limit = -1, $count = null): array|string|null
    {
        return preg_replace($pattern, $remplacement, $subject, $limit, $count);
    }

    public function twigFunction_implode($separator = '', ?array $array = null): string
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

    public function twigFunction_isRouteDefined(string $route): bool
    {
        $routes = $this->router->getRouteCollection();
        return (bool) $routes->get($route);
    }

    /** Twig Filters */
    public function twigFilter_html_entity_decode($str): mixed
    {
        $utf8_ansi2 = [
            'u00c0' => 'À',
            'u00c1' => 'Á',
            'u00c2' => 'Â',
            'u00c3' => 'Ã',
            'u00c4' => 'Ä',
            'u00c5' => 'Å',
            'u00c6' => 'Æ',
            'u00c7' => 'Ç',
            'u00c8' => 'È',
            'u00c9' => 'É',
            'u00ca' => 'Ê',
            'u00cb' => 'Ë',
            'u00cc' => 'Ì',
            'u00cd' => 'Í',
            'u00ce' => 'Î',
            'u00cf' => 'Ï',
            'u00d1' => 'Ñ',
            'u00d2' => 'Ò',
            'u00d3' => 'Ó',
            'u00d4' => 'Ô',
            'u00d5' => 'Õ',
            'u00d6' => 'Ö',
            'u00d8' => 'Ø',
            'u00d9' => 'Ù',
            'u00da' => 'Ú',
            'u00db' => 'Û',
            'u00dc' => 'Ü',
            'u00dd' => 'Ý',
            'u00df' => 'ß',
            'u00e0' => 'à',
            'u00e1' => 'á',
            'u00e2' => 'â',
            'u00e3' => 'ã',
            'u00e4' => 'ä',
            'u00e5' => 'å',
            'u00e6' => 'æ',
            'u00e7' => 'ç',
            'u00e8' => 'è',
            'u00e9' => 'é',
            'u00ea' => 'ê',
            'u00eb' => 'ë',
            'u00ec' => 'ì',
            'u00ed' => 'í',
            'u00ee' => 'î',
            'u00ef' => 'ï',
            'u00f0' => 'ð',
            'u00f1' => 'ñ',
            'u00f2' => 'ò',
            'u00f3' => 'ó',
            'u00f4' => 'ô',
            'u00f5' => 'õ',
            'u00f6' => 'ö',
            'u00f8' => 'ø',
            'u00f9' => 'ù',
            'u00fa' => 'ú',
            'u00fb' => 'û',
            'u00fc' => 'ü',
            'u00fd' => 'ý',
            'u00ff' => 'ÿ'];

        foreach ($utf8_ansi2 as $key => $val) {
            $pos = strpos($str, $key);
            if (false !== $pos) {
                $str = $this->str_replace($key, $val, $str);
            }
        }
        return $str;
    }

    public function twigFilter_boolRtn($val): bool
    {
        return (bool) $val;
    }

    public function twigFilter_xor($val1, $val2): bool
    {
        return $val1 xor $val2;
    }

    /** Twig Tests */
    public function twigTest_instanceOf($object, $class): bool
    {
        $reflectionClass = new \ReflectionClass($class);
        return $reflectionClass->isInstance($object);
    }

    /**
     * Description of KALANTwigExtension
     * => permet le test sur le type d'un champs dans un template.
     *
     * @param null $type_test
     *
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
                return $var instanceof \DateTime;
                break;
        }
    }

    public function twigTest_startWith($field, $string): bool
    {
        return 0 == strpos($field, $string) && false !== strpos($field, $string);
    }

    private function str_replace($search, $replace, $subject, $count = '')
    {
        if (!empty($count)) {
            return str_replace($search, $replace, $subject, $count);
        }
        return str_replace($search, $replace, $subject);
    }
}
