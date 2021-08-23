<?php

namespace Pipirima\PimcoreKeeperBundle\Service;

interface EventHandlerInterface
{
    public function handle(array $config, array $arguments);
}
