<?php

namespace Pipirima\PimcoreKeeperBundle\Factory;

class EventHandlerFactory
{
    /**
     * @var array EventHandlerInterface[]
     */
    protected iterable $handlers;

    public function __construct(iterable $handlers)
    {
        $this->handlers = $handlers;
    }

    /**
     * @return EventHandlerInterface[]
     */
    public function getHandlers(): iterable
    {
        return $this->handlers;
    }
}
