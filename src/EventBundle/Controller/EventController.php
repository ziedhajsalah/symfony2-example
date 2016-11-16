<?php

namespace EventBundle\Controller;

use EventBundle\Entity\Event;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Event controller.
 *
 */
class EventController extends Controller
{
    /**
     * Lists all event entities.
     *
     * @Template()
     *
     */
    public function indexAction()
    {
        return [];
    }

    /**
     * Creates a new event entity.
     *
     * @Template()
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function newAction(Request $request)
    {
        $this->enforceUserSecurity('ROLE_EVENT_CREATE');

        $event = new Event();
        $form = $this->createForm('EventBundle\Form\EventType', $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $event->setOwner($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush($event);

            return $this->redirectToRoute(
                'event_show',
                array(
                    'id' => $event->getId(),
                )
            );
        }

        return [
            'event' => $event,
            'form'  => $form->createView(),
        ];
    }

//    public function createAction(Request $request)
//    {
//        $this->enforceUserSecurity('ROLE_EVENT_CREATE');
//    }

    /**
     * Finds and displays a event entity.
     *
     * @Template()
     * @param $slug
     * @return array
     */
    public function showAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('EventBundle:Event')
            ->findOneBy(['slug' => $slug]);

        $deleteForm = $this->createDeleteForm($event);

        return [
            'event'       => $event,
            'delete_form' => $deleteForm->createView(),
        ];
    }

    public function attendAction($id, $format)
    {
        $em = $this->getDoctrine()->getManager();
        /**
         * @var Event
         */
        $event = $em->getRepository('EventBundle:Event')->find($id);

        if (!$event) {
            throw $this->createNotFoundException(
                'No event found for id '.$id
            );
        }

        if (!$event->hasAttendee($this->getUser())) {
            $event->getAttendees()->add($this->getUser());
        }

        $em->persist($event);
        $em->flush();

        return $this->createAttendingResponse($event, $format);
    }

    public function unattendAction($id, $format)
    {
        $em = $this->getDoctrine()->getManager();
        /**
         * @var Event
         */
        $event = $em->getRepository('EventBundle:Event')->find($id);

        if (!$event) {
            throw $this->createNotFoundException(
                'No event found for id '.$id
            );
        }

        if ($event->hasAttendee($this->getUser())) {
            $event->getAttendees()->removeElement($this->getUser());
        }

        $em->persist($event);
        $em->flush();

        return $this->createAttendingResponse($event, $format);
    }

    /**
     * @param $event
     * @param $format
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function createAttendingResponse(Event $event, $format) {
        if ($format == 'json') {
            $data = [
                'attending' => $event->hasAttendee($this->getUser()),
            ];

            return new JsonResponse($data);
        }

        return $this->redirectToRoute(
            'event_show',
            [
                'slug' => $event->getSlug(),
            ]
        );
    }

    /**
     * Displays a form to edit an existing event entity.
     *
     * @Template()
     *
     */
    public function editAction(Request $request, Event $event)
    {
        $this->enforceUserSecurity();
        $this->enforceOwnerSecurity($event);

        $deleteForm = $this->createDeleteForm($event);
        $editForm = $this->createForm('EventBundle\Form\EventType', $event);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute(
                'event_edit',
                array(
                    'id' => $event->getId(),
                )
            );
        }

        return [
            'event'       => $event,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Deletes a event entity.
     */
    public function deleteAction(Request $request, Event $event)
    {
        $this->enforceUserSecurity();
        $this->enforceOwnerSecurity($event);

        $form = $this->createDeleteForm($event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($event);
            $em->flush($event);
        }

        return $this->redirectToRoute('event_index');
    }

    /**
     * Creates a form to delete a event entity.
     *
     * @param Event $event The event entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Event $event)
    {
        return $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'event_delete',
                    array('id' => $event->getId())
                )
            )
            ->setMethod('DELETE')
            ->getForm();
    }


    public function _upcommingEventsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $events = $em->getRepository('EventBundle:Event')
            ->getUpcomingEvents();

        return $this->render('EventBundle:Event:_upcommingEvents.html.twig', [
            'events' => $events
        ]);
    }
}
