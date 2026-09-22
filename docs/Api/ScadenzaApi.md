# Amica\Sdk\ScadenzaApi

All URIs are relative to http://localhost, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**apiVversionCompanyIdScadenzaDistinctGet()**](ScadenzaApi.md#apiVversionCompanyIdScadenzaDistinctGet) | **GET** /api/v{version}/{companyId}/Scadenza/distinct | Restituisce una lista di elementi distinti. |
| [**apiVversionCompanyIdScadenzaGet()**](ScadenzaApi.md#apiVversionCompanyIdScadenzaGet) | **GET** /api/v{version}/{companyId}/Scadenza | Restituisce una lista di elementi. |
| [**apiVversionCompanyIdScadenzaIdDelete()**](ScadenzaApi.md#apiVversionCompanyIdScadenzaIdDelete) | **DELETE** /api/v{version}/{companyId}/Scadenza/{id} | Elimina un elemento. |
| [**apiVversionCompanyIdScadenzaIdGet()**](ScadenzaApi.md#apiVversionCompanyIdScadenzaIdGet) | **GET** /api/v{version}/{companyId}/Scadenza/{id} | Restituisce un elemento. |
| [**apiVversionCompanyIdScadenzaIdPut()**](ScadenzaApi.md#apiVversionCompanyIdScadenzaIdPut) | **PUT** /api/v{version}/{companyId}/Scadenza/{id} | Aggiorna un elemento esistente. |
| [**apiVversionCompanyIdScadenzaNewGet()**](ScadenzaApi.md#apiVversionCompanyIdScadenzaNewGet) | **GET** /api/v{version}/{companyId}/Scadenza/new | Restituisce una nuova istanza di una scadenza del tipo specificato. |
| [**apiVversionCompanyIdScadenzaPost()**](ScadenzaApi.md#apiVversionCompanyIdScadenzaPost) | **POST** /api/v{version}/{companyId}/Scadenza | Salva un nuovo elemento. |


## `apiVversionCompanyIdScadenzaDistinctGet()`

```php
apiVversionCompanyIdScadenzaDistinctGet($version, $company_id, $selector, $skip, $take): \Amica\Sdk\Model\DistinctPropertyGroup[]
```

Restituisce una lista di elementi distinti.

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


$apiInstance = new Amica\Sdk\Api\ScadenzaApi(
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
    $result = $apiInstance->apiVversionCompanyIdScadenzaDistinctGet($version, $company_id, $selector, $skip, $take);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ScadenzaApi->apiVversionCompanyIdScadenzaDistinctGet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **selector** | **string**| Il campo usato per distinguere gli elementi. | |
| **skip** | **int**| Numero di elementi, a partire dal primo, da escludere dal risultato. Combinato con &#x60;Take&#x60;, è utile per la paginazione. | [optional] |
| **take** | **int**| Numero di elementi da includere nel risultato. Combinato con &#x60;Skip&#x60;, è utile per la paginazione. | [optional] |

### Return type

[**\Amica\Sdk\Model\DistinctPropertyGroup[]**](../Model/DistinctPropertyGroup.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdScadenzaGet()`

```php
apiVversionCompanyIdScadenzaGet($version, $company_id, $skip, $take, $skip_total_count, $filter, $order, $expand): \Amica\Sdk\Model\Scadenza[]
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


$apiInstance = new Amica\Sdk\Api\ScadenzaApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$skip = 56; // int
$take = 56; // int
$skip_total_count = True; // bool
$filter = 'filter_example'; // string
$order = 'order_example'; // string
$expand = array('expand_example'); // string[]

try {
    $result = $apiInstance->apiVversionCompanyIdScadenzaGet($version, $company_id, $skip, $take, $skip_total_count, $filter, $order, $expand);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ScadenzaApi->apiVversionCompanyIdScadenzaGet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **skip** | **int**|  | [optional] |
| **take** | **int**|  | [optional] |
| **skip_total_count** | **bool**|  | [optional] |
| **filter** | **string**|  | [optional] |
| **order** | **string**|  | [optional] |
| **expand** | [**string[]**](../Model/string.md)|  | [optional] |

### Return type

[**\Amica\Sdk\Model\Scadenza[]**](../Model/Scadenza.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdScadenzaIdDelete()`

```php
apiVversionCompanyIdScadenzaIdDelete($version, $company_id, $id): \Amica\Sdk\Model\Scadenza
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


$apiInstance = new Amica\Sdk\Api\ScadenzaApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$id = 56; // int | ID dell'elemento da eliminare.

try {
    $result = $apiInstance->apiVversionCompanyIdScadenzaIdDelete($version, $company_id, $id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ScadenzaApi->apiVversionCompanyIdScadenzaIdDelete: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **id** | **int**| ID dell&#39;elemento da eliminare. | |

### Return type

[**\Amica\Sdk\Model\Scadenza**](../Model/Scadenza.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdScadenzaIdGet()`

```php
apiVversionCompanyIdScadenzaIdGet($version, $company_id, $id, $expand): \Amica\Sdk\Model\Scadenza
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


$apiInstance = new Amica\Sdk\Api\ScadenzaApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$id = 56; // int | ID dell'elemento da recuperare.
$expand = array('expand_example'); // string[]

try {
    $result = $apiInstance->apiVversionCompanyIdScadenzaIdGet($version, $company_id, $id, $expand);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ScadenzaApi->apiVversionCompanyIdScadenzaIdGet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **id** | **int**| ID dell&#39;elemento da recuperare. | |
| **expand** | [**string[]**](../Model/string.md)|  | [optional] |

### Return type

[**\Amica\Sdk\Model\Scadenza**](../Model/Scadenza.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdScadenzaIdPut()`

```php
apiVversionCompanyIdScadenzaIdPut($version, $company_id, $id, $validation_override, $idempotency_key, $scadenza): \Amica\Sdk\Model\Scadenza
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


$apiInstance = new Amica\Sdk\Api\ScadenzaApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$id = 56; // int | ID univoco dell'elemento da aggiornare.
$validation_override = True; // bool | Se applicare eventualmente l'override della validazione.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$scadenza = new \Amica\Sdk\Model\Scadenza(); // \Amica\Sdk\Model\Scadenza | Istanza dell'elemento da salvare.

try {
    $result = $apiInstance->apiVversionCompanyIdScadenzaIdPut($version, $company_id, $id, $validation_override, $idempotency_key, $scadenza);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ScadenzaApi->apiVversionCompanyIdScadenzaIdPut: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **id** | **int**| ID univoco dell&#39;elemento da aggiornare. | |
| **validation_override** | **bool**| Se applicare eventualmente l&#39;override della validazione. | [optional] |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **scadenza** | [**\Amica\Sdk\Model\Scadenza**](../Model/Scadenza.md)| Istanza dell&#39;elemento da salvare. | [optional] |

### Return type

[**\Amica\Sdk\Model\Scadenza**](../Model/Scadenza.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdScadenzaNewGet()`

```php
apiVversionCompanyIdScadenzaNewGet($version, $company_id): \Amica\Sdk\Model\Scadenza
```

Restituisce una nuova istanza di una scadenza del tipo specificato.

Richiede il parametro query `TipoScadenza` (int) che indica il tipo di scadenza da creare.

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


$apiInstance = new Amica\Sdk\Api\ScadenzaApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.

try {
    $result = $apiInstance->apiVversionCompanyIdScadenzaNewGet($version, $company_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ScadenzaApi->apiVversionCompanyIdScadenzaNewGet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |

### Return type

[**\Amica\Sdk\Model\Scadenza**](../Model/Scadenza.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdScadenzaPost()`

```php
apiVversionCompanyIdScadenzaPost($version, $company_id, $validation_override, $idempotency_key, $scadenza): \Amica\Sdk\Model\Scadenza
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


$apiInstance = new Amica\Sdk\Api\ScadenzaApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$validation_override = True; // bool | Se applicare eventualmente l'override della validazione.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$scadenza = new \Amica\Sdk\Model\Scadenza(); // \Amica\Sdk\Model\Scadenza | Istanza dell'elemento da salvare.

try {
    $result = $apiInstance->apiVversionCompanyIdScadenzaPost($version, $company_id, $validation_override, $idempotency_key, $scadenza);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ScadenzaApi->apiVversionCompanyIdScadenzaPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **validation_override** | **bool**| Se applicare eventualmente l&#39;override della validazione. | [optional] |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **scadenza** | [**\Amica\Sdk\Model\Scadenza**](../Model/Scadenza.md)| Istanza dell&#39;elemento da salvare. | [optional] |

### Return type

[**\Amica\Sdk\Model\Scadenza**](../Model/Scadenza.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
