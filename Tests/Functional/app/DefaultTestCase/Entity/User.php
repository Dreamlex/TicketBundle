<?php
/**
 * Created by PhpStorm.
 * User: dreamlex
 * Date: 05.08.16
 * Time: 9:35
 */

namespace Dreamlex\TicketBundle\Tests\Functional\app\DefaultTestCase\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dreamlex\TicketBundle\Entity\Traits\UserTrait;
use Sonata\UserBundle\Entity\BaseUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="user") <yourname> <youremail>
 */
class User extends BaseUser
{
    use UserTrait;
    /**
     * @var
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO") integer $id
     */
    protected $id;


}