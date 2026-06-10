# # Evento

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Identificativo univoco. | [optional]
**concurrency_token** | **int** | Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull&#39;entità. Se i valori coincidono, l&#39;operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l&#39;aggiornamento viene rifiutato. | [optional]
**event_guid** | **string** | GUID dell&#39;evento. | [optional]
**type** | **string** | Tipo di evento. | [optional]
**company_id** | **string** | Identificativo dell&#39;azienda per cui l&#39;evento è stato scatenato. | [optional]
**date_time** | **\DateTime** | Data e ora in cui l&#39;evento si è verificato. | [optional]
**api_version** | **string** | Versione dell&#39;API. | [optional]
**application_id** | **string** | Application ID del client. Vedi &lt;a href&#x3D;\&quot;https://amica.dev/api/required-headers/\&quot;&gt;required headers&lt;/a&gt; per maggiori informazioni. | [optional]
**object** | **mixed** | Payload. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
