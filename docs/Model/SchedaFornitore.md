# # SchedaFornitore

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** |  | [optional]
**concurrency_token** | **int** |  | [optional]
**anagrafica_id** | **int** |  | [optional]
**pagamento_id** | **int** |  | [optional]
**pagamento** | [**\Amica\Sdk\Model\Pagamento**](Pagamento.md) |  | [optional]
**listino_id** | **int** |  | [optional]
**listino** | [**\Amica\Sdk\Model\Listino**](Listino.md) |  | [optional]
**capo_area_id** | **int** |  | [optional]
**capo_area** | [**\Amica\Sdk\Model\Anagrafica**](Anagrafica.md) |  | [optional]
**categoria_anagrafica_id** | **int** |  | [optional]
**categoria_anagrafica** | [**\Amica\Sdk\Model\CategoriaAnagrafica**](CategoriaAnagrafica.md) |  | [optional]
**causale_iva_id** | **int** |  | [optional]
**causale_iva** | [**\Amica\Sdk\Model\CausaleIva**](CausaleIva.md) |  | [optional]
**fatturato_anno_corrente** | **float** |  | [optional]
**numero_fatture_anno_corrente** | **int** |  | [optional]
**insoluti_anno_corrente** | **float** |  | [optional]
**numero_insoluti_anno_corrente** | **int** |  | [optional]
**fatturato_anno_precedente** | **float** |  | [optional]
**numero_fatture_anno_precedente** | **int** |  | [optional]
**insoluti_anno_precedente** | **float** |  | [optional]
**numero_insoluti_anno_precedente** | **int** |  | [optional]
**data_ultima_fattura** | **\DateTime** |  | [optional]
**data_ultimo_pagamento** | **\DateTime** |  | [optional]
**data_ultimo_insoluto** | **\DateTime** |  | [optional]
**conto_raw** | **string** |  | [optional]
**conto** | **string** |  | [optional] [readonly]
**iscrizione_ruolo** | **string** |  | [optional]
**enasarco** | **string** |  | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
