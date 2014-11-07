<?php
/**
 * @file
 * Abstracts the Developer object in the Management API and allows clients to
 * manipulate it.
 *
 * @author djohnson
 */

namespace Apigee\ManagementAPI;

use \Apigee\Exceptions\ResponseException;
use \Apigee\Exceptions\ParameterException;

/**
 * Abstracts the Developer object in the Management API and allows clients to
 * manipulate it.
 *
 * @author djohnson
 */
class Developer extends Base implements DeveloperInterface
{

    /**
     * The apps associated with the developer.
     * @var array
     */
    protected $apps;
    /**
     * @var string
     * The developer's email, used to unique identify the developer in Edge.
     */
    protected $email;
    /**
     * @var string
     * Read-only alternate unique ID. Useful when querying developer analytics.
     */
    protected $developerId;
    /**
     * @var string
     * The first name of the developer.
     */
    protected $firstName;
    /**
     * The last name of the developer.
     * @var string
     */
    protected $lastName;
    /**
     * @var string
     * The developer's username.
     */
    protected $userName;
    /**
     * @var string
     * The Apigee organization where the developer is regsitered.
     * This property is read-only.
     */
    protected $organizationName;
    /**
     * @var string
     * The developer status: 'active' or 'inactive'.
     */
    protected $status;
    /**
     * @var array
     * Name/value pairs used to extend the default profile.
     */
    protected $attributes;
    /**
     * @var int
     * Unix time when the developer was created.
     * This property is read-only.
     */
    protected $createdAt;
    /**
     * @var string
     * Username of the user who created the developer.
     * This property is read-only.
     */
    protected $createdBy;
    /**
     * @var int
     * Unix time when the developer was last modified.
     * This property is read-only.
     */
    protected $modifiedAt;
    /**
     * @var string
     * Username of the user who last modified the developer.
     * This property is read-only.
     */
    protected $modifiedBy;

    /**
     * @var array
     * Read-only list of company identifiers of which this developer is a
     * member.
     */
    protected $companies;

    /**
     * @var string
     * Caches the previous status to see if it has changed
     */
    protected $previousStatus;

    /* Accessors (getters/setters) */
    /**
     * {@inheritDoc}
     */
    public function getApps()
    {
        return $this->apps;
    }

    /**
     * {@inheritDoc}
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * {@inheritDoc}
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * {@inheritDoc}
     */
    public function getDeveloperId()
    {
        return $this->developerId;
    }

    /**
     * {@inheritDoc}
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * {@inheritDoc}
     */
    public function setFirstName($fname)
    {
        $this->firstName = $fname;
    }

    /**
     * {@inheritDoc}
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * {@inheritDoc}
     */
    public function setLastName($lname)
    {
        $this->lastName = $lname;
    }

    /**
     * {@inheritDoc}
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * {@inheritDoc}
     */
    public function setUserName($uname)
    {
        $this->userName = $uname;
    }

    /**
     * {@inheritDoc}
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * {@inheritDoc}
     */
    public function setStatus($status)
    {
        if ($status === 0 || $status === false) {
            $status = 'inactive';
        } elseif ($status === 1 || $status === true) {
            $status = 'active';
        }
        if ($status != 'active' && $status != 'inactive') {
            throw new ParameterException('Status may be either active or inactive; value "' . $status . '" is invalid.');
        }
        $this->previousStatus = $this->status;
        $this->status = $status;
    }

