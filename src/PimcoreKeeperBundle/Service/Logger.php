<?php

namespace Pipirima\PimcoreKeeperBundle\Service;

use Pimcore\Log\Simple;
use Pipirima\PimcoreKeeperBundle\PimcoreKeeperBundle;

class Logger
{
    protected bool $debug;

    public function __construct(?bool $debug = true)
    {
        $this->debug = boolval($debug);
    }

    public function log(string $message)
    {
        if (!$this->debug) {
            return;
        }

        Simple::log(PimcoreKeeperBundle::BUNDLE_CODE, $message);
    }
}