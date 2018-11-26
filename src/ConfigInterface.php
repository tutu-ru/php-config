<?php
declare(strict_types=1);

namespace TutuRu\Config;

interface ConfigInterface
{
    public function load();


    public function getValue(string $configId);
}
