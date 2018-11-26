<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config;

use TutuRu\Config\Config;
use TutuRu\Config\Exceptions\BusinessConfigUpdateException;
use TutuRu\Config\Exceptions\ConfigException;
use TutuRu\Config\Exceptions\ConfigNodeNotExist;
use TutuRu\Config\MutatorInterface;
use TutuRu\Tests\Config\Implementations\EnvironmentConfig;

class EnvironmentConfigTest extends BaseTest
{
    public function testInitializeWithEnvironmentConfig()
    {
        $config = new Config();
        $environmentConfig = new EnvironmentConfig($this->getDefaultConfigData());
        $config->setEnvironmentConfig($environmentConfig);

        $this->assertSame($environmentConfig, $config->getEnvironmentConfig());
    }


    public function testGetValueWithNotInitializedConfig()
    {
        $config = new Config();

        $this->expectException(ConfigException::class);
        $config->getEnvironmentValue('test');
    }


    public function testGetBusinessValueWithNotInitializedConfig()
    {
        $config = new Config();

        $this->expectException(ConfigException::class);
        $config->getEnvironmentBusinessValue('test');
    }


    public function testGetServiceValueWithNotInitializedConfig()
    {
        $config = new Config();

        $this->expectException(ConfigException::class);
        $config->getEnvironmentServiceValue('test');
    }


    public function testGetInfrastructureValueWithNotInitializedConfig()
    {
        $config = new Config();

        $this->expectException(ConfigException::class);
        $config->getEnvironmentInfrastructureValue('test');
    }


    public function testUpdateBusinessValueWithNotInitializedConfig()
    {
        $config = new Config();

        $this->expectException(ConfigException::class);
        $config->updateEnvironmentBusinessValue('test', 'test');
    }


    public function testGetServiceMutatorWithNotInitializedConfig()
    {
        $config = new Config();

        $this->expectException(ConfigException::class);
        $config->getEnvironmentServiceMutator();
    }


    public function testGetBusinessMutatorWithNotInitializedConfig()
    {
        $config = new Config();

        $this->expectException(ConfigException::class);
        $config->getEnvironmentBusinessMutator();
    }


    private function getDefaultConfigData()
    {
        return [
            'service'  => ['test' => 'value'],
            'business' => ['name' => 'test'],
            'infra'    => ['connection' => 'default'],
        ];
    }


    public function testGetValue()
    {
        $config = new Config();
        $environmentConfig = new EnvironmentConfig($this->getDefaultConfigData());
        $config->setEnvironmentConfig($environmentConfig);

        $this->assertEquals('value', $config->getEnvironmentValue('test'));
        $this->assertEquals('value', $config->getEnvironmentServiceValue('test'));
        $this->assertEquals('test', $config->getEnvironmentBusinessValue('name'));
        $this->assertEquals('default', $config->getEnvironmentInfrastructureValue('connection'));
    }


    public function testGetValueNotExist()
    {
        $config = new Config();
        $environmentConfig = new EnvironmentConfig($this->getDefaultConfigData());
        $config->setEnvironmentConfig($environmentConfig);

        $this->assertEquals(null, $config->getEnvironmentValue('not_existing_value'));
        $this->assertEquals(null, $config->getEnvironmentServiceValue('not_existing_value'));
        $this->assertEquals(null, $config->getEnvironmentBusinessValue('not_existing_value'));
        $this->assertEquals(null, $config->getEnvironmentInfrastructureValue('not_existing_value'));
    }


    public function testGetValueNotExistWithDefault()
    {
        $config = new Config();
        $environmentConfig = new EnvironmentConfig($this->getDefaultConfigData());
        $config->setEnvironmentConfig($environmentConfig);

        $this->assertEquals('default', $config->getEnvironmentValue('not_existing_value', 'default'));
        $this->assertEquals('default', $config->getEnvironmentServiceValue('not_existing_value', 'default'));
        $this->assertEquals('default', $config->getEnvironmentBusinessValue('not_existing_value', 'default'));
        $this->assertEquals('default', $config->getEnvironmentInfrastructureValue('not_existing_value', 'default'));
    }


    public function testGetValueNotExistButRequired()
    {
        $config = new Config();
        $environmentConfig = new EnvironmentConfig($this->getDefaultConfigData());
        $config->setEnvironmentConfig($environmentConfig);

        $this->expectException(ConfigNodeNotExist::class);
        $config->getEnvironmentValue('not_existing_value', 'default', true);
    }


    public function testGetServiceValueNotExistButRequired()
    {
        $config = new Config();
        $environmentConfig = new EnvironmentConfig($this->getDefaultConfigData());
        $config->setEnvironmentConfig($environmentConfig);

        $this->expectException(ConfigNodeNotExist::class);
        $config->getEnvironmentServiceValue('not_existing_value', 'default', true);
    }


    public function testGetBusinessValueNotExistButRequired()
    {
        $config = new Config();
        $environmentConfig = new EnvironmentConfig($this->getDefaultConfigData());
        $config->setEnvironmentConfig($environmentConfig);

        $this->expectException(ConfigNodeNotExist::class);
        $config->getEnvironmentBusinessValue('not_existing_value', 'default', true);
    }


    public function testGetInfrastructureValueNotExistButRequired()
    {
        $config = new Config();
        $environmentConfig = new EnvironmentConfig($this->getDefaultConfigData());
        $config->setEnvironmentConfig($environmentConfig);

        $this->expectException(ConfigNodeNotExist::class);
        $config->getEnvironmentInfrastructureValue('not_existing_value', 'default', true);
    }


    public function testUpdateBusinessValue()
    {
        $config = new Config();
        $environmentConfig = new EnvironmentConfig($this->getDefaultConfigData());
        $config->setEnvironmentConfig($environmentConfig);

        $config->updateEnvironmentBusinessValue('name', 'new name');
        $this->assertEquals('new name', $config->getEnvironmentBusinessValue('name'));
    }


    public function testUpdateNotExistingBusinessValue()
    {
        $config = new Config();
        $environmentConfig = new EnvironmentConfig($this->getDefaultConfigData());
        $config->setEnvironmentConfig($environmentConfig);

        $this->expectException(BusinessConfigUpdateException::class);
        $config->updateEnvironmentBusinessValue('test', 'new name');
    }


    public function testGetServiceMutator()
    {
        $config = new Config();
        $environmentConfig = new EnvironmentConfig($this->getDefaultConfigData());
        $config->setEnvironmentConfig($environmentConfig);

        $this->assertInstanceOf(MutatorInterface::class, $config->getEnvironmentServiceMutator());
        $this->assertEquals('service', $config->getEnvironmentServiceMutator()->getValue('test'));
    }


    public function testGetBusinessMutator()
    {
        $config = new Config();
        $environmentConfig = new EnvironmentConfig($this->getDefaultConfigData());
        $config->setEnvironmentConfig($environmentConfig);

        $this->assertInstanceOf(MutatorInterface::class, $config->getEnvironmentBusinessMutator());
        $this->assertEquals('business', $config->getEnvironmentBusinessMutator()->getValue('test'));
    }
}
