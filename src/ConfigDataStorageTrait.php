<?php
declare(strict_types=1);

namespace TutuRu\Config;

trait ConfigDataStorageTrait
{
    /** @var array */
    protected $data;


    protected function getConfigData(string $path)
    {
        $parts = explode(ConfigInterface::CONFIG_PATH_SEPARATOR, $path);
        $result = $this->data;
        foreach ($parts as $c) {
            if (is_array($result) && array_key_exists($c, $result)) {
                $result = $result[$c];
            } else {
                return null;
            }
        }
        return $result;
    }


    protected function setConfigData(string $path, $value)
    {
        $parts = explode(ConfigInterface::CONFIG_PATH_SEPARATOR, $path);
        $valueNode = array_pop($parts);
        $result =& $this->data;
        foreach ($parts as $c) {
            if (!array_key_exists($c, $result)) {
                $result[$c] = [];
            }
            $result =& $result[$c];
        }
        if (is_array($result)) {
            $result[$valueNode] = $value;
        } else {
            $result = [$valueNode => $value];
        }
    }
}
