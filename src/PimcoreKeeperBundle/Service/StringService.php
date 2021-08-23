<?php

namespace Pipirima\PimcoreKeeperBundle\Service;

class StringService
{
    public function eventnameToFuncname(string $eventName): string
    {
        $pieces = explode('.', $eventName);
        $pieces = array_map(function(string $str) { return ucfirst($str); }, $pieces);
        $funcname = 'on' . implode('', $pieces);

        return $funcname;
    }
}