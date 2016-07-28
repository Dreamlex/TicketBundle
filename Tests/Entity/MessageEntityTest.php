<?php
/**
 * Created by PhpStorm.
 * User: dreamlex
 * Date: 25.07.16
 * Time: 9:44
 */

namespace Dreamlex\Bundle\TicketBundle\Tests\Entity;

use Dreamlex\Bundle\TicketBundle\Entity\Message;
use Dreamlex\Bundle\TicketBundle\Entity\Ticket;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class MessageEntityTest
 * @package Dreamlex\Bundle\TicketBundle\Tests\Entity
 */
class MessageEntityTest extends KernelTestCase
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
    /**
     * @return Message
     */
    public function testMessageGetStatuses()
    {
        $message = new Message();
        $statuses = $message:: getStatuses();
        self::assertEquals('open', $statuses['open']);
        $statuses = $message:: getStatuses('prefix_');
        self::assertEquals('prefix_open', $statuses['open']);

        return $message;
    }

    /**
     *  @depends testMessageGetStatuses
     */
    public function testMessageGet($message)
    {
        /** @var Message $message */
        $message->setMessage('message');
        $idValue = 100;
        $reflector = new \ReflectionClass('Dreamlex\Bundle\TicketBundle\Entity\Message');
        $id = $reflector->getProperty('id');
        $id->setAccessible(true);
        $id->setValue($message,$idValue);
        self::assertEquals($idValue,$message->getId());
        self::assertEquals('message', $message->__toString());
    }
}
