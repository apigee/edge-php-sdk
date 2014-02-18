<?php

namespace Apigee\ManagementAPI;

use \Apigee\Exceptions\ResponseException;
use \Apigee\Exceptions\ParameterException;

/**
 * Abstracts the Company object in the Management API and allows clients to
 * manipulate it.
 *
 * @author djohnson
 */
class Company extends Base
{
    private $name;
    private $displayName;
    private $status;
    private $attributes;
    private $createdAt;
    private $createdBy;
    private $lastModifiedAt;
    private $lastModifiedBy;
    private $apps;
    private $organization;

    public function getName()
    {
        return $this->name;
    }

    public function getDisplayName()
    {
        return $this->displayName;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getAttribute($name)
    {
        if (!array_key_exists($name, $this->attributes)) {
            return NULL;
        }
        return $this->attributes[$name];
    }
    // TODO: other accessors

    /**
     * Initializes default values of all member variables.
     *
     * @param \Apigee\Util\OrgConfig $config
     */
    public function __construct(\Apigee\Util\OrgConfig $config)
    {
        $this->init($config, '/o/' . rawurlencode($config->orgName) . '/companies');
        $this->blankValues();
    }

    public function blankValues()
    {
        $this->name = '';
        $this->displayName = '';
        $this->status = 'active';
        $this->attributes = array();
        $this->createdAt = 0;
        $this->createdBy = '';
        $this->lastModifiedAt = 0;
        $this->lastModifiedBy = '';
        $this->apps = array();
        $this->organization = '';
    }

    /**
     * {@inheritDoc}
     */
    public function listCompanies()
    {
        $this->get();
        $companies = $this->responseObj;
        return $companies;
    }

    public function load($name)
    {
        $this->get(rawurlencode($name));
        self::loadFromResponse($this, $this->responseObj);
    }

    public function save($force_update = FALSE)
    {
        // See if we need to brute-force this.
        if ($force_update === NULL) {
            try {
                $this->save(TRUE);
            } catch (ResponseException $e) {
                if ($e->getCode() == 404) {
                    // Update failed because company doesn't exist.
                    // Try insert instead.
                    $this->save(FALSE);
                } else {
                    // Some other response error.
                    throw $e;
                }
            }
            return;
        }
        $payload = array(
            'name' => $this->name,
            'displayName' => $this->displayName,
            'status' => $this->status,
            'attributes' => array()
        );
        if (count($this->attributes) > 0) {
            $payload['attributes'] = array();
            foreach ($this->attributes as $name => $value) {
                $payload['attributes'][] = array('name' => $name, 'value' => $value);
            }
        }
        $url = NULL;
        if ($force_update || $this->createdAt) {
            $url = rawurlencode($this->name);
        }
        if ($force_update) {
            $this->put($url, $payload);
        } else {
            $this->post($url, $payload);
        }
        self::loadFromResponse($this, $this->responseObj);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($name = NULL)
    {
        $name = $name ? : $this->name;
        $this->http_delete(rawurlencode($name));
        if ($name == $this->name) {
            $this->blankValues();
        }
    }

    public function listDevelopers($company_name = NULL)
    {
        $company_name = $company_name ? : $this->name;
        if (empty($company_name)) {
            throw new ParameterException('No company name given.');
        }
        $url = rawurlencode($company_name) . '/developers';
        $this->get($url);
        $devs = array();
        if ($this->responseObj && array_key_exists('developer', $this->responseObj)) {
            foreach ($this->responseObj['developer'] as $developer) {
                $role = $developer['role'];
                $devs[$role][] = $developer['email'];
            }
        }
        return $devs;
    }

    public function updateDeveloper($dev_email, $role = 'developer', $company_name = NULL)
    {
        $company_name = $company_name ? : $this->name;
        if (empty($company_name)) {
            throw new ParameterException('No company name given.');
        }
        $payload = array('developer' => array(
            array(
                'email' => $dev_email,
                'role' => $role)
        ));
        $url = rawurlencode($company_name) . '/developers';
        $this->post($url, $payload);
    }

    public function removeDeveloper($dev_email, $company_name = NULL)
    {
        $company_name = $company_name ? : $this->name;
        if (empty($company_name)) {
            throw new ParameterException('No company name given.');
        }
        $url = rawurlencode($company_name) . '/developers/' . rawurlencode($dev_email);
        $this->http_delete($url);
    }

    private static function loadFromResponse(Company &$company, array $response)
    {
        foreach ($response as $key => $value) {
            if (property_exists($company, $key)) {
                $company->$key = $value;
            }
        }
    }

    /**
     * Converts this object's properties into an array for external use.
     *
     * @return array
     */
    public function toArray()
    {
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

    /**
     * Populates this object based on an incoming array generated by the
     * toArray() method above.
     *
     * @param $array
     */
    public function fromArray($array)
    {
        foreach ($array as $key => $value) {
            if (property_exists($this, $key) && $key != 'debugData') {
                $this->{$key} = $value;
            }
        }
    }

}