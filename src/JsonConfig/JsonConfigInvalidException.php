<?php
declare(strict_types=1);

namespace TutuRu\Config\JsonConfig;

use TutuRu\Config\Exception\InvalidConfigExceptionInterface;

class JsonConfigInvalidException extends \Exception implements InvalidConfigExceptionInterface
{
}
