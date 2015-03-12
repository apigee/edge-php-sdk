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
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $displayName;
    /**
     * @var string
     */
    private $status;
    /**
     * @var array
     */
    private $attributes;
    /**
     * @var int
     */
    private $createdAt;
    /**
     * @var string
     */
    private $createdBy;
    /**
     * @var int
     */
    private $lastModifiedAt;
    /**
     * @var string
     */
    private $lastModifiedBy;
    /**
     * @var array
     */
    private $apps;
    /**
     * @var string
     */
    private $organization;

    /**
     * Gets the company's internal name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the company's internal name.
     *
     * It is advisable (but not required) that the name consist of alphanumeric
     * characters, hyphens and underscores only.
     *
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Gets the human-readable company name.
     *
     * @return mixed
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Sets the human-readable company name. This may consist of any
     * characters.
     *
     * @param $name
     */
    public function setDisplayName($name)
    {
        $this->displayName = $name;
    }

    /**
     * Gets the company status as a string. Valid values are 'active' or
     * 'inactive'. It is possible
     *
     * @return string
     */
    public function getStatus()
    {
        return (string)$this->status;
    }

    /**
     * Sets the company status. Valid values are 'active' or 'inactive', though
     * boolean or 0|1 values are also accepted.
     *
     * @param mixed $status
     */
    public function setStatus($status)
    {
        if ($status === 0 || $status === false) {
            $status = 'inactive';
        } elseif ($status === 1 || $status === true) {
            $status = 'active';
        }
        if ($status == 'active' || $status == 'inactive') {
            $this->status = $status;
        }
    }

    /**
     * Returns all defined attributes as an array of key-value pairs.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Returns a named attribute, or null if it does not exist.
     *
     * @param string $name
     * @return string|null
     */
    public function getAttribute($name)
    {
        if (!array_key_exists($name, $this->attributes)) {
            return null;
        }
        return $this->attributes[$name];
    }

    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
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

    /**
     * Sets all member variables to their pristine state.
     */
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
     * Returns an array of all internal company names defined for this org.
     *
     * @return array
     */
    public function listCompanies()
    {
        $this->get();
        $companies = $this->responseObj;
        return $companies;
    }

    /**
     * Returns an array of Company objects representing all companies defined
     * for this org.
     *
     * @return array
     */
    public function listCompaniesDetail()
    {
        $this->get('?expand=true');
        $list = $this->responseObj;
        $companies = array();
        foreach ($list['company'] as $response) {
            $company = new Company($this->config);
            self::loadFromResponse($company, $response);
            $companies[] = $company;
        }
        return $companies;
    }

    /**
     * Given a valid internal company name, populates this object with
     * its properties as fetched from the Edge server.
     *
     * @param string $name
     */
    public function load($name)
    {
        $this->get(rawurlencode($name));
        self::loadFromResponse($this, $this->responseObj);
    }

    /**
     * Saves this object's properties to the Edge server.
     *
     * If $is_update is set to true, we assume that this is an update call.
     * If it is false, we assume that it is an insert. If null is passed in,
     * we attempt an update, and if it fails we attempt an insert. This is
     * much less efficient, so declaring $is_update as a boolean will yield
     * faster response times.
     *
     * @param bool|null $force_update
     * @throws \Apigee\Exceptions\ResponseException
     * @throws \Exception
     */
    public function save($is_update = false)
    {
        // See if we need to brute-force this.
        if ($is_update === null) {
            try {
                $this->save(true);
            } catch (ResponseException $e) {
                if ($e->getCode() == 404) {
                    // Update failed because company doesn't exist.
                    // Try insert instead.
                    $this->save(false);
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
        $url = null;
        if ($is_update || $this->createdAt) {
            $url = rawurlencode($this->name);
        }
        if ($is_update) {
            $this->put($url, $payload);
        } else {
              $this->post($url, $payload);
        }
        self::loadFromResponse($this, $this->responseObj);
    }

    /**
     * Deletes a company from the org on the Edge server.
     *
     * If no $name value is passed in, the company represented by current
     * object state is assumed.
     *
     * @param string|null $name
     * @throws \Apigee\Exceptions\ParameterException
     */
    public function delete($name = null)
    {
        $name = $name ? : $this->name;
        if (empty($name)) {
            throw new ParameterException('No company name given.');
        }
        $this->http_delete(rawurlencode($name));
        if ($name == $this->name) {
            $this->blankValues();
        }
    }

    /**
     * Fetches a list of developer emails, organized by role.
     *
     * Return value is an associative array whose keys are role names, and
     * whose values are arrays of developer emails.
     *
     * @param null|string $company_name
     * @return array
     * @throws \Apigee\Exceptions\ParameterException
     */
    public function listDevelopers($company_name = null)
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

    /**
     * Adds or updates a developer (and the dev's role) on the Edge server.
     *
     * When updating an existing developer, specify both the developer's email
     * and role.
     *
     * @param string $dev_email
     * @param string $role
     * @param null|string $company_name
     * @throws \Apigee\Exceptions\ParameterException
     */
    public function updateDeveloper($dev_email, $role = 'developer', $company_name = null)
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

    /**
     * Removes a developer from a company.
     *
     * @param string $dev_email
     * @param null|string $company_name
     * @throws \Apigee\Exceptions\ParameterException
     */
    public function removeDeveloper($dev_email, $company_name = null)
    {
        $company_name = $company_name ? : $this->name;
        if (empty($company_name)) {
            throw new ParameterException('No company name given.');
        }
        $url = rawurlencode($company_name) . '/developers/' . rawurlencode($dev_email);
        $this->http_delete($url);
    }

  // Get All Companies which developer is part of
  public function getDeveloperCompanies($developer_id) {
    $url = '/organizations/' . rawurlencode($this->config->orgName) . '/developers/' . rawurlencode($developer_id) . '/companies';
//    $content_type = 'application/json; charset=utf-8';
//    $accept_type = 'application/json; charset=utf-8';

    $this->setBaseUrl($url);
    $this->get();
    $this->restoreBaseUrl();
    $response = $this->responseText;
    return $response;

  }


  /**
     * Parses an Edge response array and populates a given Company object
     * accordingly.
     *
     * @param Company $company
     * @param array $response
     */
    private static function loadFromResponse(Company &$company, array $response)
    {
        foreach ($response as $key => $value) {
            if (property_exists($company, $key)) {
                if ($key == 'attributes') {
                   foreach ($value as $name_value_pair) {
                     if (isset($name_value_pair['value'])) {
                       $company->attributes[$name_value_pair['name']] = $name_value_pair['value'];
                     }
                   }
                } else {
                    $company->$key = $value;
                }
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
                $this->$key = $value;
            }
        }
    }

    /**
     * Return an array of roles for a developer in a company.
     *
     * @param string $developer_email The email of the developer.
     * @param string $company_name The name of the company the developer belongs to.
     * @return array An array of role names associated with the developer.
     */
    public function getDeveloperRoles($developer_email, $company_name = NUll) {
      $company_name = $company_name ? : $this->name;
      if (empty($company_name)) {
        throw new ParameterException('No Company name given.');
      }
      $url = rawurlencode($company_name) . '/developers/' . $developer_email;
      $this->get($url);

      $developer_companies = $this->responseObj['company'];
      $roles = explode(',', $developer_companies[0]['role']);

      return $roles;
    }

}
