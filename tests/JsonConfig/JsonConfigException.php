<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config\JsonConfig;

use TutuRu\Config\Exception\ConfigPathNotExistExceptionInterface;
use TutuRu\Config\Exception\InvalidConfigExceptionInterface;

class JsonConfigException extends \Exception implements
    InvalidConfigExceptionInterface,
    ConfigPathNotExistExceptionInterface
{
}
