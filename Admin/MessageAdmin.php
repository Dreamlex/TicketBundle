<?php

namespace Dreamlex\Bundle\TicketBundle\Admin;

use Dreamlex\Bundle\TicketBundle\Entity\Message;
use Dreamlex\Bundle\TicketBundle\Entity\Ticket;
use Dreamlex\Bundle\TicketBundle\Form\Type\FileMediaType;
use SellMMO\Sonata\UserBundle\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class MessageAdmin
 * @package Dreamlex\Bundle\TicketBundle\Admin
 */
class MessageAdmin extends AbstractAdmin
{
    protected $parentAssociationMapping = 'ticket';
    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * @param TokenStorage $tokenStorage
     */
    public function setTokenStorage(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return Message
     */
    public function getNewInstance()
    {
        /** @var Message $message */
        $message = parent::getNewInstance();

        if ($this->isChild()) {
            $admin = $this->getParent();
            $id = $admin->getRequest()->get('id');

            /** @var Ticket $ticket */
            $ticket = $admin->getObject($id);

            $message->setStatus($ticket->getStatus())
                ->setPriority($ticket->getPriority())
                ->setUser($this->tokenStorage->getToken()->getUser())
                ->setTicket($ticket);
        }

        return $message;
    }

    /**
     * @param Message $message
     *
     * @return mixed|void
     * @internal param Ticket $ticket
     *
     */
    public function prePersist($message)
    {
        /** @var Ticket $ticket */
        $ticket = $message->getTicket();
        if ($ticket->getIsRead()) {
            $ticket->setIsRead(false);
            $ticket->setLastUser($this->tokenStorage->getToken()->getUser());
            $om = $this->getConfigurationPool()
                ->getContainer()
                ->get('doctrine')
                ->getManager();
            $om->persist($ticket);
            $om->flush();
        }
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('message')
            ->add('status', null, [], 'choice', [
                'choices' => Message::getStatuses('ticket.status_'),
            ])
            ->add('priority', null, [], 'choice', [
                'choices' => Message::getPriorities(),
            ])
            ->add('createdAt');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        /** @var Ticket $ticket */
        $ticket = $this->getParent()->getSubject();
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        if (!$ticket->getIsRead() && $user !== $ticket->getLastUser()) {
            $ticket->setIsRead(true);
            $ticket->setLastUser($user);
            $om = $this->getConfigurationPool()
                ->getContainer()
                ->get('doctrine')
                ->getManager();
            $om->persist($ticket);
            $om->flush();
        }
        $listMapper
            ->add('message', 'string')
            ->add('status', 'string', ['template' => 'DreamlexTicketBundle:Admin:list_status_label.html.twig'])
            ->add('priority', 'string', ['template' => 'DreamlexTicketBundle:Admin:list_priority_label.html.twig'])
            ->add('media', 'string', ['template' => 'DreamlexTicketBundle:Admin:list_media_label.html.twig'])
            ->add('createdAt')
            ->add('_action', null, array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                ),
            ));
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('message', TextareaType::class, [
                'required' => true,
            ])
            ->add('status', 'choice', [
                'choices' => Message::getStatuses('ticket.status_'),
            ])
            ->add('priority', 'choice', [
                'choices' => Message::getPriorities(),
            ])
            ->add('media', FileMediaType::class, [
                'provider' => 'sonata.media.provider.ticket_image',
                'context' => 'ticket',
            ])
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('message')
            ->add('status')
            ->add('priority')
            ->add('createdAt')
            ->add('media', 'sonata_media_type');
    }
}
