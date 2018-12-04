<?php
declare(strict_types=1);

namespace TutuRu\Config\ContainerExceptions;

use TutuRu\Config\Exceptions\ConfigUpdateForbiddenExceptionInterface;

/**
 * @internal use interfaces in your code
 */
class UpdateForbiddenException extends ConfigException implements ConfigUpdateForbiddenExceptionInterface
{
}
