<?php

namespace Pipirima\PimcoreKeeperBundle\EventHandler;

use Pimcore\Event\Model\DataObject\ClassDefinitionEvent;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Tool;
use Pipirima\PimcoreKeeperBundle\Service\EventHandlerInterface;
use Pipirima\PimcoreKeeperBundle\Service\Logger;
use Pipirima\PimcoreKeeperBundle\Service\StringService;
use Pipirima\PimcoreKeeperBundle\Service\MailerService;

class DataObjectEventHandler implements EventHandlerInterface
{
    protected MailerService $mailer;

    protected Logger $logger;

    protected StringService $eventToFunctionService;

    public function __construct(MailerService $mailer, Logger $logger, StringService $eventToFunctionService)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->eventToFunctionService = $eventToFunctionService;
    }

    public function handle(array $config, array $arguments)
    {
        $this->logger->log("A");
        $funcName = $arguments[0];

        if (false !== strpos($funcName, "Pre")) {
            return;
        }

        /** @var DataObjectEvent $event */
        $event = $arguments[1];
        if (!$event instanceof DataObjectEvent) {
            $message = $funcName . ": " . get_class($event);
            $this->logger->log($message);
            return;
        }

        $obj = $event->getObject();
        $classname = get_class($obj);
        $textMessage = "func $funcName: classname: $classname path: {$obj->getFullPath()} ID: {$obj->getId()}";
        $this->logger->log($textMessage);

        $toEmail = $config['email'];
        if (empty($toEmail)) {
            return;
        }

        $hostUrl = strval(Tool::getHostUrl());
        $subject = $hostUrl . ": " . ($config['subject'] ?? 'DataObject change notification');

        $this->logger->log("Sending mail to $toEmail subject: $subject");
        $this->mailer->send($subject, $textMessage, $toEmail);
    }
}
