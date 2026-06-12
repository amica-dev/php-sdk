<?php
/**
 * DocumentoComputeData
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
 * L'Amica Cloud API fornisce accesso programmatico a tutti i dati aziendali gestiti da [Amica 20](https://gestionaleamica.com). Supporta il ciclo completo di documenti, fatture, magazzino, contabilità e anagrafiche. Per guide e documentazione completa, visitare [amica.dev](https://amica.dev/).  ## Autenticazione  Tutte le richieste API richiedono un token OAuth 2.0 Bearer valido, da includere nell'header `Authorization`:  ``` Authorization: Bearer <token> ```  ### Registra la tua app  Per integrare l'Amica Cloud API registra la tua applicazione sull'**App Store di Amica**. Al termine della registrazione ottieni una coppia di credenziali OAuth 2.0 — `client_id` e `client_secret` (il secret è mostrato **una sola volta**) — e scegli gli **scope** e il **flusso di autenticazione** desiderati.  ### Scegli il flusso  - **Authorization Code con PKCE** (consigliato per app che operano per conto di un utente Amica): l'utente autorizza la tua app tramite la pagina di consenso dell'identity provider; la tua app riceve un `code` da scambiare con un access token.    ```   GET  {identity-provider}/connect/authorize?response_type=code&client_id=<client_id>&redirect_uri=<redirect_uri>&scope=openid%20read%20write%20documenti&code_challenge=<challenge>&code_challenge_method=S256   POST {identity-provider}/connect/token        grant_type=authorization_code&code=<code>&code_verifier=<verifier>&client_id=<client_id>&client_secret=<client_secret>&redirect_uri=<redirect_uri>   ```  - **Client Credentials** (per integrazioni server-to-server, senza utente interattivo):    ```   POST {identity-provider}/connect/token   Content-Type: application/x-www-form-urlencoded    grant_type=client_credentials&client_id=<client_id>&client_secret=<client_secret>&scope=read write documenti contabilita   ```    > **Nota:** il flusso Client Credentials è disponibile solo per le **app private**, non per quelle pubblicate sul marketplace.  In entrambi i casi la risposta contiene l'`access_token` (JWT con `aud: amica-cloud`) da usare nelle chiamate successive finché non scade (`expires_in`), dopodiché va richiesto un nuovo token. Ogni token contiene uno o più **scope** che determinano le aree accessibili (vedi sotto). Dalla [API Reference](https://amica.dev) è possibile incollare un access token per provare gli endpoint direttamente dal browser.  ## Scope di autorizzazione  L'accesso agli endpoint è governato dagli scope presenti nel token OAuth:  | Scope | Descrizione | |-|-| | `comune` | Anagrafiche: contatti, indirizzi, nazioni, valute, modalità di pagamento, ecc. | | `magazzino` | Magazzino: articoli, listini, movimenti di magazzino, giacenze, distinte base. | | `contabilita` | Contabilità: registrazioni contabili, scadenze, castelletti IVA, fatture elettroniche XML. | | `documenti` | Documenti: fatture, note di credito, DDT, ordini, preventivi, proforma ed emissione. |  Una richiesta verso un endpoint non coperto dallo scope del token riceverà una risposta `403 Forbidden`.  ## Company ID  La maggior parte degli endpoint è relativa a una specifica azienda e segue il pattern:  ``` /api/v3/{companyId}/risorsa ```  Il `companyId` è l'identificativo univoco di ciascun database aziendale. Usare l'endpoint `GET /api/v3/config/Azienda` per ottenere l'elenco delle aziende disponibili.  ## Paginazione  Gli endpoint che restituiscono liste supportano la paginazione tramite query parameter:  | Parametro | Descrizione | |-|-| | `Skip` | Numero di elementi da saltare dall'inizio del set di risultati. | | `Take` | Numero di elementi da includere nella risposta. |  La risposta include due header:  | Header | Descrizione | |-|-| | `X-Read-TotalCount` | Numero totale di elementi che soddisfano il filtro corrente. | | `X-Read-Count` | Numero di elementi effettivamente restituiti nel body della risposta. |  ## Ordinamento e filtri  Gli endpoint lista accettano parametri aggiuntivi:  | Parametro | Descrizione | |-|-| | `Order` | Lista di proprietà per l'ordinamento, separate da virgola. Un segno meno (`-`) prima del nome indica ordine discendente. Es. `-RagioneSociale1,Citta`. | | `Filter` | Condizioni di ricerca: lista di espressioni separate da virgola, ciascuna formata da nome campo, operatore, valore ed eventuale connettore logico. Operatori: `$eq`, `$ne`, `$gt`, `$lt`, `$lte`, `$gte`, `$ct` (contiene), `$nct`, `$sw` (inizia con), `$ew` (finisce con). Connettori: `$and` (default, omissibile) e `$or`; le sotto-query si racchiudono tra `[]`. Es. `RagioneSociale1 $ct acme $or, Fido $gt 1000`. | | `SkipTotalCount` | Se `true`, l'header `X-Read-TotalCount` viene omesso dalla risposta: migliori prestazioni a scapito del conteggio totale. Default `false`. |  ## Espansione oggetti correlati  Gli endpoint singoli e lista supportano il parametro `Expand`. Quando fornito, gli oggetti correlati vengono inclusi inline nella risposta anziché restituiti come riferimenti. È possibile specificare espansioni multiple separate da virgola.  ## Gestione errori  L'API usa codici di stato HTTP standard e restituisce [RFC 7807](https://tools.ietf.org/html/rfc7807) Problem Details in caso di errore:  | Codice | Significato | |-|-| | `400` | Bad Request — validazione fallita o parametro obbligatorio mancante. | | `401` | Unauthorized — token mancante o scaduto. | | `403` | Forbidden — il token non possiede lo scope richiesto. | | `404` | Not Found — la risorsa richiesta non esiste. | | `409` | Conflict — conflitto di concorrenza. | | `422` | Unprocessable Entity — entità non processabile (es. token di concorrenza obsoleto). |  Alcune risposte `400` includono un campo `overridable` che indica se l'errore di validazione può essere ignorato impostando il parametro `validationOverride` a `true`.  ## Controllo della concorrenza  Le entità contengono una proprietà `ConcurrencyToken`. Quando si aggiorna un'entità tramite `PUT`, il token nel body della richiesta viene confrontato con il valore memorizzato. Se differiscono, viene restituito un `422 Unprocessable Entity`. Usare sempre l'ultimo token ricevuto da una risposta `GET` o `POST`.
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
 * DocumentoComputeData Class Doc Comment
 *
 * @category Class
 * @description Utilizzato dal metodo M:Amica.DocumentoService.Compute(Amica.DocumentoComputeData,Amica.RequestSettings,System.Boolean) di &#x60;DocumentoService&#x60;.
 * @package  Amica\Sdk
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class DocumentoComputeData implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'DocumentoComputeData';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'righe' => '\Amica\Sdk\Model\DocumentoComputeDataBeneServizio[]',
        'spese_documento' => '\Amica\Sdk\Model\DocumentoComputeDataSpesa[]',
        'sconto_percentuale' => 'float',
        'sconto_pagamento_percentuale' => 'float',
        'sconto_incondizionato_importo' => 'float',
        'cassa_previdenziale_percentuale' => 'float',
        'cassa_previdenziale_causale_iva_id' => 'int',
        'ritenuta_include_cassa_previdenziale' => 'bool',
        'quota_ritenuta_su_imponibile' => 'float',
        'ritenuta_percentuale' => 'float',
        'enasarco_importo' => 'float',
        'abbuono_importo' => 'float',
        'bonus_fiscale_percentuale' => 'float'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'righe' => null,
        'spese_documento' => null,
        'sconto_percentuale' => 'double',
        'sconto_pagamento_percentuale' => 'double',
        'sconto_incondizionato_importo' => 'double',
        'cassa_previdenziale_percentuale' => 'double',
        'cassa_previdenziale_causale_iva_id' => 'int32',
        'ritenuta_include_cassa_previdenziale' => null,
        'quota_ritenuta_su_imponibile' => 'double',
        'ritenuta_percentuale' => 'double',
        'enasarco_importo' => 'double',
        'abbuono_importo' => 'double',
        'bonus_fiscale_percentuale' => 'double'
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'righe' => true,
        'spese_documento' => true,
        'sconto_percentuale' => false,
        'sconto_pagamento_percentuale' => false,
        'sconto_incondizionato_importo' => false,
        'cassa_previdenziale_percentuale' => false,
        'cassa_previdenziale_causale_iva_id' => true,
        'ritenuta_include_cassa_previdenziale' => false,
        'quota_ritenuta_su_imponibile' => false,
        'ritenuta_percentuale' => false,
        'enasarco_importo' => false,
        'abbuono_importo' => false,
        'bonus_fiscale_percentuale' => false
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
        'righe' => 'righe',
        'spese_documento' => 'speseDocumento',
        'sconto_percentuale' => 'scontoPercentuale',
        'sconto_pagamento_percentuale' => 'scontoPagamentoPercentuale',
        'sconto_incondizionato_importo' => 'scontoIncondizionatoImporto',
        'cassa_previdenziale_percentuale' => 'cassaPrevidenzialePercentuale',
        'cassa_previdenziale_causale_iva_id' => 'cassaPrevidenzialeCausaleIvaId',
        'ritenuta_include_cassa_previdenziale' => 'ritenutaIncludeCassaPrevidenziale',
        'quota_ritenuta_su_imponibile' => 'quotaRitenutaSuImponibile',
        'ritenuta_percentuale' => 'ritenutaPercentuale',
        'enasarco_importo' => 'enasarcoImporto',
        'abbuono_importo' => 'abbuonoImporto',
        'bonus_fiscale_percentuale' => 'bonusFiscalePercentuale'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'righe' => 'setRighe',
        'spese_documento' => 'setSpeseDocumento',
        'sconto_percentuale' => 'setScontoPercentuale',
        'sconto_pagamento_percentuale' => 'setScontoPagamentoPercentuale',
        'sconto_incondizionato_importo' => 'setScontoIncondizionatoImporto',
        'cassa_previdenziale_percentuale' => 'setCassaPrevidenzialePercentuale',
        'cassa_previdenziale_causale_iva_id' => 'setCassaPrevidenzialeCausaleIvaId',
        'ritenuta_include_cassa_previdenziale' => 'setRitenutaIncludeCassaPrevidenziale',
        'quota_ritenuta_su_imponibile' => 'setQuotaRitenutaSuImponibile',
        'ritenuta_percentuale' => 'setRitenutaPercentuale',
        'enasarco_importo' => 'setEnasarcoImporto',
        'abbuono_importo' => 'setAbbuonoImporto',
        'bonus_fiscale_percentuale' => 'setBonusFiscalePercentuale'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'righe' => 'getRighe',
        'spese_documento' => 'getSpeseDocumento',
        'sconto_percentuale' => 'getScontoPercentuale',
        'sconto_pagamento_percentuale' => 'getScontoPagamentoPercentuale',
        'sconto_incondizionato_importo' => 'getScontoIncondizionatoImporto',
        'cassa_previdenziale_percentuale' => 'getCassaPrevidenzialePercentuale',
        'cassa_previdenziale_causale_iva_id' => 'getCassaPrevidenzialeCausaleIvaId',
        'ritenuta_include_cassa_previdenziale' => 'getRitenutaIncludeCassaPrevidenziale',
        'quota_ritenuta_su_imponibile' => 'getQuotaRitenutaSuImponibile',
        'ritenuta_percentuale' => 'getRitenutaPercentuale',
        'enasarco_importo' => 'getEnasarcoImporto',
        'abbuono_importo' => 'getAbbuonoImporto',
        'bonus_fiscale_percentuale' => 'getBonusFiscalePercentuale'
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
        $this->setIfExists('righe', $data ?? [], null);
        $this->setIfExists('spese_documento', $data ?? [], null);
        $this->setIfExists('sconto_percentuale', $data ?? [], null);
        $this->setIfExists('sconto_pagamento_percentuale', $data ?? [], null);
        $this->setIfExists('sconto_incondizionato_importo', $data ?? [], null);
        $this->setIfExists('cassa_previdenziale_percentuale', $data ?? [], null);
        $this->setIfExists('cassa_previdenziale_causale_iva_id', $data ?? [], null);
        $this->setIfExists('ritenuta_include_cassa_previdenziale', $data ?? [], null);
        $this->setIfExists('quota_ritenuta_su_imponibile', $data ?? [], null);
        $this->setIfExists('ritenuta_percentuale', $data ?? [], null);
        $this->setIfExists('enasarco_importo', $data ?? [], null);
        $this->setIfExists('abbuono_importo', $data ?? [], null);
        $this->setIfExists('bonus_fiscale_percentuale', $data ?? [], null);
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
     * Gets righe
     *
     * @return \Amica\Sdk\Model\DocumentoComputeDataBeneServizio[]|null
     */
    public function getRighe()
    {
        return $this->container['righe'];
    }

    /**
     * Sets righe
     *
     * @param \Amica\Sdk\Model\DocumentoComputeDataBeneServizio[]|null $righe Metadati delle righe del documento.
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
     * Gets spese_documento
     *
     * @return \Amica\Sdk\Model\DocumentoComputeDataSpesa[]|null
     */
    public function getSpeseDocumento()
    {
        return $this->container['spese_documento'];
    }

    /**
     * Sets spese_documento
     *
     * @param \Amica\Sdk\Model\DocumentoComputeDataSpesa[]|null $spese_documento Metadati dell'elenco delle spese.
     *
     * @return self
     */
    public function setSpeseDocumento($spese_documento)
    {
        if (is_null($spese_documento)) {
            array_push($this->openAPINullablesSetToNull, 'spese_documento');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('spese_documento', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['spese_documento'] = $spese_documento;

        return $this;
    }

    /**
     * Gets sconto_percentuale
     *
     * @return float|null
     */
    public function getScontoPercentuale()
    {
        return $this->container['sconto_percentuale'];
    }

    /**
     * Sets sconto_percentuale
     *
     * @param float|null $sconto_percentuale Sconto, in percentuale, da calcolare sull'intero importo del documento.
     *
     * @return self
     */
    public function setScontoPercentuale($sconto_percentuale)
    {
        if (is_null($sconto_percentuale)) {
            throw new \InvalidArgumentException('non-nullable sconto_percentuale cannot be null');
        }
        $this->container['sconto_percentuale'] = $sconto_percentuale;

        return $this;
    }

    /**
     * Gets sconto_pagamento_percentuale
     *
     * @return float|null
     */
    public function getScontoPagamentoPercentuale()
    {
        return $this->container['sconto_pagamento_percentuale'];
    }

    /**
     * Sets sconto_pagamento_percentuale
     *
     * @param float|null $sconto_pagamento_percentuale Sconto riconosciuto in base alla modalità di pagamento utilizzata (es.: +5% in caso di rimessa diretta).
     *
     * @return self
     */
    public function setScontoPagamentoPercentuale($sconto_pagamento_percentuale)
    {
        if (is_null($sconto_pagamento_percentuale)) {
            throw new \InvalidArgumentException('non-nullable sconto_pagamento_percentuale cannot be null');
        }
        $this->container['sconto_pagamento_percentuale'] = $sconto_pagamento_percentuale;

        return $this;
    }

    /**
     * Gets sconto_incondizionato_importo
     *
     * @return float|null
     */
    public function getScontoIncondizionatoImporto()
    {
        return $this->container['sconto_incondizionato_importo'];
    }

    /**
     * Sets sconto_incondizionato_importo
     *
     * @param float|null $sconto_incondizionato_importo Sconto, in valuta, da applicare all'intero importo del documento.
     *
     * @return self
     */
    public function setScontoIncondizionatoImporto($sconto_incondizionato_importo)
    {
        if (is_null($sconto_incondizionato_importo)) {
            throw new \InvalidArgumentException('non-nullable sconto_incondizionato_importo cannot be null');
        }
        $this->container['sconto_incondizionato_importo'] = $sconto_incondizionato_importo;

        return $this;
    }

    /**
     * Gets cassa_previdenziale_percentuale
     *
     * @return float|null
     */
    public function getCassaPrevidenzialePercentuale()
    {
        return $this->container['cassa_previdenziale_percentuale'];
    }

    /**
     * Sets cassa_previdenziale_percentuale
     *
     * @param float|null $cassa_previdenziale_percentuale Contributo cassa previdenziale (percentuale) da applicare al documento.
     *
     * @return self
     */
    public function setCassaPrevidenzialePercentuale($cassa_previdenziale_percentuale)
    {
        if (is_null($cassa_previdenziale_percentuale)) {
            throw new \InvalidArgumentException('non-nullable cassa_previdenziale_percentuale cannot be null');
        }
        $this->container['cassa_previdenziale_percentuale'] = $cassa_previdenziale_percentuale;

        return $this;
    }

    /**
     * Gets cassa_previdenziale_causale_iva_id
     *
     * @return int|null
     */
    public function getCassaPrevidenzialeCausaleIvaId()
    {
        return $this->container['cassa_previdenziale_causale_iva_id'];
    }

    /**
     * Sets cassa_previdenziale_causale_iva_id
     *
     * @param int|null $cassa_previdenziale_causale_iva_id Identificativo univoco della causale IVA della cassa previdenziale.
     *
     * @return self
     */
    public function setCassaPrevidenzialeCausaleIvaId($cassa_previdenziale_causale_iva_id)
    {
        if (is_null($cassa_previdenziale_causale_iva_id)) {
            array_push($this->openAPINullablesSetToNull, 'cassa_previdenziale_causale_iva_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('cassa_previdenziale_causale_iva_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['cassa_previdenziale_causale_iva_id'] = $cassa_previdenziale_causale_iva_id;

        return $this;
    }

    /**
     * Gets ritenuta_include_cassa_previdenziale
     *
     * @return bool|null
     */
    public function getRitenutaIncludeCassaPrevidenziale()
    {
        return $this->container['ritenuta_include_cassa_previdenziale'];
    }

    /**
     * Sets ritenuta_include_cassa_previdenziale
     *
     * @param bool|null $ritenuta_include_cassa_previdenziale Indica se la ritenuta debba essere applicata all'importo della cassa previdenziale riportato nel documento.
     *
     * @return self
     */
    public function setRitenutaIncludeCassaPrevidenziale($ritenuta_include_cassa_previdenziale)
    {
        if (is_null($ritenuta_include_cassa_previdenziale)) {
            throw new \InvalidArgumentException('non-nullable ritenuta_include_cassa_previdenziale cannot be null');
        }
        $this->container['ritenuta_include_cassa_previdenziale'] = $ritenuta_include_cassa_previdenziale;

        return $this;
    }

    /**
     * Gets quota_ritenuta_su_imponibile
     *
     * @return float|null
     */
    public function getQuotaRitenutaSuImponibile()
    {
        return $this->container['quota_ritenuta_su_imponibile'];
    }

    /**
     * Sets quota_ritenuta_su_imponibile
     *
     * @param float|null $quota_ritenuta_su_imponibile Quota dell'imponibile su cui applicare la ritenuta.
     *
     * @return self
     */
    public function setQuotaRitenutaSuImponibile($quota_ritenuta_su_imponibile)
    {
        if (is_null($quota_ritenuta_su_imponibile)) {
            throw new \InvalidArgumentException('non-nullable quota_ritenuta_su_imponibile cannot be null');
        }
        $this->container['quota_ritenuta_su_imponibile'] = $quota_ritenuta_su_imponibile;

        return $this;
    }

    /**
     * Gets ritenuta_percentuale
     *
     * @return float|null
     */
    public function getRitenutaPercentuale()
    {
        return $this->container['ritenuta_percentuale'];
    }

    /**
     * Sets ritenuta_percentuale
     *
     * @param float|null $ritenuta_percentuale Quota di ritenuta da applicare al documento.
     *
     * @return self
     */
    public function setRitenutaPercentuale($ritenuta_percentuale)
    {
        if (is_null($ritenuta_percentuale)) {
            throw new \InvalidArgumentException('non-nullable ritenuta_percentuale cannot be null');
        }
        $this->container['ritenuta_percentuale'] = $ritenuta_percentuale;

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
     * Gets abbuono_importo
     *
     * @return float|null
     */
    public function getAbbuonoImporto()
    {
        return $this->container['abbuono_importo'];
    }

    /**
     * Sets abbuono_importo
     *
     * @param float|null $abbuono_importo Importo dell'abbuono.
     *
     * @return self
     */
    public function setAbbuonoImporto($abbuono_importo)
    {
        if (is_null($abbuono_importo)) {
            throw new \InvalidArgumentException('non-nullable abbuono_importo cannot be null');
        }
        $this->container['abbuono_importo'] = $abbuono_importo;

        return $this;
    }

    /**
     * Gets bonus_fiscale_percentuale
     *
     * @return float|null
     */
    public function getBonusFiscalePercentuale()
    {
        return $this->container['bonus_fiscale_percentuale'];
    }

    /**
     * Sets bonus_fiscale_percentuale
     *
     * @param float|null $bonus_fiscale_percentuale Importo del bonus fiscale.
     *
     * @return self
     */
    public function setBonusFiscalePercentuale($bonus_fiscale_percentuale)
    {
        if (is_null($bonus_fiscale_percentuale)) {
            throw new \InvalidArgumentException('non-nullable bonus_fiscale_percentuale cannot be null');
        }
        $this->container['bonus_fiscale_percentuale'] = $bonus_fiscale_percentuale;

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


