<?php
declare(strict_types=1);

namespace TutuRu\Config;

use TutuRu\Config\ContainerException\PathNotExistsException;
use TutuRu\Config\ContainerException\InvalidConfigException;

class ConfigContainer implements ConfigInterface
{
    private const CONFIGS_LIST_KEY_PRIORITY = 'priority';
    private const CONFIGS_LIST_KEY_IMPLEMENTATION = 'implementation';

    private $configs = [];

    /** @var ConfigInterface[] */
    private $prioritizedConfigsList = [];

    private $runtimeCache = [];


    public function getConfig(string $id): ?ConfigInterface
    {
        return $this->configs[$id][self::CONFIGS_LIST_KEY_IMPLEMENTATION] ?? null;
    }


    public function setConfig(string $id, ConfigInterface $config, int $priority)
    {
        $this->configs[$id] = [
            self::CONFIGS_LIST_KEY_IMPLEMENTATION => $config,
            self::CONFIGS_LIST_KEY_PRIORITY       => $priority
        ];
        $this->resetRuntimeCache();
        $this->buildPrioritizedConfigList();
    }


    public function getValue(string $path, bool $required = false, $defaultValue = null)
    {
        if (array_key_exists($path, $this->runtimeCache)) {
            return $this->runtimeCache[$path];
        }
        if (empty($this->configs)) {
            throw new InvalidConfigException("No initialized configs");
        }

        $value = null;
        foreach ($this->prioritizedConfigsList as $config) {
            $value = $config->getValue($path);
            if (!is_null($value)) {
                break;
            }
        }

        if ($required && is_null($value)) {
            throw new PathNotExistsException($path);
        }

        if (!is_null($value)) {
            $this->runtimeCache[$path] = $value;
            return $value;
        } else {
            return $defaultValue;
        }
    }


    public function resetRuntimeCache()
    {
        $this->runtimeCache = [];
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
}
