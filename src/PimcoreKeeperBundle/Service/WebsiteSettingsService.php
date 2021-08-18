<?php

namespace Pipirima\PimcoreKeeperBundle\Service;

use Pimcore\Model\WebsiteSetting;

class WebsiteSettingsService
{
    /**
     * @param string $name
     * @param string $default
     * @return string
     */
    public function getTextValue(string $name, string $default = ''): string
    {
        $websiteSetting = WebsiteSetting::getByName($name);
        if (!$websiteSetting instanceof WebsiteSetting) {
            return $default;
        }
        $value = strval($websiteSetting->getData());
        return $value;
    }
}
