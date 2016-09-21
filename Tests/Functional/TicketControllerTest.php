<?php

namespace Dreamlex\Bundle\TicketBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DomCrawler\Field\ChoiceFormField;
use Symfony\Component\DomCrawler\Field\FileFormField;
use Symfony\Component\DomCrawler\Field\InputFormField;
use Symfony\Component\HttpKernel\Client;

/**
 * TicketTestController
 */
class TicketControllerTest extends TicketWebTestCase
{
    /** @var  Client */
    protected $client;
    const TEST_USER = 'test-user';
    const TEST_PASSWORD = 'test-user';
    const TICKET_ID = '6';

    /**
     * {@inheritDoc}
     */

    public static function setUpBeforeClass()
    {
        # get the container
        $client = static::createClient(['test_case' => 'DefaultTestCase']);
        /** @var Container $container */
        $container = $client->getContainer();
        $application = new Application($client->getKernel());

        # create a cli application
        self::runDoctrineSchema($application);
        self::runFixMedia($application);
        self::executeFixtures($container);
    }

    public function setUp()
    {
        $this->client = static::createClient(array('test_case' => 'DefaultTestCase'), [
            'PHP_AUTH_USER' => self::TEST_USER,
            'PHP_AUTH_PW' => self::TEST_PASSWORD,
        ]);

        parent::setUp();
    }






//TODO implement this
    public function testMarkTicketIsRead()
    {
        $client = $this->client;
        $client->followRedirects();
        $crawler = $client->request('GET', '/ticket/'.self::TICKET_ID.'/show?_locale=ru');

    }






}
