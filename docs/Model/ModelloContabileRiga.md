# # ModelloContabileRiga

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Identificativo univoco. | [optional]
**concurrency_token** | **int** | Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull&#39;entità. Se i valori coincidono, l&#39;operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l&#39;aggiornamento viene rifiutato. | [optional]
**modello_contabile_id** | **int** | Identificativo univoco di &#x60;ModelloContabile&#x60;. | [optional]
**causale_contabile_id** | **int** | Identificativo univoco di &#x60;CausaleContabile&#x60;. | [optional]
**conto_raw** | **string** | Numero di conto, in formato grezzo. Sono supportati fino a cinque livelli, ciascuno espresso da un massimo di cinque caratteri allineati a destra e riempiti con spazi, ad es. \&quot;  100   20\&quot; (dieci caratteri in totale) esprime \&quot;100.20\&quot;. |
**importo** | **float** | Importo. | [optional]
**tipo_importo** | **string** | Cosa rappresenta &#x60;Importo&#x60;. | [optional]
**segno_contabile** | **string** | Dare o avere. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
