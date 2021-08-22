<?php

namespace Pipirima\PimcoreKeeperBundle\EventHandler;

use Pimcore\Event\Model\DataObject\ClassDefinitionEvent;
use Pimcore\Tool;
use Pipirima\PimcoreKeeperBundle\PimcoreKeeperBundle;
use Pipirima\PimcoreKeeperBundle\Service\ConfigService;
use Pipirima\PimcoreKeeperBundle\Service\MailerService;
use Pipirima\PimcoreKeeperBundle\Service\WebsiteSettingsService;

class ClassEventHandler
{
    protected array $config;

    protected MailerService $mailer;
    protected WebsiteSettingsService $websiteSettingsService;

    public function __construct(
        MailerService $mailer,
        WebsiteSettingsService $websiteSettingsService
    ) {
        $this->mailer = $mailer;
        $this->websiteSettingsService = $websiteSettingsService;
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    public function handle(string $funcName, array $funcArguments)
    {
        echo "XXX   CONFIG    XXX \n";
        print_r($this->config);

        $event = $funcArguments[0];
        if (!$event instanceof ClassDefinitionEvent) {
            $message = $funcName . ": " . get_class($event);
            \Pimcore\Log\Simple::log(PimcoreKeeperBundle::LOG_FILE, $message);
            return;
        }
        $classDefinition = $event->getClassDefinition();
        $classId = $classDefinition->getId();
        $className = $classDefinition->getName();
        $textMessage = "func $funcName: class: id: $classId name: $className";
        \Pimcore\Log\Simple::log(PimcoreKeeperBundle::LOG_FILE, $textMessage);
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
