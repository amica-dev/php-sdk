# # ProblemDetails

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**type** | **string** |  | [optional]
**title** | **string** |  | [optional]
**status** | **int** |  | [optional]
**detail** | **string** |  | [optional]
**instance** | **string** |  | [optional]
**overridable** | **bool** | Indica se l&#39;errore è *forzabile*: ripetendo la stessa richiesta con &#x60;validationOverride&#x3D;true&#x60; il servizio la accetta nonostante la validazione fallita (tipico delle anomalie formali su dati reali, come la partita IVA di un fornitore estero). È presente su tutte le risposte di errore e vale &#x60;false&#x60; per gli errori che non ammettono deroga. | [optional]
**code** | **string** | Codice che distingue errori con lo stesso status, quando l&#39;API ne fornisce uno. Compare sui &#x60;403&#x60;: &#x60;cloud_not_active&#x60; (account senza servizio cloud) e &#x60;company_license_not_active&#x60; (licenza dell&#39;azienda indicata nel percorso non attiva). Assente sugli altri errori. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
