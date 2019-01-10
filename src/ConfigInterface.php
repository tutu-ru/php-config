<?php
declare(strict_types=1);

namespace TutuRu\Config;

use TutuRu\Config\Exception;

interface ConfigInterface
{
    public const CONFIG_PATH_SEPARATOR = '.';

    /**
     * @param string $path
     * @param bool   $required
     * @param mixed  $defaultValue
     *
     * @throws Exception\InvalidConfigExceptionInterface
     * @throws Exception\ConfigPathNotExistExceptionInterface
     *
     * @return mixed
     */
    public function getValue(string $path, bool $required = false, $defaultValue = null);
}
