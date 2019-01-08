<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config;

use TutuRu\Config\ConfigContainer;
use TutuRu\Config\Exceptions\ConfigPathNotExistExceptionInterface;
use TutuRu\Config\Exceptions\ConfigUpdateForbiddenExceptionInterface;
use TutuRu\Config\Exceptions\InvalidConfigExceptionInterface;

class JsonConfigApplicationTest extends BaseTest
{
    public function testInitializeWithApplicationConfig()
    {
        $config = new ConfigContainer();
        $applicationConfig = $this->createAppConfig(__DIR__ . '/config/application.json');
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
        $applicationConfig = $this->createAppConfig(__DIR__ . '/config/application.json');
        $config->setApplicationConfig($applicationConfig);

        $this->assertEquals('test', $config->getApplicationValue('name'));
    }


    public function testGetValueNotExist()
    {
        $config = new ConfigContainer();
        $applicationConfig = $this->createAppConfig(__DIR__ . '/config/application.json');
        $config->setApplicationConfig($applicationConfig);

        $this->assertEquals(null, $config->getApplicationValue('not_existing_value'));
    }


    public function testGetValueNotExistWithDefault()
    {
        $config = new ConfigContainer();
        $applicationConfig = $this->createAppConfig(__DIR__ . '/config/application.json');
        $config->setApplicationConfig($applicationConfig);

        $this->assertEquals('default', $config->getApplicationValue('not_existing_value', false, 'default'));
    }


    public function testGetValueNotExistButRequired()
    {
        $config = new ConfigContainer();
        $applicationConfig = $this->createAppConfig(__DIR__ . '/config/application.json');
        $config->setApplicationConfig($applicationConfig);

        $this->expectException(ConfigPathNotExistExceptionInterface::class);
        $config->getApplicationValue('not_existing_value', true, 'default');
    }


    public function testGetValueArray()
    {
        $config = new ConfigContainer();
        $applicationConfig = $this->createAppConfig(__DIR__ . '/config/application.json');
        $config->setApplicationConfig($applicationConfig);

        $this->assertEquals(['one' => 1, 'two' => 2], $config->getApplicationValue('array'));
    }


    public function testSetValue()
    {
        $config = new ConfigContainer();
        $applicationConfig = $this->createAppConfig(__DIR__ . '/config/application.json');
        $config->setApplicationConfig($applicationConfig);

        $this->expectException(ConfigUpdateForbiddenExceptionInterface::class);
        $config->setApplicationValue('test', 'new value');
    }


    /**
     * @dataProvider setValueDataProvider
     */
    public function testSetValueWithMutableConfig($value)
    {
        $config = new ConfigContainer();
        $applicationConfig = $this->createMutableAppConfig(__DIR__ . '/config/application.json');
        $config->setApplicationConfig($applicationConfig);

        $config->setApplicationValue('name', $value);
        $this->assertEquals($value, $config->getApplicationValue('name'));
    }


    /**
     * @dataProvider setValueDataProvider
     */
    public function testSetValueWithMutableConfigForNewNode($value)
    {
        $config = new ConfigContainer();
        $applicationConfig = $this->createMutableAppConfig(__DIR__ . '/config/application.json');
        $config->setApplicationConfig($applicationConfig);

        $config->setApplicationValue('new.node', $value);
        $this->assertEquals($value, $config->getApplicationValue('new.node'));

        $config->setApplicationValue('name.test', $value);
        $this->assertEquals($value, $config->getApplicationValue('name.test'));
    }


    public function setValueDataProvider()
    {
        return [
            [null],
            ['a'],
            [['a', 'b']],
            [['a' => 1, 'b' => 2]],
        ];
    }
}
