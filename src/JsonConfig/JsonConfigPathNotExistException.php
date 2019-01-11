<?php
declare(strict_types=1);

namespace TutuRu\Config\JsonConfig;

use TutuRu\Config\Exception\ConfigPathNotExistExceptionInterface;

class JsonConfigPathNotExistException extends \Exception implements ConfigPathNotExistExceptionInterface
{
}
