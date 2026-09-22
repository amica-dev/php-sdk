# Amica\Sdk\ArticoloMediaFileApi

All URIs are relative to http://localhost, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**apiVversionCompanyIdArticoloMediaFileBulkDelete()**](ArticoloMediaFileApi.md#apiVversionCompanyIdArticoloMediaFileBulkDelete) | **DELETE** /api/v{version}/{companyId}/ArticoloMediaFile/bulk | Elimina in blocco tutti i file multimediali degli articoli. |
| [**apiVversionCompanyIdArticoloMediaFileBulkGet()**](ArticoloMediaFileApi.md#apiVversionCompanyIdArticoloMediaFileBulkGet) | **GET** /api/v{version}/{companyId}/ArticoloMediaFile/bulk | Scarica tutti i file multimediali degli articoli come archivio ZIP. |
| [**apiVversionCompanyIdArticoloMediaFileGet()**](ArticoloMediaFileApi.md#apiVversionCompanyIdArticoloMediaFileGet) | **GET** /api/v{version}/{companyId}/ArticoloMediaFile | Restituisce una lista di elementi. |
| [**apiVversionCompanyIdArticoloMediaFileIdDelete()**](ArticoloMediaFileApi.md#apiVversionCompanyIdArticoloMediaFileIdDelete) | **DELETE** /api/v{version}/{companyId}/ArticoloMediaFile/{id} | Elimina un elemento. |
| [**apiVversionCompanyIdArticoloMediaFileIdGet()**](ArticoloMediaFileApi.md#apiVversionCompanyIdArticoloMediaFileIdGet) | **GET** /api/v{version}/{companyId}/ArticoloMediaFile/{id} | Restituisce un elemento. |
| [**apiVversionCompanyIdArticoloMediaFileIdPut()**](ArticoloMediaFileApi.md#apiVversionCompanyIdArticoloMediaFileIdPut) | **PUT** /api/v{version}/{companyId}/ArticoloMediaFile/{id} | Aggiorna un elemento esistente. |
| [**apiVversionCompanyIdArticoloMediaFileNewGet()**](ArticoloMediaFileApi.md#apiVversionCompanyIdArticoloMediaFileNewGet) | **GET** /api/v{version}/{companyId}/ArticoloMediaFile/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di &#x60;Documento&#x60; avrà il &#x60;Numero&#x60; appropriato, basato sui documenti preesistenti di tipo compatibile). |
| [**apiVversionCompanyIdArticoloMediaFilePost()**](ArticoloMediaFileApi.md#apiVversionCompanyIdArticoloMediaFilePost) | **POST** /api/v{version}/{companyId}/ArticoloMediaFile | Salva un nuovo elemento. |


## `apiVversionCompanyIdArticoloMediaFileBulkDelete()`

```php
apiVversionCompanyIdArticoloMediaFileBulkDelete($company_id, $version)
```

Elimina in blocco tutti i file multimediali degli articoli.

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


$apiInstance = new Amica\Sdk\Api\ArticoloMediaFileApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$version = 'version_example'; // string

try {
    $apiInstance->apiVversionCompanyIdArticoloMediaFileBulkDelete($company_id, $version);
} catch (Exception $e) {
    echo 'Exception when calling ArticoloMediaFileApi->apiVversionCompanyIdArticoloMediaFileBulkDelete: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **version** | **string**|  | |

### Return type

void (empty response body)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `text/plain`, `application/json`, `text/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdArticoloMediaFileBulkGet()`

```php
apiVversionCompanyIdArticoloMediaFileBulkGet($company_id, $version)
```

Scarica tutti i file multimediali degli articoli come archivio ZIP.

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


$apiInstance = new Amica\Sdk\Api\ArticoloMediaFileApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$version = 'version_example'; // string

try {
    $apiInstance->apiVversionCompanyIdArticoloMediaFileBulkGet($company_id, $version);
} catch (Exception $e) {
    echo 'Exception when calling ArticoloMediaFileApi->apiVversionCompanyIdArticoloMediaFileBulkGet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **version** | **string**|  | |

### Return type

void (empty response body)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/zip`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdArticoloMediaFileGet()`

```php
apiVversionCompanyIdArticoloMediaFileGet($version, $company_id, $skip, $take, $skip_total_count, $filter, $order, $expand): \Amica\Sdk\Model\ArticoloMediaFile[]
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


$apiInstance = new Amica\Sdk\Api\ArticoloMediaFileApi(
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
    $result = $apiInstance->apiVversionCompanyIdArticoloMediaFileGet($version, $company_id, $skip, $take, $skip_total_count, $filter, $order, $expand);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ArticoloMediaFileApi->apiVversionCompanyIdArticoloMediaFileGet: ', $e->getMessage(), PHP_EOL;
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

[**\Amica\Sdk\Model\ArticoloMediaFile[]**](../Model/ArticoloMediaFile.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdArticoloMediaFileIdDelete()`

```php
apiVversionCompanyIdArticoloMediaFileIdDelete($version, $company_id, $id): \Amica\Sdk\Model\ArticoloMediaFile
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


$apiInstance = new Amica\Sdk\Api\ArticoloMediaFileApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$id = 56; // int | ID dell'elemento da eliminare.

try {
    $result = $apiInstance->apiVversionCompanyIdArticoloMediaFileIdDelete($version, $company_id, $id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ArticoloMediaFileApi->apiVversionCompanyIdArticoloMediaFileIdDelete: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **id** | **int**| ID dell&#39;elemento da eliminare. | |

### Return type

[**\Amica\Sdk\Model\ArticoloMediaFile**](../Model/ArticoloMediaFile.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdArticoloMediaFileIdGet()`

```php
apiVversionCompanyIdArticoloMediaFileIdGet($version, $company_id, $id, $expand): \Amica\Sdk\Model\ArticoloMediaFile
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


$apiInstance = new Amica\Sdk\Api\ArticoloMediaFileApi(
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
    $result = $apiInstance->apiVversionCompanyIdArticoloMediaFileIdGet($version, $company_id, $id, $expand);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ArticoloMediaFileApi->apiVversionCompanyIdArticoloMediaFileIdGet: ', $e->getMessage(), PHP_EOL;
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

[**\Amica\Sdk\Model\ArticoloMediaFile**](../Model/ArticoloMediaFile.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdArticoloMediaFileIdPut()`

```php
apiVversionCompanyIdArticoloMediaFileIdPut($version, $company_id, $id, $validation_override, $idempotency_key, $articolo_media_file): \Amica\Sdk\Model\ArticoloMediaFile
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


$apiInstance = new Amica\Sdk\Api\ArticoloMediaFileApi(
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
$articolo_media_file = new \Amica\Sdk\Model\ArticoloMediaFile(); // \Amica\Sdk\Model\ArticoloMediaFile | Istanza dell'elemento da salvare.

try {
    $result = $apiInstance->apiVversionCompanyIdArticoloMediaFileIdPut($version, $company_id, $id, $validation_override, $idempotency_key, $articolo_media_file);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ArticoloMediaFileApi->apiVversionCompanyIdArticoloMediaFileIdPut: ', $e->getMessage(), PHP_EOL;
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
| **articolo_media_file** | [**\Amica\Sdk\Model\ArticoloMediaFile**](../Model/ArticoloMediaFile.md)| Istanza dell&#39;elemento da salvare. | [optional] |

### Return type

[**\Amica\Sdk\Model\ArticoloMediaFile**](../Model/ArticoloMediaFile.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdArticoloMediaFileNewGet()`

```php
apiVversionCompanyIdArticoloMediaFileNewGet($version, $company_id): \Amica\Sdk\Model\ArticoloMediaFile
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


$apiInstance = new Amica\Sdk\Api\ArticoloMediaFileApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.

try {
    $result = $apiInstance->apiVversionCompanyIdArticoloMediaFileNewGet($version, $company_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ArticoloMediaFileApi->apiVversionCompanyIdArticoloMediaFileNewGet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |

### Return type

[**\Amica\Sdk\Model\ArticoloMediaFile**](../Model/ArticoloMediaFile.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdArticoloMediaFilePost()`

```php
apiVversionCompanyIdArticoloMediaFilePost($version, $company_id, $validation_override, $idempotency_key, $articolo_media_file): \Amica\Sdk\Model\ArticoloMediaFile
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


$apiInstance = new Amica\Sdk\Api\ArticoloMediaFileApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$validation_override = True; // bool | Se applicare eventualmente l'override della validazione.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$articolo_media_file = new \Amica\Sdk\Model\ArticoloMediaFile(); // \Amica\Sdk\Model\ArticoloMediaFile | Istanza dell'elemento da salvare.

try {
    $result = $apiInstance->apiVversionCompanyIdArticoloMediaFilePost($version, $company_id, $validation_override, $idempotency_key, $articolo_media_file);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling ArticoloMediaFileApi->apiVversionCompanyIdArticoloMediaFilePost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **validation_override** | **bool**| Se applicare eventualmente l&#39;override della validazione. | [optional] |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **articolo_media_file** | [**\Amica\Sdk\Model\ArticoloMediaFile**](../Model/ArticoloMediaFile.md)| Istanza dell&#39;elemento da salvare. | [optional] |

### Return type

[**\Amica\Sdk\Model\ArticoloMediaFile**](../Model/ArticoloMediaFile.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
