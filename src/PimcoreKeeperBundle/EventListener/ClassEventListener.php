<?php

namespace Pipirima\PimcoreKeeperBundle\EventListener;

use Pimcore\Event\Model\DataObject\ClassDefinitionEvent;
use Pimcore\Tool;
use Pipirima\PimcoreKeeperBundle\Service\MailerService;
use Pipirima\PimcoreKeeperBundle\Service\WebsiteSettingsService;
use Psr\Log\LoggerInterface;

class ClassEventListener
{
    const CLASS_EVENT_EMAIL_WS = 'class_event_email';

    protected LoggerInterface $logger;
    protected MailerService $mailer;
    protected WebsiteSettingsService $websiteSettingsService;

    public function __construct(
        LoggerInterface $logger,
        MailerService $mailer,
        WebsiteSettingsService $websiteSettingsService
    ) {
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->websiteSettingsService = $websiteSettingsService;
    }

    public function __call(string $funcName, array $funcArguments)
    {
        $event = $funcArguments[0];
        if (!$event instanceof ClassDefinitionEvent) {
            $this->logger->debug('PimcoreKeeper: ' . $funcName . ": " . get_class($event));
            return;
        }
        $classDefinition = $event->getClassDefinition();
        $classId = $classDefinition->getId();
        $className = $classDefinition->getName();
        $textMessage = "PimcoreKeeper: func $funcName: class: id: $classId name: $className";
        $this->logger->debug($textMessage);
        if (false !== strpos($funcName, "Pre")) {
            return;
        }

        $toEmail = $this->websiteSettingsService->getTextValue(self::CLASS_EVENT_EMAIL_WS);
        if (empty($toEmail)) {
            return;
        }

        $hostUrl = strval(Tool::getHostUrl());
        $subject = $hostUrl . ": Class change notification";

        $this->mailer->send($subject, $textMessage, $toEmail);
    }
}
