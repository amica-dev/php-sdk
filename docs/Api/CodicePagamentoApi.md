# Amica\Sdk\CodicePagamentoApi

All URIs are relative to http://localhost, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**apiVversionCompanyIdTabelleCodicePagamentoGet()**](CodicePagamentoApi.md#apiVversionCompanyIdTabelleCodicePagamentoGet) | **GET** /api/v{version}/{companyId}/tabelle/CodicePagamento | Restituisce il contenuto della tabella come lista di elementi. |


## `apiVversionCompanyIdTabelleCodicePagamentoGet()`

```php
apiVversionCompanyIdTabelleCodicePagamentoGet($company_id, $version): \Amica\Sdk\Model\CodicePagamento[]
```

Restituisce il contenuto della tabella come lista di elementi.

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


$apiInstance = new Amica\Sdk\Api\CodicePagamentoApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$company_id = 'company_id_example'; // string | ID univoco dell'azienda.
$version = 'version_example'; // string

try {
    $result = $apiInstance->apiVversionCompanyIdTabelleCodicePagamentoGet($company_id, $version);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling CodicePagamentoApi->apiVversionCompanyIdTabelleCodicePagamentoGet: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **company_id** | **string**| ID univoco dell&#39;azienda. | |
| **version** | **string**|  | |

### Return type

[**\Amica\Sdk\Model\CodicePagamento[]**](../Model/CodicePagamento.md)

### Authorization

[bearer](../../README.md#bearer), [oauth2ClientCredentials](../../README.md#oauth2ClientCredentials), [oauth2](../../README.md#oauth2)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `text/plain`, `application/json`, `text/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
