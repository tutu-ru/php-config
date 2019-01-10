<?php
declare(strict_types=1);

namespace TutuRu\Config\ContainerException;

use TutuRu\Config\Exception\ConfigPathNotExistExceptionInterface;

/**
 * @internal use interfaces in your code
 */
class PathNotExistsException extends \Exception implements ConfigPathNotExistExceptionInterface
{
}
