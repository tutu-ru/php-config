<?php
declare(strict_types=1);

namespace TutuRu\Config;

class EnvironmentUtils
{
    public static function getServerHostname(): string
    {
        return getenv('HOSTNAME_EXT') ?: (getenv('HOSTNAME') ?: php_uname('n'));
    }
}
