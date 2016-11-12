<?php

namespace EventBundle\Controller;

use EventBundle\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
        $em = $this->getDoctrine()->getManager();

        $events = $em->getRepository('EventBundle:Event')->findAll();
        //var_dump($em->getRepository('UserBundle:User')->findOneByUsernameOrEmail('admin@mail.com'));
        //var_dump($em->getRepository('UserBundle:User')->findOneByUsernameOrEmail('administrator'));die;
        return ['events' => $events];
    }

    /**
     * Creates a new event entity.
     *
     * @Template()
     *
     */
    public function newAction(Request $request)
    {
        $this->enforceUserSecurity();

        $event = new Event();
        $form = $this->createForm('EventBundle\Form\EventType', $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush($event);

            return $this->redirectToRoute('event_show', array('id' => $event->getId()));
        }

        return [
            'event' => $event,
            'form'  => $form->createView(),
        ];
    }

    /**
     * Finds and displays a event entity.
     *
     * @Template()
     *
     */
    public function showAction(Event $event)
    {
        $deleteForm = $this->createDeleteForm($event);

        return [
            'event'       => $event,
            'delete_form' => $deleteForm->createView(),
        ];
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

        $deleteForm = $this->createDeleteForm($event);
        $editForm = $this->createForm('EventBundle\Form\EventType', $event);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('event_edit', array('id' => $event->getId()));
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
            ->setAction($this->generateUrl('event_delete', array('id' => $event->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Checks if the user is authenticated and has ROLE_USER or throw an Exception
     */
    private function enforceUserSecurity() {
        $securityAuthorizationChecker = $this->get('security.authorization_checker');

        if(!$securityAuthorizationChecker->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('you need to be authenticated to access this page');
        }
    }
}
