# # Componente

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Identificativo univoco. | [optional]
**concurrency_token** | **int** | Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull&#39;entità. Se i valori coincidono, l&#39;operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l&#39;aggiornamento viene rifiutato. | [optional]
**articolo_id** | **int** | Identificativo univoco dell&#39;Amica.Componente.Articolo da comporre. | [optional]
**articolo** | [**\Amica\Sdk\Model\Articolo**](Articolo.md) |  | [optional]
**articolo_componente_id** | **int** | Identificativo univoco del componente (distinta base). | [optional]
**articolo_componente** | [**\Amica\Sdk\Model\Articolo**](Articolo.md) |  | [optional]
**quantita_necessaria** | **float** | Quantità necessaria per l&#39;assemblaggio. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
