<?php
/**
 * Created by PhpStorm.
 * User: dreamlex
 * Date: 25.07.16
 * Time: 12:36
 */

namespace Dreamlex\Bundle\TicketBundle\Tests\Entity\Manager;

use Dreamlex\Bundle\TicketBundle\Entity\Manager\TicketManager;
use Dreamlex\Bundle\TicketBundle\Entity\Ticket;
use SellMMO\Sonata\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class TicketManagerTest
 * @package Dreamlex\Bundle\TicketBundle\Tests\Entity\Manager
 */
class TicketManagerTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    /**
     * @var TicketManager
     */
    private $tm;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->tm = static::$kernel->getContainer()->get('dreamlex_ticket.ticket_manager');
    }

    /**
     *
     */
    public function testMarkTicketIsRead()
    {
            //Ticket
        $ticket = $this->em
            ->getRepository('DreamlexTicketBundle:Ticket')
            ->findOneBy(['id' => '6']);
            //Admin TestUser
        $userAdmin = $this->em->getRepository('SellMMOSonataUserBundle:User')
            ->findOneBy(['username' => 'test-admin']);
            //TestUser
        $userUser = $this->em->getRepository('SellMMOSonataUserBundle:User')
            ->findOneBy(['username' => 'test-user']);

        $ticket->setIsRead(false);
        $ticket->setLastUser($userAdmin);

        self::assertEquals(false, $ticket->getIsRead());

        $this->tm->markTicketIsRead($ticket, $userUser);
        $this->em->flush($ticket);

        self::assertEquals(true, $ticket->getIsRead());
        self::assertEquals($userUser, $ticket->getUser());
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }
}
