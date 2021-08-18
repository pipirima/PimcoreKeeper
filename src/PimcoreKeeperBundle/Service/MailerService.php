<?php

namespace Pipirima\PimcoreKeeperBundle\Service;

use Pipirima\PimcoreKeeperBundle\PimcoreKeeperBundle;

/**
 * Class MailerService
 * @package Pipirima\PimcoreKeeperBundle\Service
 */
class MailerService
{
    protected MailFactory $mailFactory;

    /**
     * MailerService constructor.
     * @param MailFactory $mailFactory
     */
    public function __construct(MailFactory $mailFactory)
    {
        $this->mailFactory = $mailFactory;
    }

    /**
     * @param string $subject
     * @param string $textMessage
     * @param string $toEmail
     * @throws \Exception
     */
    public function send(string $subject, string $textMessage, string $toEmail)
    {
        $mail = $this->mailFactory->createMail();
        $mail->setSubject($subject);
        $mail->addTo($toEmail);
        $mail->setBodyText($textMessage);
        try {
            $mail->send();
        } catch (\Exception $e) {
            $message = 'PimcoreKeeper: ' . 'sendmail exception: ' . $e->getMessage();
            \Pimcore\Log\Simple::log(PimcoreKeeperBundle::LOG_FILE, $message);
            throw $e;
        }
    }
}
