# # WebHook

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Identificativo univoco. | [optional]
**concurrency_token** | **int** | Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull&#39;entità. Se i valori coincidono, l&#39;operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l&#39;aggiornamento viene rifiutato. | [optional]
**azienda_id** | **int** | Azienda per cui il webhook deve essere attivo. Impostare a &#x60;null&#x60; se il webhook deve essere attivo per tutte le aziende. | [optional]
**url** | **string** | L&#39;URL del webhook. È l&#39;endpoint della tua integrazione che riceverà la richiesta POST quando gli Amica.WebHook.Events indicati vengono scatenati. Sono accettati solo URL https. | [optional]
**enabled** | **bool** | Indica se il webhook è abilitato o meno. Alla creazione viene impostato a &#x60;true&#x60;. | [optional]
**secret** | **string** | Il segreto usato per generare le firme del webhook. Restituito alla creazione del webhook e mai aggiornabile. | [optional]
**description** | **string** | Una descrizione opzionale dell&#39;utilizzo del webhook. | [optional]
**events** | **string[]** | Elenco degli eventi da abilitare per questo endpoint. Vedi Amica.Eventi.Available per l&#39;elenco dei nomi di evento validi. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