    /**
     * {@inheritDoc}
     */
    public function getAttribute($attr)
    {
        if (array_key_exists($attr, $this->attributes)) {
            return $this->attributes[$attr];
        }
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function setAttribute($attr, $value)
    {
        $this->attributes[$attr] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * {@inheritDoc}
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    public function getCompanies()
    {
        return $this->companies;
    }

    /**
     * Initializes default values of all member variables.
     *
     * @param \Apigee\Util\OrgConfig $config
     */
    public function __construct(\Apigee\Util\OrgConfig $config)
    {
        $this->init($config, '/o/' . rawurlencode($config->orgName) . '/developers');
        $this->blankValues();
    }

    /**
     * {@inheritDoc}
     */
    public function load($email)
    {
        $this->get(rawurlencode($email));
        $developer = $this->responseObj;
        self::loadFromResponse($this, $developer);
        $this->previousStatus = $this->status;
    }

    /**
     * Takes the raw KMS response and populates the member variables of the
     * passed-in Developer object from it.
     *
     * @param \Apigee\ManagementAPI\Developer $developer
     * @param array $response
     */
    protected static function loadFromResponse(Developer &$developer, array $response)
    {
        $developer->apps = $response['apps'];
        $developer->email = $response['email'];
        $developer->developerId = $response['developerId'];
        $developer->firstName = $response['firstName'];
        $developer->lastName = $response['lastName'];
        $developer->userName = $response['userName'];
        $developer->organizationName = $response['organizationName'];
        $developer->status = $response['status'];
        $developer->attributes = array();
        if (array_key_exists('attributes', $response) && is_array($response['attributes'])) {
            foreach ($response['attributes'] as $attribute) {
                $developer->attributes[$attribute['name']] = @$attribute['value'];
            }
        }
        $developer->createdAt = $response['createdAt'];
        $developer->createdBy = $response['createdBy'];
        $developer->modifiedAt = $response['lastModifiedAt'];
        $developer->modifiedBy = $response['lastModifiedBy'];
        if (array_key_exists('companies', $response)) {
            $developer->companies = $response['companies'];
        }
        else {
            $developer->companies = array();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function validate($email = null)
    {
        if (!empty($email)) {
            try {
                $this->get(rawurlencode($email));
                return true;
            } catch (ResponseException $e) {
            }
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function save($force_update = false, $old_email = null)
    {
        // See if we need to brute-force this.
        if ($force_update === null) {
            try {
                $this->save(true, $old_email);
            } catch (ResponseException $e) {
                if ($e->getCode() == 404) {
                    // Update failed because dev doesn't exist.
                    // Try insert instead.
                    $this->save(false, $old_email);
                } else {
                    // Some other response error.
                    throw $e;
                }
            }
            return;
        }

        if (!$this->validateUser()) {
            throw new ParameterException('Developer requires valid-looking email address, firstName, lastName and userName.');
        }

        if (empty($old_email)) {
            $old_email = $this->email;
        }

        $payload = array(
            'email' => $this->email,
            'userName' => $this->userName,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
        );
        if (count($this->attributes) > 0) {
            $payload['attributes'] = array();
            foreach ($this->attributes as $name => $value) {
                $payload['attributes'][] = array('name' => $name, 'value' => $value);
            }
        }
        $url = null;
        if ($force_update || $this->createdAt) {
            if ($this->developerId) {
                $payload['developerId'] = $this->developerId;
            }
            $url = rawurlencode($old_email);
        }
        // Save our desired status for later.
        $cached_status = $this->status;
        if ($force_update) {
            $this->put($url, $payload);
        } else {
            $this->post($url, $payload);
        }
        self::loadFromResponse($this, $this->responseObj);
        // If status has changed, we must directly change it with a separate
        // POST call, because Edge will ignore a 'status' member in the
        // app-save payload.
        if (isset($cached_status) && isset($this->previousStatus) && $cached_status != $this->previousStatus) {
            $this->post($old_email . '?action=' . $cached_status);
            $this->status = $cached_status;
        }
        $this->previousStatus = $this->status;
    }

    /**
     * {@inheritDoc}
     */
    public function delete($email = null)
    {
        $email = $email ? : $this->email;
        $this->http_delete(rawurlencode($email));
        if ($email == $this->email) {
            $this->blankValues();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function listDevelopers()
    {
        $this->get();
        $developers = $this->responseObj;
        return $developers;
    }

    /**
     * Returns an array of all developers in the org.
     *
     * @return array
     */
    public function loadAllDevelopers()
    {
        $this->get('?expand=true');
        $developers = $this->responseObj;
        $out = array();
        foreach ($developers['developer'] as $dev) {
            $developer = new Developer($this->config);
            self::loadFromResponse($developer, $dev);
            $developer->previousStatus = $developer->status;
            $out[] = $developer;
        }
        return $out;
    }

    /**
     * {@inheritDoc}
     */
    public function validateUser()
    {
        if (!empty($this->email) && (strpos($this->email, '@') > 0)) {
            $name = explode('@', $this->email, 2);
            if (empty($this->firstName)) {
                $this->firstName = $name[0];
            }
            if (empty($this->lastName)) {
                $this->lastName = $name[1];
            }
        }
        return (!empty($this->firstName) && !empty($this->lastName) && !empty($this->userName) && !empty($this->email) && strpos($this->email, '@') > 0);
    }

    /**
     * {@inheritDoc}
     */
    public function blankValues()
    {
        $this->apps = array();
        $this->email = null;
        $this->developerId = null;
        $this->firstName = null;
        $this->lastName = null;
        $this->userName = null;
        $this->organizationName = null;
        $this->status = null;
        $this->attributes = array();
        $this->createdAt = null;
        $this->createdBy = null;
        $this->modifiedAt = null;
        $this->modifiedBy = null;
        $this->previousStatus = null;
        $this->companies = array();
    }


    /**
     * {@inheritDoc}
     */
    public function toArray($include_debug_data = true)
    {
        $properties = array_keys(get_object_vars($this));
        $excluded_properties = array_keys(get_class_vars(get_parent_class($this)));
        $output = array();
        foreach ($properties as $property) {
            if (!in_array($property, $excluded_properties)) {
                $output[$property] = $this->$property;
            }
        }
        $output['debugData'] = $include_debug_data ? $this->getDebugData() : null;
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
