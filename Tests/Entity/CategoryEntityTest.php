<?php
/**
 * Created by PhpStorm.
 * User: dreamlex
 * Date: 22.07.16
 * Time: 16:07
 */

namespace Dreamlex\TicketBundle\Tests\Entity;

use Dreamlex\TicketBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class CategoryEntityTest
 * @package Dreamlex\TicketBundle\Tests\Entity
 */
class CategoryEntityTest extends \PHPUnit_Framework_TestCase
{
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
        $idValue = 100;
        $reflector = new \ReflectionClass('Dreamlex\TicketBundle\Entity\Category');
        $id = $reflector->getProperty('id');
        $id->setAccessible(true);
        $id->setValue($category,$idValue);
        self::assertEquals($categoryOptions['title'], $category->getTitle());
        self::assertEquals($categoryOptions['position'], $category->getPosition());
        self::assertEquals($idValue, $category->getId());
        self::assertEquals($categoryOptions['title'],$category);

        return $category;
    }

    /**
     * @depends testAddCategory
     * @param Category $category
     */
    public function testGetCategoryTickets($category)
    {
        $ticket = $this->createMock('Dreamlex\TicketBundle\Entity\Ticket');
        $category->addTicket($ticket);
        $ticketList = $category->getTickets();
        self::assertNotNull($ticketList);
        $category->removeTicket($ticket);
        $ticketList = $category->getTickets();
        self::assertEmpty($ticketList);
    }
}
