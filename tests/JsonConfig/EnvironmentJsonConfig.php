<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config\JsonConfig;

use TutuRu\Config\ConfigDataStorageTrait;
use TutuRu\Config\EnvironmentConfigInterface;
use TutuRu\Config\MutatorInterface;

class EnvironmentJsonConfig extends JsonConfig implements EnvironmentConfigInterface
{
    use ConfigDataStorageTrait;

    private const CONFIG_SERVICE = 'service';
    private const CONFIG_BUSINESS = 'business';
    private const CONFIG_INFRASTRUCTURE = 'infrastructure';

    /** @var string */
    private $serviceFilename;

    /** @var string */
    private $businessFilename;

    /** @var string */
    private $infrastructureFilename;

    /** @var ApplicationJsonConfig[] */
    private $configs = [];


    public static function createFromOneFile(string $filename): self
    {
        return new self($filename, $filename, $filename);
    }


    public function __construct(string $serviceFilename, string $businessFilename, string $infrastructureFilename)
    {
        $this->serviceFilename = $serviceFilename;
        $this->businessFilename = $businessFilename;
        $this->infrastructureFilename = $infrastructureFilename;
    }


    public function load()
    {
        /** @var ApplicationJsonConfig[] $configs */
        $configs = [
            self::CONFIG_SERVICE        => new ApplicationJsonConfig($this->serviceFilename),
            self::CONFIG_BUSINESS       => new MutableApplicationJsonConfig($this->businessFilename),
            self::CONFIG_INFRASTRUCTURE => new ApplicationJsonConfig($this->infrastructureFilename),
        ];
        foreach ($configs as $config) {
            $config->load();
        }
        $this->configs = $configs;
    }


    public function getValue(string $path)
    {
        $this->checkConfigs();
        foreach ($this->configs as $config) {
            $value = $config->getValue($path);
            if (!is_null($value)) {
                return $value;
            }
        }
        return null;
    }


    public function getBusinessValue(string $path)
    {
        $this->checkConfigs();
        return $this->configs[self::CONFIG_BUSINESS]->getValue($path);
    }


    public function updateBusinessValue(string $path, $value)
    {
        $this->checkConfigs();
        /** @var MutableApplicationJsonConfig $config */
        $config = $this->configs[self::CONFIG_BUSINESS];
        if (is_null($config->getValue($path))) {
            throw new JsonConfigException("Update forbidden: {$path} not exists");
        }
        $config->setValue($path, $value);
    }


    public function getServiceValue(string $path)
    {
        $this->checkConfigs();
        return $this->configs[self::CONFIG_SERVICE]->getValue($path);
    }


    public function getInfrastructureValue(string $path)
    {
        $this->checkConfigs();
        return $this->configs[self::CONFIG_INFRASTRUCTURE]->getValue($path);
    }


    public function getBusinessMutator(): ?MutatorInterface
    {
        return null;
    }


    public function getServiceMutator(): ?MutatorInterface
    {
        return null;
    }


    private function checkConfigs()
    {
        if (empty($this->configs)) {
            throw new JsonConfigException("Config not loaded");
        }
    }
}
