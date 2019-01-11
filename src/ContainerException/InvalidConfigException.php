<?php
declare(strict_types=1);

namespace TutuRu\Config\ContainerException;

use TutuRu\Config\Exception\InvalidConfigExceptionInterface;

/**
 * @internal use interfaces in your code
 */
class InvalidConfigException extends ConfigException implements InvalidConfigExceptionInterface
{
}
