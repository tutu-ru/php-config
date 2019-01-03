<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config\JsonConfig;

use TutuRu\Config\ConfigContainer;

class JsonConfigFactory
{
    public static function createConfig(string $appFilename, ?string $enfFilename = null): ConfigContainer
    {
        $config = new ConfigContainer();

        $appConfig = new MutableApplicationJsonConfig($appFilename);
        $appConfig->load();
        $config->setApplicationConfig($appConfig);

        $envConfig = EnvironmentJsonConfig::createFromOneFile($enfFilename ?? $appFilename);
        $envConfig->load();
        $config->setEnvironmentConfig($envConfig);

        return $config;
    }
}
