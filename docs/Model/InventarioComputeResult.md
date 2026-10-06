# # InventarioComputeResult

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**righe** | [**\Amica\Sdk\Model\InventarioRiga[]**](InventarioRiga.md) | Righe dell&#39;inventario elaborato. | [optional]
**avvisi** | **string[]** | Avvisi dell&#39;elaborazione, da mostrare all&#39;utente. | [optional]
**concurrency_token** | **int** | Impronta delle righe elaborate. Passata a &#x60;Process&#x60;, impedisce il salvataggio se nel frattempo i dati sono cambiati e l&#39;elaborazione darebbe righe diverse. | [optional]
**giacenze_allineabili** | **bool** | Indica se &#x60;Process&#x60; con &#x60;alignStock&#x60; allineerebbe davvero le giacenze: non le allinea se nel magazzino ci sono movimenti successivi alla data dell&#39;inventario. | [optional]
**inventario** | [**\Amica\Sdk\Model\Inventario**](Inventario.md) |  | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
