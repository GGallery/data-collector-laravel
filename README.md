## Struttura ad albero dei file principali

```
app
└ Console
  └ Commands
    └ GenerateApiToken.php
  └ Http
    └  Controllers
      └ Api 
        └ ContactController.php
    └  Middleware
      └  AuthenticateWithToken.php
    └  Resource
      └  ContactResource.php
└ bootstrap
  └ app.php //Configurazioni
└ database
  └ migrations
    └ create_contacts.php
    └ create_api_tokens_prefixes_table.php
└ routes
  └ api.php
```

## Comandi da tenere a mente

```
php artisan make:resource ContactResource

php artisan make:controller Api\ContactController --api

php artisan install:api per Laravel 11 <br><br>


php artisan make:command GenerateApiToken

php artisan make:middleware AuthenticateWithToken

php artisan generate:api-token-prefix "PlatformName" (generato da make:command)
```