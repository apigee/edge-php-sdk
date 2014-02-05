<?php
namespace Apigee\Util;

/**
 * A class that represents infomration about an Edge organization. 
 */
class OrgConfig {

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
   * @var array Elements are implementors of 
   * Symfony\Component\EventDispatcher\EventSubscriberInterface
   */
  public $subscribers;

  /**
   * @var array
   */
  public $http_options;

  /**
   * Create an instance of OrgConfig.
   *
   * @param string $org_name
   * @param string $endpoint
   * @param string $user
   * @param string $pass
   * @param array $options
   */
  public function __construct($org_name, $endpoint, $user, $pass, $options = array()) {
    $this->orgName = $org_name;
    $this->endpoint = $endpoint;
    $this->user = $user;
    $this->pass = $pass;

    $options += array(
      'logger' => new \Psr\Log\NullLogger(),
      'user_mail' => NULL,
      'subscribers' => array(),
      'http_options' => array(
        'connection_timeout' => 10,
        'timeout' => 10
      )
    );

    $this->logger = $options['logger'];
    $this->user_mail = $options['user_mail'];
    $this->subscribers = $options['subscribers'];
    $this->http_options = $options['http_options'];
  }
}