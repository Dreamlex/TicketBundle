Dreamlex Ticket Bundle
=======================

#Setting Bundle
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

---
##Setting User Entity
extend Sonata or FOS  BaseUser and use trait
```php

class User extends BaseUser 
{
    use UserTrait;
    
    //your own code...
    
 }
```
---
##Setting app/config/config.yml

``` yml
dreamlex_ticket:
    user_class: Path\To\Yours\User\Entity\User
    user_primary_key: id
    media_entity: Path\To\Yours\Sonata\Media\Entity\Media
```
---