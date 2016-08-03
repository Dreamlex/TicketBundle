<?php
/**
 * Created by PhpStorm.
 * User: dreamlex
 * Date: 25.07.16
 * Time: 9:44
 */

namespace Dreamlex\TicketBundle\Tests\Entity;

use Dreamlex\TicketBundle\Entity\Message;


/**
 * Class MessageEntityTest
 * @package Dreamlex\TicketBundle\Tests\Entity
 */
class MessageEntityTest extends \PHPUnit_Framework_TestCase
{
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
        $reflector = new \ReflectionClass('Dreamlex\TicketBundle\Entity\Message');
        $id = $reflector->getProperty('id');
        $id->setAccessible(true);
        $id->setValue($message,$idValue);
        self::assertEquals($idValue,$message->getId());
        self::assertEquals('message', $message);
    }
}
