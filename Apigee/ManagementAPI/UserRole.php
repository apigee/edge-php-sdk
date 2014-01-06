<?php
namespace Apigee\ManagementAPI;

class UserRole extends Base implements UserRoleInterface {

  public function __construct(\Apigee\Util\OrgConfig $config) {
    $this->init($config, '/o/' . $this->urlEncode($config->orgName) . '/userroles');
  }

  public function getUsersByRole($role) {
    if (!in_array($role, $this->listRoles())) {
      return array();
    }
    $this->get($this->urlEncode($role) . '/users');
    return $this->responseObj;
  }

  public function listRoles() {
    static $roles;
    if (empty($roles)) {
      $this->get();
      $roles = $this->responseObj;
    }
    return $roles;
  }
}
