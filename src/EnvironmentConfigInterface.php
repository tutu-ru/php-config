<?php
declare(strict_types=1);

namespace TutuRu\Config;

use TutuRu\Config\Exceptions\BusinessConfigUpdateException;

interface EnvironmentConfigInterface extends ConfigInterface
{
    public function getBusinessValue(string $configId);


    /**
     * @param string $configId
     * @param mixed  $value
     *
     * @throws BusinessConfigUpdateException
     * @return void
     */
    public function updateBusinessValue(string $configId, $value);


    public function getServiceValue(string $configId);


    public function getInfrastructureValue(string $configId);


    public function getBusinessMutator(): ?MutatorInterface;


    public function getServiceMutator(): ?MutatorInterface;
}
