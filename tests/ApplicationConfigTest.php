<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config;

use TutuRu\Config\Config;
use TutuRu\Config\Exceptions\ConfigException;
use TutuRu\Config\Exceptions\ConfigNodeNotExist;
use TutuRu\Tests\Config\Implementations\ApplicationConfig;

class ApplicationConfigTest extends BaseTest
{
    public function testInitializeWithApplicationConfig()
    {
        $config = new Config();
        $applicationConfig = new ApplicationConfig(['test' => 'value']);
        $config->setApplicationConfig($applicationConfig);

        $this->assertSame($applicationConfig, $config->getApplicationConfig());
    }


    public function testGetValueWithNotInitializedConfig()
    {
        $config = new Config();

        $this->expectException(ConfigException::class);
        $config->getApplicationValue('test');
    }


    public function testGetValue()
    {
        $config = new Config();
        $applicationConfig = new ApplicationConfig(['test' => 'value']);
        $config->setApplicationConfig($applicationConfig);

        $this->assertEquals('value', $config->getApplicationValue('test'));
    }


    public function testGetValueNotExist()
    {
        $config = new Config();
        $applicationConfig = new ApplicationConfig(['test' => 'value']);
        $config->setApplicationConfig($applicationConfig);

        $this->assertEquals(null, $config->getApplicationValue('not_existing_value'));
    }


    public function testGetValueNotExistWithDefault()
    {
        $config = new Config();
        $applicationConfig = new ApplicationConfig(['test' => 'value']);
        $config->setApplicationConfig($applicationConfig);

        $this->assertEquals('default', $config->getApplicationValue('not_existing_value', 'default'));
    }


    public function testGetValueNotExistButRequired()
    {
        $config = new Config();
        $applicationConfig = new ApplicationConfig(['test' => 'value']);
        $config->setApplicationConfig($applicationConfig);

        $this->expectException(ConfigNodeNotExist::class);
        $config->getApplicationValue('not_existing_value', 'default', true);
    }


    public function testSetValue()
    {
        $config = new Config();
        $applicationConfig = new ApplicationConfig(['test' => 'value']);
        $config->setApplicationConfig($applicationConfig);

        $config->setApplicationValue('test', 'new value');
        $this->assertEquals('new value', $config->getApplicationValue('test'));
    }
}