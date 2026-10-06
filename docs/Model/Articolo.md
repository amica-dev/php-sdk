# # Articolo

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Identificativo univoco. | [optional]
**concurrency_token** | **int** | Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull&#39;entità. Se i valori coincidono, l&#39;operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l&#39;aggiornamento viene rifiutato. | [optional]
**categoria_merceologica_id** | **int** | Identificativo univoco della &#x60;CategoriaMerceologica&#x60; (categoria merceologica). | [optional]
**categoria_merceologica** | [**\Amica\Sdk\Model\CategoriaMerceologica**](CategoriaMerceologica.md) |  | [optional]
**gruppo_merceologico_id** | **int** | Identificativo univoco del &#x60;GruppoMerceologico&#x60; (gruppo merceologico). | [optional]
**gruppo_merceologico** | [**\Amica\Sdk\Model\GruppoMerceologico**](GruppoMerceologico.md) |  | [optional]
**causale_iva_id** | **int** | Identificativo univoco della &#x60;CausaleIva&#x60; (causale IVA). Id univoco della &lt;a href&#x3D;\&quot;#tag/CausaleIva\&quot;&gt;causale IVA&lt;/a&gt; predefinita per l&#39;articolo. | [optional]
**causale_iva** | [**\Amica\Sdk\Model\CausaleIva**](CausaleIva.md) |  | [optional]
**taglia_id** | **int** | Identificativo univoco della &#x60;Taglia&#x60; (taglia). | [optional]
**taglia** | [**\Amica\Sdk\Model\Taglia**](Taglia.md) |  | [optional]
**marca_id** | **int** | Identificativo univoco della &#x60;Marca&#x60;. | [optional]
**marca** | [**\Amica\Sdk\Model\Marca**](Marca.md) |  | [optional]
**produttore_id** | **int** | Identificativo univoco del &#x60;Produttore&#x60; (produttore). | [optional]
**produttore** | [**\Amica\Sdk\Model\Produttore**](Produttore.md) |  | [optional]
**nome** | **string** | Nome. |
**codice** | **string** | Identificativo, o codice. |
**nome_breve** | **string** | Nome breve. | [optional]
**codice_fornitore** | **string** | Codice dell&#39;articolo o prodotto presso il fornitore. | [optional]
**codice_produttore** | **string** | Codice dell&#39;articolo o prodotto presso il produttore. | [optional]
**gestione_articolo** | **string** | Modalità di gestione. | [optional]
**contesto_articolo** | **string** | Contesto in cui viene utilizzato. | [optional]
**bar_code** | **string** | Codice a barre. | [optional]
**costo_ultimo** | **float** | Ultimo prezzo di costo. | [optional]
**costo_medio** | **float** | Prezzo di costo medio. | [optional]
**unit_misura_intera** | **bool** | Indica se l&#39;unità di misura deve essere arrotondata all&#39;intero più vicino. | [optional]
**articolo_composto** | **bool** | Indica se è composto assemblando altri articoli. | [optional]
**stato** | **bool** | Stato. &#x60;true&#x60; se attivo, &#x60;false&#x60; altrimenti. | [optional]
**data_inserimento** | **\DateTime** | Data di primo inserimento. | [optional]
**unit_misura_movimenti** | **string** | Unità di misura da utilizzare nei movimenti di magazzino. | [optional]
**unit_misura_inventario** | **string** | Unità di misura da utilizzare nelle elaborazioni di inventario. | [optional]
**fattore_conversione_unit_misura** | **float** | Fattore di conversione tra unità di movimento e unità di inventario. | [optional]
**note** | **string** | Note. | [optional]
**media_file** | [**\Amica\Sdk\Model\ArticoloMediaFile**](ArticoloMediaFile.md) |  | [optional]
**conto_raw_vendita** | **string** | Conto da utilizzare nelle registrazioni contabili di vendita. | [optional]
**conto_raw_acquisto** | **string** | Conto da utilizzare nelle registrazioni contabili di acquisto. | [optional]
**distinta_base** | [**\Amica\Sdk\Model\Componente[]**](Componente.md) | Distinta base. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
