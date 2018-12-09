<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config\JsonConfig;

class JsonConfig
{
    protected function loadDataFromFile(string $filename)
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            throw new JsonConfigException("{$filename} not exists or not readable");
        }
        $json = json_decode(file_get_contents($filename), true);
        $error = json_last_error();
        if ($error !== JSON_ERROR_NONE) {
            throw new JsonConfigException("JSON error in {$filename}: " . json_last_error_msg(), $error);
        }
        return $json;
    }
}
