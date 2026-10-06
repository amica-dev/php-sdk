# Amica\Sdk\InventarioApi

All URIs are relative to http://localhost, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**apiVversionCompanyIdInventarioComputePost()**](InventarioApi.md#apiVversionCompanyIdInventarioComputePost) | **POST** /api/v{version}/{companyId}/Inventario/compute | Elabora una testata non ancora salvata, senza salvare nulla. |
| [**apiVversionCompanyIdInventarioDistinctGet()**](InventarioApi.md#apiVversionCompanyIdInventarioDistinctGet) | **GET** /api/v{version}/{companyId}/Inventario/distinct | Restituisce una lista di elementi distinti. |
| [**apiVversionCompanyIdInventarioGet()**](InventarioApi.md#apiVversionCompanyIdInventarioGet) | **GET** /api/v{version}/{companyId}/Inventario | Restituisce una lista di elementi. |
| [**apiVversionCompanyIdInventarioIdComputePost()**](InventarioApi.md#apiVversionCompanyIdInventarioIdComputePost) | **POST** /api/v{version}/{companyId}/Inventario/{id}/compute | Elabora un inventario senza salvare nulla. |
| [**apiVversionCompanyIdInventarioIdDelete()**](InventarioApi.md#apiVversionCompanyIdInventarioIdDelete) | **DELETE** /api/v{version}/{companyId}/Inventario/{id} | Elimina un elemento. |
| [**apiVversionCompanyIdInventarioIdGet()**](InventarioApi.md#apiVversionCompanyIdInventarioIdGet) | **GET** /api/v{version}/{companyId}/Inventario/{id} | Restituisce un elemento. |
| [**apiVversionCompanyIdInventarioIdProcessPost()**](InventarioApi.md#apiVversionCompanyIdInventarioIdProcessPost) | **POST** /api/v{version}/{companyId}/Inventario/{id}/process | Elabora un inventario e ne salva le righe. |
| [**apiVversionCompanyIdInventarioIdPut()**](InventarioApi.md#apiVversionCompanyIdInventarioIdPut) | **PUT** /api/v{version}/{companyId}/Inventario/{id} | Aggiorna un elemento esistente. |
| [**apiVversionCompanyIdInventarioNewGet()**](InventarioApi.md#apiVversionCompanyIdInventarioNewGet) | **GET** /api/v{version}/{companyId}/Inventario/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di &#x60;Documento&#x60; avrà il &#x60;Numero&#x60; appropriato, basato sui documenti preesistenti di tipo compatibile). |
| [**apiVversionCompanyIdInventarioPost()**](InventarioApi.md#apiVversionCompanyIdInventarioPost) | **POST** /api/v{version}/{companyId}/Inventario | Salva un nuovo elemento. |
| [**apiVversionCompanyIdInventarioProcessPost()**](InventarioApi.md#apiVversionCompanyIdInventarioProcessPost) | **POST** /api/v{version}/{companyId}/Inventario/process | Salva una testata, la elabora e ne salva le righe. |


## `apiVversionCompanyIdInventarioComputePost()`

```php
apiVversionCompanyIdInventarioComputePost($version, $company_id, $idempotency_key, $inventario): \Amica\Sdk\Model\InventarioComputeResult
```

Elabora una testata non ancora salvata, senza salvare nulla.

Come `{id}/compute`, ma con i valori ricevuti invece di quelli salvati: serve per             l'anteprima di un inventario prima di crearlo, o di una testata modificata prima di salvarla. Di un             inventario esistente conta solo l'Id, che lo esclude dalla ricerca dell'inventario fiscale             precedente.  `GiacenzeAllineabili` dice se `process` con `alignStock` allineerebbe davvero le             giacenze.

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


$apiInstance = new Amica\Sdk\Api\InventarioApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$inventario = new \Amica\Sdk\Model\Inventario(); // \Amica\Sdk\Model\Inventario | Testata da elaborare: nuova (senza Id) o di un inventario esistente, con le             modifiche non ancora salvate. Le righe sono ignorate.

try {
    $result = $apiInstance->apiVversionCompanyIdInventarioComputePost($version, $company_id, $idempotency_key, $inventario);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling InventarioApi->apiVversionCompanyIdInventarioComputePost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **inventario** | [**\Amica\Sdk\Model\Inventario**](../Model/Inventario.md)| Testata da elaborare: nuova (senza Id) o di un inventario esistente, con le             modifiche non ancora salvate. Le righe sono ignorate. | [optional] |

### Return type

[**\Amica\Sdk\Model\InventarioComputeResult**](../Model/InventarioComputeResult.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdInventarioDistinctGet()`

```php
apiVversionCompanyIdInventarioDistinctGet($version, $company_id, $selector, $skip, $take): \Amica\Sdk\Model\DistinctPropertyGroup[]
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


$apiInstance = new Amica\Sdk\Api\InventarioApi(
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
    $result = $apiInstance->apiVversionCompanyIdInventarioDistinctGet($version, $company_id, $selector, $skip, $take);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling InventarioApi->apiVversionCompanyIdInventarioDistinctGet: ', $e->getMessage(), PHP_EOL;
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

## `apiVversionCompanyIdInventarioGet()`

```php
apiVversionCompanyIdInventarioGet($version, $company_id, $skip, $take, $skip_total_count, $filter, $order, $expand): \Amica\Sdk\Model\Inventario[]
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


$apiInstance = new Amica\Sdk\Api\InventarioApi(
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
    $result = $apiInstance->apiVversionCompanyIdInventarioGet($version, $company_id, $skip, $take, $skip_total_count, $filter, $order, $expand);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling InventarioApi->apiVversionCompanyIdInventarioGet: ', $e->getMessage(), PHP_EOL;
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

[**\Amica\Sdk\Model\Inventario[]**](../Model/Inventario.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdInventarioIdComputePost()`

```php
apiVversionCompanyIdInventarioIdComputePost($version, $company_id, $id, $idempotency_key): \Amica\Sdk\Model\InventarioComputeResult
```

Elabora un inventario senza salvare nulla.

Calcola una riga per ogni giacenza del magazzino dell'inventario (esclusi gli articoli             inattivi, se richiesto), partendo dall'inventario fiscale precedente e applicando i movimenti fino alla             data dell'inventario inclusa. Il valore di ogni riga è la quantità per il costo unitario, medio o             ultimo secondo la valorizzazione; il costo cambia solo con i carichi di acquisto.               Non scrive nulla: serve come anteprima, o quando basta il conteggio. Il `ConcurrencyToken`             restituito, passato a `process`, garantisce che venga salvato esattamente il risultato mostrato.

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


$apiInstance = new Amica\Sdk\Api\InventarioApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$id = 56; // int | ID univoco dell'inventario da elaborare.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.

try {
    $result = $apiInstance->apiVversionCompanyIdInventarioIdComputePost($version, $company_id, $id, $idempotency_key);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling InventarioApi->apiVversionCompanyIdInventarioIdComputePost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **id** | **int**| ID univoco dell&#39;inventario da elaborare. | |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |

### Return type

[**\Amica\Sdk\Model\InventarioComputeResult**](../Model/InventarioComputeResult.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdInventarioIdDelete()`

```php
apiVversionCompanyIdInventarioIdDelete($version, $company_id, $id): \Amica\Sdk\Model\Inventario
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


$apiInstance = new Amica\Sdk\Api\InventarioApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$id = 56; // int | ID dell'elemento da eliminare.

try {
    $result = $apiInstance->apiVversionCompanyIdInventarioIdDelete($version, $company_id, $id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling InventarioApi->apiVversionCompanyIdInventarioIdDelete: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **id** | **int**| ID dell&#39;elemento da eliminare. | |

### Return type

[**\Amica\Sdk\Model\Inventario**](../Model/Inventario.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdInventarioIdGet()`

```php
apiVversionCompanyIdInventarioIdGet($version, $company_id, $id, $expand): \Amica\Sdk\Model\Inventario
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


$apiInstance = new Amica\Sdk\Api\InventarioApi(
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
    $result = $apiInstance->apiVversionCompanyIdInventarioIdGet($version, $company_id, $id, $expand);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling InventarioApi->apiVversionCompanyIdInventarioIdGet: ', $e->getMessage(), PHP_EOL;
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

[**\Amica\Sdk\Model\Inventario**](../Model/Inventario.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdInventarioIdProcessPost()`

```php
apiVversionCompanyIdInventarioIdProcessPost($version, $company_id, $id, $concurrency_token, $align_stock, $idempotency_key): \Amica\Sdk\Model\InventarioComputeResult
```

Elabora un inventario e ne salva le righe.

Rifà l'elaborazione di `compute` e sostituisce le righe dell'inventario con quelle             calcolate. Con `alignStock` riscrive anche la quantità in giacenza di ogni giacenza elaborata,             ma solo se nel magazzino non ci sono movimenti successivi alla data dell'inventario. Tutto avviene in             un'unica transazione.

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


$apiInstance = new Amica\Sdk\Api\InventarioApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$id = 56; // int | ID univoco dell'inventario da elaborare.
$concurrency_token = 56; // int | Il `ConcurrencyToken` restituito da `compute`. Se indicato e             i dati sono cambiati nel frattempo, così che l'elaborazione dia righe diverse, non salva nulla e             risponde 422. Se omesso, salva il risultato dell'elaborazione corrente.
$align_stock = true; // bool | Se allineare le giacenze del magazzino alle quantità dell'inventario             (default `true`).
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.

try {
    $result = $apiInstance->apiVversionCompanyIdInventarioIdProcessPost($version, $company_id, $id, $concurrency_token, $align_stock, $idempotency_key);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling InventarioApi->apiVversionCompanyIdInventarioIdProcessPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **id** | **int**| ID univoco dell&#39;inventario da elaborare. | |
| **concurrency_token** | **int**| Il &#x60;ConcurrencyToken&#x60; restituito da &#x60;compute&#x60;. Se indicato e             i dati sono cambiati nel frattempo, così che l&#39;elaborazione dia righe diverse, non salva nulla e             risponde 422. Se omesso, salva il risultato dell&#39;elaborazione corrente. | [optional] |
| **align_stock** | **bool**| Se allineare le giacenze del magazzino alle quantità dell&#39;inventario             (default &#x60;true&#x60;). | [optional] [default to true] |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |

### Return type

[**\Amica\Sdk\Model\InventarioComputeResult**](../Model/InventarioComputeResult.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdInventarioIdPut()`

```php
apiVversionCompanyIdInventarioIdPut($version, $company_id, $id, $validation_override, $idempotency_key, $inventario): \Amica\Sdk\Model\Inventario
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


$apiInstance = new Amica\Sdk\Api\InventarioApi(
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
$inventario = new \Amica\Sdk\Model\Inventario(); // \Amica\Sdk\Model\Inventario | Istanza dell'elemento da salvare.

try {
    $result = $apiInstance->apiVversionCompanyIdInventarioIdPut($version, $company_id, $id, $validation_override, $idempotency_key, $inventario);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling InventarioApi->apiVversionCompanyIdInventarioIdPut: ', $e->getMessage(), PHP_EOL;
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
| **inventario** | [**\Amica\Sdk\Model\Inventario**](../Model/Inventario.md)| Istanza dell&#39;elemento da salvare. | [optional] |

### Return type

[**\Amica\Sdk\Model\Inventario**](../Model/Inventario.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdInventarioNewGet()`

```php
apiVversionCompanyIdInventarioNewGet($version, $company_id): \Amica\Sdk\Model\Inventario
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


$apiInstance = new Amica\Sdk\Api\InventarioApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.

try {
    $result = $apiInstance->apiVversionCompanyIdInventarioNewGet($version, $company_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling InventarioApi->apiVversionCompanyIdInventarioNewGet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |

### Return type

[**\Amica\Sdk\Model\Inventario**](../Model/Inventario.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdInventarioPost()`

```php
apiVversionCompanyIdInventarioPost($version, $company_id, $validation_override, $idempotency_key, $inventario): \Amica\Sdk\Model\Inventario
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


$apiInstance = new Amica\Sdk\Api\InventarioApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$validation_override = True; // bool | Se applicare eventualmente l'override della validazione.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$inventario = new \Amica\Sdk\Model\Inventario(); // \Amica\Sdk\Model\Inventario | Istanza dell'elemento da salvare.

try {
    $result = $apiInstance->apiVversionCompanyIdInventarioPost($version, $company_id, $validation_override, $idempotency_key, $inventario);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling InventarioApi->apiVversionCompanyIdInventarioPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **validation_override** | **bool**| Se applicare eventualmente l&#39;override della validazione. | [optional] |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **inventario** | [**\Amica\Sdk\Model\Inventario**](../Model/Inventario.md)| Istanza dell&#39;elemento da salvare. | [optional] |

### Return type

[**\Amica\Sdk\Model\Inventario**](../Model/Inventario.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdInventarioProcessPost()`

```php
apiVversionCompanyIdInventarioProcessPost($version, $company_id, $concurrency_token, $align_stock, $idempotency_key, $inventario): \Amica\Sdk\Model\InventarioComputeResult
```

Salva una testata, la elabora e ne salva le righe.

Come `{id}/process`, ma crea o aggiorna anche la testata, nella stessa transazione delle             righe e delle giacenze: se qualcosa non va non resta niente, nemmeno la testata nuova. L'inventario             salvato, con l'Id assegnato e il nuovo `ConcurrencyToken` della testata, è in             `Inventario`.               Per aggiornare una testata esistente va mandato il suo `ConcurrencyToken`, come in             `PUT`: se nel frattempo un altro client l'ha modificata risponde 422.

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


$apiInstance = new Amica\Sdk\Api\InventarioApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$concurrency_token = 56; // int | Il `ConcurrencyToken` restituito da `compute`. Se indicato e             i dati sono cambiati nel frattempo, così che l'elaborazione dia righe diverse, non salva nulla e             risponde 422. Se omesso, salva il risultato dell'elaborazione corrente.
$align_stock = true; // bool | Se allineare le giacenze del magazzino alle quantità dell'inventario             (default `true`).
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$inventario = new \Amica\Sdk\Model\Inventario(); // \Amica\Sdk\Model\Inventario | Testata da salvare: senza Id viene creata, con l'Id aggiornata. Le righe sono             ignorate.

try {
    $result = $apiInstance->apiVversionCompanyIdInventarioProcessPost($version, $company_id, $concurrency_token, $align_stock, $idempotency_key, $inventario);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling InventarioApi->apiVversionCompanyIdInventarioProcessPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **concurrency_token** | **int**| Il &#x60;ConcurrencyToken&#x60; restituito da &#x60;compute&#x60;. Se indicato e             i dati sono cambiati nel frattempo, così che l&#39;elaborazione dia righe diverse, non salva nulla e             risponde 422. Se omesso, salva il risultato dell&#39;elaborazione corrente. | [optional] |
| **align_stock** | **bool**| Se allineare le giacenze del magazzino alle quantità dell&#39;inventario             (default &#x60;true&#x60;). | [optional] [default to true] |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **inventario** | [**\Amica\Sdk\Model\Inventario**](../Model/Inventario.md)| Testata da salvare: senza Id viene creata, con l&#39;Id aggiornata. Le righe sono             ignorate. | [optional] |

### Return type

[**\Amica\Sdk\Model\InventarioComputeResult**](../Model/InventarioComputeResult.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
