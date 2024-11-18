<?php

namespace App\Twig\Runtime;

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
use Twig\Extension\RuntimeExtensionInterface;

class ContactCoreRuntime implements RuntimeExtensionInterface
{
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
    ) {}

    /**
     * TWIG Extension Filters
     */

     public function tFilter_html_entity_decode($str): mixed
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
 
     public function tFilter_boolRtn($val): bool
     {
         return (bool) $val;
     }
 
     public function tFilter_xor($val1, $val2): bool
     {
         return $val1 xor $val2;
     }

     public function tFilter_toString($value): string
     {
         $rtrStr = "";
         switch(gettype($value)) {
             case 'array':
                 $rtrStr = implode(', ', $value);
                 break;
         }
 
         return $rtrStr;
     }
 

    /**
     * TWIG Extension Functions
     */
    public function tFunction_assetTheme(string $path, string $theme = null): string
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

    public function tFunction_isExtensionInstall($name): bool
    {
        return $this->extensionConfig->isExtensionInstall($name);
    }

    public function tFunction_mailError() :int
    {
        return $this->courrielsDbInfos->countCourrielsToSend();
    }

    public function tFunction_template_exist($template_name): bool
    {
        /* $template_name : @namespace/local_path/name.html.twig */
        return (bool) $this->twig_env->getLoader()->exists($template_name);
    }

    public function tFunction_extConfig_get(string $path): mixed
    {
        return $this->extensionConfig->get($path);
    }

    public function tFunction_strpos($chaine, $part, $offset = 0): bool|int
    {
        return strpos($chaine, $part, $offset);
    }

    public function tFunction_str_replace($search, $replace, $subject, $count = ''): array|string
    {
        if (!empty($count)) {
            return str_replace($search, $replace, $subject, $count);
        }
        return str_replace($search, $replace, $subject);
    }

    public function tFunction_printf($format, ...$values): int
    {
        return printf($format, ...$values);
    }

    public function tFunction_sprintf($format, ...$values): string
    {
        return sprintf($format, ...$values);
    }

    public function tFunction_is_bool($param): bool
    {
        return is_bool($param);
    }

    public function tFunction_array_to_string($array = []): string
    {
        $str = '';
        foreach ($array as $key => $item) {
            $str .= $key.'= "'.$item.'";';
        }
        return $str;
    }

    public function tFunction_preg_replace($pattern, $remplacement, $subject, $limit = -1, $count = null): array|string|null
    {
        return preg_replace($pattern, $remplacement, $subject, $limit, $count);
    }

    public function tFunction_implode($separator = '', ?array $array = null): string
    {
        return implode($separator, $array);
    }

    public function tFunction_is_numeric($value): bool
    {
        return is_numeric($value);
    }

    public function tFunction_array_unique(array $array, int $flags = SORT_STRING): array
    {
        return array_unique($array, $flags);
    }

    public function tFunction_array_filter(array $args): array
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

    public function tFunction_function_exists(string $function): bool
    {
        return function_exists($function);
    }

    public function tFunction_ob_start(): void
    {
        ob_start();
    }

    public function tFunction_ob_get_clean(): bool|string
    {
        return ob_get_clean();
    }

    public function tFunction_end(array $tableau)
    {
        return end($tableau);
    }

    public function tFunction_getStatic($object, $var_name)
    {
        $obj = new \ReflectionClass($object);
        return $obj->getStaticPropertyValue($var_name);
    }

    public function tFunction_setStatic($object, $var_name, $var_value): void
    {
        $object::$var_name = $var_value;
    }

    public function tFunction_execStatic($object, $method_name, $methord_args = null)
    {
        return call_user_func_array($object::$method_name, $methord_args);
    }

    public function tFunction_gettype($var)
    {
        return gettype($var);
    }

    public function tFunction_isRouteDefined(string $route): bool
    {
        $routes = $this->router->getRouteCollection();
        return (bool) $routes->get($route);
    }

    public function tFunction_buildBreadcrumbs(string $menuName = null, Request $request = null, bool $bs5 = false)
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

    public function tFunction_buildArrayBreadcrumbs(string $menuName = null, Request $request = null, bool $bs5 = false)
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

    public function tFunction_phpversion()
    {
        return phpversion();
    }    

    /**
     * TWIG Extensions Tests
     */

     public function tTest_instanceOf($object, $class): bool
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
    public function tTest_typeOf($var, $type_test = null): bool
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

    public function tTest_startWith($field, $string): bool
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
