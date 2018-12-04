<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config;

use TutuRu\Config\ConfigContainer;
use TutuRu\Config\Exceptions\ConfigPathNotExistExceptionInterface;
use TutuRu\Config\Exceptions\ConfigUpdateForbiddenExceptionInterface;
use TutuRu\Config\Exceptions\InvalidConfigExceptionInterface;
use TutuRu\Tests\Config\Implementations\ApplicationConfig;
use TutuRu\Tests\Config\Implementations\MutableApplicationConfig;

class ApplicationConfigTest extends BaseTest
{
    public function testInitializeWithApplicationConfig()
    {
        $config = new ConfigContainer();
        $applicationConfig = new ApplicationConfig(['test' => 'value']);
        $config->setApplicationConfig($applicationConfig);

        $this->assertSame($applicationConfig, $config->getApplicationConfig());
    }


    public function testGetValueWithNotInitializedConfig()
    {
        $config = new ConfigContainer();

        $this->expectException(InvalidConfigExceptionInterface::class);
        $config->getApplicationValue('test');
    }


    public function testGetValue()
    {
        $config = new ConfigContainer();
        $applicationConfig = new ApplicationConfig(['test' => 'value']);
        $config->setApplicationConfig($applicationConfig);

        $this->assertEquals('value', $config->getApplicationValue('test'));
    }


    public function testGetValueNotExist()
    {
        $config = new ConfigContainer();
        $applicationConfig = new ApplicationConfig(['test' => 'value']);
        $config->setApplicationConfig($applicationConfig);

        $this->assertEquals(null, $config->getApplicationValue('not_existing_value'));
    }


    public function testGetValueNotExistWithDefault()
    {
        $config = new ConfigContainer();
        $applicationConfig = new ApplicationConfig(['test' => 'value']);
        $config->setApplicationConfig($applicationConfig);

        $this->assertEquals('default', $config->getApplicationValue('not_existing_value', 'default'));
    }


    public function testGetValueNotExistButRequired()
    {
        $config = new ConfigContainer();
        $applicationConfig = new ApplicationConfig(['test' => 'value']);
        $config->setApplicationConfig($applicationConfig);

        $this->expectException(ConfigPathNotExistExceptionInterface::class);
        $config->getApplicationValue('not_existing_value', 'default', true);
    }


    public function testSetValue()
    {
        $config = new ConfigContainer();
        $applicationConfig = new ApplicationConfig(['test' => 'value']);
        $config->setApplicationConfig($applicationConfig);

        $this->expectException(ConfigUpdateForbiddenExceptionInterface::class);
        $config->setApplicationValue('test', 'new value');
    }


    public function testSetValueWithMutableConfig()
    {
        $config = new ConfigContainer();
        $applicationConfig = new MutableApplicationConfig(['test' => 'value']);
        $config->setApplicationConfig($applicationConfig);

        $config->setApplicationValue('test', 'new value');
        $this->assertEquals('new value', $config->getApplicationValue('test'));
    }
}
