# Amica\Sdk\DocumentoApi

All URIs are relative to http://localhost, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**apiVversionCompanyIdDocumentoComputePost()**](DocumentoApi.md#apiVversionCompanyIdDocumentoComputePost) | **POST** /api/v{version}/{companyId}/Documento/compute | Calcola i totali di un documento. |
| [**apiVversionCompanyIdDocumentoDistinctGet()**](DocumentoApi.md#apiVversionCompanyIdDocumentoDistinctGet) | **GET** /api/v{version}/{companyId}/Documento/distinct | Restituisce una lista di elementi distinti. |
| [**apiVversionCompanyIdDocumentoDocumentoIdEmitPost()**](DocumentoApi.md#apiVversionCompanyIdDocumentoDocumentoIdEmitPost) | **POST** /api/v{version}/{companyId}/Documento/{documentoId}/emit | Emette un documento. |
| [**apiVversionCompanyIdDocumentoDocumentoIdNewinstancefromtemplatePost()**](DocumentoApi.md#apiVversionCompanyIdDocumentoDocumentoIdNewinstancefromtemplatePost) | **POST** /api/v{version}/{companyId}/Documento/{documentoId}/newinstancefromtemplate | Replica un documento. Crea una copia con la data odierna e, se è un documento di vendita, un nuovo numero. Alcuni campi vengono reimpostati ai valori predefiniti (stato del documento, stato SDI, stato del pagamento, ecc.). |
| [**apiVversionCompanyIdDocumentoGet()**](DocumentoApi.md#apiVversionCompanyIdDocumentoGet) | **GET** /api/v{version}/{companyId}/Documento | Restituisce una lista di elementi. |
| [**apiVversionCompanyIdDocumentoIdDelete()**](DocumentoApi.md#apiVversionCompanyIdDocumentoIdDelete) | **DELETE** /api/v{version}/{companyId}/Documento/{id} | Elimina un elemento. |
| [**apiVversionCompanyIdDocumentoIdGet()**](DocumentoApi.md#apiVversionCompanyIdDocumentoIdGet) | **GET** /api/v{version}/{companyId}/Documento/{id} | Restituisce un elemento. |
| [**apiVversionCompanyIdDocumentoIdImportAccontoPost()**](DocumentoApi.md#apiVversionCompanyIdDocumentoIdImportAccontoPost) | **POST** /api/v{version}/{companyId}/Documento/{id}/import/acconto | Importa righe da uno o più acconti in un documento esistente. |
| [**apiVversionCompanyIdDocumentoIdImportDdtPost()**](DocumentoApi.md#apiVversionCompanyIdDocumentoIdImportDdtPost) | **POST** /api/v{version}/{companyId}/Documento/{id}/import/ddt | Importa righe da uno o più DDT in un documento esistente. |
| [**apiVversionCompanyIdDocumentoIdImportOrdinePost()**](DocumentoApi.md#apiVversionCompanyIdDocumentoIdImportOrdinePost) | **POST** /api/v{version}/{companyId}/Documento/{id}/import/ordine | Importa righe da uno o più ordini in un documento esistente. |
| [**apiVversionCompanyIdDocumentoIdImportPreventivoPost()**](DocumentoApi.md#apiVversionCompanyIdDocumentoIdImportPreventivoPost) | **POST** /api/v{version}/{companyId}/Documento/{id}/import/preventivo | Importa righe da uno o più preventivi in un documento esistente. |
| [**apiVversionCompanyIdDocumentoIdImportProformaPost()**](DocumentoApi.md#apiVversionCompanyIdDocumentoIdImportProformaPost) | **POST** /api/v{version}/{companyId}/Documento/{id}/import/proforma | Importa righe da una o più fatture proforma in un documento esistente. |
| [**apiVversionCompanyIdDocumentoIdImportResoPost()**](DocumentoApi.md#apiVversionCompanyIdDocumentoIdImportResoPost) | **POST** /api/v{version}/{companyId}/Documento/{id}/import/reso | Importa righe da uno o più resi in un documento esistente. |
| [**apiVversionCompanyIdDocumentoIdPut()**](DocumentoApi.md#apiVversionCompanyIdDocumentoIdPut) | **PUT** /api/v{version}/{companyId}/Documento/{id} | Aggiorna un elemento esistente. |
| [**apiVversionCompanyIdDocumentoNewGet()**](DocumentoApi.md#apiVversionCompanyIdDocumentoNewGet) | **GET** /api/v{version}/{companyId}/Documento/new | Restituisce una nuova istanza di un documento del tipo specificato. |
| [**apiVversionCompanyIdDocumentoPeriodicinvoicingPost()**](DocumentoApi.md#apiVversionCompanyIdDocumentoPeriodicinvoicingPost) | **POST** /api/v{version}/{companyId}/Documento/periodicinvoicing | Genera fatture per tutti i contatti che corrispondono ai criteri di fatturazione periodica. |
| [**apiVversionCompanyIdDocumentoPost()**](DocumentoApi.md#apiVversionCompanyIdDocumentoPost) | **POST** /api/v{version}/{companyId}/Documento | Salva un nuovo elemento. |
| [**apiVversionCompanyIdDocumentoTotaliGet()**](DocumentoApi.md#apiVversionCompanyIdDocumentoTotaliGet) | **GET** /api/v{version}/{companyId}/Documento/totali | Restituisce i totali dei documenti che soddisfano il filtro, eventualmente raggruppati per mese, anno o anagrafica. I totali sono calcolati dal server: per conoscere il fatturato di un periodo non serve scaricare i documenti. |


## `apiVversionCompanyIdDocumentoComputePost()`

```php
apiVversionCompanyIdDocumentoComputePost($version, $company_id, $idempotency_key, $documento_compute_data): \Amica\Sdk\Model\DocumentoComputeResult
```

Calcola i totali di un documento.

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


$apiInstance = new Amica\Sdk\Api\DocumentoApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$documento_compute_data = new \Amica\Sdk\Model\DocumentoComputeData(); // \Amica\Sdk\Model\DocumentoComputeData | Dati di input per l'operazione di calcolo.

try {
    $result = $apiInstance->apiVversionCompanyIdDocumentoComputePost($version, $company_id, $idempotency_key, $documento_compute_data);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DocumentoApi->apiVversionCompanyIdDocumentoComputePost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **documento_compute_data** | [**\Amica\Sdk\Model\DocumentoComputeData**](../Model/DocumentoComputeData.md)| Dati di input per l&#39;operazione di calcolo. | [optional] |

### Return type

[**\Amica\Sdk\Model\DocumentoComputeResult**](../Model/DocumentoComputeResult.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `text/plain`, `application/json`, `text/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdDocumentoDistinctGet()`

```php
apiVversionCompanyIdDocumentoDistinctGet($version, $company_id, $selector, $skip, $take): \Amica\Sdk\Model\DistinctPropertyGroup[]
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


$apiInstance = new Amica\Sdk\Api\DocumentoApi(
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
    $result = $apiInstance->apiVversionCompanyIdDocumentoDistinctGet($version, $company_id, $selector, $skip, $take);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DocumentoApi->apiVversionCompanyIdDocumentoDistinctGet: ', $e->getMessage(), PHP_EOL;
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

## `apiVversionCompanyIdDocumentoDocumentoIdEmitPost()`

```php
apiVversionCompanyIdDocumentoDocumentoIdEmitPost($version, $company_id, $documento_id, $idempotency_key): \Amica\Sdk\Model\Documento
```

Emette un documento.

A seconda del tipo di documento, l'emissione comporta una serie di operazioni amministrative e contabili:               1) Aggiornamento dell'`Anagrafica` coinvolta (fatturato, numero fatture, ecc.);              2) Creazione della registrazione contabile di emissione;              3) Creazione delle scadenze attive o passive, in base al tipo di pagamento;              4) Eventuale autoproduzione dei beni fatturati tramite distinta base, se l'opzione è attiva;              5) Creazione del movimento di magazzino;              6) Aggiornamento delle giacenze dei beni coinvolti, nei rispettivi magazzini;              7) Aggiornamento di eventuali ordini collegati.               Un documento già emesso non può essere riemesso. Alcuni tipi di documento non supportano l'emissione (ordini e preventivi, ad esempio).

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


$apiInstance = new Amica\Sdk\Api\DocumentoApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$documento_id = 56; // int | ID univoco del documento da emettere.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.

try {
    $result = $apiInstance->apiVversionCompanyIdDocumentoDocumentoIdEmitPost($version, $company_id, $documento_id, $idempotency_key);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DocumentoApi->apiVversionCompanyIdDocumentoDocumentoIdEmitPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **documento_id** | **int**| ID univoco del documento da emettere. | |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |

### Return type

[**\Amica\Sdk\Model\Documento**](../Model/Documento.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `text/plain`, `application/json`, `text/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdDocumentoDocumentoIdNewinstancefromtemplatePost()`

```php
apiVversionCompanyIdDocumentoDocumentoIdNewinstancefromtemplatePost($version, $documento_id, $company_id, $idempotency_key): \Amica\Sdk\Model\Documento
```

Replica un documento. Crea una copia con la data odierna e, se è un documento di vendita, un nuovo numero. Alcuni campi vengono reimpostati ai valori predefiniti (stato del documento, stato SDI, stato del pagamento, ecc.).

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


$apiInstance = new Amica\Sdk\Api\DocumentoApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$documento_id = 56; // int | ID univoco del documento.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.

try {
    $result = $apiInstance->apiVversionCompanyIdDocumentoDocumentoIdNewinstancefromtemplatePost($version, $documento_id, $company_id, $idempotency_key);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DocumentoApi->apiVversionCompanyIdDocumentoDocumentoIdNewinstancefromtemplatePost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **documento_id** | **int**| ID univoco del documento. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |

### Return type

[**\Amica\Sdk\Model\Documento**](../Model/Documento.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `text/plain`, `application/json`, `text/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdDocumentoGet()`

```php
apiVversionCompanyIdDocumentoGet($version, $company_id, $skip, $take, $skip_total_count, $filter, $order, $expand): \Amica\Sdk\Model\Documento[]
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


$apiInstance = new Amica\Sdk\Api\DocumentoApi(
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
    $result = $apiInstance->apiVversionCompanyIdDocumentoGet($version, $company_id, $skip, $take, $skip_total_count, $filter, $order, $expand);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DocumentoApi->apiVversionCompanyIdDocumentoGet: ', $e->getMessage(), PHP_EOL;
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

[**\Amica\Sdk\Model\Documento[]**](../Model/Documento.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdDocumentoIdDelete()`

```php
apiVversionCompanyIdDocumentoIdDelete($version, $company_id, $id): \Amica\Sdk\Model\Documento
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


$apiInstance = new Amica\Sdk\Api\DocumentoApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$id = 56; // int | ID dell'elemento da eliminare.

try {
    $result = $apiInstance->apiVversionCompanyIdDocumentoIdDelete($version, $company_id, $id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DocumentoApi->apiVversionCompanyIdDocumentoIdDelete: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **id** | **int**| ID dell&#39;elemento da eliminare. | |

### Return type

[**\Amica\Sdk\Model\Documento**](../Model/Documento.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdDocumentoIdGet()`

```php
apiVversionCompanyIdDocumentoIdGet($version, $company_id, $id, $expand): \Amica\Sdk\Model\Documento
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


$apiInstance = new Amica\Sdk\Api\DocumentoApi(
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
    $result = $apiInstance->apiVversionCompanyIdDocumentoIdGet($version, $company_id, $id, $expand);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DocumentoApi->apiVversionCompanyIdDocumentoIdGet: ', $e->getMessage(), PHP_EOL;
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

[**\Amica\Sdk\Model\Documento**](../Model/Documento.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdDocumentoIdImportAccontoPost()`

```php
apiVversionCompanyIdDocumentoIdImportAccontoPost($version, $company_id, $id, $validation_override, $idempotency_key, $documento_import_data): \Amica\Sdk\Model\Documento
```

Importa righe da uno o più acconti in un documento esistente.

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


$apiInstance = new Amica\Sdk\Api\DocumentoApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$id = 56; // int | ID del documento di destinazione.
$validation_override = True; // bool | Se applicare eventualmente l'override della validazione.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$documento_import_data = new \Amica\Sdk\Model\DocumentoImportData(); // \Amica\Sdk\Model\DocumentoImportData | Opzioni di importazione, inclusi gli ID degli acconti da importare.

try {
    $result = $apiInstance->apiVversionCompanyIdDocumentoIdImportAccontoPost($version, $company_id, $id, $validation_override, $idempotency_key, $documento_import_data);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DocumentoApi->apiVversionCompanyIdDocumentoIdImportAccontoPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **id** | **int**| ID del documento di destinazione. | |
| **validation_override** | **bool**| Se applicare eventualmente l&#39;override della validazione. | [optional] |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **documento_import_data** | [**\Amica\Sdk\Model\DocumentoImportData**](../Model/DocumentoImportData.md)| Opzioni di importazione, inclusi gli ID degli acconti da importare. | [optional] |

### Return type

[**\Amica\Sdk\Model\Documento**](../Model/Documento.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdDocumentoIdImportDdtPost()`

```php
apiVversionCompanyIdDocumentoIdImportDdtPost($version, $company_id, $id, $validation_override, $idempotency_key, $ddt_import_data): \Amica\Sdk\Model\Documento
```

Importa righe da uno o più DDT in un documento esistente.

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


$apiInstance = new Amica\Sdk\Api\DocumentoApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$id = 56; // int | ID del documento di destinazione.
$validation_override = True; // bool | Se applicare eventualmente l'override della validazione.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$ddt_import_data = new \Amica\Sdk\Model\DdtImportData(); // \Amica\Sdk\Model\DdtImportData | Opzioni di importazione, inclusi gli ID dei DDT da importare.

try {
    $result = $apiInstance->apiVversionCompanyIdDocumentoIdImportDdtPost($version, $company_id, $id, $validation_override, $idempotency_key, $ddt_import_data);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DocumentoApi->apiVversionCompanyIdDocumentoIdImportDdtPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **id** | **int**| ID del documento di destinazione. | |
| **validation_override** | **bool**| Se applicare eventualmente l&#39;override della validazione. | [optional] |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **ddt_import_data** | [**\Amica\Sdk\Model\DdtImportData**](../Model/DdtImportData.md)| Opzioni di importazione, inclusi gli ID dei DDT da importare. | [optional] |

### Return type

[**\Amica\Sdk\Model\Documento**](../Model/Documento.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdDocumentoIdImportOrdinePost()`

```php
apiVversionCompanyIdDocumentoIdImportOrdinePost($version, $company_id, $id, $validation_override, $idempotency_key, $ordini_import_data): \Amica\Sdk\Model\Documento
```

Importa righe da uno o più ordini in un documento esistente.

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


$apiInstance = new Amica\Sdk\Api\DocumentoApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$id = 56; // int | ID del documento di destinazione.
$validation_override = True; // bool | Se applicare eventualmente l'override della validazione.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$ordini_import_data = new \Amica\Sdk\Model\OrdiniImportData(); // \Amica\Sdk\Model\OrdiniImportData | Opzioni di importazione, inclusi gli ID degli ordini da importare.

try {
    $result = $apiInstance->apiVversionCompanyIdDocumentoIdImportOrdinePost($version, $company_id, $id, $validation_override, $idempotency_key, $ordini_import_data);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DocumentoApi->apiVversionCompanyIdDocumentoIdImportOrdinePost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **id** | **int**| ID del documento di destinazione. | |
| **validation_override** | **bool**| Se applicare eventualmente l&#39;override della validazione. | [optional] |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **ordini_import_data** | [**\Amica\Sdk\Model\OrdiniImportData**](../Model/OrdiniImportData.md)| Opzioni di importazione, inclusi gli ID degli ordini da importare. | [optional] |

### Return type

[**\Amica\Sdk\Model\Documento**](../Model/Documento.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdDocumentoIdImportPreventivoPost()`

```php
apiVversionCompanyIdDocumentoIdImportPreventivoPost($version, $company_id, $id, $validation_override, $idempotency_key, $documento_import_data): \Amica\Sdk\Model\Documento
```

Importa righe da uno o più preventivi in un documento esistente.

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


$apiInstance = new Amica\Sdk\Api\DocumentoApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$id = 56; // int | ID del documento di destinazione.
$validation_override = True; // bool | Se applicare eventualmente l'override della validazione.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$documento_import_data = new \Amica\Sdk\Model\DocumentoImportData(); // \Amica\Sdk\Model\DocumentoImportData | Opzioni di importazione, inclusi gli ID dei preventivi da importare.

try {
    $result = $apiInstance->apiVversionCompanyIdDocumentoIdImportPreventivoPost($version, $company_id, $id, $validation_override, $idempotency_key, $documento_import_data);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DocumentoApi->apiVversionCompanyIdDocumentoIdImportPreventivoPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **id** | **int**| ID del documento di destinazione. | |
| **validation_override** | **bool**| Se applicare eventualmente l&#39;override della validazione. | [optional] |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **documento_import_data** | [**\Amica\Sdk\Model\DocumentoImportData**](../Model/DocumentoImportData.md)| Opzioni di importazione, inclusi gli ID dei preventivi da importare. | [optional] |

### Return type

[**\Amica\Sdk\Model\Documento**](../Model/Documento.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdDocumentoIdImportProformaPost()`

```php
apiVversionCompanyIdDocumentoIdImportProformaPost($version, $company_id, $id, $validation_override, $idempotency_key, $documento_import_data): \Amica\Sdk\Model\Documento
```

Importa righe da una o più fatture proforma in un documento esistente.

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


$apiInstance = new Amica\Sdk\Api\DocumentoApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$id = 56; // int | ID del documento di destinazione.
$validation_override = True; // bool | Se applicare eventualmente l'override della validazione.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$documento_import_data = new \Amica\Sdk\Model\DocumentoImportData(); // \Amica\Sdk\Model\DocumentoImportData | Opzioni di importazione, inclusi gli ID delle proforma da importare.

try {
    $result = $apiInstance->apiVversionCompanyIdDocumentoIdImportProformaPost($version, $company_id, $id, $validation_override, $idempotency_key, $documento_import_data);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DocumentoApi->apiVversionCompanyIdDocumentoIdImportProformaPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **id** | **int**| ID del documento di destinazione. | |
| **validation_override** | **bool**| Se applicare eventualmente l&#39;override della validazione. | [optional] |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **documento_import_data** | [**\Amica\Sdk\Model\DocumentoImportData**](../Model/DocumentoImportData.md)| Opzioni di importazione, inclusi gli ID delle proforma da importare. | [optional] |

### Return type

[**\Amica\Sdk\Model\Documento**](../Model/Documento.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdDocumentoIdImportResoPost()`

```php
apiVversionCompanyIdDocumentoIdImportResoPost($version, $company_id, $id, $validation_override, $idempotency_key, $reso_import_data): \Amica\Sdk\Model\Documento
```

Importa righe da uno o più resi in un documento esistente.

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


$apiInstance = new Amica\Sdk\Api\DocumentoApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$id = 56; // int | ID del documento di destinazione.
$validation_override = True; // bool | Se applicare eventualmente l'override della validazione.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$reso_import_data = new \Amica\Sdk\Model\ResoImportData(); // \Amica\Sdk\Model\ResoImportData | Opzioni di importazione, inclusi gli ID dei resi da importare.

try {
    $result = $apiInstance->apiVversionCompanyIdDocumentoIdImportResoPost($version, $company_id, $id, $validation_override, $idempotency_key, $reso_import_data);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DocumentoApi->apiVversionCompanyIdDocumentoIdImportResoPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **id** | **int**| ID del documento di destinazione. | |
| **validation_override** | **bool**| Se applicare eventualmente l&#39;override della validazione. | [optional] |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **reso_import_data** | [**\Amica\Sdk\Model\ResoImportData**](../Model/ResoImportData.md)| Opzioni di importazione, inclusi gli ID dei resi da importare. | [optional] |

### Return type

[**\Amica\Sdk\Model\Documento**](../Model/Documento.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdDocumentoIdPut()`

```php
apiVversionCompanyIdDocumentoIdPut($version, $company_id, $id, $validation_override, $idempotency_key, $documento): \Amica\Sdk\Model\Documento
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


$apiInstance = new Amica\Sdk\Api\DocumentoApi(
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
$documento = new \Amica\Sdk\Model\Documento(); // \Amica\Sdk\Model\Documento | Istanza dell'elemento da salvare.

try {
    $result = $apiInstance->apiVversionCompanyIdDocumentoIdPut($version, $company_id, $id, $validation_override, $idempotency_key, $documento);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DocumentoApi->apiVversionCompanyIdDocumentoIdPut: ', $e->getMessage(), PHP_EOL;
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
| **documento** | [**\Amica\Sdk\Model\Documento**](../Model/Documento.md)| Istanza dell&#39;elemento da salvare. | [optional] |

### Return type

[**\Amica\Sdk\Model\Documento**](../Model/Documento.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdDocumentoNewGet()`

```php
apiVversionCompanyIdDocumentoNewGet($version, $company_id): \Amica\Sdk\Model\Documento
```

Restituisce una nuova istanza di un documento del tipo specificato.

Richiede il parametro query `TipoDocumento` (int) che indica il tipo di documento da creare.

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


$apiInstance = new Amica\Sdk\Api\DocumentoApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.

try {
    $result = $apiInstance->apiVversionCompanyIdDocumentoNewGet($version, $company_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DocumentoApi->apiVversionCompanyIdDocumentoNewGet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |

### Return type

[**\Amica\Sdk\Model\Documento**](../Model/Documento.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdDocumentoPeriodicinvoicingPost()`

```php
apiVversionCompanyIdDocumentoPeriodicinvoicingPost($version, $company_id, $id, $validation_override, $idempotency_key, $periodic_invoicing_data): \Amica\Sdk\Model\Documento[]
```

Genera fatture per tutti i contatti che corrispondono ai criteri di fatturazione periodica.

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


$apiInstance = new Amica\Sdk\Api\DocumentoApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$id = 56; // int | Non utilizzato.
$validation_override = True; // bool | Se applicare eventualmente l'override della validazione.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$periodic_invoicing_data = new \Amica\Sdk\Model\PeriodicInvoicingData(); // \Amica\Sdk\Model\PeriodicInvoicingData | Criteri e opzioni di fatturazione periodica.

try {
    $result = $apiInstance->apiVversionCompanyIdDocumentoPeriodicinvoicingPost($version, $company_id, $id, $validation_override, $idempotency_key, $periodic_invoicing_data);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DocumentoApi->apiVversionCompanyIdDocumentoPeriodicinvoicingPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **id** | **int**| Non utilizzato. | [optional] |
| **validation_override** | **bool**| Se applicare eventualmente l&#39;override della validazione. | [optional] |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **periodic_invoicing_data** | [**\Amica\Sdk\Model\PeriodicInvoicingData**](../Model/PeriodicInvoicingData.md)| Criteri e opzioni di fatturazione periodica. | [optional] |

### Return type

[**\Amica\Sdk\Model\Documento[]**](../Model/Documento.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdDocumentoPost()`

```php
apiVversionCompanyIdDocumentoPost($version, $company_id, $validation_override, $idempotency_key, $documento): \Amica\Sdk\Model\Documento
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


$apiInstance = new Amica\Sdk\Api\DocumentoApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$validation_override = True; // bool | Se applicare eventualmente l'override della validazione.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$documento = new \Amica\Sdk\Model\Documento(); // \Amica\Sdk\Model\Documento | Istanza dell'elemento da salvare.

try {
    $result = $apiInstance->apiVversionCompanyIdDocumentoPost($version, $company_id, $validation_override, $idempotency_key, $documento);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DocumentoApi->apiVversionCompanyIdDocumentoPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **validation_override** | **bool**| Se applicare eventualmente l&#39;override della validazione. | [optional] |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **documento** | [**\Amica\Sdk\Model\Documento**](../Model/Documento.md)| Istanza dell&#39;elemento da salvare. | [optional] |

### Return type

[**\Amica\Sdk\Model\Documento**](../Model/Documento.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdDocumentoTotaliGet()`

```php
apiVversionCompanyIdDocumentoTotaliGet($version, $company_id, $filter, $raggruppa): \Amica\Sdk\Model\DocumentoTotali[]
```

Restituisce i totali dei documenti che soddisfano il filtro, eventualmente raggruppati per mese, anno o anagrafica. I totali sono calcolati dal server: per conoscere il fatturato di un periodo non serve scaricare i documenti.

Le note di credito, clienti e fornitori, entrano nei totali con segno negativo.  Senza raggruppamento la risposta contiene un solo gruppo, anche se nessun documento soddisfa il filtro.             Raggruppando per mese o per anno i gruppi sono in ordine cronologico e `Periodo` ne indica il primo giorno;             raggruppando per anagrafica sono in ordine di totale documento decrescente, con `AnagraficaId` e             `RagioneSociale`.

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


$apiInstance = new Amica\Sdk\Api\DocumentoApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$filter = ClasseDocumento $eq 1, Data $gte 2026-01-01, Data $lte 2026-09-30; // string | Condizioni di ricerca che selezionano i documenti da sommare, con la stessa sintassi del parametro `Filter` dell'elenco dei documenti. Senza filtro si sommano tutti i documenti dell'azienda, compresi ordini e preventivi: per il fatturato, filtrare per classe e tipo di documento.
$raggruppa = Mese; // string | Come raggruppare i totali: `Nessuno` (un solo gruppo, predefinito), `Mese`, `Anno` o `Anagrafica`.

try {
    $result = $apiInstance->apiVversionCompanyIdDocumentoTotaliGet($version, $company_id, $filter, $raggruppa);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DocumentoApi->apiVversionCompanyIdDocumentoTotaliGet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **filter** | **string**| Condizioni di ricerca che selezionano i documenti da sommare, con la stessa sintassi del parametro &#x60;Filter&#x60; dell&#39;elenco dei documenti. Senza filtro si sommano tutti i documenti dell&#39;azienda, compresi ordini e preventivi: per il fatturato, filtrare per classe e tipo di documento. | [optional] |
| **raggruppa** | **string**| Come raggruppare i totali: &#x60;Nessuno&#x60; (un solo gruppo, predefinito), &#x60;Mese&#x60;, &#x60;Anno&#x60; o &#x60;Anagrafica&#x60;. | [optional] |

### Return type

[**\Amica\Sdk\Model\DocumentoTotali[]**](../Model/DocumentoTotali.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
