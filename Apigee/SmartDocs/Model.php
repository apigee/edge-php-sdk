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
class Model extends APIObject
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

    /** @var array */
    private $tags;

    /** @var array */
    private $customAttributes;

    /**
     * @var array
     *      This is an array of string identifiers, corresponding to security
     *      schemes defined by the Security object attached to this model's
     *      revision.
     */
    private $security;

    /**
     * @var array
     *      This is a key-value store for any metadata that a client might
     *      want to persist related to the model. It is neither transmitted to
     *      Edge nor pulled from it.
     */
    private $metadata;

    /**
     * @var \Apigee\SmartDocs\Revision|null
     *      This is not auto-populated, but may be externally set.
     */
    private $activeRevision;

    /**
     * Returns the current Model to its pristine state.
     */
    protected function blankValues()
    {
        $this->id = '';
        $this->name = '';
        $this->displayName = '';
        $this->description = '';
        $this->tags = array();
        $this->customAttributes = array();
        $this->security = array();
        $this->metadata = array();

        $this->createdTime = 0;
        $this->modifiedTime = 0;
        $this->latestRevisionNumber = -1; // Indicates that this value is unset.
        $this->activeRevision = null;
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

    public function getTags()
    {
        return $this->tags;
    }
    public function setTags(array $tags)
    {
        $this->tags = $tags;
    }

    public function getSecurity()
    {
        return $this->security;
    }
    public function setSecurity(array $security)
    {
        $this->security = $security;
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

    public function getActiveRevision() {
        return $this->activeRevision;
    }
    public function setActiveRevision(Revision $revision) {
        $this->activeRevision = $revision;
    }
    public function clearActiveRevision() {
        $this->activeRevision = null;
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
                if ($key == 'activeRevision' && is_array($value) && !empty($value)) {
                    $revision = new Revision($model->getConfig(), $model->getUuid());
                    Revision::fromArray($revision, $value);
                    $value = $revision;
                }
                $model->$key = $value;
            }
        }
    }

    /**
     * Persists the current Model as an array.
     *
     * @return array
     */
    public function toArray($verbose = true)
    {
        $payload_keys = array('name', 'displayName', 'description', 'tags', 'customAttributes');
        if ($verbose) {
            $payload_keys = array_merge($payload_keys, array(
                'id', 'latestRevisionNumber', 'tags', 'createdTime', 'modifiedTime', 'metadata',
                'activeRevision'
            ));
        }
        $payload = array();
        foreach ($payload_keys as $key) {
            $value = $this->$key;
            if ($key == 'activeRevision' && $value instanceof Revision) {
                $value = $value->toArray();
            }
            $payload[$key] = $value;
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
        $this->blankValues();
        $this->init($config, '/o/' . rawurlencode($config->orgName) . '/apimodels');
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
    public function save($update = false)
    {
        $payload = $this->toArray();
        // Eliminate any customAttributes with empty keys or values.
        foreach ($payload['customAttributes'] as $key => $value) {
            if ($value === NULL || $value === '' || $key === NULL || $key === '') {
                unset($payload['customAttributes'][$key]);
            }
        }
        if (empty($payload['customAttributes'])) {
            $payload['customAttributes'] = new \stdClass;
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

    /**
     * Import a model from a file.
     *
     * The file is passed into the method as a string. Note that you do not have
     * to create a revision first, this method will automagically create the
     * revision for you.
     *
     * Note that Swagger 1.2 cannot be expressed as in a single-file format, so
     * importFile can only be used with Swagger 2.0.  For Swagger 1.2, use
     * importUrl() instead.
     *
     * @param string $document The text to import into the model.
     * @param string $document_format The format, either 'wadl', 'swagger',
     * or 'apimodel'.
     * @param string $content_type is the mime type, which valid values depend
     * on the document format:
     *   wadl: 'application/xml'
     *   swagger: 'application/yaml' or 'application/json'
     *   apimodel: 'application/json'
     * @param null|string $modelId The model id, if not passed will be the modelId
     * from this object.
     *
     * @throws ParameterException
     *
     * @return int Revision number of newly created revision.
     */
    public function importFile($document, $document_format, $content_type, $modelId = null)
    {
        $modelId = $modelId ?: $this->id;
        if (empty($modelId)) {
          throw new ParameterException('Cannot import a model with no ID.');
        }

        $this->post($modelId . '/import/file?format=' . $document_format, $document, $content_type);
        $revision = $this->responseObj['revisionNumber'];
        $this->latestRevisionNumber = $revision;
        return $revision;
    }

    /**
     * Import a model from a URL.
     *
     * The file is passed into the method as a string. Note that you do not have
     * to create a revision first, this method will automagically create the
     * revision for you.
     *
     * Note that Swagger 1.2 cannot be expressed as in a single-file format, so
     * importFile can only be used with Swagger 2.0.  For Swagger 1.2, use
     * importUrl() instead.
     *
     * @param string $url The URL to get the model from.
     * @param string $document_format The format, either 'wadl', 'swagger',
     * or 'apimodel'.
     * @param string $content_type is the mime type, which valid values depend
     * on the document format:
     *   wadl: 'application/xml'
     *   swagger: 'application/yaml' or 'application/json'
     *   apimodel: 'application/json'
     * @param null|string $modelId The model id, if not passed will be the modelId
     * from this object.
     *
     * @throws ParameterException, RequestException
     *
     * @return int Revision number of newly created revision.
     */
    public function importUrl($url, $document_format, $modelId = null)
    {
        $modelId = $modelId ?: $this->id;
        if (empty($modelId)) {
          throw new ParameterException('Cannot import a model with no ID.');
        }

        $payload = "URL=" . $url;
        $this->post($modelId . '/import/url?format=' . $document_format, $payload, 'text/plain');
        $revision = $this->responseObj['revisionNumber'];
        $this->latestRevisionNumber = $revision;
        return $revision;
    }


}
