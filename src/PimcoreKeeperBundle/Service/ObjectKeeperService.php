<?php

namespace Pipirima\PimcoreKeeperBundle\Service;

use Pimcore\Model\DataObject\Concrete;

class ObjectKeeperService
{
    private ConfigService $configService;

    private Logger $logger;

    private MailerService $mailer;

    private array $config = [];

    public function __construct(ConfigService $configService, Logger $logger, MailerService $mailer)
    {
        $this->configService = $configService;
        $this->logger = $logger;
        $this->mailer = $mailer;

        $this->config = $this->getParsedConfig();
    }

    public function processSaveObjectData(int $objectId, array $requestData)
    {
        $objectFromDatabase = Concrete::getById($objectId);
        $fullClass = get_class($objectFromDatabase);
        $classArray = explode("\\", $fullClass);
        $classname = array_pop($classArray);
        foreach ($this->config as $configItem) {
            if ($configItem['class'] !== $classname) {
                continue;
            }
            $this->processSaveObjectDataConfigItem($objectId, $requestData, $configItem);
        }
    }

    /**
     * @param int $objectId
     * @param array $requestData
     * @param array $configItem
     */
    private function processSaveObjectDataConfigItem(int $objectId, array $requestData, array $configItem)
    {
        $objectFromDatabase = Concrete::getById($objectId);
        $fullClass = get_class($objectFromDatabase);
        $classArray = explode("\\", $fullClass);
        $classname = array_pop($classArray);

        $updatedFields = [];

        foreach ($requestData as $key => $value) {
            if (!is_string($key)) {
                continue;
            }

            if (!in_array($key, $configItem['fields'])) {
                continue;
            }

            $getter = 'get' . ucfirst($key);
            if (!method_exists($objectFromDatabase, $getter)) {
                continue;
            }

            $objectValue = $objectFromDatabase->$getter();
            if ($objectValue == $value) {
                continue;
            }

            if (is_array($value) && isset($value['id'])
                && is_object($objectValue) && method_exists($objectValue, "getId")
                && ($objectValue->getId() == $value['id'])) {
                continue;
            }

            $updatedFields[] = $key;
        }

        if (0 === count($updatedFields)) {
            return;
        }

        $indexField = $configItem['indexField'] ?? 'id';
        $getter = 'get' . ucfirst($indexField);
        $indexFieldName = $configItem['indexFieldName'] ?? 'ID';
        $indexFieldValue = '';
        if (method_exists($objectFromDatabase, $getter)) {
            $indexFieldValue = $objectFromDatabase->$getter();
        }

        $objectId = $objectFromDatabase->getId();

        $hostUrl = strval(\Pimcore\Tool::getHostUrl());
        $subject = $hostUrl . ": Modyfikacja obiektu $classname, ID: $objectId" . ", " . $indexFieldValue;

        $deepLink = $hostUrl . "/admin/login/deeplink?object_{$objectId}_object";

        $message = "Obiekt: " . $classname . "\n"
            . "ID: " . $objectFromDatabase->getId() . "\n"
            . ($indexFieldValue ? "$indexFieldName: $indexFieldValue \n" : "")
            . "sciezka: " . $objectFromDatabase->getFullPath() . "\n"
            . "link: " . $deepLink . "\n"
            . "zmienione pola: \n";
        foreach ($updatedFields as $field) {
            $message .= "  - " . $field . "\n";
        }

        foreach ($configItem['emails'] as $email) {
            $this->mailer->send($subject, $message, $email);
        }
    }

    /**
     * @return array
     */
    private function getParsedConfig(): array
    {
        $newConfig = [];
        foreach ($this->configService->getConfig() as $configItem) {
            if (!isset($configItem['type']) ||
                !isset($configItem['emails']) || !is_array($configItem['emails']) ||
                !isset($configItem['class']) ||
                !isset($configItem['fields']) || !is_array($configItem['fields'])
            ) {
                continue;
            }
            if ($configItem['type'] != 'object') {
                continue;
            }
            $newConfig[] = $configItem;
        }

        return $newConfig;
    }
}
