<?php
namespace Apigee\Util;

use Apigee\Exceptions\SamlResponseException;
use Guzzle\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Guzzle\Log\PsrLogAdapter;
use Guzzle\Plugin\Log\LogPlugin;
use Guzzle\Http\Client as GuzzleClient;
use Guzzle\Http\Exception\RequestException;
use Guzzle\Http\Exception\BadResponseException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * A class that represents infomration about an Edge organization.
 */
class OrgConfig
{
    /**
     * A possible format of logs written by Guzzle's LogPlugin.
     * @see \Guzzle\Log\MessageFormatter
     */
    const LOG_SUBSCRIBER_FORMAT = <<<EOF
{method} {resource}
>>>>>>>>
{request}
<<<<<<<<
{response}
--------
{curl_stderr}
--------
Connection time: {connect_time}
Total transaction time: {total_time}
EOF;

    /**
     * The organization name.
     * @var string
     */
    public $orgName;

    /**
     * The endpoint URL of the organization.
     * This is the URL to which you make API calls.
     * @var string
     */
    public $endpoint;

    /**
     * A logger that implements the \Psr\Log\LoggerInterface interface.
     * See the {@link Apigee\Drupal\WatchdogLogger} class for an example that
     * implements the Psr\Log\LoggerInterface.
     * @var \Psr\Log\LoggerInterface|null
     */
    public $logger;

    /**
     * The email address of the authenticated user in the organization.
     * @var null|string
     */
    public $user_mail;

    /**
     * @var array
     * Array of objects that may subscribe to receive events that various
     * objects may fire.
     * Elements are implementors of
     * Symfony\Component\EventDispatcher\EventSubscriberInterface
     */
    public $subscribers;

    /**
     * @var array
     * Array of HTTP options. The only options currently supported are
     * 'follow_location', 'connection_timeout' and 'timeout'.
     */
    public $http_options;

    /**
     * @var array
     * Array of CURL options.
     */
    public $curl_options;

    /**
     * @var array
     * Array of callables to be called after each REST transaction. Each
     * callback should accept a single array parameter.
     */
    public $debug_callbacks;

    /**
     * @var string|null
     * User-Agent information to be added to the standard Guzzle UA header.
     */
    public $user_agent;

    /**
     * @var string
     * Optionally holds content to be sent in the Referer HTTP header.
     */
    public $referer;

    /**
     * @var bool
     */
    public $redirect_disable = false;

    /**
     * @var string
     */
    public $accessToken = '';

    /**
     * @var CredentialStorageInterface
     */
    private $credentialStorage;

