<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config;

use PHPUnit\Framework\MockObject\MockObject;
use TutuRu\Config\ConfigContainer;
use TutuRu\Config\Exceptions\ConfigPathNotExistExceptionInterface;
use TutuRu\Config\Exceptions\InvalidConfigExceptionInterface;
use TutuRu\Tests\Config\Implementations\ApplicationConfig;
use TutuRu\Tests\Config\Implementations\EnvironmentConfig;
use TutuRu\Tests\Config\Implementations\MutableApplicationConfig;

class ConfigContainerTest extends BaseTest
{
    public function testNotInitializedConfig()
    {
        $config = new ConfigContainer();

        $this->assertNull($config->getApplicationConfig());
        $this->assertNull($config->getEnvironmentConfig());

        $this->expectException(InvalidConfigExceptionInterface::class);
        $this->assertNull($config->getValue('some.node'));
    }


    public function testGetValue()
    {
        $config = new ConfigContainer();

        $applicationConfig = new ApplicationConfig(['name' => 'test']);
        $environmentConfig = new EnvironmentConfig(['service' => ['debug' => '1']]);

        $config->setApplicationConfig($applicationConfig);
        $config->setEnvironmentConfig($environmentConfig);

        $this->assertEquals('test', $config->getValue('name'));
        $this->assertEquals('1', $config->getValue('debug'));
    }


    public function testGetValueRuntimeCache()
    {
        $config = new ConfigContainer();

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
        $config = new ConfigContainer();

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
        $config = new ConfigContainer();

        $applicationConfig = new ApplicationConfig(['name' => 'test']);
        $environmentConfig = new EnvironmentConfig(['service' => ['debug' => '1']]);

        $config->setApplicationConfig($applicationConfig);
        $config->setEnvironmentConfig($environmentConfig);

        $this->assertEquals(null, $config->getValue('not_exist'));
    }


    public function testGetValueNotExistWithDefault()
    {
        $config = new ConfigContainer();

        $applicationConfig = new ApplicationConfig(['name' => 'test']);
        $environmentConfig = new EnvironmentConfig(['service' => ['debug' => '1']]);

        $config->setApplicationConfig($applicationConfig);
        $config->setEnvironmentConfig($environmentConfig);

        $this->assertEquals('default', $config->getValue('not_exist', 'default'));
    }


    public function testGetValueNotExistWithRequired()
    {
        $config = new ConfigContainer();

        $applicationConfig = new ApplicationConfig(['name' => 'test']);
        $environmentConfig = new EnvironmentConfig(['service' => ['debug' => '1']]);

        $config->setApplicationConfig($applicationConfig);
        $config->setEnvironmentConfig($environmentConfig);

        $this->expectException(ConfigPathNotExistExceptionInterface::class);
        $config->getValue('not_exist', null, true);
    }


    public function testDefaultPriorities()
    {
        $config = new ConfigContainer();

        $applicationConfig = new ApplicationConfig(['name' => 'test 1']);
        $environmentConfig = new EnvironmentConfig(['service' => ['name' => 'test 2']]);

        $config->setApplicationConfig($applicationConfig);
        $config->setEnvironmentConfig($environmentConfig);

        $this->assertEquals('test 2', $config->getValue('name'));
    }


    public function testCustomPriorities()
    {
        $config = new ConfigContainer();

        $applicationConfig = new ApplicationConfig(['name' => 'test 1']);
        $environmentConfig = new EnvironmentConfig(['service' => ['name' => 'test 2']]);

        $config->setApplicationConfig($applicationConfig, 3);
        $config->setEnvironmentConfig($environmentConfig, 2);

        $this->assertEquals('test 1', $config->getValue('name'));
    }
}
