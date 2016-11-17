<?php

namespace EventBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction($count, $firstName)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('EventBundle:Event');
        $event = $repo->findOneBy([
            'name' => 'Birthday party'
        ]);

        return $this->render('EventBundle:Default:index.html.twig', [
            'name'  => $firstName,
            'count' => $count,
            'event' => $event
        ]);
    }
}
