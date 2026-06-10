# # CausaleIva

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Identificativo univoco. | [optional]
**concurrency_token** | **int** | Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull&#39;entità. Se i valori coincidono, l&#39;operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l&#39;aggiornamento viene rifiutato. | [optional]
**codice** | **string** | Codice. |
**nome** | **string** | Nome, o descrizione. |
**aliquota** | **float** | Aliquota applicabile. | [optional]
**indeducibilit** | **float** | Quota di indeducibilità. | [optional]
**intracomunitaria** | **bool** | Indica se l&#39;IVA è intracomunitaria. | [optional]
**non_imponibile** | **bool** | Indica se l&#39;IVA non deve essere considerata nella determinazione dell&#39;imponibile. | [optional]
**natura** | **string** | Natura dell&#39;esenzione (codice), se applicabile (ad es. \&quot;N1\&quot;). | [optional]
**split_payment** | **bool** | Indica se si applica lo split payment o meno. | [optional]
**fattura_elettronica** | **bool** | Indica se l&#39;IVA è stata creata automaticamente durante l&#39;importazione di una fattura elettronica. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
