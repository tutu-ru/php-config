<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config;

use PHPUnit\Framework\TestCase;
use TutuRu\Config\JsonConfig\JsonConfig;
use TutuRu\Config\JsonConfig\MutableJsonConfig;

abstract class BaseTest extends TestCase
{
    protected function createJsonConfig(string $filename, bool $mock = false): JsonConfig
    {
        if ($mock) {
            $config = $this->getMockBuilder(JsonConfig::class)
                ->setConstructorArgs([$filename])
                ->enableProxyingToOriginalMethods()
                ->getMock();
        } else {
            $config = new JsonConfig($filename);
        }
        return $config;
    }


    protected function createMutableJsonConfig(string $filename, bool $mock = false): MutableJsonConfig
    {
        if ($mock) {
            $config = $this->getMockBuilder(MutableJsonConfig::class)
                ->setConstructorArgs([$filename])
                ->enableProxyingToOriginalMethods()
                ->getMock();
        } else {
            $config = new MutableJsonConfig($filename);
        }
        return $config;
    }
}
