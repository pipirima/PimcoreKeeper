<?php

namespace Pipirima\PimcoreKeeperBundle\EventHandler;

use Pimcore\Event\Model\DataObject\ClassDefinitionEvent;
use Pimcore\Tool;
use Pipirima\PimcoreKeeperBundle\Factory\EventHandlerInterface;
use Pipirima\PimcoreKeeperBundle\Logger\LoggerInterface;
use Pipirima\PimcoreKeeperBundle\Service\EventToFunctionService;
use Pipirima\PimcoreKeeperBundle\Service\MailerService;

class ClassEventHandler implements EventHandlerInterface
{
    protected MailerService $mailer;

    protected LoggerInterface $logger;

    protected EventToFunctionService $eventToFunctionService;

    public function __construct(MailerService $mailer, LoggerInterface $logger, EventToFunctionService $eventToFunctionService)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->eventToFunctionService = $eventToFunctionService;
    }

    public function handle(array $config, array $arguments)
    {
        $funcName = $arguments[0];

        if (false !== strpos($funcName, "Pre")) {
            return;
        }

        $event = $arguments[1];
        if (!$event instanceof ClassDefinitionEvent) {
            $message = $funcName . ": " . get_class($event);
            $this->logger->log($message);
            return;
        }

        $classDefinition = $event->getClassDefinition();
        $classId = $classDefinition->getId();
        $className = $classDefinition->getName();
        $textMessage = "func $funcName: class: id: $classId name: $className";
        $this->logger->log($textMessage);

        $toEmail = $config['email'];
        if (empty($toEmail)) {
            return;
        }

        $hostUrl = strval(Tool::getHostUrl());
        $subject = $hostUrl . ": Class change notification";

        $this->logger->log("Sending mail to $toEmail subject: $subject");
        $this->mailer->send($subject, $textMessage, $toEmail);
    }
}
