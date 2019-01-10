<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config;

use TutuRu\Config\Exception\ConfigPathNotExistExceptionInterface;
use TutuRu\Config\Exception\InvalidConfigExceptionInterface;

class JsonConfigTest extends BaseTest
{
    public function testBrokenConfig()
    {
        $this->expectException(InvalidConfigExceptionInterface::class);
        $this->createJsonConfig(__DIR__ . '/config/broken.json');
    }


    public function testNotExistingConfig()
    {
        $this->expectException(InvalidConfigExceptionInterface::class);
        $this->createJsonConfig(__DIR__ . '/config/not_exists.json');
    }


    public function testGetValue()
    {
        $config = $this->createJsonConfig(__DIR__ . '/config/app.json');
        $this->assertEquals('test', $config->getValue('name'));
    }


    public function testGetValueWithNotExistingPath()
    {
        $config = $this->createJsonConfig(__DIR__ . '/config/app.json');
        $this->assertEquals(null, $config->getValue('not_existing_value'));
    }


    public function testGetValueWithDefault()
    {
        $config = $this->createJsonConfig(__DIR__ . '/config/app.json');
        $this->assertEquals('test', $config->getValue('name', false, 'default'));
    }


    public function testGetValueWithNotExistingPathAndDefault()
    {
        $config = $this->createJsonConfig(__DIR__ . '/config/app.json');
        $this->assertEquals('default', $config->getValue('not_existing_value', false, 'default'));
    }


    public function testGetRequiredValue()
    {
        $config = $this->createJsonConfig(__DIR__ . '/config/app.json');
        $this->assertEquals('test', $config->getValue('name', true));
    }


    public function testGetRequiredValueWithNotExistingPath()
    {
        $config = $this->createJsonConfig(__DIR__ . '/config/app.json');
        $this->expectException(ConfigPathNotExistExceptionInterface::class);
        $config->getValue('not_existing_value', true);
    }


    public function testGetValueArray()
    {
        $config = $this->createJsonConfig(__DIR__ . '/config/app.json');
        $this->assertEquals(['one' => 1, 'two' => 2, 'sub_array' => ['y' => 'z']], $config->getValue('array'));
    }
}
