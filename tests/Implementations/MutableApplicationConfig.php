<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config\Implementations;

use TutuRu\Config\MutableApplicationConfigInterface;

class MutableApplicationConfig extends ApplicationConfig implements MutableApplicationConfigInterface
{
    public function setValue(string $configId, $value)
    {
        $this->loadedData[$configId] = $value;
    }
}
