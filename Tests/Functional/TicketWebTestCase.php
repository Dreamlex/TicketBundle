<?php
/**
 * Created by PhpStorm.
 * User: dreamlex
 * Date: 11.08.16
 * Time: 16:13
 */

namespace Dreamlex\TicketBundle\Tests\Functional;


use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Dreamlex\TicketBundle\DataFixtures\ORM\LoadTicket;
use Dreamlex\TicketBundle\DataFixtures\ORM\LoadTicketCategories;
use Dreamlex\TicketBundle\DataFixtures\ORM\LoadTicketUsers;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\DependencyInjection\Container;

class TicketWebTestCase extends FunctionalWebTestCase
{

    /**
     * @var SchemaTool
     */
    protected static $schemaTool;

    public static function runDoctrineSchema($application)
    {

        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'doctrine:schema:update', '--dump-sql' => true, '--force' => true, '--complete' => true
        ));

        $output = new BufferedOutput();
        $application->run($input, $output);
    }

    public static function runFixMedia($application)
    {
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'sonata:media:fix-media-context', '-vvv' => true
        ));

        $output = new BufferedOutput();
        $application->run($input, $output);

    }

    /**
     * @param Container $container
     */
    public static function executeFixtures($container){
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