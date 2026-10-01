<?php
/**
 * SpesaApi
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
 * The version of the OpenAPI document: 3.1.3
 * Contact: assistenza@gestionaleamica.com
 * Generated by: https://openapi-generator.tech
 * Generator version: 7.11.0
 */

/**
 * NOTE: This class is auto generated by OpenAPI Generator (https://openapi-generator.tech).
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */

namespace Amica\Sdk\Api;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Amica\Sdk\ApiException;
use Amica\Sdk\Configuration;
use Amica\Sdk\HeaderSelector;
use Amica\Sdk\ObjectSerializer;

/**
 * SpesaApi Class Doc Comment
 *
 * @category Class
 * @package  Amica\Sdk
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */
class SpesaApi
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var HeaderSelector
     */
    protected $headerSelector;

    /**
     * @var int Host index
     */
    protected $hostIndex;

    /** @var string[] $contentTypes **/
    public const contentTypes = [
        'apiVversionCompanyIdSpesaDistinctGet' => [
            'application/json',
        ],
        'apiVversionCompanyIdSpesaGet' => [
            'application/json',
        ],
        'apiVversionCompanyIdSpesaIdDelete' => [
            'application/json',
        ],
        'apiVversionCompanyIdSpesaIdGet' => [
            'application/json',
        ],
        'apiVversionCompanyIdSpesaIdPut' => [
            'application/json',
            'text/json',
            'application/*+json',
        ],
        'apiVversionCompanyIdSpesaNewGet' => [
            'application/json',
        ],
        'apiVversionCompanyIdSpesaPost' => [
            'application/json',
            'text/json',
            'application/*+json',
        ],
    ];

    /**
     * @param ClientInterface $client
     * @param Configuration   $config
     * @param HeaderSelector  $selector
     * @param int             $hostIndex (Optional) host index to select the list of hosts if defined in the OpenAPI spec
     */
    public function __construct(
        ?ClientInterface $client = null,
        ?Configuration $config = null,
        ?HeaderSelector $selector = null,
        int $hostIndex = 0
    ) {
        $this->client = $client ?: new Client();
        $this->config = $config ?: Configuration::getDefaultConfiguration();
        $this->headerSelector = $selector ?: new HeaderSelector();
        $this->hostIndex = $hostIndex;
    }

    /**
     * Set the host index
     *
     * @param int $hostIndex Host index (required)
     */
    public function setHostIndex($hostIndex): void
    {
        $this->hostIndex = $hostIndex;
    }

    /**
     * Get the host index
     *
     * @return int Host index
     */
    public function getHostIndex()
    {
        return $this->hostIndex;
    }

    /**
     * @return Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Operation apiVversionCompanyIdSpesaDistinctGet
     *
     * Restituisce una lista di elementi distinti.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  string $selector Il campo usato per distinguere gli elementi. (required)
     * @param  int|null $skip Numero di elementi, a partire dal primo, da escludere dal risultato. Combinato con &#x60;Take&#x60;, è utile per la paginazione. (optional)
     * @param  int|null $take Numero di elementi da includere nel risultato. Combinato con &#x60;Skip&#x60;, è utile per la paginazione. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDistinctGet'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amica\Sdk\Model\DistinctPropertyGroup[]|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails
     */
    public function apiVversionCompanyIdSpesaDistinctGet($version, $company_id, $selector, $skip = null, $take = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDistinctGet'][0])
    {
        list($response) = $this->apiVversionCompanyIdSpesaDistinctGetWithHttpInfo($version, $company_id, $selector, $skip, $take, $contentType);
        return $response;
    }

    /**
     * Operation apiVversionCompanyIdSpesaDistinctGetWithHttpInfo
     *
     * Restituisce una lista di elementi distinti.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  string $selector Il campo usato per distinguere gli elementi. (required)
     * @param  int|null $skip Numero di elementi, a partire dal primo, da escludere dal risultato. Combinato con &#x60;Take&#x60;, è utile per la paginazione. (optional)
     * @param  int|null $take Numero di elementi da includere nel risultato. Combinato con &#x60;Skip&#x60;, è utile per la paginazione. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDistinctGet'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amica\Sdk\Model\DistinctPropertyGroup[]|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails, HTTP status code, HTTP response headers (array of strings)
     */
    public function apiVversionCompanyIdSpesaDistinctGetWithHttpInfo($version, $company_id, $selector, $skip = null, $take = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDistinctGet'][0])
    {
        $request = $this->apiVversionCompanyIdSpesaDistinctGetRequest($version, $company_id, $selector, $skip, $take, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();


            switch($statusCode) {
                case 200:
                    if ('\Amica\Sdk\Model\DistinctPropertyGroup[]' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\DistinctPropertyGroup[]' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\DistinctPropertyGroup[]', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                    if ('\Amica\Sdk\Model\ProblemDetails' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\ProblemDetails' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\ProblemDetails', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 403:
                    if ('\Amica\Sdk\Model\ProblemDetails' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\ProblemDetails' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\ProblemDetails', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            $returnType = '\Amica\Sdk\Model\DistinctPropertyGroup[]';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    try {
                        $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                    } catch (\JsonException $exception) {
                        throw new ApiException(
                            sprintf(
                                'Error JSON decoding server response (%s)',
                                $request->getUri()
                            ),
                            $statusCode,
                            $response->getHeaders(),
                            $content
                        );
                    }
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\DistinctPropertyGroup[]',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\ProblemDetails',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\ProblemDetails',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation apiVversionCompanyIdSpesaDistinctGetAsync
     *
     * Restituisce una lista di elementi distinti.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  string $selector Il campo usato per distinguere gli elementi. (required)
     * @param  int|null $skip Numero di elementi, a partire dal primo, da escludere dal risultato. Combinato con &#x60;Take&#x60;, è utile per la paginazione. (optional)
     * @param  int|null $take Numero di elementi da includere nel risultato. Combinato con &#x60;Skip&#x60;, è utile per la paginazione. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDistinctGet'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function apiVversionCompanyIdSpesaDistinctGetAsync($version, $company_id, $selector, $skip = null, $take = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDistinctGet'][0])
    {
        return $this->apiVversionCompanyIdSpesaDistinctGetAsyncWithHttpInfo($version, $company_id, $selector, $skip, $take, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation apiVversionCompanyIdSpesaDistinctGetAsyncWithHttpInfo
     *
     * Restituisce una lista di elementi distinti.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  string $selector Il campo usato per distinguere gli elementi. (required)
     * @param  int|null $skip Numero di elementi, a partire dal primo, da escludere dal risultato. Combinato con &#x60;Take&#x60;, è utile per la paginazione. (optional)
     * @param  int|null $take Numero di elementi da includere nel risultato. Combinato con &#x60;Skip&#x60;, è utile per la paginazione. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDistinctGet'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function apiVversionCompanyIdSpesaDistinctGetAsyncWithHttpInfo($version, $company_id, $selector, $skip = null, $take = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDistinctGet'][0])
    {
        $returnType = '\Amica\Sdk\Model\DistinctPropertyGroup[]';
        $request = $this->apiVversionCompanyIdSpesaDistinctGetRequest($version, $company_id, $selector, $skip, $take, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'apiVversionCompanyIdSpesaDistinctGet'
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  string $selector Il campo usato per distinguere gli elementi. (required)
     * @param  int|null $skip Numero di elementi, a partire dal primo, da escludere dal risultato. Combinato con &#x60;Take&#x60;, è utile per la paginazione. (optional)
     * @param  int|null $take Numero di elementi da includere nel risultato. Combinato con &#x60;Skip&#x60;, è utile per la paginazione. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDistinctGet'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function apiVversionCompanyIdSpesaDistinctGetRequest($version, $company_id, $selector, $skip = null, $take = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDistinctGet'][0])
    {

        // verify the required parameter 'version' is set
        if ($version === null || (is_array($version) && count($version) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $version when calling apiVversionCompanyIdSpesaDistinctGet'
            );
        }

        // verify the required parameter 'company_id' is set
        if ($company_id === null || (is_array($company_id) && count($company_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $company_id when calling apiVversionCompanyIdSpesaDistinctGet'
            );
        }

        // verify the required parameter 'selector' is set
        if ($selector === null || (is_array($selector) && count($selector) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $selector when calling apiVversionCompanyIdSpesaDistinctGet'
            );
        }




        $resourcePath = '/api/v{version}/{companyId}/Spesa/distinct';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $selector,
            'Selector', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            true // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $skip,
            'Skip', // param base name
            'integer', // openApiType
            'form', // style
            true, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $take,
            'Take', // param base name
            'integer', // openApiType
            'form', // style
            true, // explode
            false // required
        ) ?? []);


        // path params
        if ($version !== null) {
            $resourcePath = str_replace(
                '{' . 'version' . '}',
                ObjectSerializer::toPathValue($version),
                $resourcePath
            );
        }
        // path params
        if ($company_id !== null) {
            $resourcePath = str_replace(
                '{' . 'companyId' . '}',
                ObjectSerializer::toPathValue($company_id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', 'application/problem+json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (JWT) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }
        // this endpoint requires OAuth (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }
        // this endpoint requires OAuth (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation apiVversionCompanyIdSpesaGet
     *
     * Restituisce una lista di elementi.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int|null $skip skip (optional)
     * @param  int|null $take take (optional)
     * @param  bool|null $skip_total_count skip_total_count (optional)
     * @param  string|null $filter filter (optional)
     * @param  string|null $order order (optional)
     * @param  string[]|null $expand expand (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaGet'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amica\Sdk\Model\Spesa[]|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails
     */
    public function apiVversionCompanyIdSpesaGet($version, $company_id, $skip = null, $take = null, $skip_total_count = null, $filter = null, $order = null, $expand = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaGet'][0])
    {
        list($response) = $this->apiVversionCompanyIdSpesaGetWithHttpInfo($version, $company_id, $skip, $take, $skip_total_count, $filter, $order, $expand, $contentType);
        return $response;
    }

    /**
     * Operation apiVversionCompanyIdSpesaGetWithHttpInfo
     *
     * Restituisce una lista di elementi.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int|null $skip (optional)
     * @param  int|null $take (optional)
     * @param  bool|null $skip_total_count (optional)
     * @param  string|null $filter (optional)
     * @param  string|null $order (optional)
     * @param  string[]|null $expand (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaGet'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amica\Sdk\Model\Spesa[]|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails, HTTP status code, HTTP response headers (array of strings)
     */
    public function apiVversionCompanyIdSpesaGetWithHttpInfo($version, $company_id, $skip = null, $take = null, $skip_total_count = null, $filter = null, $order = null, $expand = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaGet'][0])
    {
        $request = $this->apiVversionCompanyIdSpesaGetRequest($version, $company_id, $skip, $take, $skip_total_count, $filter, $order, $expand, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();


            switch($statusCode) {
                case 200:
                    if ('\Amica\Sdk\Model\Spesa[]' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\Spesa[]' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\Spesa[]', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                    if ('\Amica\Sdk\Model\ProblemDetails' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\ProblemDetails' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\ProblemDetails', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 403:
                    if ('\Amica\Sdk\Model\ProblemDetails' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\ProblemDetails' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\ProblemDetails', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            $returnType = '\Amica\Sdk\Model\Spesa[]';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    try {
                        $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                    } catch (\JsonException $exception) {
                        throw new ApiException(
                            sprintf(
                                'Error JSON decoding server response (%s)',
                                $request->getUri()
                            ),
                            $statusCode,
                            $response->getHeaders(),
                            $content
                        );
                    }
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\Spesa[]',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\ProblemDetails',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\ProblemDetails',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation apiVversionCompanyIdSpesaGetAsync
     *
     * Restituisce una lista di elementi.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int|null $skip (optional)
     * @param  int|null $take (optional)
     * @param  bool|null $skip_total_count (optional)
     * @param  string|null $filter (optional)
     * @param  string|null $order (optional)
     * @param  string[]|null $expand (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaGet'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function apiVversionCompanyIdSpesaGetAsync($version, $company_id, $skip = null, $take = null, $skip_total_count = null, $filter = null, $order = null, $expand = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaGet'][0])
    {
        return $this->apiVversionCompanyIdSpesaGetAsyncWithHttpInfo($version, $company_id, $skip, $take, $skip_total_count, $filter, $order, $expand, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation apiVversionCompanyIdSpesaGetAsyncWithHttpInfo
     *
     * Restituisce una lista di elementi.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int|null $skip (optional)
     * @param  int|null $take (optional)
     * @param  bool|null $skip_total_count (optional)
     * @param  string|null $filter (optional)
     * @param  string|null $order (optional)
     * @param  string[]|null $expand (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaGet'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function apiVversionCompanyIdSpesaGetAsyncWithHttpInfo($version, $company_id, $skip = null, $take = null, $skip_total_count = null, $filter = null, $order = null, $expand = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaGet'][0])
    {
        $returnType = '\Amica\Sdk\Model\Spesa[]';
        $request = $this->apiVversionCompanyIdSpesaGetRequest($version, $company_id, $skip, $take, $skip_total_count, $filter, $order, $expand, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'apiVversionCompanyIdSpesaGet'
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int|null $skip (optional)
     * @param  int|null $take (optional)
     * @param  bool|null $skip_total_count (optional)
     * @param  string|null $filter (optional)
     * @param  string|null $order (optional)
     * @param  string[]|null $expand (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaGet'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function apiVversionCompanyIdSpesaGetRequest($version, $company_id, $skip = null, $take = null, $skip_total_count = null, $filter = null, $order = null, $expand = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaGet'][0])
    {

        // verify the required parameter 'version' is set
        if ($version === null || (is_array($version) && count($version) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $version when calling apiVversionCompanyIdSpesaGet'
            );
        }

        // verify the required parameter 'company_id' is set
        if ($company_id === null || (is_array($company_id) && count($company_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $company_id when calling apiVversionCompanyIdSpesaGet'
            );
        }








        $resourcePath = '/api/v{version}/{companyId}/Spesa';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $skip,
            'Skip', // param base name
            'integer', // openApiType
            'form', // style
            true, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $take,
            'Take', // param base name
            'integer', // openApiType
            'form', // style
            true, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $skip_total_count,
            'SkipTotalCount', // param base name
            'boolean', // openApiType
            'form', // style
            true, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $filter,
            'Filter', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $order,
            'Order', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $expand,
            'Expand', // param base name
            'array', // openApiType
            'form', // style
            true, // explode
            false // required
        ) ?? []);


        // path params
        if ($version !== null) {
            $resourcePath = str_replace(
                '{' . 'version' . '}',
                ObjectSerializer::toPathValue($version),
                $resourcePath
            );
        }
        // path params
        if ($company_id !== null) {
            $resourcePath = str_replace(
                '{' . 'companyId' . '}',
                ObjectSerializer::toPathValue($company_id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', 'application/problem+json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (JWT) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }
        // this endpoint requires OAuth (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }
        // this endpoint requires OAuth (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation apiVversionCompanyIdSpesaIdDelete
     *
     * Elimina un elemento.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID dell&#39;elemento da eliminare. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaIdDelete'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amica\Sdk\Model\Spesa|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails
     */
    public function apiVversionCompanyIdSpesaIdDelete($version, $company_id, $id, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaIdDelete'][0])
    {
        list($response) = $this->apiVversionCompanyIdSpesaIdDeleteWithHttpInfo($version, $company_id, $id, $contentType);
        return $response;
    }

    /**
     * Operation apiVversionCompanyIdSpesaIdDeleteWithHttpInfo
     *
     * Elimina un elemento.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID dell&#39;elemento da eliminare. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaIdDelete'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amica\Sdk\Model\Spesa|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails, HTTP status code, HTTP response headers (array of strings)
     */
    public function apiVversionCompanyIdSpesaIdDeleteWithHttpInfo($version, $company_id, $id, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaIdDelete'][0])
    {
        $request = $this->apiVversionCompanyIdSpesaIdDeleteRequest($version, $company_id, $id, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();


            switch($statusCode) {
                case 200:
                    if ('\Amica\Sdk\Model\Spesa' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\Spesa' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\Spesa', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 404:
                    if ('\Amica\Sdk\Model\ProblemDetails' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\ProblemDetails' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\ProblemDetails', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                    if ('\Amica\Sdk\Model\ProblemDetails' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\ProblemDetails' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\ProblemDetails', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 403:
                    if ('\Amica\Sdk\Model\ProblemDetails' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\ProblemDetails' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\ProblemDetails', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            $returnType = '\Amica\Sdk\Model\Spesa';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    try {
                        $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                    } catch (\JsonException $exception) {
                        throw new ApiException(
                            sprintf(
                                'Error JSON decoding server response (%s)',
                                $request->getUri()
                            ),
                            $statusCode,
                            $response->getHeaders(),
                            $content
                        );
                    }
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\Spesa',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\ProblemDetails',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\ProblemDetails',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\ProblemDetails',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation apiVversionCompanyIdSpesaIdDeleteAsync
     *
     * Elimina un elemento.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID dell&#39;elemento da eliminare. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaIdDelete'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function apiVversionCompanyIdSpesaIdDeleteAsync($version, $company_id, $id, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaIdDelete'][0])
    {
        return $this->apiVversionCompanyIdSpesaIdDeleteAsyncWithHttpInfo($version, $company_id, $id, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation apiVversionCompanyIdSpesaIdDeleteAsyncWithHttpInfo
     *
     * Elimina un elemento.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID dell&#39;elemento da eliminare. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaIdDelete'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function apiVversionCompanyIdSpesaIdDeleteAsyncWithHttpInfo($version, $company_id, $id, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaIdDelete'][0])
    {
        $returnType = '\Amica\Sdk\Model\Spesa';
        $request = $this->apiVversionCompanyIdSpesaIdDeleteRequest($version, $company_id, $id, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'apiVversionCompanyIdSpesaIdDelete'
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID dell&#39;elemento da eliminare. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaIdDelete'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function apiVversionCompanyIdSpesaIdDeleteRequest($version, $company_id, $id, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaIdDelete'][0])
    {

        // verify the required parameter 'version' is set
        if ($version === null || (is_array($version) && count($version) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $version when calling apiVversionCompanyIdSpesaIdDelete'
            );
        }

        // verify the required parameter 'company_id' is set
        if ($company_id === null || (is_array($company_id) && count($company_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $company_id when calling apiVversionCompanyIdSpesaIdDelete'
            );
        }

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $id when calling apiVversionCompanyIdSpesaIdDelete'
            );
        }


        $resourcePath = '/api/v{version}/{companyId}/Spesa/{id}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($version !== null) {
            $resourcePath = str_replace(
                '{' . 'version' . '}',
                ObjectSerializer::toPathValue($version),
                $resourcePath
            );
        }
        // path params
        if ($company_id !== null) {
            $resourcePath = str_replace(
                '{' . 'companyId' . '}',
                ObjectSerializer::toPathValue($company_id),
                $resourcePath
            );
        }
        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{' . 'id' . '}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', 'application/problem+json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (JWT) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }
        // this endpoint requires OAuth (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }
        // this endpoint requires OAuth (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'DELETE',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation apiVversionCompanyIdSpesaIdGet
     *
     * Restituisce un elemento.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID dell&#39;elemento da recuperare. (required)
     * @param  string[]|null $expand expand (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaIdGet'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amica\Sdk\Model\Spesa|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails
     */
    public function apiVversionCompanyIdSpesaIdGet($version, $company_id, $id, $expand = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaIdGet'][0])
    {
        list($response) = $this->apiVversionCompanyIdSpesaIdGetWithHttpInfo($version, $company_id, $id, $expand, $contentType);
        return $response;
    }

    /**
     * Operation apiVversionCompanyIdSpesaIdGetWithHttpInfo
     *
     * Restituisce un elemento.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID dell&#39;elemento da recuperare. (required)
     * @param  string[]|null $expand (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaIdGet'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amica\Sdk\Model\Spesa|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails, HTTP status code, HTTP response headers (array of strings)
     */
    public function apiVversionCompanyIdSpesaIdGetWithHttpInfo($version, $company_id, $id, $expand = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaIdGet'][0])
    {
        $request = $this->apiVversionCompanyIdSpesaIdGetRequest($version, $company_id, $id, $expand, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();


            switch($statusCode) {
                case 200:
                    if ('\Amica\Sdk\Model\Spesa' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\Spesa' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\Spesa', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                    if ('\Amica\Sdk\Model\ProblemDetails' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\ProblemDetails' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\ProblemDetails', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 404:
                    if ('\Amica\Sdk\Model\ProblemDetails' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\ProblemDetails' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\ProblemDetails', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 403:
                    if ('\Amica\Sdk\Model\ProblemDetails' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\ProblemDetails' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\ProblemDetails', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            $returnType = '\Amica\Sdk\Model\Spesa';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    try {
                        $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                    } catch (\JsonException $exception) {
                        throw new ApiException(
                            sprintf(
                                'Error JSON decoding server response (%s)',
                                $request->getUri()
                            ),
                            $statusCode,
                            $response->getHeaders(),
                            $content
                        );
                    }
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\Spesa',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\ProblemDetails',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\ProblemDetails',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\ProblemDetails',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation apiVversionCompanyIdSpesaIdGetAsync
     *
     * Restituisce un elemento.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID dell&#39;elemento da recuperare. (required)
     * @param  string[]|null $expand (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaIdGet'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function apiVversionCompanyIdSpesaIdGetAsync($version, $company_id, $id, $expand = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaIdGet'][0])
    {
        return $this->apiVversionCompanyIdSpesaIdGetAsyncWithHttpInfo($version, $company_id, $id, $expand, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation apiVversionCompanyIdSpesaIdGetAsyncWithHttpInfo
     *
     * Restituisce un elemento.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID dell&#39;elemento da recuperare. (required)
     * @param  string[]|null $expand (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaIdGet'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function apiVversionCompanyIdSpesaIdGetAsyncWithHttpInfo($version, $company_id, $id, $expand = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaIdGet'][0])
    {
        $returnType = '\Amica\Sdk\Model\Spesa';
        $request = $this->apiVversionCompanyIdSpesaIdGetRequest($version, $company_id, $id, $expand, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'apiVversionCompanyIdSpesaIdGet'
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID dell&#39;elemento da recuperare. (required)
     * @param  string[]|null $expand (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaIdGet'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function apiVversionCompanyIdSpesaIdGetRequest($version, $company_id, $id, $expand = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaIdGet'][0])
    {

        // verify the required parameter 'version' is set
        if ($version === null || (is_array($version) && count($version) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $version when calling apiVversionCompanyIdSpesaIdGet'
            );
        }

        // verify the required parameter 'company_id' is set
        if ($company_id === null || (is_array($company_id) && count($company_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $company_id when calling apiVversionCompanyIdSpesaIdGet'
            );
        }

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $id when calling apiVversionCompanyIdSpesaIdGet'
            );
        }



        $resourcePath = '/api/v{version}/{companyId}/Spesa/{id}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $expand,
            'Expand', // param base name
            'array', // openApiType
            'form', // style
            true, // explode
            false // required
        ) ?? []);


        // path params
        if ($version !== null) {
            $resourcePath = str_replace(
                '{' . 'version' . '}',
                ObjectSerializer::toPathValue($version),
                $resourcePath
            );
        }
        // path params
        if ($company_id !== null) {
            $resourcePath = str_replace(
                '{' . 'companyId' . '}',
                ObjectSerializer::toPathValue($company_id),
                $resourcePath
            );
        }
        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{' . 'id' . '}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', 'application/problem+json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (JWT) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }
        // this endpoint requires OAuth (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }
        // this endpoint requires OAuth (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation apiVversionCompanyIdSpesaIdPut
     *
     * Aggiorna un elemento esistente.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID univoco dell&#39;elemento da aggiornare. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  string|null $idempotency_key Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. (optional)
     * @param  \Amica\Sdk\Model\Spesa|null $spesa Istanza dell&#39;elemento da salvare. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaIdPut'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amica\Sdk\Model\Spesa|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails
     */
    public function apiVversionCompanyIdSpesaIdPut($version, $company_id, $id, $validation_override = null, $idempotency_key = null, $spesa = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaIdPut'][0])
    {
        list($response) = $this->apiVversionCompanyIdSpesaIdPutWithHttpInfo($version, $company_id, $id, $validation_override, $idempotency_key, $spesa, $contentType);
        return $response;
    }

    /**
     * Operation apiVversionCompanyIdSpesaIdPutWithHttpInfo
     *
     * Aggiorna un elemento esistente.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID univoco dell&#39;elemento da aggiornare. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  string|null $idempotency_key Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. (optional)
     * @param  \Amica\Sdk\Model\Spesa|null $spesa Istanza dell&#39;elemento da salvare. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaIdPut'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amica\Sdk\Model\Spesa|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails, HTTP status code, HTTP response headers (array of strings)
     */
    public function apiVversionCompanyIdSpesaIdPutWithHttpInfo($version, $company_id, $id, $validation_override = null, $idempotency_key = null, $spesa = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaIdPut'][0])
    {
        $request = $this->apiVversionCompanyIdSpesaIdPutRequest($version, $company_id, $id, $validation_override, $idempotency_key, $spesa, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();


            switch($statusCode) {
                case 200:
                    if ('\Amica\Sdk\Model\Spesa' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\Spesa' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\Spesa', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 422:
                    if ('\Amica\Sdk\Model\ProblemDetails' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\ProblemDetails' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\ProblemDetails', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 404:
                    if ('\Amica\Sdk\Model\ProblemDetails' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\ProblemDetails' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\ProblemDetails', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                    if ('\Amica\Sdk\Model\ProblemDetails' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\ProblemDetails' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\ProblemDetails', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 403:
                    if ('\Amica\Sdk\Model\ProblemDetails' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\ProblemDetails' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\ProblemDetails', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            $returnType = '\Amica\Sdk\Model\Spesa';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    try {
                        $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                    } catch (\JsonException $exception) {
                        throw new ApiException(
                            sprintf(
                                'Error JSON decoding server response (%s)',
                                $request->getUri()
                            ),
                            $statusCode,
                            $response->getHeaders(),
                            $content
                        );
                    }
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\Spesa',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\ProblemDetails',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\ProblemDetails',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\ProblemDetails',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\ProblemDetails',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation apiVversionCompanyIdSpesaIdPutAsync
     *
     * Aggiorna un elemento esistente.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID univoco dell&#39;elemento da aggiornare. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  string|null $idempotency_key Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. (optional)
     * @param  \Amica\Sdk\Model\Spesa|null $spesa Istanza dell&#39;elemento da salvare. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaIdPut'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function apiVversionCompanyIdSpesaIdPutAsync($version, $company_id, $id, $validation_override = null, $idempotency_key = null, $spesa = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaIdPut'][0])
    {
        return $this->apiVversionCompanyIdSpesaIdPutAsyncWithHttpInfo($version, $company_id, $id, $validation_override, $idempotency_key, $spesa, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation apiVversionCompanyIdSpesaIdPutAsyncWithHttpInfo
     *
     * Aggiorna un elemento esistente.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID univoco dell&#39;elemento da aggiornare. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  string|null $idempotency_key Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. (optional)
     * @param  \Amica\Sdk\Model\Spesa|null $spesa Istanza dell&#39;elemento da salvare. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaIdPut'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function apiVversionCompanyIdSpesaIdPutAsyncWithHttpInfo($version, $company_id, $id, $validation_override = null, $idempotency_key = null, $spesa = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaIdPut'][0])
    {
        $returnType = '\Amica\Sdk\Model\Spesa';
        $request = $this->apiVversionCompanyIdSpesaIdPutRequest($version, $company_id, $id, $validation_override, $idempotency_key, $spesa, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'apiVversionCompanyIdSpesaIdPut'
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID univoco dell&#39;elemento da aggiornare. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  string|null $idempotency_key Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. (optional)
     * @param  \Amica\Sdk\Model\Spesa|null $spesa Istanza dell&#39;elemento da salvare. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaIdPut'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function apiVversionCompanyIdSpesaIdPutRequest($version, $company_id, $id, $validation_override = null, $idempotency_key = null, $spesa = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaIdPut'][0])
    {

        // verify the required parameter 'version' is set
        if ($version === null || (is_array($version) && count($version) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $version when calling apiVversionCompanyIdSpesaIdPut'
            );
        }

        // verify the required parameter 'company_id' is set
        if ($company_id === null || (is_array($company_id) && count($company_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $company_id when calling apiVversionCompanyIdSpesaIdPut'
            );
        }

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $id when calling apiVversionCompanyIdSpesaIdPut'
            );
        }





        $resourcePath = '/api/v{version}/{companyId}/Spesa/{id}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $validation_override,
            'validationOverride', // param base name
            'boolean', // openApiType
            'form', // style
            true, // explode
            false // required
        ) ?? []);

        // header params
        if ($idempotency_key !== null) {
            $headerParams['Idempotency-Key'] = ObjectSerializer::toHeaderValue($idempotency_key);
        }

        // path params
        if ($version !== null) {
            $resourcePath = str_replace(
                '{' . 'version' . '}',
                ObjectSerializer::toPathValue($version),
                $resourcePath
            );
        }
        // path params
        if ($company_id !== null) {
            $resourcePath = str_replace(
                '{' . 'companyId' . '}',
                ObjectSerializer::toPathValue($company_id),
                $resourcePath
            );
        }
        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{' . 'id' . '}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', 'application/problem+json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($spesa)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($spesa));
            } else {
                $httpBody = $spesa;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (JWT) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }
        // this endpoint requires OAuth (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }
        // this endpoint requires OAuth (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'PUT',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation apiVversionCompanyIdSpesaNewGet
     *
     * Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di &#x60;Documento&#x60; avrà il &#x60;Numero&#x60; appropriato, basato sui documenti preesistenti di tipo compatibile).
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaNewGet'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amica\Sdk\Model\Spesa|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails
     */
    public function apiVversionCompanyIdSpesaNewGet($version, $company_id, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaNewGet'][0])
    {
        list($response) = $this->apiVversionCompanyIdSpesaNewGetWithHttpInfo($version, $company_id, $contentType);
        return $response;
    }

    /**
     * Operation apiVversionCompanyIdSpesaNewGetWithHttpInfo
     *
     * Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di &#x60;Documento&#x60; avrà il &#x60;Numero&#x60; appropriato, basato sui documenti preesistenti di tipo compatibile).
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaNewGet'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amica\Sdk\Model\Spesa|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails, HTTP status code, HTTP response headers (array of strings)
     */
    public function apiVversionCompanyIdSpesaNewGetWithHttpInfo($version, $company_id, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaNewGet'][0])
    {
        $request = $this->apiVversionCompanyIdSpesaNewGetRequest($version, $company_id, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();


            switch($statusCode) {
                case 200:
                    if ('\Amica\Sdk\Model\Spesa' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\Spesa' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\Spesa', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                    if ('\Amica\Sdk\Model\ProblemDetails' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\ProblemDetails' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\ProblemDetails', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 403:
                    if ('\Amica\Sdk\Model\ProblemDetails' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\ProblemDetails' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\ProblemDetails', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            $returnType = '\Amica\Sdk\Model\Spesa';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    try {
                        $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                    } catch (\JsonException $exception) {
                        throw new ApiException(
                            sprintf(
                                'Error JSON decoding server response (%s)',
                                $request->getUri()
                            ),
                            $statusCode,
                            $response->getHeaders(),
                            $content
                        );
                    }
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\Spesa',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\ProblemDetails',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\ProblemDetails',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation apiVversionCompanyIdSpesaNewGetAsync
     *
     * Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di &#x60;Documento&#x60; avrà il &#x60;Numero&#x60; appropriato, basato sui documenti preesistenti di tipo compatibile).
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaNewGet'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function apiVversionCompanyIdSpesaNewGetAsync($version, $company_id, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaNewGet'][0])
    {
        return $this->apiVversionCompanyIdSpesaNewGetAsyncWithHttpInfo($version, $company_id, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation apiVversionCompanyIdSpesaNewGetAsyncWithHttpInfo
     *
     * Restituisce una nuova istanza di un oggetto. Dove necessario, i valori delle proprietà saranno coerenti con lo stato e la logica dell&#39;applicazione (ad es., una nuova istanza di &#x60;Documento&#x60; avrà il &#x60;Numero&#x60; appropriato, basato sui documenti preesistenti di tipo compatibile).
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaNewGet'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function apiVversionCompanyIdSpesaNewGetAsyncWithHttpInfo($version, $company_id, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaNewGet'][0])
    {
        $returnType = '\Amica\Sdk\Model\Spesa';
        $request = $this->apiVversionCompanyIdSpesaNewGetRequest($version, $company_id, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'apiVversionCompanyIdSpesaNewGet'
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaNewGet'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function apiVversionCompanyIdSpesaNewGetRequest($version, $company_id, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaNewGet'][0])
    {

        // verify the required parameter 'version' is set
        if ($version === null || (is_array($version) && count($version) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $version when calling apiVversionCompanyIdSpesaNewGet'
            );
        }

        // verify the required parameter 'company_id' is set
        if ($company_id === null || (is_array($company_id) && count($company_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $company_id when calling apiVversionCompanyIdSpesaNewGet'
            );
        }


        $resourcePath = '/api/v{version}/{companyId}/Spesa/new';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($version !== null) {
            $resourcePath = str_replace(
                '{' . 'version' . '}',
                ObjectSerializer::toPathValue($version),
                $resourcePath
            );
        }
        // path params
        if ($company_id !== null) {
            $resourcePath = str_replace(
                '{' . 'companyId' . '}',
                ObjectSerializer::toPathValue($company_id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', 'application/problem+json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (JWT) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }
        // this endpoint requires OAuth (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }
        // this endpoint requires OAuth (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation apiVversionCompanyIdSpesaPost
     *
     * Salva un nuovo elemento.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  string|null $idempotency_key Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. (optional)
     * @param  \Amica\Sdk\Model\Spesa|null $spesa Istanza dell&#39;elemento da salvare. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaPost'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amica\Sdk\Model\Spesa|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails
     */
    public function apiVversionCompanyIdSpesaPost($version, $company_id, $validation_override = null, $idempotency_key = null, $spesa = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaPost'][0])
    {
        list($response) = $this->apiVversionCompanyIdSpesaPostWithHttpInfo($version, $company_id, $validation_override, $idempotency_key, $spesa, $contentType);
        return $response;
    }

    /**
     * Operation apiVversionCompanyIdSpesaPostWithHttpInfo
     *
     * Salva un nuovo elemento.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  string|null $idempotency_key Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. (optional)
     * @param  \Amica\Sdk\Model\Spesa|null $spesa Istanza dell&#39;elemento da salvare. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaPost'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amica\Sdk\Model\Spesa|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails, HTTP status code, HTTP response headers (array of strings)
     */
    public function apiVversionCompanyIdSpesaPostWithHttpInfo($version, $company_id, $validation_override = null, $idempotency_key = null, $spesa = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaPost'][0])
    {
        $request = $this->apiVversionCompanyIdSpesaPostRequest($version, $company_id, $validation_override, $idempotency_key, $spesa, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();


            switch($statusCode) {
                case 201:
                    if ('\Amica\Sdk\Model\Spesa' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\Spesa' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\Spesa', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 409:
                    if ('\Amica\Sdk\Model\ProblemDetails' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\ProblemDetails' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\ProblemDetails', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                    if ('\Amica\Sdk\Model\ProblemDetails' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\ProblemDetails' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\ProblemDetails', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 403:
                    if ('\Amica\Sdk\Model\ProblemDetails' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\ProblemDetails' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\ProblemDetails', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            $returnType = '\Amica\Sdk\Model\Spesa';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    try {
                        $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                    } catch (\JsonException $exception) {
                        throw new ApiException(
                            sprintf(
                                'Error JSON decoding server response (%s)',
                                $request->getUri()
                            ),
                            $statusCode,
                            $response->getHeaders(),
                            $content
                        );
                    }
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 201:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\Spesa',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\ProblemDetails',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\ProblemDetails',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\ProblemDetails',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation apiVversionCompanyIdSpesaPostAsync
     *
     * Salva un nuovo elemento.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  string|null $idempotency_key Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. (optional)
     * @param  \Amica\Sdk\Model\Spesa|null $spesa Istanza dell&#39;elemento da salvare. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaPost'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function apiVversionCompanyIdSpesaPostAsync($version, $company_id, $validation_override = null, $idempotency_key = null, $spesa = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaPost'][0])
    {
        return $this->apiVversionCompanyIdSpesaPostAsyncWithHttpInfo($version, $company_id, $validation_override, $idempotency_key, $spesa, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation apiVversionCompanyIdSpesaPostAsyncWithHttpInfo
     *
     * Salva un nuovo elemento.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  string|null $idempotency_key Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. (optional)
     * @param  \Amica\Sdk\Model\Spesa|null $spesa Istanza dell&#39;elemento da salvare. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaPost'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function apiVversionCompanyIdSpesaPostAsyncWithHttpInfo($version, $company_id, $validation_override = null, $idempotency_key = null, $spesa = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaPost'][0])
    {
        $returnType = '\Amica\Sdk\Model\Spesa';
        $request = $this->apiVversionCompanyIdSpesaPostRequest($version, $company_id, $validation_override, $idempotency_key, $spesa, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'apiVversionCompanyIdSpesaPost'
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  string|null $idempotency_key Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. (optional)
     * @param  \Amica\Sdk\Model\Spesa|null $spesa Istanza dell&#39;elemento da salvare. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaPost'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function apiVversionCompanyIdSpesaPostRequest($version, $company_id, $validation_override = null, $idempotency_key = null, $spesa = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaPost'][0])
    {

        // verify the required parameter 'version' is set
        if ($version === null || (is_array($version) && count($version) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $version when calling apiVversionCompanyIdSpesaPost'
            );
        }

        // verify the required parameter 'company_id' is set
        if ($company_id === null || (is_array($company_id) && count($company_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $company_id when calling apiVversionCompanyIdSpesaPost'
            );
        }





        $resourcePath = '/api/v{version}/{companyId}/Spesa';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $validation_override,
            'validationOverride', // param base name
            'boolean', // openApiType
            'form', // style
            true, // explode
            false // required
        ) ?? []);

        // header params
        if ($idempotency_key !== null) {
            $headerParams['Idempotency-Key'] = ObjectSerializer::toHeaderValue($idempotency_key);
        }

        // path params
        if ($version !== null) {
            $resourcePath = str_replace(
                '{' . 'version' . '}',
                ObjectSerializer::toPathValue($version),
                $resourcePath
            );
        }
        // path params
        if ($company_id !== null) {
            $resourcePath = str_replace(
                '{' . 'companyId' . '}',
                ObjectSerializer::toPathValue($company_id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', 'application/problem+json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($spesa)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($spesa));
            } else {
                $httpBody = $spesa;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (JWT) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }
        // this endpoint requires OAuth (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }
        // this endpoint requires OAuth (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'POST',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Create http client option
     *
     * @throws \RuntimeException on file opening failure
     * @return array of http client options
     */
    protected function createHttpClientOption()
    {
        $options = [];
        if ($this->config->getDebug()) {
            $options[RequestOptions::DEBUG] = fopen($this->config->getDebugFile(), 'a');
            if (!$options[RequestOptions::DEBUG]) {
                throw new \RuntimeException('Failed to open the debug file: ' . $this->config->getDebugFile());
            }
        }

        return $options;
    }
}
