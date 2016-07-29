<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 04.07.16
 * Time: 11:35
 */

namespace Dreamlex\TicketBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Dreamlex\TicketBundle\Entity\Message;
use Dreamlex\TicketBundle\Entity\Ticket;
use Sonata\MediaBundle\Model\Media;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Yaml\Yaml;

/**
 * Class LoadTicket
 * @package Dreamlex\TicketBundle\DataFixtures\ORM
 */
class LoadTicket extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    /** @var ContainerInterface */
    private $container;

    /**
     * @return int
     */
    public function getOrder()
    {
        return 4;
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $om)
    {
        $this->loadTicketData($om);
    }

    /**
     * @param ObjectManager $om
     */
    public function loadTicketData(ObjectManager $om)
    {
        $finder = new Finder();
        $finder->name('Ticket*.yml')->in(__DIR__.'/../TicketData')->sortByName();

        foreach ($finder as $file) {
            $ticket = Yaml::parse(file_get_contents($file));
            $ticketEntity = new Ticket();
            $this->setTicketParameters($ticketEntity, $ticket);
            $om->persist($ticketEntity);
            $messages = $ticket['messages'];
            if (!empty($messages) && is_array($messages)) {
                foreach ($messages as $item) {
                    $message = new Message();
                    if (!empty($item['message'])) {
                        $message->setMessage($item['message']);
                    }

                    if (!empty($item['mediaFile'])) {
                        $this->setMessageImage($message, $item);
                    }
                    $message
                        ->setUser($this->getReference($item['user']))
                        ->setStatus($item['status'])
                        ->setPriority($item['priority'])
                        ->setCreatedAt(new \DateTime($item['createdAt']))
                        ->setTicket($ticketEntity);

                    $om->persist($message);
                }
            }
            $om->flush();
        }


        //END message

    }

    /**
     * @param Message $message
     * @param array   $item
     */
    public function setMessageImage(Message $message, array $item)
    {
        $imageFile = new File(__DIR__.'/../TicketImages/'.$item['mediaFile']);
//        var_dump($imageFile);
//        exit();
        $mediaManager = $this->getMediaManager();

        /** @var Media $image */
        $image = $mediaManager->create();
        $image->setBinaryContent($imageFile);
        $image->setEnabled(true);
        $image->setName($item['mediaName']);
        $image->setContext('ticket');
        $image->setProviderName('sonata.media.provider.ticket_image');
        $mediaManager->save($image);
        $message->setMedia($image);
    }

    /**
     * @return \Sonata\MediaBundle\Entity\MediaManager
     *
     */
    public function getMediaManager()
    {
        return $this->container->get('sonata.media.manager.media');
    }

    /**
     * @param Ticket $ticketEntity
     * @param array  $ticket
     *
     * @return Ticket
     */
    public function setTicketParameters(Ticket $ticketEntity, array $ticket)
    {
        $ticketEntity
            ->setCategory($this->getReference($ticket['category']))
            ->setIsRead($ticket['isRead'])
            ->setSubject($ticket['subject'])
            ->setStatus($ticket['status'])
            ->setPriority($ticket['priority'])
            ->setUser($this->getReference($ticket['user']))
            ->setLastUser($this->getReference($ticket['lastUser']))
            ->setCreatedAt(new \DateTime($ticket['createdAt']))
            ->setLastMessageAt(new \DateTime());

        return $ticketEntity;
    }
}
