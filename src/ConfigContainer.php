<?php
declare(strict_types=1);

namespace TutuRu\Config;

use TutuRu\Config\ContainerExceptions\PathNotExistsException;
use TutuRu\Config\ContainerExceptions\InvalidConfigException;

class ConfigContainer implements ConfigInterface
{
    private const CONFIGS_LIST_KEY_PRIORITY = 'priority';
    private const CONFIGS_LIST_KEY_IMPLEMENTATION = 'implementation';

    private $configs = [];

    /** @var ConfigInterface[] */
    private $prioritizedConfigsList = [];

    private $runtimeCache = [];

    /** @var bool */
    private $useArrayValuesMerging;


    public function __construct($mergeArrayValues = true)
    {
        $this->useArrayValuesMerging = $mergeArrayValues;
    }


    public function useArrayValuesMerging(bool $useArrayValuesMerging)
    {
        $this->useArrayValuesMerging = $useArrayValuesMerging;
    }


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


    public function getValue(string $path, $defaultValue = null)
    {
        if (array_key_exists($path, $this->runtimeCache)) {
            return $this->runtimeCache[$path];
        }
        if (empty($this->configs)) {
            throw new InvalidConfigException("No initialized configs");
        }

        $value = $this->useArrayValuesMerging ? $this->getValueWithMergedArray($path) : $this->getSimpleValue($path);

        if (!is_null($value)) {
            $this->runtimeCache[$path] = $value;
            return $value;
        } else {
            return $defaultValue;
        }
    }


    public function getRequiredValue(string $path)
    {
        $value = $this->getValue($path);
        if (is_null($value)) {
            throw new PathNotExistsException("Path {$path} not exists in config");
        }
        return $value;
    }


    public function resetRuntimeCache()
    {
        $this->runtimeCache = [];
    }


    private function getSimpleValue(string $path)
    {
        $value = null;
        foreach ($this->prioritizedConfigsList as $config) {
            $value = $config->getValue($path);
            if (!is_null($value)) {
                break;
            }
        }
        return $value;
    }


    private function getValueWithMergedArray(string $path)
    {
        $value = null;
        foreach (array_reverse($this->prioritizedConfigsList) as $config) {
            $result = $config->getValue($path);
            if (!is_null($result)) {
                $value = is_array($value) ? $this->mergeConfig($value, (array)$result) : $result;
            }
        }
        return $value;
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


    private function mergeConfig(array $array1, array $array2): array
    {
        // Merge two arrays recursive. If first and second array have the same key second overwrite first.
        $merged = $array1;
        if (is_array($array2)) {
            foreach ($array2 as $key => $val) {
                if (is_array($array2[$key])) {
                    if (isset($merged[$key]) && is_array($merged[$key])) {
                        $merged[$key] = $this->mergeConfig($merged[$key], $array2[$key]);
                    } else {
                        $merged[$key] = $array2[$key];
                    }
                } else {
                    $merged[$key] = $val;
                }
            }
        }
        return $merged;
    }
}
