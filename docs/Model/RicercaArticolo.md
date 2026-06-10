# # RicercaArticolo

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Identificativo univoco. | [optional]
**concurrency_token** | **int** | Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull&#39;entità. Se i valori coincidono, l&#39;operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l&#39;aggiornamento viene rifiutato. | [optional]
**vendite_campo1** | **string** | Campo di ricerca preferito per gli articoli in vendita. | [optional]
**vendite_campo2** | **string** | Secondo campo di ricerca per gli articoli in vendita. | [optional]
**vendite_campo3** | **string** | Terzo campo di ricerca per gli articoli in vendita. | [optional]
**vendite_campo4** | **string** | Quarto campo di ricerca per gli articoli in vendita. | [optional]
**acquisti_campo1** | **string** | Campo di ricerca preferito per gli articoli in acquisto. | [optional]
**acquisti_campo2** | **string** | Secondo campo di ricerca per gli articoli in acquisto. | [optional]
**acquisti_campo3** | **string** | Terzo campo di ricerca per gli articoli in acquisto. | [optional]
**acquisti_campo4** | **string** | Quarto campo di ricerca per gli articoli in acquisto. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
