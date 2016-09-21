<?php
/**
 * Created by PhpStorm.
 * User: dreamlex
 * Date: 01.08.16
 * Time: 11:21
 */

namespace Dreamlex\Bundle\TicketBundle\Entity\Traits;


trait MediaTrait
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
