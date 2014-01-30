<?php
namespace Apigee\ManagementAPI;

interface UserRoleInterface
{
    public function getUsersByRole($role);

    public function addUsersToRole(array $users, $role);

    public function removeUsersFromRole(array $users, $role);

    public function setRoleUsers(array $users, $role);

    public function listRoles($reset = FALSE);

    public function addRole($role_name);

    public function deleteRole($role_name);
}