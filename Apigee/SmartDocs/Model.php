<?php
namespace Apigee\SmartDocs;

use Apigee\Util\OrgConfig;
use Apigee\Util\APIObject;
use Apigee\Exceptions\ParameterException;

/**
 * Represents a related collection of resources (in one or more revisions), each
 * of which contains a list of methods.
 *
 * @package Apigee\SmartDocs
 * @author djohnson
 */
class Model extends APIObject implements \Serializable
{

    /** @var string */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $displayName;

    /** @var string */
    private $description;

    /** @var int */
    private $createdTime;

    /** @var int */
    private $modifiedTime;

    /** @var int */
    private $latestRevisionNumber;

    /**
     * @var int
     *      Not internally set,
     */
    private $methodCount;

    /** @var array */
    private $tags;

    /**
     * Returns the current Model to its pristine state.
     */
    protected function blankValues()
    {
        $this->id = '';
        $this->name = '';
        $this->displayName = '';
        $this->description = '';
        $this->createdTime = 0;
        $this->modifiedTime = 0;
        $this->latestRevisionNumber = -1;
        $this->methodCount = -1;
        $this->tags = array();
    }

    /* Accessors (getters/setters) */
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

    public function getCreatedTime()
    {
        return floor($this->createdTime / 1000);
    }

    public function getModifiedTime()
    {
        return floor($this->modifiedTime / 1000);
    }

    public function getLatestRevisionNumber()
    {
        return $this->latestRevisionNumber;
    }

    public function setLatestRevisionNumber($int)
    {
        $this->latestRevisionNumber = intval($int);
    }

    public function getMethodCount()
    {
        return $this->methodCount;
    }

    public function setMethodCount($int)
    {
        $this->methodCount = intval($int);
    }

    public function setTag($name, $value)
    {
        $this->tags[$name] = $value;
    }

    public function getTag($name)
    {
        if (array_key_exists($name, $this->tags)) {
            return $this->tags[$name];
        }
        return NULL;
    }

    /**
     * Takes values from an array and populates a Model with them.
     *
     * @param Model $model
     * @param array $array
     */
    public static function fromArray(Model $model, array $array)
    {
        foreach ($array as $key => $value) {
            if (property_exists($model, $key)) {
                $model->$key = $value;
            }
        }
    }

    /**
     * Persists the current Model as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $payload_keys = array(
            'id', 'name', 'displayName', 'description', 'createdTime', 'methodCount',
            'latestRevisionNumber', 'methodCount', 'tags', 'config'
        );
        $payload = array();
        foreach ($payload_keys as $key) {
            $payload[$key] = $this->$key;
        }
        return $payload;
    }

    /**
     * Initializes this object.
     *
     * @param OrgConfig $config
     */
    public function __construct(OrgConfig $config)
    {
        $this->init($config, '/o/' . rawurlencode($config->orgName) . '/apimodels');
        $this->blankValues();
    }

    /**
     * Returns an array of all models configured in this org.
     *
     * @return array
     */
    public function listModels()
    {
        $models = array();
        $this->get();
        foreach ($this->responseObj as $key => $blob) {
            $model = new Model($this->getConfig());
            self::fromArray($model, $blob);
            $models[$key] = $model;
        }
        return $models;
    }

    /**
     * Loads a single model.
     *
     * @param null|string $modelId
     * @throws ParameterException
     */
    public function load($modelId = null)
    {
        $modelId = $modelId ?: $this->id;
        if (empty($modelId)) {
            throw new ParameterException('Cannot load a model with no ID.');
        }
        $this->get($modelId);
        self::fromArray($this, $this->responseObj);
        if (!array_key_exists('latestRevisionNumber', $this->responseObj)) {
            $this->latestRevisionNumber = 0;
        }
    }

    /**
     * Saves (via insert or update) a model.
     *
     * @param bool $update
     */
    public function save($update = FALSE)
    {
        $payload = $this->toArray();
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
     * Deletes a model.
     *
     * @param null|string $modelId
     * @throws ParameterException
     */
    public function delete($modelId = null)
    {
        $modelId = $modelId ?: $this->id;
        if (empty($modelId)) {
            throw new ParameterException('Cannot delete a model with no ID.');
        }
        $this->http_delete($modelId);
        // TODO: should we do this, or call blankValues()?
        self::fromArray($this, $this->responseObj);
    }

    // Functions implementing the Serializable interface
    public function serialize()
    {
        return serialize($this->toArray());
    }

    public function unserialize($serialized)
    {
        self::fromArray($this, unserialize($serialized));
    }
}
