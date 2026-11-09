<?php
/**
 * BeneServizioApi
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
 * The version of the OpenAPI document: 3.3.4
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
 * BeneServizioApi Class Doc Comment
 *
 * @category Class
 * @package  Amica\Sdk
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */
class BeneServizioApi
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
        'beneServizioCollegaDocumento' => [
            'application/json',
            'text/json',
            'application/*+json',
        ],
        'beneServizioCompute' => [
            'application/json',
            'text/json',
            'application/*+json',
        ],
        'beneServizioDelete' => [
            'application/json',
        ],
        'beneServizioGet' => [
            'application/json',
        ],
        'beneServizioList' => [
            'application/json',
        ],
        'beneServizioNewInstance' => [
            'application/json',
        ],
        'beneServizioPost' => [
            'application/json',
            'text/json',
            'application/*+json',
        ],
        'beneServizioPut' => [
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
     * Operation beneServizioCollegaDocumento
     *
     * Collega un bene/servizio a un documento.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  string|null $idempotency_key Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. (optional)
     * @param  \Amica\Sdk\Model\CollegaDocumentoData|null $collega_documento_data Dati per il collegamento. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioCollegaDocumento'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amica\Sdk\Model\BeneServizio|\Amica\Sdk\Model\ProblemDetails
     */
    public function beneServizioCollegaDocumento($company_id, $idempotency_key = null, $collega_documento_data = null, string $contentType = self::contentTypes['beneServizioCollegaDocumento'][0])
    {
        list($response) = $this->beneServizioCollegaDocumentoWithHttpInfo($company_id, $idempotency_key, $collega_documento_data, $contentType);
        return $response;
    }

    /**
     * Operation beneServizioCollegaDocumentoWithHttpInfo
     *
     * Collega un bene/servizio a un documento.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  string|null $idempotency_key Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. (optional)
     * @param  \Amica\Sdk\Model\CollegaDocumentoData|null $collega_documento_data Dati per il collegamento. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioCollegaDocumento'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amica\Sdk\Model\BeneServizio|\Amica\Sdk\Model\ProblemDetails, HTTP status code, HTTP response headers (array of strings)
     */
    public function beneServizioCollegaDocumentoWithHttpInfo($company_id, $idempotency_key = null, $collega_documento_data = null, string $contentType = self::contentTypes['beneServizioCollegaDocumento'][0])
    {
        $request = $this->beneServizioCollegaDocumentoRequest($company_id, $idempotency_key, $collega_documento_data, $contentType);

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
                    if ('\Amica\Sdk\Model\BeneServizio' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\BeneServizio' !== 'string') {
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
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\BeneServizio', []),
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

            $returnType = '\Amica\Sdk\Model\BeneServizio';
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
                        '\Amica\Sdk\Model\BeneServizio',
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
     * Operation beneServizioCollegaDocumentoAsync
     *
     * Collega un bene/servizio a un documento.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  string|null $idempotency_key Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. (optional)
     * @param  \Amica\Sdk\Model\CollegaDocumentoData|null $collega_documento_data Dati per il collegamento. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioCollegaDocumento'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function beneServizioCollegaDocumentoAsync($company_id, $idempotency_key = null, $collega_documento_data = null, string $contentType = self::contentTypes['beneServizioCollegaDocumento'][0])
    {
        return $this->beneServizioCollegaDocumentoAsyncWithHttpInfo($company_id, $idempotency_key, $collega_documento_data, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation beneServizioCollegaDocumentoAsyncWithHttpInfo
     *
     * Collega un bene/servizio a un documento.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  string|null $idempotency_key Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. (optional)
     * @param  \Amica\Sdk\Model\CollegaDocumentoData|null $collega_documento_data Dati per il collegamento. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioCollegaDocumento'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function beneServizioCollegaDocumentoAsyncWithHttpInfo($company_id, $idempotency_key = null, $collega_documento_data = null, string $contentType = self::contentTypes['beneServizioCollegaDocumento'][0])
    {
        $returnType = '\Amica\Sdk\Model\BeneServizio';
        $request = $this->beneServizioCollegaDocumentoRequest($company_id, $idempotency_key, $collega_documento_data, $contentType);

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
     * Create request for operation 'beneServizioCollegaDocumento'
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  string|null $idempotency_key Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. (optional)
     * @param  \Amica\Sdk\Model\CollegaDocumentoData|null $collega_documento_data Dati per il collegamento. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioCollegaDocumento'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function beneServizioCollegaDocumentoRequest($company_id, $idempotency_key = null, $collega_documento_data = null, string $contentType = self::contentTypes['beneServizioCollegaDocumento'][0])
    {

        // verify the required parameter 'company_id' is set
        if ($company_id === null || (is_array($company_id) && count($company_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $company_id when calling beneServizioCollegaDocumento'
            );
        }




        $resourcePath = '/api/v3/{companyId}/BeneServizio/collegadocumento';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;


        // header params
        if ($idempotency_key !== null) {
            $headerParams['Idempotency-Key'] = ObjectSerializer::toHeaderValue($idempotency_key);
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
        if (isset($collega_documento_data)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($collega_documento_data));
            } else {
                $httpBody = $collega_documento_data;
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
     * Operation beneServizioCompute
     *
     * Calcola gli importi di un &#x60;BeneServizio&#x60; (vedi &#x60;BeneServizioComputeResult&#x60;), considerando parametri come quantità movimentata, IVA applicata, eventuali sconti.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  string|null $idempotency_key Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. (optional)
     * @param  \Amica\Sdk\Model\BeneServizioComputeData|null $bene_servizio_compute_data Dati di input per il calcolo. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioCompute'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amica\Sdk\Model\BeneServizioComputeResult|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails
     */
    public function beneServizioCompute($company_id, $idempotency_key = null, $bene_servizio_compute_data = null, string $contentType = self::contentTypes['beneServizioCompute'][0])
    {
        list($response) = $this->beneServizioComputeWithHttpInfo($company_id, $idempotency_key, $bene_servizio_compute_data, $contentType);
        return $response;
    }

    /**
     * Operation beneServizioComputeWithHttpInfo
     *
     * Calcola gli importi di un &#x60;BeneServizio&#x60; (vedi &#x60;BeneServizioComputeResult&#x60;), considerando parametri come quantità movimentata, IVA applicata, eventuali sconti.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  string|null $idempotency_key Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. (optional)
     * @param  \Amica\Sdk\Model\BeneServizioComputeData|null $bene_servizio_compute_data Dati di input per il calcolo. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioCompute'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amica\Sdk\Model\BeneServizioComputeResult|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails, HTTP status code, HTTP response headers (array of strings)
     */
    public function beneServizioComputeWithHttpInfo($company_id, $idempotency_key = null, $bene_servizio_compute_data = null, string $contentType = self::contentTypes['beneServizioCompute'][0])
    {
        $request = $this->beneServizioComputeRequest($company_id, $idempotency_key, $bene_servizio_compute_data, $contentType);

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
                    if ('\Amica\Sdk\Model\BeneServizioComputeResult' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\BeneServizioComputeResult' !== 'string') {
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
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\BeneServizioComputeResult', []),
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

            $returnType = '\Amica\Sdk\Model\BeneServizioComputeResult';
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
                        '\Amica\Sdk\Model\BeneServizioComputeResult',
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
     * Operation beneServizioComputeAsync
     *
     * Calcola gli importi di un &#x60;BeneServizio&#x60; (vedi &#x60;BeneServizioComputeResult&#x60;), considerando parametri come quantità movimentata, IVA applicata, eventuali sconti.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  string|null $idempotency_key Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. (optional)
     * @param  \Amica\Sdk\Model\BeneServizioComputeData|null $bene_servizio_compute_data Dati di input per il calcolo. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioCompute'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function beneServizioComputeAsync($company_id, $idempotency_key = null, $bene_servizio_compute_data = null, string $contentType = self::contentTypes['beneServizioCompute'][0])
    {
        return $this->beneServizioComputeAsyncWithHttpInfo($company_id, $idempotency_key, $bene_servizio_compute_data, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation beneServizioComputeAsyncWithHttpInfo
     *
     * Calcola gli importi di un &#x60;BeneServizio&#x60; (vedi &#x60;BeneServizioComputeResult&#x60;), considerando parametri come quantità movimentata, IVA applicata, eventuali sconti.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  string|null $idempotency_key Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. (optional)
     * @param  \Amica\Sdk\Model\BeneServizioComputeData|null $bene_servizio_compute_data Dati di input per il calcolo. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioCompute'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function beneServizioComputeAsyncWithHttpInfo($company_id, $idempotency_key = null, $bene_servizio_compute_data = null, string $contentType = self::contentTypes['beneServizioCompute'][0])
    {
        $returnType = '\Amica\Sdk\Model\BeneServizioComputeResult';
        $request = $this->beneServizioComputeRequest($company_id, $idempotency_key, $bene_servizio_compute_data, $contentType);

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
     * Create request for operation 'beneServizioCompute'
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  string|null $idempotency_key Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. (optional)
     * @param  \Amica\Sdk\Model\BeneServizioComputeData|null $bene_servizio_compute_data Dati di input per il calcolo. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioCompute'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function beneServizioComputeRequest($company_id, $idempotency_key = null, $bene_servizio_compute_data = null, string $contentType = self::contentTypes['beneServizioCompute'][0])
    {

        // verify the required parameter 'company_id' is set
        if ($company_id === null || (is_array($company_id) && count($company_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $company_id when calling beneServizioCompute'
            );
        }




        $resourcePath = '/api/v3/{companyId}/BeneServizio/compute';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;


        // header params
        if ($idempotency_key !== null) {
            $headerParams['Idempotency-Key'] = ObjectSerializer::toHeaderValue($idempotency_key);
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
        if (isset($bene_servizio_compute_data)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($bene_servizio_compute_data));
            } else {
                $httpBody = $bene_servizio_compute_data;
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
     * Operation beneServizioDelete
     *
     * Elimina un elemento.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID dell&#39;elemento da eliminare. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioDelete'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amica\Sdk\Model\BeneServizio|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails
     */
    public function beneServizioDelete($company_id, $id, $validation_override = null, string $contentType = self::contentTypes['beneServizioDelete'][0])
    {
        list($response) = $this->beneServizioDeleteWithHttpInfo($company_id, $id, $validation_override, $contentType);
        return $response;
    }

    /**
     * Operation beneServizioDeleteWithHttpInfo
     *
     * Elimina un elemento.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID dell&#39;elemento da eliminare. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioDelete'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amica\Sdk\Model\BeneServizio|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails, HTTP status code, HTTP response headers (array of strings)
     */
    public function beneServizioDeleteWithHttpInfo($company_id, $id, $validation_override = null, string $contentType = self::contentTypes['beneServizioDelete'][0])
    {
        $request = $this->beneServizioDeleteRequest($company_id, $id, $validation_override, $contentType);

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
                    if ('\Amica\Sdk\Model\BeneServizio' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\BeneServizio' !== 'string') {
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
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\BeneServizio', []),
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

            $returnType = '\Amica\Sdk\Model\BeneServizio';
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
                        '\Amica\Sdk\Model\BeneServizio',
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
     * Operation beneServizioDeleteAsync
     *
     * Elimina un elemento.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID dell&#39;elemento da eliminare. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioDelete'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function beneServizioDeleteAsync($company_id, $id, $validation_override = null, string $contentType = self::contentTypes['beneServizioDelete'][0])
    {
        return $this->beneServizioDeleteAsyncWithHttpInfo($company_id, $id, $validation_override, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation beneServizioDeleteAsyncWithHttpInfo
     *
     * Elimina un elemento.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID dell&#39;elemento da eliminare. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioDelete'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function beneServizioDeleteAsyncWithHttpInfo($company_id, $id, $validation_override = null, string $contentType = self::contentTypes['beneServizioDelete'][0])
    {
        $returnType = '\Amica\Sdk\Model\BeneServizio';
        $request = $this->beneServizioDeleteRequest($company_id, $id, $validation_override, $contentType);

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
     * Create request for operation 'beneServizioDelete'
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID dell&#39;elemento da eliminare. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioDelete'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function beneServizioDeleteRequest($company_id, $id, $validation_override = null, string $contentType = self::contentTypes['beneServizioDelete'][0])
    {

        // verify the required parameter 'company_id' is set
        if ($company_id === null || (is_array($company_id) && count($company_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $company_id when calling beneServizioDelete'
            );
        }

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $id when calling beneServizioDelete'
            );
        }



        $resourcePath = '/api/v3/{companyId}/BeneServizio/{id}';
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
     * Operation beneServizioGet
     *
     * Restituisce un elemento.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID dell&#39;elemento da recuperare. (required)
     * @param  string[]|null $expand Elenco separato da virgole di oggetti da espandere. Permette di recuperare gli oggetti correlati in un&#39;unica chiamata. Ad esempio, per includere i beni e servizi associati a ciascun documento si aggiunge &#x60;expand&#x3D;Rows&#x60; alla chiamata. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioGet'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amica\Sdk\Model\BeneServizio|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails
     */
    public function beneServizioGet($company_id, $id, $expand = null, string $contentType = self::contentTypes['beneServizioGet'][0])
    {
        list($response) = $this->beneServizioGetWithHttpInfo($company_id, $id, $expand, $contentType);
        return $response;
    }

    /**
     * Operation beneServizioGetWithHttpInfo
     *
     * Restituisce un elemento.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID dell&#39;elemento da recuperare. (required)
     * @param  string[]|null $expand Elenco separato da virgole di oggetti da espandere. Permette di recuperare gli oggetti correlati in un&#39;unica chiamata. Ad esempio, per includere i beni e servizi associati a ciascun documento si aggiunge &#x60;expand&#x3D;Rows&#x60; alla chiamata. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioGet'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amica\Sdk\Model\BeneServizio|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails, HTTP status code, HTTP response headers (array of strings)
     */
    public function beneServizioGetWithHttpInfo($company_id, $id, $expand = null, string $contentType = self::contentTypes['beneServizioGet'][0])
    {
        $request = $this->beneServizioGetRequest($company_id, $id, $expand, $contentType);

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
                    if ('\Amica\Sdk\Model\BeneServizio' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\BeneServizio' !== 'string') {
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
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\BeneServizio', []),
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

            $returnType = '\Amica\Sdk\Model\BeneServizio';
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
                        '\Amica\Sdk\Model\BeneServizio',
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
     * Operation beneServizioGetAsync
     *
     * Restituisce un elemento.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID dell&#39;elemento da recuperare. (required)
     * @param  string[]|null $expand Elenco separato da virgole di oggetti da espandere. Permette di recuperare gli oggetti correlati in un&#39;unica chiamata. Ad esempio, per includere i beni e servizi associati a ciascun documento si aggiunge &#x60;expand&#x3D;Rows&#x60; alla chiamata. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioGet'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function beneServizioGetAsync($company_id, $id, $expand = null, string $contentType = self::contentTypes['beneServizioGet'][0])
    {
        return $this->beneServizioGetAsyncWithHttpInfo($company_id, $id, $expand, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation beneServizioGetAsyncWithHttpInfo
     *
     * Restituisce un elemento.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID dell&#39;elemento da recuperare. (required)
     * @param  string[]|null $expand Elenco separato da virgole di oggetti da espandere. Permette di recuperare gli oggetti correlati in un&#39;unica chiamata. Ad esempio, per includere i beni e servizi associati a ciascun documento si aggiunge &#x60;expand&#x3D;Rows&#x60; alla chiamata. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioGet'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function beneServizioGetAsyncWithHttpInfo($company_id, $id, $expand = null, string $contentType = self::contentTypes['beneServizioGet'][0])
    {
        $returnType = '\Amica\Sdk\Model\BeneServizio';
        $request = $this->beneServizioGetRequest($company_id, $id, $expand, $contentType);

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
     * Create request for operation 'beneServizioGet'
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID dell&#39;elemento da recuperare. (required)
     * @param  string[]|null $expand Elenco separato da virgole di oggetti da espandere. Permette di recuperare gli oggetti correlati in un&#39;unica chiamata. Ad esempio, per includere i beni e servizi associati a ciascun documento si aggiunge &#x60;expand&#x3D;Rows&#x60; alla chiamata. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioGet'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function beneServizioGetRequest($company_id, $id, $expand = null, string $contentType = self::contentTypes['beneServizioGet'][0])
    {

        // verify the required parameter 'company_id' is set
        if ($company_id === null || (is_array($company_id) && count($company_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $company_id when calling beneServizioGet'
            );
        }

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $id when calling beneServizioGet'
            );
        }



        $resourcePath = '/api/v3/{companyId}/BeneServizio/{id}';
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
     * Operation beneServizioList
     *
     * Restituisce una lista di elementi.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int|null $skip Numero di elementi da escludere dall&#39;inizio del risultato. Combinato con &#x60;Take&#x60;, è utile per la paginazione. (optional)
     * @param  int|null $take Numero di elementi da includere nel risultato. Combinato con &#x60;Skip&#x60;, è utile per la paginazione. (optional)
     * @param  bool|null $skip_total_count Indica se l&#39;header &#x60;X-Read-TotalCount&#x60; debba essere escluso dalla risposta. Impostare a &#x60;true&#x60; per prestazioni migliori a scapito dell&#39;accuratezza. Il valore predefinito è &#x60;false&#x60;. (optional)
     * @param  string|null $filter Condizioni di ricerca. Elenco di espressioni separate da virgola. Ogni espressione è composta da nome del campo, operatore di confronto, valore ed eventualmente un connettore logico. Operatori validi: &#x60;$eq&#x60; (uguale), &#x60;$ne&#x60; (diverso), &#x60;$gt&#x60; (maggiore di), &#x60;$lt&#x60; (minore di), &#x60;$lte&#x60; (minore o uguale), &#x60;$gte&#x60; (maggiore o uguale), &#x60;$ct&#x60; (contiene), &#x60;$nct&#x60; (non contiene), &#x60;$sw&#x60; (inizia con), &#x60;$ew&#x60; (finisce con). Più espressioni possono essere concatenate con un connettore logico &#x60;$and&#x60; o &#x60;$or&#x60; (&#x60;$and&#x60; può essere omesso). Le query annidate si esprimono racchiudendo le sotto-query tra parentesi quadre (&#x60;[]&#x60;). Nell&#39;esempio vengono inclusi tutti gli elementi con &#x60;field1&#x3D;\&quot;hello\&quot;&#x60; oppure &#x60;field2&gt;10&#x60;. (optional)
     * @param  string|null $order Opzioni di ordinamento. Un segno meno (trattino) prima del nome indica ordinamento discendente. Nell&#39;esempio i risultati sono ordinati prima per &#x60;field1&#x60; (discendente), poi per &#x60;field2&#x60;. (optional)
     * @param  string[]|null $expand Elenco separato da virgole di oggetti da espandere. Permette di recuperare gli oggetti correlati in un&#39;unica chiamata. Ad esempio, per includere i beni e servizi associati a ciascun documento si aggiunge &#x60;expand&#x3D;Rows&#x60; alla chiamata. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioList'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amica\Sdk\Model\BeneServizio[]|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails
     */
    public function beneServizioList($company_id, $skip = null, $take = null, $skip_total_count = null, $filter = null, $order = null, $expand = null, string $contentType = self::contentTypes['beneServizioList'][0])
    {
        list($response) = $this->beneServizioListWithHttpInfo($company_id, $skip, $take, $skip_total_count, $filter, $order, $expand, $contentType);
        return $response;
    }

    /**
     * Operation beneServizioListWithHttpInfo
     *
     * Restituisce una lista di elementi.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int|null $skip Numero di elementi da escludere dall&#39;inizio del risultato. Combinato con &#x60;Take&#x60;, è utile per la paginazione. (optional)
     * @param  int|null $take Numero di elementi da includere nel risultato. Combinato con &#x60;Skip&#x60;, è utile per la paginazione. (optional)
     * @param  bool|null $skip_total_count Indica se l&#39;header &#x60;X-Read-TotalCount&#x60; debba essere escluso dalla risposta. Impostare a &#x60;true&#x60; per prestazioni migliori a scapito dell&#39;accuratezza. Il valore predefinito è &#x60;false&#x60;. (optional)
     * @param  string|null $filter Condizioni di ricerca. Elenco di espressioni separate da virgola. Ogni espressione è composta da nome del campo, operatore di confronto, valore ed eventualmente un connettore logico. Operatori validi: &#x60;$eq&#x60; (uguale), &#x60;$ne&#x60; (diverso), &#x60;$gt&#x60; (maggiore di), &#x60;$lt&#x60; (minore di), &#x60;$lte&#x60; (minore o uguale), &#x60;$gte&#x60; (maggiore o uguale), &#x60;$ct&#x60; (contiene), &#x60;$nct&#x60; (non contiene), &#x60;$sw&#x60; (inizia con), &#x60;$ew&#x60; (finisce con). Più espressioni possono essere concatenate con un connettore logico &#x60;$and&#x60; o &#x60;$or&#x60; (&#x60;$and&#x60; può essere omesso). Le query annidate si esprimono racchiudendo le sotto-query tra parentesi quadre (&#x60;[]&#x60;). Nell&#39;esempio vengono inclusi tutti gli elementi con &#x60;field1&#x3D;\&quot;hello\&quot;&#x60; oppure &#x60;field2&gt;10&#x60;. (optional)
     * @param  string|null $order Opzioni di ordinamento. Un segno meno (trattino) prima del nome indica ordinamento discendente. Nell&#39;esempio i risultati sono ordinati prima per &#x60;field1&#x60; (discendente), poi per &#x60;field2&#x60;. (optional)
     * @param  string[]|null $expand Elenco separato da virgole di oggetti da espandere. Permette di recuperare gli oggetti correlati in un&#39;unica chiamata. Ad esempio, per includere i beni e servizi associati a ciascun documento si aggiunge &#x60;expand&#x3D;Rows&#x60; alla chiamata. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioList'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amica\Sdk\Model\BeneServizio[]|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails, HTTP status code, HTTP response headers (array of strings)
     */
    public function beneServizioListWithHttpInfo($company_id, $skip = null, $take = null, $skip_total_count = null, $filter = null, $order = null, $expand = null, string $contentType = self::contentTypes['beneServizioList'][0])
    {
        $request = $this->beneServizioListRequest($company_id, $skip, $take, $skip_total_count, $filter, $order, $expand, $contentType);

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
                    if ('\Amica\Sdk\Model\BeneServizio[]' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\BeneServizio[]' !== 'string') {
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
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\BeneServizio[]', []),
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

            $returnType = '\Amica\Sdk\Model\BeneServizio[]';
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
                        '\Amica\Sdk\Model\BeneServizio[]',
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
     * Operation beneServizioListAsync
     *
     * Restituisce una lista di elementi.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int|null $skip Numero di elementi da escludere dall&#39;inizio del risultato. Combinato con &#x60;Take&#x60;, è utile per la paginazione. (optional)
     * @param  int|null $take Numero di elementi da includere nel risultato. Combinato con &#x60;Skip&#x60;, è utile per la paginazione. (optional)
     * @param  bool|null $skip_total_count Indica se l&#39;header &#x60;X-Read-TotalCount&#x60; debba essere escluso dalla risposta. Impostare a &#x60;true&#x60; per prestazioni migliori a scapito dell&#39;accuratezza. Il valore predefinito è &#x60;false&#x60;. (optional)
     * @param  string|null $filter Condizioni di ricerca. Elenco di espressioni separate da virgola. Ogni espressione è composta da nome del campo, operatore di confronto, valore ed eventualmente un connettore logico. Operatori validi: &#x60;$eq&#x60; (uguale), &#x60;$ne&#x60; (diverso), &#x60;$gt&#x60; (maggiore di), &#x60;$lt&#x60; (minore di), &#x60;$lte&#x60; (minore o uguale), &#x60;$gte&#x60; (maggiore o uguale), &#x60;$ct&#x60; (contiene), &#x60;$nct&#x60; (non contiene), &#x60;$sw&#x60; (inizia con), &#x60;$ew&#x60; (finisce con). Più espressioni possono essere concatenate con un connettore logico &#x60;$and&#x60; o &#x60;$or&#x60; (&#x60;$and&#x60; può essere omesso). Le query annidate si esprimono racchiudendo le sotto-query tra parentesi quadre (&#x60;[]&#x60;). Nell&#39;esempio vengono inclusi tutti gli elementi con &#x60;field1&#x3D;\&quot;hello\&quot;&#x60; oppure &#x60;field2&gt;10&#x60;. (optional)
     * @param  string|null $order Opzioni di ordinamento. Un segno meno (trattino) prima del nome indica ordinamento discendente. Nell&#39;esempio i risultati sono ordinati prima per &#x60;field1&#x60; (discendente), poi per &#x60;field2&#x60;. (optional)
     * @param  string[]|null $expand Elenco separato da virgole di oggetti da espandere. Permette di recuperare gli oggetti correlati in un&#39;unica chiamata. Ad esempio, per includere i beni e servizi associati a ciascun documento si aggiunge &#x60;expand&#x3D;Rows&#x60; alla chiamata. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioList'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function beneServizioListAsync($company_id, $skip = null, $take = null, $skip_total_count = null, $filter = null, $order = null, $expand = null, string $contentType = self::contentTypes['beneServizioList'][0])
    {
        return $this->beneServizioListAsyncWithHttpInfo($company_id, $skip, $take, $skip_total_count, $filter, $order, $expand, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation beneServizioListAsyncWithHttpInfo
     *
     * Restituisce una lista di elementi.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int|null $skip Numero di elementi da escludere dall&#39;inizio del risultato. Combinato con &#x60;Take&#x60;, è utile per la paginazione. (optional)
     * @param  int|null $take Numero di elementi da includere nel risultato. Combinato con &#x60;Skip&#x60;, è utile per la paginazione. (optional)
     * @param  bool|null $skip_total_count Indica se l&#39;header &#x60;X-Read-TotalCount&#x60; debba essere escluso dalla risposta. Impostare a &#x60;true&#x60; per prestazioni migliori a scapito dell&#39;accuratezza. Il valore predefinito è &#x60;false&#x60;. (optional)
     * @param  string|null $filter Condizioni di ricerca. Elenco di espressioni separate da virgola. Ogni espressione è composta da nome del campo, operatore di confronto, valore ed eventualmente un connettore logico. Operatori validi: &#x60;$eq&#x60; (uguale), &#x60;$ne&#x60; (diverso), &#x60;$gt&#x60; (maggiore di), &#x60;$lt&#x60; (minore di), &#x60;$lte&#x60; (minore o uguale), &#x60;$gte&#x60; (maggiore o uguale), &#x60;$ct&#x60; (contiene), &#x60;$nct&#x60; (non contiene), &#x60;$sw&#x60; (inizia con), &#x60;$ew&#x60; (finisce con). Più espressioni possono essere concatenate con un connettore logico &#x60;$and&#x60; o &#x60;$or&#x60; (&#x60;$and&#x60; può essere omesso). Le query annidate si esprimono racchiudendo le sotto-query tra parentesi quadre (&#x60;[]&#x60;). Nell&#39;esempio vengono inclusi tutti gli elementi con &#x60;field1&#x3D;\&quot;hello\&quot;&#x60; oppure &#x60;field2&gt;10&#x60;. (optional)
     * @param  string|null $order Opzioni di ordinamento. Un segno meno (trattino) prima del nome indica ordinamento discendente. Nell&#39;esempio i risultati sono ordinati prima per &#x60;field1&#x60; (discendente), poi per &#x60;field2&#x60;. (optional)
     * @param  string[]|null $expand Elenco separato da virgole di oggetti da espandere. Permette di recuperare gli oggetti correlati in un&#39;unica chiamata. Ad esempio, per includere i beni e servizi associati a ciascun documento si aggiunge &#x60;expand&#x3D;Rows&#x60; alla chiamata. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioList'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function beneServizioListAsyncWithHttpInfo($company_id, $skip = null, $take = null, $skip_total_count = null, $filter = null, $order = null, $expand = null, string $contentType = self::contentTypes['beneServizioList'][0])
    {
        $returnType = '\Amica\Sdk\Model\BeneServizio[]';
        $request = $this->beneServizioListRequest($company_id, $skip, $take, $skip_total_count, $filter, $order, $expand, $contentType);

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
     * Create request for operation 'beneServizioList'
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int|null $skip Numero di elementi da escludere dall&#39;inizio del risultato. Combinato con &#x60;Take&#x60;, è utile per la paginazione. (optional)
     * @param  int|null $take Numero di elementi da includere nel risultato. Combinato con &#x60;Skip&#x60;, è utile per la paginazione. (optional)
     * @param  bool|null $skip_total_count Indica se l&#39;header &#x60;X-Read-TotalCount&#x60; debba essere escluso dalla risposta. Impostare a &#x60;true&#x60; per prestazioni migliori a scapito dell&#39;accuratezza. Il valore predefinito è &#x60;false&#x60;. (optional)
     * @param  string|null $filter Condizioni di ricerca. Elenco di espressioni separate da virgola. Ogni espressione è composta da nome del campo, operatore di confronto, valore ed eventualmente un connettore logico. Operatori validi: &#x60;$eq&#x60; (uguale), &#x60;$ne&#x60; (diverso), &#x60;$gt&#x60; (maggiore di), &#x60;$lt&#x60; (minore di), &#x60;$lte&#x60; (minore o uguale), &#x60;$gte&#x60; (maggiore o uguale), &#x60;$ct&#x60; (contiene), &#x60;$nct&#x60; (non contiene), &#x60;$sw&#x60; (inizia con), &#x60;$ew&#x60; (finisce con). Più espressioni possono essere concatenate con un connettore logico &#x60;$and&#x60; o &#x60;$or&#x60; (&#x60;$and&#x60; può essere omesso). Le query annidate si esprimono racchiudendo le sotto-query tra parentesi quadre (&#x60;[]&#x60;). Nell&#39;esempio vengono inclusi tutti gli elementi con &#x60;field1&#x3D;\&quot;hello\&quot;&#x60; oppure &#x60;field2&gt;10&#x60;. (optional)
     * @param  string|null $order Opzioni di ordinamento. Un segno meno (trattino) prima del nome indica ordinamento discendente. Nell&#39;esempio i risultati sono ordinati prima per &#x60;field1&#x60; (discendente), poi per &#x60;field2&#x60;. (optional)
     * @param  string[]|null $expand Elenco separato da virgole di oggetti da espandere. Permette di recuperare gli oggetti correlati in un&#39;unica chiamata. Ad esempio, per includere i beni e servizi associati a ciascun documento si aggiunge &#x60;expand&#x3D;Rows&#x60; alla chiamata. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioList'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function beneServizioListRequest($company_id, $skip = null, $take = null, $skip_total_count = null, $filter = null, $order = null, $expand = null, string $contentType = self::contentTypes['beneServizioList'][0])
    {

        // verify the required parameter 'company_id' is set
        if ($company_id === null || (is_array($company_id) && count($company_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $company_id when calling beneServizioList'
            );
        }








        $resourcePath = '/api/v3/{companyId}/BeneServizio';
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
     * Operation beneServizioNewInstance
     *
     * Restituisce una nuova istanza di un bene/servizio.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioNewInstance'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amica\Sdk\Model\BeneServizio|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails
     */
    public function beneServizioNewInstance($company_id, string $contentType = self::contentTypes['beneServizioNewInstance'][0])
    {
        list($response) = $this->beneServizioNewInstanceWithHttpInfo($company_id, $contentType);
        return $response;
    }

    /**
     * Operation beneServizioNewInstanceWithHttpInfo
     *
     * Restituisce una nuova istanza di un bene/servizio.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioNewInstance'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amica\Sdk\Model\BeneServizio|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails, HTTP status code, HTTP response headers (array of strings)
     */
    public function beneServizioNewInstanceWithHttpInfo($company_id, string $contentType = self::contentTypes['beneServizioNewInstance'][0])
    {
        $request = $this->beneServizioNewInstanceRequest($company_id, $contentType);

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
                    if ('\Amica\Sdk\Model\BeneServizio' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\BeneServizio' !== 'string') {
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
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\BeneServizio', []),
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

            $returnType = '\Amica\Sdk\Model\BeneServizio';
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
                        '\Amica\Sdk\Model\BeneServizio',
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
     * Operation beneServizioNewInstanceAsync
     *
     * Restituisce una nuova istanza di un bene/servizio.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioNewInstance'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function beneServizioNewInstanceAsync($company_id, string $contentType = self::contentTypes['beneServizioNewInstance'][0])
    {
        return $this->beneServizioNewInstanceAsyncWithHttpInfo($company_id, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation beneServizioNewInstanceAsyncWithHttpInfo
     *
     * Restituisce una nuova istanza di un bene/servizio.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioNewInstance'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function beneServizioNewInstanceAsyncWithHttpInfo($company_id, string $contentType = self::contentTypes['beneServizioNewInstance'][0])
    {
        $returnType = '\Amica\Sdk\Model\BeneServizio';
        $request = $this->beneServizioNewInstanceRequest($company_id, $contentType);

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
     * Create request for operation 'beneServizioNewInstance'
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioNewInstance'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function beneServizioNewInstanceRequest($company_id, string $contentType = self::contentTypes['beneServizioNewInstance'][0])
    {

        // verify the required parameter 'company_id' is set
        if ($company_id === null || (is_array($company_id) && count($company_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $company_id when calling beneServizioNewInstance'
            );
        }


        $resourcePath = '/api/v3/{companyId}/BeneServizio/new';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



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
     * Operation beneServizioPost
     *
     * Salva un nuovo elemento.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  string|null $idempotency_key Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. (optional)
     * @param  \Amica\Sdk\Model\BeneServizio|null $bene_servizio Istanza dell&#39;elemento da salvare. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioPost'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amica\Sdk\Model\BeneServizio|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails
     */
    public function beneServizioPost($company_id, $validation_override = null, $idempotency_key = null, $bene_servizio = null, string $contentType = self::contentTypes['beneServizioPost'][0])
    {
        list($response) = $this->beneServizioPostWithHttpInfo($company_id, $validation_override, $idempotency_key, $bene_servizio, $contentType);
        return $response;
    }

    /**
     * Operation beneServizioPostWithHttpInfo
     *
     * Salva un nuovo elemento.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  string|null $idempotency_key Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. (optional)
     * @param  \Amica\Sdk\Model\BeneServizio|null $bene_servizio Istanza dell&#39;elemento da salvare. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioPost'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amica\Sdk\Model\BeneServizio|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails, HTTP status code, HTTP response headers (array of strings)
     */
    public function beneServizioPostWithHttpInfo($company_id, $validation_override = null, $idempotency_key = null, $bene_servizio = null, string $contentType = self::contentTypes['beneServizioPost'][0])
    {
        $request = $this->beneServizioPostRequest($company_id, $validation_override, $idempotency_key, $bene_servizio, $contentType);

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
                    if ('\Amica\Sdk\Model\BeneServizio' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\BeneServizio' !== 'string') {
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
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\BeneServizio', []),
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

            $returnType = '\Amica\Sdk\Model\BeneServizio';
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
                        '\Amica\Sdk\Model\BeneServizio',
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
     * Operation beneServizioPostAsync
     *
     * Salva un nuovo elemento.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  string|null $idempotency_key Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. (optional)
     * @param  \Amica\Sdk\Model\BeneServizio|null $bene_servizio Istanza dell&#39;elemento da salvare. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioPost'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function beneServizioPostAsync($company_id, $validation_override = null, $idempotency_key = null, $bene_servizio = null, string $contentType = self::contentTypes['beneServizioPost'][0])
    {
        return $this->beneServizioPostAsyncWithHttpInfo($company_id, $validation_override, $idempotency_key, $bene_servizio, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation beneServizioPostAsyncWithHttpInfo
     *
     * Salva un nuovo elemento.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  string|null $idempotency_key Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. (optional)
     * @param  \Amica\Sdk\Model\BeneServizio|null $bene_servizio Istanza dell&#39;elemento da salvare. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioPost'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function beneServizioPostAsyncWithHttpInfo($company_id, $validation_override = null, $idempotency_key = null, $bene_servizio = null, string $contentType = self::contentTypes['beneServizioPost'][0])
    {
        $returnType = '\Amica\Sdk\Model\BeneServizio';
        $request = $this->beneServizioPostRequest($company_id, $validation_override, $idempotency_key, $bene_servizio, $contentType);

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
     * Create request for operation 'beneServizioPost'
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  string|null $idempotency_key Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. (optional)
     * @param  \Amica\Sdk\Model\BeneServizio|null $bene_servizio Istanza dell&#39;elemento da salvare. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioPost'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function beneServizioPostRequest($company_id, $validation_override = null, $idempotency_key = null, $bene_servizio = null, string $contentType = self::contentTypes['beneServizioPost'][0])
    {

        // verify the required parameter 'company_id' is set
        if ($company_id === null || (is_array($company_id) && count($company_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $company_id when calling beneServizioPost'
            );
        }





        $resourcePath = '/api/v3/{companyId}/BeneServizio';
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
        if (isset($bene_servizio)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($bene_servizio));
            } else {
                $httpBody = $bene_servizio;
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
     * Operation beneServizioPut
     *
     * Aggiorna un elemento esistente.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID univoco dell&#39;elemento da aggiornare. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  string|null $idempotency_key Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. (optional)
     * @param  \Amica\Sdk\Model\BeneServizio|null $bene_servizio Istanza dell&#39;elemento da salvare. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioPut'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amica\Sdk\Model\BeneServizio|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails
     */
    public function beneServizioPut($company_id, $id, $validation_override = null, $idempotency_key = null, $bene_servizio = null, string $contentType = self::contentTypes['beneServizioPut'][0])
    {
        list($response) = $this->beneServizioPutWithHttpInfo($company_id, $id, $validation_override, $idempotency_key, $bene_servizio, $contentType);
        return $response;
    }

    /**
     * Operation beneServizioPutWithHttpInfo
     *
     * Aggiorna un elemento esistente.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID univoco dell&#39;elemento da aggiornare. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  string|null $idempotency_key Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. (optional)
     * @param  \Amica\Sdk\Model\BeneServizio|null $bene_servizio Istanza dell&#39;elemento da salvare. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioPut'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amica\Sdk\Model\BeneServizio|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails, HTTP status code, HTTP response headers (array of strings)
     */
    public function beneServizioPutWithHttpInfo($company_id, $id, $validation_override = null, $idempotency_key = null, $bene_servizio = null, string $contentType = self::contentTypes['beneServizioPut'][0])
    {
        $request = $this->beneServizioPutRequest($company_id, $id, $validation_override, $idempotency_key, $bene_servizio, $contentType);

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
                    if ('\Amica\Sdk\Model\BeneServizio' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\BeneServizio' !== 'string') {
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
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\BeneServizio', []),
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

            $returnType = '\Amica\Sdk\Model\BeneServizio';
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
                        '\Amica\Sdk\Model\BeneServizio',
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
     * Operation beneServizioPutAsync
     *
     * Aggiorna un elemento esistente.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID univoco dell&#39;elemento da aggiornare. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  string|null $idempotency_key Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. (optional)
     * @param  \Amica\Sdk\Model\BeneServizio|null $bene_servizio Istanza dell&#39;elemento da salvare. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioPut'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function beneServizioPutAsync($company_id, $id, $validation_override = null, $idempotency_key = null, $bene_servizio = null, string $contentType = self::contentTypes['beneServizioPut'][0])
    {
        return $this->beneServizioPutAsyncWithHttpInfo($company_id, $id, $validation_override, $idempotency_key, $bene_servizio, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation beneServizioPutAsyncWithHttpInfo
     *
     * Aggiorna un elemento esistente.
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID univoco dell&#39;elemento da aggiornare. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  string|null $idempotency_key Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. (optional)
     * @param  \Amica\Sdk\Model\BeneServizio|null $bene_servizio Istanza dell&#39;elemento da salvare. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioPut'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function beneServizioPutAsyncWithHttpInfo($company_id, $id, $validation_override = null, $idempotency_key = null, $bene_servizio = null, string $contentType = self::contentTypes['beneServizioPut'][0])
    {
        $returnType = '\Amica\Sdk\Model\BeneServizio';
        $request = $this->beneServizioPutRequest($company_id, $id, $validation_override, $idempotency_key, $bene_servizio, $contentType);

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
     * Create request for operation 'beneServizioPut'
     *
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID univoco dell&#39;elemento da aggiornare. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  string|null $idempotency_key Chiave di idempotenza (opzionale). Inviando la stessa chiave su un retry si riottiene la risposta della prima richiesta senza rieseguire l&#39;operazione: rende sicuri i retry automatici. Usa un valore univoco per operazione logica (es. un UUID). La risposta replicata riporta l&#39;header &#x60;Idempotent-Replayed: true&#x60;. (optional)
     * @param  \Amica\Sdk\Model\BeneServizio|null $bene_servizio Istanza dell&#39;elemento da salvare. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['beneServizioPut'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function beneServizioPutRequest($company_id, $id, $validation_override = null, $idempotency_key = null, $bene_servizio = null, string $contentType = self::contentTypes['beneServizioPut'][0])
    {

        // verify the required parameter 'company_id' is set
        if ($company_id === null || (is_array($company_id) && count($company_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $company_id when calling beneServizioPut'
            );
        }

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $id when calling beneServizioPut'
            );
        }





        $resourcePath = '/api/v3/{companyId}/BeneServizio/{id}';
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
        if (isset($bene_servizio)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($bene_servizio));
            } else {
                $httpBody = $bene_servizio;
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
