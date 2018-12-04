<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config\Implementations;

use TutuRu\Config\ContainerExceptions\UpdateForbiddenException;
use TutuRu\Config\EnvironmentConfigInterface;
use TutuRu\Config\MutatorInterface;

class EnvironmentConfig implements EnvironmentConfigInterface
{
    private $data;
    private $loadedData;


    public function __construct($data)
    {
        $this->data = $data;
    }

    public function load()
    {
        $this->loadedData = $this->data;
    }

    public function getValue(string $path)
    {
        $value = null;
        foreach (['service', 'business', 'infra'] as $type) {
            $value = $this->loadedData[$type][$path] ?? null;
            if (!is_null($value)) {
                break;
            }
        }
        return $value;
    }

    public function getBusinessValue(string $path)
    {
        return $this->loadedData['business'][$path] ?? null;
    }

    public function updateBusinessValue(string $path, $value)
    {
        $currentValue = $this->getBusinessValue($path);
        if (is_null($currentValue)) {
            throw new UpdateForbiddenException($path);
        }
        $this->loadedData['business'][$path] = $value;
        $this->data['business'][$path] = $value;
    }

    public function getServiceValue(string $path)
    {
        return $this->loadedData['service'][$path] ?? null;
    }

    public function getInfrastructureValue(string $path)
    {
        return $this->loadedData['infra'][$path] ?? null;
    }

    public function getBusinessMutator(): ?MutatorInterface
    {
        return new Mutator('business');
    }

    public function getServiceMutator(): ?MutatorInterface
    {
        return new Mutator('service');
    }
}
