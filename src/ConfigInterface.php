<?php
declare(strict_types=1);

namespace TutuRu\Config;

interface ConfigInterface
{
    public const CONFIG_NODES_SEPARATOR = '.';

    public function load();

    public function getValue(string $configId);
}
