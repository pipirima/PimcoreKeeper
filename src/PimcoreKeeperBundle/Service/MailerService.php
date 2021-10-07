<?php

namespace Pipirima\PimcoreKeeperBundle\Service;

/**
 * Class MailerService
 * @package Pipirima\PimcoreKeeperBundle\Service
 */
class MailerService
{
    protected Logger $logger;
    protected MailFactory $mailFactory;

    /**
     * MailerService constructor.
     * @param Logger $logger
     * @param MailFactory $mailFactory
     */
    public function __construct(Logger $logger, MailFactory $mailFactory)
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
            $this->logger->log($message);
            throw $e;
        }
    }
}
