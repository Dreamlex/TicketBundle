<?php
/**
 * Created by PhpStorm.
 * User: dreamlex
 * Date: 01.08.16
 * Time: 11:41
 */

namespace Dreamlex\Bundle\TicketBundle\Entity\Traits;

use Dreamlex\Bundle\TicketBundle\Entity\Ticket;


trait UserTrait
{
    /**
     * @ORM\OneToMany(targetEntity="Dreamlex\Bundle\TicketBundle\Entity\Ticket", mappedBy="user")
     */
    private $tickets;

    /**
     * Add ticket
     *
     * @param Ticket $ticket
     *
     * @return User
     */
    public function addTicket(Ticket $ticket)
    {
        $this->tickets[] = $ticket;

        return $this;
    }

    /**
     * Get tickets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTickets()
    {
        return $this->tickets;
    }
}