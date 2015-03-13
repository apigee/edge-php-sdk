<?php
namespace Apigee\SmartDocs;

use Apigee\Util\OrgConfig;
use Apigee\Util\APIObject;
use Apigee\Exceptions\ParameterException;

/**
 * Abstraction of a resource (URI pattern).
 * @package Apigee\SmartDocs
 * @author djohnson
 */
class Resource extends APIObject
{

    /** @var string */
    protected $id;

    /** @var string */
    protected $name;

    /** @var string */
    protected $displayName;

    /** @var string */
    protected $description;

    /** @var string */
    protected $baseUrl;

    /** @var string */
    protected $path;

    /** @var array */
    protected $parameters;

    /** @var array */
    protected $methods;

    /** @var int */
    protected $createdTime;

    /** @var int */
    protected $modifiedTime;

    /** @var string */
    protected $apiRevisionId;

    /** @var array */
    protected $resources;

    /** @var string */
    protected $modelId;

    /**
     * Returns this object to its pristine state.
     */
    protected function blankValues()
    {
        $this->id = '';
        $this->name = '';
        $this->displayName = '';
        $this->description = '';
        $this->baseUrl = '';
        $this->path = '';
        $this->parameters = array();
        $this->methods = array();
        $this->createdTime = 0;
        $this->modifiedTime = 0;
        $this->apiRevisionId = '';
        $this->resources = array();

        $this->modelId = '';
    }

    public function getUuid()
    {
        return $this->id;
    }

    public function setUuid($uuid)
    {
        $this->id = $uuid;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
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

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function setParameters(array $parms)
    {
        $this->parameters = $parms;
    }

    public function getMethods()
    {
        return $this->methods;
    }

    public function setMethods(array $methods)
    {
        $this->methods = $methods;
    }

    public function getCreatedTime()
    {
        return floor($this->createdTime / 1000);
    }

    public function getModifiedTime()
    {
        return floor($this->modifiedTime / 1000);
    }

    public function getApiRevisionId()
    {
        return $this->apiRevisionId;
    }

    public function setApiRevisionId($id)
    {
        $this->apiRevisionId = $id;
    }

    public function getResources()
    {
        return $this->resources;
    }

    public function setResources(array $resources)
    {
        $this->resources = $resources;
    }

    /**
     * Takes values from an array and populates a Resource with them.
     *
     * @param Resource $resource
     * @param array $array
     */
    public static function fromArray(Resource $resource, array $array)
    {
        foreach ($array as $key => $value) {
            if (property_exists($resource, $key)) {
                $resource->$key = $value;
            }
        }
        if (!empty($resource->methods)) {
            foreach ($resource->methods as &$method) {
                if (is_array($method)) {
                    $methodObj = new Method($resource->getConfig(), $resource->modelId, $resource->getApiRevisionId(), $resource->id);
                    Method::fromArray($methodObj, $method);
                    $method = $methodObj;
                }
            }
        }
        if (!empty($resource->resources)) {
            foreach ($resource->resources as &$subresource) {
                if (is_array($subresource)) {
                    $resourceObj = new Resource($resource->getConfig(), $resource->modelId, $resource->getApiRevisionId());
                    Resource::fromArray($resourceObj, $subresource);
                    $subresource = $resourceObj;
                }
            }
        }
    }

    /**
     * Persists the current Resource as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $payload_keys = array(
            'id', 'name', 'displayName', 'description', 'baseUrl', 'path',
            'parameters', 'methods', 'createdTime', 'modifiedTime', 'apiRevisionId',
            'resources', 'modelId'
        );
        $payload = array();
        foreach ($payload_keys as $key) {
            $payload[$key] = $this->$key;
        }
        return $payload;
    }

    /**
     * Constructs the proper values for the Apigee DocGen API.
     *
     * @param \Apigee\Util\OrgConfig $config
     * @param string $modelId
     * @param string $revisionUuid
     */
    public function __construct(OrgConfig $config, $modelId, $revisionUuid)
    {
        $this->blankValues();
        $this->modelId = $modelId;
        $this->apiRevisionId = $revisionUuid;
        $this->init($config, '/o/' . rawurlencode($config->orgName) . '/apimodels/' . rawurlencode($this->modelId) . '/revisions/' . $this->apiRevisionId . '/resources');
    }

    /**
     * Returns an array of Resources in the current revision of the current model.
     * @return array
     */
    public function listResources()
    {
        $resources = array();
        $this->get();
        foreach ($this->responseObj as $key => $blob) {
            $resource = new Resource($this->getConfig(), $this->modelId, $this->apiRevisionId);
            self::fromArray($resource, $blob);
            $resources[$key] = $resource;
        }
        return $resources;
    }

    /**
     * Loads a single resource from the current revision.
     *
     * @param null|string $resourceUuid
     * @throws ParameterException
     */
    public function load($resourceUuid = null)
    {
        $resourceUuid = $resourceUuid ?: $this->id;
        if (empty($resourceUuid)) {
            throw new ParameterException('Cannot load a resource with no Resource UUID.');
        }

        $this->get($resourceUuid);
        self::fromArray($this, $this->responseObj);
    }

    /**
     * Saves (insert or update) a resource.
     *
     * @param bool $update
     */
    public function save($update = FALSE)
    {
        $payload = $this->toArray();
        unset($payload['createdTime']);
        unset($payload['modelId']);
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
     * Deletes a resource from the current revision.
     *
     * @param null|string $resourceUuid
     * @throws ParameterException
     */
    public function delete($resourceUuid = null)
    {
        $resourceUuid = $resourceUuid ?: $this->id;
        if (empty($resourceUuid)) {
            throw new ParameterException('Cannot delete a resource with no Resource UUID.');
        }
        $this->http_delete($resourceUuid);
        // TODO: should we do this, or call blankValues()?
        self::fromArray($this, $this->responseObj);
    }
}
