<?php
namespace Dreamlex\Bundle\TicketBundle\Tests\Functional;

use Dreamlex\Bundle\CoreBundle\Test\FixtureAwareTestCaseTrait;
use Dreamlex\Bundle\CoreBundle\Test\WebTestCase;
use Dreamlex\Bundle\TicketBundle\DataFixtures\ORM\LoadTicket;
use Dreamlex\Bundle\TicketBundle\DataFixtures\ORM\LoadTicketCategories;
use Dreamlex\Bundle\TicketBundle\DataFixtures\ORM\LoadTicketUsers;
use Sonata\MediaBundle\Command\FixMediaContextCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DomCrawler\Field\ChoiceFormField;
use Symfony\Component\DomCrawler\Field\FileFormField;
use Symfony\Component\DomCrawler\Field\InputFormField;
use Symfony\Component\HttpKernel\Client;

/**
 * Class TicketControllerTest
 *
 * @package Dreamlex\Bundle\TicketBundle\Tests\Functional
 */
class TicketControllerTest extends WebTestCase
{
    use FixtureAwareTestCaseTrait;

    /** @var  Client */
    protected $client;

    /**  */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        parent::deleteTmpDir('Ticket');

        static::bootKernel(['test_case' => 'Ticket']);
        static::$em = static::$kernel->getContainer()->get('doctrine')->getManager();

        static::prepareSchema();

        static::fixMediaContext();

