<?php
declare(strict_types=1);

namespace TutuRu\Config;

interface EnvironmentConfigInterface extends ConfigInterface
{
    public function getBusinessValue(string $path);

    public function updateBusinessValue(string $path, $value);

    public function getServiceValue(string $path);

    public function getInfrastructureValue(string $path);

    public function getBusinessMutator(): ?MutatorInterface;

    public function getServiceMutator(): ?MutatorInterface;
}
