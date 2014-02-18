<?php
/**
 * @file
 * Abstracts the Organization object in the Management API and allows clients to
 * manipulate it.
 *
 * @author djohnson
 */
namespace Apigee\ManagementAPI;

/**
 * Abstracts the Organization object in the Management API and allows clients to
 * manipulate it.
 *
 * @author djohnson
 */
class Organization extends Base implements OrganizationInterface
{

    /**
     * @var string
     * The internal name of the organization.
     */
    protected $name;

    /**
     * @var string
     * The dispaly name of the organization.
     */
    protected $displayName;

    /**
     * @var array
     * Environments available in the organization. By default 'test' and 'prod'
     * environments are available.
     */
    protected $environments;

    /**
     * @var array
     * A list of descriptors used internally by Apigee.
     */
    protected $properties;

    /**
     * @var string
     * Organization type. Currently 'trial' and 'paid' are valid.
     */
    protected $type;

    /**
     * @var int
     * Unix time when the organization was created.
     */
    protected $createdAt;

    /**
     * @var string
     * Username of the Apigee user who created the organization.
     */
    protected $createdBy;

    /**
     * @var int
     * Unix time when the organization was last modified.
     */
    protected $lastModifiedAt;

    /**
     * @var string
     * Username of the Apigee user who last modified the organization.
     */
    protected $lastModifiedBy;

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * {@inheritDoc}
     */
    public function getEnvironments()
    {
        return $this->environments;
    }

    /**
     * {@inheritDoc}
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * {@inheritDoc}
     */
    public function getProperty($name)
    {
        return isset($this->properties[$name]) ? $this->properties[$name] : NULL;
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritDoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritDoc}
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * {@inheritDoc}
     */
    public function getLastModifiedAt()
    {
        return $this->lastModifiedAt;
    }

    /**
     * {@inheritDoc}
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }

    /**
     * Initializes default values of all member variables.
     *
     * @param \Apigee\Util\OrgConfig $config
     */
    public function __construct(\Apigee\Util\OrgConfig $config)
    {
        $this->init($config, '/organizations');
        $this->name = $config->orgName;
    }

    /**
     * Loads the organization specified by $org.
     * @param string|null $org
     */
    public function load($org = NULL)
    {
        $org = $org ? : $this->name;
        $this->get(rawurlencode($org));
        $organization = $this->responseObj;

        $this->name = $organization['name'];
        $this->displayName = $organization['displayName'];
        $this->environments = $organization['environments'];
        $this->type = $organization['type'];
        $this->createAt = $organization['createdAt'];
        $this->createdBy = $organization['createdBy'];
        $this->lastModifiedAt = $organization['lastModifiedAt'];
        $this->lastModifiedBy = $organization['lastModifiedBy'];
        $this->properties = array();

        if (array_key_exists('properties', $organization) && array_key_exists('property', $organization['properties'])) {
            foreach ($organization['properties']['property'] as $prop) {
                if (array_key_exists('name', $prop) && array_key_exists('value', $prop)) {
                    $this->properties[$prop['name']] = $prop['value'];
                }
            }
        }
    }
}