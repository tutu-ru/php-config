<?php
declare(strict_types=1);

namespace TutuRu\Config;

use TutuRu\Config\Exceptions\ConfigNodeNotExist;
use TutuRu\Config\Exceptions\InvalidConfigException;

class Config
{
    private const CONFIG_TYPE_APP = 'application';
    private const CONFIG_TYPE_ENV = 'environment';

    private $configs = [];

    /** @var ConfigInterface[] */
    private $prioritizedConfigsList = [];

    private $runtimeCache = [];


    public function setApplicationConfig(ApplicationConfigInterface $applicationConfig, int $priority = 0)
    {
        $applicationConfig->load();
        $this->configs[self::CONFIG_TYPE_APP] = [
            'implementation' => $applicationConfig,
            'priority'       => $priority
        ];
        $this->runtimeCache = [];
        $this->buildPrioritizedConfigList();
    }


    public function setEnvironmentConfig(EnvironmentConfigInterface $environmentConfig, int $priority = 1)
    {
        $environmentConfig->load();
        $this->configs[self::CONFIG_TYPE_ENV] = [
            'implementation' => $environmentConfig,
            'priority'       => $priority
        ];
        $this->runtimeCache = [];
        $this->runtimeCache = [];
        $this->buildPrioritizedConfigList();
    }


    public function getApplicationConfig(): ?ApplicationConfigInterface
    {
        return $this->configs[self::CONFIG_TYPE_APP]['implementation'] ?? null;
    }


    public function getEnvironmentConfig(): ?EnvironmentConfigInterface
    {
        return $this->configs[self::CONFIG_TYPE_ENV]['implementation'] ?? null;
    }


    /**
     * @return ConfigInterface[]
     */
    private function getPrioritizedConfigs(): array
    {
        return $this->prioritizedConfigsList;
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


    /**
     * @param string $configId
     * @param null   $defaultValue
     * @param bool   $required
     * @return mixed|null
     * @throws ConfigNodeNotExist
     * @throws InvalidConfigException
     */
    public function getValue(string $configId, $defaultValue = null, bool $required = false)
    {
        if (array_key_exists($configId, $this->runtimeCache)) {
            return $this->runtimeCache[$configId];
        }

        $value = null;
        $initialized = false;
        foreach ($this->getPrioritizedConfigs() as $config) {
            $initialized = true;
            $value = $config->getValue($configId);
            if (!is_null($value)) {
                $this->runtimeCache[$configId] = $value;
                return $value;
            }
        }
        if (!$initialized) {
            throw new InvalidConfigException("No initialized configs (application or environment)");
        }
        if ($required && is_null($value)) {
            throw new ConfigNodeNotExist($configId);
        }
        return $defaultValue;
    }


    public function setApplicationValue(string $configId, $value)
    {
        $this->getApplicationConfig()->setValue($configId, $value);
        $this->runtimeCache = [];
    }


    /**
     * @param string     $configId
     * @param mixed|null $defaultValue
     * @param bool       $required
     * @return mixed|null
     * @throws ConfigNodeNotExist
     * @throws InvalidConfigException
     */
    public function getApplicationValue(string $configId, $defaultValue = null, bool $required = false)
    {
        if (is_null($this->getApplicationConfig())) {
            throw new InvalidConfigException("Application config not initialized");
        }
        $value = $this->getApplicationConfig()->getValue($configId);
        if (is_null($value)) {
            if ($required) {
                throw new ConfigNodeNotExist($configId);
            } else {
                $value = $defaultValue;
            }
        }
        return $value;
    }


    /**
     * @param string     $configId
     * @param mixed|null $defaultValue
     * @param bool       $required
     * @return mixed|null
     * @throws ConfigNodeNotExist
     * @throws InvalidConfigException
     */
    public function getEnvironmentValue(string $configId, $defaultValue = null, bool $required = false)
    {
        if (is_null($this->getEnvironmentConfig())) {
            throw new InvalidConfigException("Environment config not initialized");
        }
        $value = $this->getEnvironmentConfig()->getValue($configId);
        if (is_null($value)) {
            if ($required) {
                throw new ConfigNodeNotExist($configId);
            } else {
                $value = $defaultValue;
            }
        }
        return $value;
    }


    /**
     * @param string     $configId
     * @param mixed|null $defaultValue
     * @param bool       $required
     * @return mixed|null
     * @throws ConfigNodeNotExist
     * @throws InvalidConfigException
     */
    public function getEnvironmentServiceValue(string $configId, $defaultValue = null, bool $required = false)
    {
        if (is_null($this->getEnvironmentConfig())) {
            throw new InvalidConfigException("Environment config not initialized");
        }
        $value = $this->getEnvironmentConfig()->getServiceValue($configId);
        if (is_null($value)) {
            if ($required) {
                throw new ConfigNodeNotExist($configId);
            } else {
                $value = $defaultValue;
            }
        }
        return $value;
    }


    /**
     * @param string     $configId
     * @param mixed|null $defaultValue
     * @param bool       $required
     * @return mixed|null
     * @throws ConfigNodeNotExist
     * @throws InvalidConfigException
     */
    public function getEnvironmentBusinessValue(string $configId, $defaultValue = null, bool $required = false)
    {
        if (is_null($this->getEnvironmentConfig())) {
            throw new InvalidConfigException("Environment config not initialized");
        }
        $value = $this->getEnvironmentConfig()->getBusinessValue($configId);
        if (is_null($value)) {
            if ($required) {
                throw new ConfigNodeNotExist($configId);
            } else {
                $value = $defaultValue;
            }
        }
        return $value;
    }


    /**
     * @param string     $configId
     * @param mixed|null $defaultValue
     * @param bool       $required
     * @return mixed|null
     * @throws ConfigNodeNotExist
     * @throws InvalidConfigException
     */
    public function getEnvironmentInfrastructureValue(string $configId, $defaultValue = null, bool $required = false)
    {
        if (is_null($this->getEnvironmentConfig())) {
            throw new InvalidConfigException("Environment config not initialized");
        }
        $value = $this->getEnvironmentConfig()->getInfrastructureValue($configId);
        if (is_null($value)) {
            if ($required) {
                throw new ConfigNodeNotExist($configId);
            } else {
                $value = $defaultValue;
            }
        }
        return $value;
    }


    /**
     * @param string $configId
     * @param mixed  $value
     * @throws Exceptions\BusinessConfigUpdateException
     * @throws InvalidConfigException
     */
    public function updateEnvironmentBusinessValue(string $configId, $value)
    {
        if (is_null($this->getEnvironmentConfig())) {
            throw new InvalidConfigException("Environment config not initialized");
        }
        $this->getEnvironmentConfig()->updateBusinessValue($configId, $value);
    }


    /**
     * @return null|MutatorInterface
     * @throws InvalidConfigException
     */
    public function getEnvironmentServiceMutator(): ?MutatorInterface
    {
        if (is_null($this->getEnvironmentConfig())) {
            throw new InvalidConfigException("Environment config not initialized");
        }
        return $this->getEnvironmentConfig()->getServiceMutator();
    }


    /**
     * @return null|MutatorInterface
     * @throws InvalidConfigException
     */
    public function getEnvironmentBusinessMutator(): ?MutatorInterface
    {
        if (is_null($this->getEnvironmentConfig())) {
            throw new InvalidConfigException("Environment config not initialized");
        }
        return $this->getEnvironmentConfig()->getBusinessMutator();
    }


    public function getServerHostname(): string
    {
        return getenv('HOSTNAME_EXT') ?: (getenv('HOSTNAME') ?: php_uname('n'));
    }
}
