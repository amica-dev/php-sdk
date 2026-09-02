# Amica\Sdk\FatturaElettronicaXmlApi

All URIs are relative to http://localhost, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**apiVversionCompanyIdFatturaElettronicaXmlArchivebydaterangePost()**](FatturaElettronicaXmlApi.md#apiVversionCompanyIdFatturaElettronicaXmlArchivebydaterangePost) | **POST** /api/v{version}/{companyId}/FatturaElettronicaXml/archivebydaterange | Scarica un archivio ZIP di fatture XML, filtrate per intervallo di date. |
| [**apiVversionCompanyIdFatturaElettronicaXmlArchivebyfilenamePost()**](FatturaElettronicaXmlApi.md#apiVversionCompanyIdFatturaElettronicaXmlArchivebyfilenamePost) | **POST** /api/v{version}/{companyId}/FatturaElettronicaXml/archivebyfilename | Scarica un archivio ZIP di fatture XML, filtrate per nome file. |
| [**apiVversionCompanyIdFatturaElettronicaXmlBulkDelete()**](FatturaElettronicaXmlApi.md#apiVversionCompanyIdFatturaElettronicaXmlBulkDelete) | **DELETE** /api/v{version}/{companyId}/FatturaElettronicaXml/bulk | Elimina in blocco tutte le fatture XML dell&#39;azienda. |
| [**apiVversionCompanyIdFatturaElettronicaXmlBulkGet()**](FatturaElettronicaXmlApi.md#apiVversionCompanyIdFatturaElettronicaXmlBulkGet) | **GET** /api/v{version}/{companyId}/FatturaElettronicaXml/bulk | Scarica tutte le fatture XML come archivio ZIP. |
| [**apiVversionCompanyIdFatturaElettronicaXmlBulkPost()**](FatturaElettronicaXmlApi.md#apiVversionCompanyIdFatturaElettronicaXmlBulkPost) | **POST** /api/v{version}/{companyId}/FatturaElettronicaXml/bulk | Carica in blocco fatture XML da un archivio ZIP. |
| [**apiVversionCompanyIdFatturaElettronicaXmlExportPost()**](FatturaElettronicaXmlApi.md#apiVversionCompanyIdFatturaElettronicaXmlExportPost) | **POST** /api/v{version}/{companyId}/FatturaElettronicaXml/export | Esporta uno o più documenti in una fattura XML. |
| [**apiVversionCompanyIdFatturaElettronicaXmlFileNameAttachmentsGet()**](FatturaElettronicaXmlApi.md#apiVversionCompanyIdFatturaElettronicaXmlFileNameAttachmentsGet) | **GET** /api/v{version}/{companyId}/FatturaElettronicaXml/{fileName}/attachments | Scarica i file allegati di una fattura XML. |
| [**apiVversionCompanyIdFatturaElettronicaXmlFileNameGet()**](FatturaElettronicaXmlApi.md#apiVversionCompanyIdFatturaElettronicaXmlFileNameGet) | **GET** /api/v{version}/{companyId}/FatturaElettronicaXml/{fileName} | Recupera una fattura XML per nome file. |
| [**apiVversionCompanyIdFatturaElettronicaXmlGet()**](FatturaElettronicaXmlApi.md#apiVversionCompanyIdFatturaElettronicaXmlGet) | **GET** /api/v{version}/{companyId}/FatturaElettronicaXml | Restituisce una lista di elementi. |
| [**apiVversionCompanyIdFatturaElettronicaXmlIdDelete()**](FatturaElettronicaXmlApi.md#apiVversionCompanyIdFatturaElettronicaXmlIdDelete) | **DELETE** /api/v{version}/{companyId}/FatturaElettronicaXml/{id} | Elimina un elemento. |
| [**apiVversionCompanyIdFatturaElettronicaXmlIdGet()**](FatturaElettronicaXmlApi.md#apiVversionCompanyIdFatturaElettronicaXmlIdGet) | **GET** /api/v{version}/{companyId}/FatturaElettronicaXml/{id} | Restituisce un elemento. |
| [**apiVversionCompanyIdFatturaElettronicaXmlIdPut()**](FatturaElettronicaXmlApi.md#apiVversionCompanyIdFatturaElettronicaXmlIdPut) | **PUT** /api/v{version}/{companyId}/FatturaElettronicaXml/{id} | Aggiorna un elemento esistente. |
| [**apiVversionCompanyIdFatturaElettronicaXmlImportPost()**](FatturaElettronicaXmlApi.md#apiVversionCompanyIdFatturaElettronicaXmlImportPost) | **POST** /api/v{version}/{companyId}/FatturaElettronicaXml/import | Importa una fattura XML. |
| [**apiVversionCompanyIdFatturaElettronicaXmlNewGet()**](FatturaElettronicaXmlApi.md#apiVversionCompanyIdFatturaElettronicaXmlNewGet) | **GET** /api/v{version}/{companyId}/FatturaElettronicaXml/new | Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di &#x60;Documento&#x60; avrà il &#x60;Numero&#x60; appropriato, basato sui documenti preesistenti di tipo compatibile). |
| [**apiVversionCompanyIdFatturaElettronicaXmlPairPost()**](FatturaElettronicaXmlApi.md#apiVversionCompanyIdFatturaElettronicaXmlPairPost) | **POST** /api/v{version}/{companyId}/FatturaElettronicaXml/pair | Associa una fattura XML a un documento esistente. |
| [**apiVversionCompanyIdFatturaElettronicaXmlPost()**](FatturaElettronicaXmlApi.md#apiVversionCompanyIdFatturaElettronicaXmlPost) | **POST** /api/v{version}/{companyId}/FatturaElettronicaXml | Salva un nuovo elemento. |
| [**apiVversionCompanyIdFatturaElettronicaXmlSdiDownloadEsitiPost()**](FatturaElettronicaXmlApi.md#apiVversionCompanyIdFatturaElettronicaXmlSdiDownloadEsitiPost) | **POST** /api/v{version}/{companyId}/FatturaElettronicaXml/sdi/download/esiti | Interroga il Sistema di Interscambio (SDI) e scarica gli esiti di invio delle fatture precedentemente caricate. |
| [**apiVversionCompanyIdFatturaElettronicaXmlSdiDownloadFatturePost()**](FatturaElettronicaXmlApi.md#apiVversionCompanyIdFatturaElettronicaXmlSdiDownloadFatturePost) | **POST** /api/v{version}/{companyId}/FatturaElettronicaXml/sdi/download/fatture | Scarica le nuove fatture dal Sistema di Interscambio (SDI). |
| [**apiVversionCompanyIdFatturaElettronicaXmlSdiUploadPost()**](FatturaElettronicaXmlApi.md#apiVversionCompanyIdFatturaElettronicaXmlSdiUploadPost) | **POST** /api/v{version}/{companyId}/FatturaElettronicaXml/sdi/upload | Invia una o più fatture al Sistema di Interscambio (SDI). |
| [**apiVversionCompanyIdFatturaElettronicaXmlStylesheetPost()**](FatturaElettronicaXmlApi.md#apiVversionCompanyIdFatturaElettronicaXmlStylesheetPost) | **POST** /api/v{version}/{companyId}/FatturaElettronicaXml/stylesheet | Applica un foglio di stile CSS a una fattura XML esistente e restituisce il risultato. |


## `apiVversionCompanyIdFatturaElettronicaXmlArchivebydaterangePost()`

```php
apiVversionCompanyIdFatturaElettronicaXmlArchivebydaterangePost($version, $company_id, $idempotency_key, $fattura_elettronica_xml_get_archive_by_date_range_options)
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
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$fattura_elettronica_xml_get_archive_by_date_range_options = new \Amica\Sdk\Model\FatturaElettronicaXmlGetArchiveByDateRangeOptions(); // \Amica\Sdk\Model\FatturaElettronicaXmlGetArchiveByDateRangeOptions | Opzioni di download.

try {
    $apiInstance->apiVversionCompanyIdFatturaElettronicaXmlArchivebydaterangePost($version, $company_id, $idempotency_key, $fattura_elettronica_xml_get_archive_by_date_range_options);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->apiVversionCompanyIdFatturaElettronicaXmlArchivebydaterangePost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **fattura_elettronica_xml_get_archive_by_date_range_options** | [**\Amica\Sdk\Model\FatturaElettronicaXmlGetArchiveByDateRangeOptions**](../Model/FatturaElettronicaXmlGetArchiveByDateRangeOptions.md)| Opzioni di download. | [optional] |

### Return type

void (empty response body)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/zip`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdFatturaElettronicaXmlArchivebyfilenamePost()`

```php
apiVversionCompanyIdFatturaElettronicaXmlArchivebyfilenamePost($version, $company_id, $idempotency_key, $fattura_elettronica_xml_get_archive_by_file_name_options)
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
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$fattura_elettronica_xml_get_archive_by_file_name_options = new \Amica\Sdk\Model\FatturaElettronicaXmlGetArchiveByFileNameOptions(); // \Amica\Sdk\Model\FatturaElettronicaXmlGetArchiveByFileNameOptions | Opzioni di download.

try {
    $apiInstance->apiVversionCompanyIdFatturaElettronicaXmlArchivebyfilenamePost($version, $company_id, $idempotency_key, $fattura_elettronica_xml_get_archive_by_file_name_options);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->apiVversionCompanyIdFatturaElettronicaXmlArchivebyfilenamePost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **fattura_elettronica_xml_get_archive_by_file_name_options** | [**\Amica\Sdk\Model\FatturaElettronicaXmlGetArchiveByFileNameOptions**](../Model/FatturaElettronicaXmlGetArchiveByFileNameOptions.md)| Opzioni di download. | [optional] |

### Return type

void (empty response body)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/zip`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdFatturaElettronicaXmlBulkDelete()`

```php
apiVversionCompanyIdFatturaElettronicaXmlBulkDelete($company_id, $version)
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
$version = 'version_example'; // string

try {
    $apiInstance->apiVversionCompanyIdFatturaElettronicaXmlBulkDelete($company_id, $version);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->apiVversionCompanyIdFatturaElettronicaXmlBulkDelete: ', $e->getMessage(), PHP_EOL;
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
- **Accept**: `text/plain`, `application/json`, `text/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdFatturaElettronicaXmlBulkGet()`

```php
apiVversionCompanyIdFatturaElettronicaXmlBulkGet($company_id, $version)
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
$version = 'version_example'; // string

try {
    $apiInstance->apiVversionCompanyIdFatturaElettronicaXmlBulkGet($company_id, $version);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->apiVversionCompanyIdFatturaElettronicaXmlBulkGet: ', $e->getMessage(), PHP_EOL;
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
- **Accept**: `application/zip`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdFatturaElettronicaXmlBulkPost()`

```php
apiVversionCompanyIdFatturaElettronicaXmlBulkPost($company_id, $version, $idempotency_key, $file)
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
$version = 'version_example'; // string
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$file = '/path/to/file.txt'; // \SplFileObject | Archivio ZIP contenente i file XML.

try {
    $apiInstance->apiVversionCompanyIdFatturaElettronicaXmlBulkPost($company_id, $version, $idempotency_key, $file);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->apiVversionCompanyIdFatturaElettronicaXmlBulkPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **version** | **string**|  | |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **file** | **\SplFileObject****\SplFileObject**| Archivio ZIP contenente i file XML. | [optional] |

### Return type

void (empty response body)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `multipart/form-data`
- **Accept**: `text/plain`, `application/json`, `text/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdFatturaElettronicaXmlExportPost()`

```php
apiVversionCompanyIdFatturaElettronicaXmlExportPost($version, $company_id, $validation_override, $idempotency_key, $fattura_elettronica_export_options): \Amica\Sdk\Model\FatturaElettronicaExportResult
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
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$validation_override = True; // bool | Se applicare eventualmente l'override della validazione.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$fattura_elettronica_export_options = new \Amica\Sdk\Model\FatturaElettronicaExportOptions(); // \Amica\Sdk\Model\FatturaElettronicaExportOptions | Opzioni di esportazione.

try {
    $result = $apiInstance->apiVversionCompanyIdFatturaElettronicaXmlExportPost($version, $company_id, $validation_override, $idempotency_key, $fattura_elettronica_export_options);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->apiVversionCompanyIdFatturaElettronicaXmlExportPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
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
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdFatturaElettronicaXmlFileNameAttachmentsGet()`

```php
apiVversionCompanyIdFatturaElettronicaXmlFileNameAttachmentsGet($version, $company_id, $file_name): \Amica\Sdk\Model\AllegatoDocumento[]
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
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$file_name = 'file_name_example'; // string | Nome del file.

try {
    $result = $apiInstance->apiVversionCompanyIdFatturaElettronicaXmlFileNameAttachmentsGet($version, $company_id, $file_name);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->apiVversionCompanyIdFatturaElettronicaXmlFileNameAttachmentsGet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **file_name** | **string**| Nome del file. | |

### Return type

[**\Amica\Sdk\Model\AllegatoDocumento[]**](../Model/AllegatoDocumento.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdFatturaElettronicaXmlFileNameGet()`

```php
apiVversionCompanyIdFatturaElettronicaXmlFileNameGet($version, $company_id, $file_name)
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
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$file_name = 'file_name_example'; // string | Nome del file.

try {
    $apiInstance->apiVversionCompanyIdFatturaElettronicaXmlFileNameGet($version, $company_id, $file_name);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->apiVversionCompanyIdFatturaElettronicaXmlFileNameGet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **file_name** | **string**| Nome del file. | |

### Return type

void (empty response body)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/xml`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdFatturaElettronicaXmlGet()`

```php
apiVversionCompanyIdFatturaElettronicaXmlGet($version, $company_id, $skip, $take, $skip_total_count, $filter, $order, $expand): \Amica\Sdk\Model\FatturaElettronicaXml[]
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
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$skip = 100; // int | Numero di elementi da escludere dall'inizio del risultato. Combinato con `Take`, è utile per la paginazione.
$take = 25; // int | Numero di elementi da includere nel risultato. Combinato con `Skip`, è utile per la paginazione.
$skip_total_count = True; // bool | Indica se l'header `X-Read-TotalCount` debba essere escluso dalla risposta. Impostare a `true` per prestazioni migliori a scapito dell'accuratezza. Il valore predefinito è `false`.
$filter = field1 $eq hello $or, field2 $gt 10; // string | Condizioni di ricerca. Elenco di espressioni separate da virgola. Ogni espressione è composta da nome del campo, operatore di confronto, valore ed eventualmente un connettore logico. Operatori validi: `$eq` (uguale), `$ne` (diverso), `$gt` (maggiore di), `$lt` (minore di), `$lte` (minore o uguale), `$gte` (maggiore o uguale), `$ct` (contiene), `$nct` (non contiene), `$sw` (inizia con), `$ew` (finisce con). Più espressioni possono essere concatenate con un connettore logico `$and` o `$or` (`$and` può essere omesso). Le query annidate si esprimono racchiudendo le sotto-query tra parentesi quadre (`[]`). Nell'esempio vengono inclusi tutti gli elementi con `field1=\"hello\"` oppure `field2>10`.
$order = -field1,field2; // string | Opzioni di ordinamento. Un segno meno (trattino) prima del nome indica ordinamento discendente. Nell'esempio i risultati sono ordinati prima per `field1` (discendente), poi per `field2`.
$expand = Rows; // string[] | Elenco separato da virgole di oggetti da espandere. Permette di recuperare gli oggetti correlati in un'unica chiamata. Ad esempio, per includere i beni e servizi associati a ciascun documento si aggiunge `expand=Rows` alla chiamata.

try {
    $result = $apiInstance->apiVversionCompanyIdFatturaElettronicaXmlGet($version, $company_id, $skip, $take, $skip_total_count, $filter, $order, $expand);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->apiVversionCompanyIdFatturaElettronicaXmlGet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
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
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdFatturaElettronicaXmlIdDelete()`

```php
apiVversionCompanyIdFatturaElettronicaXmlIdDelete($version, $company_id, $id): \Amica\Sdk\Model\FatturaElettronicaXml
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
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$id = 56; // int | ID dell'elemento da eliminare.

try {
    $result = $apiInstance->apiVversionCompanyIdFatturaElettronicaXmlIdDelete($version, $company_id, $id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->apiVversionCompanyIdFatturaElettronicaXmlIdDelete: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **id** | **int**| ID dell&#39;elemento da eliminare. | |

### Return type

[**\Amica\Sdk\Model\FatturaElettronicaXml**](../Model/FatturaElettronicaXml.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdFatturaElettronicaXmlIdGet()`

```php
apiVversionCompanyIdFatturaElettronicaXmlIdGet($version, $company_id, $id, $expand): \Amica\Sdk\Model\FatturaElettronicaXml
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
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$id = 56; // int | ID dell'elemento da recuperare.
$expand = Rows; // string[] | Elenco separato da virgole di oggetti da espandere. Permette di recuperare gli oggetti correlati in un'unica chiamata. Ad esempio, per includere i beni e servizi associati a ciascun documento si aggiunge `expand=Rows` alla chiamata.

try {
    $result = $apiInstance->apiVversionCompanyIdFatturaElettronicaXmlIdGet($version, $company_id, $id, $expand);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->apiVversionCompanyIdFatturaElettronicaXmlIdGet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **id** | **int**| ID dell&#39;elemento da recuperare. | |
| **expand** | [**string[]**](../Model/string.md)| Elenco separato da virgole di oggetti da espandere. Permette di recuperare gli oggetti correlati in un&#39;unica chiamata. Ad esempio, per includere i beni e servizi associati a ciascun documento si aggiunge &#x60;expand&#x3D;Rows&#x60; alla chiamata. | [optional] |

### Return type

[**\Amica\Sdk\Model\FatturaElettronicaXml**](../Model/FatturaElettronicaXml.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdFatturaElettronicaXmlIdPut()`

```php
apiVversionCompanyIdFatturaElettronicaXmlIdPut($version, $company_id, $id, $validation_override, $idempotency_key, $fattura_elettronica_xml): \Amica\Sdk\Model\FatturaElettronicaXml
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
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$id = 56; // int | ID univoco dell'elemento da aggiornare.
$validation_override = True; // bool | Se applicare eventualmente l'override della validazione.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$fattura_elettronica_xml = new \Amica\Sdk\Model\FatturaElettronicaXml(); // \Amica\Sdk\Model\FatturaElettronicaXml | Istanza dell'elemento da salvare.

try {
    $result = $apiInstance->apiVversionCompanyIdFatturaElettronicaXmlIdPut($version, $company_id, $id, $validation_override, $idempotency_key, $fattura_elettronica_xml);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->apiVversionCompanyIdFatturaElettronicaXmlIdPut: ', $e->getMessage(), PHP_EOL;
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
| **fattura_elettronica_xml** | [**\Amica\Sdk\Model\FatturaElettronicaXml**](../Model/FatturaElettronicaXml.md)| Istanza dell&#39;elemento da salvare. | [optional] |

### Return type

[**\Amica\Sdk\Model\FatturaElettronicaXml**](../Model/FatturaElettronicaXml.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdFatturaElettronicaXmlImportPost()`

```php
apiVversionCompanyIdFatturaElettronicaXmlImportPost($version, $company_id, $idempotency_key, $data_consegna_sdi, $identificativo_sdi, $file): \Amica\Sdk\Model\FatturaElettronicaImportResult[]
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
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$data_consegna_sdi = new \DateTime('2013-10-20T19:20:30+01:00'); // \DateTime | Data di consegna del Sistema di Interscambio (SdI).
$identificativo_sdi = 'identificativo_sdi_example'; // string | Identificativo assegnato dal Sistema di Interscambio (SdI).
$file = '/path/to/file.txt'; // \SplFileObject | Contenuto del file.

try {
    $result = $apiInstance->apiVversionCompanyIdFatturaElettronicaXmlImportPost($version, $company_id, $idempotency_key, $data_consegna_sdi, $identificativo_sdi, $file);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->apiVversionCompanyIdFatturaElettronicaXmlImportPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
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
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdFatturaElettronicaXmlNewGet()`

```php
apiVversionCompanyIdFatturaElettronicaXmlNewGet($version, $company_id): \Amica\Sdk\Model\FatturaElettronicaXml
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
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.

try {
    $result = $apiInstance->apiVversionCompanyIdFatturaElettronicaXmlNewGet($version, $company_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->apiVversionCompanyIdFatturaElettronicaXmlNewGet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |

### Return type

[**\Amica\Sdk\Model\FatturaElettronicaXml**](../Model/FatturaElettronicaXml.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdFatturaElettronicaXmlPairPost()`

```php
apiVversionCompanyIdFatturaElettronicaXmlPairPost($version, $company_id, $idempotency_key, $file_name, $progressivo, $documento_id, $file)
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
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$file_name = 'file_name_example'; // string | Nome del file.
$progressivo = 'progressivo_example'; // string | Progressivo del Sistema di Interscambio (SdI).
$documento_id = 56; // int | Identificativo del `Documento` da abbinare.
$file = '/path/to/file.txt'; // \SplFileObject | Contenuto del file.

try {
    $apiInstance->apiVversionCompanyIdFatturaElettronicaXmlPairPost($version, $company_id, $idempotency_key, $file_name, $progressivo, $documento_id, $file);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->apiVversionCompanyIdFatturaElettronicaXmlPairPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
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
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdFatturaElettronicaXmlPost()`

```php
apiVversionCompanyIdFatturaElettronicaXmlPost($version, $company_id, $validation_override, $idempotency_key, $fattura_elettronica_xml): \Amica\Sdk\Model\FatturaElettronicaXml
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
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$validation_override = True; // bool | Se applicare eventualmente l'override della validazione.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$fattura_elettronica_xml = new \Amica\Sdk\Model\FatturaElettronicaXml(); // \Amica\Sdk\Model\FatturaElettronicaXml | Istanza dell'elemento da salvare.

try {
    $result = $apiInstance->apiVversionCompanyIdFatturaElettronicaXmlPost($version, $company_id, $validation_override, $idempotency_key, $fattura_elettronica_xml);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->apiVversionCompanyIdFatturaElettronicaXmlPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
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
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdFatturaElettronicaXmlSdiDownloadEsitiPost()`

```php
apiVversionCompanyIdFatturaElettronicaXmlSdiDownloadEsitiPost($version, $company_id, $idempotency_key): \Amica\Sdk\Model\FatturaElettronicaImportResult[]
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
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.

try {
    $result = $apiInstance->apiVversionCompanyIdFatturaElettronicaXmlSdiDownloadEsitiPost($version, $company_id, $idempotency_key);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->apiVversionCompanyIdFatturaElettronicaXmlSdiDownloadEsitiPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |

### Return type

[**\Amica\Sdk\Model\FatturaElettronicaImportResult[]**](../Model/FatturaElettronicaImportResult.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdFatturaElettronicaXmlSdiDownloadFatturePost()`

```php
apiVversionCompanyIdFatturaElettronicaXmlSdiDownloadFatturePost($version, $company_id, $idempotency_key): \Amica\Sdk\Model\FatturaElettronicaImportResult[]
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
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.

try {
    $result = $apiInstance->apiVversionCompanyIdFatturaElettronicaXmlSdiDownloadFatturePost($version, $company_id, $idempotency_key);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->apiVversionCompanyIdFatturaElettronicaXmlSdiDownloadFatturePost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |

### Return type

[**\Amica\Sdk\Model\FatturaElettronicaImportResult[]**](../Model/FatturaElettronicaImportResult.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdFatturaElettronicaXmlSdiUploadPost()`

```php
apiVversionCompanyIdFatturaElettronicaXmlSdiUploadPost($version, $company_id, $idempotency_key, $fattura_elettronica_upload_options): \Amica\Sdk\Model\FatturaElettronicaUploadResult
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
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$fattura_elettronica_upload_options = new \Amica\Sdk\Model\FatturaElettronicaUploadOptions(); // \Amica\Sdk\Model\FatturaElettronicaUploadOptions | Opzioni di invio.

try {
    $result = $apiInstance->apiVversionCompanyIdFatturaElettronicaXmlSdiUploadPost($version, $company_id, $idempotency_key, $fattura_elettronica_upload_options);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->apiVversionCompanyIdFatturaElettronicaXmlSdiUploadPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **fattura_elettronica_upload_options** | [**\Amica\Sdk\Model\FatturaElettronicaUploadOptions**](../Model/FatturaElettronicaUploadOptions.md)| Opzioni di invio. | [optional] |

### Return type

[**\Amica\Sdk\Model\FatturaElettronicaUploadResult**](../Model/FatturaElettronicaUploadResult.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `apiVversionCompanyIdFatturaElettronicaXmlStylesheetPost()`

```php
apiVversionCompanyIdFatturaElettronicaXmlStylesheetPost($version, $company_id, $idempotency_key, $fattura_elettronica_xml_style_sheet_data)
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
$version = 'version_example'; // string | Versione API.
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$idempotency_key = 'idempotency_key_example'; // string | Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l'operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l'header `Idempotent-Replayed: true`.
$fattura_elettronica_xml_style_sheet_data = new \Amica\Sdk\Model\FatturaElettronicaXmlStyleSheetData(); // \Amica\Sdk\Model\FatturaElettronicaXmlStyleSheetData | Opzioni di corrispondenza e stile.

try {
    $apiInstance->apiVversionCompanyIdFatturaElettronicaXmlStylesheetPost($version, $company_id, $idempotency_key, $fattura_elettronica_xml_style_sheet_data);
} catch (Exception $e) {
    echo 'Exception when calling FatturaElettronicaXmlApi->apiVversionCompanyIdFatturaElettronicaXmlStylesheetPost: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **version** | **string**| Versione API. | |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **idempotency_key** | **string**| Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. | [optional] |
| **fattura_elettronica_xml_style_sheet_data** | [**\Amica\Sdk\Model\FatturaElettronicaXmlStyleSheetData**](../Model/FatturaElettronicaXmlStyleSheetData.md)| Opzioni di corrispondenza e stile. | [optional] |

### Return type

void (empty response body)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: `application/json`, `text/json`, `application/*+json`
- **Accept**: `application/xml`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
