<?php
namespace Apigee\ManagementAPI;

/**
 * The interface that an Organization object must implement.
 *
 * @author djohnson
 */
interface OrganizationInterface
{
    public function load($org = NULL);

    /**
     * Returns the internal name of the organization.
     * @return string
     */
    public function getName();

    /**
     * Returns the display name of the organization.
     * @return string
     */
    public function getDisplayName();

    /**
     * Returns the environments available in the organization. 
     * By default 'test' and 'prod' environments are available.
     * @return array
     */
    public function getEnvironments();

    /**
     * Returns a list of descriptors used internally by Apigee.
     * @return array
     */
    public function getProperties();

    /**
     * Returns a descriptor used internally by Apigee.
     * @param string $name
     * @return string|null
     */
    public function getProperty($name);

    /**
     * Returns the organization type. 
     * Currently 'trial' and 'paid' are valid.
     * @return string
     */
    public function getType();

    /**
     * Returns the Unix time when the organization was created.
     * @return int
     */
    public function getCreatedAt();

    /**
     * Returns the username of the Apigee user who created the organization.
     * @return string
     */
    public function getCreatedBy();

    /**
     * Returns the Unix time when the organization was last modified.
     * @return int
     */
    public function getLastModifiedAt();

    /**
     * Returns the username of the Apigee user who last modified 
     * the organization.
     * @return string
     */
    public function getLastModifiedBy();
}