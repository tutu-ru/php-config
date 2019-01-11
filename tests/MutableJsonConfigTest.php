<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config;

class MutableJsonConfigTest extends BaseTest
{
    /**
     * @dataProvider setValueDataProvider
     */
    public function testSetValueWithMutableConfig($value)
    {
        $config = $this->createMutableJsonConfig(__DIR__ . '/config/app.json');
        $config->setValue('name', $value);
        $this->assertEquals($value, $config->getValue('name'));
    }


    /**
     * @dataProvider setValueDataProvider
     */
    public function testSetValueWithMutableConfigForNewNode($value)
    {
        $config = $this->createMutableJsonConfig(__DIR__ . '/config/app.json');

        $config->setValue('new.node', $value);
        $this->assertEquals($value, $config->getValue('new.node'));

        $config->setValue('name.test', $value);
        $this->assertEquals($value, $config->getValue('name.test'));
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
