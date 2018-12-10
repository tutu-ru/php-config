<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config\JsonConfig;

use TutuRu\Config\ApplicationConfigInterface;
use TutuRu\Config\ConfigDataStorageTrait;

class ApplicationJsonConfig extends JsonConfig implements ApplicationConfigInterface
{
    use ConfigDataStorageTrait;

    /** @var string */
    private $filename;


    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }


    public function load()
    {
        $this->data = $this->loadDataFromFile($this->filename);
    }


    public function getValue(string $path)
    {
        return $this->getConfigData($path);
    }
}
