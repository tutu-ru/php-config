<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config\JsonConfig;

use TutuRu\Config\MutableConfigInterface;

class MutableJsonConfig extends JsonConfig implements MutableConfigInterface
{
    public function setValue(string $path, $value): void
    {
        $this->setConfigData($path, $value);
    }
}
