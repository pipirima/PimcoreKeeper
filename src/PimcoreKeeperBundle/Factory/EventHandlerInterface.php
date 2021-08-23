<?php

namespace Pipirima\PimcoreKeeperBundle\Factory;

interface EventHandlerInterface
{
    public function handle(array $config, array $arguments);
}
