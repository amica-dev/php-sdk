# # CastellettoIva

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Identificativo univoco. | [optional]
**concurrency_token** | **int** | Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull&#39;entità. Se i valori coincidono, l&#39;operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l&#39;aggiornamento viene rifiutato. | [optional]
**registrazione_contabile_id** | **int** | Identificativo univoco di Amica.CastellettoIva.RegistrazioneContabile (registrazione contabile). | [optional]
**registrazione_contabile** | [**\Amica\Sdk\Model\RegistrazioneContabile**](RegistrazioneContabile.md) |  | [optional]
**causale_iva_id** | **int** | Identificativo univoco di Amica.CastellettoIva.CausaleIva. | [optional]
**causale_iva** | [**\Amica\Sdk\Model\CausaleIva**](CausaleIva.md) |  | [optional]
**registro_iva_id** | **int** | Identificativo univoco di &#x60;RegistroIva&#x60;. | [optional]
**imponibile** | **float** | Imponibile. | [optional]
**imposta** | **float** | Imposta. | [optional]
**detraibile** | **float** | Quota detraibile. | [optional]
**indetraibile** | **float** | Quota non detraibile. | [optional]
**totale** | **float** | Importo totale (imponibile + imposta). | [optional] [readonly]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
