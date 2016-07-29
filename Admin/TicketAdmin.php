<?php

namespace Dreamlex\TicketBundle\Admin;

use Dreamlex\TicketBundle\Entity\Message;
use Dreamlex\TicketBundle\Entity\Ticket;
use JMS\DiExtraBundle\Annotation\Inject;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;


/**
 * Class TicketAdmin
 * @package Dreamlex\Bundle\TicketBundle\Admin
 */
class TicketAdmin extends AbstractAdmin
{
    protected $tokenStorage;

    /**
     * @param TokenStorage $tokenStorage
     */
    public function setTokenStorage(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Default Datagrid values
     *
     * @var array
     */
    protected $datagridValues = array(
        '_page' => 1,            // display the first page (default = 1)
        '_sort_order' => 'DESC', // reverse order (default = 'ASC')
        '_sort_by' => 'isRead',  // name of the ordered field
    );

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('lastMessageAt')
            ->add('subject')
            ->add('status', null, [], 'choice', [
                'choices' => Message::getStatuses('ticket.status_'),
            ])
            ->add('priority', null, [], 'choice', [
                'choices' => Message::getPriorities(),
            ])
            ->add('createdAt')
            ->add('isRead');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('lastMessageAt')
            ->add('category')
            ->add('subject')
            ->add('status', 'string', ['template' => 'DreamlexTicketBundle:Admin:list_status_label.html.twig'])
            ->add('priority', 'string', ['template' => 'DreamlexTicketBundle:Admin:list_priority_label.html.twig'])
            ->add('createdAt')
            ->add('isRead')
            ->add('_action', null, array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                    'message' => ['template' => 'DreamlexTicketBundle:Admin:list__action_message.html.twig'],
                )
            ));
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Ticket',[
                'class' => 'col-md-3',
                'box_class' => 'box box-solid box-warning',
            ])
            ->add('category', EntityType::class, [
                    'class' => 'Dreamlex\Bundle\TicketBundle\Entity\Category',
                    'label' => 'ticket.label.form.category.title',
                    'choice_label' => 'title',
                ]
            )
            ->add('user')
            ->add('subject')
            ->end()
            ->with('Messages', [
                'class' => 'col-md-9',
                'box_class' => 'box box-solid box-success',])
            ->add('messages', 'sonata_type_collection', ['by_reference' => false], [
                'edit' => 'inline',
            ]);
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('lastMessageAt')
            ->add('subject')
            ->add('status')
            ->add('priority')
            ->add('media', 'sonata_media_type', [
                'provider' => 'sonata.media.provider.ticket_image',
                'context' => 'ticket'
            ])
            ->add('createdAt')
            ->add('isRead');
    }

    /**
     * @param Ticket $ticket
     *
     * @return mixed|void
     */
    public function prePersist($ticket)
    {
        if ($ticket->getMessages()->count() > 0) {
            /* @var Message $message */
            $message = $ticket->getMessages()->first();
            $message->setUser($this->tokenStorage->getToken()->getUser());
        }
    }
}
