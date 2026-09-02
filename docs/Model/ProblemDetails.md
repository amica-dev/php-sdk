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

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
