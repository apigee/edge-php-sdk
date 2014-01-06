<?php
namespace Apigee\Util;

class OrgConfig {

  /**
   * @var string
   */
  public $orgName;

  /**
   * @var string
   */
  public $endpoint;

  /**
   * @var string
   */
  public $user;

  /**
   * @var string
   */
  public $pass;

  /**
   * @var \Psr\Log\LoggerInterface|null
   */
  public $logger;

  /**
   * @var null|string
   */
  public $user_mail;

  /**
   * @var array of Symfony\Component\EventDispatcher\EventSubscriberInterface implementors
   */
  public $subscribers;

  /**
   * @var array
   */
  public $http_options;

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