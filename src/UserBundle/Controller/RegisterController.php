<?php

namespace UserBundle\Controller;

use EventBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use UserBundle\Entity\User;
use UserBundle\Form\RegisterFormType;

class RegisterController extends Controller
{
    /**
     * @Route("/register", name="user_register")
     * @Template()
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $user->setUsername('put your username here');
        $user->setEmail('put you email here');
        $form = $this->createForm(RegisterFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $user = $form->getData();
//            $user->setPassword($this->encodePassword($user, $user->getPlainPassword()));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $request->getSession()
                ->getFlashBag()
                ->add('success', 'Congratulations you are now registered');

            $this->authenticateUser($user);

            return $this->redirectToRoute('event_index');
        }

        return ['register_form' => $form->createView()];
    }

    private function authenticateUser(User $user)
    {
        $this->getSecurityTokenStorage()
            ->setToken(
                new UsernamePasswordToken($user, null, 'secured_area', $user->getRoles())
            );
    }

//    private function encodePassword(User $user, $plainPassword)
//    {
//        $encoder = $this->container->get('security.encoder_factory')
//            ->getEncoder($user);
//
//        return $encoder->encodePassword($plainPassword, $user->getSalt());
//    }
}