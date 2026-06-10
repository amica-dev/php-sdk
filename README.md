# OpenAPIClient-php

La Customers API fornisce accesso programmatico a tutti i dati aziendali gestiti da [Amica 20](https://gestionaleamica.com). Supporta il ciclo completo di documenti, fatture, magazzino, contabilità e anagrafiche. Per guide e documentazione completa, visitare [amica.dev](https://amica.dev/).

## Autenticazione

Tutte le richieste API richiedono un token OAuth 2.0 Bearer valido, da includere nell'header `Authorization`:

```
Authorization: Bearer <token>
```

### Registra la tua app

Per integrare la Customers API registra la tua applicazione sull'**App Store di Amica**. Al termine della registrazione ottieni una coppia di credenziali OAuth 2.0 — `client_id` e `client_secret` (il secret è mostrato **una sola volta**) — e scegli gli **scope** e il **flusso di autenticazione** desiderati.

### Scegli il flusso

- **Authorization Code con PKCE** (consigliato per app che operano per conto di un utente Amica): l'utente autorizza la tua app tramite la pagina di consenso dell'identity provider; la tua app riceve un `code` da scambiare con un access token.

  ```
  GET  {identity-provider}/connect/authorize?response_type=code&client_id=<client_id>&redirect_uri=<redirect_uri>&scope=openid%20read%20write%20documenti&code_challenge=<challenge>&code_challenge_method=S256
  POST {identity-provider}/connect/token
       grant_type=authorization_code&code=<code>&code_verifier=<verifier>&client_id=<client_id>&client_secret=<client_secret>&redirect_uri=<redirect_uri>
  ```

- **Client Credentials** (per integrazioni server-to-server, senza utente interattivo):

  ```
  POST {identity-provider}/connect/token
  Content-Type: application/x-www-form-urlencoded

  grant_type=client_credentials&client_id=<client_id>&client_secret=<client_secret>&scope=read write documenti contabilita
  ```

  > **Nota:** il flusso Client Credentials è disponibile solo per le **app private**, non per quelle pubblicate sul marketplace.

In entrambi i casi la risposta contiene l'`access_token` (JWT con `aud: amica-cloud`) da usare nelle chiamate successive finché non scade (`expires_in`), dopodiché va richiesto un nuovo token. Ogni token contiene uno o più **scope** che determinano le aree accessibili (vedi sotto). Dalla [API Reference](https://amica.dev) è possibile incollare un access token per provare gli endpoint direttamente dal browser.

## Scope di autorizzazione

L'accesso agli endpoint è governato dagli scope presenti nel token OAuth:

| Scope | Descrizione |
|-------|-------------|
| `comune` | Anagrafiche: contatti, indirizzi, nazioni, valute, modalità di pagamento, ecc. |
| `magazzino` | Magazzino: articoli, listini, movimenti di magazzino, giacenze, distinte base. |
| `contabilita` | Contabilità: registrazioni contabili, scadenze, castelletti IVA, fatture elettroniche XML. |
| `documenti` | Documenti: fatture, note di credito, DDT, ordini, preventivi, proforma ed emissione. |

Una richiesta verso un endpoint non coperto dallo scope del token riceverà una risposta `403 Forbidden`.

## Company ID

La maggior parte degli endpoint è relativa a una specifica azienda e segue il pattern:

```
/api/v3/{companyId}/risorsa
```

Il `companyId` è l'identificativo univoco di ciascun database aziendale. Usare l'endpoint `GET /api/v3/config/Azienda` per ottenere l'elenco delle aziende disponibili.

## Paginazione

Gli endpoint che restituiscono liste supportano la paginazione tramite query parameter:

| Parametro | Descrizione |
|-----------|-------------|
| `Skip` | Numero di elementi da saltare dall'inizio del set di risultati. |
| `Take` | Numero di elementi da includere nella risposta. |

La risposta include due header:

| Header | Descrizione |
|--------|-------------|
| `X-Read-TotalCount` | Numero totale di elementi che soddisfano il filtro corrente. |
| `X-Read-Count` | Numero di elementi effettivamente restituiti nel body della risposta. |

## Ordinamento e filtri

Gli endpoint lista accettano parametri aggiuntivi:

| Parametro | Descrizione |
|-----------|-------------|
| `Order` | Lista di proprietà per l'ordinamento, separate da virgola. Un segno meno (`-`) prima del nome indica ordine discendente. Es. `-RagioneSociale1,Citta`. |
| `Filter` | Condizioni di ricerca: lista di espressioni separate da virgola, ciascuna formata da nome campo, operatore, valore ed eventuale connettore logico. Operatori: `$eq`, `$ne`, `$gt`, `$lt`, `$lte`, `$gte`, `$ct` (contiene), `$nct`, `$sw` (inizia con), `$ew` (finisce con). Connettori: `$and` (default, omissibile) e `$or`; le sotto-query si racchiudono tra `[]`. Es. `RagioneSociale1 $ct acme $or, Fido $gt 1000`. |
| `SkipTotalCount` | Se `true`, l'header `X-Read-TotalCount` viene omesso dalla risposta: migliori prestazioni a scapito del conteggio totale. Default `false`. |

## Espansione oggetti correlati

Gli endpoint singoli e lista supportano il parametro `Expand`. Quando fornito, gli oggetti correlati vengono inclusi inline nella risposta anziché restituiti come riferimenti. È possibile specificare espansioni multiple separate da virgola.

## Gestione errori

L'API usa codici di stato HTTP standard e restituisce [RFC 7807](https://tools.ietf.org/html/rfc7807) Problem Details in caso di errore:

| Codice | Significato |
|--------|-------------|
| `400` | Bad Request — validazione fallita o parametro obbligatorio mancante. |
| `401` | Unauthorized — token mancante o scaduto. |
| `403` | Forbidden — il token non possiede lo scope richiesto. |
| `404` | Not Found — la risorsa richiesta non esiste. |
| `409` | Conflict — conflitto di concorrenza. |
| `422` | Unprocessable Entity — entità non processabile (es. token di concorrenza obsoleto). |

Alcune risposte `400` includono un campo `overridable` che indica se l'errore di validazione può essere ignorato impostando il parametro `validationOverride` a `true`.

## Controllo della concorrenza

Le entità contengono una proprietà `ConcurrencyToken`. Quando si aggiorna un'entità tramite `PUT`, il token nel body della richiesta viene confrontato con il valore memorizzato. Se differiscono, viene restituito un `422 Unprocessable Entity`. Usare sempre l'ultimo token ricevuto da una risposta `GET` o `POST`.


For more information, please visit [https://amica.dev](https://amica.dev).

## Installation & Usage

### Requirements

PHP 7.4 and later.
Should also work with PHP 8.0.

### Composer

To install the bindings via [Composer](https://getcomposer.org/), add the following to `composer.json`:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/GIT_USER_ID/GIT_REPO_ID.git"
    }
  ],
  "require": {
    "GIT_USER_ID/GIT_REPO_ID": "*@dev"
  }
}
```

Then run `composer install`

### Manual Installation

Download the files and include `autoload.php`:

```php
<?php
require_once('/path/to/OpenAPIClient-php/vendor/autoload.php');
```

## Getting Started

Please follow the [installation procedure](#installation--usage) and then run the following:

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



// Configure Bearer (JWT) authorization: bearer
$config = Amica\Sdk\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure OAuth2 access token for authorization: oauth2
$config = Amica\Sdk\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure OAuth2 access token for authorization: oauth2
$config = Amica\Sdk\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new Amica\Sdk\Api\AnagraficaApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$selector = RagioneSociale1; // string | Il campo usato per distinguere gli elementi.
$skip = 100; // int | Numero di elementi, a partire dal primo, da escludere dal risultato. Combinato con `Take`, è utile per la paginazione.
$take = 25; // int | Numero di elementi da includere nel risultato. Combinato con `Skip`, è utile per la paginazione.

try {
    $result = $apiInstance->apiVversionCompanyIdAnagraficaDistinctGet($version, $company_id, $selector, $skip, $take);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AnagraficaApi->apiVversionCompanyIdAnagraficaDistinctGet: ', $e->getMessage(), PHP_EOL;
}

```

## API Endpoints

All URIs are relative to *http://localhost*

