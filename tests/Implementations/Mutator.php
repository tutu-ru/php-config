<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config\Implementations;

use TutuRu\Config\MutatorInterface;

class Mutator implements MutatorInterface
{
    private $type;


    public function __construct(string $type)
    {
        $this->type = $type;
    }


    public function init()
    {
    }


    public function copy(string $pathFrom, string $pathTo)
    {
    }


    public function delete(string $path)
    {
    }


    public function setValue(string $path, $value)
    {
    }


    public function getValue(string $path)
    {
        return $this->type;
    }
}
