<?php
/**
 * MovimentoMerceRiga
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
 * L'Amica Cloud API fornisce accesso programmatico a tutti i dati aziendali gestiti da [Amica 20](https://gestionaleamica.com). Supporta il ciclo completo di documenti, fatture, magazzino, contabilità e anagrafiche. Per guide e documentazione completa, visitare [amica.dev](https://amica.dev/).  ## Autenticazione  Tutte le richieste API richiedono un token OAuth 2.0 Bearer valido, da includere nell'header `Authorization`:  ``` Authorization: Bearer <token> ```  ### Registra la tua app  Per integrare l'Amica Cloud API registra la tua applicazione sull'[**App Store di Amica**](https://apps.amica20.it). Al termine della registrazione ottieni una coppia di credenziali OAuth 2.0 — `client_id` e `client_secret` (il secret è mostrato **una sola volta**) — e scegli gli **scope** e il **flusso di autenticazione** desiderati.  ### Scegli il flusso  La scelta dipende da **come** la tua applicazione si autentica:  - C'è un **utente** che può accedere via browser (web app, SPA, app mobile)? → **Authorization Code con PKCE**. - È un **servizio headless** (backend, batch, integrazione server-to-server) di una **singola azienda**, senza un utente che effettua il login? → **Client Credentials**.  #### Authorization Code con PKCE  Consigliato per le app che operano per conto di un utente Amica ed è l'**unico flusso disponibile per le app pubblicate sul marketplace**. Una sola registrazione serve un numero qualsiasi di aziende: ogni utente autorizza la tua app per la propria azienda tramite la pagina di consenso dell'identity provider, e il token riceve automaticamente il contesto di quell'azienda. La tua app riceve un `code` da scambiare con un access token.  ``` GET  {identity-provider}/connect/authorize?response_type=code&client_id=<client_id>&redirect_uri=<redirect_uri>&scope=openid%20read%20write%20documenti&code_challenge=<challenge>&code_challenge_method=S256 POST {identity-provider}/connect/token      grant_type=authorization_code&code=<code>&code_verifier=<verifier>&client_id=<client_id>&client_secret=<client_secret>&redirect_uri=<redirect_uri> ```  #### Client Credentials  Per integrazioni **server-to-server** senza utente interattivo. Disponibile solo per le **app private**, non per quelle pubblicate sul marketplace. Un'app Client Credentials è **legata a una singola azienda** — quella dell'utente che la registra — e il token ne riceve automaticamente il contesto. Per servire un'azienda diversa serve una registrazione separata; se devi distribuire la stessa app a più aziende usa invece **Authorization Code**.  ``` POST {identity-provider}/connect/token Content-Type: application/x-www-form-urlencoded  grant_type=client_credentials&client_id=<client_id>&client_secret=<client_secret>&scope=comune read write documenti ```  > **Importante:** non includere lo scope `openid` (né `profile`/`email`) in questo flusso. `openid` attiva OpenID Connect — richiede un utente che effettua il login e l'emissione di un ID token — cosa che un flusso server-to-server non prevede: la richiesta viene rifiutata con l'errore `Client cannot request OpenID scopes in client credentials flow`. Indica solo gli scope di risorsa (`comune`, `magazzino`, `documenti`, `contabilita`, ed eventualmente `read`/`write`).  In entrambi i casi la risposta contiene l'`access_token` (JWT con `aud: amica-cloud`) da usare nelle chiamate successive finché non scade (`expires_in`), dopodiché va richiesto un nuovo token. Ogni token contiene uno o più **scope** che determinano le aree accessibili (vedi sotto). Dalla [API Reference](https://amica.dev) è possibile incollare un access token per provare gli endpoint direttamente dal browser.  ## Scope di autorizzazione  L'accesso agli endpoint è governato dagli scope presenti nel token OAuth:  | Scope | Descrizione | |-|-| | `comune` | Anagrafiche: contatti, indirizzi, nazioni, valute, modalità di pagamento, ecc. | | `magazzino` | Magazzino: articoli, listini, movimenti di magazzino, giacenze, distinte base. | | `contabilita` | Contabilità: registrazioni contabili, scadenze, castelletti IVA, fatture elettroniche XML. | | `documenti` | Documenti: fatture, note di credito, DDT, ordini, preventivi, proforma ed emissione. |  Una richiesta verso un endpoint non coperto dallo scope del token riceverà una risposta `403 Forbidden`.  ## Company ID  La maggior parte degli endpoint è relativa a una specifica azienda e segue il pattern:  ``` /api/v3/{companyId}/risorsa ```  Il `companyId` è l'identificativo univoco di ciascun database aziendale. Usare l'endpoint `GET /api/v3/config/Azienda` per ottenere l'elenco delle aziende disponibili.  ## Paginazione  Gli endpoint che restituiscono liste supportano la paginazione tramite query parameter:  | Parametro | Descrizione | |-|-| | `Skip` | Numero di elementi da saltare dall'inizio del set di risultati. | | `Take` | Numero di elementi da includere nella risposta. |  La risposta include due header:  | Header | Descrizione | |-|-| | `X-Read-TotalCount` | Numero totale di elementi che soddisfano il filtro corrente. | | `X-Read-Count` | Numero di elementi effettivamente restituiti nel body della risposta. |  ## Ordinamento e filtri  Gli endpoint lista accettano parametri aggiuntivi:  | Parametro | Descrizione | |-|-| | `Order` | Lista di proprietà per l'ordinamento, separate da virgola. Un segno meno (`-`) prima del nome indica ordine discendente. Es. `-RagioneSociale1,Citta`. | | `Filter` | Condizioni di ricerca: lista di espressioni separate da virgola, ciascuna formata da nome campo, operatore, valore ed eventuale connettore logico. Operatori: `$eq`, `$ne`, `$gt`, `$lt`, `$lte`, `$gte`, `$ct` (contiene), `$nct`, `$sw` (inizia con), `$ew` (finisce con). Connettori: `$and` (default, omissibile) e `$or`; le sotto-query si racchiudono tra `[]`. Es. `RagioneSociale1 $ct acme $or, Fido $gt 1000`. | | `SkipTotalCount` | Se `true`, l'header `X-Read-TotalCount` viene omesso dalla risposta: migliori prestazioni a scapito del conteggio totale. Default `false`. |  ## Espansione oggetti correlati  Gli endpoint singoli e lista supportano il parametro `Expand`. Quando fornito, gli oggetti correlati vengono inclusi inline nella risposta anziché restituiti come riferimenti. È possibile specificare espansioni multiple separate da virgola.  ## Gestione errori  L'API usa codici di stato HTTP standard e restituisce [RFC 7807](https://tools.ietf.org/html/rfc7807) Problem Details in caso di errore:  | Codice | Significato | |-|-| | `400` | Bad Request — validazione fallita o parametro obbligatorio mancante. | | `401` | Unauthorized — token mancante o scaduto. | | `403` | Forbidden — il token non possiede lo scope richiesto. | | `404` | Not Found — la risorsa richiesta non esiste. | | `409` | Conflict — conflitto di concorrenza. | | `422` | Unprocessable Entity — entità non processabile (es. token di concorrenza obsoleto). |  Il motivo dell'errore è sempre nel campo `detail`. Ogni risposta di errore riporta inoltre `overridable`: quando vale `true`, l'errore di validazione è *forzabile* e basta ripetere la stessa richiesta aggiungendo `validationOverride=true` alla query string perché il servizio la accetti. È il caso delle anomalie formali su dati reali — la partita IVA di un fornitore estero, per esempio — che il cliente ha comunque bisogno di registrare.  ## Controllo della concorrenza  Le entità contengono una proprietà `ConcurrencyToken`. Quando si aggiorna un'entità tramite `PUT`, il token nel body della richiesta viene confrontato con il valore memorizzato. Se differiscono, viene restituito un `422 Unprocessable Entity`. Usare sempre l'ultimo token ricevuto da una risposta `GET` o `POST`.
 *
 * The version of the OpenAPI document: 3.0.1
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
 * MovimentoMerceRiga Class Doc Comment
 *
 * @category Class
 * @description Riga di un movimento di magazzino.
 * @package  Amica\Sdk
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class MovimentoMerceRiga implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'MovimentoMerceRiga';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'id' => 'int',
        'concurrency_token' => 'int',
        'movimento_merce_id' => 'int',
        'articolo_id' => 'int',
        'articolo' => '\Amica\Sdk\Model\Articolo',
        'listino_id' => 'int',
        'magazzino_id' => 'int',
        'prezzo_id' => 'int',
        'prezzo' => '\Amica\Sdk\Model\Prezzo',
        'causale_iva_id' => 'int',
        'causale_iva' => '\Amica\Sdk\Model\CausaleIva',
        'taglia_id' => 'int',
        'taglia' => '\Amica\Sdk\Model\Taglia',
        'quantit' => 'float',
        'prezzo_netto' => 'float',
        'prezzo_ivato' => 'float',
        'colore' => 'string',
        'identificativo' => 'string',
        'identificativo_esteso' => 'string',
        'data' => '\DateTime'
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
        'movimento_merce_id' => 'int32',
        'articolo_id' => 'int32',
        'articolo' => null,
        'listino_id' => 'int32',
        'magazzino_id' => 'int32',
        'prezzo_id' => 'int32',
        'prezzo' => null,
        'causale_iva_id' => 'int32',
        'causale_iva' => null,
        'taglia_id' => 'int32',
        'taglia' => null,
        'quantit' => 'double',
        'prezzo_netto' => 'double',
        'prezzo_ivato' => 'double',
        'colore' => null,
        'identificativo' => null,
        'identificativo_esteso' => null,
        'data' => 'date-time'
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'id' => false,
        'concurrency_token' => false,
        'movimento_merce_id' => false,
        'articolo_id' => false,
        'articolo' => false,
        'listino_id' => true,
        'magazzino_id' => false,
        'prezzo_id' => true,
        'prezzo' => false,
        'causale_iva_id' => true,
        'causale_iva' => false,
        'taglia_id' => true,
        'taglia' => false,
        'quantit' => false,
        'prezzo_netto' => false,
        'prezzo_ivato' => false,
        'colore' => true,
        'identificativo' => true,
        'identificativo_esteso' => true,
        'data' => true
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
        'movimento_merce_id' => 'movimentoMerceId',
        'articolo_id' => 'articoloId',
        'articolo' => 'articolo',
        'listino_id' => 'listinoId',
        'magazzino_id' => 'magazzinoId',
        'prezzo_id' => 'prezzoId',
        'prezzo' => 'prezzo',
        'causale_iva_id' => 'causaleIvaId',
        'causale_iva' => 'causaleIva',
        'taglia_id' => 'tagliaId',
        'taglia' => 'taglia',
        'quantit' => 'quantità',
        'prezzo_netto' => 'prezzoNetto',
        'prezzo_ivato' => 'prezzoIvato',
        'colore' => 'colore',
        'identificativo' => 'identificativo',
        'identificativo_esteso' => 'identificativoEsteso',
        'data' => 'data'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'id' => 'setId',
        'concurrency_token' => 'setConcurrencyToken',
        'movimento_merce_id' => 'setMovimentoMerceId',
        'articolo_id' => 'setArticoloId',
        'articolo' => 'setArticolo',
        'listino_id' => 'setListinoId',
        'magazzino_id' => 'setMagazzinoId',
        'prezzo_id' => 'setPrezzoId',
        'prezzo' => 'setPrezzo',
        'causale_iva_id' => 'setCausaleIvaId',
        'causale_iva' => 'setCausaleIva',
        'taglia_id' => 'setTagliaId',
        'taglia' => 'setTaglia',
        'quantit' => 'setQuantit',
        'prezzo_netto' => 'setPrezzoNetto',
        'prezzo_ivato' => 'setPrezzoIvato',
        'colore' => 'setColore',
        'identificativo' => 'setIdentificativo',
        'identificativo_esteso' => 'setIdentificativoEsteso',
        'data' => 'setData'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'id' => 'getId',
        'concurrency_token' => 'getConcurrencyToken',
        'movimento_merce_id' => 'getMovimentoMerceId',
        'articolo_id' => 'getArticoloId',
        'articolo' => 'getArticolo',
        'listino_id' => 'getListinoId',
        'magazzino_id' => 'getMagazzinoId',
        'prezzo_id' => 'getPrezzoId',
        'prezzo' => 'getPrezzo',
        'causale_iva_id' => 'getCausaleIvaId',
        'causale_iva' => 'getCausaleIva',
        'taglia_id' => 'getTagliaId',
        'taglia' => 'getTaglia',
        'quantit' => 'getQuantit',
        'prezzo_netto' => 'getPrezzoNetto',
        'prezzo_ivato' => 'getPrezzoIvato',
        'colore' => 'getColore',
        'identificativo' => 'getIdentificativo',
        'identificativo_esteso' => 'getIdentificativoEsteso',
        'data' => 'getData'
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
        $this->setIfExists('movimento_merce_id', $data ?? [], null);
        $this->setIfExists('articolo_id', $data ?? [], null);
        $this->setIfExists('articolo', $data ?? [], null);
        $this->setIfExists('listino_id', $data ?? [], null);
        $this->setIfExists('magazzino_id', $data ?? [], null);
        $this->setIfExists('prezzo_id', $data ?? [], null);
        $this->setIfExists('prezzo', $data ?? [], null);
        $this->setIfExists('causale_iva_id', $data ?? [], null);
        $this->setIfExists('causale_iva', $data ?? [], null);
        $this->setIfExists('taglia_id', $data ?? [], null);
        $this->setIfExists('taglia', $data ?? [], null);
        $this->setIfExists('quantit', $data ?? [], null);
        $this->setIfExists('prezzo_netto', $data ?? [], null);
        $this->setIfExists('prezzo_ivato', $data ?? [], null);
        $this->setIfExists('colore', $data ?? [], null);
        $this->setIfExists('identificativo', $data ?? [], null);
        $this->setIfExists('identificativo_esteso', $data ?? [], null);
        $this->setIfExists('data', $data ?? [], null);
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

        if (!is_null($this->container['colore']) && (mb_strlen($this->container['colore']) > 20)) {
            $invalidProperties[] = "invalid value for 'colore', the character length must be smaller than or equal to 20.";
        }

        if (!is_null($this->container['colore']) && (mb_strlen($this->container['colore']) < 0)) {
            $invalidProperties[] = "invalid value for 'colore', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['identificativo']) && (mb_strlen($this->container['identificativo']) > 20)) {
            $invalidProperties[] = "invalid value for 'identificativo', the character length must be smaller than or equal to 20.";
        }

        if (!is_null($this->container['identificativo']) && (mb_strlen($this->container['identificativo']) < 0)) {
            $invalidProperties[] = "invalid value for 'identificativo', the character length must be bigger than or equal to 0.";
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
     * Gets movimento_merce_id
     *
     * @return int|null
     */
    public function getMovimentoMerceId()
    {
        return $this->container['movimento_merce_id'];
    }

    /**
     * Sets movimento_merce_id
     *
     * @param int|null $movimento_merce_id Identificativo univoco di `MovimentoMerce`.
     *
     * @return self
     */
    public function setMovimentoMerceId($movimento_merce_id)
    {
        if (is_null($movimento_merce_id)) {
            throw new \InvalidArgumentException('non-nullable movimento_merce_id cannot be null');
        }
        $this->container['movimento_merce_id'] = $movimento_merce_id;

        return $this;
    }

    /**
     * Gets articolo_id
     *
     * @return int|null
     */
    public function getArticoloId()
    {
        return $this->container['articolo_id'];
    }

    /**
     * Sets articolo_id
     *
     * @param int|null $articolo_id Identificativo univoco di `Articolo`.
     *
     * @return self
     */
    public function setArticoloId($articolo_id)
    {
        if (is_null($articolo_id)) {
            throw new \InvalidArgumentException('non-nullable articolo_id cannot be null');
        }
        $this->container['articolo_id'] = $articolo_id;

        return $this;
    }

    /**
     * Gets articolo
     *
     * @return \Amica\Sdk\Model\Articolo|null
     */
    public function getArticolo()
    {
        return $this->container['articolo'];
    }

    /**
     * Sets articolo
     *
     * @param \Amica\Sdk\Model\Articolo|null $articolo articolo
     *
     * @return self
     */
    public function setArticolo($articolo)
    {
        if (is_null($articolo)) {
            throw new \InvalidArgumentException('non-nullable articolo cannot be null');
        }
        $this->container['articolo'] = $articolo;

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
     * @param int|null $listino_id Identificativo univoco di `Listino`.
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
     * Gets magazzino_id
     *
     * @return int|null
     */
    public function getMagazzinoId()
    {
        return $this->container['magazzino_id'];
    }

    /**
     * Sets magazzino_id
     *
     * @param int|null $magazzino_id Identificativo univoco di `Magazzino`.
     *
     * @return self
     */
    public function setMagazzinoId($magazzino_id)
    {
        if (is_null($magazzino_id)) {
            throw new \InvalidArgumentException('non-nullable magazzino_id cannot be null');
        }
        $this->container['magazzino_id'] = $magazzino_id;

        return $this;
    }

    /**
     * Gets prezzo_id
     *
     * @return int|null
     */
    public function getPrezzoId()
    {
        return $this->container['prezzo_id'];
    }

    /**
     * Sets prezzo_id
     *
     * @param int|null $prezzo_id Identificativo univoco di `Prezzo`.
     *
     * @return self
     */
    public function setPrezzoId($prezzo_id)
    {
        if (is_null($prezzo_id)) {
            array_push($this->openAPINullablesSetToNull, 'prezzo_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('prezzo_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['prezzo_id'] = $prezzo_id;

        return $this;
    }

    /**
     * Gets prezzo
     *
     * @return \Amica\Sdk\Model\Prezzo|null
     */
    public function getPrezzo()
    {
        return $this->container['prezzo'];
    }

    /**
     * Sets prezzo
     *
     * @param \Amica\Sdk\Model\Prezzo|null $prezzo prezzo
     *
     * @return self
     */
    public function setPrezzo($prezzo)
    {
        if (is_null($prezzo)) {
            throw new \InvalidArgumentException('non-nullable prezzo cannot be null');
        }
        $this->container['prezzo'] = $prezzo;

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
     * @param int|null $causale_iva_id Identificativo univoco di `CausaleIva`.
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
     * @param int|null $taglia_id Identificativo univoco di `Taglia`.
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
     * Gets quantit
     *
     * @return float|null
     */
    public function getQuantit()
    {
        return $this->container['quantit'];
    }

    /**
     * Sets quantit
     *
     * @param float|null $quantit Quantità movimentata.
     *
     * @return self
     */
    public function setQuantit($quantit)
    {
        if (is_null($quantit)) {
            throw new \InvalidArgumentException('non-nullable quantit cannot be null');
        }
        $this->container['quantit'] = $quantit;

        return $this;
    }

    /**
     * Gets prezzo_netto
     *
     * @return float|null
     */
    public function getPrezzoNetto()
    {
        return $this->container['prezzo_netto'];
    }

    /**
     * Sets prezzo_netto
     *
     * @param float|null $prezzo_netto Prezzo applicato, al netto dell'IVA.
     *
     * @return self
     */
    public function setPrezzoNetto($prezzo_netto)
    {
        if (is_null($prezzo_netto)) {
            throw new \InvalidArgumentException('non-nullable prezzo_netto cannot be null');
        }
        $this->container['prezzo_netto'] = $prezzo_netto;

        return $this;
    }

    /**
     * Gets prezzo_ivato
     *
     * @return float|null
     */
    public function getPrezzoIvato()
    {
        return $this->container['prezzo_ivato'];
    }

    /**
     * Sets prezzo_ivato
     *
     * @param float|null $prezzo_ivato Prezzo applicato, IVA inclusa.
     *
     * @return self
     */
    public function setPrezzoIvato($prezzo_ivato)
    {
        if (is_null($prezzo_ivato)) {
            throw new \InvalidArgumentException('non-nullable prezzo_ivato cannot be null');
        }
        $this->container['prezzo_ivato'] = $prezzo_ivato;

        return $this;
    }

    /**
     * Gets colore
     *
     * @return string|null
     */
    public function getColore()
    {
        return $this->container['colore'];
    }

    /**
     * Sets colore
     *
     * @param string|null $colore Colore.
     *
     * @return self
     */
    public function setColore($colore)
    {
        if (is_null($colore)) {
            array_push($this->openAPINullablesSetToNull, 'colore');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('colore', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($colore) && (mb_strlen($colore) > 20)) {
            throw new \InvalidArgumentException('invalid length for $colore when calling MovimentoMerceRiga., must be smaller than or equal to 20.');
        }
        if (!is_null($colore) && (mb_strlen($colore) < 0)) {
            throw new \InvalidArgumentException('invalid length for $colore when calling MovimentoMerceRiga., must be bigger than or equal to 0.');
        }

        $this->container['colore'] = $colore;

        return $this;
    }

    /**
     * Gets identificativo
     *
     * @return string|null
     */
    public function getIdentificativo()
    {
        return $this->container['identificativo'];
    }

    /**
     * Sets identificativo
     *
     * @param string|null $identificativo Numero di serie o altro identificativo del prodotto.
     *
     * @return self
     */
    public function setIdentificativo($identificativo)
    {
        if (is_null($identificativo)) {
            array_push($this->openAPINullablesSetToNull, 'identificativo');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('identificativo', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($identificativo) && (mb_strlen($identificativo) > 20)) {
            throw new \InvalidArgumentException('invalid length for $identificativo when calling MovimentoMerceRiga., must be smaller than or equal to 20.');
        }
        if (!is_null($identificativo) && (mb_strlen($identificativo) < 0)) {
            throw new \InvalidArgumentException('invalid length for $identificativo when calling MovimentoMerceRiga., must be bigger than or equal to 0.');
        }

        $this->container['identificativo'] = $identificativo;

        return $this;
    }

    /**
     * Gets identificativo_esteso
     *
     * @return string|null
     */
    public function getIdentificativoEsteso()
    {
        return $this->container['identificativo_esteso'];
    }

    /**
     * Sets identificativo_esteso
     *
     * @param string|null $identificativo_esteso Identificativo esteso del prodotto.
     *
     * @return self
     */
    public function setIdentificativoEsteso($identificativo_esteso)
    {
        if (is_null($identificativo_esteso)) {
            array_push($this->openAPINullablesSetToNull, 'identificativo_esteso');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('identificativo_esteso', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['identificativo_esteso'] = $identificativo_esteso;

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
     * @param \DateTime|null $data Data di scadenza, produzione, fine garanzia o altro.
     *
     * @return self
     */
    public function setData($data)
    {
        if (is_null($data)) {
            array_push($this->openAPINullablesSetToNull, 'data');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('data', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['data'] = $data;

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


