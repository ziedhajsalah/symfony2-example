<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

class RegisterController extends Controller
{
    /**
     * @Route("/register", name="user_register")
     * @Template()
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $user->setUsername('put your username here');
        $user->setEmail('put you email here');
        $form = $this->createFormBuilder($user, [
            'data_class' => User::class
        ])
            ->add('username', TextType::class)
            ->add('email', EmailType::class)
            ->add('plainPassword',
                RepeatedType::class,
                ['type' => PasswordType::class]
            )
            ->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $user = $form->getData();
            $user->setPassword($this->encodePassword($user, $user->getPlainPassword()));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('event_index');
        }

        return ['register_form' => $form->createView()];
    }

    private function encodePassword(User $user, $plainPassword)
    {
        $encoder = $this->container->get('security.encoder_factory')
            ->getEncoder($user);

        return $encoder->encodePassword($plainPassword, $user->getSalt());
    }
}