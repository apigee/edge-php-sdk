<?php
namespace Apigee\ManagementAPI;

class UserRole extends Base
{

    /**
     * Initializes default values of all member variables.
     *
     * @param \Apigee\Util\OrgConfig $config
     */
    public function __construct(\Apigee\Util\OrgConfig $config)
    {
        $this->init($config, '/o/' . rawurlencode($config->orgName) . '/userroles');
    }

    /**
     * Returns a list of all system users associated with a role.
     *
     * @param string $role
     * @return array
     *    An array of email addresses of the users.
     */
    public function getUsersByRole($role)
    {
        if (!in_array($role, $this->listRoles())) {
            return array();
        }
        $this->get(rawurlencode($role) . '/users');
        return $this->responseObj;
    }

    /**
     * Adds users to a role.
     *
     * @param array $users
     *    An array of email addresses of the users.
     * @param string $role
     * @return bool
     *    True if the users are added, and false if not.
     */
    public function addUsersToRole(array $users, $role)
    {
        if (!in_array($role, $this->listRoles())) {
            return false;
        }
        $existing_users = $this->getUsersByRole($role);
        foreach ($users as $user) {
            if (!in_array($user, $existing_users)) {
                $payload = 'id=' . rawurlencode($user);
                // TODO: catch exceptions
                $this->post(rawurlencode($role) . '/users', $payload, 'application/x-www-form-urlencoded');
            }
        }
        return true;
    }

    /**
     * Removes users from a role.
     *
     * @param array $users
     *    An array of email addresses of the users.
     * @param string $role
     * @return bool
     *    True if the users are removed, and false if not.
     */
    public function removeUsersFromRole(array $users, $role)
    {
        if (!in_array($role, $this->listRoles())) {
            return false;
        }
        $existing_users = $this->getUsersByRole($role);
        foreach ($users as $user) {
            if (in_array($user, $existing_users)) {
                // TODO: catch exceptions
                $this->http_delete(rawurlencode($role) . '/users/' . rawurlencode($user));
            }
        }
        return true;
    }

    /**
     * Replaces the users in a role with $users.
     *
     * @param array $users
     *    An array of email addresses of the users.
     * @param string $role
     * @return bool
     *    True if the set was successful, and false if not.
     */
    public function setRoleUsers(array $users, $role)
    {
        if (!in_array($role, $this->listRoles())) {
            return false;
        }
        $existing_users = $this->getUsersByRole($role);
        $to_add = array();
        $to_del = array();

        foreach ($users as $user) {
            if (in_array($user, $existing_users)) {
                $to_del[] = $user;
            } else {
                $to_add[] = $user;
            }
        }
        if (!empty($to_add)) {
            $this->addUsersToRole($to_add, $role);
        }
        if (!empty($to_del)) {
            $this->removeUsersFromRole($to_del, $role);
        }
        return true;
    }

    /**
     * Returns a list of all system roles.
     *
     * @param bool|array $reset
     *    Internal use only. Used to reset internal cache.
     * @return array
     */
    public function listRoles($reset = false)
    {
        static $roles;

        if ($reset) {
            if (is_array($reset)) {
                $roles = $reset;
            } else {
                $roles = null;
            }
        }
        if (empty($roles)) {
            $this->get();
            $roles = $this->responseObj;
        }
        return $roles;
    }

    /**
     * Adds a role.
     *
     * @param string $role_name
     */
    public function addRole($role_name)
    {
        $roles = $this->listRoles();
        if (!in_array($role_name, $roles)) {
            $roles[] = $role_name;
            $payload = array('role' => array(array('name' => $role_name)));
            $this->post(null, $payload);
            // Reset the cache
            $this->listRoles($roles);
        }
    }

    /**
     * Deletes a role.
     *
     * @param string $role_name
     */
    public function deleteRole($role_name)
    {
        $roles = $this->listRoles();
        if (($i = array_search($role_name, $roles)) !== false) {
            unset($roles[$i]);
            $this->http_delete(rawurlencode($role_name));
            // Reset the cache
            $this->listRoles($roles);
        }
    }

    /**
     * Returns true if the role exists.
     * @param string
     * @return bool
     */
    public function roleExists($role_name)
    {
        return in_array($role_name, $this->listRoles());
    }
}
