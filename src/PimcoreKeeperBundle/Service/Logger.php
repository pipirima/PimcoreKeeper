<?php

namespace Pipirima\PimcoreKeeperBundle\Service;

use Pimcore\Log\Simple;

class Logger
{
    const LOG_FILE = 'pimcore_keeper';

    public function log(string $message)
    {
        Simple::log(self::LOG_FILE, $message);
    }
}
