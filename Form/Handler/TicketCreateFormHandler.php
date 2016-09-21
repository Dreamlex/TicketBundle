<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 06.07.16
 * Time: 13:51
 */

namespace Dreamlex\Bundle\TicketBundle\Form\Handler;

use Dreamlex\TicketBundle\Entity\Manager\TicketManager;
use Dreamlex\TicketBundle\Entity\Ticket;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TicketCreateFormHandler
 * @package Dreamlex\Bundle\TicketBundle\Form\Handler
 */
class TicketCreateFormHandler
{
    private $ticketManager;

    /**
     * TicketCreateFormHandler constructor.
     *
     * @param TicketManager $ticketManager
     */
    public function __construct(TicketManager $ticketManager)
    {
        $this->ticketManager = $ticketManager;
    }

    /**
     * @param FormInterface $form
     * @param Request       $request
     *
     *
     * @return bool
     */
    public function handle(FormInterface $form, Request $request)
    {
        if (!$request->isMethod('POST')) {
            return false;
        }

        $form->handleRequest($request);
        if (!$form->isValid()) {
            return false;
        }
        /**
         * @var Ticket $ticket
         */
        $ticket = $form->getData();
        $this->ticketManager->flushTicket($ticket);

        return true;
    }
}