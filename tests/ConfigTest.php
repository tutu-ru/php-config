<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config;

use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use TutuRu\Config\Config;
use TutuRu\Config\Exceptions\ConfigNodeNotExist;
use TutuRu\Config\Exceptions\InvalidConfigException;
use TutuRu\Tests\Config\Implementations\ApplicationConfig;
use TutuRu\Tests\Config\Implementations\EnvironmentConfig;
use TutuRu\Tests\Config\Implementations\MutableApplicationConfig;

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

        /** @var ApplicationConfig|MockObject $applicationConfig */
        $applicationConfig = $this->getMockBuilder(ApplicationConfig::class)
            ->setConstructorArgs([['name' => 'test']])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        /** @var EnvironmentConfig|MockObject $environmentConfig */
        $environmentConfig = $this->getMockBuilder(EnvironmentConfig::class)
            ->setConstructorArgs([['service' => ['debug' => '1']]])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        $config->setApplicationConfig($applicationConfig);
        $config->setEnvironmentConfig($environmentConfig);

        $applicationConfig->expects($this->exactly(1))->method('getValue');
        $environmentConfig->expects($this->exactly(1))->method('getValue');

        $config->getValue('name');
        $config->getValue('name');
    }


    public function testGetValueRuntimeCacheReset()
    {
        $config = new Config();

        /** @var MutableApplicationConfig|MockObject $applicationConfig */
        $applicationConfig = $this->getMockBuilder(MutableApplicationConfig::class)
            ->setConstructorArgs([['name' => 'test']])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        /** @var EnvironmentConfig|MockObject $environmentConfig */
        $environmentConfig = $this->getMockBuilder(EnvironmentConfig::class)
            ->setConstructorArgs([['service' => ['debug' => '1']]])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        $config->setApplicationConfig($applicationConfig);
        $config->setEnvironmentConfig($environmentConfig);

        $applicationConfig->expects($this->exactly(2))->method('getValue');
        $environmentConfig->expects($this->exactly(2))->method('getValue');

        $config->getValue('name');
        $config->setApplicationValue('name', 'new value');
        $config->getValue('name');
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
