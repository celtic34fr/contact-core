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
        $this->extConfig = $this->getAllConfigs();
    }

    /**
     * @param string $projectDir
     */
    public function initialize(string $projectDir)
    {
        $this->projectDir = $projectDir;
        $sources = $this->projectDir . '/config';
        if (is_dir($sources . '/extensions')) {
            $sources .= '/extensions';
            $filesNames = scandir($sources);
            foreach ($filesNames as $fileName) {
                if (!is_dir($sources . '/' . $fileName)) {
                    $this->extInstall[] = $fileName;
                    $content = file_get_contents($sources . '/' . $fileName);
                    $arrays = Yaml::parse($content);
                    $name = substr($fileName, 0, strpos($fileName, '.'));
                    $this->extConfig[$name] = $arrays;
                }
            }
        }
    }

    /**
     * @param string $path
     * @return mixed
     */
    public function get(string $path): mixed
    {
        $paths = explode('/', $path);
        $pathLength = sizeof($paths);
        $idx = 0;
        $localConfig = $this->extConfig;
        $path = array_shift($paths);
        if (array_key_exists($path, $localConfig)) {
            $idx += 1;
            return $this->getNext($paths, $localConfig[$path], $idx, $pathLength);
        }
        return null;
    }

    /**
     * @param string $path
     * @param $value
     * @return mixed
     */
    public function set(string $path, $value): mixed
    {
        $paths = explode('/', $path);
        $pathLength = sizeof($paths);
        $idx = 0;
        $localConfig = $this->extConfig;
        $path = array_pop($paths);
        if (is_array($localConfig) && array_key_exists($path, $localConfig)) {
            $idx += 1;
            $localConfig[$path] =  $this->setNext($paths, $localConfig[$path], $idx, $pathLength, $value);
            return $localConfig;
        }
        return null;
    }

    /**
     * @param string $extName
     * @return bool
     */
    public function isExtnsionInstall(string $extName): bool
    {
        foreach ($this->extInstall as $extInstalled) {
            $name = substr($extInstalled, strpos($extInstalled, "-") + 1);
            $name = substr($name, 0, strpos($name, "."));
            if ($name === $extName) return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getAllConfigs(): array
    {
        $local = $this->extConfig;
        $globalsPaths = $this->config->getPaths();
        $global = [];
        foreach ($globalsPaths as $globalPath) {
            $global[$globalPath] = $this->config->get($globalPath);
        }
        return array_merge($global, $local);
    }

    /**
     * @param array $paths
     * @param $localConfig
     * @param int $idx
     * @param int $pathLength
     * @return mixed
     */
    private function getNext(array $paths, $localConfig, int $idx, int $pathLength): mixed
    {
        $path = array_shift($paths);
        if (is_array($localConfig) && array_key_exists($path, $localConfig)) {
            $idx += 1;
            return $this->getNext($paths, $localConfig[$path], $idx, $pathLength);
        }
        if ($idx === $pathLength) {
            return $localConfig;
        }
        return null;
    }

    /**
     * @param array $paths
     * @param $localConfig
     * @param int $idx
     * @param int $pathLength
     * @param $value
     * @return mixed
     */
    private function setNext(array $paths, $localConfig, int $idx, int $pathLength, $value = null): mixed
    {
        $path = array_pop($paths);
        if (is_array($localConfig) && array_key_exists($path, $localConfig)) {
            $idx += 1;
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
