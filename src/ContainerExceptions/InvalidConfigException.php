<?php
declare(strict_types=1);

namespace TutuRu\Config\ContainerExceptions;

use TutuRu\Config\Exceptions\InvalidConfigExceptionInterface;

/**
 * @internal use interfaces in your code
 */
class InvalidConfigException extends ConfigException implements InvalidConfigExceptionInterface
{
}