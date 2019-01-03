<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config;

use PHPUnit\Framework\TestCase;
use TutuRu\Tests\Config\JsonConfig\ApplicationJsonConfig;
use TutuRu\Tests\Config\JsonConfig\EnvironmentJsonConfig;
use TutuRu\Tests\Config\JsonConfig\MutableApplicationJsonConfig;

abstract class BaseTest extends TestCase
{
    protected function createAppConfig(string $filename, bool $mock = false): ApplicationJsonConfig
    {
        if ($mock) {
            $applicationConfig = $this->getMockBuilder(ApplicationJsonConfig::class)
                ->setConstructorArgs([$filename])
                ->enableProxyingToOriginalMethods()
                ->getMock();
        } else {
            $applicationConfig = new ApplicationJsonConfig($filename);
        }
        $applicationConfig->load();
        return $applicationConfig;
    }


    protected function createMutableAppConfig(string $filename, bool $mock = false): MutableApplicationJsonConfig
    {
        if ($mock) {
            $applicationConfig = $this->getMockBuilder(MutableApplicationJsonConfig::class)
                ->setConstructorArgs([$filename])
                ->enableProxyingToOriginalMethods()
                ->getMock();
        } else {
            $applicationConfig = new MutableApplicationJsonConfig($filename);
        }
        $applicationConfig->load();
        return $applicationConfig;
    }


    protected function createEnvConfig(string $filename, bool $mock = false): EnvironmentJsonConfig
    {
        if ($mock) {
            $environmentConfig = $this->getMockBuilder(EnvironmentJsonConfig::class)
                ->setConstructorArgs(array_fill(0, 3, $filename))
                ->enableProxyingToOriginalMethods()
                ->getMock();
        } else {
            $environmentConfig = EnvironmentJsonConfig::createFromOneFile($filename);
        }
        $environmentConfig->load();
        return $environmentConfig;
    }
}
