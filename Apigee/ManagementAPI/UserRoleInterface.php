<?php
namespace Apigee\ManagementAPI;

/**
 * The interface that an UserRole object must implement.
 *
 * @author djohnson
 */
interface UserRoleInterface
{
    /**
     * Returns a list of all system users associated with a role.
     * @param string
     * @return array An array of email addresses of the users.
     */
    public function getUsersByRole($role);

    /**
     * Adds users to a role.
     * @param array An array of email addresses of the users.
     * @param string
     * @return bool TRUE if the users are added, and FALSE if not.
     */
    public function addUsersToRole(array $users, $role);

    /**
     * Removes users from a role.
     * @param array An array of email addresses of the users.
     * @param string
     * @return bool TRUE if the users are removed, and FALSE if not.
     */
    public function removeUsersFromRole(array $users, $role);

    /**
     * Replaces the users in a role with $users.
     * @param array An array of email addresses of the users.
     * @param string
     * @return bool TRUE if the set was successful, and FALSE if not.
     */
    public function setRoleUsers(array $users, $role);

    /**
     * Returns a list of all system roles.
     * @param bool Internal use only.
     * @return array
     */
    public function listRoles($reset = FALSE);

    /**
     * Adds a role.
     * @param string
     */
    public function addRole($role_name);

    /**
     * Deletes a role.
     * @param string
     */
    public function deleteRole($role_name);
}