<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config\JsonConfig;

use TutuRu\Config\ConfigContainer;

class JsonConfigFactory
{
    public static function createConfig(string $appFilename, ?string $enfFilename = null): ConfigContainer
    {
        $config = new ConfigContainer();
        $config->setApplicationConfig(new MutableApplicationJsonConfig($appFilename));
        $config->setEnvironmentConfig(EnvironmentJsonConfig::createFromOneFile($enfFilename ?? $appFilename));
        return $config;
    }
}
