<?php
/**
 * Created by PhpStorm.
 * User: dreamlex
 * Date: 10.08.16
 * Time: 16:44
 */

namespace Dreamlex\Bundle\TicketBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DomCrawler\Field\ChoiceFormField;
use Symfony\Component\DomCrawler\Field\FileFormField;
use Symfony\Component\DomCrawler\Field\InputFormField;
use Symfony\Component\HttpKernel\Client;

class TicketShowTest extends TicketWebTestCase
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
//TODO implement tests


    /**
     *  Просмотр тикета
     */
    public function testTicketShow()
    {
        $client = static::createClient(array('test_case' => 'DefaultTestCase'), [
            'PHP_AUTH_USER' => self::TEST_USER,
            'PHP_AUTH_PW' => self::TEST_PASSWORD,
        ]);
        $crawler = $client->request('GET', '/ticket/'.self::TICKET_ID.'/show?_locale=ru');
        self::assertTrue($client->getResponse()->isSuccessful());
        self::assertGreaterThan(
            0,
            $crawler->filter(
                'html:contains("заменил ластюзер на противоположный. что будет?)")'
            )->count()
        );
    }
    /**
     * доступ другим юзерам 403
     */
    public function testTicketShowAnotherUserFail()
    {
        $client = static::createClient(array('test_case' => 'DefaultTestCase'), [
            'PHP_AUTH_USER' => 'test-admin',
            'PHP_AUTH_PW' => 'test-admin',
        ]);
        $client->request('GET', '/ticket/'.self::TICKET_ID.'/show?_locale=ru');
        self::assertTrue($client->getResponse()->isForbidden());
    }

    /**
     * доступ анонимным юзерам 401
     */
    public function testTicketShowAnonymousMustLogin()
    {
        $client = static::createClient(['test_case' => 'DefaultTestCase']);
        $client->request('GET', '/ticket/'.self::TICKET_ID.'/show?_locale=ru');
        self::assertEquals(
            401,
            $client->getResponse()->getStatusCode());
    }

    /**
     * добавление текстового ответа, показ флеша
     */
    public function testTicketAddTextAnswer()
    {
        $client = $this->client;
        $client->followRedirects();
        $crawler = $client->request('GET', '/ticket/'.self::TICKET_ID.'/show?_locale=ru');
        $form = $crawler->selectButton('Message[submit]')->form();
        $form['Message[message]'] = 'TEST MESSAGE ANSWER';
        $crawler = $client->submit($form);
        self::assertGreaterThan(0, $crawler->filter('html:contains("Ваш ответ добавлен")')->count());

    }

    /**
     * добавление ответа с картинкой, показ флеша
     */
    public function testTicketAddImageAnswer()
    {
        $client = $this->client;
        $client->followRedirects();
        $crawler = $client->request('GET', '/ticket/'.self::TICKET_ID.'/show?_locale=ru');
        $form = $crawler->selectButton('Message[submit]')->form();
        /** @var  $media FileFormField */
        $media = $form['Message[media][binaryContent]'];
        $media->upload(__DIR__.'/../Images/8.jpg');
        /** @var $priority ChoiceFormField */
        $priority = $form['Message[priority]'];
        $priorityOptions = $priority->availableOptionValues();
        $priority->select($priorityOptions[2]);
        $crawler = $client->submit($form);
        static::assertGreaterThan(0, $crawler->filter('html:contains("Ваш ответ добавлен")')->count());
    }

    /**
     * изменение приоритета/показ флеша
     */
    public function testTicketChangePriority()
    {
        $client = $this->client;
        $client->followRedirects();
        $crawler = $client->request('GET', '/ticket/'.self::TICKET_ID.'/show?_locale=ru');
        $form = $crawler->selectButton('Message[changePriority]')->form();
        /** @var $priority ChoiceFormField */
        $priority = $form['Message[priority]'];
        $priorityOptions = $priority->availableOptionValues();
        $priority->select($priorityOptions[1]);
        $crawler = $client->submit($form);
        static::assertTrue($client->getResponse()->isSuccessful(), 'response status is 2xx');
        static::assertGreaterThan(0, $crawler->filter('html:contains("Приоритет изменен")')->count());
    }

    /**
     * Отображение ошибки при ответе
     */
    public function testTicketReplyErrors()
    {
        $client = $this->client;
        $crawler = $client->request('GET', '/ticket/'.self::TICKET_ID.'/show?_locale=ru');
        $form = $crawler->selectButton('Message[submit]')->form();
        $crawler = $client->submit($form);
        static::assertGreaterThan(
            0,
            $crawler->filter('html:contains("Заполните поле Сообщение или загрузите изображение")')->count()
        );
    }
}
