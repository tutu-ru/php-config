<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config\JsonConfig;

use TutuRu\Config\ConfigInterface;
use TutuRu\Config\ConfigDataStorageTrait;

class JsonConfig implements ConfigInterface
{
    use ConfigDataStorageTrait;

    /** @var string */
    private $filename;


    public function __construct(string $filename)
    {
        $this->filename = $filename;
        $this->load();
    }


    public function getValue(string $path, $defaultValue = null)
    {
        return $this->getConfigData($path) ?? $defaultValue;
    }


    public function getRequiredValue(string $path)
    {
        $value = $this->getValue($path);
        if (is_null($value)) {
            throw new JsonConfigException("Path {$path} not exists in config");
        }
        return $value;
    }


    private function load()
    {
        if (!file_exists($this->filename) || !is_readable($this->filename)) {
            throw new JsonConfigException("{$this->filename} not exists or not readable");
        }
        $json = json_decode(file_get_contents($this->filename), true);
        $error = json_last_error();
        if ($error !== JSON_ERROR_NONE) {
            throw new JsonConfigException("JSON error in {$this->filename}: " . json_last_error_msg(), $error);
        }
        $this->data = $json;
    }
}
