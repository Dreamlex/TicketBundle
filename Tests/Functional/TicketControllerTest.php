<?php

namespace Dreamlex\TicketBundle\Tests\Functional;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Dreamlex\TicketBundle\DataFixtures\ORM\LoadTicket;
use Dreamlex\TicketBundle\DataFixtures\ORM\LoadTicketCategories;
use Dreamlex\TicketBundle\DataFixtures\ORM\LoadTicketUsers;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DomCrawler\Field\ChoiceFormField;
use Symfony\Component\DomCrawler\Field\FileFormField;
use Symfony\Component\DomCrawler\Field\InputFormField;
use Symfony\Component\HttpKernel\Client;

/**
 * TicketTestController
 */
class TicketControllerTest extends FunctionalWebTestCase
{
    /** @var  Client */
    protected $client;
    //TODO Всё перепроверить
    //TODO Указать даты
    const TEST_USER = 'test-user';
    const TEST_PASSWORD = 'test-user';
    const TICKET_ID = '6';
    const DATE_FROM = '2016-07-28 00:00:00';
    const DATE_TO = '2016-07-28 23:59:59';

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->client = static::createClient(array('test_case' => 'DefaultTestCase'), [
            'PHP_AUTH_USER' => self::TEST_USER,
            'PHP_AUTH_PW' => self::TEST_PASSWORD,
        ]);
        parent::setUp();



