<?php

namespace Pipirima\PimcoreKeeperBundle\Service;

use Psr\Log\LoggerInterface;

/**
 * Class MailerService
 * @package Pipirima\PimcoreKeeperBundle\Service
 */
class MailerService
{
    protected LoggerInterface $logger;
    protected MailFactory $mailFactory;

    /**
     * MailerService constructor.
     * @param LoggerInterface $logger
     * @param MailFactory $mailFactory
     */
    public function __construct(LoggerInterface $logger, MailFactory $mailFactory)
    {
        $this->logger = $logger;
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
            $this->logger->error($message);
            throw $e;
        }
    }
}
