<?php

namespace Pipirima\PimcoreKeeperBundle\Logger;

use Pimcore\Log\Simple;

class Logger implements LoggerInterface
{
    public function log(string $message)
    {
        Simple::log(self::LOG_FILE, $message);
    }
}
