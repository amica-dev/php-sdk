# # ContoContabile

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Identificativo univoco. | [optional]
**concurrency_token** | **int** | Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull&#39;entità. Se i valori coincidono, l&#39;operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l&#39;aggiornamento viene rifiutato. | [optional]
**banca_id** | **int** | Identificativo univoco di Amica.Banca. | [optional]
**sezione** | **string** | Sezione di bilancio. | [optional]
**tipo** | **string** | Tipo di conto. | [optional]
**raw** | **string** | Numero di conto, in formato grezzo. Sono supportati fino a cinque livelli, ciascuno espresso da un massimo di cinque caratteri allineati a destra e riempiti con spazi, ad es. \&quot;  100   20\&quot; (dieci caratteri in totale) esprime \&quot;100.20\&quot;. | [optional]
**sottoconto** | **string** | Ultimo quintetto del numero di conto (sottoconto). Per \&quot;100.20\&quot;, il valore sarebbe \&quot;20\&quot;. | [optional]
**descrizione** | **string** | Descrizione. | [optional]
**conto** | **string** | Numero di conto (ad es. \&quot;100.20\&quot;). Sono supportati fino a 5 livelli. Il valore è ricavato da Amica.ContoContabile.Raw. | [optional] [readonly]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
