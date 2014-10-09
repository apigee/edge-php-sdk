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
     * The username of an authenticated user in the organization
     * when making API calls to the endpoint URL.
     * @var string
     */
    public $user;

    /**
     * The password of the authenticated user in the organization
     * when making API calls to the endpoint URL.
     * @var string
     */
    public $pass;

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
     * @var array
     * These may be used for storing arbitrary metadata
     */
    public $tags;

    /**
     * @var string
     * Optionally holds content to be sent in the Referer HTTP header.
     */
    public $referer;

    /**
     * @var string
     * Describes the authorization type. Defaults to 'basic' but can also be 'digest' or 'ntlm'.
     */
    public $auth;
    /**
     * Create an instance of OrgConfig.
     *
     * <p>The $options argument is an array containing the fields 'logger', 'user_email',
     * 'subscribers', 'debug_callbacks', and 'http_options'. </p>
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
     *     'subscribers' => null,
     *     'http_options' => array(
     *       'connection_timeout' => 10,
     *       'timeout' => 50
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
        $this->user = $user;
        $this->pass = $pass;
        $this->tags = array();

        $options += array(
            'logger' => new \Psr\Log\NullLogger(),
            'user_mail' => null,
            'subscribers' => array(),
            'http_options' => array(
                'follow_location' => true,
                'connection_timeout' => 10,
                'timeout' => 10,
            ),
            'debug_callbacks' => array(),
            'user_agent' => null,
            'variable_store' => null,
            'referer' => null,
            'auth' => 'basic'
        );
        if (!in_array($options['auth'], array('basic', 'digest', 'ntlm'))) {
            $options['auth'] = 'basic';
        }

        $this->logger = $options['logger'];
        $this->user_mail = $options['user_mail'];
        $this->subscribers = $options['subscribers'];
        $this->http_options = $options['http_options'];
        $this->debug_callbacks = $options['debug_callbacks'];
        $this->user_agent = $options['user_agent'];
        $this->variable_store = $options['variable_store'];
        $this->referer = $options['referer'];
        $this->auth = $options['auth'];
    }
}
