<?php

namespace Celtic34fr\ContactCore;

use Bolt\Configuration\Config;
use Bolt\Twig\AssetsExtension;
use Celtic34fr\ContactCore\Service\CourrielsDbInfos;
use Celtic34fr\ContactCore\Service\ExtensionConfig;
use Celtic34fr\ContactCore\Service\ToolsService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

class TwigExtension extends AbstractExtension
{
    const SAFE = ['is_safe' => ['html']];

    public function __construct(
        private AssetsExtension $asset,
        private ContainerInterface $container,
        private Filesystem $filesystem,
        private Config $config,
        private ExtensionConfig $extensionConfig,
        private CourrielsDbInfos $courrielsDbInfos,
        private Environment $twig_env,
        private RouterInterface $router,
        private UrlGeneratorInterface $urlGenerator,
        private ToolsService $tools,
    )
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('force_to_int', fn ($value) => intval($value), self::SAFE),
            new TwigFilter('html_entity_decode', [$this, 'twigFilter_html_entity_decode'], self::SAFE),
            new TwigFilter('bool', [$this, 'twigFilter_boolRtn'], self::SAFE),
            new TwigFilter('xor', [$this, 'twigFilter_xor'], self::SAFE),
            new TwigFilter('parseInt', fn ($value) => intval($value), self::SAFE),
            new TwigFilter('parseFloat', fn ($value) => floatval($value), self::SAFE),
            new TwigFilter('json_decode',
                fn ($value) => json_decode(str_replace('\\', '', $value), true), self::SAFE),
        ];
    }


    public function getFunctions(): array
    {
        return [
            new TwigFunction('assetTheme', [$this, 'twigFunction_assetTheme']),

            new TwigFunction('isExtensionInstall', [$this, 'twigFunction_isExtensionInstall'], self::SAFE),
            new TwigFunction('mailError', [$this, 'twigFunction_mailError'], self::SAFE),
            new TwigFunction('template_exist', [$this, 'twigFunction_template_exist', self::SAFE]),
            new TwigFunction('extConfig_get', [$this, 'twigFunction_extConfig_get', self::SAFE]),

            new TwigFunction('strpos', [$this, 'twigFunction_strpos'], self::SAFE),
            new TwigFunction('str_replace', [$this, 'twigFunction_str_replace'], self::SAFE),
            new TwigFunction('printf', [$this, 'twigFunction_printf'], self::SAFE),
            new TwigFunction('sprintf', [$this, 'twigFunction_sprintf'], self::SAFE),
            new TwigFunction('is_bool', [$this, 'twigFunction_is_bool'], self::SAFE),
            new TwigFunction('array_to_string', [$this, 'twigFunction_array_to_string'], self::SAFE),
            new TwigFunction('preg_replace', [$this, 'twigFunction_preg_replace'], self::SAFE),
            new TwigFunction('implode', [$this, 'twigFunction_implode'], self::SAFE),
            new TwigFunction('is_numeric', [$this, 'twigFunction_is_numeric'], self::SAFE),
            new TwigFunction('array_unique', [$this, 'twigFunction_array_unique'], self::SAFE),
            new TwigFunction('array_filter', [$this, 'twigFunction_array_filter'], self::SAFE),
            new TwigFunction('function_exists', [$this, 'twigFunction_function_exists'], self::SAFE),
            new TwigFunction('ob_start', [$this, 'twigFunction_ob_start'], self::SAFE),
            new TwigFunction('ob_get_clean', [$this, 'twigFunction_ob_get_clean'], self::SAFE),
            new TwigFunction('end', [$this, 'twigFunction_end'], self::SAFE),
            new TwigFunction('getStatic', [$this, 'twigFunction_getStatic'], self::SAFE),
            new TwigFunction('setStatic', [$this, 'twigFunction_setStatic'], self::SAFE),
            new TwigFunction('execStatic', [$this, 'twigFunction_execStatic'], self::SAFE),
            new TwigFunction('gettype', [$this, 'twigFunction_gettype'], self::SAFE),
            new TwigFunction('isRouteDefined', [$this, 'twigFunction_isRouteDefined'], self::SAFE),

            new TwigFunction('buildBreadcrumbs', [$this, 'twigFunction_buildBreadcrumbs']),
            new TwigFunction('buildArrayBreadcrumbs', [$this, 'twigFunction_buildArrayBreadcrumbs']),
        ];
    }

    public function getTests(): array
    {
        return [
            new TwigTest('instanceOf', [$this, 'twigTest_instanceOf']),
            new TwigTest('typeOf', [$this, 'twigTest_typeOf']),
            new TwigTest('startWith', [$this, 'twigTest_startWith']),
        ];
    }


    /**
     * TWIG Extension Filters
     */

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



    /**
     * TWIG Extension Functions
     */
    public function twigFunction_assetTheme(string $path, string $theme = null): string
    {
        if (null === $theme) {
            $theme = $this->config->get('general/theme');
        }
        $theme_path = 'theme/'.$theme.'/'.$path;

        $search_path = $this->container->getParameter('kernel.project_dir').'/';
        $search_path .= $this->container->getParameter('bolt.public_folder').'/theme/';
        $search_path .= $theme;
        if ($this->existFolder($search_path)) {
            /** cas ou le thème est connu */
            $full_path = $search_path.'/'.$path;
            if ($this->filesystem->exists($full_path)) {
                /* cas ou le fichier recherché existe */
                return sprintf(
                    '%s://%s%s',
                    isset($_SERVER['HTTPS']) && 'off' != $_SERVER['HTTPS'] ? 'https' : 'http',
                    $_SERVER['SERVER_NAME'],
                    $this->asset->getAssetUrl($theme_path, null)
                );
            }
        }
        throw new \Exception("resource thème $theme et/ou fichier $path inconnu ");
    }


    public function twigFunction_isExtensionInstall($name): bool
    {
        return $this->extensionConfig->isExtnsionInstall($name);
    }

    public function twigFunction_mailError() :int
    {
        return $this->courrielsDbInfos->countCourrielsToSend();
    }

    public function twigFunction_template_exist($template_name): bool
    {
        /* $template_name : @namespace/local_path/name.html.twig */
        return (bool) $this->twig_env->getLoader()->exists($template_name);
    }

    public function twigFunction_extConfig_get(string $path): mixed
    {
        return $this->extensionConfig->get($path);
    }

    public function twigFilter_toString($value): string
    {
        $rtrStr = "";
        switch(gettype($value)) {
            case 'array':
                $rtrStr = implode(', ', $value);
                break;
        }

        return $rtrStr;
    }

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

    public function twigFunction_buildBreadcrumbs(string $menuName = null, Request $request = null, bool $bs5 = false)
    {
        $menu = [];
        $uri = "";
        $allMenus = $this->config->get('menu');

        if ($menuName && $allMenus) {
            $menu = $allMenus[$menuName];
        }
        if ($request) {
            $uri = substr($request->getRequestUri(), 1);
            $baseUrl = $request->getBaseUrl();
        }

        return $this->tools->generateBreadcrumbsFromMenu($menu, $uri, $baseUrl, $bs5);
    }

    public function twigFunction_buildArrayBreadcrumbs(string $menuName = null, Request $request = null, bool $bs5 = false)
    {
        $menu = [];
        $uri = "";
        $allMenus = $this->config->get('menu');

        if ($menuName && $allMenus) {
            $menu = $allMenus[$menuName];
        }
        if ($request) {
            $uri = substr($request->getRequestUri(), 1);
            $baseUrl = $request->getBaseUrl();
        }

        return $this->tools->generateBreadcrumbsFromMenuArray($menu, $uri, $baseUrl, $bs5);
    }

    /**
     * TWIG Extensions Tests
     */

    public function twigTest_instanceOf($object, $class): bool
    {
        $reflectionClass = new \ReflectionClass($class);
        return $reflectionClass->isInstance($object);
    }

    /**
     * Description of KALANTwigExtension
     * => permet le test sur le type d'un champs dans un template.
     * @param null $type_test
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



    /**
     * Privates Functions
     */

     private function existFolder(string $path_folder): bool
     {
         return $this->filesystem->exists($path_folder);
     }
 
     private function str_replace($search, $replace, $subject, $count = '')
     {
         if (!empty($count)) {
             return str_replace($search, $replace, $subject, $count);
         }
         return str_replace($search, $replace, $subject);
     }
 }