<?php
namespace Apigee\ManagementAPI;

class UserRole extends Base implements UserRoleInterface
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function addUsersToRole(array $users, $role)
    {
        if (!in_array($role, $this->listRoles())) {
            return FALSE;
        }
        $existing_users = $this->getUsersByRole($role);
        foreach ($users as $user) {
            if (!in_array($user, $existing_users)) {
                $payload = 'id=' . rawurlencode($user);
                // TODO: catch exceptions
                $this->post(rawurlencode($role) . '/users', $payload, 'application/x-www-form-urlencoded');
            }
        }
        return TRUE;
    }

    /**
     * {@inheritDoc}
     */
    public function removeUsersFromRole(array $users, $role)
    {
        if (!in_array($role, $this->listRoles())) {
            return FALSE;
        }
        $existing_users = $this->getUsersByRole($role);
        foreach ($users as $user) {
            if (in_array($user, $existing_users)) {
                // TODO: catch exceptions
                $this->http_delete(rawurlencode($role) . '/users/' . rawurlencode($user));
            }
        }
        return TRUE;
    }

    /**
     * {@inheritDoc}
     */
    public function setRoleUsers(array $users, $role)
    {
        if (!in_array($role, $this->listRoles())) {
            return FALSE;
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
        return TRUE;
    }

    /**
     * {@inheritDoc}
     */
    public function listRoles($reset = FALSE)
    {
        static $roles;

        if ($reset) {
            if (is_array($reset)) {
                $roles = $reset;
            } else {
                $roles = NULL;
            }
        }
        if (empty($roles)) {
            $this->get();
            $roles = $this->responseObj;
        }
        return $roles;
    }

    /**
     * {@inheritDoc}
     */
    public function addRole($role_name)
    {
        $roles = $this->listRoles();
        if (!in_array($role_name, $roles)) {
            $roles[] = $role_name;
            $payload = array('role' => array(array('name' => $role_name)));
            $this->post(NULL, $payload);
            // Reset the cache
            $this->listRoles($roles);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function deleteRole($role_name)
    {
        $roles = $this->listRoles();
        if (($i = array_search($role_name, $roles)) !== FALSE) {
            unset($roles[$i]);
            $this->http_delete(rawurlencode($role_name));
            // Reset the cache
            $this->listRoles($roles);
        }
    }

    /**
     * Returns TRUE if the role exists.
     * @param string
     * @return bool
     */
    public function roleExists($role_name)
    {
        return in_array($role_name, $this->listRoles());
    }
}
