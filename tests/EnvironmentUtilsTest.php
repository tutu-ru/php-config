<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config;

use TutuRu\Config\EnvironmentUtils;

class EnvironmentUtilsTest extends BaseTest
{
    public function testHostname()
    {
        putenv("HOSTNAME=phpunit");
        $this->assertEquals('phpunit', EnvironmentUtils::getServerHostname());
    }


    public function testHostnameExt()
    {
        putenv("HOSTNAME_EXT=phpunit.ext");
        putenv("HOSTNAME=phpunit");
        $this->assertEquals('phpunit.ext', EnvironmentUtils::getServerHostname());
    }
}
