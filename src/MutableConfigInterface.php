<?php
declare(strict_types=1);

namespace TutuRu\Config;

use TutuRu\Config\Exception;

interface MutableConfigInterface extends ConfigInterface
{
    /**
     * @param string $path
     * @param mixed  $value
     *
     * @throws Exception\InvalidConfigExceptionInterface
     * @throws Exception\InvalidConfigValueExceptionInterface
     * @throws Exception\ConfigValueUpdateExceptionInterface
     */
    public function setValue(string $path, $value): void;
}
