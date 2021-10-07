<?php

namespace Pipirima\PimcoreKeeperBundle\Service;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\WebsiteSetting;

class PimcoreKeeperService
{
    const KEEPER_OBJECT_WS = 'keeper_object';

    private ConfigService $configService;

    private Logger $logger;

    private array $config = [];

    public function __construct(ConfigService $configService, Logger $logger)
    {
        $this->configService = $configService;
        $this->logger = $logger;

        $this->config = $this->getWsParsedConfig();
    }

    /**
     * @param int $objectId
     * @param array $requestData
     */
    public function processSaveObjectData(int $objectId, array $requestData)
    {
        if (!$this->config || !$this->config['classname'] || !$this->config['emails'] || !$this->config['fields']) {
            return;
        }
        $objectFromDatabase = Concrete::getById($objectId);
        $fullClass = get_class($objectFromDatabase);
        $classArray = explode("\\", $fullClass);
        $classname = array_pop($classArray);
        if ($this->config['classname'] !== $classname) {
            return;
        }

        $updatedFields = [];

        foreach ($requestData as $key => $value) {
            if (!is_string($key)) {
                continue;
            }

            if (!in_array($key, $this->config['fields'])) {
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

        if (count($updatedFields)) {
            $this->mail($objectFromDatabase, $updatedFields);
        }
    }

    /**
     * @return array
     */
    private function getWsParsedConfig(): array
    {
        $websiteSetting = WebsiteSetting::getByName(self::KEEPER_OBJECT_WS);
        if (!$websiteSetting instanceof WebsiteSetting) {
            return [];
        }

        $data = strval($websiteSetting->getData());
        $dataArr = explode(',', $data);
        $dataArr = array_map('trim', $dataArr);
        $result = [
            'classname' => $dataArr[0],
            'emails' => [],
            'fields' => []
        ];

        array_unshift($dataArr);

        foreach ($dataArr as $item) {
            if (false !== strpos($item, '@')) {
                $result['emails'][] = $item;
            } else {
                $result['fields'][] = $item;
            }
        }

        return $result;
    }

    /**
     * @param Concrete $objectFromDatabase
     * @param array $updatedFields
     */
    private function mail(Concrete $objectFromDatabase, array $updatedFields)
    {
        $fullClass = get_class($objectFromDatabase);
        $classArray = explode("\\", $fullClass);
        $classname = array_pop($classArray);

        $materialIndex = ' brak material index';
        if (method_exists($objectFromDatabase, "getMaterialIndex")) {
            $materialIndex = $objectFromDatabase->getMaterialIndex();
        }

        $objectId = $objectFromDatabase->getId();

        $hostUrl = strval(\Pimcore\Tool::getHostUrl());
        $subject = $hostUrl . ": Modyfikacja obiektu $classname, ID: $objectId" . ", " . $materialIndex;

        $deepLink = $hostUrl . "/admin/login/deeplink?object_{$objectId}_object";

        $message = "Obiekt: " . $classname . "\n"
            . "ID: " . $objectFromDatabase->getId() . "\n"
            . ($materialIndex ? "Indeks materialowy: $materialIndex \n" : "")
            . "sciezka: " . $objectFromDatabase->getFullPath() . "\n"
            . "link: " . $deepLink . "\n"
            . "zmienione pola: \n";
        foreach ($updatedFields as $field) {
            $message .= "  - " . $field . "\n";
        }

        foreach ($this->config['emails'] as $email) {
            $this->sendMail($email, $subject, $message);
        }
    }

    /**
     * @param string $emailTo
     * @param string $subject
     * @param string $message
     */
    private function sendMail(string $emailTo, string $subject, string $message)
    {
        $mail = new \Pimcore\Mail($subject);
        $mail->addTo($emailTo);
        $mail->setSubject($subject);
        $mail->setBodyText($message);

        try {
            $mail->send();
        } catch (\Exception $e) {
            $message = "sendmail exception: " . $e->getMessage();
            $this->logger->log($message);
        }
    }
}
