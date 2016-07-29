<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 06.07.16
 * Time: 17:19
 */

namespace Dreamlex\TicketBundle\Entity\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use Dreamlex\TicketBundle\Entity\Message;
use Dreamlex\TicketBundle\Entity\Ticket;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 *
 * Class MessageManager
 * @package Dreamlex\Bundle\TicketBundle\Entity\Manager
 */
class MessageManager
{
    private $om;
    private $tokenStorage;

    /**
     * MessageManager constructor.
     *
     * @param ObjectManager         $om
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(ObjectManager $om, TokenStorageInterface $tokenStorage)
    {
        $this->om = $om;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param Ticket $ticket
     *
     * @return Message
     */
    public function createMessage(Ticket $ticket)
    {
        $message = new Message();
        $message->setUser($this->tokenStorage->getToken()->getUser())
            ->setTicket($ticket);
        $message->setPriority($ticket->getPriority());

        $this->om->persist($message);

        return $message;
    }

    /**
     * @param Ticket $ticket
     */
    public function createNewTicketMessage(Ticket $ticket)
    {
        $message = new Message();
        $ticket->addMessage($message);
        $message->setStatus(Message::STATUS_OPEN)
            ->setUser($this->tokenStorage->getToken()->getUser())
            ->setTicket($ticket);
    }

}