# # MovimentoMerceRiga

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Identificativo univoco. | [optional]
**concurrency_token** | **int** | Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull&#39;entità. Se i valori coincidono, l&#39;operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l&#39;aggiornamento viene rifiutato. | [optional]
**movimento_merce_id** | **int** | Identificativo univoco di Amica.MovimentoMerce. | [optional]
**articolo_id** | **int** | Identificativo univoco di Amica.MovimentoMerceRiga.Articolo. | [optional]
**articolo** | [**\Amica\Sdk\Model\Articolo**](Articolo.md) |  | [optional]
**listino_id** | **int** | Identificativo univoco di Amica.Listino. | [optional]
**magazzino_id** | **int** | Identificativo univoco di Amica.Magazzino. | [optional]
**prezzo_id** | **int** | Identificativo univoco di Amica.MovimentoMerceRiga.Prezzo. | [optional]
**prezzo** | [**\Amica\Sdk\Model\Prezzo**](Prezzo.md) |  | [optional]
**causale_iva_id** | **int** | Identificativo univoco di Amica.MovimentoMerceRiga.CausaleIva. | [optional]
**causale_iva** | [**\Amica\Sdk\Model\CausaleIva**](CausaleIva.md) |  | [optional]
**taglia_id** | **int** | Identificativo univoco di Amica.MovimentoMerceRiga.Taglia. | [optional]
**taglia** | [**\Amica\Sdk\Model\Taglia**](Taglia.md) |  | [optional]
**quantit** | **float** | Quantità movimentata. | [optional]
**prezzo_netto** | **float** | Prezzo applicato, al netto dell&#39;IVA. | [optional]
**prezzo_ivato** | **float** | Prezzo applicato, IVA inclusa. | [optional]
**colore** | **string** | Colore. | [optional]
**identificativo** | **string** | Numero di serie o altro identificativo del prodotto. | [optional]
**identificativo_esteso** | **string** | Identificativo esteso del prodotto. | [optional]
**data** | **\DateTime** | Data di scadenza, produzione, fine garanzia o altro. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
