<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config;

use PHPUnit\Framework\MockObject\MockObject;
use TutuRu\Config\ConfigContainer;
use TutuRu\Config\Exceptions\ConfigPathNotExistExceptionInterface;
use TutuRu\Config\Exceptions\InvalidConfigExceptionInterface;
use TutuRu\Tests\Config\JsonConfig\ApplicationJsonConfig;
use TutuRu\Tests\Config\JsonConfig\EnvironmentJsonConfig;
use TutuRu\Tests\Config\JsonConfig\MutableApplicationJsonConfig;

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

        $applicationConfig = new ApplicationJsonConfig(__DIR__ . '/config/application.json');
        $environmentConfig = EnvironmentJsonConfig::createFromOneFile(__DIR__ . '/config/env_service.json');

        $config->setApplicationConfig($applicationConfig);
        $config->setEnvironmentConfig($environmentConfig);

        $this->assertEquals('test', $config->getValue('name'));
        $this->assertEquals('1', $config->getValue('debug'));
    }


    public function testGetValueRuntimeCache()
    {
        $config = new ConfigContainer();

        /** @var ApplicationJsonConfig|MockObject $applicationConfig */
        $applicationConfig = $this->getMockBuilder(ApplicationJsonConfig::class)
            ->setConstructorArgs([__DIR__ . '/config/application.json'])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        /** @var EnvironmentJsonConfig|MockObject $environmentConfig */
        $environmentConfig = $this->getMockBuilder(EnvironmentJsonConfig::class)
            ->setConstructorArgs(array_fill(0, 3, __DIR__ . '/config/env_service.json'))
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

        /** @var MutableApplicationJsonConfig|MockObject $applicationConfig */
        $applicationConfig = $this->getMockBuilder(MutableApplicationJsonConfig::class)
            ->setConstructorArgs([__DIR__ . '/config/application.json'])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        /** @var EnvironmentJsonConfig|MockObject $environmentConfig */
        $environmentConfig = $this->getMockBuilder(EnvironmentJsonConfig::class)
            ->setConstructorArgs(array_fill(0, 3, __DIR__ . '/config/env_service.json'))
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

        $applicationConfig = new ApplicationJsonConfig(__DIR__ . '/config/application.json');
        $environmentConfig = EnvironmentJsonConfig::createFromOneFile(__DIR__ . '/config/env_service.json');

        $config->setApplicationConfig($applicationConfig);
        $config->setEnvironmentConfig($environmentConfig);

        $this->assertEquals(null, $config->getValue('not_exist'));
    }


    public function testGetValueNotExistWithDefault()
    {
        $config = new ConfigContainer();

        $applicationConfig = new ApplicationJsonConfig(__DIR__ . '/config/application.json');
        $environmentConfig = EnvironmentJsonConfig::createFromOneFile(__DIR__ . '/config/env_service.json');

        $config->setApplicationConfig($applicationConfig);
        $config->setEnvironmentConfig($environmentConfig);

        $this->assertEquals('default', $config->getValue('not_exist', 'default'));
    }


    public function testGetValueNotExistWithRequired()
    {
        $config = new ConfigContainer();

        $applicationConfig = new ApplicationJsonConfig(__DIR__ . '/config/application.json');
        $environmentConfig = EnvironmentJsonConfig::createFromOneFile(__DIR__ . '/config/env_service.json');

        $config->setApplicationConfig($applicationConfig);
        $config->setEnvironmentConfig($environmentConfig);

        $this->expectException(ConfigPathNotExistExceptionInterface::class);
        $config->getValue('not_exist', null, true);
    }


    public function testDefaultPriorities()
    {
        $config = new ConfigContainer();

        $applicationConfig = new ApplicationJsonConfig(__DIR__ . '/config/application.json');
        $environmentConfig = EnvironmentJsonConfig::createFromOneFile(__DIR__ . '/config/env_business.json');

        $config->setApplicationConfig($applicationConfig);
        $config->setEnvironmentConfig($environmentConfig);

        $this->assertEquals('test business', $config->getValue('name'));
    }


    public function testCustomPriorities()
    {
        $config = new ConfigContainer();

        $applicationConfig = new ApplicationJsonConfig(__DIR__ . '/config/application.json');
        $environmentConfig = EnvironmentJsonConfig::createFromOneFile(__DIR__ . '/config/env_business.json');

        $config->setApplicationConfig($applicationConfig, 3);
        $config->setEnvironmentConfig($environmentConfig, 2);

        $this->assertEquals('test', $config->getValue('name'));
    }
}