        $application = new Application(static::$kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'doctrine:schema:update', '--dump-sql' => true, '--force' => true, '--complete' => true
        ));

        $output = new BufferedOutput();
        $application->run($input, $output);

        $application = new Application(static::$kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'sonata:media:fix-media-context', '-vvv' => true
        ));



        $output = new BufferedOutput();
        $application->run($input, $output);


        $loader = new ContainerAwareLoader($this->container);
        $loader->addFixture(new LoadTicketUsers());
        $loader->addFixture(new LoadTicketCategories());
        $loader->addFixture(new LoadTicket());

        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->execute($loader->getFixtures(), true);
    }


    /**
     * Вывод списка тикетов
     */
    public function testTicketList()
    {
        $crawler = $this->client->request('GET', '/ticket/?_locale=ru');
        print_r($this->client->getResponse()->getContent());
        static::assertGreaterThan(0, $crawler->filter('html:contains("Список тикетов")')->count());
    }


    /**
     *  создания тикета с текстом
     */
    public function testTicketCreateTextOnly()
    {
        $client = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => self::TEST_USER,
                'PHP_AUTH_PW' => self::TEST_PASSWORD,
            ]
        );
        $client->followRedirects();
        $crawler = $client->request('GET', '/ticket/create?_locale=ru');
        $form = $crawler->selectButton('submit')->form();

        /**  @var $category ChoiceFormField */
        $category = $form['ticket[category]'];
        $categoryOptions = $category->availableOptionValues();
        $category->select($categoryOptions[4]);

        $form['ticket[subject]'] = 'Testing Subject';
        $form['ticket[messages][0][message]'] = 'Lorem Ipsum - це текст-"риба", що використовується в друкарстві та дизайні. Lorem Ipsum є, фактично, стандартною "рибою" аж з XVI сторіччя, коли невідомий друкар взяв шрифтову гранку та склав на ній підбірку зразків шрифтів. "Риба" не тільки успішно пережила п\'ять століть, але й прижилася в електронному верстуванні, залишаючись по суті незмінною. Вона популяризувалась в 60-их роках минулого сторіччя завдяки виданню зразків шрифтів Letraset, які містили уривки з Lorem Ipsum, і вдруге - нещодавно завдяки програмам комп\'ютерного верстування на кшталт Aldus Pagemaker, які використовували різні версії Lorem Ipsum';

        /** @var $priority ChoiceFormField */
        $priority = $form['ticket[messages][0][priority]'];
        $priorityOptions = $priority->availableOptionValues();
        $priority->select($priorityOptions[0]);

        $crawler = $client->submit($form);

        self::assertGreaterThan(0, $crawler->filter('html:contains("Тикет создан")')->count());
    }

    /**
     * Флеш после создания
     */
    public function testFlashShowAfterCreateTicket()
    {
        $client = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => self::TEST_USER,
                'PHP_AUTH_PW' => self::TEST_PASSWORD,
            ]
        );
        $client->followRedirects();
        $crawler = $client->request('GET', '/ticket/create?_locale=ru');
        $form = $crawler->selectButton('Создать тикет')->form();

        /**  @var $category ChoiceFormField */
        $category = $form['ticket[category]'];
        $categoryOptions = $category->availableOptionValues();
        $category->select($categoryOptions[4]);

        $form['ticket[subject]'] = 'Flash testing';
        $form['ticket[messages][0][message]'] = 'Lorem Ipsum - це текст-"риба", що використовується в друкарстві та дизайні. Lorem Ipsum є, фактично, стандартною "рибою" аж з XVI сторіччя, коли невідомий друкар взяв шрифтову гранку та склав на ній підбірку зразків шрифтів. "Риба" не тільки успішно пережила п\'ять століть, але й прижилася в електронному верстуванні, залишаючись по суті незмінною. Вона популяризувалась в 60-их роках минулого сторіччя завдяки виданню зразків шрифтів Letraset, які містили уривки з Lorem Ipsum, і вдруге - нещодавно завдяки програмам комп\'ютерного верстування на кшталт Aldus Pagemaker, які використовували різні версії Lorem Ipsum';

        /** @var $priority ChoiceFormField */
        $priority = $form['ticket[messages][0][priority]'];
        $priorityOptions = $priority->availableOptionValues();
        $priority->select($priorityOptions[1]);

        $crawler = $client->submit($form);
        self::assertGreaterThan(0, $crawler->filter('html:contains("Тикет создан")')->count());
    }

    /**
     * создание тикета с картинкой
     */
    public function testTicketCreateImageOnly()
    {
        $client = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => self::TEST_USER,
                'PHP_AUTH_PW' => self::TEST_PASSWORD,
            ]
        );
        $client->followRedirects();
        $crawler = $client->request('GET', '/ticket/create?_locale=ru');
        $form = $crawler->selectButton('submit')->form();
        /**  @var $category ChoiceFormField */
        $category = $form['ticket[category]'];
        $categoryOptions = $category->availableOptionValues();
        $category->select($categoryOptions['2']);

        $form['ticket[subject]'] = 'Ticket with image';

        /** @var  $media FileFormField */
        $media = $form['ticket[messages][0][media][binaryContent]'];
        $media->upload(__DIR__.'/../Images/8.jpg');
        /** @var $priority ChoiceFormField */
        $priority = $form['ticket[messages][0][priority]'];
        $priorityOptions = $priority->availableOptionValues();
        $priority->select($priorityOptions[0]);
        $crawler = $client->submit($form);
        static::assertGreaterThan(0, $crawler->filter('html:contains("Тикет создан")')->count());
    }

    /**
     *  Просмотр тикета
     */
    public function testTicketShow()
    {
        $client = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => self::TEST_USER,
                'PHP_AUTH_PW' => self::TEST_PASSWORD,
            ]
        );
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
     * Voter test
     */
    public function testTicketShowAnotherUserFail()
    {
        $client = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => 'test-admin',
                'PHP_AUTH_PW' => 'test-admin',
            ]
        );
        $client->request('GET', '/ticket/'.self::TICKET_ID.'/show?_locale=ru');
        self::assertTrue($client->getResponse()->isForbidden());
        self::assertContains('Access Denied.', $client->getResponse()->getContent());
    }

    /**
     * voter test
     */
    public function testTicketShowAnonymousRedirect()
    {
        $client = static::createClient();
        $client->request('GET', '/ticket/'.self::TICKET_ID.'/show?_locale=ru');
        self::assertEquals(
            302,
            $client->getResponse()->getStatusCode());
    }

    /**
     * вывод ошибки пустое поле темы при создании тикета
     */
    public function testErrorSubjectCreatingTicket()
    {
        $client = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => self::TEST_USER,
                'PHP_AUTH_PW' => self::TEST_PASSWORD,
            ]
        );
        $client->followRedirects();
        $crawler = $client->request('GET', '/ticket/create?_locale=ru');
        $form = $crawler->selectButton('submit')->form();

        /**  @var $category ChoiceFormField */
        $category = $form['ticket[category]'];
        $categoryOptions = $category->availableOptionValues();
        $category->select($categoryOptions['2']);

        $form['ticket[messages][0][message]'] = 'Lorem Ipsum - це текст-"риба", що використовується в друкарстві та дизайні. Lorem Ipsum є, фактично, стандартною "рибою" аж з XVI сторіччя, коли невідомий друкар взяв шрифтову гранку та склав на ній підбірку зразків шрифтів. "Риба" не тільки успішно пережила п\'ять століть, але й прижилася в електронному верстуванні, залишаючись по суті незмінною. Вона популяризувалась в 60-их роках минулого сторіччя завдяки виданню зразків шрифтів Letraset, які містили уривки з Lorem Ipsum, і вдруге - нещодавно завдяки програмам комп\'ютерного верстування на кшталт Aldus Pagemaker, які використовували різні версії Lorem Ipsum';

        $priority = $form['ticket[messages][0][priority]'];
        /** @var $priority ChoiceFormField */
        $priority->select('high');

        $crawler = $client->submit($form);
        self::assertGreaterThan(0, $crawler->filter('html:contains("Поле Тема не может быть пустым")')->count());
    }

    /**
     * вывод ошибки заполните сообщения
     */
    public function testErrorMessageCreatingTicket()
    {
        $client = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => self::TEST_USER,
                'PHP_AUTH_PW' => self::TEST_PASSWORD,
            ]
        );
        $crawler = $client->request('GET', '/ticket/create?_locale=ru');
        $form = $crawler->selectButton('submit')->form();

        /**  @var $category ChoiceFormField */
        $category = $form['ticket[category]'];
        $categoryOptions = $category->availableOptionValues();
        $category->select($categoryOptions['2']);
        $form['ticket[subject]'] = 'Subject with 3 category';

        /** @var $priority ChoiceFormField */
        $priority = $form['ticket[messages][0][priority]'];
        $priorityOptions = $priority->availableOptionValues();
        $priority->select($priorityOptions[0]);

        $crawler = $client->submit($form);
        self::assertGreaterThan(
            0,
            $crawler->filter('html:contains("Заполните поле Сообщение или загрузите изображение")')->count()
        );
    }

    /**
     * добавление текстового ответа
     */
    public function testTicketAddTextAnswer()
    {
        $client = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => self::TEST_USER,
                'PHP_AUTH_PW' => self::TEST_PASSWORD,
            ]
        );
        $client->followRedirects();
        $crawler = $client->request('GET', '/ticket/'.self::TICKET_ID.'/show?_locale=ru');
        $form = $crawler->selectButton('Message[submit]')->form();
        $form['Message[message]'] = 'TEST MESSAGE ANSWER';
        $crawler = $client->submit($form);
        self::assertGreaterThan(0, $crawler->filter('html:contains("Ваш ответ добавлен")'));

    }

    public function testMarkTicketIsRead()
    {
        $client = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => self::TEST_USER,
                'PHP_AUTH_PW' => self::TEST_PASSWORD,
            ]
        );
        $client->followRedirects();
        $crawler = $client->request('GET', '/ticket/'.self::TICKET_ID.'/show?_locale=ru');

    }

    /**
     * добавление ответа с картинкой
     */
    public function testTicketAddImageAnswer()
    {
        $client = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => self::TEST_USER,
                'PHP_AUTH_PW' => self::TEST_PASSWORD,
            ]
        );
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
        static::assertGreaterThan(0, $crawler->filter('html:contains("Ваш ответ добавлен")'));
    }

    /**
     * изменение приоритета/показ флеша
     */
    public function testTicketChangePriority()
    {
        $client = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => self::TEST_USER,
                'PHP_AUTH_PW' => self::TEST_PASSWORD,
            ]
        );
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
        $client = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => self::TEST_USER,
                'PHP_AUTH_PW' => self::TEST_PASSWORD,
            ]
        );
        $client->followRedirects();
        $crawler = $client->request('GET', '/ticket/'.self::TICKET_ID.'/show?_locale=ru');
        $form = $crawler->selectButton('Message[submit]')->form();
        $crawler = $client->submit($form);
        static::assertGreaterThan(
            0,
            $crawler->filter('html:contains("Заполните поле Сообщение или загрузите изображение ")')->count()
        );
    }

    /**
     * закрытие тикета, показ флеша
     */
    public function testTicketCloseAction()
    {
        $client = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => self::TEST_USER,
                'PHP_AUTH_PW' => self::TEST_PASSWORD,
            ]
        );
        $client->followRedirects();
        $crawler = $client->request('GET', '/ticket/'.self::TICKET_ID.'/show?_locale=ru');
        $form = $crawler->selectButton('Message[closeTicket]')->form();
        $crawler = $client->submit($form);
        static::assertGreaterThan(
            0,
            $crawler->filter('html:contains("Тикет закрыт")')->count()
        );
    }

    /**
     * Показ сообщения как открыть тикет, открытие тикета
     */
    public function testTicketReOpen()
    {
        $client = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => self::TEST_USER,
                'PHP_AUTH_PW' => self::TEST_PASSWORD,
            ]
        );
        $client->followRedirects();
        $crawler = $client->request('GET', '/ticket/'.self::TICKET_ID.'/show?_locale=ru');
        static:: assertGreaterThan(
            0,
            $crawler->filter('html:contains("test-user")')->count()
        );
        $form = $crawler->selectButton('Message[submit]')->form();
        $form['Message[message]'] = 'Сообщение что бы открыть тикет';
        $crawler = $client->submit($form);

        $crawler = $client->request('GET', '/ticket/'.self::TICKET_ID.'/show?_locale=ru');
        static:: assertSame(
            0,
            $crawler->filter('html:contains("Добавьте сообщение чтобы заново открыть тикет")')->count()
        );
    }

    /**
     * Фильтры категорий //TODO Доделать
     */
    public function testTicketCategoryFilters()
    {
        $client = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => self::TEST_USER,
                'PHP_AUTH_PW' => self::TEST_PASSWORD,
            ]
        );
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
        $client = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => self::TEST_USER,
                'PHP_AUTH_PW' => self::TEST_PASSWORD,
            ]
        );
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
        $client = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => self::TEST_USER,
                'PHP_AUTH_PW' => self::TEST_PASSWORD,
            ]
        );
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
        $client = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => self::TEST_USER,
                'PHP_AUTH_PW' => self::TEST_PASSWORD,
            ]
        );
        $client->followRedirects();
        $crawler = $client->request('GET', '/ticket/?_locale=ru');
        $form = $crawler->filter('form')->form();
        $dateFrom = $form['grid_ticket[createdAt][from]'];
        $dateTo = $form['grid_ticket[createdAt][to]'];
        /** @var InputFormField $dateFrom */
        $dateFrom->setValue(self::DATE_FROM);
        /** @var InputFormField $dateTo */
        $dateTo->setValue(self::DATE_TO);
        $crawler = $client->submit($form);
        self::assertContains('Скрин без текста', $client->getResponse()->getContent());
        self::assertContains('Замена ластюзер', $client->getResponse()->getContent());
        self::assertContains('Хочу рекламировать Вас!', $client->getResponse()->getContent());
        self:: assertNotContains('Подключите мой магазин', $client->getResponse()->getContent());
        self:: assertNotContains('Хочу вывести средства', $client->getResponse()->getContent());
    }
}
