# # Banca

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Identificativo univoco. | [optional]
**concurrency_token** | **int** | Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull&#39;entità. Se i valori coincidono, l&#39;operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l&#39;aggiornamento viene rifiutato. | [optional]
**nome** | **string** | Nome. | [optional]
**iban** | **string** | Codice IBAN. | [optional]
**abi** | **string** | Codice ABI. | [optional]
**cab** | **string** | Codice CAB. | [optional]
**conto_corrente** | **string** | Numero di conto corrente. | [optional]
**bic** | **string** | Codice BIC. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
