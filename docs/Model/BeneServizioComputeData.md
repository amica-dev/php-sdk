# # BeneServizioComputeData

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**iva_esclusa** | **bool** | Indica se il calcolo debba essere effettuato a partire da Amica.BeneServizioComputeData.Prezzo o da Amica.BeneServizioComputeData.PrezzoIvato. | [optional]
**causale_iva_id** | **int** | Identificativo univoco di Amica.CausaleIva. | [optional]
**listino_id** | **int** | Identificativo univoco di Amica.Listino. | [optional]
**quantit** | **float** | Quantità. | [optional]
**prezzo** | **float** | Prezzo IVA esclusa. | [optional]
**prezzo_ivato** | **float** | Prezzo IVA inclusa. | [optional]
**sconto1** | **float** | Primo sconto percentuale, applicato al prezzo del bene o servizio. | [optional]
**sconto2** | **float** | Secondo sconto percentuale, applicato dopo l&#39;applicazione di Amica.BeneServizioComputeData.Sconto1 | [optional]
**sconto3** | **float** | Terzo sconto percentuale, applicato dopo l&#39;applicazione di Amica.BeneServizioComputeData.Sconto2 | [optional]
**sconto4** | **float** | Quarto sconto percentuale, applicato dopo l&#39;applicazione di Amica.BeneServizioComputeData.Sconto3 | [optional]
**sconto_incondizionato** | **float** | Sconto incondizionato, in valuta, applicato al prezzo del bene o servizio. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
