# Struttura ad albero dei file principali

```
app
└ Console/Commands
  └ GenerateApiToken.php
└ Helpers
  └ EncryptionHelper.php
└ Http
  └ Controllers/Api
    └ ContactController.php
    └ ContactDetailsController.php
  └ Middleware
    └ AuthenticateWithToken.php
  └ Resources
    └ ContactResource.php
    └ ContactDetailsResource.php
└ Models
  └ ApiTokenPrefix.php
  └ Contact.php
  └ ContactDetails.php
  └ SystemLog.php

database
└ migrations
  └ create_contacts_table.php 
  └ create_contacts_details_table.php
  └ create_api_tokens_prefixes_table.php
  └ create_system_log_table.php
└ seeder
  └ ContactSeeder.php // Ormai obsoleto
```

# Comandi da tenere a mente

```
php artisan make:resource ContactResource

php artisan make:controller Api\ContactController --api

php artisan install:api per Laravel 11


php artisan make:command GenerateApiToken

php artisan make:middleware AuthenticateWithToken

php artisan generate:api-token-prefix "PlatformName" (generato da make:command)
```

<br>

# Componenti

## Commands

`GenerateApiToken`

- Genera token API per le piattaforme
- Crea un prefisso token e lo combina con un timestamp
- Cripta il token combinato usando `EncryptionHelper`
- Salva il prefisso e il nome della piattaforma nel database


## Helpers

`EncryptionHelper`

- Gestisce la crittografia/decrittografia usando SHA-256
- Utilizzato per la crittografia/decrittografia dei token API
- Usa variabili d'ambiente per chiave segreta e IV


## Controllers

`ContactController`

- Gestisce le operazioni CRUD base dei contatti
- Usa `ContactResource` per le risposte JSON
- Protetto dal middleware `AuthenticateWithToken`


`ContactDetailsController`

- Gestisce le informazioni dettagliate dei contatti
- Usa `ContactDetailsController` per le risposte JSON
- Include logging degli errori con identificazione della piattaforma
- Protetto dal middleware `AuthenticateWithToken`


## Middleware

`AuthenticateWithToken`

- Valida i token API
- Decripta i token e verifica i prefissi della piattaforma
- Controlla la scadenza del token
- Registra errori di autenticazione con dettagli della piattaforma


## Models

`ApiTokenPrefix`

- Memorizza nomi delle piattaforme e prefissi dei token
- Usato per la validazione dei token e l'identificazione della piattaforma


`Contact`

- Informazioni base del contatto
- Campi fillable: nome, username, email, password


`ContactDetails`

- Informazioni estese del contatto
- Include dettagli professionali e personali
- Molteplici campi nullable per flessibilità


`SystemLog`

- Registra eventi di sistema ed errori
- Traccia l'identificazione della piattaforma
- Memorizza file, funzione e dettagli degli errori
