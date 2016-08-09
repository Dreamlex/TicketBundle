<?php

/*
 * This file is part of the FOSRestBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return array(
    new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
    new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
    new \Symfony\Bundle\TwigBundle\TwigBundle(),
    new \Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
    new \Symfony\Bundle\MonologBundle\MonologBundle(),
    new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
    new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
    new \Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
    new \FOS\UserBundle\FOSUserBundle(),
    new Knp\Bundle\MenuBundle\KnpMenuBundle(),
    new \Pix\SortableBehaviorBundle\PixSortableBehaviorBundle(),
    new \Sonata\CoreBundle\SonataCoreBundle(),
    new \Sonata\MediaBundle\SonataMediaBundle(),
    new \Sonata\TranslationBundle\SonataTranslationBundle(),
    new \Sonata\ClassificationBundle\SonataClassificationBundle(),
    new \Sonata\UserBundle\SonataUserBundle(),
    new \Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
    new \Dreamlex\TicketBundle\DreamlexTicketBundle(),
    new \Dreamlex\TicketBundle\Tests\Functional\Sonata\ClassificationBundle\DreamlexSonataClassificationBundle(),
    new \Dreamlex\TicketBundle\Tests\Functional\Sonata\MediaBundle\DreamlexSonataMediaBundle(),
    new \Dreamlex\TicketBundle\Tests\Functional\Sonata\UserBundle\DreamlexSonataUserBundle(),
);
