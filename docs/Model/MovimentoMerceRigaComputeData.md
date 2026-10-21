# # MovimentoMerceRigaComputeData

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**riga** | [**\Amica\Sdk\Model\MovimentoMerceRiga**](MovimentoMerceRiga.md) |  | [optional]
**modificato** | **string** | Il campo della riga appena modificato, che decide cosa ricalcolare. Se non indicato si ricava il prezzo non modificabile da quello modificabile, come per &#x60;CausaleIva&#x60;. | [optional]
**iva_esclusa** | **bool** | &#x60;IvaEsclusa&#x60; della testata. Con IVA esclusa si scrive il prezzo netto e si             ricava quello ivato, con IVA inclusa il contrario. | [optional]
**anagrafica_id** | **int** | &#x60;AnagraficaId&#x60; della testata, se già scelta. Alla scelta dell&#39;articolo ne             determina listino e causale IVA. | [optional]
**causale_magazzino_id** | **int** | &#x60;CausaleMagazzinoId&#x60; della testata, se già scelta. Dice se il movimento è un             carico, e quindi se il listino dell&#39;anagrafica è quello del fornitore o del cliente. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
