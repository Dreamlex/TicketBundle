Dreamlex Ticket Bundle
=======================

Ticket bundle for symfony 

Setting Bundle
=====
##Setting media entity
Extend sonata BaseMedia and use MediaTrait
```php
<?php

namespace YourProjectNamespace\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sonata\MediaBundle\Entity\BaseMedia;
use Dreamlex\TicketBundle\Entity\Traits\MediaTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="SET_YOUR_TABLE_NAME")
 */
class Media extends BaseMedia
{
    use MediaTrait;
    
    //your code....
}
```

##Setting User Entity
extend sonata BaseUser and use trait
```php
use Dreamlex\TicketBundle\Model\UserInterface;

class User extends BaseUser implement UserInterface
{
    use UserTrait;
    
    //your own code...
    
 }
```