<?php
declare(strict_types=1);

namespace TutuRu\Config;

interface MutatorInterface
{
    public function init();


    /**
     * @param string $pathFrom
     * @param string $pathTo
     */
    public function copy($pathFrom, $pathTo);


    /**
     * @param string $path
     */
    public function delete($path);


    /**
     * @param string $path
     * @param mixed  $value
     */
    public function setValue($path, $value);


    /**
     * @param string $path
     * @return mixed
     */
    public function getValue($path);
}
