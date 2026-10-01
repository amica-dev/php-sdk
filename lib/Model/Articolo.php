<?php
/**
 * Articolo
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
 * L'Amica Cloud API fornisce accesso programmatico a tutti i dati aziendali gestiti da [Amica 20](https://gestionaleamica.com). Supporta il ciclo completo di documenti, fatture, magazzino, contabilità e anagrafiche. Per guide e documentazione completa, visitare [amica.dev](https://amica.dev/).  ## Autenticazione  Tutte le richieste API richiedono un token OAuth 2.0 Bearer valido, da includere nell'header `Authorization`:  ``` Authorization: Bearer <token> ```  ### Registra la tua app  Per integrare l'Amica Cloud API registra la tua applicazione sull'[**App Store di Amica**](https://apps.amica20.it). Al termine della registrazione ottieni una coppia di credenziali OAuth 2.0 — `client_id` e `client_secret` (il secret è mostrato **una sola volta**) — e scegli gli **scope** e il **flusso di autenticazione** desiderati.  ### Scegli il flusso  La scelta dipende da **come** la tua applicazione si autentica:  - C'è un **utente** che può accedere via browser (web app, SPA, app mobile)? → **Authorization Code con PKCE**. - È un **servizio headless** (backend, batch, integrazione server-to-server) di una **singola azienda**, senza un utente che effettua il login? → **Client Credentials**.  #### Authorization Code con PKCE  Consigliato per le app che operano per conto di un utente Amica ed è l'**unico flusso disponibile per le app pubblicate sul marketplace**. Una sola registrazione serve un numero qualsiasi di aziende: ogni utente autorizza la tua app per la propria azienda tramite la pagina di consenso dell'identity provider, e il token riceve automaticamente il contesto di quell'azienda. La tua app riceve un `code` da scambiare con un access token.  ``` GET  {identity-provider}/connect/authorize?response_type=code&client_id=<client_id>&redirect_uri=<redirect_uri>&scope=openid%20read%20write%20documenti&code_challenge=<challenge>&code_challenge_method=S256 POST {identity-provider}/connect/token      grant_type=authorization_code&code=<code>&code_verifier=<verifier>&client_id=<client_id>&client_secret=<client_secret>&redirect_uri=<redirect_uri> ```  #### Client Credentials  Per integrazioni **server-to-server** senza utente interattivo. Disponibile solo per le **app private**, non per quelle pubblicate sul marketplace. Un'app Client Credentials è **legata a una singola azienda** — quella dell'utente che la registra — e il token ne riceve automaticamente il contesto. Per servire un'azienda diversa serve una registrazione separata; se devi distribuire la stessa app a più aziende usa invece **Authorization Code**.  ``` POST {identity-provider}/connect/token Content-Type: application/x-www-form-urlencoded  grant_type=client_credentials&client_id=<client_id>&client_secret=<client_secret>&scope=comune read write documenti ```  > **Importante:** non includere lo scope `openid` (né `profile`/`email`) in questo flusso. `openid` attiva OpenID Connect — richiede un utente che effettua il login e l'emissione di un ID token — cosa che un flusso server-to-server non prevede: la richiesta viene rifiutata con l'errore `Client cannot request OpenID scopes in client credentials flow`. Indica solo gli scope di risorsa (`comune`, `magazzino`, `documenti`, `contabilita`, ed eventualmente `read`/`write`).  In entrambi i casi la risposta contiene l'`access_token` (JWT con `aud: amica-cloud`) da usare nelle chiamate successive finché non scade (`expires_in`), dopodiché va richiesto un nuovo token. Ogni token contiene uno o più **scope** che determinano le aree accessibili (vedi sotto). Dalla [API Reference](https://amica.dev) è possibile incollare un access token per provare gli endpoint direttamente dal browser.  ## Scope di autorizzazione  L'accesso agli endpoint è governato dagli scope presenti nel token OAuth:  | Scope | Descrizione | |-|-| | `comune` | Anagrafiche: contatti, indirizzi, nazioni, valute, modalità di pagamento, ecc. | | `magazzino` | Magazzino: articoli, listini, movimenti di magazzino, giacenze, distinte base. | | `contabilita` | Contabilità: registrazioni contabili, scadenze, castelletti IVA, fatture elettroniche XML. | | `documenti` | Documenti: fatture, note di credito, DDT, ordini, preventivi, proforma ed emissione. |  Una richiesta verso un endpoint non coperto dallo scope del token riceverà una risposta `403 Forbidden`.  ## Company ID  La maggior parte degli endpoint è relativa a una specifica azienda e segue il pattern:  ``` /api/v3/{companyId}/risorsa ```  Il `companyId` è l'identificativo univoco di ciascun database aziendale. Usare l'endpoint `GET /api/v3/config/Azienda` per ottenere l'elenco delle aziende disponibili.  ## Paginazione  Gli endpoint che restituiscono liste supportano la paginazione tramite query parameter:  | Parametro | Descrizione | |-|-| | `Skip` | Numero di elementi da saltare dall'inizio del set di risultati. | | `Take` | Numero di elementi da includere nella risposta. |  La risposta include due header:  | Header | Descrizione | |-|-| | `X-Read-TotalCount` | Numero totale di elementi che soddisfano il filtro corrente. | | `X-Read-Count` | Numero di elementi effettivamente restituiti nel body della risposta. |  ## Ordinamento e filtri  Gli endpoint lista accettano parametri aggiuntivi:  | Parametro | Descrizione | |-|-| | `Order` | Lista di proprietà per l'ordinamento, separate da virgola. Un segno meno (`-`) prima del nome indica ordine discendente. Es. `-RagioneSociale1,Citta`. | | `Filter` | Condizioni di ricerca: lista di espressioni separate da virgola, ciascuna formata da nome campo, operatore, valore ed eventuale connettore logico. Operatori: `$eq`, `$ne`, `$gt`, `$lt`, `$lte`, `$gte`, `$ct` (contiene), `$nct`, `$sw` (inizia con), `$ew` (finisce con). Connettori: `$and` (default, omissibile) e `$or`; le sotto-query si racchiudono tra `[]`. Es. `RagioneSociale1 $ct acme $or, Fido $gt 1000`. | | `SkipTotalCount` | Se `true`, l'header `X-Read-TotalCount` viene omesso dalla risposta: migliori prestazioni a scapito del conteggio totale. Default `false`. |  ## Espansione oggetti correlati  Gli endpoint singoli e lista supportano il parametro `Expand`. Quando fornito, gli oggetti correlati vengono inclusi inline nella risposta anziché restituiti come riferimenti. È possibile specificare espansioni multiple separate da virgola.  ## Gestione errori  L'API usa codici di stato HTTP standard e restituisce [RFC 7807](https://tools.ietf.org/html/rfc7807) Problem Details in caso di errore:  | Codice | Significato | |-|-| | `400` | Bad Request — validazione fallita o parametro obbligatorio mancante. | | `401` | Unauthorized — token mancante o scaduto. | | `403` | Forbidden — accesso negato (vedi sotto le possibili cause). | | `404` | Not Found — la risorsa richiesta non esiste. | | `409` | Conflict — conflitto di concorrenza. | | `422` | Unprocessable Entity — entità non processabile (es. token di concorrenza obsoleto). |  Un `403` può avere tre cause; nelle ultime due il campo `code` della risposta indica quale:  - il token non possiede lo scope richiesto dall'endpoint; - `code: cloud_not_active` — l'account lavora con una connessione locale e la Cloud API non espone i suoi dati. Per attivare il servizio cloud contatta l'assistenza, poi richiedi un nuovo token: lo stato del servizio è letto al rilascio del token; - `code: company_license_not_active` — la licenza dell'azienda indicata nel percorso è scaduta o non è attiva. Le altre aziende con licenza attiva restano accessibili.  Il motivo dell'errore è sempre nel campo `detail`. Ogni risposta di errore riporta inoltre `overridable`: quando vale `true`, l'errore di validazione è *forzabile* e basta ripetere la stessa richiesta aggiungendo `validationOverride=true` alla query string perché il servizio la accetti. È il caso delle anomalie formali su dati reali — la partita IVA di un fornitore estero, per esempio — che il cliente ha comunque bisogno di registrare.  ## Controllo della concorrenza  Le entità contengono una proprietà `ConcurrencyToken`. Quando si aggiorna un'entità tramite `PUT`, il token nel body della richiesta viene confrontato con il valore memorizzato. Se differiscono, viene restituito un `422 Unprocessable Entity`. Usare sempre l'ultimo token ricevuto da una risposta `GET` o `POST`.
 *
 * The version of the OpenAPI document: 3.1.4
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
 * Articolo Class Doc Comment
 *
 * @category Class
 * @package  Amica\Sdk
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class Articolo implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'Articolo';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'id' => 'int',
        'concurrency_token' => 'int',
        'categoria_merceologica_id' => 'int',
        'categoria_merceologica' => '\Amica\Sdk\Model\CategoriaMerceologica',
        'gruppo_merceologico_id' => 'int',
        'gruppo_merceologico' => '\Amica\Sdk\Model\GruppoMerceologico',
        'causale_iva_id' => 'int',
        'causale_iva' => '\Amica\Sdk\Model\CausaleIva',
        'taglia_id' => 'int',
        'taglia' => '\Amica\Sdk\Model\Taglia',
        'marca_id' => 'int',
        'marca' => '\Amica\Sdk\Model\Marca',
        'produttore_id' => 'int',
        'produttore' => '\Amica\Sdk\Model\Produttore',
        'nome' => 'string',
        'codice' => 'string',
        'nome_breve' => 'string',
        'codice_fornitore' => 'string',
        'codice_produttore' => 'string',
        'gestione_articolo' => 'string',
        'contesto_articolo' => 'string',
        'bar_code' => 'string',
        'costo_ultimo' => 'float',
        'costo_medio' => 'float',
        'unit_misura_intera' => 'bool',
        'articolo_composto' => 'bool',
        'stato' => 'bool',
        'data_inserimento' => '\DateTime',
        'unit_misura_movimenti' => 'string',
        'unit_misura_inventario' => 'string',
        'fattore_conversione_unit_misura' => 'float',
        'note' => 'string',
        'media_file' => '\Amica\Sdk\Model\ArticoloMediaFile',
        'conto_raw_vendita' => 'string',
        'conto_raw_acquisto' => 'string',
        'distinta_base' => '\Amica\Sdk\Model\Componente[]'
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
        'categoria_merceologica_id' => 'int32',
        'categoria_merceologica' => null,
        'gruppo_merceologico_id' => 'int32',
        'gruppo_merceologico' => null,
        'causale_iva_id' => 'int32',
        'causale_iva' => null,
        'taglia_id' => 'int32',
        'taglia' => null,
        'marca_id' => 'int32',
        'marca' => null,
        'produttore_id' => 'int32',
        'produttore' => null,
        'nome' => null,
        'codice' => null,
        'nome_breve' => null,
        'codice_fornitore' => null,
        'codice_produttore' => null,
        'gestione_articolo' => null,
        'contesto_articolo' => null,
        'bar_code' => null,
        'costo_ultimo' => 'double',
        'costo_medio' => 'double',
        'unit_misura_intera' => null,
        'articolo_composto' => null,
        'stato' => null,
        'data_inserimento' => 'date-time',
        'unit_misura_movimenti' => null,
        'unit_misura_inventario' => null,
        'fattore_conversione_unit_misura' => 'double',
        'note' => null,
        'media_file' => null,
        'conto_raw_vendita' => null,
        'conto_raw_acquisto' => null,
        'distinta_base' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'id' => false,
        'concurrency_token' => false,
        'categoria_merceologica_id' => true,
        'categoria_merceologica' => false,
        'gruppo_merceologico_id' => true,
        'gruppo_merceologico' => false,
        'causale_iva_id' => true,
        'causale_iva' => false,
        'taglia_id' => true,
        'taglia' => false,
        'marca_id' => true,
        'marca' => false,
        'produttore_id' => true,
        'produttore' => false,
        'nome' => false,
        'codice' => false,
        'nome_breve' => true,
        'codice_fornitore' => true,
        'codice_produttore' => true,
        'gestione_articolo' => false,
        'contesto_articolo' => false,
        'bar_code' => true,
        'costo_ultimo' => false,
        'costo_medio' => false,
        'unit_misura_intera' => false,
        'articolo_composto' => false,
        'stato' => false,
        'data_inserimento' => true,
        'unit_misura_movimenti' => true,
        'unit_misura_inventario' => true,
        'fattore_conversione_unit_misura' => false,
        'note' => true,
        'media_file' => false,
        'conto_raw_vendita' => true,
        'conto_raw_acquisto' => true,
        'distinta_base' => true
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
        'categoria_merceologica_id' => 'categoriaMerceologicaId',
        'categoria_merceologica' => 'categoriaMerceologica',
        'gruppo_merceologico_id' => 'gruppoMerceologicoId',
        'gruppo_merceologico' => 'gruppoMerceologico',
        'causale_iva_id' => 'causaleIvaId',
        'causale_iva' => 'causaleIva',
        'taglia_id' => 'tagliaId',
        'taglia' => 'taglia',
        'marca_id' => 'marcaId',
        'marca' => 'marca',
        'produttore_id' => 'produttoreId',
        'produttore' => 'produttore',
        'nome' => 'nome',
        'codice' => 'codice',
        'nome_breve' => 'nomeBreve',
        'codice_fornitore' => 'codiceFornitore',
        'codice_produttore' => 'codiceProduttore',
        'gestione_articolo' => 'gestioneArticolo',
        'contesto_articolo' => 'contestoArticolo',
        'bar_code' => 'barCode',
        'costo_ultimo' => 'costoUltimo',
        'costo_medio' => 'costoMedio',
        'unit_misura_intera' => 'unitàMisuraIntera',
        'articolo_composto' => 'articoloComposto',
        'stato' => 'stato',
        'data_inserimento' => 'dataInserimento',
        'unit_misura_movimenti' => 'unitàMisuraMovimenti',
        'unit_misura_inventario' => 'unitàMisuraInventario',
        'fattore_conversione_unit_misura' => 'fattoreConversioneUnitàMisura',
        'note' => 'note',
        'media_file' => 'mediaFile',
        'conto_raw_vendita' => 'contoRawVendita',
        'conto_raw_acquisto' => 'contoRawAcquisto',
        'distinta_base' => 'distintaBase'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'id' => 'setId',
        'concurrency_token' => 'setConcurrencyToken',
        'categoria_merceologica_id' => 'setCategoriaMerceologicaId',
        'categoria_merceologica' => 'setCategoriaMerceologica',
        'gruppo_merceologico_id' => 'setGruppoMerceologicoId',
        'gruppo_merceologico' => 'setGruppoMerceologico',
        'causale_iva_id' => 'setCausaleIvaId',
        'causale_iva' => 'setCausaleIva',
        'taglia_id' => 'setTagliaId',
        'taglia' => 'setTaglia',
        'marca_id' => 'setMarcaId',
        'marca' => 'setMarca',
        'produttore_id' => 'setProduttoreId',
        'produttore' => 'setProduttore',
        'nome' => 'setNome',
        'codice' => 'setCodice',
        'nome_breve' => 'setNomeBreve',
        'codice_fornitore' => 'setCodiceFornitore',
        'codice_produttore' => 'setCodiceProduttore',
        'gestione_articolo' => 'setGestioneArticolo',
        'contesto_articolo' => 'setContestoArticolo',
        'bar_code' => 'setBarCode',
        'costo_ultimo' => 'setCostoUltimo',
        'costo_medio' => 'setCostoMedio',
        'unit_misura_intera' => 'setUnitMisuraIntera',
        'articolo_composto' => 'setArticoloComposto',
        'stato' => 'setStato',
        'data_inserimento' => 'setDataInserimento',
        'unit_misura_movimenti' => 'setUnitMisuraMovimenti',
        'unit_misura_inventario' => 'setUnitMisuraInventario',
        'fattore_conversione_unit_misura' => 'setFattoreConversioneUnitMisura',
        'note' => 'setNote',
        'media_file' => 'setMediaFile',
        'conto_raw_vendita' => 'setContoRawVendita',
        'conto_raw_acquisto' => 'setContoRawAcquisto',
        'distinta_base' => 'setDistintaBase'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'id' => 'getId',
        'concurrency_token' => 'getConcurrencyToken',
        'categoria_merceologica_id' => 'getCategoriaMerceologicaId',
        'categoria_merceologica' => 'getCategoriaMerceologica',
        'gruppo_merceologico_id' => 'getGruppoMerceologicoId',
        'gruppo_merceologico' => 'getGruppoMerceologico',
        'causale_iva_id' => 'getCausaleIvaId',
        'causale_iva' => 'getCausaleIva',
        'taglia_id' => 'getTagliaId',
        'taglia' => 'getTaglia',
        'marca_id' => 'getMarcaId',
        'marca' => 'getMarca',
        'produttore_id' => 'getProduttoreId',
        'produttore' => 'getProduttore',
        'nome' => 'getNome',
        'codice' => 'getCodice',
        'nome_breve' => 'getNomeBreve',
        'codice_fornitore' => 'getCodiceFornitore',
        'codice_produttore' => 'getCodiceProduttore',
        'gestione_articolo' => 'getGestioneArticolo',
        'contesto_articolo' => 'getContestoArticolo',
        'bar_code' => 'getBarCode',
        'costo_ultimo' => 'getCostoUltimo',
        'costo_medio' => 'getCostoMedio',
        'unit_misura_intera' => 'getUnitMisuraIntera',
        'articolo_composto' => 'getArticoloComposto',
        'stato' => 'getStato',
        'data_inserimento' => 'getDataInserimento',
        'unit_misura_movimenti' => 'getUnitMisuraMovimenti',
        'unit_misura_inventario' => 'getUnitMisuraInventario',
        'fattore_conversione_unit_misura' => 'getFattoreConversioneUnitMisura',
        'note' => 'getNote',
        'media_file' => 'getMediaFile',
        'conto_raw_vendita' => 'getContoRawVendita',
        'conto_raw_acquisto' => 'getContoRawAcquisto',
        'distinta_base' => 'getDistintaBase'
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

    public const GESTIONE_ARTICOLO_STANDARD = 'Standard';
    public const GESTIONE_ARTICOLO_TAGLIE_COLORI = 'TaglieColori';
    public const GESTIONE_ARTICOLO_NUMERI_SERIALI = 'NumeriSeriali';
    public const GESTIONE_ARTICOLO_LOTTI = 'Lotti';
    public const CONTESTO_ARTICOLO_SEMPRE = 'Sempre';
    public const CONTESTO_ARTICOLO_VENDITE = 'Vendite';
    public const CONTESTO_ARTICOLO_ACQUISTI = 'Acquisti';
    public const CONTESTO_ARTICOLO_MAI = 'Mai';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getGestioneArticoloAllowableValues()
    {
        return [
            self::GESTIONE_ARTICOLO_STANDARD,
            self::GESTIONE_ARTICOLO_TAGLIE_COLORI,
            self::GESTIONE_ARTICOLO_NUMERI_SERIALI,
            self::GESTIONE_ARTICOLO_LOTTI,
        ];
    }

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getContestoArticoloAllowableValues()
    {
        return [
            self::CONTESTO_ARTICOLO_SEMPRE,
            self::CONTESTO_ARTICOLO_VENDITE,
            self::CONTESTO_ARTICOLO_ACQUISTI,
            self::CONTESTO_ARTICOLO_MAI,
        ];
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
        $this->setIfExists('categoria_merceologica_id', $data ?? [], null);
        $this->setIfExists('categoria_merceologica', $data ?? [], null);
        $this->setIfExists('gruppo_merceologico_id', $data ?? [], null);
        $this->setIfExists('gruppo_merceologico', $data ?? [], null);
        $this->setIfExists('causale_iva_id', $data ?? [], null);
        $this->setIfExists('causale_iva', $data ?? [], null);
        $this->setIfExists('taglia_id', $data ?? [], null);
        $this->setIfExists('taglia', $data ?? [], null);
        $this->setIfExists('marca_id', $data ?? [], null);
        $this->setIfExists('marca', $data ?? [], null);
        $this->setIfExists('produttore_id', $data ?? [], null);
        $this->setIfExists('produttore', $data ?? [], null);
        $this->setIfExists('nome', $data ?? [], null);
        $this->setIfExists('codice', $data ?? [], null);
        $this->setIfExists('nome_breve', $data ?? [], null);
        $this->setIfExists('codice_fornitore', $data ?? [], null);
        $this->setIfExists('codice_produttore', $data ?? [], null);
        $this->setIfExists('gestione_articolo', $data ?? [], null);
        $this->setIfExists('contesto_articolo', $data ?? [], null);
        $this->setIfExists('bar_code', $data ?? [], null);
        $this->setIfExists('costo_ultimo', $data ?? [], null);
        $this->setIfExists('costo_medio', $data ?? [], null);
        $this->setIfExists('unit_misura_intera', $data ?? [], null);
        $this->setIfExists('articolo_composto', $data ?? [], null);
        $this->setIfExists('stato', $data ?? [], null);
        $this->setIfExists('data_inserimento', $data ?? [], null);
        $this->setIfExists('unit_misura_movimenti', $data ?? [], null);
        $this->setIfExists('unit_misura_inventario', $data ?? [], null);
        $this->setIfExists('fattore_conversione_unit_misura', $data ?? [], null);
        $this->setIfExists('note', $data ?? [], null);
        $this->setIfExists('media_file', $data ?? [], null);
        $this->setIfExists('conto_raw_vendita', $data ?? [], null);
        $this->setIfExists('conto_raw_acquisto', $data ?? [], null);
        $this->setIfExists('distinta_base', $data ?? [], null);
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

        if ($this->container['nome'] === null) {
            $invalidProperties[] = "'nome' can't be null";
        }
        if ((mb_strlen($this->container['nome']) > 254)) {
            $invalidProperties[] = "invalid value for 'nome', the character length must be smaller than or equal to 254.";
        }

        if ((mb_strlen($this->container['nome']) < 0)) {
            $invalidProperties[] = "invalid value for 'nome', the character length must be bigger than or equal to 0.";
        }

        if ($this->container['codice'] === null) {
            $invalidProperties[] = "'codice' can't be null";
        }
        if ((mb_strlen($this->container['codice']) > 30)) {
            $invalidProperties[] = "invalid value for 'codice', the character length must be smaller than or equal to 30.";
        }

        if ((mb_strlen($this->container['codice']) < 0)) {
            $invalidProperties[] = "invalid value for 'codice', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['nome_breve']) && (mb_strlen($this->container['nome_breve']) > 25)) {
            $invalidProperties[] = "invalid value for 'nome_breve', the character length must be smaller than or equal to 25.";
        }

        if (!is_null($this->container['nome_breve']) && (mb_strlen($this->container['nome_breve']) < 0)) {
            $invalidProperties[] = "invalid value for 'nome_breve', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['codice_fornitore']) && (mb_strlen($this->container['codice_fornitore']) > 30)) {
            $invalidProperties[] = "invalid value for 'codice_fornitore', the character length must be smaller than or equal to 30.";
        }

        if (!is_null($this->container['codice_fornitore']) && (mb_strlen($this->container['codice_fornitore']) < 0)) {
            $invalidProperties[] = "invalid value for 'codice_fornitore', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['codice_produttore']) && (mb_strlen($this->container['codice_produttore']) > 30)) {
            $invalidProperties[] = "invalid value for 'codice_produttore', the character length must be smaller than or equal to 30.";
        }

        if (!is_null($this->container['codice_produttore']) && (mb_strlen($this->container['codice_produttore']) < 0)) {
            $invalidProperties[] = "invalid value for 'codice_produttore', the character length must be bigger than or equal to 0.";
        }

        $allowedValues = $this->getGestioneArticoloAllowableValues();
        if (!is_null($this->container['gestione_articolo']) && !in_array($this->container['gestione_articolo'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'gestione_articolo', must be one of '%s'",
                $this->container['gestione_articolo'],
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getContestoArticoloAllowableValues();
        if (!is_null($this->container['contesto_articolo']) && !in_array($this->container['contesto_articolo'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'contesto_articolo', must be one of '%s'",
                $this->container['contesto_articolo'],
                implode("', '", $allowedValues)
            );
        }

        if (!is_null($this->container['bar_code']) && (mb_strlen($this->container['bar_code']) > 128)) {
            $invalidProperties[] = "invalid value for 'bar_code', the character length must be smaller than or equal to 128.";
        }

        if (!is_null($this->container['bar_code']) && (mb_strlen($this->container['bar_code']) < 0)) {
            $invalidProperties[] = "invalid value for 'bar_code', the character length must be bigger than or equal to 0.";
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
     * @param int|null $id id
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
     * @param int|null $concurrency_token concurrency_token
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
     * Gets categoria_merceologica_id
     *
     * @return int|null
     */
    public function getCategoriaMerceologicaId()
    {
        return $this->container['categoria_merceologica_id'];
    }

    /**
     * Sets categoria_merceologica_id
     *
     * @param int|null $categoria_merceologica_id categoria_merceologica_id
     *
     * @return self
     */
    public function setCategoriaMerceologicaId($categoria_merceologica_id)
    {
        if (is_null($categoria_merceologica_id)) {
            array_push($this->openAPINullablesSetToNull, 'categoria_merceologica_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('categoria_merceologica_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['categoria_merceologica_id'] = $categoria_merceologica_id;

        return $this;
    }

    /**
     * Gets categoria_merceologica
     *
     * @return \Amica\Sdk\Model\CategoriaMerceologica|null
     */
    public function getCategoriaMerceologica()
    {
        return $this->container['categoria_merceologica'];
    }

    /**
     * Sets categoria_merceologica
     *
     * @param \Amica\Sdk\Model\CategoriaMerceologica|null $categoria_merceologica categoria_merceologica
     *
     * @return self
     */
    public function setCategoriaMerceologica($categoria_merceologica)
    {
        if (is_null($categoria_merceologica)) {
            throw new \InvalidArgumentException('non-nullable categoria_merceologica cannot be null');
        }
        $this->container['categoria_merceologica'] = $categoria_merceologica;

        return $this;
    }

    /**
     * Gets gruppo_merceologico_id
     *
     * @return int|null
     */
    public function getGruppoMerceologicoId()
    {
        return $this->container['gruppo_merceologico_id'];
    }

    /**
     * Sets gruppo_merceologico_id
     *
     * @param int|null $gruppo_merceologico_id gruppo_merceologico_id
     *
     * @return self
     */
    public function setGruppoMerceologicoId($gruppo_merceologico_id)
    {
        if (is_null($gruppo_merceologico_id)) {
            array_push($this->openAPINullablesSetToNull, 'gruppo_merceologico_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('gruppo_merceologico_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['gruppo_merceologico_id'] = $gruppo_merceologico_id;

        return $this;
    }

    /**
     * Gets gruppo_merceologico
     *
     * @return \Amica\Sdk\Model\GruppoMerceologico|null
     */
    public function getGruppoMerceologico()
    {
        return $this->container['gruppo_merceologico'];
    }

    /**
     * Sets gruppo_merceologico
     *
     * @param \Amica\Sdk\Model\GruppoMerceologico|null $gruppo_merceologico gruppo_merceologico
     *
     * @return self
     */
    public function setGruppoMerceologico($gruppo_merceologico)
    {
        if (is_null($gruppo_merceologico)) {
            throw new \InvalidArgumentException('non-nullable gruppo_merceologico cannot be null');
        }
        $this->container['gruppo_merceologico'] = $gruppo_merceologico;

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
     * @param int|null $causale_iva_id causale_iva_id
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
     * Gets taglia_id
     *
     * @return int|null
     */
    public function getTagliaId()
    {
        return $this->container['taglia_id'];
    }

    /**
     * Sets taglia_id
     *
     * @param int|null $taglia_id taglia_id
     *
     * @return self
     */
    public function setTagliaId($taglia_id)
    {
        if (is_null($taglia_id)) {
            array_push($this->openAPINullablesSetToNull, 'taglia_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('taglia_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['taglia_id'] = $taglia_id;

        return $this;
    }

    /**
     * Gets taglia
     *
     * @return \Amica\Sdk\Model\Taglia|null
     */
    public function getTaglia()
    {
        return $this->container['taglia'];
    }

    /**
     * Sets taglia
     *
     * @param \Amica\Sdk\Model\Taglia|null $taglia taglia
     *
     * @return self
     */
    public function setTaglia($taglia)
    {
        if (is_null($taglia)) {
            throw new \InvalidArgumentException('non-nullable taglia cannot be null');
        }
        $this->container['taglia'] = $taglia;

        return $this;
    }

    /**
     * Gets marca_id
     *
     * @return int|null
     */
    public function getMarcaId()
    {
        return $this->container['marca_id'];
    }

    /**
     * Sets marca_id
     *
     * @param int|null $marca_id marca_id
     *
     * @return self
     */
    public function setMarcaId($marca_id)
    {
        if (is_null($marca_id)) {
            array_push($this->openAPINullablesSetToNull, 'marca_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('marca_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['marca_id'] = $marca_id;

        return $this;
    }

    /**
     * Gets marca
     *
     * @return \Amica\Sdk\Model\Marca|null
     */
    public function getMarca()
    {
        return $this->container['marca'];
    }

    /**
     * Sets marca
     *
     * @param \Amica\Sdk\Model\Marca|null $marca marca
     *
     * @return self
     */
    public function setMarca($marca)
    {
        if (is_null($marca)) {
            throw new \InvalidArgumentException('non-nullable marca cannot be null');
        }
        $this->container['marca'] = $marca;

        return $this;
    }

    /**
     * Gets produttore_id
     *
     * @return int|null
     */
    public function getProduttoreId()
    {
        return $this->container['produttore_id'];
    }

    /**
     * Sets produttore_id
     *
     * @param int|null $produttore_id produttore_id
     *
     * @return self
     */
    public function setProduttoreId($produttore_id)
    {
        if (is_null($produttore_id)) {
            array_push($this->openAPINullablesSetToNull, 'produttore_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('produttore_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['produttore_id'] = $produttore_id;

        return $this;
    }

    /**
     * Gets produttore
     *
     * @return \Amica\Sdk\Model\Produttore|null
     */
    public function getProduttore()
    {
        return $this->container['produttore'];
    }

    /**
     * Sets produttore
     *
     * @param \Amica\Sdk\Model\Produttore|null $produttore produttore
     *
     * @return self
     */
    public function setProduttore($produttore)
    {
        if (is_null($produttore)) {
            throw new \InvalidArgumentException('non-nullable produttore cannot be null');
        }
        $this->container['produttore'] = $produttore;

        return $this;
    }

    /**
     * Gets nome
     *
     * @return string
     */
    public function getNome()
    {
        return $this->container['nome'];
    }

    /**
     * Sets nome
     *
     * @param string $nome nome
     *
     * @return self
     */
    public function setNome($nome)
    {
        if (is_null($nome)) {
            throw new \InvalidArgumentException('non-nullable nome cannot be null');
        }
        if ((mb_strlen($nome) > 254)) {
            throw new \InvalidArgumentException('invalid length for $nome when calling Articolo., must be smaller than or equal to 254.');
        }
        if ((mb_strlen($nome) < 0)) {
            throw new \InvalidArgumentException('invalid length for $nome when calling Articolo., must be bigger than or equal to 0.');
        }

        $this->container['nome'] = $nome;

        return $this;
    }

    /**
     * Gets codice
     *
     * @return string
     */
    public function getCodice()
    {
        return $this->container['codice'];
    }

    /**
     * Sets codice
     *
     * @param string $codice codice
     *
     * @return self
     */
    public function setCodice($codice)
    {
        if (is_null($codice)) {
            throw new \InvalidArgumentException('non-nullable codice cannot be null');
        }
        if ((mb_strlen($codice) > 30)) {
            throw new \InvalidArgumentException('invalid length for $codice when calling Articolo., must be smaller than or equal to 30.');
        }
        if ((mb_strlen($codice) < 0)) {
            throw new \InvalidArgumentException('invalid length for $codice when calling Articolo., must be bigger than or equal to 0.');
        }

        $this->container['codice'] = $codice;

        return $this;
    }

    /**
     * Gets nome_breve
     *
     * @return string|null
     */
    public function getNomeBreve()
    {
        return $this->container['nome_breve'];
    }

    /**
     * Sets nome_breve
     *
     * @param string|null $nome_breve nome_breve
     *
     * @return self
     */
    public function setNomeBreve($nome_breve)
    {
        if (is_null($nome_breve)) {
            array_push($this->openAPINullablesSetToNull, 'nome_breve');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('nome_breve', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($nome_breve) && (mb_strlen($nome_breve) > 25)) {
            throw new \InvalidArgumentException('invalid length for $nome_breve when calling Articolo., must be smaller than or equal to 25.');
        }
        if (!is_null($nome_breve) && (mb_strlen($nome_breve) < 0)) {
            throw new \InvalidArgumentException('invalid length for $nome_breve when calling Articolo., must be bigger than or equal to 0.');
        }

        $this->container['nome_breve'] = $nome_breve;

        return $this;
    }

    /**
     * Gets codice_fornitore
     *
     * @return string|null
     */
    public function getCodiceFornitore()
    {
        return $this->container['codice_fornitore'];
    }

    /**
     * Sets codice_fornitore
     *
     * @param string|null $codice_fornitore codice_fornitore
     *
     * @return self
     */
    public function setCodiceFornitore($codice_fornitore)
    {
        if (is_null($codice_fornitore)) {
            array_push($this->openAPINullablesSetToNull, 'codice_fornitore');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('codice_fornitore', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($codice_fornitore) && (mb_strlen($codice_fornitore) > 30)) {
            throw new \InvalidArgumentException('invalid length for $codice_fornitore when calling Articolo., must be smaller than or equal to 30.');
        }
        if (!is_null($codice_fornitore) && (mb_strlen($codice_fornitore) < 0)) {
            throw new \InvalidArgumentException('invalid length for $codice_fornitore when calling Articolo., must be bigger than or equal to 0.');
        }

        $this->container['codice_fornitore'] = $codice_fornitore;

        return $this;
    }

    /**
     * Gets codice_produttore
     *
     * @return string|null
     */
    public function getCodiceProduttore()
    {
        return $this->container['codice_produttore'];
    }

    /**
     * Sets codice_produttore
     *
     * @param string|null $codice_produttore codice_produttore
     *
     * @return self
     */
    public function setCodiceProduttore($codice_produttore)
    {
        if (is_null($codice_produttore)) {
            array_push($this->openAPINullablesSetToNull, 'codice_produttore');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('codice_produttore', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($codice_produttore) && (mb_strlen($codice_produttore) > 30)) {
            throw new \InvalidArgumentException('invalid length for $codice_produttore when calling Articolo., must be smaller than or equal to 30.');
        }
        if (!is_null($codice_produttore) && (mb_strlen($codice_produttore) < 0)) {
            throw new \InvalidArgumentException('invalid length for $codice_produttore when calling Articolo., must be bigger than or equal to 0.');
        }

        $this->container['codice_produttore'] = $codice_produttore;

        return $this;
    }

    /**
     * Gets gestione_articolo
     *
     * @return string|null
     */
    public function getGestioneArticolo()
    {
        return $this->container['gestione_articolo'];
    }

    /**
     * Sets gestione_articolo
     *
     * @param string|null $gestione_articolo gestione_articolo
     *
     * @return self
     */
    public function setGestioneArticolo($gestione_articolo)
    {
        if (is_null($gestione_articolo)) {
            throw new \InvalidArgumentException('non-nullable gestione_articolo cannot be null');
        }
        $allowedValues = $this->getGestioneArticoloAllowableValues();
        if (!in_array($gestione_articolo, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'gestione_articolo', must be one of '%s'",
                    $gestione_articolo,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['gestione_articolo'] = $gestione_articolo;

        return $this;
    }

    /**
     * Gets contesto_articolo
     *
     * @return string|null
     */
    public function getContestoArticolo()
    {
        return $this->container['contesto_articolo'];
    }

    /**
     * Sets contesto_articolo
     *
     * @param string|null $contesto_articolo contesto_articolo
     *
     * @return self
     */
    public function setContestoArticolo($contesto_articolo)
    {
        if (is_null($contesto_articolo)) {
            throw new \InvalidArgumentException('non-nullable contesto_articolo cannot be null');
        }
        $allowedValues = $this->getContestoArticoloAllowableValues();
        if (!in_array($contesto_articolo, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'contesto_articolo', must be one of '%s'",
                    $contesto_articolo,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['contesto_articolo'] = $contesto_articolo;

        return $this;
    }

    /**
     * Gets bar_code
     *
     * @return string|null
     */
    public function getBarCode()
    {
        return $this->container['bar_code'];
    }

    /**
     * Sets bar_code
     *
     * @param string|null $bar_code bar_code
     *
     * @return self
     */
    public function setBarCode($bar_code)
    {
        if (is_null($bar_code)) {
            array_push($this->openAPINullablesSetToNull, 'bar_code');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('bar_code', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($bar_code) && (mb_strlen($bar_code) > 128)) {
            throw new \InvalidArgumentException('invalid length for $bar_code when calling Articolo., must be smaller than or equal to 128.');
        }
        if (!is_null($bar_code) && (mb_strlen($bar_code) < 0)) {
            throw new \InvalidArgumentException('invalid length for $bar_code when calling Articolo., must be bigger than or equal to 0.');
        }

        $this->container['bar_code'] = $bar_code;

        return $this;
    }

    /**
     * Gets costo_ultimo
     *
     * @return float|null
     */
    public function getCostoUltimo()
    {
        return $this->container['costo_ultimo'];
    }

    /**
     * Sets costo_ultimo
     *
     * @param float|null $costo_ultimo costo_ultimo
     *
     * @return self
     */
    public function setCostoUltimo($costo_ultimo)
    {
        if (is_null($costo_ultimo)) {
            throw new \InvalidArgumentException('non-nullable costo_ultimo cannot be null');
        }
        $this->container['costo_ultimo'] = $costo_ultimo;

        return $this;
    }

    /**
     * Gets costo_medio
     *
     * @return float|null
     */
    public function getCostoMedio()
    {
        return $this->container['costo_medio'];
    }

    /**
     * Sets costo_medio
     *
     * @param float|null $costo_medio costo_medio
     *
     * @return self
     */
    public function setCostoMedio($costo_medio)
    {
        if (is_null($costo_medio)) {
            throw new \InvalidArgumentException('non-nullable costo_medio cannot be null');
        }
        $this->container['costo_medio'] = $costo_medio;

        return $this;
    }

    /**
     * Gets unit_misura_intera
     *
     * @return bool|null
     */
    public function getUnitMisuraIntera()
    {
        return $this->container['unit_misura_intera'];
    }

    /**
     * Sets unit_misura_intera
     *
     * @param bool|null $unit_misura_intera unit_misura_intera
     *
     * @return self
     */
    public function setUnitMisuraIntera($unit_misura_intera)
    {
        if (is_null($unit_misura_intera)) {
            throw new \InvalidArgumentException('non-nullable unit_misura_intera cannot be null');
        }
        $this->container['unit_misura_intera'] = $unit_misura_intera;

        return $this;
    }

    /**
     * Gets articolo_composto
     *
     * @return bool|null
     */
    public function getArticoloComposto()
    {
        return $this->container['articolo_composto'];
    }

    /**
     * Sets articolo_composto
     *
     * @param bool|null $articolo_composto articolo_composto
     *
     * @return self
     */
    public function setArticoloComposto($articolo_composto)
    {
        if (is_null($articolo_composto)) {
            throw new \InvalidArgumentException('non-nullable articolo_composto cannot be null');
        }
        $this->container['articolo_composto'] = $articolo_composto;

        return $this;
    }

    /**
     * Gets stato
     *
     * @return bool|null
     */
    public function getStato()
    {
        return $this->container['stato'];
    }

    /**
     * Sets stato
     *
     * @param bool|null $stato stato
     *
     * @return self
     */
    public function setStato($stato)
    {
        if (is_null($stato)) {
            throw new \InvalidArgumentException('non-nullable stato cannot be null');
        }
        $this->container['stato'] = $stato;

        return $this;
    }

    /**
     * Gets data_inserimento
     *
     * @return \DateTime|null
     */
    public function getDataInserimento()
    {
        return $this->container['data_inserimento'];
    }

    /**
     * Sets data_inserimento
     *
     * @param \DateTime|null $data_inserimento data_inserimento
     *
     * @return self
     */
    public function setDataInserimento($data_inserimento)
    {
        if (is_null($data_inserimento)) {
            array_push($this->openAPINullablesSetToNull, 'data_inserimento');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('data_inserimento', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['data_inserimento'] = $data_inserimento;

        return $this;
    }

    /**
     * Gets unit_misura_movimenti
     *
     * @return string|null
     */
    public function getUnitMisuraMovimenti()
    {
        return $this->container['unit_misura_movimenti'];
    }

    /**
     * Sets unit_misura_movimenti
     *
     * @param string|null $unit_misura_movimenti unit_misura_movimenti
     *
     * @return self
     */
    public function setUnitMisuraMovimenti($unit_misura_movimenti)
    {
        if (is_null($unit_misura_movimenti)) {
            array_push($this->openAPINullablesSetToNull, 'unit_misura_movimenti');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('unit_misura_movimenti', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['unit_misura_movimenti'] = $unit_misura_movimenti;

        return $this;
    }

    /**
     * Gets unit_misura_inventario
     *
     * @return string|null
     */
    public function getUnitMisuraInventario()
    {
        return $this->container['unit_misura_inventario'];
    }

    /**
     * Sets unit_misura_inventario
     *
     * @param string|null $unit_misura_inventario unit_misura_inventario
     *
     * @return self
     */
    public function setUnitMisuraInventario($unit_misura_inventario)
    {
        if (is_null($unit_misura_inventario)) {
            array_push($this->openAPINullablesSetToNull, 'unit_misura_inventario');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('unit_misura_inventario', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['unit_misura_inventario'] = $unit_misura_inventario;

        return $this;
    }

    /**
     * Gets fattore_conversione_unit_misura
     *
     * @return float|null
     */
    public function getFattoreConversioneUnitMisura()
    {
        return $this->container['fattore_conversione_unit_misura'];
    }

    /**
     * Sets fattore_conversione_unit_misura
     *
     * @param float|null $fattore_conversione_unit_misura fattore_conversione_unit_misura
     *
     * @return self
     */
    public function setFattoreConversioneUnitMisura($fattore_conversione_unit_misura)
    {
        if (is_null($fattore_conversione_unit_misura)) {
            throw new \InvalidArgumentException('non-nullable fattore_conversione_unit_misura cannot be null');
        }
        $this->container['fattore_conversione_unit_misura'] = $fattore_conversione_unit_misura;

        return $this;
    }

    /**
     * Gets note
     *
     * @return string|null
     */
    public function getNote()
    {
        return $this->container['note'];
    }

    /**
     * Sets note
     *
     * @param string|null $note note
     *
     * @return self
     */
    public function setNote($note)
    {
        if (is_null($note)) {
            array_push($this->openAPINullablesSetToNull, 'note');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('note', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['note'] = $note;

        return $this;
    }

    /**
     * Gets media_file
     *
     * @return \Amica\Sdk\Model\ArticoloMediaFile|null
     */
    public function getMediaFile()
    {
        return $this->container['media_file'];
    }

    /**
     * Sets media_file
     *
     * @param \Amica\Sdk\Model\ArticoloMediaFile|null $media_file media_file
     *
     * @return self
     */
    public function setMediaFile($media_file)
    {
        if (is_null($media_file)) {
            throw new \InvalidArgumentException('non-nullable media_file cannot be null');
        }
        $this->container['media_file'] = $media_file;

        return $this;
    }

    /**
     * Gets conto_raw_vendita
     *
     * @return string|null
     */
    public function getContoRawVendita()
    {
        return $this->container['conto_raw_vendita'];
    }

    /**
     * Sets conto_raw_vendita
     *
     * @param string|null $conto_raw_vendita conto_raw_vendita
     *
     * @return self
     */
    public function setContoRawVendita($conto_raw_vendita)
    {
        if (is_null($conto_raw_vendita)) {
            array_push($this->openAPINullablesSetToNull, 'conto_raw_vendita');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('conto_raw_vendita', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['conto_raw_vendita'] = $conto_raw_vendita;

        return $this;
    }

    /**
     * Gets conto_raw_acquisto
     *
     * @return string|null
     */
    public function getContoRawAcquisto()
    {
        return $this->container['conto_raw_acquisto'];
    }

    /**
     * Sets conto_raw_acquisto
     *
     * @param string|null $conto_raw_acquisto conto_raw_acquisto
     *
     * @return self
     */
    public function setContoRawAcquisto($conto_raw_acquisto)
    {
        if (is_null($conto_raw_acquisto)) {
            array_push($this->openAPINullablesSetToNull, 'conto_raw_acquisto');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('conto_raw_acquisto', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['conto_raw_acquisto'] = $conto_raw_acquisto;

        return $this;
    }

    /**
     * Gets distinta_base
     *
     * @return \Amica\Sdk\Model\Componente[]|null
     */
    public function getDistintaBase()
    {
        return $this->container['distinta_base'];
    }

    /**
     * Sets distinta_base
     *
     * @param \Amica\Sdk\Model\Componente[]|null $distinta_base distinta_base
     *
     * @return self
     */
    public function setDistintaBase($distinta_base)
    {
        if (is_null($distinta_base)) {
            array_push($this->openAPINullablesSetToNull, 'distinta_base');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('distinta_base', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['distinta_base'] = $distinta_base;

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


