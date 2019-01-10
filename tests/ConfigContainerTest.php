<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config;

use TutuRu\Config\ConfigContainer;
use TutuRu\Config\Exception\ConfigPathNotExistExceptionInterface;
use TutuRu\Config\Exception\InvalidConfigExceptionInterface;

class ConfigContainerTest extends BaseTest
{
    public function testSetAndGetConfig()
    {
        $config = new ConfigContainer();
        $this->assertNull($config->getConfig('app'));
        $this->assertNull($config->getConfig('env'));

        $applicationConfig = $this->createJsonConfig(__DIR__ . '/config/app.json');
        $config->setConfig('app', $applicationConfig, 0);

        $this->assertSame($applicationConfig, $config->getConfig('app'));
        $this->assertNull($config->getConfig('env'));
    }


    public function testReplaceConfig()
    {
        $config = new ConfigContainer();
        $config1 = $this->createJsonConfig(__DIR__ . '/config/app.json');
        $config2 = $this->createJsonConfig(__DIR__ . '/config/app.json');

        $config->setConfig('app', $config1, 0);
        $config->setConfig('app', $config2, 1);

        $this->assertSame($config2, $config->getConfig('app'));
    }


    public function testGetValueWithNotInitializedConfig()
    {
        $config = new ConfigContainer();
        $this->expectException(InvalidConfigExceptionInterface::class);
        $config->getValue('test');
    }


    public function testGetValue()
    {
        $config = $this->createDefaultConfigContainer();
        $this->assertEquals('test', $config->getValue('name'));
        $this->assertEquals('1', $config->getValue('debug'));
    }


    public function testGetValueWithNotExistingPath()
    {
        $config = $this->createDefaultConfigContainer();
        $this->assertEquals(null, $config->getValue('not_exist'));
    }


    public function testGetValueWithDefault()
    {
        $config = $this->createDefaultConfigContainer();
        $this->assertEquals('test', $config->getValue('name', false, 'default'));
    }


    public function testGetValueWithNotExistingPathAndDefault()
    {
        $config = $this->createDefaultConfigContainer();
        $this->assertEquals('default', $config->getValue('not_exist', false, 'default'));
    }


    public function testGetRequiredValue()
    {
        $config = $this->createDefaultConfigContainer();
        $this->assertEquals('test', $config->getValue('name', true));
    }


    public function testGetRequiredValueWithNotExistingPath()
    {
        $config = $this->createDefaultConfigContainer();
        $this->expectException(ConfigPathNotExistExceptionInterface::class);
        $config->getValue('not_exist', true);
    }


    public function testRuntimeCache()
    {
        $config = $this->createDefaultConfigContainer(true);

        $config->getConfig('app')->expects($this->exactly(1))->method('getValue');
        $config->getConfig('env')->expects($this->exactly(1))->method('getValue');

        $config->getValue('name');
        $config->getValue('name');
    }


    public function testRuntimeCacheReset()
    {
        $config = $this->createDefaultConfigContainer(true);

        $config->getConfig('app')->expects($this->exactly(2))->method('getValue');
        $config->getConfig('env')->expects($this->exactly(2))->method('getValue');

        $config->getValue('name');
        $config->resetRuntimeCache();
        $config->getValue('name');
    }


    public function testRuntimeCacheResetOnSetConfig()
    {
        $config = $this->createDefaultConfigContainer(true);

        $config->getConfig('app')->expects($this->exactly(2))->method('getValue');
        $config->getConfig('env')->expects($this->exactly(2))->method('getValue');

        $config->getValue('name');
        $config->setConfig('app', $config->getConfig('app'), 0);
        $config->getValue('name');
    }


    public function testArrayMerging()
    {
        $config = $this->createDefaultConfigContainer();
        $this->assertEquals(
            ['one' => 1, 'two' => 2, 'three' => 3, 'sub_array' => ['x', 'y' => 'z']],
            $config->getValue('array')
        );
    }


    public function testDisabledArrayMerging()
    {
        $config = $this->createDefaultConfigContainer();
        $config->useArrayValuesMerging(false);
        $this->assertEquals(['three' => 3, 'sub_array' => ['x']], $config->getValue('array'));
    }


    public function testPriorities()
    {
        $config = $this->createDefaultConfigContainer();
        $this->assertEquals('env', $config->getValue('priority'));
        $config->setConfig('app', $config->getConfig('app'), 2);
        $this->assertEquals('app', $config->getValue('priority'));
    }


    private function createDefaultConfigContainer(bool $useMocks = false): ConfigContainer
    {
        $config = new ConfigContainer();
        $applicationConfig = $this->createJsonConfig(__DIR__ . '/config/app.json', $useMocks);
        $environmentConfig = $this->createJsonConfig(__DIR__ . '/config/env.json', $useMocks);
        $config->setConfig('app', $applicationConfig, 0);
        $config->setConfig('env', $environmentConfig, 1);
        return $config;
    }
}
