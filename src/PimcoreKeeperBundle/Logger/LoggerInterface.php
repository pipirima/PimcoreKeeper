<?php

namespace Pipirima\PimcoreKeeperBundle\Logger;

interface LoggerInterface
{
    public const LOG_FILE = 'pimcore_keeper';

    public function log(string $message);
}
