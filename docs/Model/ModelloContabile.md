# # ModelloContabile

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Identificativo univoco. | [optional]
**concurrency_token** | **int** | Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull&#39;entità. Se i valori coincidono, l&#39;operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l&#39;aggiornamento viene rifiutato. | [optional]
**codice** | **string** | Codice del modello. |
**nome** | **string** | Nome. |
**stato** | **bool** | Indica se il modello è attivo o meno. | [optional]
**registro_iva_id** | **int** | Identificativo univoco di &#x60;RegistroIva&#x60;. | [optional]
**segno_iva** | **string** | Positività o negatività dell&#39;IVA. | [optional]
**tipo_operazione_contabile** | **string** | Tipo di operazione contabile. | [optional]
**tipo_registrazione_contabile** | **string** | Tipo di registrazione contabile. | [optional]
**azione_partita** | **string** | Effetto sulla partita aperta. | [optional]
**righe** | [**\Amica\Sdk\Model\ModelloContabileRiga[]**](ModelloContabileRiga.md) | Righe del modello contabile. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
