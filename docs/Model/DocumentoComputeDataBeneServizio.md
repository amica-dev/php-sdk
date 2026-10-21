# # DocumentoComputeDataBeneServizio

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**ritenuta_fiscale** | **bool** | Indica se la ritenuta fiscale è applicabile o meno. | [optional]
**importo** | **float** | Importo. | [optional]
**importo_ivato** | **float** | Importo IVA inclusa della riga, come restituito da &#x60;beneservizio/compute&#x60;. Serve con IVA inclusa (&#x60;IvaEsclusa&#x60; a &#x60;false&#x60;): l&#39;imposta del riepilogo è la differenza fra ivato e imponibile. Se non indicato si ricava da importo e aliquota. | [optional]
**causale_iva_id** | **int** | Identificativo univoco del codice IVA. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
