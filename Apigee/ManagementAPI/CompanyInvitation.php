<?php

namespace Apigee\ManagementAPI;

use \Apigee\Exceptions\ResponseException;
use \Apigee\Exceptions\ParameterException;

/**
 * Abstracts the CompanyInvitation object from the Management API and allows clients to manipulate it.
 *
 * @author Sudheesh
 */
class CompanyInvitation extends Base {

  /**
   * The Company Id of the company associated with the Invitation.
   * @var string
   */
  private $companyId;

  /**
   * The Invitation created date.
   * @var string
   */
  private $created_at;

  /**
   * The Developer Id of the developer associated with the Invitation.
   * @var string
   */
  private $developerId;

  /**
   * The unique Id of the Invitation.
   * @var string
   */
  private $id;

  /**
   * The last modfied time.
   * @var int
   */
  private $lastmodified_at;

  /**
   * The org id
   * @var string
   */
  private $orgId;

  /**
   * The entity that created the Invitation.
   * @var string
   */
  private $requestor;

  /**
   * The current state of the Invitation.
   * @var string
   */
  private $state;

  /**
   * The type of the Invitation.
   * @var string
   */
  private $type;

  /**
   * The email id of the source developer.
   * @var string
   */
  private $sourceDeveloperEmail;

  /**
   * The url for the developer to accept invitation.
   * @var string.
   */
  private $responseUrl;

  /**
   * Gets the id of the Invitation object.
   * @return string
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Sets the id of the Invitation object.
   *
   * @param string $id
   */
  public function setId($id) {
    $this->id = $id;
  }

  /**
   * Gets the Company Id.
   *
   * @return string
   */
  public function getCompanyId() {
    return $this->companyId;
  }

  /**
   * Sets the Company Id.
   *
   * @param string $companyId
   */
  public function setCompanyId($companyId) {
    $this->companyId = $companyId;
  }

  /**
   * Gets the Developer Id.
   *
   * @return string
   */
  public function getDeveloperId() {
    return $this->developerId;
  }

  /**
   * Sets the Developer Id.
   *
   * @param string $developerId
   */
  public function setDeveloperId($developerId) {
    $this->developerId = $developerId;
  }

  /**
   * Gets the created time.
   *
   * @return int
   */
  public function getCreatedAt() {
    return $this->created_at;
  }

  /**
   * Gets the modified time.
   *
   * @return int
   */
  public function getLastModifedAt() {
    return $this->lastmodified_at;
  }

  /**
   * Gets the orgId.
   *
   * @return string
   */
  public function getOrgId() {
    return $this->orgId;
  }

  /**
   * Gets the requestor.
   *
   * @return string
   */
  public function getRequestor() {
    return $this->requestor;
  }

  /**
   * Sets the requestor.
   *
   * @param string $requestor
   */
  public function setRequestor($requestor) {
    $this->requestor = $requestor;
  }

  /**
   * Gets the state of the Invitation.
   *
   * @return string
   */
  public function getState() {
    return $this->state;
  }

  /**
   * Sets the state of the Invitation.
   *
   * @param string $state
   */
  public function setSate($state) {
    $this->state = $state;
  }

  /**
   * Gets the type of the Invitation.
   *
   * @return string
   */
  public function getType() {
    return $this->type;
  }

  /**
   * Sets  the type of the Invitation.
   *
   * @param string $type
   */
  public function setType($type) {
    $this->type = $type;
  }

  /**
   * Gets the source developer email.
   *
   * @return string
   */
  public function getSourceDeveloperEmail() {
    return $this->sourceDeveloperEmail;
  }

  /**
   * Sets the source developer email.
   *
   * @param type $sourceDeveloperEmail
   */
  public function setSourceDeveloperEmail($sourceDeveloperEmail) {
    $this->sourceDeveloperEmail = $sourceDeveloperEmail;
  }

  /**
   * Get the response url of the invitation.
   * @return string
   */
  public function getResponseUrl(){
    return $this->responseUrl;
  }

  /**
   * Sets the reponse url of the invitation.
   * @param string $responseUrl
   */
  public function setResponseUrl($url){
    $this->responseUrl = $url;
  }

  /**
   * Initializes default values of all the member variables
   * @param \Apigee\Util\OrgConfig $config
   */
  public function __construct(\Apigee\Util\OrgConfig $config) {
    $this->init($config, '/o/' . rawurlencode($config->orgName) . '/invitations');
    $this->blankValues();
  }

