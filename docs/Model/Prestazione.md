# # Prestazione

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Identificativo univoco. | [optional]
**concurrency_token** | **int** | Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull&#39;entità. Se i valori coincidono, l&#39;operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l&#39;aggiornamento viene rifiutato. | [optional]
**categoria_merceologica_id** | **int** | Identificativo univoco di Amica.Prestazione.CategoriaMerceologica. | [optional]
**categoria_merceologica** | [**\Amica\Sdk\Model\CategoriaMerceologica**](CategoriaMerceologica.md) |  | [optional]
**gruppo_merceologico_id** | **int** | Identificativo univoco di Amica.Prestazione.GruppoMerceologico. | [optional]
**gruppo_merceologico** | [**\Amica\Sdk\Model\GruppoMerceologico**](GruppoMerceologico.md) |  | [optional]
**causale_iva_id** | **int** | Identificativo univoco di Amica.Prestazione.CausaleIva. | [optional]
**causale_iva** | [**\Amica\Sdk\Model\CausaleIva**](CausaleIva.md) |  | [optional]
**nome** | **string** | Nome. |
**codice** | **string** | Codice. |
**bar_code** | **string** | Codice a barre. | [optional]
**stato** | **bool** | Indica se è attiva o meno. | [optional]
**note** | **string** | Note. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
