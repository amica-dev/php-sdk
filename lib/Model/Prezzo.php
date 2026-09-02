<?php
/**
 * Prezzo
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
 * Prezzo Class Doc Comment
 *
 * @category Class
 * @description Prezzo di listino.
 * @package  Amica\Sdk
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class Prezzo implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'Prezzo';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'id' => 'int',
        'concurrency_token' => 'int',
        'listino_id' => 'int',
        'articolo_id' => 'int',
        'articolo' => '\Amica\Sdk\Model\Articolo',
        'prestazione_id' => 'int',
        'prestazione' => '\Amica\Sdk\Model\Articolo',
        'importo' => 'float',
        'sconto1' => 'float',
        'sconto2' => 'float',
        'sconto3' => 'float',
        'sconto4' => 'float',
        'ricarico' => 'float',
        'provvigione_agente' => 'float',
        'provvigione_capo_area' => 'float'
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
        'listino_id' => 'int32',
        'articolo_id' => 'int32',
        'articolo' => null,
        'prestazione_id' => 'int32',
        'prestazione' => null,
        'importo' => 'double',
        'sconto1' => 'double',
        'sconto2' => 'double',
        'sconto3' => 'double',
        'sconto4' => 'double',
        'ricarico' => 'double',
        'provvigione_agente' => 'double',
        'provvigione_capo_area' => 'double'
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'id' => false,
        'concurrency_token' => false,
        'listino_id' => true,
        'articolo_id' => true,
        'articolo' => false,
        'prestazione_id' => true,
        'prestazione' => false,
        'importo' => false,
        'sconto1' => false,
        'sconto2' => false,
        'sconto3' => false,
        'sconto4' => false,
        'ricarico' => false,
        'provvigione_agente' => true,
        'provvigione_capo_area' => true
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
        'listino_id' => 'listinoId',
        'articolo_id' => 'articoloId',
        'articolo' => 'articolo',
        'prestazione_id' => 'prestazioneId',
        'prestazione' => 'prestazione',
        'importo' => 'importo',
        'sconto1' => 'sconto1',
        'sconto2' => 'sconto2',
        'sconto3' => 'sconto3',
        'sconto4' => 'sconto4',
        'ricarico' => 'ricarico',
        'provvigione_agente' => 'provvigioneAgente',
        'provvigione_capo_area' => 'provvigioneCapoArea'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'id' => 'setId',
        'concurrency_token' => 'setConcurrencyToken',
        'listino_id' => 'setListinoId',
        'articolo_id' => 'setArticoloId',
        'articolo' => 'setArticolo',
        'prestazione_id' => 'setPrestazioneId',
        'prestazione' => 'setPrestazione',
        'importo' => 'setImporto',
        'sconto1' => 'setSconto1',
        'sconto2' => 'setSconto2',
        'sconto3' => 'setSconto3',
        'sconto4' => 'setSconto4',
        'ricarico' => 'setRicarico',
        'provvigione_agente' => 'setProvvigioneAgente',
        'provvigione_capo_area' => 'setProvvigioneCapoArea'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'id' => 'getId',
        'concurrency_token' => 'getConcurrencyToken',
        'listino_id' => 'getListinoId',
        'articolo_id' => 'getArticoloId',
        'articolo' => 'getArticolo',
        'prestazione_id' => 'getPrestazioneId',
        'prestazione' => 'getPrestazione',
        'importo' => 'getImporto',
        'sconto1' => 'getSconto1',
        'sconto2' => 'getSconto2',
        'sconto3' => 'getSconto3',
        'sconto4' => 'getSconto4',
        'ricarico' => 'getRicarico',
        'provvigione_agente' => 'getProvvigioneAgente',
        'provvigione_capo_area' => 'getProvvigioneCapoArea'
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
        $this->setIfExists('listino_id', $data ?? [], null);
        $this->setIfExists('articolo_id', $data ?? [], null);
        $this->setIfExists('articolo', $data ?? [], null);
        $this->setIfExists('prestazione_id', $data ?? [], null);
        $this->setIfExists('prestazione', $data ?? [], null);
        $this->setIfExists('importo', $data ?? [], null);
        $this->setIfExists('sconto1', $data ?? [], null);
        $this->setIfExists('sconto2', $data ?? [], null);
        $this->setIfExists('sconto3', $data ?? [], null);
        $this->setIfExists('sconto4', $data ?? [], null);
        $this->setIfExists('ricarico', $data ?? [], null);
        $this->setIfExists('provvigione_agente', $data ?? [], null);
        $this->setIfExists('provvigione_capo_area', $data ?? [], null);
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
     * @param int|null $articolo_id Identificativo univoco di `Articolo`. In alternativa, usare `PrestazioneId`.
     *
     * @return self
     */
    public function setArticoloId($articolo_id)
    {
        if (is_null($articolo_id)) {
            array_push($this->openAPINullablesSetToNull, 'articolo_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('articolo_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
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
     * Gets prestazione_id
     *
     * @return int|null
     */
    public function getPrestazioneId()
    {
        return $this->container['prestazione_id'];
    }

    /**
     * Sets prestazione_id
     *
     * @param int|null $prestazione_id Identificativo univoco di `Prestazione`. In alternativa, usare `ArticoloId`.
     *
     * @return self
     */
    public function setPrestazioneId($prestazione_id)
    {
        if (is_null($prestazione_id)) {
            array_push($this->openAPINullablesSetToNull, 'prestazione_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('prestazione_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['prestazione_id'] = $prestazione_id;

        return $this;
    }

    /**
     * Gets prestazione
     *
     * @return \Amica\Sdk\Model\Articolo|null
     */
    public function getPrestazione()
    {
        return $this->container['prestazione'];
    }

    /**
     * Sets prestazione
     *
     * @param \Amica\Sdk\Model\Articolo|null $prestazione prestazione
     *
     * @return self
     */
    public function setPrestazione($prestazione)
    {
        if (is_null($prestazione)) {
            throw new \InvalidArgumentException('non-nullable prestazione cannot be null');
        }
        $this->container['prestazione'] = $prestazione;

        return $this;
    }

    /**
     * Gets importo
     *
     * @return float|null
     */
    public function getImporto()
    {
        return $this->container['importo'];
    }

    /**
     * Sets importo
     *
     * @param float|null $importo Importo (prezzo).
     *
     * @return self
     */
    public function setImporto($importo)
    {
        if (is_null($importo)) {
            throw new \InvalidArgumentException('non-nullable importo cannot be null');
        }
        $this->container['importo'] = $importo;

        return $this;
    }

    /**
     * Gets sconto1
     *
     * @return float|null
     */
    public function getSconto1()
    {
        return $this->container['sconto1'];
    }

    /**
     * Sets sconto1
     *
     * @param float|null $sconto1 Primo sconto percentuale, applicato sul prezzo del bene o della prestazione.
     *
     * @return self
     */
    public function setSconto1($sconto1)
    {
        if (is_null($sconto1)) {
            throw new \InvalidArgumentException('non-nullable sconto1 cannot be null');
        }
        $this->container['sconto1'] = $sconto1;

        return $this;
    }

    /**
     * Gets sconto2
     *
     * @return float|null
     */
    public function getSconto2()
    {
        return $this->container['sconto2'];
    }

    /**
     * Sets sconto2
     *
     * @param float|null $sconto2 Secondo sconto percentuale, applicato a seguito dell'applicazione di `Sconto1`
     *
     * @return self
     */
    public function setSconto2($sconto2)
    {
        if (is_null($sconto2)) {
            throw new \InvalidArgumentException('non-nullable sconto2 cannot be null');
        }
        $this->container['sconto2'] = $sconto2;

        return $this;
    }

    /**
     * Gets sconto3
     *
     * @return float|null
     */
    public function getSconto3()
    {
        return $this->container['sconto3'];
    }

    /**
     * Sets sconto3
     *
     * @param float|null $sconto3 Terzo sconto percentuale, applicato a seguito dell'applicazione di `Sconto2`
     *
     * @return self
     */
    public function setSconto3($sconto3)
    {
        if (is_null($sconto3)) {
            throw new \InvalidArgumentException('non-nullable sconto3 cannot be null');
        }
        $this->container['sconto3'] = $sconto3;

        return $this;
    }

    /**
     * Gets sconto4
     *
     * @return float|null
     */
    public function getSconto4()
    {
        return $this->container['sconto4'];
    }

    /**
     * Sets sconto4
     *
     * @param float|null $sconto4 Quarto sconto percentuale, applicato a seguito dell'applicazione di `Sconto3`
     *
     * @return self
     */
    public function setSconto4($sconto4)
    {
        if (is_null($sconto4)) {
            throw new \InvalidArgumentException('non-nullable sconto4 cannot be null');
        }
        $this->container['sconto4'] = $sconto4;

        return $this;
    }

    /**
     * Gets ricarico
     *
     * @return float|null
     */
    public function getRicarico()
    {
        return $this->container['ricarico'];
    }

    /**
     * Sets ricarico
     *
     * @param float|null $ricarico Ricarico (percentuale) da applicare sul costo del prodotto (o della prestazione). Se utilizzato, `Importo` viene ignorato.
     *
     * @return self
     */
    public function setRicarico($ricarico)
    {
        if (is_null($ricarico)) {
            throw new \InvalidArgumentException('non-nullable ricarico cannot be null');
        }
        $this->container['ricarico'] = $ricarico;

        return $this;
    }

    /**
     * Gets provvigione_agente
     *
     * @return float|null
     */
    public function getProvvigioneAgente()
    {
        return $this->container['provvigione_agente'];
    }

    /**
     * Sets provvigione_agente
     *
     * @param float|null $provvigione_agente Provvigione corrisposta all'agente, in percentuale.
     *
     * @return self
     */
    public function setProvvigioneAgente($provvigione_agente)
    {
        if (is_null($provvigione_agente)) {
            array_push($this->openAPINullablesSetToNull, 'provvigione_agente');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('provvigione_agente', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['provvigione_agente'] = $provvigione_agente;

        return $this;
    }

    /**
     * Gets provvigione_capo_area
     *
     * @return float|null
     */
    public function getProvvigioneCapoArea()
    {
        return $this->container['provvigione_capo_area'];
    }

    /**
     * Sets provvigione_capo_area
     *
     * @param float|null $provvigione_capo_area Provvigione corrisposta al capo area, in percentuale.
     *
     * @return self
     */
    public function setProvvigioneCapoArea($provvigione_capo_area)
    {
        if (is_null($provvigione_capo_area)) {
            array_push($this->openAPINullablesSetToNull, 'provvigione_capo_area');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('provvigione_capo_area', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['provvigione_capo_area'] = $provvigione_capo_area;

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


