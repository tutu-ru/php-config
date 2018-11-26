<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config\Implementations;

use TutuRu\Config\ApplicationConfigInterface;

class ApplicationConfig implements ApplicationConfigInterface
{
    private $data;
    private $loadedData;


    public function __construct($data)
    {
        $this->data = $data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function load()
    {
        $this->loadedData = $this->data;
    }

    public function getValue(string $configId)
    {
        return $this->loadedData[$configId] ?? null;
    }

    public function setValue(string $configId, $value)
    {
        $this->loadedData[$configId] = $value;
    }
}
