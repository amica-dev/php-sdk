# # ArticoloColore

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Identificativo univoco. | [optional]
**concurrency_token** | **int** | Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull&#39;entità. Se i valori coincidono, l&#39;operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l&#39;aggiornamento viene rifiutato. | [optional]
**articolo_id** | **int** | Identificativo univoco di &#x60;Articolo&#x60;. | [optional]
**articolo** | [**\Amica\Sdk\Model\Articolo**](Articolo.md) |  | [optional]
**colore** | **string** | Nome del colore. Il confronto ignora maiuscole e minuscole: &#x60;Rosso&#x60; e &#x60;rosso&#x60; sono lo stesso colore. |

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
