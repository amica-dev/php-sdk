<?php
/**
 * SchedaCliente
 *
 * PHP version 7.4
 *
 * @category Class
 * @package  Amica\Sdk
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */

/**
 * Amica Cloud API
 *
 * L'Amica Cloud API fornisce accesso programmatico a tutti i dati aziendali gestiti da [Amica 20](https://gestionaleamica.com). Supporta il ciclo completo di documenti, fatture, magazzino, contabilità e anagrafiche. Per guide e documentazione completa, visitare [amica.dev](https://amica.dev/).  ## Autenticazione  Tutte le richieste API richiedono un token OAuth 2.0 Bearer valido, da includere nell'header `Authorization`:  ``` Authorization: Bearer <token> ```  ### Registra la tua app  Per integrare l'Amica Cloud API registra la tua applicazione sull'[**App Store di Amica**](https://apps.amica20.it). Al termine della registrazione ottieni una coppia di credenziali OAuth 2.0 — `client_id` e `client_secret` (il secret è mostrato **una sola volta**) — e scegli gli **scope** e il **flusso di autenticazione** desiderati.  ### Scegli il flusso  La scelta dipende da **come** la tua applicazione si autentica:  - C'è un **utente** che può accedere via browser (web app, SPA, app mobile)? → **Authorization Code con PKCE**. - È un **servizio headless** (backend, batch, integrazione server-to-server) di una **singola azienda**, senza un utente che effettua il login? → **Client Credentials**.  #### Authorization Code con PKCE  Consigliato per le app che operano per conto di un utente Amica ed è l'**unico flusso disponibile per le app pubblicate sul marketplace**. Una sola registrazione serve un numero qualsiasi di aziende: ogni utente autorizza la tua app per la propria azienda tramite la pagina di consenso dell'identity provider, e il token riceve automaticamente il contesto di quell'azienda. La tua app riceve un `code` da scambiare con un access token.  ``` GET  {identity-provider}/connect/authorize?response_type=code&client_id=<client_id>&redirect_uri=<redirect_uri>&scope=openid%20read%20write%20documenti&code_challenge=<challenge>&code_challenge_method=S256 POST {identity-provider}/connect/token      grant_type=authorization_code&code=<code>&code_verifier=<verifier>&client_id=<client_id>&client_secret=<client_secret>&redirect_uri=<redirect_uri> ```  #### Client Credentials  Per integrazioni **server-to-server** senza utente interattivo. Disponibile solo per le **app private**, non per quelle pubblicate sul marketplace. Un'app Client Credentials è **legata a una singola azienda** — quella dell'utente che la registra — e il token ne riceve automaticamente il contesto. Per servire un'azienda diversa serve una registrazione separata; se devi distribuire la stessa app a più aziende usa invece **Authorization Code**.  ``` POST {identity-provider}/connect/token Content-Type: application/x-www-form-urlencoded  grant_type=client_credentials&client_id=<client_id>&client_secret=<client_secret>&scope=comune read write documenti ```  > **Importante:** non includere lo scope `openid` (né `profile`/`email`) in questo flusso. `openid` attiva OpenID Connect — richiede un utente che effettua il login e l'emissione di un ID token — cosa che un flusso server-to-server non prevede: la richiesta viene rifiutata con l'errore `Client cannot request OpenID scopes in client credentials flow`. Indica solo gli scope di risorsa (`comune`, `magazzino`, `documenti`, `contabilita`, ed eventualmente `read`/`write`).  In entrambi i casi la risposta contiene l'`access_token` (JWT con `aud: amica-cloud`) da usare nelle chiamate successive finché non scade (`expires_in`), dopodiché va richiesto un nuovo token. Ogni token contiene uno o più **scope** che determinano le aree accessibili (vedi sotto). Dalla [API Reference](https://amica.dev) è possibile incollare un access token per provare gli endpoint direttamente dal browser.  ## Scope di autorizzazione  L'accesso agli endpoint è governato dagli scope presenti nel token OAuth:  | Scope | Descrizione | |-|-| | `comune` | Anagrafiche: contatti, indirizzi, nazioni, valute, modalità di pagamento, ecc. | | `magazzino` | Magazzino: articoli, listini, movimenti di magazzino, giacenze, distinte base. | | `contabilita` | Contabilità: registrazioni contabili, scadenze, castelletti IVA, fatture elettroniche XML. | | `documenti` | Documenti: fatture, note di credito, DDT, ordini, preventivi, proforma ed emissione. |  Una richiesta verso un endpoint non coperto dallo scope del token riceverà una risposta `403 Forbidden`.  ## Company ID  La maggior parte degli endpoint è relativa a una specifica azienda e segue il pattern:  ``` /api/v3/{companyId}/risorsa ```  Il `companyId` è l'identificativo univoco di ciascun database aziendale. Usare l'endpoint `GET /api/v3/config/Azienda` per ottenere l'elenco delle aziende disponibili.  ## Paginazione  Gli endpoint che restituiscono liste supportano la paginazione tramite query parameter:  | Parametro | Descrizione | |-|-| | `Skip` | Numero di elementi da saltare dall'inizio del set di risultati. | | `Take` | Numero di elementi da includere nella risposta. |  La risposta include due header:  | Header | Descrizione | |-|-| | `X-Read-TotalCount` | Numero totale di elementi che soddisfano il filtro corrente. | | `X-Read-Count` | Numero di elementi effettivamente restituiti nel body della risposta. |  ## Ordinamento e filtri  Gli endpoint lista accettano parametri aggiuntivi:  | Parametro | Descrizione | |-|-| | `Order` | Lista di proprietà per l'ordinamento, separate da virgola. Un segno meno (`-`) prima del nome indica ordine discendente. Es. `-RagioneSociale1,Citta`. | | `Filter` | Condizioni di ricerca: lista di espressioni separate da virgola, ciascuna formata da nome campo, operatore, valore ed eventuale connettore logico. Operatori: `$eq`, `$ne`, `$gt`, `$lt`, `$lte`, `$gte`, `$ct` (contiene), `$nct`, `$sw` (inizia con), `$ew` (finisce con). Connettori: `$and` (default, omissibile) e `$or`; le sotto-query si racchiudono tra `[]`. Es. `RagioneSociale1 $ct acme $or, Fido $gt 1000`. | | `SkipTotalCount` | Se `true`, l'header `X-Read-TotalCount` viene omesso dalla risposta: migliori prestazioni a scapito del conteggio totale. Default `false`. |  ## Espansione oggetti correlati  Gli endpoint singoli e lista supportano il parametro `Expand`. Quando fornito, gli oggetti correlati vengono inclusi inline nella risposta anziché restituiti come riferimenti. È possibile specificare espansioni multiple separate da virgola.  ## Gestione errori  L'API usa codici di stato HTTP standard e restituisce [RFC 7807](https://tools.ietf.org/html/rfc7807) Problem Details in caso di errore:  | Codice | Significato | |-|-| | `400` | Bad Request — validazione fallita o parametro obbligatorio mancante. | | `401` | Unauthorized — token mancante o scaduto. | | `403` | Forbidden — il token non possiede lo scope richiesto. | | `404` | Not Found — la risorsa richiesta non esiste. | | `409` | Conflict — conflitto di concorrenza. | | `422` | Unprocessable Entity — entità non processabile (es. token di concorrenza obsoleto). |  Alcune risposte `400` includono un campo `overridable` che indica se l'errore di validazione può essere ignorato impostando il parametro `validationOverride` a `true`.  ## Controllo della concorrenza  Le entità contengono una proprietà `ConcurrencyToken`. Quando si aggiorna un'entità tramite `PUT`, il token nel body della richiesta viene confrontato con il valore memorizzato. Se differiscono, viene restituito un `422 Unprocessable Entity`. Usare sempre l'ultimo token ricevuto da una risposta `GET` o `POST`.
 *
 * The version of the OpenAPI document: v3
 * Contact: assistenza@gestionaleamica.com
 * Generated by: https://openapi-generator.tech
 * Generator version: 7.11.0
 */

