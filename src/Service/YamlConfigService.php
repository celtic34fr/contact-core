<?php

namespace Celtic34fr\ContactCore\Service;

use Symfony\Component\Yaml\Yaml;

class YamlConfigService
{
    private ?string $filePath = null;

    public function __construct(?string $filePath = null)
    {
        $this->filePath = $filePath ?? "";
    }

    public function setFilePath($filePath): self
    {
        $this->filePath = $filePath;
        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function read(): mixed
    {
        return Yaml::parseFile($this->filePath);
    }

    public function write(array $data, int $deep = 1)
    {
        $yaml = Yaml::dump($data, $deep);

        file_put_contents($this->filePath, $yaml);

        return $this;
    }
}