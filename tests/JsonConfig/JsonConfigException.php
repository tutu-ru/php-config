<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config\JsonConfig;

use TutuRu\Config\Exceptions\ConfigUpdateForbiddenExceptionInterface;
use TutuRu\Config\Exceptions\InvalidConfigExceptionInterface;

class JsonConfigException extends \Exception implements
    InvalidConfigExceptionInterface,
    ConfigUpdateForbiddenExceptionInterface
{
}