Class | Method | HTTP request | Description
------------ | ------------- | ------------- | -------------
*AnagraficaApi* | [**apiVversionCompanyIdAnagraficaDistinctGet**](docs/Api/AnagraficaApi.md#apivversioncompanyidanagraficadistinctget) | **GET** /api/v{version}/{companyId}/Anagrafica/distinct | Restituisce una lista di elementi distinti.
*AnagraficaApi* | [**apiVversionCompanyIdAnagraficaGet**](docs/Api/AnagraficaApi.md#apivversioncompanyidanagraficaget) | **GET** /api/v{version}/{companyId}/Anagrafica | Restituisce una lista di elementi.
*AnagraficaApi* | [**apiVversionCompanyIdAnagraficaIdDelete**](docs/Api/AnagraficaApi.md#apivversioncompanyidanagraficaiddelete) | **DELETE** /api/v{version}/{companyId}/Anagrafica/{id} | Elimina un elemento.
*AnagraficaApi* | [**apiVversionCompanyIdAnagraficaIdGet**](docs/Api/AnagraficaApi.md#apivversioncompanyidanagraficaidget) | **GET** /api/v{version}/{companyId}/Anagrafica/{id} | Restituisce un elemento.
*AnagraficaApi* | [**apiVversionCompanyIdAnagraficaIdPut**](docs/Api/AnagraficaApi.md#apivversioncompanyidanagraficaidput) | **PUT** /api/v{version}/{companyId}/Anagrafica/{id} | Aggiorna un elemento esistente.
*AnagraficaApi* | [**apiVversionCompanyIdAnagraficaNewGet**](docs/Api/AnagraficaApi.md#apivversioncompanyidanagraficanewget) | **GET** /api/v{version}/{companyId}/Anagrafica/new | Restituisce una nuova istanza di un contatto del tipo specificato.
*AnagraficaApi* | [**apiVversionCompanyIdAnagraficaPost**](docs/Api/AnagraficaApi.md#apivversioncompanyidanagraficapost) | **POST** /api/v{version}/{companyId}/Anagrafica | Salva un nuovo elemento.
*AreaGeograficaApi* | [**apiVversionCompanyIdAreaGeograficaDistinctGet**](docs/Api/AreaGeograficaApi.md#apivversioncompanyidareageograficadistinctget) | **GET** /api/v{version}/{companyId}/AreaGeografica/distinct | Restituisce una lista di elementi distinti.
*AreaGeograficaApi* | [**apiVversionCompanyIdAreaGeograficaGet**](docs/Api/AreaGeograficaApi.md#apivversioncompanyidareageograficaget) | **GET** /api/v{version}/{companyId}/AreaGeografica | Restituisce una lista di elementi.
*AreaGeograficaApi* | [**apiVversionCompanyIdAreaGeograficaIdDelete**](docs/Api/AreaGeograficaApi.md#apivversioncompanyidareageograficaiddelete) | **DELETE** /api/v{version}/{companyId}/AreaGeografica/{id} | Elimina un elemento.
*AreaGeograficaApi* | [**apiVversionCompanyIdAreaGeograficaIdGet**](docs/Api/AreaGeograficaApi.md#apivversioncompanyidareageograficaidget) | **GET** /api/v{version}/{companyId}/AreaGeografica/{id} | Restituisce un elemento.
*AreaGeograficaApi* | [**apiVversionCompanyIdAreaGeograficaIdPut**](docs/Api/AreaGeograficaApi.md#apivversioncompanyidareageograficaidput) | **PUT** /api/v{version}/{companyId}/AreaGeografica/{id} | Aggiorna un elemento esistente.
*AreaGeograficaApi* | [**apiVversionCompanyIdAreaGeograficaNewGet**](docs/Api/AreaGeograficaApi.md#apivversioncompanyidareageograficanewget) | **GET** /api/v{version}/{companyId}/AreaGeografica/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*AreaGeograficaApi* | [**apiVversionCompanyIdAreaGeograficaPost**](docs/Api/AreaGeograficaApi.md#apivversioncompanyidareageograficapost) | **POST** /api/v{version}/{companyId}/AreaGeografica | Salva un nuovo elemento.
*ArticoloApi* | [**apiVversionCompanyIdArticoloGet**](docs/Api/ArticoloApi.md#apivversioncompanyidarticologet) | **GET** /api/v{version}/{companyId}/Articolo | Restituisce una lista di elementi.
*ArticoloApi* | [**apiVversionCompanyIdArticoloIdDelete**](docs/Api/ArticoloApi.md#apivversioncompanyidarticoloiddelete) | **DELETE** /api/v{version}/{companyId}/Articolo/{id} | Elimina un elemento.
*ArticoloApi* | [**apiVversionCompanyIdArticoloIdGet**](docs/Api/ArticoloApi.md#apivversioncompanyidarticoloidget) | **GET** /api/v{version}/{companyId}/Articolo/{id} | Restituisce un elemento.
*ArticoloApi* | [**apiVversionCompanyIdArticoloIdPut**](docs/Api/ArticoloApi.md#apivversioncompanyidarticoloidput) | **PUT** /api/v{version}/{companyId}/Articolo/{id} | Aggiorna un elemento esistente.
*ArticoloApi* | [**apiVversionCompanyIdArticoloNewGet**](docs/Api/ArticoloApi.md#apivversioncompanyidarticolonewget) | **GET** /api/v{version}/{companyId}/Articolo/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*ArticoloApi* | [**apiVversionCompanyIdArticoloPost**](docs/Api/ArticoloApi.md#apivversioncompanyidarticolopost) | **POST** /api/v{version}/{companyId}/Articolo | Salva un nuovo elemento.
*ArticoloMediaFileApi* | [**apiVversionCompanyIdArticoloMediaFileBulkDelete**](docs/Api/ArticoloMediaFileApi.md#apivversioncompanyidarticolomediafilebulkdelete) | **DELETE** /api/v{version}/{companyId}/ArticoloMediaFile/bulk | Elimina in blocco tutti i file multimediali degli articoli.
*ArticoloMediaFileApi* | [**apiVversionCompanyIdArticoloMediaFileBulkGet**](docs/Api/ArticoloMediaFileApi.md#apivversioncompanyidarticolomediafilebulkget) | **GET** /api/v{version}/{companyId}/ArticoloMediaFile/bulk | Scarica tutti i file multimediali degli articoli come archivio ZIP.
*ArticoloMediaFileApi* | [**apiVversionCompanyIdArticoloMediaFileGet**](docs/Api/ArticoloMediaFileApi.md#apivversioncompanyidarticolomediafileget) | **GET** /api/v{version}/{companyId}/ArticoloMediaFile | Restituisce una lista di elementi.
*ArticoloMediaFileApi* | [**apiVversionCompanyIdArticoloMediaFileIdDelete**](docs/Api/ArticoloMediaFileApi.md#apivversioncompanyidarticolomediafileiddelete) | **DELETE** /api/v{version}/{companyId}/ArticoloMediaFile/{id} | Elimina un elemento.
*ArticoloMediaFileApi* | [**apiVversionCompanyIdArticoloMediaFileIdGet**](docs/Api/ArticoloMediaFileApi.md#apivversioncompanyidarticolomediafileidget) | **GET** /api/v{version}/{companyId}/ArticoloMediaFile/{id} | Restituisce un elemento.
*ArticoloMediaFileApi* | [**apiVversionCompanyIdArticoloMediaFileIdPut**](docs/Api/ArticoloMediaFileApi.md#apivversioncompanyidarticolomediafileidput) | **PUT** /api/v{version}/{companyId}/ArticoloMediaFile/{id} | Aggiorna un elemento esistente.
*ArticoloMediaFileApi* | [**apiVversionCompanyIdArticoloMediaFileNewGet**](docs/Api/ArticoloMediaFileApi.md#apivversioncompanyidarticolomediafilenewget) | **GET** /api/v{version}/{companyId}/ArticoloMediaFile/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*ArticoloMediaFileApi* | [**apiVversionCompanyIdArticoloMediaFilePost**](docs/Api/ArticoloMediaFileApi.md#apivversioncompanyidarticolomediafilepost) | **POST** /api/v{version}/{companyId}/ArticoloMediaFile | Salva un nuovo elemento.
*AziendaApi* | [**apiVversionConfigAziendaGet**](docs/Api/AziendaApi.md#apivversionconfigaziendaget) | **GET** /api/v{version}/config/Azienda | Restituisce una lista di elementi.
*AziendaApi* | [**apiVversionConfigAziendaIdGet**](docs/Api/AziendaApi.md#apivversionconfigaziendaidget) | **GET** /api/v{version}/config/Azienda/{id} | Restituisce un elemento.
*BeneServizioApi* | [**apiVversionCompanyIdBeneServizioCollegadocumentoPost**](docs/Api/BeneServizioApi.md#apivversioncompanyidbeneserviziocollegadocumentopost) | **POST** /api/v{version}/{companyId}/BeneServizio/collegadocumento | Collega un bene/servizio a un documento.
*BeneServizioApi* | [**apiVversionCompanyIdBeneServizioComputePost**](docs/Api/BeneServizioApi.md#apivversioncompanyidbeneserviziocomputepost) | **POST** /api/v{version}/{companyId}/BeneServizio/compute | Calcola gli importi di un Amica.BeneServizio (vedi Amica.BeneServizioComputeResult), considerando parametri come quantità movimentata, IVA applicata, eventuali sconti.
*BeneServizioApi* | [**apiVversionCompanyIdBeneServizioGet**](docs/Api/BeneServizioApi.md#apivversioncompanyidbeneservizioget) | **GET** /api/v{version}/{companyId}/BeneServizio | Restituisce una lista di elementi.
*BeneServizioApi* | [**apiVversionCompanyIdBeneServizioIdDelete**](docs/Api/BeneServizioApi.md#apivversioncompanyidbeneservizioiddelete) | **DELETE** /api/v{version}/{companyId}/BeneServizio/{id} | Elimina un elemento.
*BeneServizioApi* | [**apiVversionCompanyIdBeneServizioIdGet**](docs/Api/BeneServizioApi.md#apivversioncompanyidbeneservizioidget) | **GET** /api/v{version}/{companyId}/BeneServizio/{id} | Restituisce un elemento.
*BeneServizioApi* | [**apiVversionCompanyIdBeneServizioIdPut**](docs/Api/BeneServizioApi.md#apivversioncompanyidbeneservizioidput) | **PUT** /api/v{version}/{companyId}/BeneServizio/{id} | Aggiorna un elemento esistente.
*BeneServizioApi* | [**apiVversionCompanyIdBeneServizioNewGet**](docs/Api/BeneServizioApi.md#apivversioncompanyidbeneservizionewget) | **GET** /api/v{version}/{companyId}/BeneServizio/new | Restituisce una nuova istanza di un bene/servizio.
*BeneServizioApi* | [**apiVversionCompanyIdBeneServizioPost**](docs/Api/BeneServizioApi.md#apivversioncompanyidbeneserviziopost) | **POST** /api/v{version}/{companyId}/BeneServizio | Salva un nuovo elemento.
*CassaPrevidenzialeApi* | [**apiVversionCompanyIdTabelleCassaPrevidenzialeGet**](docs/Api/CassaPrevidenzialeApi.md#apivversioncompanyidtabellecassaprevidenzialeget) | **GET** /api/v{version}/{companyId}/tabelle/CassaPrevidenziale | Restituisce il contenuto della tabella come lista di elementi.
*CastellettoIvaApi* | [**apiVversionCompanyIdCastellettoIvaGet**](docs/Api/CastellettoIvaApi.md#apivversioncompanyidcastellettoivaget) | **GET** /api/v{version}/{companyId}/CastellettoIva | Restituisce una lista di elementi.
*CastellettoIvaApi* | [**apiVversionCompanyIdCastellettoIvaIdDelete**](docs/Api/CastellettoIvaApi.md#apivversioncompanyidcastellettoivaiddelete) | **DELETE** /api/v{version}/{companyId}/CastellettoIva/{id} | Elimina un elemento.
*CastellettoIvaApi* | [**apiVversionCompanyIdCastellettoIvaIdGet**](docs/Api/CastellettoIvaApi.md#apivversioncompanyidcastellettoivaidget) | **GET** /api/v{version}/{companyId}/CastellettoIva/{id} | Restituisce un elemento.
*CastellettoIvaApi* | [**apiVversionCompanyIdCastellettoIvaIdPut**](docs/Api/CastellettoIvaApi.md#apivversioncompanyidcastellettoivaidput) | **PUT** /api/v{version}/{companyId}/CastellettoIva/{id} | Aggiorna un elemento esistente.
*CastellettoIvaApi* | [**apiVversionCompanyIdCastellettoIvaNewGet**](docs/Api/CastellettoIvaApi.md#apivversioncompanyidcastellettoivanewget) | **GET** /api/v{version}/{companyId}/CastellettoIva/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*CastellettoIvaApi* | [**apiVversionCompanyIdCastellettoIvaPost**](docs/Api/CastellettoIvaApi.md#apivversioncompanyidcastellettoivapost) | **POST** /api/v{version}/{companyId}/CastellettoIva | Salva un nuovo elemento.
*CategoriaAnagraficaApi* | [**apiVversionCompanyIdCategoriaAnagraficaDistinctGet**](docs/Api/CategoriaAnagraficaApi.md#apivversioncompanyidcategoriaanagraficadistinctget) | **GET** /api/v{version}/{companyId}/CategoriaAnagrafica/distinct | Restituisce una lista di elementi distinti.
*CategoriaAnagraficaApi* | [**apiVversionCompanyIdCategoriaAnagraficaGet**](docs/Api/CategoriaAnagraficaApi.md#apivversioncompanyidcategoriaanagraficaget) | **GET** /api/v{version}/{companyId}/CategoriaAnagrafica | Restituisce una lista di elementi.
*CategoriaAnagraficaApi* | [**apiVversionCompanyIdCategoriaAnagraficaIdDelete**](docs/Api/CategoriaAnagraficaApi.md#apivversioncompanyidcategoriaanagraficaiddelete) | **DELETE** /api/v{version}/{companyId}/CategoriaAnagrafica/{id} | Elimina un elemento.
*CategoriaAnagraficaApi* | [**apiVversionCompanyIdCategoriaAnagraficaIdGet**](docs/Api/CategoriaAnagraficaApi.md#apivversioncompanyidcategoriaanagraficaidget) | **GET** /api/v{version}/{companyId}/CategoriaAnagrafica/{id} | Restituisce un elemento.
*CategoriaAnagraficaApi* | [**apiVversionCompanyIdCategoriaAnagraficaIdPut**](docs/Api/CategoriaAnagraficaApi.md#apivversioncompanyidcategoriaanagraficaidput) | **PUT** /api/v{version}/{companyId}/CategoriaAnagrafica/{id} | Aggiorna un elemento esistente.
*CategoriaAnagraficaApi* | [**apiVversionCompanyIdCategoriaAnagraficaNewGet**](docs/Api/CategoriaAnagraficaApi.md#apivversioncompanyidcategoriaanagraficanewget) | **GET** /api/v{version}/{companyId}/CategoriaAnagrafica/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*CategoriaAnagraficaApi* | [**apiVversionCompanyIdCategoriaAnagraficaPost**](docs/Api/CategoriaAnagraficaApi.md#apivversioncompanyidcategoriaanagraficapost) | **POST** /api/v{version}/{companyId}/CategoriaAnagrafica | Salva un nuovo elemento.
*CategoriaMerceologicaApi* | [**apiVversionCompanyIdCategoriaMerceologicaGet**](docs/Api/CategoriaMerceologicaApi.md#apivversioncompanyidcategoriamerceologicaget) | **GET** /api/v{version}/{companyId}/CategoriaMerceologica | Restituisce una lista di elementi.
*CategoriaMerceologicaApi* | [**apiVversionCompanyIdCategoriaMerceologicaIdDelete**](docs/Api/CategoriaMerceologicaApi.md#apivversioncompanyidcategoriamerceologicaiddelete) | **DELETE** /api/v{version}/{companyId}/CategoriaMerceologica/{id} | Elimina un elemento.
*CategoriaMerceologicaApi* | [**apiVversionCompanyIdCategoriaMerceologicaIdGet**](docs/Api/CategoriaMerceologicaApi.md#apivversioncompanyidcategoriamerceologicaidget) | **GET** /api/v{version}/{companyId}/CategoriaMerceologica/{id} | Restituisce un elemento.
*CategoriaMerceologicaApi* | [**apiVversionCompanyIdCategoriaMerceologicaIdPut**](docs/Api/CategoriaMerceologicaApi.md#apivversioncompanyidcategoriamerceologicaidput) | **PUT** /api/v{version}/{companyId}/CategoriaMerceologica/{id} | Aggiorna un elemento esistente.
*CategoriaMerceologicaApi* | [**apiVversionCompanyIdCategoriaMerceologicaNewGet**](docs/Api/CategoriaMerceologicaApi.md#apivversioncompanyidcategoriamerceologicanewget) | **GET** /api/v{version}/{companyId}/CategoriaMerceologica/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*CategoriaMerceologicaApi* | [**apiVversionCompanyIdCategoriaMerceologicaPost**](docs/Api/CategoriaMerceologicaApi.md#apivversioncompanyidcategoriamerceologicapost) | **POST** /api/v{version}/{companyId}/CategoriaMerceologica | Salva un nuovo elemento.
*CausaleDocumentoApi* | [**apiVversionCompanyIdCausaleDocumentoGet**](docs/Api/CausaleDocumentoApi.md#apivversioncompanyidcausaledocumentoget) | **GET** /api/v{version}/{companyId}/CausaleDocumento | Restituisce una lista di elementi.
*CausaleDocumentoApi* | [**apiVversionCompanyIdCausaleDocumentoIdDelete**](docs/Api/CausaleDocumentoApi.md#apivversioncompanyidcausaledocumentoiddelete) | **DELETE** /api/v{version}/{companyId}/CausaleDocumento/{id} | Elimina un elemento.
*CausaleDocumentoApi* | [**apiVversionCompanyIdCausaleDocumentoIdGet**](docs/Api/CausaleDocumentoApi.md#apivversioncompanyidcausaledocumentoidget) | **GET** /api/v{version}/{companyId}/CausaleDocumento/{id} | Restituisce un elemento.
*CausaleDocumentoApi* | [**apiVversionCompanyIdCausaleDocumentoIdPut**](docs/Api/CausaleDocumentoApi.md#apivversioncompanyidcausaledocumentoidput) | **PUT** /api/v{version}/{companyId}/CausaleDocumento/{id} | Aggiorna un elemento esistente.
*CausaleDocumentoApi* | [**apiVversionCompanyIdCausaleDocumentoNewGet**](docs/Api/CausaleDocumentoApi.md#apivversioncompanyidcausaledocumentonewget) | **GET** /api/v{version}/{companyId}/CausaleDocumento/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*CausaleDocumentoApi* | [**apiVversionCompanyIdCausaleDocumentoPost**](docs/Api/CausaleDocumentoApi.md#apivversioncompanyidcausaledocumentopost) | **POST** /api/v{version}/{companyId}/CausaleDocumento | Salva un nuovo elemento.
*CausaleIvaApi* | [**apiVversionCompanyIdCausaleIvaGet**](docs/Api/CausaleIvaApi.md#apivversioncompanyidcausaleivaget) | **GET** /api/v{version}/{companyId}/CausaleIva | Restituisce una lista di elementi.
*CausaleIvaApi* | [**apiVversionCompanyIdCausaleIvaIdDelete**](docs/Api/CausaleIvaApi.md#apivversioncompanyidcausaleivaiddelete) | **DELETE** /api/v{version}/{companyId}/CausaleIva/{id} | Elimina un elemento.
*CausaleIvaApi* | [**apiVversionCompanyIdCausaleIvaIdGet**](docs/Api/CausaleIvaApi.md#apivversioncompanyidcausaleivaidget) | **GET** /api/v{version}/{companyId}/CausaleIva/{id} | Restituisce un elemento.
*CausaleIvaApi* | [**apiVversionCompanyIdCausaleIvaIdPut**](docs/Api/CausaleIvaApi.md#apivversioncompanyidcausaleivaidput) | **PUT** /api/v{version}/{companyId}/CausaleIva/{id} | Aggiorna un elemento esistente.
*CausaleIvaApi* | [**apiVversionCompanyIdCausaleIvaNewGet**](docs/Api/CausaleIvaApi.md#apivversioncompanyidcausaleivanewget) | **GET** /api/v{version}/{companyId}/CausaleIva/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*CausaleIvaApi* | [**apiVversionCompanyIdCausaleIvaPost**](docs/Api/CausaleIvaApi.md#apivversioncompanyidcausaleivapost) | **POST** /api/v{version}/{companyId}/CausaleIva | Salva un nuovo elemento.
*CausaleMagazzinoApi* | [**apiVversionCompanyIdCausaleMagazzinoGet**](docs/Api/CausaleMagazzinoApi.md#apivversioncompanyidcausalemagazzinoget) | **GET** /api/v{version}/{companyId}/CausaleMagazzino | Restituisce una lista di elementi.
*CausaleMagazzinoApi* | [**apiVversionCompanyIdCausaleMagazzinoIdDelete**](docs/Api/CausaleMagazzinoApi.md#apivversioncompanyidcausalemagazzinoiddelete) | **DELETE** /api/v{version}/{companyId}/CausaleMagazzino/{id} | Elimina un elemento.
*CausaleMagazzinoApi* | [**apiVversionCompanyIdCausaleMagazzinoIdGet**](docs/Api/CausaleMagazzinoApi.md#apivversioncompanyidcausalemagazzinoidget) | **GET** /api/v{version}/{companyId}/CausaleMagazzino/{id} | Restituisce un elemento.
*CausaleMagazzinoApi* | [**apiVversionCompanyIdCausaleMagazzinoIdPut**](docs/Api/CausaleMagazzinoApi.md#apivversioncompanyidcausalemagazzinoidput) | **PUT** /api/v{version}/{companyId}/CausaleMagazzino/{id} | Aggiorna un elemento esistente.
*CausaleMagazzinoApi* | [**apiVversionCompanyIdCausaleMagazzinoNewGet**](docs/Api/CausaleMagazzinoApi.md#apivversioncompanyidcausalemagazzinonewget) | **GET** /api/v{version}/{companyId}/CausaleMagazzino/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*CausaleMagazzinoApi* | [**apiVversionCompanyIdCausaleMagazzinoPost**](docs/Api/CausaleMagazzinoApi.md#apivversioncompanyidcausalemagazzinopost) | **POST** /api/v{version}/{companyId}/CausaleMagazzino | Salva un nuovo elemento.
*CodicePagamentoApi* | [**apiVversionCompanyIdTabelleCodicePagamentoGet**](docs/Api/CodicePagamentoApi.md#apivversioncompanyidtabellecodicepagamentoget) | **GET** /api/v{version}/{companyId}/tabelle/CodicePagamento | Restituisce il contenuto della tabella come lista di elementi.
*ContoContabileApi* | [**apiVversionCompanyIdContoContabileGet**](docs/Api/ContoContabileApi.md#apivversioncompanyidcontocontabileget) | **GET** /api/v{version}/{companyId}/ContoContabile | Restituisce una lista di elementi.
*ContoContabileApi* | [**apiVversionCompanyIdContoContabileIdDelete**](docs/Api/ContoContabileApi.md#apivversioncompanyidcontocontabileiddelete) | **DELETE** /api/v{version}/{companyId}/ContoContabile/{id} | Elimina un elemento.
*ContoContabileApi* | [**apiVversionCompanyIdContoContabileIdGet**](docs/Api/ContoContabileApi.md#apivversioncompanyidcontocontabileidget) | **GET** /api/v{version}/{companyId}/ContoContabile/{id} | Restituisce un elemento.
*ContoContabileApi* | [**apiVversionCompanyIdContoContabileIdPut**](docs/Api/ContoContabileApi.md#apivversioncompanyidcontocontabileidput) | **PUT** /api/v{version}/{companyId}/ContoContabile/{id} | Aggiorna un elemento esistente.
*ContoContabileApi* | [**apiVversionCompanyIdContoContabileNewGet**](docs/Api/ContoContabileApi.md#apivversioncompanyidcontocontabilenewget) | **GET** /api/v{version}/{companyId}/ContoContabile/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*ContoContabileApi* | [**apiVversionCompanyIdContoContabilePost**](docs/Api/ContoContabileApi.md#apivversioncompanyidcontocontabilepost) | **POST** /api/v{version}/{companyId}/ContoContabile | Salva un nuovo elemento.
*ContoContabileApi* | [**apiVversionCompanyIdContoContabileRepairPost**](docs/Api/ContoContabileApi.md#apivversioncompanyidcontocontabilerepairpost) | **POST** /api/v{version}/{companyId}/ContoContabile/repair | Valida le registrazioni contabili, cercando di correggere i problemi più comuni.
*DistintaBaseApi* | [**apiVversionCompanyIdDistintaBaseGet**](docs/Api/DistintaBaseApi.md#apivversioncompanyiddistintabaseget) | **GET** /api/v{version}/{companyId}/DistintaBase | Restituisce una lista di elementi.
*DistintaBaseApi* | [**apiVversionCompanyIdDistintaBaseIdDelete**](docs/Api/DistintaBaseApi.md#apivversioncompanyiddistintabaseiddelete) | **DELETE** /api/v{version}/{companyId}/DistintaBase/{id} | Elimina un elemento.
*DistintaBaseApi* | [**apiVversionCompanyIdDistintaBaseIdGet**](docs/Api/DistintaBaseApi.md#apivversioncompanyiddistintabaseidget) | **GET** /api/v{version}/{companyId}/DistintaBase/{id} | Restituisce un elemento.
*DistintaBaseApi* | [**apiVversionCompanyIdDistintaBaseIdPut**](docs/Api/DistintaBaseApi.md#apivversioncompanyiddistintabaseidput) | **PUT** /api/v{version}/{companyId}/DistintaBase/{id} | Aggiorna un elemento esistente.
*DistintaBaseApi* | [**apiVversionCompanyIdDistintaBaseNewGet**](docs/Api/DistintaBaseApi.md#apivversioncompanyiddistintabasenewget) | **GET** /api/v{version}/{companyId}/DistintaBase/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*DistintaBaseApi* | [**apiVversionCompanyIdDistintaBasePost**](docs/Api/DistintaBaseApi.md#apivversioncompanyiddistintabasepost) | **POST** /api/v{version}/{companyId}/DistintaBase | Salva un nuovo elemento.
*DocumentoApi* | [**apiVversionCompanyIdDocumentoComputePost**](docs/Api/DocumentoApi.md#apivversioncompanyiddocumentocomputepost) | **POST** /api/v{version}/{companyId}/Documento/compute | Calcola i totali di un documento.
*DocumentoApi* | [**apiVversionCompanyIdDocumentoDistinctGet**](docs/Api/DocumentoApi.md#apivversioncompanyiddocumentodistinctget) | **GET** /api/v{version}/{companyId}/Documento/distinct | Restituisce una lista di elementi distinti.
*DocumentoApi* | [**apiVversionCompanyIdDocumentoDocumentoIdEmitPost**](docs/Api/DocumentoApi.md#apivversioncompanyiddocumentodocumentoidemitpost) | **POST** /api/v{version}/{companyId}/Documento/{documentoId}/emit | Emette un documento.
*DocumentoApi* | [**apiVversionCompanyIdDocumentoDocumentoIdNewinstancefromtemplatePost**](docs/Api/DocumentoApi.md#apivversioncompanyiddocumentodocumentoidnewinstancefromtemplatepost) | **POST** /api/v{version}/{companyId}/Documento/{documentoId}/newinstancefromtemplate | Replica un documento. Crea una copia con la data odierna e, se è un documento di vendita, un nuovo numero. Alcuni campi vengono reimpostati ai valori predefiniti (stato del documento, stato SDI, stato del pagamento, ecc.).
*DocumentoApi* | [**apiVversionCompanyIdDocumentoGet**](docs/Api/DocumentoApi.md#apivversioncompanyiddocumentoget) | **GET** /api/v{version}/{companyId}/Documento | Restituisce una lista di elementi.
*DocumentoApi* | [**apiVversionCompanyIdDocumentoIdDelete**](docs/Api/DocumentoApi.md#apivversioncompanyiddocumentoiddelete) | **DELETE** /api/v{version}/{companyId}/Documento/{id} | Elimina un elemento.
*DocumentoApi* | [**apiVversionCompanyIdDocumentoIdGet**](docs/Api/DocumentoApi.md#apivversioncompanyiddocumentoidget) | **GET** /api/v{version}/{companyId}/Documento/{id} | Restituisce un elemento.
*DocumentoApi* | [**apiVversionCompanyIdDocumentoIdImportAccontoPost**](docs/Api/DocumentoApi.md#apivversioncompanyiddocumentoidimportaccontopost) | **POST** /api/v{version}/{companyId}/Documento/{id}/import/acconto | Importa righe da uno o più acconti in un documento esistente.
*DocumentoApi* | [**apiVversionCompanyIdDocumentoIdImportDdtPost**](docs/Api/DocumentoApi.md#apivversioncompanyiddocumentoidimportddtpost) | **POST** /api/v{version}/{companyId}/Documento/{id}/import/ddt | Importa righe da uno o più DDT in un documento esistente.
*DocumentoApi* | [**apiVversionCompanyIdDocumentoIdImportOrdinePost**](docs/Api/DocumentoApi.md#apivversioncompanyiddocumentoidimportordinepost) | **POST** /api/v{version}/{companyId}/Documento/{id}/import/ordine | Importa righe da uno o più ordini in un documento esistente.
*DocumentoApi* | [**apiVversionCompanyIdDocumentoIdImportPreventivoPost**](docs/Api/DocumentoApi.md#apivversioncompanyiddocumentoidimportpreventivopost) | **POST** /api/v{version}/{companyId}/Documento/{id}/import/preventivo | Importa righe da uno o più preventivi in un documento esistente.
*DocumentoApi* | [**apiVversionCompanyIdDocumentoIdImportProformaPost**](docs/Api/DocumentoApi.md#apivversioncompanyiddocumentoidimportproformapost) | **POST** /api/v{version}/{companyId}/Documento/{id}/import/proforma | Importa righe da una o più fatture proforma in un documento esistente.
*DocumentoApi* | [**apiVversionCompanyIdDocumentoIdImportResoPost**](docs/Api/DocumentoApi.md#apivversioncompanyiddocumentoidimportresopost) | **POST** /api/v{version}/{companyId}/Documento/{id}/import/reso | Importa righe da uno o più resi in un documento esistente.
*DocumentoApi* | [**apiVversionCompanyIdDocumentoIdPut**](docs/Api/DocumentoApi.md#apivversioncompanyiddocumentoidput) | **PUT** /api/v{version}/{companyId}/Documento/{id} | Aggiorna un elemento esistente.
*DocumentoApi* | [**apiVversionCompanyIdDocumentoNewGet**](docs/Api/DocumentoApi.md#apivversioncompanyiddocumentonewget) | **GET** /api/v{version}/{companyId}/Documento/new | Restituisce una nuova istanza di un documento del tipo specificato.
*DocumentoApi* | [**apiVversionCompanyIdDocumentoPeriodicinvoicingPost**](docs/Api/DocumentoApi.md#apivversioncompanyiddocumentoperiodicinvoicingpost) | **POST** /api/v{version}/{companyId}/Documento/periodicinvoicing | Genera fatture per tutti i contatti che corrispondono ai criteri di fatturazione periodica.
*DocumentoApi* | [**apiVversionCompanyIdDocumentoPost**](docs/Api/DocumentoApi.md#apivversioncompanyiddocumentopost) | **POST** /api/v{version}/{companyId}/Documento | Salva un nuovo elemento.
*DocumentoMediaFileApi* | [**apiVversionCompanyIdDocumentoMediaFileBulkDelete**](docs/Api/DocumentoMediaFileApi.md#apivversioncompanyiddocumentomediafilebulkdelete) | **DELETE** /api/v{version}/{companyId}/DocumentoMediaFile/bulk | Elimina in blocco tutti i file multimediali dei documenti.
*DocumentoMediaFileApi* | [**apiVversionCompanyIdDocumentoMediaFileBulkGet**](docs/Api/DocumentoMediaFileApi.md#apivversioncompanyiddocumentomediafilebulkget) | **GET** /api/v{version}/{companyId}/DocumentoMediaFile/bulk | Scarica tutti i file multimediali dei documenti come archivio ZIP.
*DocumentoMediaFileApi* | [**apiVversionCompanyIdDocumentoMediaFileGet**](docs/Api/DocumentoMediaFileApi.md#apivversioncompanyiddocumentomediafileget) | **GET** /api/v{version}/{companyId}/DocumentoMediaFile | Restituisce una lista di elementi.
*DocumentoMediaFileApi* | [**apiVversionCompanyIdDocumentoMediaFileIdDelete**](docs/Api/DocumentoMediaFileApi.md#apivversioncompanyiddocumentomediafileiddelete) | **DELETE** /api/v{version}/{companyId}/DocumentoMediaFile/{id} | Elimina un elemento.
*DocumentoMediaFileApi* | [**apiVversionCompanyIdDocumentoMediaFileIdGet**](docs/Api/DocumentoMediaFileApi.md#apivversioncompanyiddocumentomediafileidget) | **GET** /api/v{version}/{companyId}/DocumentoMediaFile/{id} | Restituisce un elemento.
*DocumentoMediaFileApi* | [**apiVversionCompanyIdDocumentoMediaFileIdPut**](docs/Api/DocumentoMediaFileApi.md#apivversioncompanyiddocumentomediafileidput) | **PUT** /api/v{version}/{companyId}/DocumentoMediaFile/{id} | Aggiorna un elemento esistente.
*DocumentoMediaFileApi* | [**apiVversionCompanyIdDocumentoMediaFileNewGet**](docs/Api/DocumentoMediaFileApi.md#apivversioncompanyiddocumentomediafilenewget) | **GET** /api/v{version}/{companyId}/DocumentoMediaFile/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*DocumentoMediaFileApi* | [**apiVversionCompanyIdDocumentoMediaFilePost**](docs/Api/DocumentoMediaFileApi.md#apivversioncompanyiddocumentomediafilepost) | **POST** /api/v{version}/{companyId}/DocumentoMediaFile | Salva un nuovo elemento.
*EventoApi* | [**apiVversionEventoGet**](docs/Api/EventoApi.md#apivversioneventoget) | **GET** /api/v{version}/Evento | 
*EventoApi* | [**apiVversionEventoIdDelete**](docs/Api/EventoApi.md#apivversioneventoiddelete) | **DELETE** /api/v{version}/Evento/{id} | Elimina un elemento.
*EventoApi* | [**apiVversionEventoIdGet**](docs/Api/EventoApi.md#apivversioneventoidget) | **GET** /api/v{version}/Evento/{id} | 
*EventoApi* | [**apiVversionEventoIdPut**](docs/Api/EventoApi.md#apivversioneventoidput) | **PUT** /api/v{version}/Evento/{id} | Aggiorna un elemento esistente.
*EventoApi* | [**apiVversionEventoNewGet**](docs/Api/EventoApi.md#apivversioneventonewget) | **GET** /api/v{version}/Evento/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*EventoApi* | [**apiVversionEventoPost**](docs/Api/EventoApi.md#apivversioneventopost) | **POST** /api/v{version}/Evento | Salva un nuovo elemento.
*FatturaElettronicaXmlApi* | [**apiVversionCompanyIdFatturaElettronicaXmlArchivebydaterangePost**](docs/Api/FatturaElettronicaXmlApi.md#apivversioncompanyidfatturaelettronicaxmlarchivebydaterangepost) | **POST** /api/v{version}/{companyId}/FatturaElettronicaXml/archivebydaterange | Scarica un archivio ZIP di fatture XML, filtrate per intervallo di date.
*FatturaElettronicaXmlApi* | [**apiVversionCompanyIdFatturaElettronicaXmlArchivebyfilenamePost**](docs/Api/FatturaElettronicaXmlApi.md#apivversioncompanyidfatturaelettronicaxmlarchivebyfilenamepost) | **POST** /api/v{version}/{companyId}/FatturaElettronicaXml/archivebyfilename | Scarica un archivio ZIP di fatture XML, filtrate per nome file.
*FatturaElettronicaXmlApi* | [**apiVversionCompanyIdFatturaElettronicaXmlBulkDelete**](docs/Api/FatturaElettronicaXmlApi.md#apivversioncompanyidfatturaelettronicaxmlbulkdelete) | **DELETE** /api/v{version}/{companyId}/FatturaElettronicaXml/bulk | Elimina in blocco tutte le fatture XML dell&#39;azienda.
*FatturaElettronicaXmlApi* | [**apiVversionCompanyIdFatturaElettronicaXmlBulkGet**](docs/Api/FatturaElettronicaXmlApi.md#apivversioncompanyidfatturaelettronicaxmlbulkget) | **GET** /api/v{version}/{companyId}/FatturaElettronicaXml/bulk | Scarica tutte le fatture XML come archivio ZIP.
*FatturaElettronicaXmlApi* | [**apiVversionCompanyIdFatturaElettronicaXmlBulkPost**](docs/Api/FatturaElettronicaXmlApi.md#apivversioncompanyidfatturaelettronicaxmlbulkpost) | **POST** /api/v{version}/{companyId}/FatturaElettronicaXml/bulk | Carica in blocco fatture XML da un archivio ZIP.
*FatturaElettronicaXmlApi* | [**apiVversionCompanyIdFatturaElettronicaXmlExportPost**](docs/Api/FatturaElettronicaXmlApi.md#apivversioncompanyidfatturaelettronicaxmlexportpost) | **POST** /api/v{version}/{companyId}/FatturaElettronicaXml/export | Esporta uno o più documenti in una fattura XML.
*FatturaElettronicaXmlApi* | [**apiVversionCompanyIdFatturaElettronicaXmlFileNameAttachmentsGet**](docs/Api/FatturaElettronicaXmlApi.md#apivversioncompanyidfatturaelettronicaxmlfilenameattachmentsget) | **GET** /api/v{version}/{companyId}/FatturaElettronicaXml/{fileName}/attachments | Scarica i file allegati di una fattura XML.
*FatturaElettronicaXmlApi* | [**apiVversionCompanyIdFatturaElettronicaXmlFileNameGet**](docs/Api/FatturaElettronicaXmlApi.md#apivversioncompanyidfatturaelettronicaxmlfilenameget) | **GET** /api/v{version}/{companyId}/FatturaElettronicaXml/{fileName} | Recupera una fattura XML per nome file.
*FatturaElettronicaXmlApi* | [**apiVversionCompanyIdFatturaElettronicaXmlGet**](docs/Api/FatturaElettronicaXmlApi.md#apivversioncompanyidfatturaelettronicaxmlget) | **GET** /api/v{version}/{companyId}/FatturaElettronicaXml | Restituisce una lista di elementi.
*FatturaElettronicaXmlApi* | [**apiVversionCompanyIdFatturaElettronicaXmlIdDelete**](docs/Api/FatturaElettronicaXmlApi.md#apivversioncompanyidfatturaelettronicaxmliddelete) | **DELETE** /api/v{version}/{companyId}/FatturaElettronicaXml/{id} | Elimina un elemento.
*FatturaElettronicaXmlApi* | [**apiVversionCompanyIdFatturaElettronicaXmlIdGet**](docs/Api/FatturaElettronicaXmlApi.md#apivversioncompanyidfatturaelettronicaxmlidget) | **GET** /api/v{version}/{companyId}/FatturaElettronicaXml/{id} | Restituisce un elemento.
*FatturaElettronicaXmlApi* | [**apiVversionCompanyIdFatturaElettronicaXmlIdPut**](docs/Api/FatturaElettronicaXmlApi.md#apivversioncompanyidfatturaelettronicaxmlidput) | **PUT** /api/v{version}/{companyId}/FatturaElettronicaXml/{id} | Aggiorna un elemento esistente.
*FatturaElettronicaXmlApi* | [**apiVversionCompanyIdFatturaElettronicaXmlImportPost**](docs/Api/FatturaElettronicaXmlApi.md#apivversioncompanyidfatturaelettronicaxmlimportpost) | **POST** /api/v{version}/{companyId}/FatturaElettronicaXml/import | Importa una fattura XML.
*FatturaElettronicaXmlApi* | [**apiVversionCompanyIdFatturaElettronicaXmlNewGet**](docs/Api/FatturaElettronicaXmlApi.md#apivversioncompanyidfatturaelettronicaxmlnewget) | **GET** /api/v{version}/{companyId}/FatturaElettronicaXml/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*FatturaElettronicaXmlApi* | [**apiVversionCompanyIdFatturaElettronicaXmlPairPost**](docs/Api/FatturaElettronicaXmlApi.md#apivversioncompanyidfatturaelettronicaxmlpairpost) | **POST** /api/v{version}/{companyId}/FatturaElettronicaXml/pair | Associa una fattura XML a un documento esistente.
*FatturaElettronicaXmlApi* | [**apiVversionCompanyIdFatturaElettronicaXmlPost**](docs/Api/FatturaElettronicaXmlApi.md#apivversioncompanyidfatturaelettronicaxmlpost) | **POST** /api/v{version}/{companyId}/FatturaElettronicaXml | Salva un nuovo elemento.
*FatturaElettronicaXmlApi* | [**apiVversionCompanyIdFatturaElettronicaXmlSdiDownloadEsitiPost**](docs/Api/FatturaElettronicaXmlApi.md#apivversioncompanyidfatturaelettronicaxmlsdidownloadesitipost) | **POST** /api/v{version}/{companyId}/FatturaElettronicaXml/sdi/download/esiti | Interroga il Sistema di Interscambio (SDI) e scarica gli esiti di invio delle fatture precedentemente caricate.
*FatturaElettronicaXmlApi* | [**apiVversionCompanyIdFatturaElettronicaXmlSdiDownloadFatturePost**](docs/Api/FatturaElettronicaXmlApi.md#apivversioncompanyidfatturaelettronicaxmlsdidownloadfatturepost) | **POST** /api/v{version}/{companyId}/FatturaElettronicaXml/sdi/download/fatture | Scarica le nuove fatture dal Sistema di Interscambio (SDI).
*FatturaElettronicaXmlApi* | [**apiVversionCompanyIdFatturaElettronicaXmlSdiUploadPost**](docs/Api/FatturaElettronicaXmlApi.md#apivversioncompanyidfatturaelettronicaxmlsdiuploadpost) | **POST** /api/v{version}/{companyId}/FatturaElettronicaXml/sdi/upload | Invia una o più fatture al Sistema di Interscambio (SDI).
*FatturaElettronicaXmlApi* | [**apiVversionCompanyIdFatturaElettronicaXmlStylesheetPost**](docs/Api/FatturaElettronicaXmlApi.md#apivversioncompanyidfatturaelettronicaxmlstylesheetpost) | **POST** /api/v{version}/{companyId}/FatturaElettronicaXml/stylesheet | Applica un foglio di stile CSS a una fattura XML esistente e restituisce il risultato.
*FatturaElettronicaXmlTemplateApi* | [**apiVversionCompanyIdFatturaElettronicaXmlTemplateGet**](docs/Api/FatturaElettronicaXmlTemplateApi.md#apivversioncompanyidfatturaelettronicaxmltemplateget) | **GET** /api/v{version}/{companyId}/FatturaElettronicaXmlTemplate | Restituisce una lista di elementi.
*FatturaElettronicaXmlTemplateApi* | [**apiVversionCompanyIdFatturaElettronicaXmlTemplateIdDelete**](docs/Api/FatturaElettronicaXmlTemplateApi.md#apivversioncompanyidfatturaelettronicaxmltemplateiddelete) | **DELETE** /api/v{version}/{companyId}/FatturaElettronicaXmlTemplate/{id} | Elimina un elemento.
*FatturaElettronicaXmlTemplateApi* | [**apiVversionCompanyIdFatturaElettronicaXmlTemplateIdGet**](docs/Api/FatturaElettronicaXmlTemplateApi.md#apivversioncompanyidfatturaelettronicaxmltemplateidget) | **GET** /api/v{version}/{companyId}/FatturaElettronicaXmlTemplate/{id} | Restituisce un elemento.
*FatturaElettronicaXmlTemplateApi* | [**apiVversionCompanyIdFatturaElettronicaXmlTemplateIdPut**](docs/Api/FatturaElettronicaXmlTemplateApi.md#apivversioncompanyidfatturaelettronicaxmltemplateidput) | **PUT** /api/v{version}/{companyId}/FatturaElettronicaXmlTemplate/{id} | Aggiorna un elemento esistente.
*FatturaElettronicaXmlTemplateApi* | [**apiVversionCompanyIdFatturaElettronicaXmlTemplateNewGet**](docs/Api/FatturaElettronicaXmlTemplateApi.md#apivversioncompanyidfatturaelettronicaxmltemplatenewget) | **GET** /api/v{version}/{companyId}/FatturaElettronicaXmlTemplate/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*FatturaElettronicaXmlTemplateApi* | [**apiVversionCompanyIdFatturaElettronicaXmlTemplatePost**](docs/Api/FatturaElettronicaXmlTemplateApi.md#apivversioncompanyidfatturaelettronicaxmltemplatepost) | **POST** /api/v{version}/{companyId}/FatturaElettronicaXmlTemplate | Salva un nuovo elemento.
*GiacenzaApi* | [**apiVversionCompanyIdGiacenzaDistinctGet**](docs/Api/GiacenzaApi.md#apivversioncompanyidgiacenzadistinctget) | **GET** /api/v{version}/{companyId}/Giacenza/distinct | Restituisce una lista di elementi distinti.
*GiacenzaApi* | [**apiVversionCompanyIdGiacenzaGet**](docs/Api/GiacenzaApi.md#apivversioncompanyidgiacenzaget) | **GET** /api/v{version}/{companyId}/Giacenza | Restituisce una lista di elementi.
*GiacenzaApi* | [**apiVversionCompanyIdGiacenzaIdDelete**](docs/Api/GiacenzaApi.md#apivversioncompanyidgiacenzaiddelete) | **DELETE** /api/v{version}/{companyId}/Giacenza/{id} | Elimina un elemento.
*GiacenzaApi* | [**apiVversionCompanyIdGiacenzaIdGet**](docs/Api/GiacenzaApi.md#apivversioncompanyidgiacenzaidget) | **GET** /api/v{version}/{companyId}/Giacenza/{id} | Restituisce un elemento.
*GiacenzaApi* | [**apiVversionCompanyIdGiacenzaIdPut**](docs/Api/GiacenzaApi.md#apivversioncompanyidgiacenzaidput) | **PUT** /api/v{version}/{companyId}/Giacenza/{id} | Aggiorna un elemento esistente.
*GiacenzaApi* | [**apiVversionCompanyIdGiacenzaNewGet**](docs/Api/GiacenzaApi.md#apivversioncompanyidgiacenzanewget) | **GET** /api/v{version}/{companyId}/Giacenza/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*GiacenzaApi* | [**apiVversionCompanyIdGiacenzaPost**](docs/Api/GiacenzaApi.md#apivversioncompanyidgiacenzapost) | **POST** /api/v{version}/{companyId}/Giacenza | Salva un nuovo elemento.
*GiornaleMagazzinoApi* | [**apiVversionCompanyIdGiornaleMagazzinoGet**](docs/Api/GiornaleMagazzinoApi.md#apivversioncompanyidgiornalemagazzinoget) | **GET** /api/v{version}/{companyId}/GiornaleMagazzino | Restituisce una lista di elementi.
*GiornaleMagazzinoApi* | [**apiVversionCompanyIdGiornaleMagazzinoIdDelete**](docs/Api/GiornaleMagazzinoApi.md#apivversioncompanyidgiornalemagazzinoiddelete) | **DELETE** /api/v{version}/{companyId}/GiornaleMagazzino/{id} | Elimina un elemento.
*GiornaleMagazzinoApi* | [**apiVversionCompanyIdGiornaleMagazzinoIdGet**](docs/Api/GiornaleMagazzinoApi.md#apivversioncompanyidgiornalemagazzinoidget) | **GET** /api/v{version}/{companyId}/GiornaleMagazzino/{id} | Restituisce un elemento.
*GiornaleMagazzinoApi* | [**apiVversionCompanyIdGiornaleMagazzinoIdPut**](docs/Api/GiornaleMagazzinoApi.md#apivversioncompanyidgiornalemagazzinoidput) | **PUT** /api/v{version}/{companyId}/GiornaleMagazzino/{id} | Aggiorna un elemento esistente.
*GiornaleMagazzinoApi* | [**apiVversionCompanyIdGiornaleMagazzinoNewGet**](docs/Api/GiornaleMagazzinoApi.md#apivversioncompanyidgiornalemagazzinonewget) | **GET** /api/v{version}/{companyId}/GiornaleMagazzino/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*GiornaleMagazzinoApi* | [**apiVversionCompanyIdGiornaleMagazzinoPost**](docs/Api/GiornaleMagazzinoApi.md#apivversioncompanyidgiornalemagazzinopost) | **POST** /api/v{version}/{companyId}/GiornaleMagazzino | Salva un nuovo elemento.
*GruppoMerceologicoApi* | [**apiVversionCompanyIdGruppoMerceologicoGet**](docs/Api/GruppoMerceologicoApi.md#apivversioncompanyidgruppomerceologicoget) | **GET** /api/v{version}/{companyId}/GruppoMerceologico | Restituisce una lista di elementi.
*GruppoMerceologicoApi* | [**apiVversionCompanyIdGruppoMerceologicoIdDelete**](docs/Api/GruppoMerceologicoApi.md#apivversioncompanyidgruppomerceologicoiddelete) | **DELETE** /api/v{version}/{companyId}/GruppoMerceologico/{id} | Elimina un elemento.
*GruppoMerceologicoApi* | [**apiVversionCompanyIdGruppoMerceologicoIdGet**](docs/Api/GruppoMerceologicoApi.md#apivversioncompanyidgruppomerceologicoidget) | **GET** /api/v{version}/{companyId}/GruppoMerceologico/{id} | Restituisce un elemento.
*GruppoMerceologicoApi* | [**apiVversionCompanyIdGruppoMerceologicoIdPut**](docs/Api/GruppoMerceologicoApi.md#apivversioncompanyidgruppomerceologicoidput) | **PUT** /api/v{version}/{companyId}/GruppoMerceologico/{id} | Aggiorna un elemento esistente.
*GruppoMerceologicoApi* | [**apiVversionCompanyIdGruppoMerceologicoNewGet**](docs/Api/GruppoMerceologicoApi.md#apivversioncompanyidgruppomerceologiconewget) | **GET** /api/v{version}/{companyId}/GruppoMerceologico/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*GruppoMerceologicoApi* | [**apiVversionCompanyIdGruppoMerceologicoPost**](docs/Api/GruppoMerceologicoApi.md#apivversioncompanyidgruppomerceologicopost) | **POST** /api/v{version}/{companyId}/GruppoMerceologico | Salva un nuovo elemento.
*IndirizzoApi* | [**apiVversionCompanyIdIndirizzoGet**](docs/Api/IndirizzoApi.md#apivversioncompanyidindirizzoget) | **GET** /api/v{version}/{companyId}/Indirizzo | Restituisce una lista di elementi.
*IndirizzoApi* | [**apiVversionCompanyIdIndirizzoIdDelete**](docs/Api/IndirizzoApi.md#apivversioncompanyidindirizzoiddelete) | **DELETE** /api/v{version}/{companyId}/Indirizzo/{id} | Elimina un elemento.
*IndirizzoApi* | [**apiVversionCompanyIdIndirizzoIdGet**](docs/Api/IndirizzoApi.md#apivversioncompanyidindirizzoidget) | **GET** /api/v{version}/{companyId}/Indirizzo/{id} | Restituisce un elemento.
*IndirizzoApi* | [**apiVversionCompanyIdIndirizzoIdPut**](docs/Api/IndirizzoApi.md#apivversioncompanyidindirizzoidput) | **PUT** /api/v{version}/{companyId}/Indirizzo/{id} | Aggiorna un elemento esistente.
*IndirizzoApi* | [**apiVversionCompanyIdIndirizzoNewGet**](docs/Api/IndirizzoApi.md#apivversioncompanyidindirizzonewget) | **GET** /api/v{version}/{companyId}/Indirizzo/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*IndirizzoApi* | [**apiVversionCompanyIdIndirizzoPost**](docs/Api/IndirizzoApi.md#apivversioncompanyidindirizzopost) | **POST** /api/v{version}/{companyId}/Indirizzo | Salva un nuovo elemento.
*ListinoApi* | [**apiVversionCompanyIdListinoGet**](docs/Api/ListinoApi.md#apivversioncompanyidlistinoget) | **GET** /api/v{version}/{companyId}/Listino | Restituisce una lista di elementi.
*ListinoApi* | [**apiVversionCompanyIdListinoIdDelete**](docs/Api/ListinoApi.md#apivversioncompanyidlistinoiddelete) | **DELETE** /api/v{version}/{companyId}/Listino/{id} | Elimina un elemento.
*ListinoApi* | [**apiVversionCompanyIdListinoIdGet**](docs/Api/ListinoApi.md#apivversioncompanyidlistinoidget) | **GET** /api/v{version}/{companyId}/Listino/{id} | Restituisce un elemento.
*ListinoApi* | [**apiVversionCompanyIdListinoIdPut**](docs/Api/ListinoApi.md#apivversioncompanyidlistinoidput) | **PUT** /api/v{version}/{companyId}/Listino/{id} | Aggiorna un elemento esistente.
*ListinoApi* | [**apiVversionCompanyIdListinoNewGet**](docs/Api/ListinoApi.md#apivversioncompanyidlistinonewget) | **GET** /api/v{version}/{companyId}/Listino/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*ListinoApi* | [**apiVversionCompanyIdListinoPost**](docs/Api/ListinoApi.md#apivversioncompanyidlistinopost) | **POST** /api/v{version}/{companyId}/Listino | Salva un nuovo elemento.
*MagazzinoApi* | [**apiVversionCompanyIdMagazzinoGet**](docs/Api/MagazzinoApi.md#apivversioncompanyidmagazzinoget) | **GET** /api/v{version}/{companyId}/Magazzino | Restituisce una lista di elementi.
*MagazzinoApi* | [**apiVversionCompanyIdMagazzinoIdDelete**](docs/Api/MagazzinoApi.md#apivversioncompanyidmagazzinoiddelete) | **DELETE** /api/v{version}/{companyId}/Magazzino/{id} | Elimina un elemento.
*MagazzinoApi* | [**apiVversionCompanyIdMagazzinoIdGet**](docs/Api/MagazzinoApi.md#apivversioncompanyidmagazzinoidget) | **GET** /api/v{version}/{companyId}/Magazzino/{id} | Restituisce un elemento.
*MagazzinoApi* | [**apiVversionCompanyIdMagazzinoIdPut**](docs/Api/MagazzinoApi.md#apivversioncompanyidmagazzinoidput) | **PUT** /api/v{version}/{companyId}/Magazzino/{id} | Aggiorna un elemento esistente.
*MagazzinoApi* | [**apiVversionCompanyIdMagazzinoNewGet**](docs/Api/MagazzinoApi.md#apivversioncompanyidmagazzinonewget) | **GET** /api/v{version}/{companyId}/Magazzino/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*MagazzinoApi* | [**apiVversionCompanyIdMagazzinoPost**](docs/Api/MagazzinoApi.md#apivversioncompanyidmagazzinopost) | **POST** /api/v{version}/{companyId}/Magazzino | Salva un nuovo elemento.
*MarcaApi* | [**apiVversionCompanyIdMarcaGet**](docs/Api/MarcaApi.md#apivversioncompanyidmarcaget) | **GET** /api/v{version}/{companyId}/Marca | Restituisce una lista di elementi.
*MarcaApi* | [**apiVversionCompanyIdMarcaIdDelete**](docs/Api/MarcaApi.md#apivversioncompanyidmarcaiddelete) | **DELETE** /api/v{version}/{companyId}/Marca/{id} | Elimina un elemento.
*MarcaApi* | [**apiVversionCompanyIdMarcaIdGet**](docs/Api/MarcaApi.md#apivversioncompanyidmarcaidget) | **GET** /api/v{version}/{companyId}/Marca/{id} | Restituisce un elemento.
*MarcaApi* | [**apiVversionCompanyIdMarcaIdPut**](docs/Api/MarcaApi.md#apivversioncompanyidmarcaidput) | **PUT** /api/v{version}/{companyId}/Marca/{id} | Aggiorna un elemento esistente.
*MarcaApi* | [**apiVversionCompanyIdMarcaNewGet**](docs/Api/MarcaApi.md#apivversioncompanyidmarcanewget) | **GET** /api/v{version}/{companyId}/Marca/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*MarcaApi* | [**apiVversionCompanyIdMarcaPost**](docs/Api/MarcaApi.md#apivversioncompanyidmarcapost) | **POST** /api/v{version}/{companyId}/Marca | Salva un nuovo elemento.
*ModalitaPagamentoApi* | [**apiVversionCompanyIdModalitaPagamentoDistinctGet**](docs/Api/ModalitaPagamentoApi.md#apivversioncompanyidmodalitapagamentodistinctget) | **GET** /api/v{version}/{companyId}/ModalitaPagamento/distinct | Restituisce una lista di elementi distinti.
*ModalitaPagamentoApi* | [**apiVversionCompanyIdModalitaPagamentoGet**](docs/Api/ModalitaPagamentoApi.md#apivversioncompanyidmodalitapagamentoget) | **GET** /api/v{version}/{companyId}/ModalitaPagamento | Restituisce una lista di elementi.
*ModalitaPagamentoApi* | [**apiVversionCompanyIdModalitaPagamentoIdDelete**](docs/Api/ModalitaPagamentoApi.md#apivversioncompanyidmodalitapagamentoiddelete) | **DELETE** /api/v{version}/{companyId}/ModalitaPagamento/{id} | Elimina un elemento.
*ModalitaPagamentoApi* | [**apiVversionCompanyIdModalitaPagamentoIdGet**](docs/Api/ModalitaPagamentoApi.md#apivversioncompanyidmodalitapagamentoidget) | **GET** /api/v{version}/{companyId}/ModalitaPagamento/{id} | Restituisce un elemento.
*ModalitaPagamentoApi* | [**apiVversionCompanyIdModalitaPagamentoIdPut**](docs/Api/ModalitaPagamentoApi.md#apivversioncompanyidmodalitapagamentoidput) | **PUT** /api/v{version}/{companyId}/ModalitaPagamento/{id} | Aggiorna un elemento esistente.
*ModalitaPagamentoApi* | [**apiVversionCompanyIdModalitaPagamentoNewGet**](docs/Api/ModalitaPagamentoApi.md#apivversioncompanyidmodalitapagamentonewget) | **GET** /api/v{version}/{companyId}/ModalitaPagamento/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*ModalitaPagamentoApi* | [**apiVversionCompanyIdModalitaPagamentoPost**](docs/Api/ModalitaPagamentoApi.md#apivversioncompanyidmodalitapagamentopost) | **POST** /api/v{version}/{companyId}/ModalitaPagamento | Salva un nuovo elemento.
*ModelloContabileApi* | [**apiVversionCompanyIdModelloContabileGet**](docs/Api/ModelloContabileApi.md#apivversioncompanyidmodellocontabileget) | **GET** /api/v{version}/{companyId}/ModelloContabile | Restituisce una lista di elementi.
*ModelloContabileApi* | [**apiVversionCompanyIdModelloContabileIdDelete**](docs/Api/ModelloContabileApi.md#apivversioncompanyidmodellocontabileiddelete) | **DELETE** /api/v{version}/{companyId}/ModelloContabile/{id} | Elimina un elemento.
*ModelloContabileApi* | [**apiVversionCompanyIdModelloContabileIdGet**](docs/Api/ModelloContabileApi.md#apivversioncompanyidmodellocontabileidget) | **GET** /api/v{version}/{companyId}/ModelloContabile/{id} | Restituisce un elemento.
*ModelloContabileApi* | [**apiVversionCompanyIdModelloContabileIdPut**](docs/Api/ModelloContabileApi.md#apivversioncompanyidmodellocontabileidput) | **PUT** /api/v{version}/{companyId}/ModelloContabile/{id} | Aggiorna un elemento esistente.
*ModelloContabileApi* | [**apiVversionCompanyIdModelloContabileNewGet**](docs/Api/ModelloContabileApi.md#apivversioncompanyidmodellocontabilenewget) | **GET** /api/v{version}/{companyId}/ModelloContabile/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*ModelloContabileApi* | [**apiVversionCompanyIdModelloContabilePost**](docs/Api/ModelloContabileApi.md#apivversioncompanyidmodellocontabilepost) | **POST** /api/v{version}/{companyId}/ModelloContabile | Salva un nuovo elemento.
*MovimentoMerceApi* | [**apiVversionCompanyIdMovimentoMerceGet**](docs/Api/MovimentoMerceApi.md#apivversioncompanyidmovimentomerceget) | **GET** /api/v{version}/{companyId}/MovimentoMerce | Restituisce una lista di elementi.
*MovimentoMerceApi* | [**apiVversionCompanyIdMovimentoMerceIdDelete**](docs/Api/MovimentoMerceApi.md#apivversioncompanyidmovimentomerceiddelete) | **DELETE** /api/v{version}/{companyId}/MovimentoMerce/{id} | Elimina un elemento.
*MovimentoMerceApi* | [**apiVversionCompanyIdMovimentoMerceIdGet**](docs/Api/MovimentoMerceApi.md#apivversioncompanyidmovimentomerceidget) | **GET** /api/v{version}/{companyId}/MovimentoMerce/{id} | Restituisce un elemento.
*MovimentoMerceApi* | [**apiVversionCompanyIdMovimentoMerceIdPut**](docs/Api/MovimentoMerceApi.md#apivversioncompanyidmovimentomerceidput) | **PUT** /api/v{version}/{companyId}/MovimentoMerce/{id} | Aggiorna un elemento esistente.
*MovimentoMerceApi* | [**apiVversionCompanyIdMovimentoMerceNewGet**](docs/Api/MovimentoMerceApi.md#apivversioncompanyidmovimentomercenewget) | **GET** /api/v{version}/{companyId}/MovimentoMerce/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*MovimentoMerceApi* | [**apiVversionCompanyIdMovimentoMercePost**](docs/Api/MovimentoMerceApi.md#apivversioncompanyidmovimentomercepost) | **POST** /api/v{version}/{companyId}/MovimentoMerce | Salva un nuovo elemento.
*NaturaIvaApi* | [**apiVversionCompanyIdTabelleNaturaIvaGet**](docs/Api/NaturaIvaApi.md#apivversioncompanyidtabellenaturaivaget) | **GET** /api/v{version}/{companyId}/tabelle/NaturaIva | Restituisce il contenuto della tabella come lista di elementi.
*NazioneApi* | [**apiVversionCompanyIdNazioneDistinctGet**](docs/Api/NazioneApi.md#apivversioncompanyidnazionedistinctget) | **GET** /api/v{version}/{companyId}/Nazione/distinct | Restituisce una lista di elementi distinti.
*NazioneApi* | [**apiVversionCompanyIdNazioneGet**](docs/Api/NazioneApi.md#apivversioncompanyidnazioneget) | **GET** /api/v{version}/{companyId}/Nazione | Restituisce una lista di elementi.
*NazioneApi* | [**apiVversionCompanyIdNazioneIdDelete**](docs/Api/NazioneApi.md#apivversioncompanyidnazioneiddelete) | **DELETE** /api/v{version}/{companyId}/Nazione/{id} | Elimina un elemento.
*NazioneApi* | [**apiVversionCompanyIdNazioneIdGet**](docs/Api/NazioneApi.md#apivversioncompanyidnazioneidget) | **GET** /api/v{version}/{companyId}/Nazione/{id} | Restituisce un elemento.
*NazioneApi* | [**apiVversionCompanyIdNazioneIdPut**](docs/Api/NazioneApi.md#apivversioncompanyidnazioneidput) | **PUT** /api/v{version}/{companyId}/Nazione/{id} | Aggiorna un elemento esistente.
*NazioneApi* | [**apiVversionCompanyIdNazioneNewGet**](docs/Api/NazioneApi.md#apivversioncompanyidnazionenewget) | **GET** /api/v{version}/{companyId}/Nazione/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*NazioneApi* | [**apiVversionCompanyIdNazionePost**](docs/Api/NazioneApi.md#apivversioncompanyidnazionepost) | **POST** /api/v{version}/{companyId}/Nazione | Salva un nuovo elemento.
*PagamentoApi* | [**apiVversionCompanyIdPagamentoDistinctGet**](docs/Api/PagamentoApi.md#apivversioncompanyidpagamentodistinctget) | **GET** /api/v{version}/{companyId}/Pagamento/distinct | Restituisce una lista di elementi distinti.
*PagamentoApi* | [**apiVversionCompanyIdPagamentoGet**](docs/Api/PagamentoApi.md#apivversioncompanyidpagamentoget) | **GET** /api/v{version}/{companyId}/Pagamento | Restituisce una lista di elementi.
*PagamentoApi* | [**apiVversionCompanyIdPagamentoIdDelete**](docs/Api/PagamentoApi.md#apivversioncompanyidpagamentoiddelete) | **DELETE** /api/v{version}/{companyId}/Pagamento/{id} | Elimina un elemento.
*PagamentoApi* | [**apiVversionCompanyIdPagamentoIdGet**](docs/Api/PagamentoApi.md#apivversioncompanyidpagamentoidget) | **GET** /api/v{version}/{companyId}/Pagamento/{id} | Restituisce un elemento.
*PagamentoApi* | [**apiVversionCompanyIdPagamentoIdPut**](docs/Api/PagamentoApi.md#apivversioncompanyidpagamentoidput) | **PUT** /api/v{version}/{companyId}/Pagamento/{id} | Aggiorna un elemento esistente.
*PagamentoApi* | [**apiVversionCompanyIdPagamentoNewGet**](docs/Api/PagamentoApi.md#apivversioncompanyidpagamentonewget) | **GET** /api/v{version}/{companyId}/Pagamento/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*PagamentoApi* | [**apiVversionCompanyIdPagamentoPost**](docs/Api/PagamentoApi.md#apivversioncompanyidpagamentopost) | **POST** /api/v{version}/{companyId}/Pagamento | Salva un nuovo elemento.
*PreferenzeApi* | [**apiVversionCompanyIdPreferenzeGet**](docs/Api/PreferenzeApi.md#apivversioncompanyidpreferenzeget) | **GET** /api/v{version}/{companyId}/Preferenze | Restituisce una lista di elementi.
*PreferenzeApi* | [**apiVversionCompanyIdPreferenzeIdDelete**](docs/Api/PreferenzeApi.md#apivversioncompanyidpreferenzeiddelete) | **DELETE** /api/v{version}/{companyId}/Preferenze/{id} | Elimina un elemento.
*PreferenzeApi* | [**apiVversionCompanyIdPreferenzeIdGet**](docs/Api/PreferenzeApi.md#apivversioncompanyidpreferenzeidget) | **GET** /api/v{version}/{companyId}/Preferenze/{id} | Restituisce un elemento.
*PreferenzeApi* | [**apiVversionCompanyIdPreferenzeIdPut**](docs/Api/PreferenzeApi.md#apivversioncompanyidpreferenzeidput) | **PUT** /api/v{version}/{companyId}/Preferenze/{id} | Aggiorna un elemento esistente.
*PreferenzeApi* | [**apiVversionCompanyIdPreferenzeNewGet**](docs/Api/PreferenzeApi.md#apivversioncompanyidpreferenzenewget) | **GET** /api/v{version}/{companyId}/Preferenze/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*PreferenzeApi* | [**apiVversionCompanyIdPreferenzePost**](docs/Api/PreferenzeApi.md#apivversioncompanyidpreferenzepost) | **POST** /api/v{version}/{companyId}/Preferenze | Salva un nuovo elemento.
*PrestazioneApi* | [**apiVversionCompanyIdPrestazioneGet**](docs/Api/PrestazioneApi.md#apivversioncompanyidprestazioneget) | **GET** /api/v{version}/{companyId}/Prestazione | Restituisce una lista di elementi.
*PrestazioneApi* | [**apiVversionCompanyIdPrestazioneIdDelete**](docs/Api/PrestazioneApi.md#apivversioncompanyidprestazioneiddelete) | **DELETE** /api/v{version}/{companyId}/Prestazione/{id} | Elimina un elemento.
*PrestazioneApi* | [**apiVversionCompanyIdPrestazioneIdGet**](docs/Api/PrestazioneApi.md#apivversioncompanyidprestazioneidget) | **GET** /api/v{version}/{companyId}/Prestazione/{id} | Restituisce un elemento.
*PrestazioneApi* | [**apiVversionCompanyIdPrestazioneIdPut**](docs/Api/PrestazioneApi.md#apivversioncompanyidprestazioneidput) | **PUT** /api/v{version}/{companyId}/Prestazione/{id} | Aggiorna un elemento esistente.
*PrestazioneApi* | [**apiVversionCompanyIdPrestazioneNewGet**](docs/Api/PrestazioneApi.md#apivversioncompanyidprestazionenewget) | **GET** /api/v{version}/{companyId}/Prestazione/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*PrestazioneApi* | [**apiVversionCompanyIdPrestazionePost**](docs/Api/PrestazioneApi.md#apivversioncompanyidprestazionepost) | **POST** /api/v{version}/{companyId}/Prestazione | Salva un nuovo elemento.
*PrezzoApi* | [**apiVversionCompanyIdPrezzoGet**](docs/Api/PrezzoApi.md#apivversioncompanyidprezzoget) | **GET** /api/v{version}/{companyId}/Prezzo | Restituisce una lista di elementi.
*PrezzoApi* | [**apiVversionCompanyIdPrezzoIdDelete**](docs/Api/PrezzoApi.md#apivversioncompanyidprezzoiddelete) | **DELETE** /api/v{version}/{companyId}/Prezzo/{id} | Elimina un elemento.
*PrezzoApi* | [**apiVversionCompanyIdPrezzoIdGet**](docs/Api/PrezzoApi.md#apivversioncompanyidprezzoidget) | **GET** /api/v{version}/{companyId}/Prezzo/{id} | Restituisce un elemento.
*PrezzoApi* | [**apiVversionCompanyIdPrezzoIdPut**](docs/Api/PrezzoApi.md#apivversioncompanyidprezzoidput) | **PUT** /api/v{version}/{companyId}/Prezzo/{id} | Aggiorna un elemento esistente.
*PrezzoApi* | [**apiVversionCompanyIdPrezzoNewGet**](docs/Api/PrezzoApi.md#apivversioncompanyidprezzonewget) | **GET** /api/v{version}/{companyId}/Prezzo/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*PrezzoApi* | [**apiVversionCompanyIdPrezzoPost**](docs/Api/PrezzoApi.md#apivversioncompanyidprezzopost) | **POST** /api/v{version}/{companyId}/Prezzo | Salva un nuovo elemento.
*ProduttoreApi* | [**apiVversionCompanyIdProduttoreGet**](docs/Api/ProduttoreApi.md#apivversioncompanyidproduttoreget) | **GET** /api/v{version}/{companyId}/Produttore | Restituisce una lista di elementi.
*ProduttoreApi* | [**apiVversionCompanyIdProduttoreIdDelete**](docs/Api/ProduttoreApi.md#apivversioncompanyidproduttoreiddelete) | **DELETE** /api/v{version}/{companyId}/Produttore/{id} | Elimina un elemento.
*ProduttoreApi* | [**apiVversionCompanyIdProduttoreIdGet**](docs/Api/ProduttoreApi.md#apivversioncompanyidproduttoreidget) | **GET** /api/v{version}/{companyId}/Produttore/{id} | Restituisce un elemento.
*ProduttoreApi* | [**apiVversionCompanyIdProduttoreIdPut**](docs/Api/ProduttoreApi.md#apivversioncompanyidproduttoreidput) | **PUT** /api/v{version}/{companyId}/Produttore/{id} | Aggiorna un elemento esistente.
*ProduttoreApi* | [**apiVversionCompanyIdProduttoreNewGet**](docs/Api/ProduttoreApi.md#apivversioncompanyidproduttorenewget) | **GET** /api/v{version}/{companyId}/Produttore/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*ProduttoreApi* | [**apiVversionCompanyIdProduttorePost**](docs/Api/ProduttoreApi.md#apivversioncompanyidproduttorepost) | **POST** /api/v{version}/{companyId}/Produttore | Salva un nuovo elemento.
*ProgressivoInvioApi* | [**apiVversionCompanyIdProgressivoInvioGet**](docs/Api/ProgressivoInvioApi.md#apivversioncompanyidprogressivoinvioget) | **GET** /api/v{version}/{companyId}/ProgressivoInvio | Restituisce una lista di elementi.
*ProgressivoInvioApi* | [**apiVversionCompanyIdProgressivoInvioIdDelete**](docs/Api/ProgressivoInvioApi.md#apivversioncompanyidprogressivoinvioiddelete) | **DELETE** /api/v{version}/{companyId}/ProgressivoInvio/{id} | Elimina un elemento.
*ProgressivoInvioApi* | [**apiVversionCompanyIdProgressivoInvioIdGet**](docs/Api/ProgressivoInvioApi.md#apivversioncompanyidprogressivoinvioidget) | **GET** /api/v{version}/{companyId}/ProgressivoInvio/{id} | Restituisce un elemento.
*ProgressivoInvioApi* | [**apiVversionCompanyIdProgressivoInvioIdPut**](docs/Api/ProgressivoInvioApi.md#apivversioncompanyidprogressivoinvioidput) | **PUT** /api/v{version}/{companyId}/ProgressivoInvio/{id} | Aggiorna un elemento esistente.
*ProgressivoInvioApi* | [**apiVversionCompanyIdProgressivoInvioNewGet**](docs/Api/ProgressivoInvioApi.md#apivversioncompanyidprogressivoinvionewget) | **GET** /api/v{version}/{companyId}/ProgressivoInvio/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*ProgressivoInvioApi* | [**apiVversionCompanyIdProgressivoInvioPost**](docs/Api/ProgressivoInvioApi.md#apivversioncompanyidprogressivoinviopost) | **POST** /api/v{version}/{companyId}/ProgressivoInvio | Salva un nuovo elemento.
*RegistrazioneContabileApi* | [**apiVversionCompanyIdRegistrazioneContabileGet**](docs/Api/RegistrazioneContabileApi.md#apivversioncompanyidregistrazionecontabileget) | **GET** /api/v{version}/{companyId}/RegistrazioneContabile | Restituisce una lista di elementi.
*RegistrazioneContabileApi* | [**apiVversionCompanyIdRegistrazioneContabileIdDelete**](docs/Api/RegistrazioneContabileApi.md#apivversioncompanyidregistrazionecontabileiddelete) | **DELETE** /api/v{version}/{companyId}/RegistrazioneContabile/{id} | Elimina un elemento.
*RegistrazioneContabileApi* | [**apiVversionCompanyIdRegistrazioneContabileIdGet**](docs/Api/RegistrazioneContabileApi.md#apivversioncompanyidregistrazionecontabileidget) | **GET** /api/v{version}/{companyId}/RegistrazioneContabile/{id} | Restituisce un elemento.
*RegistrazioneContabileApi* | [**apiVversionCompanyIdRegistrazioneContabileIdPut**](docs/Api/RegistrazioneContabileApi.md#apivversioncompanyidregistrazionecontabileidput) | **PUT** /api/v{version}/{companyId}/RegistrazioneContabile/{id} | Aggiorna un elemento esistente.
*RegistrazioneContabileApi* | [**apiVversionCompanyIdRegistrazioneContabileNewGet**](docs/Api/RegistrazioneContabileApi.md#apivversioncompanyidregistrazionecontabilenewget) | **GET** /api/v{version}/{companyId}/RegistrazioneContabile/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*RegistrazioneContabileApi* | [**apiVversionCompanyIdRegistrazioneContabilePost**](docs/Api/RegistrazioneContabileApi.md#apivversioncompanyidregistrazionecontabilepost) | **POST** /api/v{version}/{companyId}/RegistrazioneContabile | Salva un nuovo elemento.
*RegistrazioneContabileApi* | [**apiVversionCompanyIdRegistrazioneContabileValidatePost**](docs/Api/RegistrazioneContabileApi.md#apivversioncompanyidregistrazionecontabilevalidatepost) | **POST** /api/v{version}/{companyId}/RegistrazioneContabile/validate | Valida le registrazioni contabili di un anno, cercando principalmente squilibri contabili.
*RepartoApi* | [**apiVversionCompanyIdRepartoGet**](docs/Api/RepartoApi.md#apivversioncompanyidrepartoget) | **GET** /api/v{version}/{companyId}/Reparto | Restituisce una lista di elementi.
*RepartoApi* | [**apiVversionCompanyIdRepartoIdDelete**](docs/Api/RepartoApi.md#apivversioncompanyidrepartoiddelete) | **DELETE** /api/v{version}/{companyId}/Reparto/{id} | Elimina un elemento.
*RepartoApi* | [**apiVversionCompanyIdRepartoIdGet**](docs/Api/RepartoApi.md#apivversioncompanyidrepartoidget) | **GET** /api/v{version}/{companyId}/Reparto/{id} | Restituisce un elemento.
*RepartoApi* | [**apiVversionCompanyIdRepartoIdPut**](docs/Api/RepartoApi.md#apivversioncompanyidrepartoidput) | **PUT** /api/v{version}/{companyId}/Reparto/{id} | Aggiorna un elemento esistente.
*RepartoApi* | [**apiVversionCompanyIdRepartoNewGet**](docs/Api/RepartoApi.md#apivversioncompanyidrepartonewget) | **GET** /api/v{version}/{companyId}/Reparto/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*RepartoApi* | [**apiVversionCompanyIdRepartoPost**](docs/Api/RepartoApi.md#apivversioncompanyidrepartopost) | **POST** /api/v{version}/{companyId}/Reparto | Salva un nuovo elemento.
*RisorsaFinanziariaApi* | [**apiVversionCompanyIdRisorsaFinanziariaGet**](docs/Api/RisorsaFinanziariaApi.md#apivversioncompanyidrisorsafinanziariaget) | **GET** /api/v{version}/{companyId}/RisorsaFinanziaria | Restituisce una lista di elementi.
*RisorsaFinanziariaApi* | [**apiVversionCompanyIdRisorsaFinanziariaIdDelete**](docs/Api/RisorsaFinanziariaApi.md#apivversioncompanyidrisorsafinanziariaiddelete) | **DELETE** /api/v{version}/{companyId}/RisorsaFinanziaria/{id} | Elimina un elemento.
*RisorsaFinanziariaApi* | [**apiVversionCompanyIdRisorsaFinanziariaIdGet**](docs/Api/RisorsaFinanziariaApi.md#apivversioncompanyidrisorsafinanziariaidget) | **GET** /api/v{version}/{companyId}/RisorsaFinanziaria/{id} | Restituisce un elemento.
*RisorsaFinanziariaApi* | [**apiVversionCompanyIdRisorsaFinanziariaIdPut**](docs/Api/RisorsaFinanziariaApi.md#apivversioncompanyidrisorsafinanziariaidput) | **PUT** /api/v{version}/{companyId}/RisorsaFinanziaria/{id} | Aggiorna un elemento esistente.
*RisorsaFinanziariaApi* | [**apiVversionCompanyIdRisorsaFinanziariaNewGet**](docs/Api/RisorsaFinanziariaApi.md#apivversioncompanyidrisorsafinanziarianewget) | **GET** /api/v{version}/{companyId}/RisorsaFinanziaria/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*RisorsaFinanziariaApi* | [**apiVversionCompanyIdRisorsaFinanziariaPost**](docs/Api/RisorsaFinanziariaApi.md#apivversioncompanyidrisorsafinanziariapost) | **POST** /api/v{version}/{companyId}/RisorsaFinanziaria | Salva un nuovo elemento.
*ScadenzaApi* | [**apiVversionCompanyIdScadenzaDistinctGet**](docs/Api/ScadenzaApi.md#apivversioncompanyidscadenzadistinctget) | **GET** /api/v{version}/{companyId}/Scadenza/distinct | Restituisce una lista di elementi distinti.
*ScadenzaApi* | [**apiVversionCompanyIdScadenzaGet**](docs/Api/ScadenzaApi.md#apivversioncompanyidscadenzaget) | **GET** /api/v{version}/{companyId}/Scadenza | Restituisce una lista di elementi.
*ScadenzaApi* | [**apiVversionCompanyIdScadenzaIdDelete**](docs/Api/ScadenzaApi.md#apivversioncompanyidscadenzaiddelete) | **DELETE** /api/v{version}/{companyId}/Scadenza/{id} | Elimina un elemento.
*ScadenzaApi* | [**apiVversionCompanyIdScadenzaIdGet**](docs/Api/ScadenzaApi.md#apivversioncompanyidscadenzaidget) | **GET** /api/v{version}/{companyId}/Scadenza/{id} | Restituisce un elemento.
*ScadenzaApi* | [**apiVversionCompanyIdScadenzaIdPut**](docs/Api/ScadenzaApi.md#apivversioncompanyidscadenzaidput) | **PUT** /api/v{version}/{companyId}/Scadenza/{id} | Aggiorna un elemento esistente.
*ScadenzaApi* | [**apiVversionCompanyIdScadenzaNewGet**](docs/Api/ScadenzaApi.md#apivversioncompanyidscadenzanewget) | **GET** /api/v{version}/{companyId}/Scadenza/new | Restituisce una nuova istanza di una scadenza del tipo specificato.
*ScadenzaApi* | [**apiVversionCompanyIdScadenzaPost**](docs/Api/ScadenzaApi.md#apivversioncompanyidscadenzapost) | **POST** /api/v{version}/{companyId}/Scadenza | Salva un nuovo elemento.
*SituazioneApi* | [**apiVversionCompanyIdSituazioneCreditodebitomensileGet**](docs/Api/SituazioneApi.md#apivversioncompanyidsituazionecreditodebitomensileget) | **GET** /api/v{version}/{companyId}/Situazione/creditodebitomensile | Restituisce i saldi mensili di credito e debito dell&#39;azienda.
*SituazioneApi* | [**apiVversionCompanyIdSituazioneFatturatomensileGet**](docs/Api/SituazioneApi.md#apivversioncompanyidsituazionefatturatomensileget) | **GET** /api/v{version}/{companyId}/Situazione/fatturatomensile | Restituisce il fatturato mensile dell&#39;azienda.
*SpesaApi* | [**apiVversionCompanyIdSpesaDistinctGet**](docs/Api/SpesaApi.md#apivversioncompanyidspesadistinctget) | **GET** /api/v{version}/{companyId}/Spesa/distinct | Restituisce una lista di elementi distinti.
*SpesaApi* | [**apiVversionCompanyIdSpesaGet**](docs/Api/SpesaApi.md#apivversioncompanyidspesaget) | **GET** /api/v{version}/{companyId}/Spesa | Restituisce una lista di elementi.
*SpesaApi* | [**apiVversionCompanyIdSpesaIdDelete**](docs/Api/SpesaApi.md#apivversioncompanyidspesaiddelete) | **DELETE** /api/v{version}/{companyId}/Spesa/{id} | Elimina un elemento.
*SpesaApi* | [**apiVversionCompanyIdSpesaIdGet**](docs/Api/SpesaApi.md#apivversioncompanyidspesaidget) | **GET** /api/v{version}/{companyId}/Spesa/{id} | Restituisce un elemento.
*SpesaApi* | [**apiVversionCompanyIdSpesaIdPut**](docs/Api/SpesaApi.md#apivversioncompanyidspesaidput) | **PUT** /api/v{version}/{companyId}/Spesa/{id} | Aggiorna un elemento esistente.
*SpesaApi* | [**apiVversionCompanyIdSpesaNewGet**](docs/Api/SpesaApi.md#apivversioncompanyidspesanewget) | **GET** /api/v{version}/{companyId}/Spesa/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*SpesaApi* | [**apiVversionCompanyIdSpesaPost**](docs/Api/SpesaApi.md#apivversioncompanyidspesapost) | **POST** /api/v{version}/{companyId}/Spesa | Salva un nuovo elemento.
*SpesaDocumentoApi* | [**apiVversionCompanyIdSpesaDocumentoGet**](docs/Api/SpesaDocumentoApi.md#apivversioncompanyidspesadocumentoget) | **GET** /api/v{version}/{companyId}/SpesaDocumento | Restituisce una lista di elementi.
*SpesaDocumentoApi* | [**apiVversionCompanyIdSpesaDocumentoIdDelete**](docs/Api/SpesaDocumentoApi.md#apivversioncompanyidspesadocumentoiddelete) | **DELETE** /api/v{version}/{companyId}/SpesaDocumento/{id} | Elimina un elemento.
*SpesaDocumentoApi* | [**apiVversionCompanyIdSpesaDocumentoIdGet**](docs/Api/SpesaDocumentoApi.md#apivversioncompanyidspesadocumentoidget) | **GET** /api/v{version}/{companyId}/SpesaDocumento/{id} | Restituisce un elemento.
*SpesaDocumentoApi* | [**apiVversionCompanyIdSpesaDocumentoIdPut**](docs/Api/SpesaDocumentoApi.md#apivversioncompanyidspesadocumentoidput) | **PUT** /api/v{version}/{companyId}/SpesaDocumento/{id} | Aggiorna un elemento esistente.
*SpesaDocumentoApi* | [**apiVversionCompanyIdSpesaDocumentoNewGet**](docs/Api/SpesaDocumentoApi.md#apivversioncompanyidspesadocumentonewget) | **GET** /api/v{version}/{companyId}/SpesaDocumento/new | Restituisce una nuova istanza di una spesa documento.
*SpesaDocumentoApi* | [**apiVversionCompanyIdSpesaDocumentoPost**](docs/Api/SpesaDocumentoApi.md#apivversioncompanyidspesadocumentopost) | **POST** /api/v{version}/{companyId}/SpesaDocumento | Salva un nuovo elemento.
*TagliaApi* | [**apiVversionCompanyIdTagliaGet**](docs/Api/TagliaApi.md#apivversioncompanyidtagliaget) | **GET** /api/v{version}/{companyId}/Taglia | Restituisce una lista di elementi.
*TagliaApi* | [**apiVversionCompanyIdTagliaIdDelete**](docs/Api/TagliaApi.md#apivversioncompanyidtagliaiddelete) | **DELETE** /api/v{version}/{companyId}/Taglia/{id} | Elimina un elemento.
*TagliaApi* | [**apiVversionCompanyIdTagliaIdGet**](docs/Api/TagliaApi.md#apivversioncompanyidtagliaidget) | **GET** /api/v{version}/{companyId}/Taglia/{id} | Restituisce un elemento.
*TagliaApi* | [**apiVversionCompanyIdTagliaIdPut**](docs/Api/TagliaApi.md#apivversioncompanyidtagliaidput) | **PUT** /api/v{version}/{companyId}/Taglia/{id} | Aggiorna un elemento esistente.
*TagliaApi* | [**apiVversionCompanyIdTagliaNewGet**](docs/Api/TagliaApi.md#apivversioncompanyidtaglianewget) | **GET** /api/v{version}/{companyId}/Taglia/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*TagliaApi* | [**apiVversionCompanyIdTagliaPost**](docs/Api/TagliaApi.md#apivversioncompanyidtagliapost) | **POST** /api/v{version}/{companyId}/Taglia | Salva un nuovo elemento.
*ValutaApi* | [**apiVversionCompanyIdValutaGet**](docs/Api/ValutaApi.md#apivversioncompanyidvalutaget) | **GET** /api/v{version}/{companyId}/Valuta | Restituisce una lista di elementi.
*ValutaApi* | [**apiVversionCompanyIdValutaIdDelete**](docs/Api/ValutaApi.md#apivversioncompanyidvalutaiddelete) | **DELETE** /api/v{version}/{companyId}/Valuta/{id} | Elimina un elemento.
*ValutaApi* | [**apiVversionCompanyIdValutaIdGet**](docs/Api/ValutaApi.md#apivversioncompanyidvalutaidget) | **GET** /api/v{version}/{companyId}/Valuta/{id} | Restituisce un elemento.
*ValutaApi* | [**apiVversionCompanyIdValutaIdPut**](docs/Api/ValutaApi.md#apivversioncompanyidvalutaidput) | **PUT** /api/v{version}/{companyId}/Valuta/{id} | Aggiorna un elemento esistente.
*ValutaApi* | [**apiVversionCompanyIdValutaNewGet**](docs/Api/ValutaApi.md#apivversioncompanyidvalutanewget) | **GET** /api/v{version}/{companyId}/Valuta/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*ValutaApi* | [**apiVversionCompanyIdValutaPost**](docs/Api/ValutaApi.md#apivversioncompanyidvalutapost) | **POST** /api/v{version}/{companyId}/Valuta | Salva un nuovo elemento.
*WebHookApi* | [**apiVversionCompanyIdWebHookGet**](docs/Api/WebHookApi.md#apivversioncompanyidwebhookget) | **GET** /api/v{version}/{companyId}/WebHook | Restituisce una lista di Amica.WebHook.
*WebHookApi* | [**apiVversionCompanyIdWebHookIdDelete**](docs/Api/WebHookApi.md#apivversioncompanyidwebhookiddelete) | **DELETE** /api/v{version}/{companyId}/WebHook/{id} | 
*WebHookApi* | [**apiVversionCompanyIdWebHookIdGet**](docs/Api/WebHookApi.md#apivversioncompanyidwebhookidget) | **GET** /api/v{version}/{companyId}/WebHook/{id} | Restituisce un Amica.WebHook.
*WebHookApi* | [**apiVversionCompanyIdWebHookIdPut**](docs/Api/WebHookApi.md#apivversioncompanyidwebhookidput) | **PUT** /api/v{version}/{companyId}/WebHook/{id} | Aggiorna un Amica.WebHook.
*WebHookApi* | [**apiVversionCompanyIdWebHookNewGet**](docs/Api/WebHookApi.md#apivversioncompanyidwebhooknewget) | **GET** /api/v{version}/{companyId}/WebHook/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di Amica.Documento avrà il Amica.Documento.Numero appropriato, basato sui documenti preesistenti di tipo compatibile).
*WebHookApi* | [**apiVversionCompanyIdWebHookPost**](docs/Api/WebHookApi.md#apivversioncompanyidwebhookpost) | **POST** /api/v{version}/{companyId}/WebHook | 

## Models

- [AllegatoDocumento](docs/Model/AllegatoDocumento.md)
- [Anagrafica](docs/Model/Anagrafica.md)
- [AnagraficaNominativo1](docs/Model/AnagraficaNominativo1.md)
- [AnagraficaNominativo2](docs/Model/AnagraficaNominativo2.md)
- [AnagraficaSedeLegale](docs/Model/AnagraficaSedeLegale.md)
- [AreaGeografica](docs/Model/AreaGeografica.md)
- [Articolo](docs/Model/Articolo.md)
- [ArticoloMediaFile](docs/Model/ArticoloMediaFile.md)
- [Azienda](docs/Model/Azienda.md)
- [Banca](docs/Model/Banca.md)
- [BancaDocumento](docs/Model/BancaDocumento.md)
- [BeneServizio](docs/Model/BeneServizio.md)
- [BeneServizioComputeData](docs/Model/BeneServizioComputeData.md)
- [BeneServizioComputeResult](docs/Model/BeneServizioComputeResult.md)
- [CassaPrevidenziale](docs/Model/CassaPrevidenziale.md)
- [CastellettoIva](docs/Model/CastellettoIva.md)
- [CategoriaAnagrafica](docs/Model/CategoriaAnagrafica.md)
- [CategoriaMerceologica](docs/Model/CategoriaMerceologica.md)
- [CausaleDocumento](docs/Model/CausaleDocumento.md)
- [CausaleIva](docs/Model/CausaleIva.md)
- [CausaleMagazzino](docs/Model/CausaleMagazzino.md)
- [CodicePagamento](docs/Model/CodicePagamento.md)
- [CollegaDocumentoData](docs/Model/CollegaDocumentoData.md)
- [Componente](docs/Model/Componente.md)
- [ContoContabile](docs/Model/ContoContabile.md)
- [ContoContabileValidationResult](docs/Model/ContoContabileValidationResult.md)
- [CreditoDebitoMensile](docs/Model/CreditoDebitoMensile.md)
- [DatiAggiuntivi](docs/Model/DatiAggiuntivi.md)
- [DatiPA](docs/Model/DatiPA.md)
- [DatiSdi](docs/Model/DatiSdi.md)
- [DatiTrasporto](docs/Model/DatiTrasporto.md)
- [DdtImportData](docs/Model/DdtImportData.md)
- [DistinctPropertyGroup](docs/Model/DistinctPropertyGroup.md)
- [Documento](docs/Model/Documento.md)
- [DocumentoCollegato](docs/Model/DocumentoCollegato.md)
- [DocumentoComputeData](docs/Model/DocumentoComputeData.md)
- [DocumentoComputeDataBeneServizio](docs/Model/DocumentoComputeDataBeneServizio.md)
- [DocumentoComputeDataSpesa](docs/Model/DocumentoComputeDataSpesa.md)
- [DocumentoComputeResult](docs/Model/DocumentoComputeResult.md)
- [DocumentoComputeResultRiepilogoIva](docs/Model/DocumentoComputeResultRiepilogoIva.md)
- [DocumentoImportData](docs/Model/DocumentoImportData.md)
- [DocumentoMediaFile](docs/Model/DocumentoMediaFile.md)
- [Evento](docs/Model/Evento.md)
- [FatturaElettronicaExportOptions](docs/Model/FatturaElettronicaExportOptions.md)
- [FatturaElettronicaExportResult](docs/Model/FatturaElettronicaExportResult.md)
- [FatturaElettronicaImportResult](docs/Model/FatturaElettronicaImportResult.md)
- [FatturaElettronicaUploadOptions](docs/Model/FatturaElettronicaUploadOptions.md)
- [FatturaElettronicaUploadResult](docs/Model/FatturaElettronicaUploadResult.md)
- [FatturaElettronicaXml](docs/Model/FatturaElettronicaXml.md)
- [FatturaElettronicaXmlGetArchiveByDateRangeOptions](docs/Model/FatturaElettronicaXmlGetArchiveByDateRangeOptions.md)
- [FatturaElettronicaXmlGetArchiveByFileNameOptions](docs/Model/FatturaElettronicaXmlGetArchiveByFileNameOptions.md)
- [FatturaElettronicaXmlStyleSheetData](docs/Model/FatturaElettronicaXmlStyleSheetData.md)
- [FatturaElettronicaXmlTemplate](docs/Model/FatturaElettronicaXmlTemplate.md)
- [FatturatoMensile](docs/Model/FatturatoMensile.md)
- [Giacenza](docs/Model/Giacenza.md)
- [GruppoMerceologico](docs/Model/GruppoMerceologico.md)
- [ImpostazioniSdi](docs/Model/ImpostazioniSdi.md)
- [Indirizzo](docs/Model/Indirizzo.md)
- [Listino](docs/Model/Listino.md)
- [Magazzino](docs/Model/Magazzino.md)
- [Marca](docs/Model/Marca.md)
- [ModalitaPagamento](docs/Model/ModalitaPagamento.md)
- [ModelloContabile](docs/Model/ModelloContabile.md)
- [ModelloContabileRiga](docs/Model/ModelloContabileRiga.md)
- [MovimentoMerce](docs/Model/MovimentoMerce.md)
- [MovimentoMerceRiga](docs/Model/MovimentoMerceRiga.md)
- [NaturaIva](docs/Model/NaturaIva.md)
- [Nazione](docs/Model/Nazione.md)
- [OrdiniImportData](docs/Model/OrdiniImportData.md)
- [Pagamento](docs/Model/Pagamento.md)
- [PeriodicInvoicingData](docs/Model/PeriodicInvoicingData.md)
- [Preferenze](docs/Model/Preferenze.md)
- [Prestazione](docs/Model/Prestazione.md)
- [Prezzo](docs/Model/Prezzo.md)
- [ProblemDetails](docs/Model/ProblemDetails.md)
- [Produttore](docs/Model/Produttore.md)
- [ProgressivoInvio](docs/Model/ProgressivoInvio.md)
- [RegistrazioneContabile](docs/Model/RegistrazioneContabile.md)
- [RegistrazioneContabileRiga](docs/Model/RegistrazioneContabileRiga.md)
- [RegistrazioneContabileValidationResult](docs/Model/RegistrazioneContabileValidationResult.md)
- [Reparto](docs/Model/Reparto.md)
- [ResoImportData](docs/Model/ResoImportData.md)
- [RicercaArticolo](docs/Model/RicercaArticolo.md)
- [RisorsaFinanziaria](docs/Model/RisorsaFinanziaria.md)
- [Scadenza](docs/Model/Scadenza.md)
- [SchedaCliente](docs/Model/SchedaCliente.md)
- [SchedaFornitore](docs/Model/SchedaFornitore.md)
- [SedeAltra](docs/Model/SedeAltra.md)
- [SedeLegale](docs/Model/SedeLegale.md)
- [SedeOperativa](docs/Model/SedeOperativa.md)
- [Spesa](docs/Model/Spesa.md)
- [SpesaDocumento](docs/Model/SpesaDocumento.md)
- [Taglia](docs/Model/Taglia.md)
- [Valuta](docs/Model/Valuta.md)
- [WebHook](docs/Model/WebHook.md)

## Authorization

Authentication schemes defined for the API:
### oauth2

- **Type**: `OAuth`
- **Flow**: `application`
- **Authorization URL**: ``
- **Scopes**: 
    - **read**: Accesso in lettura alle risorse
    - **write**: Accesso in scrittura alle risorse
    - **comune**: Anagrafiche e tabelle di base comuni
    - **magazzino**: Articoli, listini, giacenze e movimenti di magazzino
    - **documenti**: Documenti commerciali (ordini, DDT, preventivi, ...)
    - **contabilita**: Contabilità, fatture elettroniche, pagamenti e scadenze

### oauth2

- **Type**: `OAuth`
- **Flow**: `accessCode`
- **Authorization URL**: `https://auth.amica20.it/connect/authorize`
- **Scopes**: 
    - **read**: Accesso in lettura alle risorse
    - **write**: Accesso in scrittura alle risorse
    - **comune**: Anagrafiche e tabelle di base comuni
    - **magazzino**: Articoli, listini, giacenze e movimenti di magazzino
    - **documenti**: Documenti commerciali (ordini, DDT, preventivi, ...)
    - **contabilita**: Contabilità, fatture elettroniche, pagamenti e scadenze

### bearer

- **Type**: Bearer authentication (JWT)

## Tests

To run the tests, use:

```bash
composer install
vendor/bin/phpunit
```

## Author

assistenza@gestionaleamica.com

## About this package

This PHP package is automatically generated by the [OpenAPI Generator](https://openapi-generator.tech) project:

- API version: `v3`
    - Generator version: `7.11.0`
- Build package: `org.openapitools.codegen.languages.PhpClientCodegen`
