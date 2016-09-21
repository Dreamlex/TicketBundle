<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 06.07.16
 * Time: 13:56
 */

namespace Dreamlex\Bundle\TicketBundle\Entity\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use Dreamlex\TicketBundle\Entity\Message;
use Dreamlex\TicketBundle\Entity\Ticket;
use Symfony\Component\Security\Core\User\UserInterface as User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class TicketManager
 * @package Dreamlex\Bundle\TicketBundle\DomainManager
 */
class TicketManager
{
    private $tokenStorage;
    private $om;
    private $messageManager;

    /**
     * TicketManager constructor.
     *
     * @param ObjectManager         $om
     * @param TokenStorageInterface $tokenStorage
     * @param MessageManager        $messageManager
     */
    public function __construct(ObjectManager $om, TokenStorageInterface $tokenStorage, MessageManager $messageManager)
    {
        $this->messageManager = $messageManager;
        $this->om = $om;
        $this->tokenStorage = $tokenStorage;

    }

    /**
     * @return Ticket
     */
    public function createTicket()
    {
        $ticket = new Ticket();
        $this->messageManager->createNewTicketMessage($ticket);

        return $ticket;

    }

    /**
     * @param Ticket $ticket
     */
    public function flushTicket(Ticket $ticket)
    {
        $this->om->persist($ticket);
        $this->om->flush();
    }

    /**
     * @param Ticket  $ticket
     * @param bool    $isCloseTicket
     * @param Message $message
     */
    public function updateTicket(Ticket $ticket, bool $isCloseTicket, Message $message)
    {
        if ($isCloseTicket) {
            $ticket->setStatus('closed');
        } else {
            $ticket->setStatus('open');
        }
        $ticket->setPriority($message->getPriority());
        $ticket->setLastUser($this->getUser());
        $ticket->setIsRead(false);
        $ticket->setLastMessageAt($message->getCreatedAt());

        $this->om->flush();

    }

    /**
     * @param Ticket $ticket
     */
    public function markTicketIsRead(Ticket $ticket, User $currentUser)
    {
        if (!$ticket->getIsRead() && $ticket->getLastUser() !== $currentUser) {
            $ticket->setIsRead(true);
            $this->om->flush();
        }
    }

    /**
     * @return mixed
     */
    private function getUser()
    {
        return $this->tokenStorage->getToken()->getUser();
    }
}
