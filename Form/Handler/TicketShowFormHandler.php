<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 05.07.16
 * Time: 16:48
 */

namespace Dreamlex\TicketBundle\Form\Handler;

use Dreamlex\TicketBundle\Entity\Manager\TicketManager;
use Dreamlex\TicketBundle\Entity\Message;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TicketShowFormHandler
 * @package Dreamlex\Bundle\TicketBundle\Form\Handler
 */
class TicketShowFormHandler
{
    private $ticketManager;

    /**
     * TicketShowFormHandler constructor.
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
         * @var Message $message
         */
        $message = $form->getData();

        $ticket = $message->getTicket();
        /**
         * @var SubmitButton $closeTicket
         */
        $closeTicket = $form->get('closeTicket');
        $this->ticketManager->updateTicket($ticket, $closeTicket->isClicked(), $message);

        return true;
    }

}