/**
 * NOTE: This class is auto generated by OpenAPI Generator (https://openapi-generator.tech).
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */

namespace Amica\Sdk\Model;

use \ArrayAccess;
use \Amica\Sdk\ObjectSerializer;

/**
 * SchedaCliente Class Doc Comment
 *
 * @category Class
 * @description Impostazioni e informazioni di vendita di un&#39;Amica.Anagrafica.
 * @package  Amica\Sdk
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class SchedaCliente implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'SchedaCliente';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'id' => 'int',
        'concurrency_token' => 'int',
        'anagrafica_id' => 'int',
        'agente_id' => 'int',
        'agente' => '\Amica\Sdk\Model\Anagrafica',
        'pagamento_id' => 'int',
        'pagamento' => '\Amica\Sdk\Model\Pagamento',
        'listino_id' => 'int',
        'listino' => '\Amica\Sdk\Model\Listino',
        'categoria_anagrafica_id' => 'int',
        'categoria_anagrafica' => '\Amica\Sdk\Model\CategoriaAnagrafica',
        'causale_iva_id' => 'int',
        'causale_iva' => '\Amica\Sdk\Model\CausaleIva',
        'fido' => 'float',
        'sconto' => 'float',
        'fatturato_anno_corrente' => 'float',
        'numero_fatture_anno_corrente' => 'int',
        'insoluti_anno_corrente' => 'float',
        'numero_insoluti_anno_corrente' => 'int',
        'fatturato_anno_precedente' => 'float',
        'numero_fatture_anno_precedente' => 'int',
        'insoluti_anno_precedente' => 'float',
        'numero_insoluti_anno_precedente' => 'int',
        'data_ultima_fattura' => '\DateTime',
        'data_ultimo_pagamento' => '\DateTime',
        'data_ultimo_insoluto' => '\DateTime',
        'conto_raw' => 'string',
        'conto' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'id' => 'int32',
        'concurrency_token' => 'int32',
        'anagrafica_id' => 'int32',
        'agente_id' => 'int32',
        'agente' => null,
        'pagamento_id' => 'int32',
        'pagamento' => null,
        'listino_id' => 'int32',
        'listino' => null,
        'categoria_anagrafica_id' => 'int32',
        'categoria_anagrafica' => null,
        'causale_iva_id' => 'int32',
        'causale_iva' => null,
        'fido' => 'double',
        'sconto' => 'double',
        'fatturato_anno_corrente' => 'double',
        'numero_fatture_anno_corrente' => 'int32',
        'insoluti_anno_corrente' => 'double',
        'numero_insoluti_anno_corrente' => 'int32',
        'fatturato_anno_precedente' => 'double',
        'numero_fatture_anno_precedente' => 'int32',
        'insoluti_anno_precedente' => 'double',
        'numero_insoluti_anno_precedente' => 'int32',
        'data_ultima_fattura' => 'date-time',
        'data_ultimo_pagamento' => 'date-time',
        'data_ultimo_insoluto' => 'date-time',
        'conto_raw' => null,
        'conto' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'id' => false,
        'concurrency_token' => false,
        'anagrafica_id' => true,
        'agente_id' => true,
        'agente' => false,
        'pagamento_id' => true,
        'pagamento' => false,
        'listino_id' => true,
        'listino' => false,
        'categoria_anagrafica_id' => true,
        'categoria_anagrafica' => false,
        'causale_iva_id' => true,
        'causale_iva' => false,
        'fido' => false,
        'sconto' => false,
        'fatturato_anno_corrente' => false,
        'numero_fatture_anno_corrente' => false,
        'insoluti_anno_corrente' => false,
        'numero_insoluti_anno_corrente' => false,
        'fatturato_anno_precedente' => false,
        'numero_fatture_anno_precedente' => false,
        'insoluti_anno_precedente' => false,
        'numero_insoluti_anno_precedente' => false,
        'data_ultima_fattura' => true,
        'data_ultimo_pagamento' => true,
        'data_ultimo_insoluto' => true,
        'conto_raw' => true,
        'conto' => true
    ];

    /**
      * If a nullable field gets set to null, insert it here
      *
      * @var boolean[]
      */
    protected array $openAPINullablesSetToNull = [];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function openAPITypes()
    {
        return self::$openAPITypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function openAPIFormats()
    {
        return self::$openAPIFormats;
    }

    /**
     * Array of nullable properties
     *
     * @return array
     */
    protected static function openAPINullables(): array
    {
        return self::$openAPINullables;
    }

    /**
     * Array of nullable field names deliberately set to null
     *
     * @return boolean[]
     */
    private function getOpenAPINullablesSetToNull(): array
    {
        return $this->openAPINullablesSetToNull;
    }

    /**
     * Setter - Array of nullable field names deliberately set to null
     *
     * @param boolean[] $openAPINullablesSetToNull
     */
    private function setOpenAPINullablesSetToNull(array $openAPINullablesSetToNull): void
    {
        $this->openAPINullablesSetToNull = $openAPINullablesSetToNull;
    }

    /**
     * Checks if a property is nullable
     *
     * @param string $property
     * @return bool
     */
    public static function isNullable(string $property): bool
    {
        return self::openAPINullables()[$property] ?? false;
    }

    /**
     * Checks if a nullable property is set to null.
     *
     * @param string $property
     * @return bool
     */
    public function isNullableSetToNull(string $property): bool
    {
        return in_array($property, $this->getOpenAPINullablesSetToNull(), true);
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'id' => 'id',
        'concurrency_token' => 'concurrencyToken',
        'anagrafica_id' => 'anagraficaId',
        'agente_id' => 'agenteId',
        'agente' => 'agente',
        'pagamento_id' => 'pagamentoId',
        'pagamento' => 'pagamento',
        'listino_id' => 'listinoId',
        'listino' => 'listino',
        'categoria_anagrafica_id' => 'categoriaAnagraficaId',
        'categoria_anagrafica' => 'categoriaAnagrafica',
        'causale_iva_id' => 'causaleIvaId',
        'causale_iva' => 'causaleIva',
        'fido' => 'fido',
        'sconto' => 'sconto',
        'fatturato_anno_corrente' => 'fatturatoAnnoCorrente',
        'numero_fatture_anno_corrente' => 'numeroFattureAnnoCorrente',
        'insoluti_anno_corrente' => 'insolutiAnnoCorrente',
        'numero_insoluti_anno_corrente' => 'numeroInsolutiAnnoCorrente',
        'fatturato_anno_precedente' => 'fatturatoAnnoPrecedente',
        'numero_fatture_anno_precedente' => 'numeroFattureAnnoPrecedente',
        'insoluti_anno_precedente' => 'insolutiAnnoPrecedente',
        'numero_insoluti_anno_precedente' => 'numeroInsolutiAnnoPrecedente',
        'data_ultima_fattura' => 'dataUltimaFattura',
        'data_ultimo_pagamento' => 'dataUltimoPagamento',
        'data_ultimo_insoluto' => 'dataUltimoInsoluto',
        'conto_raw' => 'contoRaw',
        'conto' => 'conto'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'id' => 'setId',
        'concurrency_token' => 'setConcurrencyToken',
        'anagrafica_id' => 'setAnagraficaId',
        'agente_id' => 'setAgenteId',
        'agente' => 'setAgente',
        'pagamento_id' => 'setPagamentoId',
        'pagamento' => 'setPagamento',
        'listino_id' => 'setListinoId',
        'listino' => 'setListino',
        'categoria_anagrafica_id' => 'setCategoriaAnagraficaId',
        'categoria_anagrafica' => 'setCategoriaAnagrafica',
        'causale_iva_id' => 'setCausaleIvaId',
        'causale_iva' => 'setCausaleIva',
        'fido' => 'setFido',
        'sconto' => 'setSconto',
        'fatturato_anno_corrente' => 'setFatturatoAnnoCorrente',
        'numero_fatture_anno_corrente' => 'setNumeroFattureAnnoCorrente',
        'insoluti_anno_corrente' => 'setInsolutiAnnoCorrente',
        'numero_insoluti_anno_corrente' => 'setNumeroInsolutiAnnoCorrente',
        'fatturato_anno_precedente' => 'setFatturatoAnnoPrecedente',
        'numero_fatture_anno_precedente' => 'setNumeroFattureAnnoPrecedente',
        'insoluti_anno_precedente' => 'setInsolutiAnnoPrecedente',
        'numero_insoluti_anno_precedente' => 'setNumeroInsolutiAnnoPrecedente',
        'data_ultima_fattura' => 'setDataUltimaFattura',
        'data_ultimo_pagamento' => 'setDataUltimoPagamento',
        'data_ultimo_insoluto' => 'setDataUltimoInsoluto',
        'conto_raw' => 'setContoRaw',
        'conto' => 'setConto'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'id' => 'getId',
        'concurrency_token' => 'getConcurrencyToken',
        'anagrafica_id' => 'getAnagraficaId',
        'agente_id' => 'getAgenteId',
        'agente' => 'getAgente',
        'pagamento_id' => 'getPagamentoId',
        'pagamento' => 'getPagamento',
        'listino_id' => 'getListinoId',
        'listino' => 'getListino',
        'categoria_anagrafica_id' => 'getCategoriaAnagraficaId',
        'categoria_anagrafica' => 'getCategoriaAnagrafica',
        'causale_iva_id' => 'getCausaleIvaId',
        'causale_iva' => 'getCausaleIva',
        'fido' => 'getFido',
        'sconto' => 'getSconto',
        'fatturato_anno_corrente' => 'getFatturatoAnnoCorrente',
        'numero_fatture_anno_corrente' => 'getNumeroFattureAnnoCorrente',
        'insoluti_anno_corrente' => 'getInsolutiAnnoCorrente',
        'numero_insoluti_anno_corrente' => 'getNumeroInsolutiAnnoCorrente',
        'fatturato_anno_precedente' => 'getFatturatoAnnoPrecedente',
        'numero_fatture_anno_precedente' => 'getNumeroFattureAnnoPrecedente',
        'insoluti_anno_precedente' => 'getInsolutiAnnoPrecedente',
        'numero_insoluti_anno_precedente' => 'getNumeroInsolutiAnnoPrecedente',
        'data_ultima_fattura' => 'getDataUltimaFattura',
        'data_ultimo_pagamento' => 'getDataUltimoPagamento',
        'data_ultimo_insoluto' => 'getDataUltimoInsoluto',
        'conto_raw' => 'getContoRaw',
        'conto' => 'getConto'
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$openAPIModelName;
    }


    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param mixed[]|null $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(?array $data = null)
    {
        $this->setIfExists('id', $data ?? [], null);
        $this->setIfExists('concurrency_token', $data ?? [], null);
        $this->setIfExists('anagrafica_id', $data ?? [], null);
        $this->setIfExists('agente_id', $data ?? [], null);
        $this->setIfExists('agente', $data ?? [], null);
        $this->setIfExists('pagamento_id', $data ?? [], null);
        $this->setIfExists('pagamento', $data ?? [], null);
        $this->setIfExists('listino_id', $data ?? [], null);
        $this->setIfExists('listino', $data ?? [], null);
        $this->setIfExists('categoria_anagrafica_id', $data ?? [], null);
        $this->setIfExists('categoria_anagrafica', $data ?? [], null);
        $this->setIfExists('causale_iva_id', $data ?? [], null);
        $this->setIfExists('causale_iva', $data ?? [], null);
        $this->setIfExists('fido', $data ?? [], null);
        $this->setIfExists('sconto', $data ?? [], null);
        $this->setIfExists('fatturato_anno_corrente', $data ?? [], null);
        $this->setIfExists('numero_fatture_anno_corrente', $data ?? [], null);
        $this->setIfExists('insoluti_anno_corrente', $data ?? [], null);
        $this->setIfExists('numero_insoluti_anno_corrente', $data ?? [], null);
        $this->setIfExists('fatturato_anno_precedente', $data ?? [], null);
        $this->setIfExists('numero_fatture_anno_precedente', $data ?? [], null);
        $this->setIfExists('insoluti_anno_precedente', $data ?? [], null);
        $this->setIfExists('numero_insoluti_anno_precedente', $data ?? [], null);
        $this->setIfExists('data_ultima_fattura', $data ?? [], null);
        $this->setIfExists('data_ultimo_pagamento', $data ?? [], null);
        $this->setIfExists('data_ultimo_insoluto', $data ?? [], null);
        $this->setIfExists('conto_raw', $data ?? [], null);
        $this->setIfExists('conto', $data ?? [], null);
    }

    /**
    * Sets $this->container[$variableName] to the given data or to the given default Value; if $variableName
    * is nullable and its value is set to null in the $fields array, then mark it as "set to null" in the
    * $this->openAPINullablesSetToNull array
    *
    * @param string $variableName
    * @param array  $fields
    * @param mixed  $defaultValue
    */
    private function setIfExists(string $variableName, array $fields, $defaultValue): void
    {
        if (self::isNullable($variableName) && array_key_exists($variableName, $fields) && is_null($fields[$variableName])) {
            $this->openAPINullablesSetToNull[] = $variableName;
        }

        $this->container[$variableName] = $fields[$variableName] ?? $defaultValue;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        if (!is_null($this->container['conto_raw']) && (mb_strlen($this->container['conto_raw']) > 25)) {
            $invalidProperties[] = "invalid value for 'conto_raw', the character length must be smaller than or equal to 25.";
        }

        if (!is_null($this->container['conto_raw']) && (mb_strlen($this->container['conto_raw']) < 0)) {
            $invalidProperties[] = "invalid value for 'conto_raw', the character length must be bigger than or equal to 0.";
        }

        return $invalidProperties;
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        return count($this->listInvalidProperties()) === 0;
    }


    /**
     * Gets id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->container['id'];
    }

    /**
     * Sets id
     *
     * @param int|null $id Identificativo univoco.
     *
     * @return self
     */
    public function setId($id)
    {
        if (is_null($id)) {
            throw new \InvalidArgumentException('non-nullable id cannot be null');
        }
        $this->container['id'] = $id;

        return $this;
    }

    /**
     * Gets concurrency_token
     *
     * @return int|null
     */
    public function getConcurrencyToken()
    {
        return $this->container['concurrency_token'];
    }

    /**
     * Sets concurrency_token
     *
     * @param int|null $concurrency_token Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull'entità. Se i valori coincidono, l'operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l'aggiornamento viene rifiutato.
     *
     * @return self
     */
    public function setConcurrencyToken($concurrency_token)
    {
        if (is_null($concurrency_token)) {
            throw new \InvalidArgumentException('non-nullable concurrency_token cannot be null');
        }
        $this->container['concurrency_token'] = $concurrency_token;

        return $this;
    }

    /**
     * Gets anagrafica_id
     *
     * @return int|null
     */
    public function getAnagraficaId()
    {
        return $this->container['anagrafica_id'];
    }

    /**
     * Sets anagrafica_id
     *
     * @param int|null $anagrafica_id Identificativo univoco di Amica.Anagrafica.
     *
     * @return self
     */
    public function setAnagraficaId($anagrafica_id)
    {
        if (is_null($anagrafica_id)) {
            array_push($this->openAPINullablesSetToNull, 'anagrafica_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('anagrafica_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['anagrafica_id'] = $anagrafica_id;

        return $this;
    }

    /**
     * Gets agente_id
     *
     * @return int|null
     */
    public function getAgenteId()
    {
        return $this->container['agente_id'];
    }

    /**
     * Sets agente_id
     *
     * @param int|null $agente_id Identificativo univoco di Amica.Anagrafica dell'agente.
     *
     * @return self
     */
    public function setAgenteId($agente_id)
    {
        if (is_null($agente_id)) {
            array_push($this->openAPINullablesSetToNull, 'agente_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('agente_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['agente_id'] = $agente_id;

        return $this;
    }

    /**
     * Gets agente
     *
     * @return \Amica\Sdk\Model\Anagrafica|null
     */
    public function getAgente()
    {
        return $this->container['agente'];
    }

    /**
     * Sets agente
     *
     * @param \Amica\Sdk\Model\Anagrafica|null $agente agente
     *
     * @return self
     */
    public function setAgente($agente)
    {
        if (is_null($agente)) {
            throw new \InvalidArgumentException('non-nullable agente cannot be null');
        }
        $this->container['agente'] = $agente;

        return $this;
    }

    /**
     * Gets pagamento_id
     *
     * @return int|null
     */
    public function getPagamentoId()
    {
        return $this->container['pagamento_id'];
    }

    /**
     * Sets pagamento_id
     *
     * @param int|null $pagamento_id Identificativo univoco di Amica.SchedaCliente.Pagamento.
     *
     * @return self
     */
    public function setPagamentoId($pagamento_id)
    {
        if (is_null($pagamento_id)) {
            array_push($this->openAPINullablesSetToNull, 'pagamento_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('pagamento_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['pagamento_id'] = $pagamento_id;

        return $this;
    }

    /**
     * Gets pagamento
     *
     * @return \Amica\Sdk\Model\Pagamento|null
     */
    public function getPagamento()
    {
        return $this->container['pagamento'];
    }

    /**
     * Sets pagamento
     *
     * @param \Amica\Sdk\Model\Pagamento|null $pagamento pagamento
     *
     * @return self
     */
    public function setPagamento($pagamento)
    {
        if (is_null($pagamento)) {
            throw new \InvalidArgumentException('non-nullable pagamento cannot be null');
        }
        $this->container['pagamento'] = $pagamento;

        return $this;
    }

    /**
     * Gets listino_id
     *
     * @return int|null
     */
    public function getListinoId()
    {
        return $this->container['listino_id'];
    }

    /**
     * Sets listino_id
     *
     * @param int|null $listino_id Identificativo univoco di Amica.SchedaCliente.Listino.
     *
     * @return self
     */
    public function setListinoId($listino_id)
    {
        if (is_null($listino_id)) {
            array_push($this->openAPINullablesSetToNull, 'listino_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('listino_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['listino_id'] = $listino_id;

        return $this;
    }

    /**
     * Gets listino
     *
     * @return \Amica\Sdk\Model\Listino|null
     */
    public function getListino()
    {
        return $this->container['listino'];
    }

    /**
     * Sets listino
     *
     * @param \Amica\Sdk\Model\Listino|null $listino listino
     *
     * @return self
     */
    public function setListino($listino)
    {
        if (is_null($listino)) {
            throw new \InvalidArgumentException('non-nullable listino cannot be null');
        }
        $this->container['listino'] = $listino;

        return $this;
    }

    /**
     * Gets categoria_anagrafica_id
     *
     * @return int|null
     */
    public function getCategoriaAnagraficaId()
    {
        return $this->container['categoria_anagrafica_id'];
    }

    /**
     * Sets categoria_anagrafica_id
     *
     * @param int|null $categoria_anagrafica_id Identificativo univoco di Amica.SchedaCliente.CategoriaAnagrafica.
     *
     * @return self
     */
    public function setCategoriaAnagraficaId($categoria_anagrafica_id)
    {
        if (is_null($categoria_anagrafica_id)) {
            array_push($this->openAPINullablesSetToNull, 'categoria_anagrafica_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('categoria_anagrafica_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['categoria_anagrafica_id'] = $categoria_anagrafica_id;

        return $this;
    }

    /**
     * Gets categoria_anagrafica
     *
     * @return \Amica\Sdk\Model\CategoriaAnagrafica|null
     */
    public function getCategoriaAnagrafica()
    {
        return $this->container['categoria_anagrafica'];
    }

    /**
     * Sets categoria_anagrafica
     *
     * @param \Amica\Sdk\Model\CategoriaAnagrafica|null $categoria_anagrafica categoria_anagrafica
     *
     * @return self
     */
    public function setCategoriaAnagrafica($categoria_anagrafica)
    {
        if (is_null($categoria_anagrafica)) {
            throw new \InvalidArgumentException('non-nullable categoria_anagrafica cannot be null');
        }
        $this->container['categoria_anagrafica'] = $categoria_anagrafica;

        return $this;
    }

    /**
     * Gets causale_iva_id
     *
     * @return int|null
     */
    public function getCausaleIvaId()
    {
        return $this->container['causale_iva_id'];
    }

    /**
     * Sets causale_iva_id
     *
     * @param int|null $causale_iva_id Identificativo univoco di Amica.SchedaCliente.CausaleIva.
     *
     * @return self
     */
    public function setCausaleIvaId($causale_iva_id)
    {
        if (is_null($causale_iva_id)) {
            array_push($this->openAPINullablesSetToNull, 'causale_iva_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('causale_iva_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['causale_iva_id'] = $causale_iva_id;

        return $this;
    }

    /**
     * Gets causale_iva
     *
     * @return \Amica\Sdk\Model\CausaleIva|null
     */
    public function getCausaleIva()
    {
        return $this->container['causale_iva'];
    }

    /**
     * Sets causale_iva
     *
     * @param \Amica\Sdk\Model\CausaleIva|null $causale_iva causale_iva
     *
     * @return self
     */
    public function setCausaleIva($causale_iva)
    {
        if (is_null($causale_iva)) {
            throw new \InvalidArgumentException('non-nullable causale_iva cannot be null');
        }
        $this->container['causale_iva'] = $causale_iva;

        return $this;
    }

    /**
     * Gets fido
     *
     * @return float|null
     */
    public function getFido()
    {
        return $this->container['fido'];
    }

    /**
     * Sets fido
     *
     * @param float|null $fido Fido concesso al cliente.
     *
     * @return self
     */
    public function setFido($fido)
    {
        if (is_null($fido)) {
            throw new \InvalidArgumentException('non-nullable fido cannot be null');
        }
        $this->container['fido'] = $fido;

        return $this;
    }

    /**
     * Gets sconto
     *
     * @return float|null
     */
    public function getSconto()
    {
        return $this->container['sconto'];
    }

    /**
     * Sets sconto
     *
     * @param float|null $sconto Sconto riservato al cliente (percentuale).
     *
     * @return self
     */
    public function setSconto($sconto)
    {
        if (is_null($sconto)) {
            throw new \InvalidArgumentException('non-nullable sconto cannot be null');
        }
        $this->container['sconto'] = $sconto;

        return $this;
    }

    /**
     * Gets fatturato_anno_corrente
     *
     * @return float|null
     */
    public function getFatturatoAnnoCorrente()
    {
        return $this->container['fatturato_anno_corrente'];
    }

    /**
     * Sets fatturato_anno_corrente
     *
     * @param float|null $fatturato_anno_corrente Fatturato realizzato nell'anno corrente.
     *
     * @return self
     */
    public function setFatturatoAnnoCorrente($fatturato_anno_corrente)
    {
        if (is_null($fatturato_anno_corrente)) {
            throw new \InvalidArgumentException('non-nullable fatturato_anno_corrente cannot be null');
        }
        $this->container['fatturato_anno_corrente'] = $fatturato_anno_corrente;

        return $this;
    }

    /**
     * Gets numero_fatture_anno_corrente
     *
     * @return int|null
     */
    public function getNumeroFattureAnnoCorrente()
    {
        return $this->container['numero_fatture_anno_corrente'];
    }

    /**
     * Sets numero_fatture_anno_corrente
     *
     * @param int|null $numero_fatture_anno_corrente Numero di fatture emesse nell'anno corrente.
     *
     * @return self
     */
    public function setNumeroFattureAnnoCorrente($numero_fatture_anno_corrente)
    {
        if (is_null($numero_fatture_anno_corrente)) {
            throw new \InvalidArgumentException('non-nullable numero_fatture_anno_corrente cannot be null');
        }
        $this->container['numero_fatture_anno_corrente'] = $numero_fatture_anno_corrente;

        return $this;
    }

    /**
     * Gets insoluti_anno_corrente
     *
     * @return float|null
     */
    public function getInsolutiAnnoCorrente()
    {
        return $this->container['insoluti_anno_corrente'];
    }

    /**
     * Sets insoluti_anno_corrente
     *
     * @param float|null $insoluti_anno_corrente Importo insoluto nell'anno corrente.
     *
     * @return self
     */
    public function setInsolutiAnnoCorrente($insoluti_anno_corrente)
    {
        if (is_null($insoluti_anno_corrente)) {
            throw new \InvalidArgumentException('non-nullable insoluti_anno_corrente cannot be null');
        }
        $this->container['insoluti_anno_corrente'] = $insoluti_anno_corrente;

        return $this;
    }

    /**
     * Gets numero_insoluti_anno_corrente
     *
     * @return int|null
     */
    public function getNumeroInsolutiAnnoCorrente()
    {
        return $this->container['numero_insoluti_anno_corrente'];
    }

    /**
     * Sets numero_insoluti_anno_corrente
     *
     * @param int|null $numero_insoluti_anno_corrente Numero di insoluti nell'anno corrente.
     *
     * @return self
     */
    public function setNumeroInsolutiAnnoCorrente($numero_insoluti_anno_corrente)
    {
        if (is_null($numero_insoluti_anno_corrente)) {
            throw new \InvalidArgumentException('non-nullable numero_insoluti_anno_corrente cannot be null');
        }
        $this->container['numero_insoluti_anno_corrente'] = $numero_insoluti_anno_corrente;

        return $this;
    }

    /**
     * Gets fatturato_anno_precedente
     *
     * @return float|null
     */
    public function getFatturatoAnnoPrecedente()
    {
        return $this->container['fatturato_anno_precedente'];
    }

    /**
     * Sets fatturato_anno_precedente
     *
     * @param float|null $fatturato_anno_precedente Fatturato realizzato nell'anno precedente.
     *
     * @return self
     */
    public function setFatturatoAnnoPrecedente($fatturato_anno_precedente)
    {
        if (is_null($fatturato_anno_precedente)) {
            throw new \InvalidArgumentException('non-nullable fatturato_anno_precedente cannot be null');
        }
        $this->container['fatturato_anno_precedente'] = $fatturato_anno_precedente;

        return $this;
    }

    /**
     * Gets numero_fatture_anno_precedente
     *
     * @return int|null
     */
    public function getNumeroFattureAnnoPrecedente()
    {
        return $this->container['numero_fatture_anno_precedente'];
    }

    /**
     * Sets numero_fatture_anno_precedente
     *
     * @param int|null $numero_fatture_anno_precedente Numero di fatture emesse nell'anno precedente.
     *
     * @return self
     */
    public function setNumeroFattureAnnoPrecedente($numero_fatture_anno_precedente)
    {
        if (is_null($numero_fatture_anno_precedente)) {
            throw new \InvalidArgumentException('non-nullable numero_fatture_anno_precedente cannot be null');
        }
        $this->container['numero_fatture_anno_precedente'] = $numero_fatture_anno_precedente;

        return $this;
    }

    /**
     * Gets insoluti_anno_precedente
     *
     * @return float|null
     */
    public function getInsolutiAnnoPrecedente()
    {
        return $this->container['insoluti_anno_precedente'];
    }

    /**
     * Sets insoluti_anno_precedente
     *
     * @param float|null $insoluti_anno_precedente Importo insoluto nell'anno precedente.
     *
     * @return self
     */
    public function setInsolutiAnnoPrecedente($insoluti_anno_precedente)
    {
        if (is_null($insoluti_anno_precedente)) {
            throw new \InvalidArgumentException('non-nullable insoluti_anno_precedente cannot be null');
        }
        $this->container['insoluti_anno_precedente'] = $insoluti_anno_precedente;

        return $this;
    }

    /**
     * Gets numero_insoluti_anno_precedente
     *
     * @return int|null
     */
    public function getNumeroInsolutiAnnoPrecedente()
    {
        return $this->container['numero_insoluti_anno_precedente'];
    }

    /**
     * Sets numero_insoluti_anno_precedente
     *
     * @param int|null $numero_insoluti_anno_precedente Numero di insoluti verificatisi nell'anno precedente.
     *
     * @return self
     */
    public function setNumeroInsolutiAnnoPrecedente($numero_insoluti_anno_precedente)
    {
        if (is_null($numero_insoluti_anno_precedente)) {
            throw new \InvalidArgumentException('non-nullable numero_insoluti_anno_precedente cannot be null');
        }
        $this->container['numero_insoluti_anno_precedente'] = $numero_insoluti_anno_precedente;

        return $this;
    }

    /**
     * Gets data_ultima_fattura
     *
     * @return \DateTime|null
     */
    public function getDataUltimaFattura()
    {
        return $this->container['data_ultima_fattura'];
    }

    /**
     * Sets data_ultima_fattura
     *
     * @param \DateTime|null $data_ultima_fattura Data dell'ultima fattura.
     *
     * @return self
     */
    public function setDataUltimaFattura($data_ultima_fattura)
    {
        if (is_null($data_ultima_fattura)) {
            array_push($this->openAPINullablesSetToNull, 'data_ultima_fattura');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('data_ultima_fattura', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['data_ultima_fattura'] = $data_ultima_fattura;

        return $this;
    }

    /**
     * Gets data_ultimo_pagamento
     *
     * @return \DateTime|null
     */
    public function getDataUltimoPagamento()
    {
        return $this->container['data_ultimo_pagamento'];
    }

    /**
     * Sets data_ultimo_pagamento
     *
     * @param \DateTime|null $data_ultimo_pagamento Data dell'ultimo pagamento.
     *
     * @return self
     */
    public function setDataUltimoPagamento($data_ultimo_pagamento)
    {
        if (is_null($data_ultimo_pagamento)) {
            array_push($this->openAPINullablesSetToNull, 'data_ultimo_pagamento');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('data_ultimo_pagamento', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['data_ultimo_pagamento'] = $data_ultimo_pagamento;

        return $this;
    }

    /**
     * Gets data_ultimo_insoluto
     *
     * @return \DateTime|null
     */
    public function getDataUltimoInsoluto()
    {
        return $this->container['data_ultimo_insoluto'];
    }

    /**
     * Sets data_ultimo_insoluto
     *
     * @param \DateTime|null $data_ultimo_insoluto Data dell'ultimo insoluto.
     *
     * @return self
     */
    public function setDataUltimoInsoluto($data_ultimo_insoluto)
    {
        if (is_null($data_ultimo_insoluto)) {
            array_push($this->openAPINullablesSetToNull, 'data_ultimo_insoluto');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('data_ultimo_insoluto', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['data_ultimo_insoluto'] = $data_ultimo_insoluto;

        return $this;
    }

    /**
     * Gets conto_raw
     *
     * @return string|null
     */
    public function getContoRaw()
    {
        return $this->container['conto_raw'];
    }

    /**
     * Sets conto_raw
     *
     * @param string|null $conto_raw Numero di conto, in formato grezzo (es. \"  100   20\"). Sono ammessi fino a cinque livelli.
     *
     * @return self
     */
    public function setContoRaw($conto_raw)
    {
        if (is_null($conto_raw)) {
            array_push($this->openAPINullablesSetToNull, 'conto_raw');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('conto_raw', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($conto_raw) && (mb_strlen($conto_raw) > 25)) {
            throw new \InvalidArgumentException('invalid length for $conto_raw when calling SchedaCliente., must be smaller than or equal to 25.');
        }
        if (!is_null($conto_raw) && (mb_strlen($conto_raw) < 0)) {
            throw new \InvalidArgumentException('invalid length for $conto_raw when calling SchedaCliente., must be bigger than or equal to 0.');
        }

        $this->container['conto_raw'] = $conto_raw;

        return $this;
    }

    /**
     * Gets conto
     *
     * @return string|null
     */
    public function getConto()
    {
        return $this->container['conto'];
    }

    /**
     * Sets conto
     *
     * @param string|null $conto Numero di conto, in formato leggibile (es. \"100.20\").
     *
     * @return self
     */
    public function setConto($conto)
    {
        if (is_null($conto)) {
            array_push($this->openAPINullablesSetToNull, 'conto');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('conto', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['conto'] = $conto;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    public function offsetExists($offset): bool
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed|null
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->container[$offset] ?? null;
    }

    /**
     * Sets value based on offset.
     *
     * @param int|null $offset Offset
     * @param mixed    $value  Value to be set
     *
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param integer $offset Offset
     *
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->container[$offset]);
    }

    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     * @link https://www.php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed Returns data which can be serialized by json_encode(), which is a value
     * of any type other than a resource.
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
       return ObjectSerializer::sanitizeForSerialization($this);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode(
            ObjectSerializer::sanitizeForSerialization($this),
            JSON_PRETTY_PRINT
        );
    }

    /**
     * Gets a header-safe presentation of the object
     *
     * @return string
     */
    public function toHeaderValue()
    {
        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}


