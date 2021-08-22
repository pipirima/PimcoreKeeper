<?php

namespace Pipirima\PimcoreKeeperBundle\Factory;

interface EventHandlerInterface
{
    public function setConfig(array $config);
    public function handle(array $arguments);
}
