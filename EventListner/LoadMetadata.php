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
    protected $mediaEntity;

    public function __construct($userRepository, $primary_key, $mediaEntity)
    {
        $this->userRepository = $userRepository;
        $this->primary_key = $primary_key;
        $this->mediaEntity = $mediaEntity;
    }

    public function getSubscribedEvents()
    {
        return ['loadClassMetadata',];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();
        $class_name = $classMetadata->getName();

        if($class_name == "Dreamlex\TicketBundle\Entity\Ticket") {

            // The following is to map ORM with PHP
            $mapping = array(
                'targetEntity' => $this->userRepository,
                'fieldName' => 'user',
                'inversedBy' => 'tickets',
                'joinColumns' => array(
                    array(
                        'name' => 'user_id',
                        'referencedColumnName' => $this->primary_key,
                        'nullable' => false
                    )
                )
            );

            $classMetadata->mapManyToOne($mapping);
        }
        if($class_name == "Dreamlex\TicketBundle\Entity\Message") {

            // The following is to map ORM with PHP
            $mapping = array(
                'targetEntity' => $this->userRepository,
                'fieldName' => 'user',
                'joinColumns' => array(
                    array(
                        'name' => 'user_id',
                        'referencedColumnName' => $this->primary_key,
                        'nullable' => false
                    )
                )
            );
            $mappingMedia = array(
                'targetEntity' => $this->mediaEntity,
                'cascade' => array(
                    'persist',
                ),
                'joinColumns' => array(
                    array(
                        'name' => 'media_id',
                        'referencedColumnName' => 'id',
                    )
                )
            );
            $classMetadata->mapManyToOne($mapping);
            $classMetadata->mapManyToOne($mappingMedia);
        }
    }
}