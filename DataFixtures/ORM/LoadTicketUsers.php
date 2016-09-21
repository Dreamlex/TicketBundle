<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 05.07.16
 * Time: 10:18
 */

namespace Dreamlex\Bundle\TicketBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadTicketUsers
 * @package Dreamlex\Bundle\TicketBundle\DataFixtures\ORM
 */
class LoadTicketUsers extends AbstractFixture implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    /** @var ContainerInterface */
    private $container;

    /**
     * @return int
     */
    public function getOrder()
    {
        return 2;
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param ObjectManager $om
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function load(ObjectManager $om)
    {
        /** @var $manager \FOS\UserBundle\Doctrine\UserManager */
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername('test-user');

        if (\is_null($user)) {
            $user = $userManager->createUser();
            $user->setUsername('test-user')
                ->setEmail('testuser@mail.us')
                ->setPlainPassword('test-user')
                ->setEnabled(true)
                ->setRoles(['ROLE_USER']);
            $userManager->updateUser($user, true);
        }
        $this->addReference('test-user', $user);

        $userAdmin = $userManager->findUserByUsername('test-admin');
        if (\is_null($userAdmin)) {
            $userAdmin = $userManager->createUser();
            $userAdmin->setUsername('test-admin')
                ->setEmail('testadmin@mail.us')
                ->setPlainPassword('test-admin')
                ->setEnabled(true)
                ->setRoles(['ROLE_SUPER_ADMIN']);
            $userManager->updateUser($userAdmin, true);
        }
        $this->addReference('test-admin', $userAdmin);
    }
}
