# # RisorsaFinanziaria

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Identificativo univoco. | [optional]
**concurrency_token** | **int** | Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull&#39;entità. Se i valori coincidono, l&#39;operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l&#39;aggiornamento viene rifiutato. | [optional]
**nome** | **string** | Nome o descrizione. |
**indirizzo** | **string** | Indirizzo. | [optional]
**localit** | **string** | Città, comune o altra denominazione di località. | [optional]
**cap** | **string** | CAP o codice di zona. | [optional]
**provincia** | **string** | Provincia o denominazione dell&#39;area. | [optional]
**telefono1** | **string** | Numero di telefono principale. | [optional]
**telefono2** | **string** | Numero di telefono secondario. | [optional]
**fax** | **string** | Numero di fax. | [optional]
**contatto1_nome** | **string** | Nome del contatto. | [optional]
**contatto1_incarico** | **string** | Qualifica o ruolo del contatto. | [optional]
**contatto1_telefono1** | **string** | Telefono principale del contatto. | [optional]
**contatto1_telefono2** | **string** | Telefono secondario del contatto. | [optional]
**contatto1_e_mail** | **string** | Email del contatto. | [optional]
**contatto2_nome** | **string** | Nome del contatto secondario. | [optional]
**contatto2_incarico** | **string** | Qualifica o ruolo del contatto secondario. | [optional]
**contatto2_telefono1** | **string** | Numero di telefono del contatto secondario. | [optional]
**contatto2_telefono2** | **string** | Numero di telefono alternativo del contatto secondario. | [optional]
**contatto2_e_mail** | **string** | Email del contatto secondario. | [optional]
**codice_abi** | **string** | Codice ABI. | [optional]
**codice_cab** | **string** | Codice CAB. | [optional]
**conto_corrente** | **string** | Numero di conto corrente. | [optional]
**codice_cin** | **string** | Codice CIN. | [optional]
**codice_cin_eur** | **string** | CIN EUR. | [optional]
**codice_cin_paese** | **string** | Codice CIN della nazione. | [optional]
**codice_swift** | **string** | Codice SWIFT. | [optional]
**codice_iban** | **string** | Codice IBAN. | [optional]
**web** | **string** | Sito web. | [optional]
**tipologia** | **string** | Tipologia di risorsa finanziaria. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
