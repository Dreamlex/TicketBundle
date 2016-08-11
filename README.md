Dreamlex Ticket Bundle
[![Build Status](https://travis-ci.org/Dreamlex/TicketBundle.svg?branch=master)](https://travis-ci.org/Dreamlex/TicketBundle)
=======================

Ticket bundle for symfony 

Setting Bundle
=====
#####Для доступа к картинкам с Административной части:

Админ должен иметь Роль `ROLE_TICKET_ADMIN`
##Setting User Entity
extend Sonata or FOS  BaseUser and use trait
```php

class User extends BaseUser 
{
    use UserTrait;
    
    //your own code...
    
 }
```
##Setting app/config/config.yml

``` yml
dreamlex_ticket:
    user_class: Path\To\Yours\User\Entity\User
    user_primary_key: id
    media_entity: Path\To\Yours\Sonata\Media\Entity\Media
```
add libs to your bower.json

```
    "select2": "~4",
    "featherlight": "1",
    "select2-bootstrap-theme": "*",
    "bootstrap-daterangepicker": "*"
```
Add css files to your main template such as
``` html
    'path/to/lib/bootstrap-daterangepicker/daterangepicker.css'
    'path/to/lib/select2/dist/css/select2.min.css'
    'path/to/lib/select2-bootstrap-theme/dist/select2-bootstrap.min.css'
    'path/to/lib/featherlight/release/featherlight.min.css'
```
Add js files to your main template such as
```
    '@path/to/lib/featherlight/src/featherlight.js'
    '@path/to/lib/bootstrap-daterangepicker/daterangepicker.js'
    '@path/to/lib/select2/dist/js/select2.full.min.js'
    '@path/to/lib/select2/dist/js/i18n/ru.js'
    '@path/to/lib/select2/dist/js/i18n/en.js'
```

Configure sonata media bundle and add image provider
```
sonata_media:
//some code
    contexts:
        ticket:
            providers:
               - sonata.media.provider.ticket_image
               - sonata.media.provider.file
            formats:
               small: { width: 100 , quality: 100}
               big:   { width: 1200 , quality: 100}
```
