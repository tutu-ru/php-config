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

    private $configs = [];

    /** @var ConfigInterface[] */
    private $prioritizedConfigsList = [];

    private $runtimeCache = [];


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
        return $this->configs[self::CONFIG_TYPE_APP]['implementation'] ?? null;
    }


    public function getEnvironmentConfig(): ?EnvironmentConfigInterface
    {
        return $this->configs[self::CONFIG_TYPE_ENV]['implementation'] ?? null;
    }


    public function getValue(string $path, $defaultValue = null, bool $required = false)
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
        if ($required && is_null($value)) {
            throw new PathNotExistsException($path);
        }
        return $defaultValue;
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


    public function getApplicationValue(string $path, $defaultValue = null, bool $required = false)
    {
        $this->checkApplicationConfig();
        $value = $this->getApplicationConfig()->getValue($path);
        if (is_null($value)) {
            if ($required) {
                throw new PathNotExistsException($path);
            } else {
                $value = $defaultValue;
            }
        }
        return $value;
    }


    public function getEnvironmentValue(string $path, $defaultValue = null, bool $required = false)
    {
        $this->checkEnvironmentConfig();
        $value = $this->getEnvironmentConfig()->getValue($path);
        if (is_null($value)) {
            if ($required) {
                throw new PathNotExistsException($path);
            } else {
                $value = $defaultValue;
            }
        }
        return $value;
    }


    public function getEnvironmentServiceValue(string $path, $defaultValue = null, bool $required = false)
    {
        $this->checkEnvironmentConfig();
        $value = $this->getEnvironmentConfig()->getServiceValue($path);
        if (is_null($value)) {
            if ($required) {
                throw new PathNotExistsException($path);
            } else {
                $value = $defaultValue;
            }
        }
        return $value;
    }


    public function getEnvironmentBusinessValue(string $path, $defaultValue = null, bool $required = false)
    {
        $this->checkEnvironmentConfig();
        $value = $this->getEnvironmentConfig()->getBusinessValue($path);
        if (is_null($value)) {
            if ($required) {
                throw new PathNotExistsException($path);
            } else {
                $value = $defaultValue;
            }
        }
        return $value;
    }


    public function getEnvironmentInfrastructureValue(string $path, $defaultValue = null, bool $required = false)
    {
        if (is_null($this->getEnvironmentConfig())) {
            throw new InvalidConfigException("Environment config not initialized");
        }
        $value = $this->getEnvironmentConfig()->getInfrastructureValue($path);
        if (is_null($value)) {
            if ($required) {
                throw new PathNotExistsException($path);
            } else {
                $value = $defaultValue;
            }
        }
        return $value;
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


    public function getServerHostname(): string
    {
        return getenv('HOSTNAME_EXT') ?: (getenv('HOSTNAME') ?: php_uname('n'));
    }


    private function setConfig(ConfigInterface $config, string $id, int $priority)
    {
        $config->load();
        $this->configs[$id] = ['implementation' => $config, 'priority' => $priority];
        $this->runtimeCache = [];
        $this->buildPrioritizedConfigList();
    }


    private function buildPrioritizedConfigList()
    {
        uasort(
            $this->configs,
            function ($a, $b) {
                return $b['priority'] <=> $a['priority'];
            }
        );
        $this->prioritizedConfigsList = [];
        foreach ($this->configs as $configData) {
            $this->prioritizedConfigsList[] = $configData['implementation'];
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
}
