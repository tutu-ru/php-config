<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config\Implementations;

use TutuRu\Config\MutableApplicationConfigInterface;

class MutableApplicationConfig extends ApplicationConfig implements MutableApplicationConfigInterface
{
    public function setValue(string $path, $value)
    {
        $this->loadedData[$path] = $value;
    }
}
