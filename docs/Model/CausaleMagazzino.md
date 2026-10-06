# # CausaleMagazzino

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Identificativo univoco. | [optional]
**concurrency_token** | **int** | Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull&#39;entità. Se i valori coincidono, l&#39;operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l&#39;aggiornamento viene rifiutato. | [optional]
**nome** | **string** | Nome o descrizione. |
**codice** | **string** | Codice o identificativo univoco. |
**movimento_di_carico** | **bool** | Indica se l&#39;utilizzo causa la creazione di un movimento di carico. | [optional]
**tipo_causale_magazzino** | **string** | Tipologia del movimento di magazzino. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
