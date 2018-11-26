<?php
declare(strict_types=1);

namespace TutuRu\Config;

interface ApplicationConfigInterface extends ConfigInterface
{
    public function setValue(string $configId, $value);
}