    /**
     * Create an instance of OrgConfig.
     *
     * <p>The $options argument is an array containing the fields 'logger',
     * 'user_email', 'subscribers', 'debug_callbacks', and 'http_options'.</p>
     *
     * <p>For example:</p>
     * <pre>
     *   $logger = new Apigee\Drupal\WatchdogLogger();
     *   $logger::setLogThreshold($log_threshold);
     *   $user_mail = "me@myCo.com";
     *
     *   $options = array(
     *     'logger' => $logger,
     *     'user_mail' => $user_mail,
     *     'subscribers' => array(),
     *     'http_options' => array(
     *       'connection_timeout' => 10,
     *       'timeout' => 50,
     *     ),
     *     'saml' => array(
     *        'endpoint' => 'https://login.apigee.com/oauth/token',
     *        'key' => 'abc',
     *        'secret' => '123',
     *     ),
     *   );
     * </pre>
     *
     * @param string $org_name
     * @param string $endpoint
     * @param string $user
     * @param string $pass
     * @param array $options
     */
    public function __construct($org_name, $endpoint, $user, $pass, $options = array())
    {
        $this->orgName = $org_name;
        $this->endpoint = $endpoint;

        if (array_key_exists('logger', $options) && $options['logger'] instanceof LoggerInterface) {
            $this->logger = $options['logger'];
        } else {
            $this->logger = new NullLogger();
        }

        $this->curl_options = (array_key_exists('curl_options', $options) ? $options['curl_options'] : array());


        if (isset($options['credential_storage']) && $options['credential_storage'] instanceof CredentialStorageInterface) {
            $this->credentialStorage = $options['credential_storage'];
        } else {
            $this->credentialStorage = new FilesystemCredentialStorage();
        }

        $use_saml = array_key_exists('saml', $options) && is_array($options['saml']);
        $saml_info = $use_saml ? $options['saml'] : array();
        $saml_error = null;
        if ($use_saml) {
            try {
                $this->accessToken = $this->getAccessTokenWithPasswordGrant($user, $pass, $saml_info);
            } catch (SamlResponseException $saml_error) {
                // We know we are broken. $saml_error will be thrown at the end of the constructor.
                $use_saml = false;
            }
        }

        $request_options = (array_key_exists('http_options', $options) ? $options['http_options'] : array());

        // Work around old bug in client implementations, wherein a key of
        // "connection_timeout" was passed instead of "connect_timeout".
        if (array_key_exists('connection_timeout', $request_options)) {
            $request_options['connect_timeout'] = $request_options['connection_timeout'];
            unset($request_options['connection_timeout']);
        }
        if (!array_key_exists('connect_timeout', $request_options)) {
            $request_options['connect_timeout'] = 10;
        }

        if (!array_key_exists('timeout', $request_options)) {
            $request_options['timeout'] = 10;
        }

        if (isset($request_options['follow_location'])) {
            $this->redirect_disable = !$request_options['follow_location'];
            unset($request_options['follow_location']);
        }

        if ($use_saml) {
            $request_options['headers']['Authorization'] = 'Bearer ' . $this->accessToken;
        } else {
            $auth = (array_key_exists('auth', $options) ? $options['auth'] : 'basic');
            if ($auth != 'basic' && $auth != 'digest') {
                $auth = 'basic';
            }
            $request_options['auth'] = array($user, $pass, $auth);
        }

        if (array_key_exists('referer', $options)) {
            $request_options['headers']['Referer'] = $options['referer'];
        }

        $proxy = null;
        if (!$proxy = getenv('HTTPS_PROXY')) {
            $proxy = getenv('HTTP_PROXY');
        }
        if (!empty($proxy)) {
            $request_options['proxy'] = $proxy;
        }

        $subscribers = array();
        if (array_key_exists('subscribers', $options) && is_array($options['subscribers'])) {
            foreach ($options['subscribers'] as $subscriber) {
                if ($subscriber instanceof LoggerInterface) {
                    if (array_key_exists('log_subscriber_format', $options)) {
                        $log_subscriber_format = $options['log_subscriber_format'];
                    } else {
                        $log_subscriber_format = self::LOG_SUBSCRIBER_FORMAT;
                    }
                    $subscribers[] = new LogPlugin(new PsrLogAdapter($subscriber), $log_subscriber_format);
                } elseif ($subscriber instanceof EventSubscriberInterface) {
                    $subscribers[] = $subscriber;
                }
            }
        }
        $this->http_options = $request_options;
        $this->user_mail = array_key_exists('user_mail', $options) ? $options['user_mail'] : null;
        $this->subscribers = $subscribers;
        $this->debug_callbacks = array_key_exists('debug_callbacks', $options) ? $options['debug_callbacks'] : array();
        $this->user_agent = array_key_exists('user_agent', $options) ? $options['user_agent'] : null;
        if ($saml_error) {
            $saml_error->orgConfig = $this;
            throw $saml_error;
        }
    }

