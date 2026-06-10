# # Scadenza

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Identificativo univoco. | [optional]
**concurrency_token** | **int** | Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull&#39;entità. Se i valori coincidono, l&#39;operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l&#39;aggiornamento viene rifiutato. | [optional]
**registrazione_contabile_id** | **int** | Identificativo univoco di Amica.Scadenza.RegistrazioneContabile. | [optional]
**registrazione_contabile** | [**\Amica\Sdk\Model\RegistrazioneContabile**](RegistrazioneContabile.md) |  | [optional]
**anagrafica_id** | **int** | Identificativo univoco di Amica.Scadenza.Anagrafica. | [optional]
**anagrafica** | [**\Amica\Sdk\Model\Anagrafica**](Anagrafica.md) |  | [optional]
**documento_id** | **int** | Identificativo univoco di Amica.Scadenza.Documento. | [optional]
**documento** | [**\Amica\Sdk\Model\Documento**](Documento.md) |  | [optional]
**modalita_pagamento_id** | **int** | Identificativo univoco di Amica.Scadenza.ModalitaPagamento. | [optional]
**modalita_pagamento** | [**\Amica\Sdk\Model\ModalitaPagamento**](ModalitaPagamento.md) |  | [optional]
**pagata_manualmente** | **bool** | Indica se la scadenza è stata impostata manualmente come &#39;pagata&#39;. | [optional]
**pagata** | **bool** | Indica se la scadenza è pagata. | [optional] [readonly]
**data** | **\DateTime** | Data di scadenza. | [optional]
**data_pagamento** | **\DateTime** | Data di pagamento. | [optional]
**data_documento** | **\DateTime** | Data del documento fiscale. | [optional]
**importo** | **float** | Importo della scadenza. | [optional]
**partita_anno** | **int** | Anno della partita aperta. | [optional]
**partita_numero** | **int** | Numero della partita aperta. | [optional]
**note** | **string** | Note. | [optional]
**descrizione** | **string** | Descrizione. | [optional]
**documento_numero_parte_numerica** | **int** | Numero documento, la parte numerica (es. 99). | [optional]
**documento_numero_parte_testo** | **string** | Numero documento, la parte alfanumerica (es. \&quot;bis\&quot;) | [optional]
**documento_numero** | **string** | Numero documento, es. \&quot;99/bis\&quot;. Il documento deve essere già salvato sul sistema. | [optional]
**tipo_scadenza** | **string** | Tipo di scadenza. | [optional]
**importo_pagato** | **float** | L&#39;importo già pagato della scadenza. | [optional]
**importo_da_pagare** | **float** | Rimanenza che risulta ancora da pagare. | [optional] [readonly]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
