<?php
/**
 * Created by PhpStorm.
 * User: dreamlex
 * Date: 10.08.16
 * Time: 16:42
 */

namespace Dreamlex\Bundle\TicketBundle\Tests\Functional;

use Symfony\Component\DomCrawler\Field\ChoiceFormField;
use Symfony\Component\DomCrawler\Field\FileFormField;


class TicketCreateTest extends TicketWebTestCase
{
    /**
     *  создания тикета с текстом
     */
    public function testTicketCreateTextOnly()
    {
        $client = $this->client;
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
     * показ, Флеш после создания
     */
    public function testFlashShowAfterCreateTicket()
    {
        $client = $this->client;
        $client->followRedirects();
        $crawler = $client->request('GET', '/ticket/create?_locale=ru');
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

        $crawler = $client->submit($form);
        self::assertGreaterThan(0, $crawler->filter('html:contains("Тикет создан")')->count());
    }

    /**
     * создание тикета с картинкой/показ флеша
     */
    public function testTicketCreateImageOnly()
    {
        $client = $this->client;
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
     * вывод ошибки пустое поле темы при создании тикета
     */
    public function testErrorSubjectCreatingTicket()
    {
        $client = $this->client;
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
        $client = $this->client;
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

    public function testTicketCreateMustLogin()
    {
        $client = static::createClient(['test_case' => 'DefaultTestCase']);
        $client->request('GET', '/ticket/create?_locale=ru');
        self::assertEquals(401, $client->getResponse()->getStatusCode());
    }

}
