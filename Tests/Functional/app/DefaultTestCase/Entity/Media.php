<?php
/**
 * Created by PhpStorm.
 * User: dreamlex
 * Date: 05.08.16
 * Time: 9:32
 */

namespace Dreamlex\TicketBundle\Tests\Functional\app\DefaultTestCase\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sonata\MediaBundle\Entity\BaseMedia;

/**
 * @ORM\Entity
 * @ORM\Table(name="media__media") <yourname> <youremail>
 */
class Media extends BaseMedia
{
    /**
     * @var
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO") int $id
     */
    protected $id;

    /**
     * Get id
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }
}