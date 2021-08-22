<?php

namespace Pipirima\PimcoreKeeperBundle\EventListener;

use Pipirima\PimcoreKeeperBundle\Processor\EventProcessor;

class EventListener
{
    protected EventProcessor $eventProcessor;

    /**
     * EventListener constructor.
     * @param EventProcessor $eventProcessor
     */
    public function __construct(EventProcessor $eventProcessor)
    {
        $this->eventProcessor = $eventProcessor;
    }

    /**
     * @param string $funcName
     * @param array $funcArguments
     */
    public function __call(string $funcName, array $funcArguments)
    {
        array_unshift($funcArguments, $funcName);
        $this->eventProcessor->handleEvent($funcArguments);
    }
}
