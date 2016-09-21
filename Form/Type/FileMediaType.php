<?php
/**
 * Created by PhpStorm.
 * User: SERGEY
 * Date: 08.04.2016
 * Time: 10:50
 */

namespace Dreamlex\Bundle\TicketBundle\Form\Type;

use Sonata\MediaBundle\Form\DataTransformer\ProviderDataTransformer;
use Sonata\MediaBundle\Form\Type\MediaType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class FileMediaType extends MediaType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new ProviderDataTransformer($this->pool, $this->class, array(
            'provider'      => $options['provider'],
            'context'       => $options['context'],
            'empty_on_new'  => $options['empty_on_new'],
            'new_on_update' => $options['new_on_update'],
        )));

//        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
//            if ($event->getForm()->has('unlink') && $event->getForm()->get('unlink')->getData()) {
//                $event->setData(null);
//            }
//        }
//        );

        /** @var FormBuilder $builder */
        $this->pool->getProvider($options['provider'])->buildMediaType($builder);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'file_type';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}