  /**
   * Set all memeber variables to the default values.
   */
  public function blankValues() {
    $this->companyId = '';
    $this->created_at = '';
    $this->developerId = '';
    $this->id = '';
    $this->lastmodified_at = '';
    $this->orgId = '';
    $this->requestor = '';
    $this->state = '';
    $this->type = '';
    $this->sourceDeveloperEmail = '';
    $this->responseUrl = '';
  }

  /**
   * Get all the Invitation for a company.
   * @param string $companyId
   * @param string $state
   * @return mixed
   */
  public function getAllInvitationsForCompany($companyId, $state = NULL) {
    $query = '?company_id=' . $companyId;
    if (!empty($state)) {
      $query .= '&state=' . $state;
    }
    $this->get($query);
    $config = $this->config;
    return self::loadInvitationArray($this->responseObj, $config);
  }

  /**
   * Get all Invitations for a developer.
   * @param string $developerId
   * @param type $state
   * @return type
   */
  public function getAllInvitationsForDeveloper($developerId, $state = NULL) {
    $query = '?dev_id=' . urlencode($developerId);
    if (!empty($state)) {
      $query .= '&state=' . $state;
    }

    $this->get($query);
    $config = $this->config;
    return self::loadInvitationArray($this->responseObj, $config);
  }

  public function getAllInvitationsForOrg() {
    $this->get();
    $config = $this->config;
    return self::loadInvitationArray($this->responseObj, $config);
  }

  /**
   * Parses an Edge reponse and return an array of Invitations.
   * @param array $reponseobj
   * @return array of fully loaded response objects
   */
  private static function loadInvitationArray($reponseobj, \Apigee\Util\OrgConfig $config) {

    $invitations = array();
    foreach ($reponseobj['invitations'] as $response) {
      $invitation = new CompanyInvitation($config);
      self::loadFromResponse($invitation, $response);
      $invitations[] = $invitation;
    }
    return $invitations;
  }

  /**
   * Parses response from Edge and populates a CompanyInvitation object.
   * @param \Apigee\ManagementAPI\CompanyInvitation $company_invitation
   * @param array $reponse
   */
  private static function loadFromResponse(CompanyInvitation &$invitation, array $response) {
    foreach ($response as $key => $value) {
      if (property_exists($invitation, $key)) {
        $invitation->$key = $value;
      }
    }
  }

  /**
   * Load an invitation from Edge.
   * @param type $id
   * @return \Apigee\ManagementAPI\CompanyInvitation
   */
  public function load($id) {
    $this->get($id);
    self::loadFromResponse($this, $this->responseObj);
  }

  /**
   * Saves an invitation object to the Edge server.
   *
   * If $force_update is true then a PUT call is made to update an existing Invitation, otherwise a POST
   * call is made to create a new Invitaion.
   *
   * @param boolean $force_update
   */
  public function save($force_update = FALSE) {
    if ($force_update === NULL) {
      try {
        $this->save(TRUE);
      }
      catch (ResponseException $e) {
        if ($e->getCode() == 404) {
          $this->save(FALSE);
        }
        else {
          throw $e;
        }
      }
      return;
    }

    $payload = array(
      'companyId' => $this->companyId,
      'developerId' => $this->developerId,
    );
    if (!empty($this->requestor)) {
      $payload['requestor'] = $this->requestor;
    }
    if (!empty($this->type)) {
      $payload['type'] = $this->type;
    }
    if (!empty($this->state)) {
      $payload['state'] = $this->state;
    }
    if (!empty($this->id)) {
      $payload['id'] = $this->id;
    }


    $url = NULL;
    if ($force_update || $this->created_at) {
      $url = rawurldecode($this->id);
    }

    $headers = array(
      'source' => $this->sourceDeveloperEmail,
    );
    if ($force_update) {
      $this->put($url, $payload, 'application/json; charset=utf-8', 'application/json; charset=utf-8', $headers);
    }
    else {
      $headers['responseUrl'] = $this->responseUrl;
      $this->post($url, $payload, 'application/json; charset=utf-8', 'application/json; charset=utf-8', $headers);
    }
    self::loadFromResponse($this, $this->responseObj);
  }

  /**
   * Deletes a Invitation object from the Edge.
   * @param string|NULL $id
   * @throws ParameterException
   */
  public function delete($id = NULL) {
    $id = $id? : $this->id;
    if (empty($id)) {
      throw new ParameterException("No Invitation Id given");
    }

    $headers = array('source' => $this->sourceDeveloperEmail);
    $this->http_delete(rawurlencode($id), 'application/json; charset=utf-8', $headers);
    if ($id == $this->id) {
      $this->blankValues();
    }
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

}

?>
