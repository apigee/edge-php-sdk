<?php
namespace Apigee\SmartDocs;

use Apigee\Util\OrgConfig;
use Apigee\Util\APIObject;
use Apigee\Exceptions\ParameterException;

/**
 * Represents an HTTP method on a versioned API resource.
 *
 * @package Apigee\SmartDocs
 * @author djohnson
 */
class Method extends APIObject
{

    /**
     * @var string
     *      a read-only UUID.
     */
    protected $id;
    /** @var string */
    protected $name;
    /** @var string */
    protected $verb;
    /**
     * @var array
     *      May contain the following keys:
     *      contentType, doc, sample, schema, parameters, attachments
     */
    protected $body;
    /**
     * @var array
     *      May contain the following keys:
     *      contentType, doc, sample, schema, parameters, errors
     */
    protected $response;
    /**
     * @var array
     *      May contain the following keys:
     *      contentType, type, harValue, sampleUrl
     */
    protected $samples;
    /** @var string */
    protected $displayName;
    /** @var string */
    protected $description;
    /** @var array */
    protected $parameters;
    /** @var array */
    protected $parameterGroups;
    /** @var array */
    protected $customAttributes;
    /** @var array */
    protected $tags;

    // Authorship attributes (read-only)
    // Times are in milliseconds after Jan 1, 1970 UTC. Div by 1000 for Unix time.
    /** @var int */
    protected $createdTime;
    /** @var int */
    protected $modifiedTime;
    /** @var string */
    protected $createdBy;
    /** @var string */
    protected $modifiedBy;

    // Inherited attributes (read-only)
    /** @var string */
    protected $apiRevisionId;
    /** @var string */
    protected $baseUrl;
    /** @var string */
    protected $path;
    /** @var string */
    protected $resourceId;
    /** @var string */
    protected $resourceName;
    /** @var int */
    protected $revision;
    /** @var string */
    protected $apiId;

    /**
     * @var array
     *      This is an array of string identifiers, corresponding to security
     *      schemes defined by the Security object attached to this model's
     *      revision.
     */
    private $security;

    /** @var array */
    protected $metadata;

    /**
     * Returns this object's member vars to their pristine state.
     */
    protected function blankValues()
    {
        $this->id = '';
        $this->name = '';
        $this->verb = '';
        $this->body = array();
        $this->response = array();
        $this->samples = array();
        $this->displayName = '';
        $this->description = '';
        $this->parameters = array();
        $this->parameterGroups = array();
        $this->customAttributes = array();
        $this->tags = array();
        $this->metadata = array();
        $this->security = array();

        $this->createdTime = 0;
        $this->modifiedTime = 0;
        $this->createdBy = '';
        $this->modifiedBy = '';

        $this->apiRevisionId = '';
        $this->baseUrl = '';
        $this->path = '';
        $this->resourceId = '';
        $this->resourceName = '';
        $this->revision = 0;
        $this->apiId = '';
    }

    /* Accessors (getters/setters) */
    public function getUuid()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getVerb()
    {
        return $this->verb;
    }

    public function setVerb($verb)
    {
        $this->verb = $verb;
    }

