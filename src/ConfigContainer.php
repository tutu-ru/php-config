<?php
declare(strict_types=1);

namespace TutuRu\Config;

use TutuRu\Config\ContainerExceptions\PathNotExistsException;
use TutuRu\Config\ContainerExceptions\InvalidConfigException;
use TutuRu\Config\ContainerExceptions\UpdateForbiddenException;

class ConfigContainer
{
    private const CONFIG_TYPE_APP = 'application';
    private const CONFIG_TYPE_ENV = 'environment';

    private const CONFIGS_LIST_KEY_PRIORITY = 'priority';
    private const CONFIGS_LIST_KEY_IMPLEMENTATION = 'implementation';

    private $configs = [];

    /** @var ConfigInterface[] */
    private $prioritizedConfigsList = [];

    protected $runtimeCache = [];


    public function setApplicationConfig(ApplicationConfigInterface $applicationConfig, int $priority = 0)
    {
        $this->setConfig($applicationConfig, self::CONFIG_TYPE_APP, $priority);
    }


    public function setEnvironmentConfig(EnvironmentConfigInterface $environmentConfig, int $priority = 1)
    {
        $this->setConfig($environmentConfig, self::CONFIG_TYPE_ENV, $priority);
    }


    public function getApplicationConfig(): ?ApplicationConfigInterface
    {
        return $this->configs[self::CONFIG_TYPE_APP][self::CONFIGS_LIST_KEY_IMPLEMENTATION] ?? null;
    }


    public function getEnvironmentConfig(): ?EnvironmentConfigInterface
    {
        return $this->configs[self::CONFIG_TYPE_ENV][self::CONFIGS_LIST_KEY_IMPLEMENTATION] ?? null;
    }


    public function getValue(string $path, bool $required = false, $defaultValue = null)
    {
        if (array_key_exists($path, $this->runtimeCache)) {
            return $this->runtimeCache[$path];
        }

        $value = null;
        $initialized = false;
        foreach ($this->prioritizedConfigsList as $config) {
            $initialized = true;
            $value = $config->getValue($path);
            if (!is_null($value)) {
                $this->runtimeCache[$path] = $value;
                return $value;
            }
        }
        if (!$initialized) {
            throw new InvalidConfigException("No initialized configs (application or environment)");
        }
        return $this->prepareValue($path, $value, $defaultValue, $required);
    }


    public function setApplicationValue(string $path, $value)
    {
        $applicationConfig = $this->getApplicationConfig();
        if (!is_null($applicationConfig) && $applicationConfig instanceof MutableApplicationConfigInterface) {
            $applicationConfig->setValue($path, $value);
            $this->runtimeCache = [];
        } else {
            throw new UpdateForbiddenException("Not mutable application config");
        }
    }


    public function getApplicationValue(string $path, bool $required = false, $defaultValue = null)
    {
        $this->checkApplicationConfig();
        $value = $this->getApplicationConfig()->getValue($path);
        return $this->prepareValue($path, $value, $defaultValue, $required);
    }


    public function getEnvironmentValue(string $path, bool $required = false, $defaultValue = null)
    {
        $this->checkEnvironmentConfig();
        $value = $this->getEnvironmentConfig()->getValue($path);
        return $this->prepareValue($path, $value, $defaultValue, $required);
    }


    public function getEnvironmentServiceValue(string $path, bool $required = false, $defaultValue = null)
    {
        $this->checkEnvironmentConfig();
        $value = $this->getEnvironmentConfig()->getServiceValue($path);
        return $this->prepareValue($path, $value, $defaultValue, $required);
    }


    public function getEnvironmentBusinessValue(string $path, bool $required = false, $defaultValue = null)
    {
        $this->checkEnvironmentConfig();
        $value = $this->getEnvironmentConfig()->getBusinessValue($path);
        return $this->prepareValue($path, $value, $defaultValue, $required);
    }


    public function getEnvironmentInfrastructureValue(string $path, bool $required = false, $defaultValue = null)
    {
        $this->checkEnvironmentConfig();
        $value = $this->getEnvironmentConfig()->getInfrastructureValue($path);
        return $this->prepareValue($path, $value, $defaultValue, $required);
    }


    public function updateEnvironmentBusinessValue(string $path, $value)
    {
        $this->checkEnvironmentConfig();
        $this->getEnvironmentConfig()->updateBusinessValue($path, $value);
    }


    public function getEnvironmentServiceMutator(): ?MutatorInterface
    {
        $this->checkEnvironmentConfig();
        return $this->getEnvironmentConfig()->getServiceMutator();
    }


    public function getEnvironmentBusinessMutator(): ?MutatorInterface
    {
        $this->checkEnvironmentConfig();
        return $this->getEnvironmentConfig()->getBusinessMutator();
    }


    private function setConfig(ConfigInterface $config, string $id, int $priority)
    {
        $this->configs[$id] = [
            self::CONFIGS_LIST_KEY_IMPLEMENTATION => $config,
            self::CONFIGS_LIST_KEY_PRIORITY       => $priority
        ];
        $this->runtimeCache = [];
        $this->buildPrioritizedConfigList();
    }


    private function buildPrioritizedConfigList()
    {
        uasort(
            $this->configs,
            function ($a, $b) {
                return $b[self::CONFIGS_LIST_KEY_PRIORITY] <=> $a[self::CONFIGS_LIST_KEY_PRIORITY];
            }
        );
        $this->prioritizedConfigsList = [];
        foreach ($this->configs as $configData) {
            $this->prioritizedConfigsList[] = $configData[self::CONFIGS_LIST_KEY_IMPLEMENTATION];
        }
    }


    private function checkApplicationConfig()
    {
        if (is_null($this->getApplicationConfig())) {
            throw new InvalidConfigException("Application config not initialized");
        }
    }


    private function checkEnvironmentConfig()
    {
        if (is_null($this->getEnvironmentConfig())) {
            throw new InvalidConfigException("Environment config not initialized");
        }
    }


    private function prepareValue(string $path, $value, $defaultValue, bool $required)
    {
        if (is_null($value)) {
            if ($required) {
                throw new PathNotExistsException($path);
            } else {
                $value = $defaultValue;
            }
        }
        return $value;
    }
}
