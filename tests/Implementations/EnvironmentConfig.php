<?php
declare(strict_types=1);

namespace TutuRu\Tests\Config\Implementations;

use TutuRu\Config\EnvironmentConfigInterface;
use TutuRu\Config\Exceptions\BusinessConfigUpdateException;
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

    public function getValue(string $configId)
    {
        $value = null;
        foreach (['service', 'business', 'infra'] as $type) {
            $value = $this->loadedData[$type][$configId] ?? null;
            if (!is_null($value)) {
                break;
            }
        }
        return $value;
    }

    public function getBusinessValue(string $configId)
    {
        return $this->loadedData['business'][$configId] ?? null;
    }

    /**
     * @param string $configId
     * @param mixed  $value
     *
     * @throws BusinessConfigUpdateException
     * @return void
     */
    public function updateBusinessValue(string $configId, $value)
    {
        $currentValue = $this->getBusinessValue($configId);
        if (is_null($currentValue)) {
            throw new BusinessConfigUpdateException($configId);
        }
        $this->loadedData['business'][$configId] = $value;
        $this->data['business'][$configId] = $value;
    }

    public function getServiceValue(string $configId)
    {
        return $this->loadedData['service'][$configId] ?? null;
    }

    public function getInfrastructureValue(string $configId)
    {
        return $this->loadedData['infra'][$configId] ?? null;
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
