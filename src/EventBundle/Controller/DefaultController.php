<?php

namespace EventBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return new Response('index page');
    }

    public function helloAction($firstName)
    {
        return $this->render('EventBundle:Default:index.html.twig', [
            'name' => $firstName
        ]);
    }

    public function testAction($firstName)
    {
        $arr = ['name' => $firstName];
        $response = new Response(json_encode($arr));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
