<?php

namespace EventBundle\Reporting;

use Doctrine\ORM\EntityManager;
use EventBundle\Entity\Event;

class EventReportManager
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getRecentlyUpdatedReport()
    {
        $events = $this->em->getRepository('EventBundle:Event')
            ->getRecentlyUpdatedEvents();

        $rows = [];

        /**
         * @var $event Event
         */
        foreach ($events as $event) {
            $data = [
                $event->getId(),
                $event->getName(),
                $event->getTime()->format('Y-m-d H:i:s'),
            ];

            $rows[] = implode(',', $data);
        }

        return implode("\n", $rows);
    }
}