    public function getSecurity()
    {
        return $this->security;
    }
    public function setSecurity(array $security)
    {
        $this->security = $security;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody(array $body)
    {
        $this->body = $body;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse(array $response)
    {
        $this->response = $response;
    }

    public function getSamples()
    {
        return $this->samples;
    }

    public function setSamples(array $samples)
    {
        $this->samples = $samples;
    }

    public function getDisplayName()
    {
        if (empty($this->displayName)) {
            return $this->name;
        }
        return $this->displayName;
    }

    public function setDisplayName($name)
    {
        $this->displayName = $name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($desc)
    {
        $this->description = $desc;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function setParameters(array $parms)
    {
        $this->parameters = $parms;
    }

    public function getParameterGroups()
    {
        return $this->parameterGroups;
    }

    public function setParameterGroups(array $groups)
    {
        $this->parameterGroups = $groups;
    }

    public function getCustomAttributes()
    {
        return $this->customAttributes;
    }

    public function getCustomAttribute($name)
    {
        if (array_key_exists($name, $this->customAttributes)) {
            return $this->customAttributes[$name];
        }
        return NULL;
    }
    public function setCustomAttribute($name, $value)
    {
        if ($value === NULL || $value === '') {
            if (array_key_exists($name, $this->customAttributes)) {
                unset($this->customAttributes[$name]);
            }
        }
        elseif ($name !== NULL && $name !== '' && is_scalar($value)) {
            $this->customAttributes[strval($name)] = strval($value);
        }
        else {
            if (!is_scalar($value)) {
                throw new ParameterException('Custom Attribute value must be a scalar.');
            }
            else {
                throw new ParameterException('Custom Attribute name cannot be empty.');
            }
        }
    }
    public function setCustomAttributes(array $attr)
    {
        $this->customAttributes = array();
        foreach ($attr as $key => $value) {
            if ($value !== NULL && $value !== '' && $key !== NULL && $key !== '' && is_scalar($value)) {
                $this->customAttributes[strval($key)] = strval($value);
            }
        }
    }
    public function clearCustomAttribute($name)
    {
        if (array_key_exists($name, $this->customAttributes)) {
            unset($this->customAttributes[$name]);
        }
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function setTags(array $tags)
    {
        $this->tags = $tags;
    }

    public function getCreatedTime()
    {
        return floor($this->createdTime / 1000);
    }

    public function getModifiedTime()
    {
        return floor($this->modifiedTime / 1000);
    }

    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    public function getApiRevisionId()
    {
        return $this->apiRevisionId;
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getResourceId()
    {
        return $this->resourceId;
    }

    public function getResourceName()
    {
        return $this->resourceName;
    }

    public function getRevisionNumber()
    {
        return $this->revision;
    }

    public function getModelId()
    {
        return $this->apiId;
    }

    public function setMetadata($name, $value)
    {
        $this->metadata[$name] = $value;
    }
    public function getMetadata($name)
    {
        if (array_key_exists($name, $this->metadata)) {
            return $this->metadata[$name];
        }
        return NULL;
    }

    /**
     * Takes values from an array and populates a Method with them.
     *
     * @param Method $method
     * @param array $array
     */
    public static function fromArray(Method $method, array $array)
    {
        foreach ($array as $key => $value) {
            if (property_exists($method, $key)) {
                $method->$key = $value;
            }
        }
        // Fill in sub-objects
        if (!empty($method->resources)) {
            foreach ($method->resources as &$resource) {
                if (is_array($resource)) {
                    $resourceObj = new Resource($method->getConfig(), $method->apiId, $method->getApiRevisionId());
                    Resource::fromArray($resourceObj, $resource);
                    $resource = $resourceObj;
                }
            }
        }
    }

    /**
     * Persists the current Method as an array.
     *
     * @return array
     */
    public function toArray($verbose = true)
    {

        $payload_keys = array(
            'name', 'verb', 'security', 'body', 'response', 'samples',
            'displayName', 'description', 'parameters', 'parameterGroups',
            'customAttributes', 'tags', 'path'
        );
        if ($verbose) {
            $payload_keys = array_merge($payload_keys, array(
                'id', 'createdTime', 'modifiedTime', 'createdBy', 'modifiedBy',
                'metadata'
            ));
        }
        $payload = array();
        foreach ($payload_keys as $key) {
            $payload[$key] = $this->$key;
        }
        return $payload;
    }

    /**
     * Initializes all member variables.
     *
     * @param \Apigee\Util\OrgConfig $config
     * @param string $apiUuid
     * @param string $revisionUuid
     * @param string $resourceUuid
     */
    public function __construct(OrgConfig $config, $modelId, $revisionUuid, $resourceUuid)
    {
        $this->blankValues();
        $this->apiRevisionId = $revisionUuid;
        $this->resourceId = $resourceUuid;
        $this->apiId = $modelId;
        $basePath = '/o/' . rawurlencode($config->orgName) . '/apimodels/' . rawurlencode($this->apiId) . '/revisions/' . rawurlencode($this->apiRevisionId) . '/resources/' . $this->resourceId . '/methods';
        $this->init($config, $basePath);
    }

    /**
     * Fetches and returns an array of Methods on the current versioned resource.
     *
     * @return array
     */
    public function listMethods()
    {
        $methods = array();
        $this->get();
        foreach ($this->responseObj as $key => $blob) {
            $method = new Method($this->getConfig(), $this->apiId, $this->apiRevisionId, $this->resourceId);
            self::fromArray($method, $blob);
            $methods[$key] = $method;
        }
        return $methods;
    }

    /**
     * Fetches detail of a single Method from the current versioned resource.
     *
     * @param string|null $methodUuid
     * @throws ParameterException
     */
    public function load($methodUuid = null)
    {
        $methodUuid = $methodUuid ?: $this->id;
        if (empty($methodUuid)) {
            throw new ParameterException('Cannot load a method with no Method UUID.');
        }

        $this->get($methodUuid);
        self::fromArray($this, $this->responseObj);
    }

    /**
     * Persists detail of a single Method on the current versioned resource.
     *
     * @todo Validate that required fields are not empty.
     *
     * @param bool $update
     */
    public function save($update = false)
    {
        $array_members = array('security', 'parameters', 'parameterGroups', 'tags', 'samples');
        $object_members = array('body', 'response', 'customAttributes');
        $payload = $this->toArray(false);
        // Eliminate any customAttributes with empty keys or values.
        foreach ($payload['customAttributes'] as $key => $value) {
            if ($value === NULL || $value === '' || $key === NULL || $key === '') {
                unset($payload['customAttributes'][$key]);
            }
        }

        foreach ($array_members as $key) {
            if (empty($payload[$key])) {
                $payload[$key] = array();
            }
        }
        foreach ($object_members as $key) {
            if (empty($payload[$key])) {
                $payload[$key] = new \stdClass;
            }
        }

        if ($update) {
            $url = $this->id;
            $method = 'put';
        } else {
            $url = '';
            $method = 'post';
        }
        $this->$method($url, $payload);
        self::fromArray($this, $this->responseObj);
    }

    /**
     * Deletes a single Method from the current versioned resource.
     *
     * @param null|string $methodUuid
     * @throws ParameterException
     */
    public function delete($methodUuid = null)
    {
        $methodUuid = $methodUuid ?: $this->id;
        if (empty($methodUuid)) {
            throw new ParameterException('Cannot delete a method with no Method UUID.');
        }
        $this->http_delete($methodUuid);
        // TODO: should we do this, or call blankValues()?
        self::fromArray($this, $this->responseObj);
    }
}
