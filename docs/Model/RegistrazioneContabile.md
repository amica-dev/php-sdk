# # RegistrazioneContabile

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** |  | [optional]
**concurrency_token** | **int** |  | [optional]
**data** | **\DateTime** |  | [optional]
**data_competenza** | **\DateTime** |  | [optional]
**numero** | **int** |  | [optional]
**tipo_registrazione** | **string** |  | [optional]
**segno_iva** | **string** |  | [optional]
**tipo_operazione** | **string** |  | [optional]
**registro_iva_id** | **int** |  | [optional]
**anagrafica_id** | **int** |  | [optional]
**pagamento_id** | **int** |  | [optional]
**documento_id** | **int** |  | [optional]
**documento** | [**\Amica\Sdk\Model\Documento**](Documento.md) |  | [optional]
**data_documento** | **\DateTime** |  | [optional]
**documento_numero_parte_testo** | **string** |  | [optional]
**documento_numero_parte_numerica** | **int** |  | [optional]
**documento_numero** | **string** |  | [optional]
**protocollo** | **int** |  | [optional]
**descrizione** | **string** |  | [optional]
**note** | **string** |  | [optional]
**righe** | [**\Amica\Sdk\Model\RegistrazioneContabileRiga[]**](RegistrazioneContabileRiga.md) |  | [optional]
**castelletto_iva** | [**\Amica\Sdk\Model\CastellettoIva[]**](CastellettoIva.md) |  | [optional]
**scadenze** | [**\Amica\Sdk\Model\Scadenza[]**](Scadenza.md) |  | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
