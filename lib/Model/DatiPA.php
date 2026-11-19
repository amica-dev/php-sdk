<?php
/**
 * DatiPA
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
 * DatiPA Class Doc Comment
 *
 * @category Class
 * @description Dati per la pubblica amministrazione.
 * @package  Amica\Sdk
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class DatiPA implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'DatiPA';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'codice_commessa_convenzione_pa' => 'string',
        'codice_cup' => 'string',
        'codice_cig' => 'string',
        'numero_ordine_pa' => 'string',
        'data_ordine_pa' => '\DateTime'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'codice_commessa_convenzione_pa' => null,
        'codice_cup' => null,
        'codice_cig' => null,
        'numero_ordine_pa' => null,
        'data_ordine_pa' => 'date-time'
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'codice_commessa_convenzione_pa' => true,
        'codice_cup' => true,
        'codice_cig' => true,
        'numero_ordine_pa' => true,
        'data_ordine_pa' => true
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
        'codice_commessa_convenzione_pa' => 'codiceCommessaConvenzionePA',
        'codice_cup' => 'codiceCup',
        'codice_cig' => 'codiceCig',
        'numero_ordine_pa' => 'numeroOrdinePA',
        'data_ordine_pa' => 'dataOrdinePA'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'codice_commessa_convenzione_pa' => 'setCodiceCommessaConvenzionePa',
        'codice_cup' => 'setCodiceCup',
        'codice_cig' => 'setCodiceCig',
        'numero_ordine_pa' => 'setNumeroOrdinePa',
        'data_ordine_pa' => 'setDataOrdinePa'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'codice_commessa_convenzione_pa' => 'getCodiceCommessaConvenzionePa',
        'codice_cup' => 'getCodiceCup',
        'codice_cig' => 'getCodiceCig',
        'numero_ordine_pa' => 'getNumeroOrdinePa',
        'data_ordine_pa' => 'getDataOrdinePa'
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
        $this->setIfExists('codice_commessa_convenzione_pa', $data ?? [], null);
        $this->setIfExists('codice_cup', $data ?? [], null);
        $this->setIfExists('codice_cig', $data ?? [], null);
        $this->setIfExists('numero_ordine_pa', $data ?? [], null);
        $this->setIfExists('data_ordine_pa', $data ?? [], null);
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

        if (!is_null($this->container['codice_commessa_convenzione_pa']) && (mb_strlen($this->container['codice_commessa_convenzione_pa']) > 100)) {
            $invalidProperties[] = "invalid value for 'codice_commessa_convenzione_pa', the character length must be smaller than or equal to 100.";
        }

        if (!is_null($this->container['codice_commessa_convenzione_pa']) && (mb_strlen($this->container['codice_commessa_convenzione_pa']) < 0)) {
            $invalidProperties[] = "invalid value for 'codice_commessa_convenzione_pa', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['codice_cup']) && (mb_strlen($this->container['codice_cup']) > 15)) {
            $invalidProperties[] = "invalid value for 'codice_cup', the character length must be smaller than or equal to 15.";
        }

        if (!is_null($this->container['codice_cup']) && (mb_strlen($this->container['codice_cup']) < 0)) {
            $invalidProperties[] = "invalid value for 'codice_cup', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['codice_cig']) && (mb_strlen($this->container['codice_cig']) > 15)) {
            $invalidProperties[] = "invalid value for 'codice_cig', the character length must be smaller than or equal to 15.";
        }

        if (!is_null($this->container['codice_cig']) && (mb_strlen($this->container['codice_cig']) < 0)) {
            $invalidProperties[] = "invalid value for 'codice_cig', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['numero_ordine_pa']) && (mb_strlen($this->container['numero_ordine_pa']) > 20)) {
            $invalidProperties[] = "invalid value for 'numero_ordine_pa', the character length must be smaller than or equal to 20.";
        }

        if (!is_null($this->container['numero_ordine_pa']) && (mb_strlen($this->container['numero_ordine_pa']) < 0)) {
            $invalidProperties[] = "invalid value for 'numero_ordine_pa', the character length must be bigger than or equal to 0.";
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
     * Gets codice_commessa_convenzione_pa
     *
     * @return string|null
     */
    public function getCodiceCommessaConvenzionePa()
    {
        return $this->container['codice_commessa_convenzione_pa'];
    }

    /**
     * Sets codice_commessa_convenzione_pa
     *
     * @param string|null $codice_commessa_convenzione_pa Identificativo del contratto o della convenzione assegnato dalla pubblica amministrazione.
     *
     * @return self
     */
    public function setCodiceCommessaConvenzionePa($codice_commessa_convenzione_pa)
    {
        if (is_null($codice_commessa_convenzione_pa)) {
            array_push($this->openAPINullablesSetToNull, 'codice_commessa_convenzione_pa');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('codice_commessa_convenzione_pa', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($codice_commessa_convenzione_pa) && (mb_strlen($codice_commessa_convenzione_pa) > 100)) {
            throw new \InvalidArgumentException('invalid length for $codice_commessa_convenzione_pa when calling DatiPA., must be smaller than or equal to 100.');
        }
        if (!is_null($codice_commessa_convenzione_pa) && (mb_strlen($codice_commessa_convenzione_pa) < 0)) {
            throw new \InvalidArgumentException('invalid length for $codice_commessa_convenzione_pa when calling DatiPA., must be bigger than or equal to 0.');
        }

        $this->container['codice_commessa_convenzione_pa'] = $codice_commessa_convenzione_pa;

        return $this;
    }

    /**
     * Gets codice_cup
     *
     * @return string|null
     */
    public function getCodiceCup()
    {
        return $this->container['codice_cup'];
    }

    /**
     * Sets codice_cup
     *
     * @param string|null $codice_cup Codice CUP assegnato dalla pubblica amministrazione.
     *
     * @return self
     */
    public function setCodiceCup($codice_cup)
    {
        if (is_null($codice_cup)) {
            array_push($this->openAPINullablesSetToNull, 'codice_cup');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('codice_cup', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($codice_cup) && (mb_strlen($codice_cup) > 15)) {
            throw new \InvalidArgumentException('invalid length for $codice_cup when calling DatiPA., must be smaller than or equal to 15.');
        }
        if (!is_null($codice_cup) && (mb_strlen($codice_cup) < 0)) {
            throw new \InvalidArgumentException('invalid length for $codice_cup when calling DatiPA., must be bigger than or equal to 0.');
        }

        $this->container['codice_cup'] = $codice_cup;

        return $this;
    }

    /**
     * Gets codice_cig
     *
     * @return string|null
     */
    public function getCodiceCig()
    {
        return $this->container['codice_cig'];
    }

    /**
     * Sets codice_cig
     *
     * @param string|null $codice_cig Codice CIG assegnato dalla pubblica amministrazione.
     *
     * @return self
     */
    public function setCodiceCig($codice_cig)
    {
        if (is_null($codice_cig)) {
            array_push($this->openAPINullablesSetToNull, 'codice_cig');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('codice_cig', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($codice_cig) && (mb_strlen($codice_cig) > 15)) {
            throw new \InvalidArgumentException('invalid length for $codice_cig when calling DatiPA., must be smaller than or equal to 15.');
        }
        if (!is_null($codice_cig) && (mb_strlen($codice_cig) < 0)) {
            throw new \InvalidArgumentException('invalid length for $codice_cig when calling DatiPA., must be bigger than or equal to 0.');
        }

        $this->container['codice_cig'] = $codice_cig;

        return $this;
    }

    /**
     * Gets numero_ordine_pa
     *
     * @return string|null
     */
    public function getNumeroOrdinePa()
    {
        return $this->container['numero_ordine_pa'];
    }

    /**
     * Sets numero_ordine_pa
     *
     * @param string|null $numero_ordine_pa Numero d'ordine della pubblica amministrazione.
     *
     * @return self
     */
    public function setNumeroOrdinePa($numero_ordine_pa)
    {
        if (is_null($numero_ordine_pa)) {
            array_push($this->openAPINullablesSetToNull, 'numero_ordine_pa');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('numero_ordine_pa', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($numero_ordine_pa) && (mb_strlen($numero_ordine_pa) > 20)) {
            throw new \InvalidArgumentException('invalid length for $numero_ordine_pa when calling DatiPA., must be smaller than or equal to 20.');
        }
        if (!is_null($numero_ordine_pa) && (mb_strlen($numero_ordine_pa) < 0)) {
            throw new \InvalidArgumentException('invalid length for $numero_ordine_pa when calling DatiPA., must be bigger than or equal to 0.');
        }

        $this->container['numero_ordine_pa'] = $numero_ordine_pa;

        return $this;
    }

    /**
     * Gets data_ordine_pa
     *
     * @return \DateTime|null
     */
    public function getDataOrdinePa()
    {
        return $this->container['data_ordine_pa'];
    }

    /**
     * Sets data_ordine_pa
     *
     * @param \DateTime|null $data_ordine_pa Data dell'ordine della pubblica amministrazione.
     *
     * @return self
     */
    public function setDataOrdinePa($data_ordine_pa)
    {
        if (is_null($data_ordine_pa)) {
            array_push($this->openAPINullablesSetToNull, 'data_ordine_pa');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('data_ordine_pa', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['data_ordine_pa'] = $data_ordine_pa;

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


