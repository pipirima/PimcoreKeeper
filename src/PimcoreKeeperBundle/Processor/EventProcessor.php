<?php

namespace Pipirima\PimcoreKeeperBundle\Processor;

use Pipirima\PimcoreKeeperBundle\Factory\EventHandlerFactory;

class EventProcessor
{
    protected EventHandlerFactory $eventHandlerFactory;

    public function __construct(EventHandlerFactory $eventHandlerFactory)
    {
        $this->eventHandlerFactory = $eventHandlerFactory;
    }

    public function handleEvent(array $arguments)
    {
        foreach ($this->eventHandlerFactory->getHandlers($arguments) as $handler) {
            $handler->handle($arguments);
        }
    }
}
