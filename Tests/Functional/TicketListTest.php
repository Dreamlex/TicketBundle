<?php
/**
 * Created by PhpStorm.
 * User: dreamlex
 * Date: 10.08.16
 * Time: 16:40
 */

namespace Dreamlex\Bundle\TicketBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DomCrawler\Field\ChoiceFormField;
use Symfony\Component\DomCrawler\Field\FileFormField;
use Symfony\Component\DomCrawler\Field\InputFormField;
use Symfony\Component\HttpKernel\Client;

class TicketListTest extends TicketWebTestCase
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
     * Показ
     */
    public function testTicketList()
    {
        $crawler = $this->client->request('GET', '/ticket/?_locale=ru');
        static::assertGreaterThan(0, $crawler->filter('html:contains("Список тикетов")')->count());
    }

    //Фильтры

    /**
     * фильтр категорий
     */
    public function testTicketCategoryFilters()
    {
        $client = $this->client;
        $client->followRedirects();
        $crawler = $client->request('GET', '/ticket/?_locale=ru');
        $form = $crawler->filter('form')->form();
        /** @var ChoiceFormField $category */
        $category = $form['grid_ticket[category.title][from]'];
        $categoryOptions = $category->availableOptionValues();
        $category->select(
            [
                $categoryOptions[1],
                $categoryOptions[2],
            ]
        );
        $crawler = $client->submit($form);
        self:: assertGreaterThan(0, $crawler->filter('html:contains("Скрин без текста")')->count());
        self:: assertGreaterThan(0, $crawler->filter('html:contains("Клиент не берет трейд")')->count());
        self:: assertNotContains('Хочу вывести средства', $client->getResponse()->getContent());
    }

    /**
     * фильтр статусов
     */
    public function testTicketStatusFilters()
    {
        $client = $this->client;
        $client->followRedirects();
        $crawler = $client->request('GET', '/ticket/?_locale=ru');
        $form = $crawler->filter('form')->form();
        /** @var ChoiceFormField $status */
        $status = $form['grid_ticket[status][from]'];
        $statusOptions = $status->availableOptionValues();
        $status->select($statusOptions[2]);
        $crawler = $client->submit($form);
        self:: assertGreaterThan(0, $crawler->filter('html:contains("Клиент не берет трейд")')->count());
        self:: assertGreaterThan(0, $crawler->filter('html:contains("Хочу вывести средства")')->count());
        self:: assertGreaterThan(0, $crawler->filter('html:contains("Бот для игры")')->count());

        self:: assertNotContains('Подключите мой магазин', $client->getResponse()->getContent());
        self:: assertNotContains('Изменение нумерации сообщений', $client->getResponse()->getContent());
    }

    /**
     * фильтр приоритетов
     */
    public function testTicketPriorityFilters()
    {
        $client = $this->client;
        $client->followRedirects();
        $crawler = $client->request('GET', '/ticket/?_locale=ru');
        $form = $crawler->filter('form')->form();
        /** @var ChoiceFormField $priority */
        $priority = $form['grid_ticket[priority][from]'];
        $priorityOptions = $priority->availableOptionValues();
        $priority->select($priorityOptions[1]);
        $crawler = $client->submit($form);
        self:: assertGreaterThan(0, $crawler->filter('html:contains("Скрин без текста")')->count());
        self:: assertGreaterThan(0, $crawler->filter('html:contains("Хочу рекламировать Вас!")')->count());
        self:: assertGreaterThan(0, $crawler->filter('html:contains("Бот для игры")')->count());
        self:: assertNotContains('ТЕстирование с изменением изрид на 1', $client->getResponse()->getContent());
        self:: assertNotContains('Изменение нумерации сообщений', $client->getResponse()->getContent());
    }

    /**
     * фильтр даты
     */
    public function testTicketDateFilters()
    {
        $client = $this->client;
        $client->followRedirects();
        $dateValueFrom = new \DateTime('today');
        $dateValueFrom = $dateValueFrom->format('Y-m-d H:i:s');
        $dateValueTo = new \DateTime('tomorrow');
        $dateValueTo = $dateValueTo->format('Y-m-d H:i:s');
        $crawler = $client->request('GET', '/ticket/?_locale=ru');
        $form = $crawler->filter('form')->form();
        $dateFrom = $form['grid_ticket[createdAt][from]'];
        $dateTo = $form['grid_ticket[createdAt][to]'];
        /** @var InputFormField $dateFrom */
        $dateFrom->setValue($dateValueFrom);
        /** @var InputFormField $dateTo */
        $dateTo->setValue($dateValueTo);
        $crawler = $client->submit($form);
        self::assertContains('Скрин без текста', $client->getResponse()->getContent());
        self::assertContains('Замена ластюзер', $client->getResponse()->getContent());
        self::assertContains('Хочу рекламировать Вас!', $client->getResponse()->getContent());
        self:: assertNotContains('Подключите мой магазин', $client->getResponse()->getContent());
        self:: assertNotContains('Хочу вывести средства', $client->getResponse()->getContent());
    }

    /**
     *  запрет доступа к тикетам анонимным юзерам
     */
    public function testTicketShowAnonymousRedirect()
    {
        $client = static::createClient(['test_case' => 'DefaultTestCase']);
        $client->request('GET', '/ticket/?_locale=ru');
        self::assertEquals(
            401,
            $client->getResponse()->getStatusCode());
    }


    //Картинки тестирование


    public function testGetMediaBig()
    {
        $crawler = $this->client->request('GET', '/ticket/'.self::TICKET_ID.'/show');
        self::assertTrue($this->client->getResponse()->isSuccessful());
        $imageUri = $crawler->selectLink('p(32)')->link()->getUri();
        $crawler = $this->client->request('GET', $imageUri);
        self::assertTrue($this->client->getResponse()->isSuccessful());

        return $imageUri;
    }

    /**
     * @param string $imageUri
     * @depends testGetMediaBig
     */
    public function testGetMediaBigFail($imageUri)
    {
        $client = static::createClient(['test_case' => 'DefaultTestCase']);
        $client->request('GET', $imageUri);
        self::assertEquals(401,$client->getResponse()->getStatusCode());
    }

    /**
     * @return mixed|string
     * @depends testGetMediaBig
     */
    public function testGetMediaReference($imageUri)
    {
        $imageUri = str_replace('ticket_big', 'reference', $imageUri);
        $this->client->request('GET', $imageUri);
        self::assertTrue($this->client->getResponse()->isSuccessful());

        return $imageUri;
    }

    /**
     * @param string $imageUri
     * @depends testGetMediaReference
     */
    public function testGetMediaReferenceFail($imageUri)
    {
        $client = static::createClient(['test_case' => 'DefaultTestCase']);
        $client->request('GET', $imageUri);
        self::assertEquals(401,$client->getResponse()->getStatusCode());
    }

    /**
     * @param string $imageUri
     * @depends testGetMediaBig
     */
    public function testGetMediaDownloadUserOwner($imageUri)
    {
        $imageUri = str_replace('ticket_big', 'download', $imageUri);
        $crawler = $this->client->request('GET', $imageUri);
        self::assertTrue($this->client->getResponse()->isSuccessful());

        return $imageUri;
    }

    /**
     * @param string $imageUri
     * @return mixed
     * @depends testGetMediaDownloadUserOwner
     */
    public function testGetMediaDownloadAnotherUserFail($imageUri)
    {
        $client = static::createClient(
            ['test_case' => 'DefaultTestCase'],
            [
                'PHP_AUTH_USER' => 'test-admin',
                'PHP_AUTH_PW' => 'test-admin',
            ]
        );
        $client->request('GET', $imageUri);
        self::assertTrue($client->getResponse()->isNotFound());

        return $imageUri;
    }

    /**
     * @param string $imageUri
     * @depends testGetMediaDownloadUserOwner
     */
    public function testGetMediaDownloadAnon($imageUri)
    {
        $client = static::createClient(['test_case' => 'DefaultTestCase']);
        $client->request('GET', $imageUri);
        self::assertEquals(401,$client->getResponse()->getStatusCode());

    }
}
