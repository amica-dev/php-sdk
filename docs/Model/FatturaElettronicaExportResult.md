# # FatturaElettronicaExportResult

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**file_name** | **string** | Nome del file. | [optional]
**valido** | **bool** | Indica se la fattura elettronica (XML) generata ha superato il test di validazione secondo le specifiche del Sistema di Interscambio (SdI). Se &#x60;false&#x60;, vedi Amica.FatturaElettronicaExportResult.Errori per l&#39;elenco degli errori di validazione. | [optional] [readonly]
**errori** | **string[]** | Errori di validazione, se presenti. | [optional]
**xml** | **string** | Contenuto del file. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
