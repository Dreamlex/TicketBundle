<?php

/*
 * This file is part of the FOSRestBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dreamlex\TicketBundle\Tests\Functional;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;

class FunctionalWebTestCase extends WebTestCase
{
    /**
     * @var EntityManager
     */
    protected $em;

    protected static $schemaSetUp = false;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client;

    /**
     * TODO проверить зачем эта функция
     *
     * @param $testCase
     */
    protected function deleteTmpDir($testCase)
    {
        if (!file_exists($dir = sys_get_temp_dir().'/'.Kernel::VERSION.'/'.$testCase)) {
            return;
        }

        $fs = new Filesystem();
        $fs->remove($dir);
    }

    protected static function getKernelClass()
    {
        require_once __DIR__.'/app/AppKernel.php';

        return 'Dreamlex\TicketBundle\Tests\Functional\app\AppKernel';
    }

    protected function setUp()
    {
        if (null === $this->em) {
            $this->em = $this->client->getContainer()->get('doctrine')->getManager();
            if (!static::$schemaSetUp) {
                $st = new SchemaTool($this->em);

                $classes = $this->em->getMetadataFactory()->getAllMetadata();

                $st->dropSchema($classes);
                $st->createSchema($classes);

                static::$schemaSetUp = true;
            }
        }

        $this->container = static::$kernel->getContainer();

        parent::setUp();
    }

    protected static function createKernel(array $options = array())
    {
        $class = self::getKernelClass();

        if (!isset($options['test_case'])) {
            throw new \InvalidArgumentException('The option "test_case" must be set.');
        }

        return new $class(
            $options['test_case'],
            isset($options['root_config']) ? $options['root_config'] : 'config.yml',
            isset($options['environment']) ? $options['environment'] : 'test'.strtolower($options['test_case']),
            isset($options['debug']) ? $options['debug'] : true
        );
    }
}
