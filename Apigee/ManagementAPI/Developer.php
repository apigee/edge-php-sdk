<?php
/**
 * @file
 * Abstracts the Developer object in the Management API and allows clients to
 * manipulate it.
 *
 * @author djohnson
 */

namespace Apigee\ManagementAPI;

use \Apigee\Exceptions\ResponseException;
use \Apigee\Exceptions\ParameterException;

class Developer extends Base implements DeveloperInterface {

  /**
   * @var array
   */
  protected $apps;
  /**
   * @var string
   * This is actually the unique-key (within the org) for the Developer
   */
  protected $email;
  /**
   * @var string
   * Read-only alternate unique ID. Useful when querying developer analytics.
   */
  protected $developerId;
  /**
   * @var string
   */
  protected $firstName;
  /**
   * @var string
   */
  protected $lastName;
  /**
   * @var string
   */
  protected $userName;
  /**
   * @var string
   * Read-only
   */
  protected $organizationName;
  /**
   * @var string
   * Should be either 'active' or 'inactive'.
   */
  protected $status;
  /**
   * @var array
   * This must be protected because Base wants to twiddle with it.
   */
  protected $attributes;
  /**
   * @var int
   * Read-only
   */
  protected $createdAt;
  /**
   * @var string
   * Read-only
   */
  protected $createdBy;
  /**
   * @var int
   * Read-only
   */
  protected $modifiedAt;
  /**
   * @var string
   * Read-only
   */
  protected $modifiedBy;

  protected $baseUrl;

  /* Accessors (getters/setters) */
  public function getApps() {
    return $this->apps;
  }
  public function getEmail() {
    return $this->email;
  }
  public function setEmail($email) {
    $this->email = $email;
  }
  public function getDeveloperId() {
    return $this->developerId;
  }
  public function getFirstName() {
    return $this->firstName;
  }
  public function setFirstName($fname) {
    $this->firstName = $fname;
  }
  public function getLastName() {
    return $this->lastName;
  }
  public function setLastName($lname) {
    $this->lastName = $lname;
  }
  public function getUserName() {
    return $this->userName;
  }
  public function setUserName($uname) {
    $this->userName = $uname;
  }
  public function getStatus() {
    return $this->status;
  }
  public function setStatus($status) {
    if ($status === 0 || $status === FALSE) {
      $status = 'inactive';
    }
    elseif ($status === 1 || $status === TRUE) {
      $status = 'active';
    }
    if ($status != 'active' && $status != 'inactive') {
      throw new ParameterException('Status may be either active or inactive; value "' . $status . '" is invalid.');
    }
    $this->status = $status;
  }
  public function getAttribute($attr) {
    if (array_key_exists($attr, $this->attributes)) {
      return $this->attributes[$attr];
    }
    return NULL;
  }
  public function setAttribute($attr, $value) {
    $this->attributes[$attr] = $value;
  }
  public function getAttributes() {
    return $this->attributes;
  }
  public function getModifiedAt() {
    return $this->modifiedAt;
  }

  /**
   * Initializes default values of all member variables.
   *
   * @param \Apigee\Util\OrgConfig $client
   */
  public function __construct(\Apigee\Util\OrgConfig $config) {
    $this->init($config, '/o/' . $this->urlEncode($config->orgName) . '/developers');
    $this->blankValues();
  }

  /**
   * Loads a developer from the Management API using $email as the unique key.
   *
   * @param string $email
   *    This can be either the developer's email address or the unique
   *    developerId.
   */
  public function load($email) {
    $this->get($this->urlEncode($email));
    $developer = $this->responseObj;
    self::loadFromResponse($this, $developer);
  }

  protected static function loadFromResponse(&$developer, $response) {
    $developer->apps = $response['apps'];
    $developer->email = $response['email'];
    $developer->developerId = $response['developerId'];
    $developer->firstName = $response['firstName'];
    $developer->lastName = $response['lastName'];
    $developer->userName = $response['userName'];
    $developer->organizationName = $response['organizationName'];
    $developer->status = $response['status'];
    $developer->attributes = array();
    if (array_key_exists('attributes', $response) && is_array($response['attributes'])) {
      foreach ($response['attributes'] as $attribute) {
        $developer->attributes[$attribute['name']] = $attribute['value'];
      }
    }
    $developer->createdAt = $response['createdAt'];
    $developer->createdBy = $response['createdBy'];
    $developer->modifiedAt = $response['lastModifiedAt'];
    $developer->modifiedBy = $response['lastModifiedBy'];
  }

