<?php
namespace Apigee\ManagementAPI;

interface UserRoleInterface {
  public function getUsersByRole($role);
}