# Amica\Sdk\GruppoMerceologicoApi

All URIs are relative to http://localhost, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**gruppoMerceologicoDelete()**](GruppoMerceologicoApi.md#gruppoMerceologicoDelete) | **DELETE** /api/v3/{companyId}/GruppoMerceologico/{id} | Elimina un elemento. |
| [**gruppoMerceologicoGet()**](GruppoMerceologicoApi.md#gruppoMerceologicoGet) | **GET** /api/v3/{companyId}/GruppoMerceologico/{id} | Restituisce un elemento. |
| [**gruppoMerceologicoList()**](GruppoMerceologicoApi.md#gruppoMerceologicoList) | **GET** /api/v3/{companyId}/GruppoMerceologico | Restituisce una lista di elementi. |
| [**gruppoMerceologicoNewInstance()**](GruppoMerceologicoApi.md#gruppoMerceologicoNewInstance) | **GET** /api/v3/{companyId}/GruppoMerceologico/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di &#x60;Documento&#x60; avrà il &#x60;Numero&#x60; appropriato, basato sui documenti preesistenti di tipo compatibile). |
| [**gruppoMerceologicoPost()**](GruppoMerceologicoApi.md#gruppoMerceologicoPost) | **POST** /api/v3/{companyId}/GruppoMerceologico | Salva un nuovo elemento. |
| [**gruppoMerceologicoPut()**](GruppoMerceologicoApi.md#gruppoMerceologicoPut) | **PUT** /api/v3/{companyId}/GruppoMerceologico/{id} | Aggiorna un elemento esistente. |


## `gruppoMerceologicoDelete()`

```php
gruppoMerceologicoDelete($company_id, $id, $validation_override): \Amica\Sdk\Model\GruppoMerceologico
```

Elimina un elemento.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: bearer
$config = Amica\Sdk\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure OAuth2 access token for authorization: oauth2ClientCredentials
$config = Amica\Sdk\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure OAuth2 access token for authorization: oauth2
$config = Amica\Sdk\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new Amica\Sdk\Api\GruppoMerceologicoApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$id = 56; // int | ID dell'elemento da eliminare.
$validation_override = True; // bool | Se applicare eventualmente l'override della validazione.

try {
    $result = $apiInstance->gruppoMerceologicoDelete($company_id, $id, $validation_override);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling GruppoMerceologicoApi->gruppoMerceologicoDelete: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **id** | **int**| ID dell&#39;elemento da eliminare. | |
| **validation_override** | **bool**| Se applicare eventualmente l&#39;override della validazione. | [optional] |

### Return type

[**\Amica\Sdk\Model\GruppoMerceologico**](../Model/GruppoMerceologico.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `gruppoMerceologicoGet()`

```php
gruppoMerceologicoGet($company_id, $id, $expand): \Amica\Sdk\Model\GruppoMerceologico
```

Restituisce un elemento.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: bearer
$config = Amica\Sdk\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure OAuth2 access token for authorization: oauth2ClientCredentials
$config = Amica\Sdk\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure OAuth2 access token for authorization: oauth2
$config = Amica\Sdk\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new Amica\Sdk\Api\GruppoMerceologicoApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$id = 56; // int | ID dell'elemento da recuperare.
$expand = Rows; // string[] | Elenco separato da virgole di oggetti da espandere. Permette di recuperare gli oggetti correlati in un'unica chiamata. Ad esempio, per includere i beni e servizi associati a ciascun documento si aggiunge `expand=Rows` alla chiamata.

try {
    $result = $apiInstance->gruppoMerceologicoGet($company_id, $id, $expand);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling GruppoMerceologicoApi->gruppoMerceologicoGet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **id** | **int**| ID dell&#39;elemento da recuperare. | |
| **expand** | [**string[]**](../Model/string.md)| Elenco separato da virgole di oggetti da espandere. Permette di recuperare gli oggetti correlati in un&#39;unica chiamata. Ad esempio, per includere i beni e servizi associati a ciascun documento si aggiunge &#x60;expand&#x3D;Rows&#x60; alla chiamata. | [optional] |

### Return type

[**\Amica\Sdk\Model\GruppoMerceologico**](../Model/GruppoMerceologico.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `gruppoMerceologicoList()`

```php
gruppoMerceologicoList($company_id, $skip, $take, $skip_total_count, $filter, $order, $expand): \Amica\Sdk\Model\GruppoMerceologico[]
```

Restituisce una lista di elementi.

La risposta include due header con informazioni sui risultati.  `X-Read-TotalCount` indica il numero di elementi che soddisfano il filtro impostato nella query;  `X-Read-Count` indica il numero di elementi effettivamente contenuti nella risposta.  I due header possono riportare valori diversi quando i parametri `Take` e `Skip` sono stati usati nelle opzioni di query.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: bearer
$config = Amica\Sdk\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure OAuth2 access token for authorization: oauth2ClientCredentials
$config = Amica\Sdk\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure OAuth2 access token for authorization: oauth2
$config = Amica\Sdk\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new Amica\Sdk\Api\GruppoMerceologicoApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$skip = 100; // int | Numero di elementi da escludere dall'inizio del risultato. Combinato con `Take`, è utile per la paginazione.
$take = 25; // int | Numero di elementi da includere nel risultato. Combinato con `Skip`, è utile per la paginazione.
$skip_total_count = True; // bool | Indica se l'header `X-Read-TotalCount` debba essere escluso dalla risposta. Impostare a `true` per prestazioni migliori a scapito dell'accuratezza. Il valore predefinito è `false`.
$filter = field1 $eq hello $or, field2 $gt 10; // string | Condizioni di ricerca. Elenco di espressioni separate da virgola. Ogni espressione è composta da nome del campo, operatore di confronto, valore ed eventualmente un connettore logico. Operatori validi: `$eq` (uguale), `$ne` (diverso), `$gt` (maggiore di), `$lt` (minore di), `$lte` (minore o uguale), `$gte` (maggiore o uguale), `$ct` (contiene), `$nct` (non contiene), `$sw` (inizia con), `$ew` (finisce con). Più espressioni possono essere concatenate con un connettore logico `$and` o `$or` (`$and` può essere omesso). Le query annidate si esprimono racchiudendo le sotto-query tra parentesi quadre (`[]`). Nell'esempio vengono inclusi tutti gli elementi con `field1=\"hello\"` oppure `field2>10`.
$order = -field1,field2; // string | Opzioni di ordinamento. Un segno meno (trattino) prima del nome indica ordinamento discendente. Nell'esempio i risultati sono ordinati prima per `field1` (discendente), poi per `field2`.
$expand = Rows; // string[] | Elenco separato da virgole di oggetti da espandere. Permette di recuperare gli oggetti correlati in un'unica chiamata. Ad esempio, per includere i beni e servizi associati a ciascun documento si aggiunge `expand=Rows` alla chiamata.

try {
    $result = $apiInstance->gruppoMerceologicoList($company_id, $skip, $take, $skip_total_count, $filter, $order, $expand);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling GruppoMerceologicoApi->gruppoMerceologicoList: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **skip** | **int**| Numero di elementi da escludere dall&#39;inizio del risultato. Combinato con &#x60;Take&#x60;, è utile per la paginazione. | [optional] |
| **take** | **int**| Numero di elementi da includere nel risultato. Combinato con &#x60;Skip&#x60;, è utile per la paginazione. | [optional] |
| **skip_total_count** | **bool**| Indica se l&#39;header &#x60;X-Read-TotalCount&#x60; debba essere escluso dalla risposta. Impostare a &#x60;true&#x60; per prestazioni migliori a scapito dell&#39;accuratezza. Il valore predefinito è &#x60;false&#x60;. | [optional] |
| **filter** | **string**| Condizioni di ricerca. Elenco di espressioni separate da virgola. Ogni espressione è composta da nome del campo, operatore di confronto, valore ed eventualmente un connettore logico. Operatori validi: &#x60;$eq&#x60; (uguale), &#x60;$ne&#x60; (diverso), &#x60;$gt&#x60; (maggiore di), &#x60;$lt&#x60; (minore di), &#x60;$lte&#x60; (minore o uguale), &#x60;$gte&#x60; (maggiore o uguale), &#x60;$ct&#x60; (contiene), &#x60;$nct&#x60; (non contiene), &#x60;$sw&#x60; (inizia con), &#x60;$ew&#x60; (finisce con). Più espressioni possono essere concatenate con un connettore logico &#x60;$and&#x60; o &#x60;$or&#x60; (&#x60;$and&#x60; può essere omesso). Le query annidate si esprimono racchiudendo le sotto-query tra parentesi quadre (&#x60;[]&#x60;). Nell&#39;esempio vengono inclusi tutti gli elementi con &#x60;field1&#x3D;\&quot;hello\&quot;&#x60; oppure &#x60;field2&gt;10&#x60;. | [optional] |
| **order** | **string**| Opzioni di ordinamento. Un segno meno (trattino) prima del nome indica ordinamento discendente. Nell&#39;esempio i risultati sono ordinati prima per &#x60;field1&#x60; (discendente), poi per &#x60;field2&#x60;. | [optional] |
| **expand** | [**string[]**](../Model/string.md)| Elenco separato da virgole di oggetti da espandere. Permette di recuperare gli oggetti correlati in un&#39;unica chiamata. Ad esempio, per includere i beni e servizi associati a ciascun documento si aggiunge &#x60;expand&#x3D;Rows&#x60; alla chiamata. | [optional] |

### Return type

[**\Amica\Sdk\Model\GruppoMerceologico[]**](../Model/GruppoMerceologico.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `gruppoMerceologicoNewInstance()`

```php
gruppoMerceologicoNewInstance($company_id): \Amica\Sdk\Model\GruppoMerceologico
```

Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell'applicazione (ad es., una nuova istanza di `Documento` avrà il `Numero` appropriato, basato sui documenti preesistenti di tipo compatibile).

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: bearer
$config = Amica\Sdk\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure OAuth2 access token for authorization: oauth2ClientCredentials
$config = Amica\Sdk\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure OAuth2 access token for authorization: oauth2
$config = Amica\Sdk\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new Amica\Sdk\Api\GruppoMerceologicoApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.

try {
    $result = $apiInstance->gruppoMerceologicoNewInstance($company_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling GruppoMerceologicoApi->gruppoMerceologicoNewInstance: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |

### Return type

[**\Amica\Sdk\Model\GruppoMerceologico**](../Model/GruppoMerceologico.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `gruppoMerceologicoPost()`

```php
gruppoMerceologicoPost($company_id, $validation_override, $idempotency_key, $gruppo_merceologico): \Amica\Sdk\Model\GruppoMerceologico
```

Salva un nuovo elemento.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: bearer
$config = Amica\Sdk\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure OAuth2 access token for authorization: oauth2ClientCredentials
$config = Amica\Sdk\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure OAuth2 access token for authorization: oauth2
$config = Amica\Sdk\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new Amica\Sdk\Api\GruppoMerceologicoApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$validation_override = True; // bool | Se applicare eventualmente l'override della validazione.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$gruppo_merceologico = new \Amica\Sdk\Model\GruppoMerceologico(); // \Amica\Sdk\Model\GruppoMerceologico | Istanza dell'elemento da salvare.

try {
    $result = $apiInstance->gruppoMerceologicoPost($company_id, $validation_override, $idempotency_key, $gruppo_merceologico);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling GruppoMerceologicoApi->gruppoMerceologicoPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **validation_override** | **bool**| Se applicare eventualmente l&#39;override della validazione. | [optional] |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **gruppo_merceologico** | [**\Amica\Sdk\Model\GruppoMerceologico**](../Model/GruppoMerceologico.md)| Istanza dell&#39;elemento da salvare. | [optional] |

### Return type

[**\Amica\Sdk\Model\GruppoMerceologico**](../Model/GruppoMerceologico.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `gruppoMerceologicoPut()`

```php
gruppoMerceologicoPut($company_id, $id, $validation_override, $idempotency_key, $gruppo_merceologico): \Amica\Sdk\Model\GruppoMerceologico
```

Aggiorna un elemento esistente.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: bearer
$config = Amica\Sdk\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure OAuth2 access token for authorization: oauth2ClientCredentials
$config = Amica\Sdk\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');

// Configure OAuth2 access token for authorization: oauth2
$config = Amica\Sdk\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new Amica\Sdk\Api\GruppoMerceologicoApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$id = 56; // int | ID univoco dell'elemento da aggiornare.
$validation_override = True; // bool | Se applicare eventualmente l'override della validazione.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$gruppo_merceologico = new \Amica\Sdk\Model\GruppoMerceologico(); // \Amica\Sdk\Model\GruppoMerceologico | Istanza dell'elemento da salvare.

try {
    $result = $apiInstance->gruppoMerceologicoPut($company_id, $id, $validation_override, $idempotency_key, $gruppo_merceologico);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling GruppoMerceologicoApi->gruppoMerceologicoPut: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **id** | **int**| ID univoco dell&#39;elemento da aggiornare. | |
| **validation_override** | **bool**| Se applicare eventualmente l&#39;override della validazione. | [optional] |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **gruppo_merceologico** | [**\Amica\Sdk\Model\GruppoMerceologico**](../Model/GruppoMerceologico.md)| Istanza dell&#39;elemento da salvare. | [optional] |

### Return type

[**\Amica\Sdk\Model\GruppoMerceologico**](../Model/GruppoMerceologico.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
