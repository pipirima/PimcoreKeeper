<?php

namespace Pipirima\PimcoreKeeperBundle\EventListener;

use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Tool;
use Pipirima\PimcoreKeeperBundle\PimcoreKeeperBundle;
use Pipirima\PimcoreKeeperBundle\Service\ConfigService;
use Pipirima\PimcoreKeeperBundle\Service\MailerService;
use Pipirima\PimcoreKeeperBundle\Service\WebsiteSettingsService;

class DataObjectEventListener
{
    const DATAOBJECT_EVENT_EMAIL_WS = 'dataobject_event_email';

    protected MailerService $mailer;
    protected WebsiteSettingsService $websiteSettingsService;
    protected ConfigService $config;

    public function __construct(
        MailerService $mailer,
        WebsiteSettingsService $websiteSettingsService,
        ConfigService $config
    ) {
        $this->mailer = $mailer;
        $this->websiteSettingsService = $websiteSettingsService;
        $this->config = $config;
    }

    public function __call(string $funcName, array $funcArguments)
    {
        echo "XXX   CONFIG    XXX \n";
        print_r($this->config->getConfig());

        exit;

        $event = $funcArguments[0];
        if (!$event instanceof DataObjectEvent) {
            $message = $funcName . ": " . get_class($event);
            \Pimcore\Log\Simple::log(PimcoreKeeperBundle::LOG_FILE, $message);
            return;
        }
        $obj = $event->getObject();

        $class = get_class($obj);
        $className = array_pop(explode("\\", $class));

        $textMessage = "PimcoreKeeper: class: $class  classname: $className";
        \Pimcore\Log\Simple::log(PimcoreKeeperBundle::LOG_FILE, $textMessage);
        return;
        if (false !== strpos($funcName, "Pre")) {
            return;
        }

        $toEmail = $this->websiteSettingsService->getTextValue(self::DATAOBJECT_EVENT_EMAIL_WS);
        if (empty($toEmail)) {
            return;
        }

        $hostUrl = strval(Tool::getHostUrl());
        $subject = $hostUrl . ": Class change notification";

        $this->mailer->send($subject, $textMessage, $toEmail);
    }
}