        static::addFixture(new LoadTicketUsers());
        static::addFixture(new LoadTicketCategories());
        static::addFixture(new LoadTicket());
        static::executeFixtures();
    }

    /**  */
    public static function tearDownAfterClass()
    {
        parent::deleteTmpDir('Ticket');
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

    /**
     *  создания тикета с текстом
     */
    public function testTicketCreateTextOnly()
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', '/ticket/create?_locale=ru');
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

        $crawler = $this->client->submit($form);

        self::assertGreaterThan(0, $crawler->filter('html:contains("Тикет создан")')->count());
    }

    /**
     * показ, Флеш после создания
     */
    public function testFlashShowAfterCreateTicket()
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', '/ticket/create?_locale=ru');
        $form = $crawler->selectButton('Создать тикет')->form();
        self::assertGreaterThan(0, $crawler->filter('html:contains("Создать тикет")')->count());

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

        $crawler = $this->client->submit($form);
        self::assertGreaterThan(0, $crawler->filter('html:contains("Тикет создан")')->count());
    }

    /**
     * создание тикета с картинкой/показ флеша
     */
    public function testTicketCreateImageOnly()
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', '/ticket/create?_locale=ru');
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
        $crawler = $this->client->submit($form);
        static::assertGreaterThan(0, $crawler->filter('html:contains("Тикет создан")')->count());
    }

    /**
     * вывод ошибки пустое поле темы при создании тикета
     */
    public function testErrorSubjectCreatingTicket()
    {
        $crawler = $this->client->request('GET', '/ticket/create?_locale=ru');

        $form = $crawler->selectButton('submit')->form();

        /**  @var $category ChoiceFormField */
        $category = $form['ticket[category]'];
        $categoryOptions = $category->availableOptionValues();
        $category->select($categoryOptions['2']);

        $form['ticket[messages][0][message]'] = 'Lorem Ipsum - це текст-"риба", що використовується в друкарстві та дизайні. Lorem Ipsum є, фактично, стандартною "рибою" аж з XVI сторіччя, коли невідомий друкар взяв шрифтову гранку та склав на ній підбірку зразків шрифтів. "Риба" не тільки успішно пережила п\'ять століть, але й прижилася в електронному верстуванні, залишаючись по суті незмінною. Вона популяризувалась в 60-их роках минулого сторіччя завдяки виданню зразків шрифтів Letraset, які містили уривки з Lorem Ipsum, і вдруге - нещодавно завдяки програмам комп\'ютерного верстування на кшталт Aldus Pagemaker, які використовували різні версії Lorem Ipsum';

        $priority = $form['ticket[messages][0][priority]'];
        /** @var $priority ChoiceFormField */
        $priority->select('high');

        $crawler = $this->client->submit($form);
        self::assertGreaterThan(0, $crawler->filter('html:contains("Поле Тема не может быть пустым")')->count());
    }

    /**
     * вывод ошибки заполните сообщения
     */
    public function testErrorMessageCreatingTicket()
    {
        $crawler = $this->client->request('GET', '/ticket/create?_locale=ru');
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

        $crawler = $this->client->submit($form);
        self::assertGreaterThan(
            0,
            $crawler->filter('html:contains("Заполните поле Сообщение или загрузите изображение")')->count()
        );
    }

    public function testTicketCreateMustLogin()
    {
        $client = static::createClient(['test_case' => 'Ticket']);
        $client->request('GET', '/ticket/create?_locale=ru');
        self::assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * закрытие тикета, показ флеша
     */
    public function testTicketCloseAction()
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', '/ticket/6/show?_locale=ru');
        $form = $crawler->selectButton('Message[closeTicket]')->form();
        $crawler = $this->client->submit($form);
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
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', '/ticket/6/show?_locale=ru');
        static:: assertGreaterThan(
            0,
            $crawler->filter('html:contains("test-user")')->count()
        );
        $form = $crawler->selectButton('Message[submit]')->form();
        $form['Message[message]'] = 'Сообщение что бы открыть тикет';
        $this->client->submit($form);

        $crawler = $this->client->request('GET', '/ticket/6/show?_locale=ru');
        static:: assertSame(
            0,
            $crawler->filter('html:contains("Добавьте сообщение чтобы заново открыть тикет")')->count()
        );
    }

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
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', '/ticket/?_locale=ru');
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
        $crawler = $this->client->submit($form);
        self:: assertGreaterThan(0, $crawler->filter('html:contains("Скрин без текста")')->count());
        self:: assertGreaterThan(0, $crawler->filter('html:contains("Клиент не берет трейд")')->count());
        self:: assertNotContains('Хочу вывести средства', $this->client->getResponse()->getContent());
    }

    /**
     * фильтр статусов
     */
    public function testTicketStatusFilters()
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', '/ticket/?_locale=ru');
        $form = $crawler->filter('form')->form();
        /** @var ChoiceFormField $status */
        $status = $form['grid_ticket[status][from]'];
        $statusOptions = $status->availableOptionValues();
        $status->select($statusOptions[2]);
        $crawler = $this->client->submit($form);
        self:: assertGreaterThan(0, $crawler->filter('html:contains("Клиент не берет трейд")')->count());
        self:: assertGreaterThan(0, $crawler->filter('html:contains("Хочу вывести средства")')->count());
        self:: assertGreaterThan(0, $crawler->filter('html:contains("Бот для игры")')->count());

        self:: assertNotContains('Подключите мой магазин', $this->client->getResponse()->getContent());
        self:: assertNotContains('Изменение нумерации сообщений', $this->client->getResponse()->getContent());
    }

    /**
     * фильтр приоритетов
     */
    public function testTicketPriorityFilters()
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', '/ticket/?_locale=ru');
        $form = $crawler->filter('form')->form();
        /** @var ChoiceFormField $priority */
        $priority = $form['grid_ticket[priority][from]'];
        $priorityOptions = $priority->availableOptionValues();
        $priority->select($priorityOptions[1]);
        $crawler = $this->client->submit($form);
        self:: assertGreaterThan(0, $crawler->filter('html:contains("Скрин без текста")')->count());
        self:: assertGreaterThan(0, $crawler->filter('html:contains("Хочу рекламировать Вас!")')->count());
        self:: assertGreaterThan(0, $crawler->filter('html:contains("Бот для игры")')->count());
        self:: assertNotContains('ТЕстирование с изменением изрид на 1', $this->client->getResponse()->getContent());
        self:: assertNotContains('Изменение нумерации сообщений', $this->client->getResponse()->getContent());
    }

    /**
     * фильтр даты
     */
    public function testTicketDateFilters()
    {
        $this->client->followRedirects();
        $dateValueFrom = new \DateTime('today');
        $dateValueFrom = $dateValueFrom->format('Y-m-d H:i:s');
        $dateValueTo = new \DateTime('tomorrow');
        $dateValueTo = $dateValueTo->format('Y-m-d H:i:s');
        $crawler = $this->client->request('GET', '/ticket/?_locale=ru');
        $form = $crawler->filter('form')->form();
        $dateFrom = $form['grid_ticket[createdAt][from]'];
        $dateTo = $form['grid_ticket[createdAt][to]'];
        /** @var InputFormField $dateFrom */
        $dateFrom->setValue($dateValueFrom);
        /** @var InputFormField $dateTo */
        $dateTo->setValue($dateValueTo);
        $this->client->submit($form);
        self::assertContains('Скрин без текста', $this->client->getResponse()->getContent());
        self::assertContains('Замена ластюзер', $this->client->getResponse()->getContent());
        self::assertContains('Хочу рекламировать Вас!', $this->client->getResponse()->getContent());
        self:: assertNotContains('Подключите мой магазин', $this->client->getResponse()->getContent());
        self:: assertNotContains('Хочу вывести средства', $this->client->getResponse()->getContent());
    }

    /**
     *  запрет доступа к тикетам анонимным юзерам
     */
    public function testTicketShowAnonymousRedirect()
    {
        $client = static::createClient(['test_case' => 'Ticket']);
        $client->request('GET', '/ticket/?_locale=ru');
        self::assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * Картинки тестирование
     *
     * @return string
     */
    public function testGetMediaBig()
    {
        $crawler = $this->client->request('GET', '/ticket/6/show');
        self::assertTrue($this->client->getResponse()->isSuccessful());
        $imageUri = $crawler->selectLink('32')->link()->getUri();
        $this->client->request('GET', $imageUri);
        self::assertTrue($this->client->getResponse()->isSuccessful());

        return $imageUri;
    }

    /**
     * @param string $imageUri
     *
     * @depends testGetMediaBig
     */
    public function testGetMediaBigFail($imageUri)
    {
        $client = static::createClient(['test_case' => 'Ticket']);
        $client->request('GET', $imageUri);
        self::assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * @param $imageUri
     *
     * @return mixed|string
     *
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
     *
     * @depends testGetMediaReference
     */
    public function testGetMediaReferenceFail($imageUri)
    {
        $client = static::createClient(['test_case' => 'Ticket']);
        $client->request('GET', $imageUri);
        self::assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * @param string $imageUri
     *
     * @depends testGetMediaBig
     *
     * @return mixed|string
     */
    public function testGetMediaDownloadUserOwner($imageUri)
    {
        $imageUri = str_replace('ticket_big', 'download', $imageUri);
        $this->client->request('GET', $imageUri);
        self::assertTrue($this->client->getResponse()->isSuccessful());

        return $imageUri;
    }

    /**
     * @param string $imageUri
     *
     * @return mixed
     * @depends testGetMediaDownloadUserOwner
     */
    public function testGetMediaDownloadAnotherUserFail($imageUri)
    {
        $client = static::createClient(['test_case' => 'Ticket'], [
            'PHP_AUTH_USER' => 'test-admin',
            'PHP_AUTH_PW' => 'test-admin',
        ]);
        $client->request('GET', $imageUri);
        self::assertTrue($client->getResponse()->isNotFound());

        return $imageUri;
    }

    /**
     * @param string $imageUri
     *
     * @depends testGetMediaDownloadUserOwner
     */
    public function testGetMediaDownloadAnon($imageUri)
    {
        $client = static::createClient(['test_case' => 'Ticket']);
        $client->request('GET', $imageUri);
        self::assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     *  Просмотр тикета
     */
    public function testTicketShow()
    {
        $crawler = $this->client->request('GET', '/ticket/6/show?_locale=ru');
        self::assertTrue($this->client->getResponse()->isSuccessful());
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
        $client = static::createClient(['test_case' => 'Ticket'], [
            'PHP_AUTH_USER' => 'test-admin',
            'PHP_AUTH_PW' => 'test-admin',
        ]);

        $client->request('GET', '/ticket/6/show?_locale=ru');
        self::assertTrue($client->getResponse()->isForbidden());
    }

    /**
     * доступ анонимным юзерам 401
     */
    public function testTicketShowAnonymousMustLogin()
    {
        $client = static::createClient(['test_case' => 'Ticket']);

        $client->request('GET', '/ticket/6/show?_locale=ru');
        self::assertEquals(401, $client->getResponse()->getStatusCode());
    }

    /**
     * добавление текстового ответа, показ флеша
     */
    public function testTicketAddTextAnswer()
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', '/ticket/6/show?_locale=ru');
        $form = $crawler->selectButton('Message[submit]')->form();
        $form['Message[message]'] = 'TEST MESSAGE ANSWER';
        $crawler = $this->client->submit($form);
        self::assertGreaterThan(0, $crawler->filter('html:contains("Ваш ответ добавлен")')->count());

    }

    /**
     * добавление ответа с картинкой, показ флеша
     */
    public function testTicketAddImageAnswer()
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', '/ticket/6/show?_locale=ru');
        $form = $crawler->selectButton('Message[submit]')->form();
        /** @var  $media FileFormField */
        $media = $form['Message[media][binaryContent]'];
        $media->upload(__DIR__.'/../Images/8.jpg');
        /** @var $priority ChoiceFormField */
        $priority = $form['Message[priority]'];
        $priorityOptions = $priority->availableOptionValues();
        $priority->select($priorityOptions[2]);
        $crawler = $this->client->submit($form);
        static::assertGreaterThan(0, $crawler->filter('html:contains("Ваш ответ добавлен")')->count());
    }

    /**
     * изменение приоритета/показ флеша
     */
    public function testTicketChangePriority()
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', '/ticket/6/show?_locale=ru');
        $form = $crawler->selectButton('Message[changePriority]')->form();
        /** @var $priority ChoiceFormField */
        $priority = $form['Message[priority]'];
        $priorityOptions = $priority->availableOptionValues();
        $priority->select($priorityOptions[1]);
        $crawler = $this->client->submit($form);
        static::assertTrue($this->client->getResponse()->isSuccessful(), 'response status is 2xx');
        static::assertGreaterThan(0, $crawler->filter('html:contains("Приоритет изменен")')->count());
    }

    /**
     * Отображение ошибки при ответе
     */
    public function testTicketReplyErrors()
    {
        $crawler = $this->client->request('GET', '/ticket/6/show?_locale=ru');
        $form = $crawler->selectButton('Message[submit]')->form();
        $crawler = $this->client->submit($form);
        static::assertGreaterThan(
            0,
            $crawler->filter('html:contains("Заполните поле Сообщение или загрузите изображение")')->count()
        );
    }

    protected static function getKernelClass()
    {
        require_once __DIR__.'/app/AppKernel.php';

        return 'Dreamlex\Bundle\TicketBundle\Tests\Functional\app\AppKernel';
    }

    protected static function fixMediaContext()
    {
        $application = new Application(static::$kernel);
        $application->add(new FixMediaContextCommand());

        $command = $application->find('sonata:media:fix-media-context');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
        ]);
    }
}
