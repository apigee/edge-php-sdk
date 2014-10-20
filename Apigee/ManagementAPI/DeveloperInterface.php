<?php

namespace Apigee\ManagementAPI;

/**
 * The interface that a Developer object in the Management API must implement.
 *
 * @author djohnson
 */
interface DeveloperInterface
{
    /**
     * Loads a developer from the Management API using $email as the unique key.
     *
     * @param string $email
     *    This can be either the developer's email address or the unique
     *    developerId.
     */
    public function load($email);

    /**
     * Attempts to load developer from Management API. Returns true if load was
     * successful.
     *
     * If $email is not supplied, the result will always be false.
     *
     * The $email parameter may either be the actual
     * developer email, or it can be a developer_id.
     *
     * @param null|string $email
     * @return bool
     */
    public function validate($email = null);

    /**
     * Saves user data to the Management API. This operates as both insert and
     * update.
     *
     * If user's email doesn't look valid (must contain @), a
     * ParameterException is thrown.
     *
     * @param bool|null $force_update
     *   If false, assume that this is a new instance.
     *   If true, assume that this is an update to an existing instance.
     *   If null, try an update, and if that fails, try an insert.
     * @param string|null $old_email
     *   If the developer's email has changed, this field must contain the
     *   previous email value.
     *
     * @throws \Apigee\Exceptions\ParameterException
     */
    public function save($force_update = false, $old_email = null);

    /**
     * Deletes a developer.
     *
     * If $email is not supplied, $this->email is used.
     *
     * @param null|string $email
     */
    public function delete($email = null);

    /**
     * Returns an array of all developer emails for this org.
     *
     * @return array
     */
    public function listDevelopers();

    /**
     * Ensures that current developer's email is valid.
     *
     * If first name and/or last name are not supplied, they are auto-
     * populated based on email.
     *
     * @return bool
     */
    public function validateUser();

    /**
     * Resets this object's properties to null or to empty arrays based on type.
     */
    public function blankValues();

    /**
     * Returns the apps associated with the developer.
     * @return array
     */
    public function getApps();

    /**
     * Returns the email address associated with the developer.
     * @return string
     */
    public function getEmail();

    /**
     * Sets the email address associated with the developer.
     * @param string $email
     */
    public function setEmail($email);

    /**
     * Returns the ID associated with the developer.
     * @return string
     */
    public function getDeveloperId();

    /**
     * Returns the first name associated with the developer.
     * @return string
     */
    public function getFirstName();

    /**
     * Sets the first name associated with the developer.
     * @param string $fname
     */
    public function setFirstName($fname);

    /**
     * Returns the last name associated with the developer.
     * @return string
     */
    public function getLastName();

    /**
     * Sets the last name associated with the developer.
     * @param string $lname
     */
    public function setLastName($lname);

    /**
     * Returns the username associated with the developer.
     * @return string
     */
    public function getUserName();

    /**
     * Sets the username associated with the developer.
     * @param string #uname
     */
    public function setUserName($uname);

    /**
     * Returns the developer status: 'active' or 'inactive'.
     * @return string
     */
    public function getStatus();

    /**
     * Sets the developer status: 'active' or 'inactive'.
     * @param string $status
     */
    public function setStatus($status);

    /**
     * Returns an attribute associated with the developer,
     * or null if the attribute does not exist.
     * @return array
     */
    public function getAttribute($name);

    /**
     * Sets an attribute on the developer.
     * @param $attr
     * @param $value
     */
    public function setAttribute($name, $value);

    /**
     * Returns the attributes associated with the developer.
     * @return array
     */
    public function getAttributes();

    /**
     * Returns the Unix time when the developer was last modified.
     * @return integer
     */
    public function getModifiedAt();

    /**
     * Returns a list of string identifiers for companies of which this
     * developer is a member.
     * @return array
     */
    public function getCompanies();

    /**
     * Converts this object's properties into an array for external use.
     *
     * @return array
     */
    public function toArray($include_debug_data = true);
}
