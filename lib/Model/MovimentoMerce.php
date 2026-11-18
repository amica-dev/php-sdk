<?php
/**
 * MovimentoMerce
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
 * The version of the OpenAPI document: 3.3.5
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
 * MovimentoMerce Class Doc Comment
 *
 * @category Class
 * @description Movimento di magazzino.
 * @package  Amica\Sdk
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class MovimentoMerce implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'MovimentoMerce';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'id' => 'int',
        'concurrency_token' => 'int',
        'modalita_pagamento_id' => 'int',
        'modalita_pagamento' => '\Amica\Sdk\Model\ModalitaPagamento',
        'causale_magazzino_id' => 'int',
        'causale_magazzino' => '\Amica\Sdk\Model\CausaleMagazzino',
        'anagrafica_id' => 'int',
        'anagrafica' => '\Amica\Sdk\Model\Anagrafica',
        'documento_id' => 'int',
        'iva_esclusa' => 'bool',
        'data' => '\DateTime',
        'descrizione' => 'string',
        'documento_numero' => 'string',
        'documento_data' => '\DateTime',
        'note' => 'string',
        'righe' => '\Amica\Sdk\Model\MovimentoMerceRiga[]'
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
        'modalita_pagamento_id' => 'int32',
        'modalita_pagamento' => null,
        'causale_magazzino_id' => 'int32',
        'causale_magazzino' => null,
        'anagrafica_id' => 'int32',
        'anagrafica' => null,
        'documento_id' => 'int32',
        'iva_esclusa' => null,
        'data' => 'date-time',
        'descrizione' => null,
        'documento_numero' => null,
        'documento_data' => 'date-time',
        'note' => null,
        'righe' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'id' => false,
        'concurrency_token' => false,
        'modalita_pagamento_id' => true,
        'modalita_pagamento' => false,
        'causale_magazzino_id' => false,
        'causale_magazzino' => false,
        'anagrafica_id' => true,
        'anagrafica' => false,
        'documento_id' => true,
        'iva_esclusa' => false,
        'data' => false,
        'descrizione' => true,
        'documento_numero' => true,
        'documento_data' => true,
        'note' => true,
        'righe' => true
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
        'modalita_pagamento_id' => 'modalitaPagamentoId',
        'modalita_pagamento' => 'modalitaPagamento',
        'causale_magazzino_id' => 'causaleMagazzinoId',
        'causale_magazzino' => 'causaleMagazzino',
        'anagrafica_id' => 'anagraficaId',
        'anagrafica' => 'anagrafica',
        'documento_id' => 'documentoId',
        'iva_esclusa' => 'ivaEsclusa',
        'data' => 'data',
        'descrizione' => 'descrizione',
        'documento_numero' => 'documentoNumero',
        'documento_data' => 'documentoData',
        'note' => 'note',
        'righe' => 'righe'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'id' => 'setId',
        'concurrency_token' => 'setConcurrencyToken',
        'modalita_pagamento_id' => 'setModalitaPagamentoId',
        'modalita_pagamento' => 'setModalitaPagamento',
        'causale_magazzino_id' => 'setCausaleMagazzinoId',
        'causale_magazzino' => 'setCausaleMagazzino',
        'anagrafica_id' => 'setAnagraficaId',
        'anagrafica' => 'setAnagrafica',
        'documento_id' => 'setDocumentoId',
        'iva_esclusa' => 'setIvaEsclusa',
        'data' => 'setData',
        'descrizione' => 'setDescrizione',
        'documento_numero' => 'setDocumentoNumero',
        'documento_data' => 'setDocumentoData',
        'note' => 'setNote',
        'righe' => 'setRighe'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'id' => 'getId',
        'concurrency_token' => 'getConcurrencyToken',
        'modalita_pagamento_id' => 'getModalitaPagamentoId',
        'modalita_pagamento' => 'getModalitaPagamento',
        'causale_magazzino_id' => 'getCausaleMagazzinoId',
        'causale_magazzino' => 'getCausaleMagazzino',
        'anagrafica_id' => 'getAnagraficaId',
        'anagrafica' => 'getAnagrafica',
        'documento_id' => 'getDocumentoId',
        'iva_esclusa' => 'getIvaEsclusa',
        'data' => 'getData',
        'descrizione' => 'getDescrizione',
        'documento_numero' => 'getDocumentoNumero',
        'documento_data' => 'getDocumentoData',
        'note' => 'getNote',
        'righe' => 'getRighe'
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
        $this->setIfExists('modalita_pagamento_id', $data ?? [], null);
        $this->setIfExists('modalita_pagamento', $data ?? [], null);
        $this->setIfExists('causale_magazzino_id', $data ?? [], null);
        $this->setIfExists('causale_magazzino', $data ?? [], null);
        $this->setIfExists('anagrafica_id', $data ?? [], null);
        $this->setIfExists('anagrafica', $data ?? [], null);
        $this->setIfExists('documento_id', $data ?? [], null);
        $this->setIfExists('iva_esclusa', $data ?? [], null);
        $this->setIfExists('data', $data ?? [], null);
        $this->setIfExists('descrizione', $data ?? [], null);
        $this->setIfExists('documento_numero', $data ?? [], null);
        $this->setIfExists('documento_data', $data ?? [], null);
        $this->setIfExists('note', $data ?? [], null);
        $this->setIfExists('righe', $data ?? [], null);
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

        if (!is_null($this->container['descrizione']) && (mb_strlen($this->container['descrizione']) > 50)) {
            $invalidProperties[] = "invalid value for 'descrizione', the character length must be smaller than or equal to 50.";
        }

        if (!is_null($this->container['descrizione']) && (mb_strlen($this->container['descrizione']) < 0)) {
            $invalidProperties[] = "invalid value for 'descrizione', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['documento_numero']) && (mb_strlen($this->container['documento_numero']) > 20)) {
            $invalidProperties[] = "invalid value for 'documento_numero', the character length must be smaller than or equal to 20.";
        }

        if (!is_null($this->container['documento_numero']) && (mb_strlen($this->container['documento_numero']) < 0)) {
            $invalidProperties[] = "invalid value for 'documento_numero', the character length must be bigger than or equal to 0.";
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
     * Gets modalita_pagamento_id
     *
     * @return int|null
     */
    public function getModalitaPagamentoId()
    {
        return $this->container['modalita_pagamento_id'];
    }

    /**
     * Sets modalita_pagamento_id
     *
     * @param int|null $modalita_pagamento_id Identificativo univoco di `ModalitaPagamento`.
     *
     * @return self
     */
    public function setModalitaPagamentoId($modalita_pagamento_id)
    {
        if (is_null($modalita_pagamento_id)) {
            array_push($this->openAPINullablesSetToNull, 'modalita_pagamento_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('modalita_pagamento_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['modalita_pagamento_id'] = $modalita_pagamento_id;

        return $this;
    }

    /**
     * Gets modalita_pagamento
     *
     * @return \Amica\Sdk\Model\ModalitaPagamento|null
     */
    public function getModalitaPagamento()
    {
        return $this->container['modalita_pagamento'];
    }

    /**
     * Sets modalita_pagamento
     *
     * @param \Amica\Sdk\Model\ModalitaPagamento|null $modalita_pagamento modalita_pagamento
     *
     * @return self
     */
    public function setModalitaPagamento($modalita_pagamento)
    {
        if (is_null($modalita_pagamento)) {
            throw new \InvalidArgumentException('non-nullable modalita_pagamento cannot be null');
        }
        $this->container['modalita_pagamento'] = $modalita_pagamento;

        return $this;
    }

    /**
     * Gets causale_magazzino_id
     *
     * @return int|null
     */
    public function getCausaleMagazzinoId()
    {
        return $this->container['causale_magazzino_id'];
    }

    /**
     * Sets causale_magazzino_id
     *
     * @param int|null $causale_magazzino_id Identificativo univoco di `CausaleMagazzino`.
     *
     * @return self
     */
    public function setCausaleMagazzinoId($causale_magazzino_id)
    {
        if (is_null($causale_magazzino_id)) {
            throw new \InvalidArgumentException('non-nullable causale_magazzino_id cannot be null');
        }
        $this->container['causale_magazzino_id'] = $causale_magazzino_id;

        return $this;
    }

    /**
     * Gets causale_magazzino
     *
     * @return \Amica\Sdk\Model\CausaleMagazzino|null
     */
    public function getCausaleMagazzino()
    {
        return $this->container['causale_magazzino'];
    }

    /**
     * Sets causale_magazzino
     *
     * @param \Amica\Sdk\Model\CausaleMagazzino|null $causale_magazzino causale_magazzino
     *
     * @return self
     */
    public function setCausaleMagazzino($causale_magazzino)
    {
        if (is_null($causale_magazzino)) {
            throw new \InvalidArgumentException('non-nullable causale_magazzino cannot be null');
        }
        $this->container['causale_magazzino'] = $causale_magazzino;

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
     * @param int|null $anagrafica_id Identificativo univoco di `Anagrafica`.
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
     * Gets anagrafica
     *
     * @return \Amica\Sdk\Model\Anagrafica|null
     */
    public function getAnagrafica()
    {
        return $this->container['anagrafica'];
    }

    /**
     * Sets anagrafica
     *
     * @param \Amica\Sdk\Model\Anagrafica|null $anagrafica anagrafica
     *
     * @return self
     */
    public function setAnagrafica($anagrafica)
    {
        if (is_null($anagrafica)) {
            throw new \InvalidArgumentException('non-nullable anagrafica cannot be null');
        }
        $this->container['anagrafica'] = $anagrafica;

        return $this;
    }

    /**
     * Gets documento_id
     *
     * @return int|null
     */
    public function getDocumentoId()
    {
        return $this->container['documento_id'];
    }

    /**
     * Sets documento_id
     *
     * @param int|null $documento_id Identificativo univoco di `Documento`.
     *
     * @return self
     */
    public function setDocumentoId($documento_id)
    {
        if (is_null($documento_id)) {
            array_push($this->openAPINullablesSetToNull, 'documento_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('documento_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['documento_id'] = $documento_id;

        return $this;
    }

    /**
     * Gets iva_esclusa
     *
     * @return bool|null
     */
    public function getIvaEsclusa()
    {
        return $this->container['iva_esclusa'];
    }

    /**
     * Sets iva_esclusa
     *
     * @param bool|null $iva_esclusa Indica se i prezzi inseriti sono da considerarsi al netto dell'IVA. Se `false`, l'IVA verrà scorporata in fase di elaborazione (app ufficiali).
     *
     * @return self
     */
    public function setIvaEsclusa($iva_esclusa)
    {
        if (is_null($iva_esclusa)) {
            throw new \InvalidArgumentException('non-nullable iva_esclusa cannot be null');
        }
        $this->container['iva_esclusa'] = $iva_esclusa;

        return $this;
    }

    /**
     * Gets data
     *
     * @return \DateTime|null
     */
    public function getData()
    {
        return $this->container['data'];
    }

    /**
     * Sets data
     *
     * @param \DateTime|null $data Data del movimento.
     *
     * @return self
     */
    public function setData($data)
    {
        if (is_null($data)) {
            throw new \InvalidArgumentException('non-nullable data cannot be null');
        }
        $this->container['data'] = $data;

        return $this;
    }

    /**
     * Gets descrizione
     *
     * @return string|null
     */
    public function getDescrizione()
    {
        return $this->container['descrizione'];
    }

    /**
     * Sets descrizione
     *
     * @param string|null $descrizione Descrizione.
     *
     * @return self
     */
    public function setDescrizione($descrizione)
    {
        if (is_null($descrizione)) {
            array_push($this->openAPINullablesSetToNull, 'descrizione');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('descrizione', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($descrizione) && (mb_strlen($descrizione) > 50)) {
            throw new \InvalidArgumentException('invalid length for $descrizione when calling MovimentoMerce., must be smaller than or equal to 50.');
        }
        if (!is_null($descrizione) && (mb_strlen($descrizione) < 0)) {
            throw new \InvalidArgumentException('invalid length for $descrizione when calling MovimentoMerce., must be bigger than or equal to 0.');
        }

        $this->container['descrizione'] = $descrizione;

        return $this;
    }

    /**
     * Gets documento_numero
     *
     * @return string|null
     */
    public function getDocumentoNumero()
    {
        return $this->container['documento_numero'];
    }

    /**
     * Sets documento_numero
     *
     * @param string|null $documento_numero Numero del documento di riferimento.
     *
     * @return self
     */
    public function setDocumentoNumero($documento_numero)
    {
        if (is_null($documento_numero)) {
            array_push($this->openAPINullablesSetToNull, 'documento_numero');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('documento_numero', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($documento_numero) && (mb_strlen($documento_numero) > 20)) {
            throw new \InvalidArgumentException('invalid length for $documento_numero when calling MovimentoMerce., must be smaller than or equal to 20.');
        }
        if (!is_null($documento_numero) && (mb_strlen($documento_numero) < 0)) {
            throw new \InvalidArgumentException('invalid length for $documento_numero when calling MovimentoMerce., must be bigger than or equal to 0.');
        }

        $this->container['documento_numero'] = $documento_numero;

        return $this;
    }

    /**
     * Gets documento_data
     *
     * @return \DateTime|null
     */
    public function getDocumentoData()
    {
        return $this->container['documento_data'];
    }

    /**
     * Sets documento_data
     *
     * @param \DateTime|null $documento_data Data del documento di riferimento.
     *
     * @return self
     */
    public function setDocumentoData($documento_data)
    {
        if (is_null($documento_data)) {
            array_push($this->openAPINullablesSetToNull, 'documento_data');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('documento_data', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['documento_data'] = $documento_data;

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
     * Gets righe
     *
     * @return \Amica\Sdk\Model\MovimentoMerceRiga[]|null
     */
    public function getRighe()
    {
        return $this->container['righe'];
    }

    /**
     * Sets righe
     *
     * @param \Amica\Sdk\Model\MovimentoMerceRiga[]|null $righe Righe del movimento.
     *
     * @return self
     */
    public function setRighe($righe)
    {
        if (is_null($righe)) {
            array_push($this->openAPINullablesSetToNull, 'righe');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('righe', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['righe'] = $righe;

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


