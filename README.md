Dreamlex Ticket Bundle
=======================
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/872d9e36-1211-4f17-aa8d-dc8c3f9e5d3f/mini.png)](https://insight.sensiolabs.com/projects/872d9e36-1211-4f17-aa8d-dc8c3f9e5d3f)
[![Build Status](https://travis-ci.org/Dreamlex/TicketBundle.svg?branch=master)](https://travis-ci.org/Dreamlex/TicketBundle)
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