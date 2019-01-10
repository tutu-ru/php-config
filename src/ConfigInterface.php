<?php
declare(strict_types=1);

namespace TutuRu\Config;

use TutuRu\Config\Exception;

interface ConfigInterface
{
    public const CONFIG_PATH_SEPARATOR = '.';

    /**
     * @param string $path
     * @param mixed  $defaultValue
     *
     * @throws Exception\InvalidConfigExceptionInterface
     *
     * @return mixed
     */
    public function getValue(string $path, $defaultValue = null);

    /**
     * @param string $path
     *
     * @throws Exception\InvalidConfigExceptionInterface
     * @throws Exception\ConfigPathNotExistExceptionInterface
     *
     * @return mixed
     */
    public function getRequiredValue(string $path);
}
