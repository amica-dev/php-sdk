# # SedeAltra

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Identificativo univoco. | [optional]
**concurrency_token** | **int** | Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull&#39;entità. Se i valori coincidono, l&#39;operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l&#39;aggiornamento viene rifiutato. | [optional]
**indirizzo** | **string** | Indirizzo. | [optional]
**localit** | **string** | Città, comune o altro nome di località. | [optional]
**cap** | **string** | CAP o codice di zona. | [optional]
**provincia** | **string** | Provincia o nome dell&#39;area. | [optional]
**telefono1** | **string** | Numero di telefono principale. | [optional]
**telefono2** | **string** | Numero di telefono secondario. | [optional]
**fax** | **string** | Numero di fax. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
