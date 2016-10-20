<?php
namespace Dreamlex\Bundle\TicketBundle\Tests\Functional;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Dreamlex\Bundle\CoreBundle\Tests\FixtureAwareTestCaseTrait;
use Dreamlex\Bundle\CoreBundle\Tests\WebTestCase;
use Dreamlex\Bundle\TicketBundle\DataFixtures\ORM\LoadTicket;
use Dreamlex\Bundle\TicketBundle\DataFixtures\ORM\LoadTicketCategories;
use Dreamlex\Bundle\TicketBundle\DataFixtures\ORM\LoadTicketUsers;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Client;

class TicketWebTestCase extends WebTestCase
{
    use FixtureAwareTestCaseTrait;

    /** @var  Client */
    protected $client;

    /**  */
    public static function setUpBeforeClass()
    {
        parent::deleteTmpDir('UnitManager');

        static::bootKernel();
        static::$em = static::$kernel->getContainer()->get('doctrine')->getManager();

        static::prepareSchema();

        static::addFixture(new LoadUserStatusFixture());
        static::executeFixtures();

        $manipulator = static::$kernel->getContainer()->get('fos_user.util.user_manipulator');
        $manipulator->create('admin', 'admin', 'test@example.com', true, false);
        $manipulator->addRole('admin', 'ROLE_SUPER_ADMIN');
    }

    /**  */
    public static function tearDownAfterClass()
    {
        parent::deleteTmpDir('UnitManager');
    }

    /**  */
    public function setUp()
    {
        $this->client = static::createClient(['test_case' => 'Ticket'], [
            'PHP_AUTH_USER' => 'test-user',
            'PHP_AUTH_PW' => 'test-user',
        ]);

        parent::setUp();
    }

    /**  */
    public function tearDown()
    {
        self::$em->clear();

        parent::tearDown();
    }

    /**
     * @var SchemaTool
     */
    protected static $schemaTool;

//    public static function runFixMedia($application)
//    {
//        $application->setAutoExit(false);
//
//        $input = new ArrayInput(array(
//            'command' => 'sonata:media:fix-media-context', '-vvv' => true
//        ));
//
//        $output = new BufferedOutput();
//        $application->run($input, $output);
//
//    }

    /**
     * @param Container $container
     */
    public static function executeFixtures($container)
    {
        $loader = new ContainerAwareLoader($container);
        $loader->addFixture(new LoadTicketUsers());
        $loader->addFixture(new LoadTicketCategories());
        $loader->addFixture(new LoadTicket());

        /**
         * @var $em EntityManager
         */
        $em = $container->get('doctrine')->getManager();

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($loader->getFixtures(), true);
    }
}