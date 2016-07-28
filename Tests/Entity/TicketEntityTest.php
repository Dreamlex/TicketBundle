<?php
/**
 * Created by PhpStorm.
 * User: dreamlex
 * Date: 25.07.16
 * Time: 9:17
 */

namespace Dreamlex\Bundle\TicketBundle\Tests\Entity;

use Dreamlex\Bundle\TicketBundle\Entity\Ticket;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class TicketEntityTest
 * @package Dreamlex\Bundle\TicketBundle\Tests\Entity
 */
class TicketEntityTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager $em
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testTicketAddRemoveMessage()
    {
        $ticket = new Ticket();
        $user = $this->createMock('SellMMO\Sonata\UserBundle\Entity\User');
        $category = $this->createMock('Dreamlex\Bundle\TicketBundle\Entity\Category');
        $message = $this->createMock('Dreamlex\Bundle\TicketBundle\Entity\Message');
        $ticket->setUser($user)
            ->setSubject('subject')
            ->setCategory($category)
            ->addMessage($message)
            ->setPriority('low')
            ->setIsRead('false')
            ->setStatus('open');
        self::assertEquals('subject',$ticket->getSubject());
        self::assertEquals($category,$ticket->getCategory());
        self::assertNotNull($ticket->getMessages());
        self::assertEquals('subject',$ticket->__toString());
        $ticket->removeMessage($message);
        self::assertEmpty($ticket->getMessages());
    }
}
