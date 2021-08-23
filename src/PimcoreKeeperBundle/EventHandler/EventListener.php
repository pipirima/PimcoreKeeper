<?php

namespace Pipirima\PimcoreKeeperBundle\EventHandler;

use Pipirima\PimcoreKeeperBundle\Factory\EventHandlerFactory;
use Pipirima\PimcoreKeeperBundle\Factory\EventHandlerInterface;
use Pipirima\PimcoreKeeperBundle\Service\ConfigService;
use Pipirima\PimcoreKeeperBundle\Service\EventToFunctionService;

class EventListener
{
    protected EventHandlerFactory $factory;

    protected ConfigService $config;

    protected EventToFunctionService $eventToFunctionService;

    public function __construct(EventHandlerFactory $factory, ConfigService $config, EventToFunctionService $eventToFunctionService)
    {
        $this->factory = $factory;
        $this->config = $config;
        $this->eventToFunctionService = $eventToFunctionService;
    }

    private function isFuncNameMatchingEventsName(string $funcName, array $eventsNames): bool
    {
        foreach ($eventsNames as $eventName) {
            $funcNameFromEventName = $this->eventToFunctionService->convertToFuncname($eventName);
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
        array_unshift($funcArguments, $funcName);
        /** @var EventHandlerInterface $handler */
        foreach ($this->factory->getHandlers() as $handler) {
            foreach ($this->config->getConfig()['alerts'] as $alertConfig) {

                if (!$this->isFuncNameMatchingEventsName($funcName, $alertConfig['events'])) {
                    return;
                }

                $handler->handle($alertConfig, $funcArguments);
            }
        }
    }
}
