<?php
declare(strict_types=1);

namespace TutuRu\Config\JsonConfig;

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


    public function getValue(string $path, bool $required = false, $defaultValue = null)
    {
        $value = $this->getConfigData($path);
        if ($required && is_null($value)) {
            throw new JsonConfigPathNotExistException("Path {$path} not exists in config");
        }
        return $value ?? $defaultValue;
    }


    private function load()
    {
        if (!file_exists($this->filename) || !is_readable($this->filename)) {
            throw new JsonConfigInvalidException("{$this->filename} not exists or not readable");
        }
        $json = json_decode(file_get_contents($this->filename), true);
        $error = json_last_error();
        if ($error !== JSON_ERROR_NONE) {
            throw new JsonConfigInvalidException("JSON error in {$this->filename}: " . json_last_error_msg(), $error);
        }
        $this->data = $json;
    }
}
