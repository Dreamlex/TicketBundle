<?php
/**
 * Created by PhpStorm.
 * User: dreamlex
 * Date: 22.07.16
 * Time: 10:06
 */

namespace Dreamlex\Bundle\TicketBundle\Tests\Controller;

use Dreamlex\Bundle\TicketBundle\Controller\MediaController;
use Dreamlex\Bundle\TicketBundle\Provider\TicketImageProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class TicketMediaControllerTest
 * @package Dreamlex\Bundle\TicketBundle\Tests\Controller
 */
class TicketMediaControllerTest extends WebTestCase
{
    const TEST_USER = 'test-user'; //TODO Сменить ticket-user на test-user
    const TEST_PASSWORD = 'test-user'; //TODO Сменить пароль для test-user
    const TICKET_ID = '6'; //TODO Указать Ticket ID

    /**
     * Тест получение большой картинки
     */
    public function testGetMediaBig()
    {
        $client = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => self::TEST_USER,
                'PHP_AUTH_PW' => self::TEST_PASSWORD,
            ]
        );
        $crawler = $client->request('GET', '/ticket/'.self::TICKET_ID.'/show');
        self::assertTrue($client->getResponse()->isSuccessful());
        $imageUri = $crawler->selectLink('p(32)')->link()->getUri();
        $crawler = $client->request('GET', $imageUri);
        self::assertTrue($client->getResponse()->isSuccessful());

        return $imageUri;
    }

    /**
     * @param string $imageUri
     * @depends testGetMediaBig
     */
    public function testGetMediaBigFail($imageUri)
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $imageUri);
        self::assertTrue($client->getResponse()->isNotFound());
    }

    /**
     * @return mixed|string
     * @depends testGetMediaBig
     */
    public function testGetMediaReference($imageUri)
    {
        $client = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => self::TEST_USER,
                'PHP_AUTH_PW' => self::TEST_PASSWORD,
            ]
        );
        $imageUri = str_replace('ticket_big', 'reference', $imageUri);
        $crawler = $client->request('GET', $imageUri);
        self::assertTrue($client->getResponse()->isSuccessful());

        return $imageUri;
    }

    /**
     * @param string $imageUri
     * @depends testGetMediaReference
     */
    public function testGetMediaReferenceFail($imageUri)
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $imageUri);
        self::assertTrue($client->getResponse()->isNotFound());
    }

    /**
     * @param string $imageUri
     * @depends testGetMediaBig
     */
    public function testGetMediaDownloadUserOwner($imageUri)
    {
        $client = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => self::TEST_USER,
                'PHP_AUTH_PW' => self::TEST_PASSWORD,
            ]
        );
        $imageUri = str_replace('ticket_big', 'download', $imageUri);
        $crawler = $client->request('GET', $imageUri);
        self::assertTrue($client->getResponse()->isSuccessful());

        return $imageUri;
    }

    /**
     * @param string $imageUri
     * @return mixed
     * @depends testGetMediaDownloadUserOwner
     */
    public function testGetMediaDownloadAdmin($imageUri)
    {
        $client = static::createClient(
            [],
            [
                'PHP_AUTH_USER' => 'test-admin',
                'PHP_AUTH_PW' => 'test-admin',
            ]
        );
        $client->request('GET', $imageUri);
        self::assertTrue($client->getResponse()->isSuccessful());

        return $imageUri;
    }/**
     * @param string $imageUri
     * @depends testGetMediaDownloadUserOwner
     */
    public function testGetMediaDownloadAnon($imageUri)
    {
        $client = static::createClient();
        $client->request('GET', $imageUri);
        self::assertTrue($client->getResponse()->isNotFound());

    }
}
