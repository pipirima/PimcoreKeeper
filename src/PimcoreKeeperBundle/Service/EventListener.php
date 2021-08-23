<?php

namespace Pipirima\PimcoreKeeperBundle\Service;

use Pipirima\PimcoreKeeperBundle\Service\EventHandlerFactory;
use Pipirima\PimcoreKeeperBundle\Service\EventHandlerInterface;
use Pipirima\PimcoreKeeperBundle\Service\ConfigService;
use Pipirima\PimcoreKeeperBundle\Service\StringService;

class EventListener
{
    protected EventHandlerFactory $factory;

    protected ConfigService $config;

    protected StringService $stringService;

    protected Logger $logger;

    public function __construct(EventHandlerFactory $factory, ConfigService $config, StringService $stringService, Logger $logger)
    {
        $this->factory = $factory;
        $this->config = $config;
        $this->stringService = $stringService;
        $this->logger = $logger;
    }

    private function isFuncNameMatchingEventsName(string $funcName, array $eventsNames): bool
    {
        foreach ($eventsNames as $eventName) {
            $funcNameFromEventName = $this->stringService->eventnameToFuncname($eventName);
            if ($funcName === $funcNameFromEventName) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $funcName
     * @param array $funcArguments
     */
    public function __call(string $funcName, array $funcArguments)
    {
        $this->logger->log("=====================================================================");
        $this->logger->log("event funcName: $funcName");
        array_unshift($funcArguments, $funcName);
        /** @var EventHandlerInterface $handler */
        foreach ($this->factory->getHandlers() as $handler) {
            foreach ($this->config->getConfig()['alerts'] as $alertConfig) {
                if ($this->isFuncNameMatchingEventsName($funcName, $alertConfig['events'])) {
                    $handler->handle($alertConfig, $funcArguments);
                }
            }
        }
    }
}
