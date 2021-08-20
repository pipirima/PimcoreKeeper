<?php

namespace Pipirima\PimcoreKeeperBundle\Service;

use Pipirima\PimcoreKeeperBundle\PimcoreKeeperBundle;

/**
 * Class Config
 * @package Pipirima\PimcoreKeeperBundle\Service
 */
class ConfigService
{
    protected array $config;

    /**
     * Config constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}
