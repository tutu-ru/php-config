<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config\JsonConfig;

use TutuRu\Config\MutableApplicationConfigInterface;

class MutableApplicationJsonConfig extends ApplicationJsonConfig implements MutableApplicationConfigInterface
{
    public function setValue(string $path, $value)
    {
        $this->setConfigData($path, $value);
    }
}
