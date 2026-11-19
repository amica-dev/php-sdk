<?php
/**
 * RegistrazioneContabileRiga
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
 * The version of the OpenAPI document: 3.3.6
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
 * RegistrazioneContabileRiga Class Doc Comment
 *
 * @category Class
 * @description Riga di registrazione contabile.
 * @package  Amica\Sdk
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class RegistrazioneContabileRiga implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'RegistrazioneContabileRiga';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'id' => 'int',
        'concurrency_token' => 'int',
        'registrazione_contabile_id' => 'int',
        'scadenza_id' => 'int',
        'causale_contabile_id' => 'int',
        'emissione_id' => 'int',
        'tipo_conto' => 'string',
        'sezione_conto' => 'string',
        'partita_anno' => 'int',
        'partita_numero' => 'int',
        'conto' => 'string',
        'conto_raw' => 'string',
        'descrizione_conto' => 'string',
        'dare' => 'float',
        'avere' => 'float',
        'note' => 'string'
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
        'registrazione_contabile_id' => 'int32',
        'scadenza_id' => 'int32',
        'causale_contabile_id' => 'int32',
        'emissione_id' => 'int32',
        'tipo_conto' => null,
        'sezione_conto' => null,
        'partita_anno' => 'int32',
        'partita_numero' => 'int32',
        'conto' => null,
        'conto_raw' => null,
        'descrizione_conto' => null,
        'dare' => 'double',
        'avere' => 'double',
        'note' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'id' => false,
        'concurrency_token' => false,
        'registrazione_contabile_id' => false,
        'scadenza_id' => true,
        'causale_contabile_id' => true,
        'emissione_id' => true,
        'tipo_conto' => false,
        'sezione_conto' => false,
        'partita_anno' => false,
        'partita_numero' => false,
        'conto' => true,
        'conto_raw' => true,
        'descrizione_conto' => true,
        'dare' => false,
        'avere' => false,
        'note' => true
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
        'registrazione_contabile_id' => 'registrazioneContabileId',
        'scadenza_id' => 'scadenzaId',
        'causale_contabile_id' => 'causaleContabileId',
        'emissione_id' => 'emissioneId',
        'tipo_conto' => 'tipoConto',
        'sezione_conto' => 'sezioneConto',
        'partita_anno' => 'partitaAnno',
        'partita_numero' => 'partitaNumero',
        'conto' => 'conto',
        'conto_raw' => 'contoRaw',
        'descrizione_conto' => 'descrizioneConto',
        'dare' => 'dare',
        'avere' => 'avere',
        'note' => 'note'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'id' => 'setId',
        'concurrency_token' => 'setConcurrencyToken',
        'registrazione_contabile_id' => 'setRegistrazioneContabileId',
        'scadenza_id' => 'setScadenzaId',
        'causale_contabile_id' => 'setCausaleContabileId',
        'emissione_id' => 'setEmissioneId',
        'tipo_conto' => 'setTipoConto',
        'sezione_conto' => 'setSezioneConto',
        'partita_anno' => 'setPartitaAnno',
        'partita_numero' => 'setPartitaNumero',
        'conto' => 'setConto',
        'conto_raw' => 'setContoRaw',
        'descrizione_conto' => 'setDescrizioneConto',
        'dare' => 'setDare',
        'avere' => 'setAvere',
        'note' => 'setNote'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'id' => 'getId',
        'concurrency_token' => 'getConcurrencyToken',
        'registrazione_contabile_id' => 'getRegistrazioneContabileId',
        'scadenza_id' => 'getScadenzaId',
        'causale_contabile_id' => 'getCausaleContabileId',
        'emissione_id' => 'getEmissioneId',
        'tipo_conto' => 'getTipoConto',
        'sezione_conto' => 'getSezioneConto',
        'partita_anno' => 'getPartitaAnno',
        'partita_numero' => 'getPartitaNumero',
        'conto' => 'getConto',
        'conto_raw' => 'getContoRaw',
        'descrizione_conto' => 'getDescrizioneConto',
        'dare' => 'getDare',
        'avere' => 'getAvere',
        'note' => 'getNote'
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

    public const TIPO_CONTO_CONTABILE = 'Contabile';
    public const TIPO_CONTO_PARTITARIO_CLIENTI = 'PartitarioClienti';
    public const TIPO_CONTO_PARTITARIO_FORNITORI = 'PartitarioFornitori';
    public const TIPO_CONTO_BANCARIO = 'Bancario';
    public const SEZIONE_CONTO_ATTIVIT = 'Attività';
    public const SEZIONE_CONTO_PASSIVIT = 'Passività';
    public const SEZIONE_CONTO_COSTI = 'Costi';
    public const SEZIONE_CONTO_RICAVI = 'Ricavi';
    public const SEZIONE_CONTO_ORDINE = 'Ordine';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getTipoContoAllowableValues()
    {
        return [
            self::TIPO_CONTO_CONTABILE,
            self::TIPO_CONTO_PARTITARIO_CLIENTI,
            self::TIPO_CONTO_PARTITARIO_FORNITORI,
            self::TIPO_CONTO_BANCARIO,
        ];
    }

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getSezioneContoAllowableValues()
    {
        return [
            self::SEZIONE_CONTO_ATTIVIT,
            self::SEZIONE_CONTO_PASSIVIT,
            self::SEZIONE_CONTO_COSTI,
            self::SEZIONE_CONTO_RICAVI,
            self::SEZIONE_CONTO_ORDINE,
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
        $this->setIfExists('registrazione_contabile_id', $data ?? [], null);
        $this->setIfExists('scadenza_id', $data ?? [], null);
        $this->setIfExists('causale_contabile_id', $data ?? [], null);
        $this->setIfExists('emissione_id', $data ?? [], null);
        $this->setIfExists('tipo_conto', $data ?? [], null);
        $this->setIfExists('sezione_conto', $data ?? [], null);
        $this->setIfExists('partita_anno', $data ?? [], null);
        $this->setIfExists('partita_numero', $data ?? [], null);
        $this->setIfExists('conto', $data ?? [], null);
        $this->setIfExists('conto_raw', $data ?? [], null);
        $this->setIfExists('descrizione_conto', $data ?? [], null);
        $this->setIfExists('dare', $data ?? [], null);
        $this->setIfExists('avere', $data ?? [], null);
        $this->setIfExists('note', $data ?? [], null);
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

        $allowedValues = $this->getTipoContoAllowableValues();
        if (!is_null($this->container['tipo_conto']) && !in_array($this->container['tipo_conto'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'tipo_conto', must be one of '%s'",
                $this->container['tipo_conto'],
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getSezioneContoAllowableValues();
        if (!is_null($this->container['sezione_conto']) && !in_array($this->container['sezione_conto'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'sezione_conto', must be one of '%s'",
                $this->container['sezione_conto'],
                implode("', '", $allowedValues)
            );
        }

        if (!is_null($this->container['conto']) && (mb_strlen($this->container['conto']) > 25)) {
            $invalidProperties[] = "invalid value for 'conto', the character length must be smaller than or equal to 25.";
        }

        if (!is_null($this->container['conto']) && (mb_strlen($this->container['conto']) < 0)) {
            $invalidProperties[] = "invalid value for 'conto', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['conto_raw']) && (mb_strlen($this->container['conto_raw']) > 25)) {
            $invalidProperties[] = "invalid value for 'conto_raw', the character length must be smaller than or equal to 25.";
        }

        if (!is_null($this->container['conto_raw']) && (mb_strlen($this->container['conto_raw']) < 0)) {
            $invalidProperties[] = "invalid value for 'conto_raw', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['descrizione_conto']) && (mb_strlen($this->container['descrizione_conto']) > 121)) {
            $invalidProperties[] = "invalid value for 'descrizione_conto', the character length must be smaller than or equal to 121.";
        }

        if (!is_null($this->container['descrizione_conto']) && (mb_strlen($this->container['descrizione_conto']) < 0)) {
            $invalidProperties[] = "invalid value for 'descrizione_conto', the character length must be bigger than or equal to 0.";
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
     * Gets registrazione_contabile_id
     *
     * @return int|null
     */
    public function getRegistrazioneContabileId()
    {
        return $this->container['registrazione_contabile_id'];
    }

    /**
     * Sets registrazione_contabile_id
     *
     * @param int|null $registrazione_contabile_id Identificativo univoco di `RegistrazioneContabile`.
     *
     * @return self
     */
    public function setRegistrazioneContabileId($registrazione_contabile_id)
    {
        if (is_null($registrazione_contabile_id)) {
            throw new \InvalidArgumentException('non-nullable registrazione_contabile_id cannot be null');
        }
        $this->container['registrazione_contabile_id'] = $registrazione_contabile_id;

        return $this;
    }

    /**
     * Gets scadenza_id
     *
     * @return int|null
     */
    public function getScadenzaId()
    {
        return $this->container['scadenza_id'];
    }

    /**
     * Sets scadenza_id
     *
     * @param int|null $scadenza_id Identificativo univoco di `Scadenza`.
     *
     * @return self
     */
    public function setScadenzaId($scadenza_id)
    {
        if (is_null($scadenza_id)) {
            array_push($this->openAPINullablesSetToNull, 'scadenza_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('scadenza_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['scadenza_id'] = $scadenza_id;

        return $this;
    }

    /**
     * Gets causale_contabile_id
     *
     * @return int|null
     */
    public function getCausaleContabileId()
    {
        return $this->container['causale_contabile_id'];
    }

    /**
     * Sets causale_contabile_id
     *
     * @param int|null $causale_contabile_id Identificativo univoco di `CausaleContabile`.
     *
     * @return self
     */
    public function setCausaleContabileId($causale_contabile_id)
    {
        if (is_null($causale_contabile_id)) {
            array_push($this->openAPINullablesSetToNull, 'causale_contabile_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('causale_contabile_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['causale_contabile_id'] = $causale_contabile_id;

        return $this;
    }

    /**
     * Gets emissione_id
     *
     * @return int|null
     */
    public function getEmissioneId()
    {
        return $this->container['emissione_id'];
    }

    /**
     * Sets emissione_id
     *
     * @param int|null $emissione_id Identificativo del `Documento` di riferimento.
     *
     * @return self
     */
    public function setEmissioneId($emissione_id)
    {
        if (is_null($emissione_id)) {
            array_push($this->openAPINullablesSetToNull, 'emissione_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('emissione_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['emissione_id'] = $emissione_id;

        return $this;
    }

    /**
     * Gets tipo_conto
     *
     * @return string|null
     */
    public function getTipoConto()
    {
        return $this->container['tipo_conto'];
    }

    /**
     * Sets tipo_conto
     *
     * @param string|null $tipo_conto Tipo di conto (piano dei conti).
     *
     * @return self
     */
    public function setTipoConto($tipo_conto)
    {
        if (is_null($tipo_conto)) {
            throw new \InvalidArgumentException('non-nullable tipo_conto cannot be null');
        }
        $allowedValues = $this->getTipoContoAllowableValues();
        if (!in_array($tipo_conto, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'tipo_conto', must be one of '%s'",
                    $tipo_conto,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['tipo_conto'] = $tipo_conto;

        return $this;
    }

    /**
     * Gets sezione_conto
     *
     * @return string|null
     */
    public function getSezioneConto()
    {
        return $this->container['sezione_conto'];
    }

    /**
     * Sets sezione_conto
     *
     * @param string|null $sezione_conto Sezione del piano dei conti.
     *
     * @return self
     */
    public function setSezioneConto($sezione_conto)
    {
        if (is_null($sezione_conto)) {
            throw new \InvalidArgumentException('non-nullable sezione_conto cannot be null');
        }
        $allowedValues = $this->getSezioneContoAllowableValues();
        if (!in_array($sezione_conto, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'sezione_conto', must be one of '%s'",
                    $sezione_conto,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['sezione_conto'] = $sezione_conto;

        return $this;
    }

    /**
     * Gets partita_anno
     *
     * @return int|null
     */
    public function getPartitaAnno()
    {
        return $this->container['partita_anno'];
    }

    /**
     * Sets partita_anno
     *
     * @param int|null $partita_anno Anno della partita aperta.
     *
     * @return self
     */
    public function setPartitaAnno($partita_anno)
    {
        if (is_null($partita_anno)) {
            throw new \InvalidArgumentException('non-nullable partita_anno cannot be null');
        }
        $this->container['partita_anno'] = $partita_anno;

        return $this;
    }

    /**
     * Gets partita_numero
     *
     * @return int|null
     */
    public function getPartitaNumero()
    {
        return $this->container['partita_numero'];
    }

    /**
     * Sets partita_numero
     *
     * @param int|null $partita_numero Numero della partita aperta.
     *
     * @return self
     */
    public function setPartitaNumero($partita_numero)
    {
        if (is_null($partita_numero)) {
            throw new \InvalidArgumentException('non-nullable partita_numero cannot be null');
        }
        $this->container['partita_numero'] = $partita_numero;

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
     * @param string|null $conto Numero di conto (ad es. \"100.20\").
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
        if (!is_null($conto) && (mb_strlen($conto) > 25)) {
            throw new \InvalidArgumentException('invalid length for $conto when calling RegistrazioneContabileRiga., must be smaller than or equal to 25.');
        }
        if (!is_null($conto) && (mb_strlen($conto) < 0)) {
            throw new \InvalidArgumentException('invalid length for $conto when calling RegistrazioneContabileRiga., must be bigger than or equal to 0.');
        }

        $this->container['conto'] = $conto;

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
     * @param string|null $conto_raw Numero di conto, in formato grezzo (ad es. \"  100   20\").
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
            throw new \InvalidArgumentException('invalid length for $conto_raw when calling RegistrazioneContabileRiga., must be smaller than or equal to 25.');
        }
        if (!is_null($conto_raw) && (mb_strlen($conto_raw) < 0)) {
            throw new \InvalidArgumentException('invalid length for $conto_raw when calling RegistrazioneContabileRiga., must be bigger than or equal to 0.');
        }

        $this->container['conto_raw'] = $conto_raw;

        return $this;
    }

    /**
     * Gets descrizione_conto
     *
     * @return string|null
     */
    public function getDescrizioneConto()
    {
        return $this->container['descrizione_conto'];
    }

    /**
     * Sets descrizione_conto
     *
     * @param string|null $descrizione_conto Descrizione.
     *
     * @return self
     */
    public function setDescrizioneConto($descrizione_conto)
    {
        if (is_null($descrizione_conto)) {
            array_push($this->openAPINullablesSetToNull, 'descrizione_conto');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('descrizione_conto', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($descrizione_conto) && (mb_strlen($descrizione_conto) > 121)) {
            throw new \InvalidArgumentException('invalid length for $descrizione_conto when calling RegistrazioneContabileRiga., must be smaller than or equal to 121.');
        }
        if (!is_null($descrizione_conto) && (mb_strlen($descrizione_conto) < 0)) {
            throw new \InvalidArgumentException('invalid length for $descrizione_conto when calling RegistrazioneContabileRiga., must be bigger than or equal to 0.');
        }

        $this->container['descrizione_conto'] = $descrizione_conto;

        return $this;
    }

    /**
     * Gets dare
     *
     * @return float|null
     */
    public function getDare()
    {
        return $this->container['dare'];
    }

    /**
     * Sets dare
     *
     * @param float|null $dare Importo in dare.
     *
     * @return self
     */
    public function setDare($dare)
    {
        if (is_null($dare)) {
            throw new \InvalidArgumentException('non-nullable dare cannot be null');
        }
        $this->container['dare'] = $dare;

        return $this;
    }

    /**
     * Gets avere
     *
     * @return float|null
     */
    public function getAvere()
    {
        return $this->container['avere'];
    }

    /**
     * Sets avere
     *
     * @param float|null $avere Importo in avere.
     *
     * @return self
     */
    public function setAvere($avere)
    {
        if (is_null($avere)) {
            throw new \InvalidArgumentException('non-nullable avere cannot be null');
        }
        $this->container['avere'] = $avere;

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
     * @param string|null $note Note.
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


