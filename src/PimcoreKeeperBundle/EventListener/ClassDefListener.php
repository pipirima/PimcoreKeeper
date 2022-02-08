<?php

namespace Pipirima\PimcoreKeeperBundle\EventListener;

use Pimcore\Event\Model\DataObject\ClassDefinitionEvent;
use Pimcore\Tool;
use Pipirima\PimcoreKeeperBundle\Service\ConfigService;
use Pipirima\PimcoreKeeperBundle\Service\Logger;
use Pipirima\PimcoreKeeperBundle\Service\MailerService;

class ClassDefListener
{
    protected Logger $logger;
    protected MailerService $mailer;
    protected ConfigService $configService;

    protected array $emails;

    public function __construct(
        Logger $logger,
        MailerService $mailer,
        ConfigService $configService
    ) {
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->configService = $configService;
        $this->emails = $this->getEmails();
    }

    public function __call(string $funcName, array $funcArguments)
    {
        $event = $funcArguments[0];
        if (!$event instanceof ClassDefinitionEvent) {
            $this->logger->log('PimcoreKeeper: ' . $funcName . ": " . get_class($event));
            return;
        }
        $classDefinition = $event->getClassDefinition();
        $classId = $classDefinition->getId();
        $className = $classDefinition->getName();
        $textMessage = "PimcoreKeeper: func $funcName: class: id: $classId name: $className";
        $this->logger->log($textMessage);
        if (false !== strpos($funcName, "Pre")) {
            return;
        }

        $hostUrl = strval(Tool::getHostUrl());
        $subject = "$hostUrl : Class change notification: $className (ID:$classId)";

        foreach ($this->emails as $email) {
            $this->mailer->send($subject, $textMessage, $email);
        }
    }

    private function getEmails(): array
    {
        $emails = [];
        foreach ($this->configService->getConfig() as $configItem) {
            if (($configItem['type'] ?? '') != 'class') {
                continue;
            }
            foreach (($configItem['emails'] ?? []) as $email) {
                $emails[] = $email;
            }
        }

        return $emails;
    }
}
