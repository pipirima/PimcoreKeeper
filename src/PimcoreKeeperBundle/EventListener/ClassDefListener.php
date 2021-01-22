<?php

namespace Pipirima\PimcoreKeeperBundle\EventListener;

use Pimcore\Event\Model\DataObject\ClassDefinitionEvent;
use Pimcore\Model\WebsiteSetting;

class ClassDefListener
{
    const KEEPER_EMAIL_WS = 'keeper_email';

    public function __call(string $funcName, array $funcArguments)
    {
        $event = $funcArguments[0];
        if (!$event instanceof ClassDefinitionEvent) {
            $this->log($funcName . ": " . get_class($event));
            return;
        }
        $classDefinition = $event->getClassDefinition();
        $classId = $classDefinition->getId();
        $className = $classDefinition->getName();
        $message = "func $funcName: class: id: $classId name: $className";
        $this->log($message);
        if (false === strpos($funcName, "Pre")) {
            $this->mail($message);
        }
    }

    private function log(string $message)
    {
        $filename = 'keeper_events.log';
        $content = file_exists($filename) ? file_get_contents($filename) : '';
        $content .= $message . "\n";
        file_put_contents($filename, $content);
    }

    private function mail(string $message)
    {
        $hostUrl = strval(\Pimcore\Tool::getHostUrl());
        $mail = new \Pimcore\Mail($hostUrl . ": Class change notification");
        $websiteSetting = WebsiteSetting::getByName(self::KEEPER_EMAIL_WS);
        if (!$websiteSetting instanceof WebsiteSetting) {
            return;
        }
        $email = strval($websiteSetting->getData());
        $mail->addTo($email);
        $mail->setBodyText($message);
        try {
            $mail->send();
        } catch (Exception $e) {
            $message = "sendmail exception: " . $e->getMessage();
            $this->log($message);
        }
    }
}
