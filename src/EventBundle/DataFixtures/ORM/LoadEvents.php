<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use EventBundle\Entity\Event;

class LoadEvents implements FixtureInterface, OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $administrator = $manager->getRepository('UserBundle:User')
            ->findOneByUsernameOrEmail('administrator');

        $event1 = new Event();
        $event1->setName('football match');
        $event1->setLocation('sousse');
        $event1->setTime(new \DateTime('monday noon'));
        $event1->setDetails('big match');
        $event1->setOwner($administrator);
        $manager->persist($event1);

        $event2 = new Event();
        $event2->setName('wedding party');
        $event2->setLocation('jammel');
        $event2->setTime(new \DateTime('tomorrow noon'));
        $event2->setDetails('rabbi yhanni');
        $event2->setOwner($administrator);
        $manager->persist($event2);

        $event3 = new Event();
        $event3->setName('job interview');
        $event3->setLocation('tunis');
        $event3->setTime(new \DateTime('next thursday'));
        $event3->setDetails('????');
        $event3->setOwner($administrator);
        $manager->persist($event3);

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 20;
    }
}