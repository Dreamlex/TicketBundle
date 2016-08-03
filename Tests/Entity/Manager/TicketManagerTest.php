<?php
/**
 * Created by PhpStorm.
 * User: dreamlex
 * Date: 25.07.16
 * Time: 12:36
 */

namespace Dreamlex\TicketBundle\Tests\Entity\Manager;

use Dreamlex\TicketBundle\Entity\Manager\MessageManager;
use Dreamlex\TicketBundle\Entity\Manager\TicketManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class TicketManagerTest
 * @package Dreamlex\Bundle\TicketBundle\Tests\Entity\Manager
 */
class TicketManagerTest extends WebTestCase
{
    private $tm;
    private $om;
    private $tokenStorage;
    private $messageManager;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->om = $this->createMock('Doctrine\ORM\EntityManager');
        $this->tokenStorage = new TokenStorage();
        $this->messageManager = new MessageManager($this->om, $this->tokenStorage);
        $this->tm = new TicketManager($this->om, $this->tokenStorage, $this->messageManager);
    }

    /**
     *
     */
    public function testGetManagers()
    {
        self::assertInstanceOf(TicketManager::class, $this->tm);
        self::assertInstanceOf(MessageManager::class, $this->messageManager);
    }

    public function testCreateTicket()
    {

    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
    }
}