    /**
     * Fetches a bearer token for use with subsequent requests.
     *
     * @param string $user
     * @param string $pass
     * @param array $saml_info
     *
     * @return string
     *
     * @throws SamlResponseException
     */
    protected function getAccessTokenWithPasswordGrant($user, $pass, array $saml_info)
    {
        $identifier = md5(serialize(func_get_args()));
        $token_details = $this->credentialStorage->read($identifier);

        if ($token_details !== false) {
            $contents = json_decode($token_details, TRUE);
            if (is_array($contents) && array_key_exists('token', $contents) && array_key_exists('expiry', $contents)) {
                // If token is not expired, return it.
                // Require fetching a new token 10 seconds before old one expires,
                // to avoid unexpected failures mid-transaction.
                if (time() < $contents['expiry'] - 10) {
                    return $contents['token'];
                }
            }
        }
        $this->logger->info('Bearer token cache miss; attempting a re-fetch.');
        $rq_opts = $this->http_options;
        // Reset headers to a reasonable subset.
        $headers = array(
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authentication' => 'Basic ' . base64_encode($saml_info['key'] . ":" . $saml_info['secret']),
        );
        if (is_array($rq_opts['headers']) && array_key_exists('Referer', $rq_opts['headers'])) {
            $headers['Referer'] = $rq_opts['headers']['Referer'];
        }
        unset($rq_opts['headers']);
        $rq_opts['auth'] = array($saml_info['key'], $saml_info['secret']);

        $opts = array();
        if (is_array($this->curl_options) && !empty($this->curl_options)) {
            foreach ($this->curl_options as $key => $value) {
                $opts[GuzzleClient::CURL_OPTIONS][$key] = $value;
            }
        }

        $client = new GuzzleClient(null, $opts);
        if (isset($this->user_agent)) {
            $client->setUserAgent($this->user_agent);
        }
        $parameters = array(
            'grant_type' => 'password',
            'username' => $user,
            'password' => $pass,
            'client_id' => $saml_info['key'],
            'client_secret' => $saml_info['secret'],
        );
        $payload = http_build_query($parameters);

        // Default value if auth server does not return a response.
        $response_body = null;
        $request = null;

        // Now make the HTTP request.
        try {
            $request = $client->post($saml_info['endpoint'], $headers, $payload);
            $response = $request->send();
        } catch (RequestException $e) {
            $error_code = $e->getCode();
            if ($e instanceof BadResponseException) {
                // Server responded. Grab the response body, if any.
                $response_body = $e->getResponse()->__toString();
                $error_code = $e->getResponse()->getStatusCode();
            }
            $ex = new SamlResponseException(
                'Cannot fetch bearer token',
                $error_code,
                $saml_info['endpoint'],
                $payload,
                $response_body
            );
            if ($e instanceof BadResponseException) {
                $ex->responseObj = $e->getResponse();
            }
            if ($request instanceof RequestInterface) {
                // Mask all credentials in the logs.
                $parameters2 = $parameters;
                $keys2mask = array('password', 'client_secret');
                foreach ($keys2mask as $key) {
                    $parameters2[$key] = 'XXXXXXX';
                }
                $request->setBody(http_build_query($parameters2));
                $request->setHeader('Authentication', '**masked**');
                $ex->requestObj = $request;
            }
            throw $ex;
        }

        $token_details = json_decode($response->getBody(true), true);
        if (!is_array($token_details)
            || !array_key_exists('access_token', $token_details)
            || !array_key_exists('expires_in', $token_details)
        ) {
            $ex = new SamlResponseException(
                'Invalid JSON returned from bearer token fetch',
                0,
                $saml_info['endpoint'],
                $payload,
                $response_body
            );
            $ex->requestObj = $request;
            $ex->responseObj = $response;
            throw $ex;
        }
        $token_cache_info = array(
            'token' => $token_details['access_token'],
            'expiry' => time() + $token_details['expires_in'],
        );
        // 128 = JSON_PRETTY_PRINT. If PHP is not new enough to recognize this value,
        // it will just be ignored. This is purely optional but it might help in
        // debugging.
        $this->credentialStorage->write($identifier, json_encode($token_cache_info, 128));
        return $token_details['access_token'];
    }
}
