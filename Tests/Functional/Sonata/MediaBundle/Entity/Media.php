<?php

/**
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dreamlex\TicketBundle\Tests\Functional\Sonata\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sonata\MediaBundle\Entity\BaseMedia as BaseMedia;

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
