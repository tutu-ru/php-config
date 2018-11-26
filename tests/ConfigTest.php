<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config;

use TutuRu\Config\Config;
use TutuRu\Config\Exceptions\ConfigNodeNotExist;
use TutuRu\Config\Exceptions\InvalidConfigException;
use TutuRu\Tests\Config\Implementations\ApplicationConfig;
use TutuRu\Tests\Config\Implementations\EnvironmentConfig;

class ConfigTest extends BaseTest
{
    public function testNotInitializedConfig()
    {
        $config = new Config();

        $this->assertNull($config->getApplicationConfig());
        $this->assertNull($config->getEnvironmentConfig());

        $this->expectException(InvalidConfigException::class);
        $this->assertNull($config->getValue('some.node'));
    }


    public function testGetValue()
    {
        $config = new Config();

        $applicationConfig = new ApplicationConfig(['name' => 'test']);
        $environmentConfig = new EnvironmentConfig(['service' => ['debug' => '1']]);

        $config->setApplicationConfig($applicationConfig);
        $config->setEnvironmentConfig($environmentConfig);

        $this->assertEquals('test', $config->getValue('name'));
        $this->assertEquals('1', $config->getValue('debug'));
    }


    public function testGetValueRuntimeCache()
    {
        $config = new Config();

        $applicationConfig = new ApplicationConfig(['name' => 'test']);
        $environmentConfig = new EnvironmentConfig(['service' => ['debug' => '1']]);

        $config->setApplicationConfig($applicationConfig);
        $config->setEnvironmentConfig($environmentConfig);

        $this->assertEquals('test', $config->getValue('name'));

        $applicationConfig->setValue('name', 'new test');
        $this->assertEquals('test', $config->getValue('name'));
    }


    public function testGetValueNotExist()
    {
        $config = new Config();

        $applicationConfig = new ApplicationConfig(['name' => 'test']);
        $environmentConfig = new EnvironmentConfig(['service' => ['debug' => '1']]);

        $config->setApplicationConfig($applicationConfig);
        $config->setEnvironmentConfig($environmentConfig);

        $this->assertEquals(null, $config->getValue('not_exist'));
    }


    public function testGetValueNotExistWithDefault()
    {
        $config = new Config();

        $applicationConfig = new ApplicationConfig(['name' => 'test']);
        $environmentConfig = new EnvironmentConfig(['service' => ['debug' => '1']]);

        $config->setApplicationConfig($applicationConfig);
        $config->setEnvironmentConfig($environmentConfig);

        $this->assertEquals('default', $config->getValue('not_exist', 'default'));
    }


    public function testGetValueNotExistWithRequired()
    {
        $config = new Config();

        $applicationConfig = new ApplicationConfig(['name' => 'test']);
        $environmentConfig = new EnvironmentConfig(['service' => ['debug' => '1']]);

        $config->setApplicationConfig($applicationConfig);
        $config->setEnvironmentConfig($environmentConfig);

        $this->expectException(ConfigNodeNotExist::class);
        $config->getValue('not_exist', null, true);
    }


    public function testDefaultPriorities()
    {
        $config = new Config();

        $applicationConfig = new ApplicationConfig(['name' => 'test 1']);
        $environmentConfig = new EnvironmentConfig(['service' => ['name' => 'test 2']]);

        $config->setApplicationConfig($applicationConfig);
        $config->setEnvironmentConfig($environmentConfig);

        $this->assertEquals('test 2', $config->getValue('name'));
    }


    public function testCustomPriorities()
    {
        $config = new Config();

        $applicationConfig = new ApplicationConfig(['name' => 'test 1']);
        $environmentConfig = new EnvironmentConfig(['service' => ['name' => 'test 2']]);

        $config->setApplicationConfig($applicationConfig, 3);
        $config->setEnvironmentConfig($environmentConfig, 2);

        $this->assertEquals('test 1', $config->getValue('name'));
    }
}
