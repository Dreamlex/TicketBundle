<?php

namespace Dreamlex\Bundle\TicketBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TicketType
 * @package Dreamlex\TicketBundle\Form
 */
class TicketType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class, [
                    'class' => 'Dreamlex\TicketBundle\Entity\Category',
                    'label' => 'ticket.label.form.category.title',
                    'choice_label' => 'title',
                ]
            )
            ->add('subject', TextType::class, [
                'label' => 'ticket.label.form.subject',
                'attr' => [
                    'placeholder' => 'ticket.placeholder.form.subject',
                ],
            ])
            ->add('messages', CollectionType::class, [
                'type' => new TicketMessageType(true),
                'label' => false,
                'allow_add' => true,
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Dreamlex\TicketBundle\Entity\Ticket',
            'show_legend' => false,
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'ticket';
    }
}
