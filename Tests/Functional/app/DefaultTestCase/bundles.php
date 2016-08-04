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
    new \Sonata\MediaBundle\SonataMediaBundle(),
    new \Symfony\Bundle\TwigBundle\TwigBundle(),

//    new \FOS\UserBundle\FOSUserBundle(),
    new \Pix\SortableBehaviorBundle\PixSortableBehaviorBundle(),
);
