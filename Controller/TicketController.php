<?php

namespace Dreamlex\TicketBundle\Controller;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Row;
use APY\DataGridBundle\Grid\Source\Entity;
use Doctrine\ORM\QueryBuilder;
use Dreamlex\TicketBundle\Entity\Ticket;
use Dreamlex\TicketBundle\Form\TicketMessageType;
use Dreamlex\TicketBundle\Form\TicketType;
use SellMMO\Sonata\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/", options={"i18n": false})
 */
class TicketController extends Controller
{
    //TODO Change All roles To Provider, уточнить какие роли имееют доступ к тикетам
    //TODO REVERT JMS SECURITY BUNDLE

    /**
     * @Template()
     * @Route("/", name="ticket.ticket.index")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @return array
     */
    public function indexAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $source = new Entity('DreamlexTicketBundle:Ticket');
        $source->addHint(
            \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        $prefixTitle = 'ticket.label.grid.';
        $translator = $this->get('translator');
        $tableAlias = $source->getTableAlias();
        $source->manipulateQuery(
            function ($query) use ($tableAlias, $user) {
                /** @var QueryBuilder $query */
                $query->andWhere($tableAlias.'.user = :user')
                    ->setParameter('user', $user);
            }
        );
        $source->manipulateRow(
            function ($row) use ($user) {
                /** @var Row $row */
                if ($row->getField('lastUser.id') != $user->getId() && !$row->getField('isRead')) {
                    $row->setClass('warning');
                }

                return $row;
            }
        );
        $grid = $this->get('grid');
        $grid->setSource($source);
        $grid->setLimits(10);
        $grid->setPrefixTitle($prefixTitle);
        $grid->setId('ticket');
        $grid->setNoDataMessage($translator->trans('ticket.message.no_tickets'));
        $replyAction = new RowAction(
            'ticket.button.reply',
            'ticket.ticket.show',
            false,
            '_self',
            [
                'class' => 'btn btn-success',
                'icon' => 'fa fa-reply',
            ]
        );
        $grid->addRowAction($replyAction);
        $grid->isReadyForRedirect();

        return $grid->getGridResponse();
    }


    /**
     * @Route("/create", name="ticket.ticket.create")
     * @Template()
     * @param Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \LogicException
     */
    public function createAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $translator = $this->get('translator');
        $ticketManager = $this->get('dreamlex_ticket.ticket_manager');
        $ticket = $ticketManager->createTicket();

        $form = $this->createForm(new TicketType(), $ticket);
        $formHandler = $this->get('dreamlex_ticket.create_form_handler');
        if ($formHandler->handle($form, $request)) {
            $this->addFlash('notice', $translator->trans('ticket.flash.created'));

            return $this->redirectToRoute('ticket.ticket.index');
        }

        return [
            'entity' => $ticket,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Template()
     * @Route("/{id}/show", name="ticket.ticket.show")
     * @ParamConverter("ticket", class="DreamlexTicketBundle:Ticket")
     * @param Request $request
     * @param Ticket $ticket
     *
     * @return array
     * @throws \LogicException
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function showAction(Request $request, Ticket $ticket)
    {
        $this->denyAccessUnlessGranted('edit', $ticket);

        $translator = $this->get('translator');

        $ticketManager = $this->get('dreamlex_ticket.ticket_manager');
        /** @var User $user */
        $ticketManager->markTicketIsRead($ticket, $this->getUser());

        $messageManager = $this->get('dreamlex_ticket.message_manager');
        $message = $messageManager->createMessage($ticket);
        $currentPriority = $ticket->getPriority();
        $form = $this->createForm(new TicketMessageType(), $message);

        $formHandler = $this->get('dreamlex_ticket.show_form_handler');
        if ($formHandler->handle($form, $request)) {
            if ($ticket->getStatus() === 'closed') {
                $this->addFlash('danger', $translator->trans('ticket.flash.closed'));

                return $this->redirectToRoute('ticket.ticket.index');
            }
            if ($ticket->getPriority() !== $currentPriority) {
                $this->addFlash('notice', $translator->trans('ticket.flash.changed_priority'));
            }

            if ($message->getMessage() !== null || $message->getMedia() !== null) {
                $this->addFlash('notice', $translator->trans('ticket.flash.answer_added'));
            }

            return $this->redirectToRoute('ticket.ticket.show', ['id' => $ticket->getId()]);
        }

        return [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ];
    }
}
