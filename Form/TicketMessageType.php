<?php

namespace Dreamlex\Bundle\TicketBundle\Form;

use Dreamlex\TicketBundle\Entity\Message;
use Dreamlex\TicketBundle\Form\Type\FileMediaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TicketMessageType
 * @package Dreamlex\TicketBundle\Form
 */
class TicketMessageType extends AbstractType
{
    private $isNewTicket;

    /**
     * TicketMessageType constructor.
     *
     * @param bool $isNewTicket
     */
    public function __construct($isNewTicket = false)
    {
        $this->isNewTicket = $isNewTicket;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'message',
                TextareaType::class,
                [
                    'label' => 'ticket.label.form.message',
                    'attr' => [
                        'placeholder' => 'ticket.placeholder.form.message',
                    ],
                    'required' => false,
                ]
            )
            ->add(
                'media',
                FileMediaType::class,
                [
                    'provider' => 'sonata.media.provider.ticket_image',
                    'context' => 'ticket',
                    'label' => 'ticket.label.form.image',
                    'translation_domain' => 'messages',
                    'required' => false,
                ]
            )
            ->add(
                'priority',
                ChoiceType::class,
                [
                    'label' => 'ticket.label.form.priority',
                    'choices' => Message::getPriorities(),
                ]
            );

        // if existing ticket add status
        if (!$this->isNewTicket) {
            $builder
                ->add(
                    'closeTicket',
                    SubmitType::class,
                    [
                        'label' => 'ticket.button.close_ticket',
                        'translation_domain' => 'messages',
                        'attr' => ['value' => 'closeTicket', 'class' => 'btn btn-danger'],
                        'validation_groups' => false,
                    ]
                )
                ->add(
                    'changePriority',
                    SubmitType::class,
                    [
                        'label' => 'ticket.button.change.priority',
                        'validation_groups' => false,
                        'attr' => ['value' => 'changePriority', 'class' => 'btn btn-warning'],
                    ]
                )
                ->add(
                    'submit',
                    SubmitType::class,
                    [
                        'label' => 'ticket.button.reply',
                        'attr' => ['class' => 'btn btn-primary'],
                    ]
                );
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'Dreamlex\TicketBundle\Entity\Message',
                'show_legend' => false,
            ]
        );
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'Message';
    }
}
