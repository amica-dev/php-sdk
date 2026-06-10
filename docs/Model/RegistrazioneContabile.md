# # RegistrazioneContabile

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Identificativo univoco. | [optional]
**concurrency_token** | **int** | Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull&#39;entità. Se i valori coincidono, l&#39;operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l&#39;aggiornamento viene rifiutato. | [optional]
**data** | **\DateTime** | Data della registrazione contabile. | [optional]
**data_competenza** | **\DateTime** | Data di competenza. | [optional]
**numero** | **int** | Numero della registrazione contabile. | [optional]
**tipo_registrazione** | **string** | Tipo di registrazione. | [optional]
**segno_iva** | **string** | Positività o negatività dell&#39;IVA. | [optional]
**tipo_operazione** | **string** | Tipo di operazione contabile. | [optional]
**registro_iva_id** | **int** | Identificativo univoco di &#x60;RegistroIva&#x60;. | [optional]
**anagrafica_id** | **int** | Identificativo univoco di Amica.Anagrafica. | [optional]
**pagamento_id** | **int** | Identificativo univoco di Amica.Pagamento. | [optional]
**documento_id** | **int** | Identificativo univoco di Amica.RegistrazioneContabile.Documento. | [optional]
**documento** | [**\Amica\Sdk\Model\Documento**](Documento.md) |  | [optional]
**data_documento** | **\DateTime** | Data del documento di riferimento. | [optional]
**documento_numero_parte_testo** | **string** | La parte alfanumerica del numero documento, se presente (es. \&quot;bis\&quot;). | [optional]
**documento_numero_parte_numerica** | **int** | La parte numerica del numero documento (es. \&quot;99\&quot;). | [optional]
**documento_numero** | **string** | Numero documento completo, es. \&quot;99/bis\&quot;. Il documento deve essere già stato salvato sul backend. | [optional]
**protocollo** | **int** | Numero di protocollo. | [optional]
**descrizione** | **string** | Descrizione. | [optional]
**note** | **string** | Note. | [optional]
**righe** | [**\Amica\Sdk\Model\RegistrazioneContabileRiga[]**](RegistrazioneContabileRiga.md) | Righe della registrazione contabile. | [optional]
**castelletto_iva** | [**\Amica\Sdk\Model\CastellettoIva[]**](CastellettoIva.md) | Importo IVA da versare. | [optional]
**scadenze** | [**\Amica\Sdk\Model\Scadenza[]**](Scadenza.md) | Scadenze. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
