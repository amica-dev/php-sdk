# Amica\Sdk\FatturaElettronicaXmlApi

All URIs are relative to http://localhost, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**fatturaElettronicaXmlApplyStyleSheet()**](FatturaElettronicaXmlApi.md#fatturaElettronicaXmlApplyStyleSheet) | **POST** /api/v3/{companyId}/FatturaElettronicaXml/stylesheet | Applica un foglio di stile CSS a una fattura XML esistente e restituisce il risultato. |
| [**fatturaElettronicaXmlArchiveByDateRange()**](FatturaElettronicaXmlApi.md#fatturaElettronicaXmlArchiveByDateRange) | **POST** /api/v3/{companyId}/FatturaElettronicaXml/archivebydaterange | Scarica un archivio ZIP di fatture XML, filtrate per intervallo di date. |
| [**fatturaElettronicaXmlArchiveByFileName()**](FatturaElettronicaXmlApi.md#fatturaElettronicaXmlArchiveByFileName) | **POST** /api/v3/{companyId}/FatturaElettronicaXml/archivebyfilename | Scarica un archivio ZIP di fatture XML, filtrate per nome file. |
| [**fatturaElettronicaXmlBulkAdd()**](FatturaElettronicaXmlApi.md#fatturaElettronicaXmlBulkAdd) | **POST** /api/v3/{companyId}/FatturaElettronicaXml/bulk | Carica in blocco fatture XML da un archivio ZIP. |
| [**fatturaElettronicaXmlBulkDelete()**](FatturaElettronicaXmlApi.md#fatturaElettronicaXmlBulkDelete) | **DELETE** /api/v3/{companyId}/FatturaElettronicaXml/bulk | Elimina in blocco tutte le fatture XML dell&#39;azienda. |
| [**fatturaElettronicaXmlDelete()**](FatturaElettronicaXmlApi.md#fatturaElettronicaXmlDelete) | **DELETE** /api/v3/{companyId}/FatturaElettronicaXml/{id} | Elimina un elemento. |
| [**fatturaElettronicaXmlDownloadEsiti()**](FatturaElettronicaXmlApi.md#fatturaElettronicaXmlDownloadEsiti) | **POST** /api/v3/{companyId}/FatturaElettronicaXml/sdi/download/esiti | Interroga il Sistema di Interscambio (SDI) e scarica gli esiti di invio delle fatture precedentemente caricate. |
| [**fatturaElettronicaXmlDownloadFatture()**](FatturaElettronicaXmlApi.md#fatturaElettronicaXmlDownloadFatture) | **POST** /api/v3/{companyId}/FatturaElettronicaXml/sdi/download/fatture | Scarica le nuove fatture dal Sistema di Interscambio (SDI). |
| [**fatturaElettronicaXmlExport()**](FatturaElettronicaXmlApi.md#fatturaElettronicaXmlExport) | **POST** /api/v3/{companyId}/FatturaElettronicaXml/export | Esporta uno o più documenti in una fattura XML. |
| [**fatturaElettronicaXmlGet()**](FatturaElettronicaXmlApi.md#fatturaElettronicaXmlGet) | **GET** /api/v3/{companyId}/FatturaElettronicaXml/{id} | Restituisce un elemento. |
| [**fatturaElettronicaXmlGetAllAsArchive()**](FatturaElettronicaXmlApi.md#fatturaElettronicaXmlGetAllAsArchive) | **GET** /api/v3/{companyId}/FatturaElettronicaXml/bulk | Scarica tutte le fatture XML come archivio ZIP. |
| [**fatturaElettronicaXmlGetAsXml()**](FatturaElettronicaXmlApi.md#fatturaElettronicaXmlGetAsXml) | **GET** /api/v3/{companyId}/FatturaElettronicaXml/{fileName} | Recupera una fattura XML per nome file. |
| [**fatturaElettronicaXmlGetAttachments()**](FatturaElettronicaXmlApi.md#fatturaElettronicaXmlGetAttachments) | **GET** /api/v3/{companyId}/FatturaElettronicaXml/{fileName}/attachments | Scarica i file allegati di una fattura XML. |
| [**fatturaElettronicaXmlImport()**](FatturaElettronicaXmlApi.md#fatturaElettronicaXmlImport) | **POST** /api/v3/{companyId}/FatturaElettronicaXml/import | Importa una fattura XML. |
| [**fatturaElettronicaXmlList()**](FatturaElettronicaXmlApi.md#fatturaElettronicaXmlList) | **GET** /api/v3/{companyId}/FatturaElettronicaXml | Restituisce una lista di elementi. |
| [**fatturaElettronicaXmlNewInstance()**](FatturaElettronicaXmlApi.md#fatturaElettronicaXmlNewInstance) | **GET** /api/v3/{companyId}/FatturaElettronicaXml/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di &#x60;Documento&#x60; avrà il &#x60;Numero&#x60; appropriato, basato sui documenti preesistenti di tipo compatibile). |
| [**fatturaElettronicaXmlPair()**](FatturaElettronicaXmlApi.md#fatturaElettronicaXmlPair) | **POST** /api/v3/{companyId}/FatturaElettronicaXml/pair | Associa una fattura XML a un documento esistente. |
| [**fatturaElettronicaXmlPost()**](FatturaElettronicaXmlApi.md#fatturaElettronicaXmlPost) | **POST** /api/v3/{companyId}/FatturaElettronicaXml | Salva un nuovo elemento. |
| [**fatturaElettronicaXmlPut()**](FatturaElettronicaXmlApi.md#fatturaElettronicaXmlPut) | **PUT** /api/v3/{companyId}/FatturaElettronicaXml/{id} | Aggiorna un elemento esistente. |
| [**fatturaElettronicaXmlUpload()**](FatturaElettronicaXmlApi.md#fatturaElettronicaXmlUpload) | **POST** /api/v3/{companyId}/FatturaElettronicaXml/sdi/upload | Invia una o più fatture al Sistema di Interscambio (SDI). |


## `fatturaElettronicaXmlApplyStyleSheet()`

```php
fatturaElettronicaXmlApplyStyleSheet($company_id, $idempotency_key, $fattura_elettronica_xml_style_sheet_data)
```

Applica un foglio di stile CSS a una fattura XML esistente e restituisce il risultato.

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


$apiInstance = new Amica\Sdk\Api\FatturaElettronicaXmlApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$fattura_elettronica_xml_style_sheet_data = new \Amica\Sdk\Model\FatturaElettronicaXmlStyleSheetData(); // \Amica\Sdk\Model\FatturaElettronicaXmlStyleSheetData | Opzioni di corrispondenza e stile.

try {
    $apiInstance->fatturaElettronicaXmlApplyStyleSheet($company_id, $idempotency_key, $fattura_elettronica_xml_style_sheet_data);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->fatturaElettronicaXmlApplyStyleSheet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **fattura_elettronica_xml_style_sheet_data** | [**\Amica\Sdk\Model\FatturaElettronicaXmlStyleSheetData**](../Model/FatturaElettronicaXmlStyleSheetData.md)| Opzioni di corrispondenza e stile. | [optional] |

### Return type

void (empty response body)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/xml`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `fatturaElettronicaXmlArchiveByDateRange()`

```php
fatturaElettronicaXmlArchiveByDateRange($company_id, $idempotency_key, $fattura_elettronica_xml_get_archive_by_date_range_options)
```

Scarica un archivio ZIP di fatture XML, filtrate per intervallo di date.

Non è possibile scaricare più di 350 file alla volta.

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


$apiInstance = new Amica\Sdk\Api\FatturaElettronicaXmlApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$fattura_elettronica_xml_get_archive_by_date_range_options = new \Amica\Sdk\Model\FatturaElettronicaXmlGetArchiveByDateRangeOptions(); // \Amica\Sdk\Model\FatturaElettronicaXmlGetArchiveByDateRangeOptions | Opzioni di download.

try {
    $apiInstance->fatturaElettronicaXmlArchiveByDateRange($company_id, $idempotency_key, $fattura_elettronica_xml_get_archive_by_date_range_options);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->fatturaElettronicaXmlArchiveByDateRange: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **fattura_elettronica_xml_get_archive_by_date_range_options** | [**\Amica\Sdk\Model\FatturaElettronicaXmlGetArchiveByDateRangeOptions**](../Model/FatturaElettronicaXmlGetArchiveByDateRangeOptions.md)| Opzioni di download. | [optional] |

### Return type

void (empty response body)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/zip`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `fatturaElettronicaXmlArchiveByFileName()`

```php
fatturaElettronicaXmlArchiveByFileName($company_id, $idempotency_key, $fattura_elettronica_xml_get_archive_by_file_name_options)
```

Scarica un archivio ZIP di fatture XML, filtrate per nome file.

Non è possibile scaricare più di 350 file alla volta.

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


$apiInstance = new Amica\Sdk\Api\FatturaElettronicaXmlApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$fattura_elettronica_xml_get_archive_by_file_name_options = new \Amica\Sdk\Model\FatturaElettronicaXmlGetArchiveByFileNameOptions(); // \Amica\Sdk\Model\FatturaElettronicaXmlGetArchiveByFileNameOptions | Opzioni di download.

try {
    $apiInstance->fatturaElettronicaXmlArchiveByFileName($company_id, $idempotency_key, $fattura_elettronica_xml_get_archive_by_file_name_options);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->fatturaElettronicaXmlArchiveByFileName: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **fattura_elettronica_xml_get_archive_by_file_name_options** | [**\Amica\Sdk\Model\FatturaElettronicaXmlGetArchiveByFileNameOptions**](../Model/FatturaElettronicaXmlGetArchiveByFileNameOptions.md)| Opzioni di download. | [optional] |

### Return type

void (empty response body)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/zip`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `fatturaElettronicaXmlBulkAdd()`

```php
fatturaElettronicaXmlBulkAdd($company_id, $idempotency_key, $file)
```

Carica in blocco fatture XML da un archivio ZIP.

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


$apiInstance = new Amica\Sdk\Api\FatturaElettronicaXmlApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$file = '/path/to/file.txt'; // \SplFileObject | Archivio ZIP contenente i file XML.

try {
    $apiInstance->fatturaElettronicaXmlBulkAdd($company_id, $idempotency_key, $file);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->fatturaElettronicaXmlBulkAdd: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **file** | **\SplFileObject****\SplFileObject**| Archivio ZIP contenente i file XML. | [optional] |

### Return type

void (empty response body)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `multipart/form-data`
- **Accept**: `text/plain`, `application/json`, `text/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `fatturaElettronicaXmlBulkDelete()`

```php
fatturaElettronicaXmlBulkDelete($company_id)
```

Elimina in blocco tutte le fatture XML dell'azienda.

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


$apiInstance = new Amica\Sdk\Api\FatturaElettronicaXmlApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.

try {
    $apiInstance->fatturaElettronicaXmlBulkDelete($company_id);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->fatturaElettronicaXmlBulkDelete: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |

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

## `fatturaElettronicaXmlDelete()`

```php
fatturaElettronicaXmlDelete($company_id, $id, $validation_override): \Amica\Sdk\Model\FatturaElettronicaXml
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


$apiInstance = new Amica\Sdk\Api\FatturaElettronicaXmlApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$id = 56; // int | ID dell'elemento da eliminare.
$validation_override = True; // bool | Se applicare eventualmente l'override della validazione.

try {
    $result = $apiInstance->fatturaElettronicaXmlDelete($company_id, $id, $validation_override);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->fatturaElettronicaXmlDelete: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **id** | **int**| ID dell&#39;elemento da eliminare. | |
| **validation_override** | **bool**| Se applicare eventualmente l&#39;override della validazione. | [optional] |

### Return type

[**\Amica\Sdk\Model\FatturaElettronicaXml**](../Model/FatturaElettronicaXml.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `fatturaElettronicaXmlDownloadEsiti()`

```php
fatturaElettronicaXmlDownloadEsiti($company_id, $idempotency_key): \Amica\Sdk\Model\FatturaElettronicaImportResult[]
```

Interroga il Sistema di Interscambio (SDI) e scarica gli esiti di invio delle fatture precedentemente caricate.

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


$apiInstance = new Amica\Sdk\Api\FatturaElettronicaXmlApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.

try {
    $result = $apiInstance->fatturaElettronicaXmlDownloadEsiti($company_id, $idempotency_key);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->fatturaElettronicaXmlDownloadEsiti: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |

### Return type

[**\Amica\Sdk\Model\FatturaElettronicaImportResult[]**](../Model/FatturaElettronicaImportResult.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `fatturaElettronicaXmlDownloadFatture()`

```php
fatturaElettronicaXmlDownloadFatture($company_id, $idempotency_key): \Amica\Sdk\Model\FatturaElettronicaImportResult[]
```

Scarica le nuove fatture dal Sistema di Interscambio (SDI).

Il <a href=\"https://gestionaleamica.com/Software/ServizioInterscambioFatture\">Servizio di Interscambio Documenti</a> deve essere attivo per l'azienda e la relativa chiave di attivazione deve essere presente in `SdiApiKey`.

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


$apiInstance = new Amica\Sdk\Api\FatturaElettronicaXmlApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.

try {
    $result = $apiInstance->fatturaElettronicaXmlDownloadFatture($company_id, $idempotency_key);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->fatturaElettronicaXmlDownloadFatture: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |

### Return type

[**\Amica\Sdk\Model\FatturaElettronicaImportResult[]**](../Model/FatturaElettronicaImportResult.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `fatturaElettronicaXmlExport()`

```php
fatturaElettronicaXmlExport($company_id, $validation_override, $idempotency_key, $fattura_elettronica_export_options): \Amica\Sdk\Model\FatturaElettronicaExportResult
```

Esporta uno o più documenti in una fattura XML.

Tutti i documenti devono riferirsi alla stessa <a href=\"#tag/Anagrafica\">Anagrafica</a>.

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


$apiInstance = new Amica\Sdk\Api\FatturaElettronicaXmlApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$validation_override = True; // bool | Se applicare eventualmente l'override della validazione.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$fattura_elettronica_export_options = new \Amica\Sdk\Model\FatturaElettronicaExportOptions(); // \Amica\Sdk\Model\FatturaElettronicaExportOptions | Opzioni di esportazione.

try {
    $result = $apiInstance->fatturaElettronicaXmlExport($company_id, $validation_override, $idempotency_key, $fattura_elettronica_export_options);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->fatturaElettronicaXmlExport: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **validation_override** | **bool**| Se applicare eventualmente l&#39;override della validazione. | [optional] |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **fattura_elettronica_export_options** | [**\Amica\Sdk\Model\FatturaElettronicaExportOptions**](../Model/FatturaElettronicaExportOptions.md)| Opzioni di esportazione. | [optional] |

### Return type

[**\Amica\Sdk\Model\FatturaElettronicaExportResult**](../Model/FatturaElettronicaExportResult.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `fatturaElettronicaXmlGet()`

```php
fatturaElettronicaXmlGet($company_id, $id, $expand): \Amica\Sdk\Model\FatturaElettronicaXml
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


$apiInstance = new Amica\Sdk\Api\FatturaElettronicaXmlApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$id = 56; // int | ID dell'elemento da recuperare.
$expand = Rows; // string[] | Elenco separato da virgole di oggetti da espandere. Permette di recuperare gli oggetti correlati in un'unica chiamata. Ad esempio, per includere i beni e servizi associati a ciascun documento si aggiunge `expand=Rows` alla chiamata.

try {
    $result = $apiInstance->fatturaElettronicaXmlGet($company_id, $id, $expand);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->fatturaElettronicaXmlGet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **id** | **int**| ID dell&#39;elemento da recuperare. | |
| **expand** | [**string[]**](../Model/string.md)| Elenco separato da virgole di oggetti da espandere. Permette di recuperare gli oggetti correlati in un&#39;unica chiamata. Ad esempio, per includere i beni e servizi associati a ciascun documento si aggiunge &#x60;expand&#x3D;Rows&#x60; alla chiamata. | [optional] |

### Return type

[**\Amica\Sdk\Model\FatturaElettronicaXml**](../Model/FatturaElettronicaXml.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `fatturaElettronicaXmlGetAllAsArchive()`

```php
fatturaElettronicaXmlGetAllAsArchive($company_id)
```

Scarica tutte le fatture XML come archivio ZIP.

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


$apiInstance = new Amica\Sdk\Api\FatturaElettronicaXmlApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.

try {
    $apiInstance->fatturaElettronicaXmlGetAllAsArchive($company_id);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->fatturaElettronicaXmlGetAllAsArchive: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |

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

## `fatturaElettronicaXmlGetAsXml()`

```php
fatturaElettronicaXmlGetAsXml($company_id, $file_name)
```

Recupera una fattura XML per nome file.

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


$apiInstance = new Amica\Sdk\Api\FatturaElettronicaXmlApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$file_name = 'file_name_example'; // string | Nome del file.

try {
    $apiInstance->fatturaElettronicaXmlGetAsXml($company_id, $file_name);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->fatturaElettronicaXmlGetAsXml: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **file_name** | **string**| Nome del file. | |

### Return type

void (empty response body)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/xml`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `fatturaElettronicaXmlGetAttachments()`

```php
fatturaElettronicaXmlGetAttachments($company_id, $file_name): \Amica\Sdk\Model\AllegatoDocumento[]
```

Scarica i file allegati di una fattura XML.

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


$apiInstance = new Amica\Sdk\Api\FatturaElettronicaXmlApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$file_name = 'file_name_example'; // string | Nome del file.

try {
    $result = $apiInstance->fatturaElettronicaXmlGetAttachments($company_id, $file_name);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->fatturaElettronicaXmlGetAttachments: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **file_name** | **string**| Nome del file. | |

### Return type

[**\Amica\Sdk\Model\AllegatoDocumento[]**](../Model/AllegatoDocumento.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `fatturaElettronicaXmlImport()`

```php
fatturaElettronicaXmlImport($company_id, $idempotency_key, $data_consegna_sdi, $identificativo_sdi, $file): \Amica\Sdk\Model\FatturaElettronicaImportResult[]
```

Importa una fattura XML.

Viene creato un nuovo <a href=\"#tag/Documento\">Documento</a> corrispondente al contenuto XML.

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


$apiInstance = new Amica\Sdk\Api\FatturaElettronicaXmlApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$data_consegna_sdi = new \DateTime('2013-10-20T19:20:30+01:00'); // \DateTime | Data di consegna del Sistema di Interscambio (SdI).
$identificativo_sdi = 'identificativo_sdi_example'; // string | Identificativo assegnato dal Sistema di Interscambio (SdI).
$file = '/path/to/file.txt'; // \SplFileObject | Contenuto del file.

try {
    $result = $apiInstance->fatturaElettronicaXmlImport($company_id, $idempotency_key, $data_consegna_sdi, $identificativo_sdi, $file);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->fatturaElettronicaXmlImport: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **data_consegna_sdi** | **\DateTime**| Data di consegna del Sistema di Interscambio (SdI). | [optional] |
| **identificativo_sdi** | **string**| Identificativo assegnato dal Sistema di Interscambio (SdI). | [optional] |
| **file** | **\SplFileObject****\SplFileObject**| Contenuto del file. | [optional] |

### Return type

[**\Amica\Sdk\Model\FatturaElettronicaImportResult[]**](../Model/FatturaElettronicaImportResult.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `multipart/form-data`
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `fatturaElettronicaXmlList()`

```php
fatturaElettronicaXmlList($company_id, $skip, $take, $skip_total_count, $filter, $order, $expand): \Amica\Sdk\Model\FatturaElettronicaXml[]
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


$apiInstance = new Amica\Sdk\Api\FatturaElettronicaXmlApi(
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
    $result = $apiInstance->fatturaElettronicaXmlList($company_id, $skip, $take, $skip_total_count, $filter, $order, $expand);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->fatturaElettronicaXmlList: ', $e->getMessage(), PHP_EOL;
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

[**\Amica\Sdk\Model\FatturaElettronicaXml[]**](../Model/FatturaElettronicaXml.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `fatturaElettronicaXmlNewInstance()`

```php
fatturaElettronicaXmlNewInstance($company_id): \Amica\Sdk\Model\FatturaElettronicaXml
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


$apiInstance = new Amica\Sdk\Api\FatturaElettronicaXmlApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.

try {
    $result = $apiInstance->fatturaElettronicaXmlNewInstance($company_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->fatturaElettronicaXmlNewInstance: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |

### Return type

[**\Amica\Sdk\Model\FatturaElettronicaXml**](../Model/FatturaElettronicaXml.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `fatturaElettronicaXmlPair()`

```php
fatturaElettronicaXmlPair($company_id, $idempotency_key, $file_name, $progressivo, $documento_id, $file)
```

Associa una fattura XML a un documento esistente.

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


$apiInstance = new Amica\Sdk\Api\FatturaElettronicaXmlApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$file_name = 'file_name_example'; // string | Nome del file.
$progressivo = 'progressivo_example'; // string | Progressivo del Sistema di Interscambio (SdI).
$documento_id = 56; // int | Identificativo del `Documento` da abbinare.
$file = '/path/to/file.txt'; // \SplFileObject | Contenuto del file.

try {
    $apiInstance->fatturaElettronicaXmlPair($company_id, $idempotency_key, $file_name, $progressivo, $documento_id, $file);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->fatturaElettronicaXmlPair: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **file_name** | **string**| Nome del file. | [optional] |
| **progressivo** | **string**| Progressivo del Sistema di Interscambio (SdI). | [optional] |
| **documento_id** | **int**| Identificativo del &#x60;Documento&#x60; da abbinare. | [optional] |
| **file** | **\SplFileObject****\SplFileObject**| Contenuto del file. | [optional] |

### Return type

void (empty response body)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `multipart/form-data`
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `fatturaElettronicaXmlPost()`

```php
fatturaElettronicaXmlPost($company_id, $validation_override, $idempotency_key, $fattura_elettronica_xml): \Amica\Sdk\Model\FatturaElettronicaXml
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


$apiInstance = new Amica\Sdk\Api\FatturaElettronicaXmlApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$validation_override = True; // bool | Se applicare eventualmente l'override della validazione.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$fattura_elettronica_xml = new \Amica\Sdk\Model\FatturaElettronicaXml(); // \Amica\Sdk\Model\FatturaElettronicaXml | Istanza dell'elemento da salvare.

try {
    $result = $apiInstance->fatturaElettronicaXmlPost($company_id, $validation_override, $idempotency_key, $fattura_elettronica_xml);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->fatturaElettronicaXmlPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **validation_override** | **bool**| Se applicare eventualmente l&#39;override della validazione. | [optional] |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **fattura_elettronica_xml** | [**\Amica\Sdk\Model\FatturaElettronicaXml**](../Model/FatturaElettronicaXml.md)| Istanza dell&#39;elemento da salvare. | [optional] |

### Return type

[**\Amica\Sdk\Model\FatturaElettronicaXml**](../Model/FatturaElettronicaXml.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `fatturaElettronicaXmlPut()`

```php
fatturaElettronicaXmlPut($company_id, $id, $validation_override, $idempotency_key, $fattura_elettronica_xml): \Amica\Sdk\Model\FatturaElettronicaXml
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


$apiInstance = new Amica\Sdk\Api\FatturaElettronicaXmlApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$id = 56; // int | ID univoco dell'elemento da aggiornare.
$validation_override = True; // bool | Se applicare eventualmente l'override della validazione.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$fattura_elettronica_xml = new \Amica\Sdk\Model\FatturaElettronicaXml(); // \Amica\Sdk\Model\FatturaElettronicaXml | Istanza dell'elemento da salvare.

try {
    $result = $apiInstance->fatturaElettronicaXmlPut($company_id, $id, $validation_override, $idempotency_key, $fattura_elettronica_xml);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->fatturaElettronicaXmlPut: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **id** | **int**| ID univoco dell&#39;elemento da aggiornare. | |
| **validation_override** | **bool**| Se applicare eventualmente l&#39;override della validazione. | [optional] |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **fattura_elettronica_xml** | [**\Amica\Sdk\Model\FatturaElettronicaXml**](../Model/FatturaElettronicaXml.md)| Istanza dell&#39;elemento da salvare. | [optional] |

### Return type

[**\Amica\Sdk\Model\FatturaElettronicaXml**](../Model/FatturaElettronicaXml.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `fatturaElettronicaXmlUpload()`

```php
fatturaElettronicaXmlUpload($company_id, $idempotency_key, $fattura_elettronica_upload_options): \Amica\Sdk\Model\FatturaElettronicaUploadResult
```

Invia una o più fatture al Sistema di Interscambio (SDI).

Il <a href=\"https://gestionaleamica.com/Software/ServizioInterscambioFatture\">Servizio di Interscambio Documenti</a> deve essere attivo per l'azienda e la relativa chiave di attivazione deve essere presente in `SdiApiKey`.

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


$apiInstance = new Amica\Sdk\Api\FatturaElettronicaXmlApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$fattura_elettronica_upload_options = new \Amica\Sdk\Model\FatturaElettronicaUploadOptions(); // \Amica\Sdk\Model\FatturaElettronicaUploadOptions | Opzioni di invio.

try {
    $result = $apiInstance->fatturaElettronicaXmlUpload($company_id, $idempotency_key, $fattura_elettronica_upload_options);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->fatturaElettronicaXmlUpload: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **fattura_elettronica_upload_options** | [**\Amica\Sdk\Model\FatturaElettronicaUploadOptions**](../Model/FatturaElettronicaUploadOptions.md)| Opzioni di invio. | [optional] |

### Return type

[**\Amica\Sdk\Model\FatturaElettronicaUploadResult**](../Model/FatturaElettronicaUploadResult.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/json`, `application/problem+json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
