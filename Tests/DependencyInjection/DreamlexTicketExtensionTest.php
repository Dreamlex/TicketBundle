<?php
/**
 * Created by PhpStorm.
 * User: dreamlex
 * Date: 23.07.16
 * Time: 15:39
 */

namespace Dreamlex\TicketBundle\Tests\DependencyInjection;

use Dreamlex\TicketBundle\DependencyInjection\DreamlexTicketExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DreamlexTicketExtensionTest
 * @package Dreamlex\Bundle\TicketBundle\Tests\DependencyInjection
 */
class DreamlexTicketExtensionTest extends KernelTestCase
{
    /**
     * @var DreamlexTicketExtension
     */
    private $extension;
    /**
     * @var ContainerBuilder
     */
    private $container;

    protected function setUp()
    {
        self::bootKernel();
        $this->extension = new DreamlexTicketExtension();

        $this->container = new ContainerBuilder();
        $this->container->registerExtension($this->extension);
    }

    /**
     * Service load
     */
    public function testDreamlexServiceLoad()
    {
        $this->container->loadFromExtension($this->extension->getAlias());
        self::assertEquals($this->extension->getAlias(), 'dreamlex_ticket');
        self::assertNull($this->extension->load([], $this->container));
    }
}
