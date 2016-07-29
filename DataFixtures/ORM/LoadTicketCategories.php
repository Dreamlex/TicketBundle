<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 04.07.16
 * Time: 11:35
 */

namespace Dreamlex\TicketBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Dreamlex\TicketBundle\Entity\Category;
use Gedmo\Translatable\Entity\Translation;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadTicketData
 * @package Dreamlex\Bundle\TicketBundle\DataFixtures\ORM
 */
class LoadTicketCategories extends AbstractFixture implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    /** @var ContainerInterface */
    private $container;

    /**
     * @return int
     */
    public function getOrder()
    {
        return 1;
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->loadCategories($manager);
    }

    /**
     * @param ObjectManager $om
     */
    public function loadCategories(ObjectManager $om)
    {
        $translationRepo = $om->getRepository(Translation::class);
        $categoryTitleRU = ['Общие Вопросы', 'Проблемы с заказом', 'Финансы', 'Стать Магазином', 'Промо-коды'];
        $categoryTitleEN = ['General questions', 'Problems with Order', 'Finance', 'Become A Store', 'Promo Codes'];
        for ($i = 0; $i < 5; $i++) {
            $category = new Category();
            $category->setTitle($categoryTitleRU[$i]);
            $translationRepo->translate($category, 'title', 'en', $categoryTitleEN[$i]);
            $om->persist($category);
            $om->flush();
            $this->addReference('category-'.$i, $category);
        }
    }
}
