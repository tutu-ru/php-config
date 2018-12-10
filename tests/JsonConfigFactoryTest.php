<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config;

use TutuRu\Config\Exceptions\InvalidConfigExceptionInterface;
use TutuRu\Tests\Config\JsonConfig\JsonConfigFactory;

class JsonConfigFactoryTest extends BaseTest
{
    public function testCreateWithEmptyFilename()
    {
        $this->expectException(InvalidConfigExceptionInterface::class);
        JsonConfigFactory::createConfig('');
    }


    public function testCreateWithNotExistingFile()
    {
        $this->expectException(InvalidConfigExceptionInterface::class);
        JsonConfigFactory::createConfig('not/existing/file.json');
    }


    public function testCreateWithBrokenJson()
    {
        $this->expectException(InvalidConfigExceptionInterface::class);
        JsonConfigFactory::createConfig(__DIR__ . '/config/broken.json');
    }


    public function testWithAppConfig()
    {
        $config = JsonConfigFactory::createConfig(__DIR__ . '/config/application.json');

        $this->assertEquals('test', $config->getValue('name'));
        $this->assertEquals('test', $config->getApplicationValue('name'));
        $this->assertEquals('test', $config->getEnvironmentServiceValue('name'));
        $this->assertEquals('test', $config->getEnvironmentBusinessValue('name'));
        $this->assertEquals('test', $config->getEnvironmentInfrastructureValue('name'));
    }


    public function testWithAppAndEnvConfig()
    {
        $config = JsonConfigFactory::createConfig(
            __DIR__ . '/config/application.json',
            __DIR__ . '/config/env_business.json'
        );

        $this->assertEquals('test business', $config->getValue('name'));
        $this->assertEquals('test', $config->getApplicationValue('name'));
        $this->assertEquals('test business', $config->getEnvironmentServiceValue('name'));
        $this->assertEquals('test business', $config->getEnvironmentBusinessValue('name'));
        $this->assertEquals('test business', $config->getEnvironmentInfrastructureValue('name'));
    }
}
