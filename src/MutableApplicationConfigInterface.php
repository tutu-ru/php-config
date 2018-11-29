<?php
declare(strict_types=1);

namespace TutuRu\Config;

interface MutableApplicationConfigInterface extends ApplicationConfigInterface
{
    public function setValue(string $configId, $value);
}
