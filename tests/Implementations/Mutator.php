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
        // TODO: Implement init() method.
    }

    /**
     * @param string $pathFrom
     * @param string $pathTo
     */
    public function copy($pathFrom, $pathTo)
    {
        // TODO: Implement copy() method.
    }

    /**
     * @param string $path
     */
    public function delete($path)
    {
        // TODO: Implement delete() method.
    }

    /**
     * @param string $path
     * @param mixed  $value
     */
    public function setValue($path, $value)
    {
        // TODO: Implement setValue() method.
    }

    /**
     * @param string $path
     * @return mixed
     */
    public function getValue($path)
    {
        return $this->type;
    }
}