# # Inventario

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Identificativo univoco. | [optional]
**concurrency_token** | **int** | Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull&#39;entità. Se i valori coincidono, l&#39;operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l&#39;aggiornamento viene rifiutato. | [optional]
**magazzino_id** | **int** | Identificativo univoco di &#x60;Magazzino&#x60;. Obbligatorio in scrittura; può mancare negli inventari creati dal client Windows, che non si possono elaborare. |
**magazzino** | [**\Amica\Sdk\Model\Magazzino**](Magazzino.md) |  | [optional]
**data** | **\DateTime** | Data dell&#39;inventario: l&#39;elaborazione considera i movimenti fino a questa data inclusa. |
**nome** | **string** | Nome o descrizione. | [optional]
**fiscale** | **bool** | Indica se l&#39;inventario è fiscale. L&#39;elaborazione parte dall&#39;inventario fiscale precedente dello stesso magazzino. | [optional]
**valorizzazione** | **string** | Criterio di valorizzazione delle righe. |
**escludi_articoli_inattivi** | **bool** | Indica se l&#39;elaborazione deve escludere gli articoli non attivi. | [optional]
**note** | **string** | Note. | [optional]
**righe** | [**\Amica\Sdk\Model\InventarioRiga[]**](InventarioRiga.md) | Righe dell&#39;inventario. In sola lettura: le produce l&#39;elaborazione (&#x60;Process&#x60;). | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
