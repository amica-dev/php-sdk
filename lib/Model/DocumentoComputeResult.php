<?php
/**
 * DocumentoComputeResult
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
 * The version of the OpenAPI document: 3.2.2
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
 * DocumentoComputeResult Class Doc Comment
 *
 * @category Class
 * @description Restituito dal metodo &#x60;Compute&#x60; di &#x60;DocumentoService&#x60;.
 * @package  Amica\Sdk
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class DocumentoComputeResult implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'DocumentoComputeResult';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'totale_merce' => 'float',
        'totale_merce_scontata' => 'float',
        'totale_imponibile' => 'float',
        'totale_spese_esenti' => 'float',
        'totale_spese_non_esenti' => 'float',
        'totale_spese' => 'float',
        'totale_ritenuta' => 'float',
        'totale_cassa_previdenziale' => 'float',
        'enasarco_importo' => 'float',
        'totale_esente' => 'float',
        'totale_imposta' => 'float',
        'totale_split_payment' => 'float',
        'totale_abbuoni' => 'float',
        'totale_documento' => 'float',
        'bonus_fiscale_importo' => 'float',
        'netto_a_pagare' => 'float',
        'riepiloghi_iva' => '\Amica\Sdk\Model\DocumentoComputeResultRiepilogoIva[]'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'totale_merce' => 'double',
        'totale_merce_scontata' => 'double',
        'totale_imponibile' => 'double',
        'totale_spese_esenti' => 'double',
        'totale_spese_non_esenti' => 'double',
        'totale_spese' => 'double',
        'totale_ritenuta' => 'double',
        'totale_cassa_previdenziale' => 'double',
        'enasarco_importo' => 'double',
        'totale_esente' => 'double',
        'totale_imposta' => 'double',
        'totale_split_payment' => 'double',
        'totale_abbuoni' => 'double',
        'totale_documento' => 'double',
        'bonus_fiscale_importo' => 'double',
        'netto_a_pagare' => 'double',
        'riepiloghi_iva' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'totale_merce' => false,
        'totale_merce_scontata' => false,
        'totale_imponibile' => false,
        'totale_spese_esenti' => false,
        'totale_spese_non_esenti' => false,
        'totale_spese' => false,
        'totale_ritenuta' => false,
        'totale_cassa_previdenziale' => false,
        'enasarco_importo' => false,
        'totale_esente' => false,
        'totale_imposta' => false,
        'totale_split_payment' => false,
        'totale_abbuoni' => false,
        'totale_documento' => false,
        'bonus_fiscale_importo' => false,
        'netto_a_pagare' => false,
        'riepiloghi_iva' => true
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
        'totale_merce' => 'totaleMerce',
        'totale_merce_scontata' => 'totaleMerceScontata',
        'totale_imponibile' => 'totaleImponibile',
        'totale_spese_esenti' => 'totaleSpeseEsenti',
        'totale_spese_non_esenti' => 'totaleSpeseNonEsenti',
        'totale_spese' => 'totaleSpese',
        'totale_ritenuta' => 'totaleRitenuta',
        'totale_cassa_previdenziale' => 'totaleCassaPrevidenziale',
        'enasarco_importo' => 'enasarcoImporto',
        'totale_esente' => 'totaleEsente',
        'totale_imposta' => 'totaleImposta',
        'totale_split_payment' => 'totaleSplitPayment',
        'totale_abbuoni' => 'totaleAbbuoni',
        'totale_documento' => 'totaleDocumento',
        'bonus_fiscale_importo' => 'bonusFiscaleImporto',
        'netto_a_pagare' => 'nettoAPagare',
        'riepiloghi_iva' => 'riepiloghiIva'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'totale_merce' => 'setTotaleMerce',
        'totale_merce_scontata' => 'setTotaleMerceScontata',
        'totale_imponibile' => 'setTotaleImponibile',
        'totale_spese_esenti' => 'setTotaleSpeseEsenti',
        'totale_spese_non_esenti' => 'setTotaleSpeseNonEsenti',
        'totale_spese' => 'setTotaleSpese',
        'totale_ritenuta' => 'setTotaleRitenuta',
        'totale_cassa_previdenziale' => 'setTotaleCassaPrevidenziale',
        'enasarco_importo' => 'setEnasarcoImporto',
        'totale_esente' => 'setTotaleEsente',
        'totale_imposta' => 'setTotaleImposta',
        'totale_split_payment' => 'setTotaleSplitPayment',
        'totale_abbuoni' => 'setTotaleAbbuoni',
        'totale_documento' => 'setTotaleDocumento',
        'bonus_fiscale_importo' => 'setBonusFiscaleImporto',
        'netto_a_pagare' => 'setNettoAPagare',
        'riepiloghi_iva' => 'setRiepiloghiIva'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'totale_merce' => 'getTotaleMerce',
        'totale_merce_scontata' => 'getTotaleMerceScontata',
        'totale_imponibile' => 'getTotaleImponibile',
        'totale_spese_esenti' => 'getTotaleSpeseEsenti',
        'totale_spese_non_esenti' => 'getTotaleSpeseNonEsenti',
        'totale_spese' => 'getTotaleSpese',
        'totale_ritenuta' => 'getTotaleRitenuta',
        'totale_cassa_previdenziale' => 'getTotaleCassaPrevidenziale',
        'enasarco_importo' => 'getEnasarcoImporto',
        'totale_esente' => 'getTotaleEsente',
        'totale_imposta' => 'getTotaleImposta',
        'totale_split_payment' => 'getTotaleSplitPayment',
        'totale_abbuoni' => 'getTotaleAbbuoni',
        'totale_documento' => 'getTotaleDocumento',
        'bonus_fiscale_importo' => 'getBonusFiscaleImporto',
        'netto_a_pagare' => 'getNettoAPagare',
        'riepiloghi_iva' => 'getRiepiloghiIva'
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
        $this->setIfExists('totale_merce', $data ?? [], null);
        $this->setIfExists('totale_merce_scontata', $data ?? [], null);
        $this->setIfExists('totale_imponibile', $data ?? [], null);
        $this->setIfExists('totale_spese_esenti', $data ?? [], null);
        $this->setIfExists('totale_spese_non_esenti', $data ?? [], null);
        $this->setIfExists('totale_spese', $data ?? [], null);
        $this->setIfExists('totale_ritenuta', $data ?? [], null);
        $this->setIfExists('totale_cassa_previdenziale', $data ?? [], null);
        $this->setIfExists('enasarco_importo', $data ?? [], null);
        $this->setIfExists('totale_esente', $data ?? [], null);
        $this->setIfExists('totale_imposta', $data ?? [], null);
        $this->setIfExists('totale_split_payment', $data ?? [], null);
        $this->setIfExists('totale_abbuoni', $data ?? [], null);
        $this->setIfExists('totale_documento', $data ?? [], null);
        $this->setIfExists('bonus_fiscale_importo', $data ?? [], null);
        $this->setIfExists('netto_a_pagare', $data ?? [], null);
        $this->setIfExists('riepiloghi_iva', $data ?? [], null);
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
     * Gets totale_merce
     *
     * @return float|null
     */
    public function getTotaleMerce()
    {
        return $this->container['totale_merce'];
    }

    /**
     * Sets totale_merce
     *
     * @param float|null $totale_merce Importo della merce.
     *
     * @return self
     */
    public function setTotaleMerce($totale_merce)
    {
        if (is_null($totale_merce)) {
            throw new \InvalidArgumentException('non-nullable totale_merce cannot be null');
        }
        $this->container['totale_merce'] = $totale_merce;

        return $this;
    }

    /**
     * Gets totale_merce_scontata
     *
     * @return float|null
     */
    public function getTotaleMerceScontata()
    {
        return $this->container['totale_merce_scontata'];
    }

    /**
     * Sets totale_merce_scontata
     *
     * @param float|null $totale_merce_scontata Importo della merce scontata.
     *
     * @return self
     */
    public function setTotaleMerceScontata($totale_merce_scontata)
    {
        if (is_null($totale_merce_scontata)) {
            throw new \InvalidArgumentException('non-nullable totale_merce_scontata cannot be null');
        }
        $this->container['totale_merce_scontata'] = $totale_merce_scontata;

        return $this;
    }

    /**
     * Gets totale_imponibile
     *
     * @return float|null
     */
    public function getTotaleImponibile()
    {
        return $this->container['totale_imponibile'];
    }

    /**
     * Sets totale_imponibile
     *
     * @param float|null $totale_imponibile Totale imponibile, considerando tutte le causali IVA coinvolte.
     *
     * @return self
     */
    public function setTotaleImponibile($totale_imponibile)
    {
        if (is_null($totale_imponibile)) {
            throw new \InvalidArgumentException('non-nullable totale_imponibile cannot be null');
        }
        $this->container['totale_imponibile'] = $totale_imponibile;

        return $this;
    }

    /**
     * Gets totale_spese_esenti
     *
     * @return float|null
     */
    public function getTotaleSpeseEsenti()
    {
        return $this->container['totale_spese_esenti'];
    }

    /**
     * Sets totale_spese_esenti
     *
     * @param float|null $totale_spese_esenti Totale delle spese esenti da IVA.
     *
     * @return self
     */
    public function setTotaleSpeseEsenti($totale_spese_esenti)
    {
        if (is_null($totale_spese_esenti)) {
            throw new \InvalidArgumentException('non-nullable totale_spese_esenti cannot be null');
        }
        $this->container['totale_spese_esenti'] = $totale_spese_esenti;

        return $this;
    }

    /**
     * Gets totale_spese_non_esenti
     *
     * @return float|null
     */
    public function getTotaleSpeseNonEsenti()
    {
        return $this->container['totale_spese_non_esenti'];
    }

    /**
     * Sets totale_spese_non_esenti
     *
     * @param float|null $totale_spese_non_esenti Totale delle spese non esenti da IVA.
     *
     * @return self
     */
    public function setTotaleSpeseNonEsenti($totale_spese_non_esenti)
    {
        if (is_null($totale_spese_non_esenti)) {
            throw new \InvalidArgumentException('non-nullable totale_spese_non_esenti cannot be null');
        }
        $this->container['totale_spese_non_esenti'] = $totale_spese_non_esenti;

        return $this;
    }

    /**
     * Gets totale_spese
     *
     * @return float|null
     */
    public function getTotaleSpese()
    {
        return $this->container['totale_spese'];
    }

    /**
     * Sets totale_spese
     *
     * @param float|null $totale_spese Totale delle spese esenti e non esenti.
     *
     * @return self
     */
    public function setTotaleSpese($totale_spese)
    {
        if (is_null($totale_spese)) {
            throw new \InvalidArgumentException('non-nullable totale_spese cannot be null');
        }
        $this->container['totale_spese'] = $totale_spese;

        return $this;
    }

    /**
     * Gets totale_ritenuta
     *
     * @return float|null
     */
    public function getTotaleRitenuta()
    {
        return $this->container['totale_ritenuta'];
    }

    /**
     * Sets totale_ritenuta
     *
     * @param float|null $totale_ritenuta Importo della ritenuta di acconto.
     *
     * @return self
     */
    public function setTotaleRitenuta($totale_ritenuta)
    {
        if (is_null($totale_ritenuta)) {
            throw new \InvalidArgumentException('non-nullable totale_ritenuta cannot be null');
        }
        $this->container['totale_ritenuta'] = $totale_ritenuta;

        return $this;
    }

    /**
     * Gets totale_cassa_previdenziale
     *
     * @return float|null
     */
    public function getTotaleCassaPrevidenziale()
    {
        return $this->container['totale_cassa_previdenziale'];
    }

    /**
     * Sets totale_cassa_previdenziale
     *
     * @param float|null $totale_cassa_previdenziale Importo della cassa previdenziale.
     *
     * @return self
     */
    public function setTotaleCassaPrevidenziale($totale_cassa_previdenziale)
    {
        if (is_null($totale_cassa_previdenziale)) {
            throw new \InvalidArgumentException('non-nullable totale_cassa_previdenziale cannot be null');
        }
        $this->container['totale_cassa_previdenziale'] = $totale_cassa_previdenziale;

        return $this;
    }

    /**
     * Gets enasarco_importo
     *
     * @return float|null
     */
    public function getEnasarcoImporto()
    {
        return $this->container['enasarco_importo'];
    }

    /**
     * Sets enasarco_importo
     *
     * @param float|null $enasarco_importo Importo Enasarco.
     *
     * @return self
     */
    public function setEnasarcoImporto($enasarco_importo)
    {
        if (is_null($enasarco_importo)) {
            throw new \InvalidArgumentException('non-nullable enasarco_importo cannot be null');
        }
        $this->container['enasarco_importo'] = $enasarco_importo;

        return $this;
    }

    /**
     * Gets totale_esente
     *
     * @return float|null
     */
    public function getTotaleEsente()
    {
        return $this->container['totale_esente'];
    }

    /**
     * Sets totale_esente
     *
     * @param float|null $totale_esente Importi esenti da IVA.
     *
     * @return self
     */
    public function setTotaleEsente($totale_esente)
    {
        if (is_null($totale_esente)) {
            throw new \InvalidArgumentException('non-nullable totale_esente cannot be null');
        }
        $this->container['totale_esente'] = $totale_esente;

        return $this;
    }

    /**
     * Gets totale_imposta
     *
     * @return float|null
     */
    public function getTotaleImposta()
    {
        return $this->container['totale_imposta'];
    }

    /**
     * Sets totale_imposta
     *
     * @param float|null $totale_imposta Imposta IVA.
     *
     * @return self
     */
    public function setTotaleImposta($totale_imposta)
    {
        if (is_null($totale_imposta)) {
            throw new \InvalidArgumentException('non-nullable totale_imposta cannot be null');
        }
        $this->container['totale_imposta'] = $totale_imposta;

        return $this;
    }

    /**
     * Gets totale_split_payment
     *
     * @return float|null
     */
    public function getTotaleSplitPayment()
    {
        return $this->container['totale_split_payment'];
    }

    /**
     * Sets totale_split_payment
     *
     * @param float|null $totale_split_payment Importo dello split payment.
     *
     * @return self
     */
    public function setTotaleSplitPayment($totale_split_payment)
    {
        if (is_null($totale_split_payment)) {
            throw new \InvalidArgumentException('non-nullable totale_split_payment cannot be null');
        }
        $this->container['totale_split_payment'] = $totale_split_payment;

        return $this;
    }

    /**
     * Gets totale_abbuoni
     *
     * @return float|null
     */
    public function getTotaleAbbuoni()
    {
        return $this->container['totale_abbuoni'];
    }

    /**
     * Sets totale_abbuoni
     *
     * @param float|null $totale_abbuoni Importo degli abbuoni.
     *
     * @return self
     */
    public function setTotaleAbbuoni($totale_abbuoni)
    {
        if (is_null($totale_abbuoni)) {
            throw new \InvalidArgumentException('non-nullable totale_abbuoni cannot be null');
        }
        $this->container['totale_abbuoni'] = $totale_abbuoni;

        return $this;
    }

    /**
     * Gets totale_documento
     *
     * @return float|null
     */
    public function getTotaleDocumento()
    {
        return $this->container['totale_documento'];
    }

    /**
     * Sets totale_documento
     *
     * @param float|null $totale_documento Importo totale del documento.
     *
     * @return self
     */
    public function setTotaleDocumento($totale_documento)
    {
        if (is_null($totale_documento)) {
            throw new \InvalidArgumentException('non-nullable totale_documento cannot be null');
        }
        $this->container['totale_documento'] = $totale_documento;

        return $this;
    }

    /**
     * Gets bonus_fiscale_importo
     *
     * @return float|null
     */
    public function getBonusFiscaleImporto()
    {
        return $this->container['bonus_fiscale_importo'];
    }

    /**
     * Sets bonus_fiscale_importo
     *
     * @param float|null $bonus_fiscale_importo Importo del bonus fiscale.
     *
     * @return self
     */
    public function setBonusFiscaleImporto($bonus_fiscale_importo)
    {
        if (is_null($bonus_fiscale_importo)) {
            throw new \InvalidArgumentException('non-nullable bonus_fiscale_importo cannot be null');
        }
        $this->container['bonus_fiscale_importo'] = $bonus_fiscale_importo;

        return $this;
    }

    /**
     * Gets netto_a_pagare
     *
     * @return float|null
     */
    public function getNettoAPagare()
    {
        return $this->container['netto_a_pagare'];
    }

    /**
     * Sets netto_a_pagare
     *
     * @param float|null $netto_a_pagare Netto a pagare.
     *
     * @return self
     */
    public function setNettoAPagare($netto_a_pagare)
    {
        if (is_null($netto_a_pagare)) {
            throw new \InvalidArgumentException('non-nullable netto_a_pagare cannot be null');
        }
        $this->container['netto_a_pagare'] = $netto_a_pagare;

        return $this;
    }

    /**
     * Gets riepiloghi_iva
     *
     * @return \Amica\Sdk\Model\DocumentoComputeResultRiepilogoIva[]|null
     */
    public function getRiepiloghiIva()
    {
        return $this->container['riepiloghi_iva'];
    }

    /**
     * Sets riepiloghi_iva
     *
     * @param \Amica\Sdk\Model\DocumentoComputeResultRiepilogoIva[]|null $riepiloghi_iva Riepiloghi IVA.
     *
     * @return self
     */
    public function setRiepiloghiIva($riepiloghi_iva)
    {
        if (is_null($riepiloghi_iva)) {
            array_push($this->openAPINullablesSetToNull, 'riepiloghi_iva');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('riepiloghi_iva', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['riepiloghi_iva'] = $riepiloghi_iva;

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


