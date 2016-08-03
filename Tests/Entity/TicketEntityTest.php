<?php
/**
 * Created by PhpStorm.
 * User: dreamlex
 * Date: 25.07.16
 * Time: 9:17
 */

namespace Dreamlex\TicketBundle\Tests\Entity;

use Dreamlex\TicketBundle\Entity\Ticket;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Class TicketEntityTest
 * @package Dreamlex\Bundle\TicketBundle\Tests\Entity
 */
class TicketEntityTest extends \PHPUnit_Framework_TestCase
{

    private $object;
    public function setUp()
    {
        $this->object = new Ticket();
    }
    public function tearDown()
    {
        unset($this->object);
    }
    public function testObjectCreated()
    {
        static::assertInstanceOf(Ticket::class, $this->object);
    }
    public function testTicketAddRemoveMessage()
    {
        $ticket = new Ticket();
        $category = $this->createMock('Dreamlex\TicketBundle\Entity\Category');
        $message = $this->createMock('Dreamlex\TicketBundle\Entity\Message');
        $user = $this->createMock('Sonata\UserBundle\Entity\BaseUser');
        $datetime = new DateTime();
        $ticket
            ->setSubject('subject')
            ->setCategory($category)
            ->addMessage($message)
            ->setPriority('low')
            ->setUser($user)
            ->setLastUser($user)
            ->setIsRead('false')
            ->setStatus('open')
            ->setLastMessageAt($ticket->getCreatedAt());
        self::assertEquals('subject',$ticket->getSubject());
        self::assertEquals($category,$ticket->getCategory());
        self::assertEquals($ticket->getCreatedAt(),$ticket->getLastMessageAt());
        self::assertNotNull($ticket->getMessages());
        self::assertEquals('subject',$ticket);
        $ticket->removeMessage($message);
        self::assertEmpty($ticket->getMessages());
    }

}
