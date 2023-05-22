<?php

namespace Celtic34fr\ContactCore\Service;

use Bolt\Configuration\Config;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;

class ExtensionConfig
{
    protected array $extConfig;
    protected array $extInstall;
    protected string $projectDir;

    public function __construct(private KernelInterface $appKernel, private Config $config)
    {
        $this->projectDir = $this->appKernel->getProjectDir();
        $this->initialize($this->projectDir);
    }

    public function initialize(string $projectDir)
    {
        $this->projectDir = $projectDir;
        $sources = $this->projectDir.'/config';
        if (is_dir($sources.'/extensions')) {
            $sources .= '/extensions';
            $filesNames = scandir($sources);
            foreach ($filesNames as $fileName) {
                if (!is_dir($sources.'/'.$fileName)) {
                    $name = substr($fileName, 0, strpos($fileName, '.'));
                    $this->extInstall[] = $name;
                    $content = file_get_contents($sources.'/'.$fileName);
                    $arrays = Yaml::parse($content);
                    $this->extConfig[$name] = $arrays;
                }
            }
        }
    }

    /**
     * @param string $path
     */
    public function get(string $path_base): mixed
    {
        $paths = explode('/', $path_base);
        $path = $path_base;
        $pathLength = sizeof($paths);
        $idx = 0;
        $rslt = null;
        $localConfig = $this->extConfig;
        $path = array_shift($paths);
        if (array_key_exists($path, $localConfig)) {
            ++$idx;
            $rslt = $this->getNext($paths, $localConfig[$path], $idx, $pathLength);
        }
        if (null === $rslt) {
            $rslt = $this->config->get($path_base);
        }

        return $rslt;
    }

    public function set(string $path, $value): mixed
    {
        $paths = explode('/', $path);
        $pathLength = sizeof($paths);
        $idx = 0;
        $localConfig = $this->extConfig;
        $path = array_pop($paths);
        if (is_array($localConfig) && array_key_exists($path, $localConfig)) {
            ++$idx;
            $localConfig[$path] = $this->setNext($paths, $localConfig[$path], $idx, $pathLength, $value);
            return $localConfig;
        }

        return null;
    }

    public function isExtnsionInstall(string $extName): bool
    {
        foreach ($this->extInstall as $extInstalled) {
            $name = substr($extInstalled, strpos($extInstalled, '-') + 1);
            $name = substr($name, 0, (strpos($name, '.') ? strpos($name, '.') : strlen($name)));
            if ($name === $extName) {
                return true;
            }
        }

        return false;
    }

    public function getInstalled()
    {
        return $this->extInstall;
    }

    private function getNext(array $paths, $localConfig, int $idx, int $pathLength): mixed
    {
        $path = array_shift($paths);
        if (is_array($localConfig) && array_key_exists($path, $localConfig)) {
            ++$idx;

            return $this->getNext($paths, $localConfig[$path], $idx, $pathLength);
        }
        if ($idx === $pathLength) {
            return $localConfig;
        }

        return null;
    }

    private function setNext(array $paths, $localConfig, int $idx, int $pathLength, $value = null): mixed
    {
        $path = array_pop($paths);
        if (is_array($localConfig) && array_key_exists($path, $localConfig)) {
            ++$idx;
            $localConfig[$path] = $this->setNext($paths, $localConfig[$path], $idx, $pathLength, $value);
            return $localConfig;
        }
        if ($idx === $pathLength) {
            return $value;
        } elseif (($idx + 1) === $pathLength) {
            return [$path => $value];
        }

        return null;
    }
}
