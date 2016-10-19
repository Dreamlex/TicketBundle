Dreamlex Ticket Bundle
=======================
[![Build Status](https://travis-ci.org/Dreamlex/TicketBundle.svg?branch=master)](https://travis-ci.org/Dreamlex/TicketBundle)

Ticket bundle for symfony 

Setting Bundle
=====
#####Для доступа к картинкам с Административной части:
Админ должен иметь Роль `ROLE_TICKET_ADMIN`

typpings
--
Install typings `npm i typings` and add in typings.json:
```
    "bazinga-translator": "registry:dt/bazinga-translator#0.0.0+20160724062856",
    "daterangepicker": "registry:dt/daterangepicker#2.1.19+20160608082020",
    "handlebars": "registry:dt/handlebars#3.0.3+20160317120654",
    "jquery": "registry:dt/jquery#1.10.0+20160628074423",
    "jquery.validation": "registry:dt/jquery.validation#1.13.1+20160626224847",
    "moment": "registry:dt/moment#2.8.0+20160316155526",
    "moment-node": "registry:dt/moment-node#2.11.1+20160511043338",
    "select2": "registry:dt/select2#4.0.1+20160606153014",
    "underscore": "registry:dt/underscore#1.7.0+20160622050840"
    
```
    
##composer.json
`"pixassociates/sortable-behavior-bundle": "dev-master",`


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

TODO
--

1. add `, {}, 'frontend'` in translations 
