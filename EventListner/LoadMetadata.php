<?php
/**
 * Created by PhpStorm.
 * User: dreamlex
 * Date: 01.08.16
 * Time: 16:16
 */

namespace Dreamlex\TicketBundle\EventListner;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

/**
 * Class LoadMetadata
 * @package Dreamlex\TicketBundle\EventListner
 */
class LoadMetadata {
    protected $userRepository;
    protected $primary_key;

    public function __construct($userRepository, $primary_key)
    {
        $this->userRepository = $userRepository;
        $this->primary_key = $primary_key;
    }

    public function getSubscribedEvents()
    {
        return ['loadClassMetadata',];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();
        $class_name = $classMetadata->getName();

        if($class_name == "Dreamlex\TicketBundle\Entity\Ticket" OR
            $class_name == "Dreamlex\TicketBundle\Entity\Message" ) {

            // The following is to map ORM with PHP
            $mapping = array(
                'targetEntity' => $this->userRepository,
                'fieldName' => 'user',
                'joinColumns' => array(
                    array(
                        'name' => 'user_id',
                        'referencedColumnName' => $this->primary_key
                    )
                )
            );

            $classMetadata->mapManyToOne($mapping);
        }
    }
}