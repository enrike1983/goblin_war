<?php
/**
 * User Fixtures
 */

namespace UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use UserBundle\Entity\User;

/**
 * Class LoadUsersFixtures
 * @package UserBundle\DataFixtures\ORM
 */
class LoadUsersFixtures implements FixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load User Fixtures
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('goblin_war_first_user');
        $user->setIsActive(true);
        $user->setEmail('test@goblin_war.dev');
        $user->setApiKey('123');

        $plainPassword = 'goblin_war_first_user';
        $encoder = $this->container->get('security.password_encoder');
        $encoded = $encoder->encodePassword($user, $plainPassword);

        $user->setPassword($encoded);

        $manager->persist($user);

        $manager->flush();
    }
}