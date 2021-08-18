<?php

namespace Pipirima\PimcoreKeeperBundle\Service;

use Pimcore\Mail;

class MailFactory
{
    public function createMail(): Mail
    {
        return new Mail;
    }
}
