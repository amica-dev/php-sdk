# # Indirizzo

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Identificativo univoco. | [optional]
**concurrency_token** | **int** | Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull&#39;entità. Se i valori coincidono, l&#39;operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l&#39;aggiornamento viene rifiutato. | [optional]
**anagrafica_id** | **int** | Identificativo univoco di &#x60;Anagrafica&#x60;. | [optional]
**ragione_sociale1** | **string** | Ragione sociale, oppure nome e cognome della persona fisica. | [optional]
**ragione_sociale2** | **string** | Eventuale seconda riga della ragione sociale. | [optional]
**via** | **string** | Indirizzo (via). | [optional]
**localit** | **string** | Località. | [optional]
**cap** | **string** | CAP o codice di avviamento postale. | [optional]
**provincia** | **string** | Provincia, contea o nome dell&#39;area. | [optional]
**telefono1** | **string** | Numero di telefono principale. | [optional]
**telefono2** | **string** | Numero di telefono secondario. | [optional]
**fax** | **string** | Numero di fax. | [optional]
**email** | **string** | Indirizzo email. | [optional]
**stato** | **bool** | Indica se l&#39;indirizzo è attivo oppure no. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
