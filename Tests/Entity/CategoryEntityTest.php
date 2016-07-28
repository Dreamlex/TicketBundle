<?php
/**
 * Created by PhpStorm.
 * User: dreamlex
 * Date: 22.07.16
 * Time: 16:07
 */

namespace Dreamlex\Bundle\TicketBundle\Tests\Entity;

use Dreamlex\Bundle\TicketBundle\Entity\Category;
use Dreamlex\Bundle\TicketBundle\Entity\Ticket;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class CategoryEntityTest
 * @package Dreamlex\Bundle\TicketBundle\Tests\Entity
 */
class CategoryEntityTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager $em
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * @return Category
     */
    public function testAddCategory()
    {
        $category = new Category();
        $categoryOptions = [
            'position' => 1,
            'title' => 'Title',
        ];
        $category->setTitle($categoryOptions['title']);
        $category->setPosition($categoryOptions['position']);
        $this->em->persist($category);
        $this->em->flush($category);
        self::assertEquals($categoryOptions['title'], $category->getTitle());
        self::assertEquals($categoryOptions['position'], $category->getPosition());
        self::assertNotNull($category->getId());

        $this->em->remove($category);
        $this->em->flush();

        return $category;
    }

    /**
     * @depends testAddCategory
     * @param Category $category
     */
    public function testGetCategoryTickets($category)
    {
        $ticket = $this->createMock('Dreamlex\Bundle\TicketBundle\Entity\Ticket');
        $category->addTicket($ticket);
        $ticketList = $category->getTickets();
        self::assertNotNull($ticketList);
        $category->removeTicket($ticket);
        $ticketList = $category->getTickets();
        self::assertEmpty($ticketList);
    }

    /**
     * {@inheritDoc}
     *
     */
    protected function tearDown($category)
    {
        parent::tearDown();
        $this->em->close();
        $this->em = null; // avoid memory leaks
    }
}
