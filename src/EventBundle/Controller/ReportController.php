<?php

namespace EventBundle\Controller;

use EventBundle\Entity\Event;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    /**
     * @Route("/events/report/recentlyUpdated.csv")
     */
    public function updatedEventsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $events = $em->getRepository('EventBundle:Event')
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

        $response = new Response(implode("\n", $rows));
        $response->headers->set(
            'content-type',
            'text/csv'
        );

        return $response;
    }
}