  /**
   * Attempts to load developer from Management API. Returns TRUE if load was
   * successful.
   *
   * If $email is not supplied, the result will always be FALSE.
   *
   * As a bit of trivia, the $email parameter may either be the actual
   * developer email, or it can be a developer_id.
   *
   * @param null|string $email
   * @return bool
   */
  public function validate($email = NULL) {
    if (!empty($email)) {
      try {
        $this->get($this->urlEncode($email));
        return TRUE;
      }
      catch (ResponseException $e) { }
    }
    return FALSE;
  }

  /**
   * Saves user data to the Management API. This operates as both insert and
   * update.
   *
   * If user's email doesn't look valid (must contain @), a
   * ParameterException is thrown.
   *
   * @var bool|null $force_update
   *   If FALSE, assume that this is a new instance.
   *   If TRUE, assume that this is an update to an existing instance.
   *   If NULL, try an update, and if that fails, try an insert.
   *
   * @throws \Apigee\Exceptions\ParameterException
   */
  public function save($force_update = FALSE) {

    // See if we need to brute-force this.
    if ($force_update === NULL) {
      try {
        $this->save(TRUE);
      }
      catch (ResponseException $e) {
        if ($e->getCode() == 404) {
          // Update failed because dev doesn't exist.
          // Try insert instead.
          $this->save(FALSE);
        }
        else {
          // Some other response error.
          throw $e;
        }
      }
      return;
    }

    if (!$this->validateUser()) {
      throw new ParameterException('Invalid email address; cannot save user.');
    }

    $payload = array(
      'email' => $this->email,
      'userName' => $this->userName,
      'firstName' => $this->firstName,
      'lastName' => $this->lastName,
      'status' => $this->status,
    );
    if (count($this->attributes) > 0) {
      $payload['attributes'] = array();
      foreach ($this->attributes as $name => $value) {
        $payload['attributes'][] = array('name' => $name, 'value' => $value);
      }
    }
    $url = NULL;
    if ($force_update || $this->createdAt) {
      if ($this->developerId) {
        $payload['developerId'] = $this->developerId;
      }
      $url = $this->urlEncode($this->email);
    }
    if ($force_update) {
      $this->put($url, $payload);
    }
    else {
      $this->post($url, $payload);
    }
  }

  /**
   * Deletes a developer.
   *
   * If $email is not supplied, $this->email is used.
   *
   * @param null|string $email
   */
  public function delete($email = NULL) {
    $email = $email ?: $this->email;
    $this->http_delete($this->urlEncode($email));
    if ($email == $this->email) {
      $this->blankValues();
    }
  }

  /**
   * Returns an array of all developer emails for this org.
   *
   * @return array
   */
  public function listDevelopers() {
    $this->get();
    $developers = $this->responseObj;
    return $developers;
  }

  /**
   * Returns an array of all developers in the org.
   *
   * @return array
   */
  public function loadAllDevelopers() {
    $this->get('?expand=true');
    $developers = $this->responseObj;
    $out = array();
    foreach ($developers['developer'] as $dev) {
      $developer = new Developer($this->config);
      self::loadFromResponse($developer, $dev);
      $out[] = $developer;
    }
    return $out;
  }

  /**
   * Ensures that current developer's email looks at least sort of valid.
   *
   * If first name and/or last name are not supplied, they are auto-
   * populated based on email. This is kind of shoddy but it's the best we can
   * do.
   *
   * @return bool
   */
  public function validateUser() {
    if (!empty($this->email) && (strpos($this->email, '@') > 0)) {
      $name = explode('@', $this->email, 2);
      if (empty($this->firstName)) {
        $this->firstName = $name[0];
      }
      if (empty($this->lastName)) {
        $this->lastName = $name[1];
      }
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Restores this object's properties to their pristine state.
   */
  public function blankValues() {
    $this->apps = array();
    $this->email = NULL;
    $this->developerId = NULL;
    $this->firstName = NULL;
    $this->lastName = NULL;
    $this->userName = NULL;
    $this->organizationName = NULL;
    $this->status = NULL;
    $this->attributes = array();
    $this->createdAt = NULL;
    $this->createdBy = NULL;
    $this->modifiedAt = NULL;
    $this->modifiedBy = NULL;
  }


  /**
   * Turns this object's properties into an array for external use.
   *
   * @return array
   */
  public function toArray() {
    $properties = array_keys(get_object_vars($this));
    $excluded_properties = array_keys(get_class_vars(get_parent_class($this)));
    $output = array();
    foreach ($properties as $property) {
      if (!in_array($property, $excluded_properties)) {
        $output[$property] = $this->$property;
      }
    }
    $output['debugData'] = $this->getDebugData();
    return $output;
  }

  /**
   * Populates this object based on an incoming array generated by the
   * toArray() method above.
   *
   * @param $array
   */
  public function fromArray($array) {
    foreach($array as $key => $value) {
      if (property_exists($this, $key) && $key != 'debugData') {
        $this->{$key} = $value;
      }
    }
    $this->loaded = TRUE;
  }

}