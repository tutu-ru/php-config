<?php
declare(strict_types=1);

namespace TutuRu\Config;

interface MutatorInterface
{
    public function init();

    public function copy(string $pathFrom, string $pathTo);

    public function delete(string $path);

    public function setValue(string $path, $value);

    public function getValue(string $path);
}
