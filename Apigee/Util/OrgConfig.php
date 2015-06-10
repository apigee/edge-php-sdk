<?php
namespace Apigee\Util;

/**
 * A class that represents infomration about an Edge organization.
 */
class OrgConfig
{

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
     * @var KeyValueStoreInterface|null
     */
    public $variable_store;

    /**
     * @var string
     * Optionally holds content to be sent in the Referer HTTP header.
     */
    public $referer;

    /**
     * @var bool
     */
    public $redirect_disable = FALSE;

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
     *     'variable_store' => new Apigee\Drupal\VariableCache()
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

        $request_options = (array_key_exists('http_options', $options) ? $options['http_options'] : array());

        // Work around old bug in client implementations, wherein a key of
        // "connection_timeout" was passed instead of "connect_timeout".
        if (array_key_exists('connection_timeout', $request_options)) {
            $request_options['connect_timeout'] = $request_options['connection_timeout'];
            unset($request_options['connection_timeout']);
        }
        elseif (!array_key_exists('connect_timeout', $options)) {
            $request_options['connect_timeout'] = 10;
        }

        if (!array_key_exists('timeout', $options)) {
            $request_options['timeout'] = 10;
        }

        if (isset($request_options['follow_location'])) {
            $this->redirect_disable = !$request_options['follow_location'];
            unset($request_options['follow_location']);
        }

        $auth = (array_key_exists('auth', $options) ? $options['auth'] : 'basic');
        if ($auth != 'basic' && $auth != 'digest') {
            $auth = 'basic';
        }

        $request_options['auth'] = array($user, $pass, $auth);
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

        $this->logger = array_key_exists('logger', $options) && $options['logger'] instanceof \Psr\Log\LoggerInterface ? $options['logger'] : new \Psr\Log\NullLogger();
        $this->user_mail = array_key_exists('user_mail', $options) ? $options['user_mail'] : null;
        $this->subscribers = array_key_exists('subscribers', $options) ? $options['subscribers'] : array();
        $this->http_options = $request_options;
        $this->debug_callbacks = array_key_exists('debug_callbacks', $options) ? $options['debug_callbacks'] : array();
        $this->user_agent = array_key_exists('user_agent', $options) ? $options['user_agent'] : null;
        $this->variable_store = array_key_exists('variable_store', $options) ? $options['variable_store'] : null;
    }
}
