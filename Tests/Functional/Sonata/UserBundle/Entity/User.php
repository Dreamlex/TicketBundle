<?php

namespace Dreamlex\Bundle\TicketBundle\Tests\Functional\Sonata\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dreamlex\Bundle\NewsBundle\Entity\News;
use Dreamlex\Bundle\TicketBundle\Entity\Ticket;
use SellMMO\Bundle\ProviderBundle\Entity\Offer;
use SellMMO\Bundle\StoreBundle\Entity\Order;
use SellMMO\Bundle\StoreBundle\Entity\OrderPart;
use SellMMO\Sonata\MediaBundle\Entity\Media;
use Sonata\UserBundle\Entity\BaseUser as BaseUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * @var
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO") integer $id
     */
    protected $id;

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }
}
