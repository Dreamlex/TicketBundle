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
    new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
    new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
    new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
    new \Dreamlex\TicketBundle\DreamlexTicketBundle(),
    new \Symfony\Bundle\TwigBundle\TwigBundle(),
    new \Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
    new \FOS\UserBundle\FOSUserBundle(),
    new \Pix\SortableBehaviorBundle\PixSortableBehaviorBundle(),

    new \Sonata\CoreBundle\SonataCoreBundle(),
    new \Sonata\MediaBundle\SonataMediaBundle(),
    new \Sonata\TranslationBundle\SonataTranslationBundle(),
    new \Sonata\ClassificationBundle\SonataClassificationBundle(),
    new \Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
    new \Dreamlex\TicketBundle\Tests\Functional\Bundle\TestBundle\TestBundle(),
    new \Dreamlex\TicketBundle\Tests\Functional\Bundle\ClassificationBundle\ClassificationBundle(),
    new \Dreamlex\TicketBundle\Tests\Functional\Bundle\MediaBundle\MediaBundle(),
);
