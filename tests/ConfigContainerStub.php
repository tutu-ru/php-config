<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config;

class ConfigContainerStub extends \TutuRu\Config\ConfigContainer
{
    private $runtimeStorage = [];

    public function getValue(string $path, bool $required = false, $defaultValue = null)
    {
        if (array_key_exists($path, $this->runtimeStorage)) {
            return $this->runtimeStorage[$path];
        }

        return parent::getValue($path, $required, $defaultValue);
    }

    public function setValue(string $path, $value = null): void
    {
        if (is_null($value)) {
            unset($this->runtimeStorage[$path]);
        } else {
            $this->runtimeStorage[$path] = $value;
        }
    }
}
