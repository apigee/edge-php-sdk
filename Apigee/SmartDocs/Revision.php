<?php

namespace Apigee\SmartDocs;

use Apigee\Util\OrgConfig;
use Apigee\Util\APIObject;
use Apigee\Exceptions\ParameterException;

/**
 * Abstraction of a Revision of a model.
 *
 * @package Apigee\SmartDocs
 * @author djohnson
 */
class Revision extends APIObject
{
    /** @var string */
    protected $id;

    /** @var string */
    protected $displayName;

    /** @var string */
    protected $description;

    /** @var string */
    protected $releaseVersion;

    /** @var string */
    protected $changeLog;

    /** @var string */
    protected $baseUrl;

    /** @var array */
    protected $params;

    /** @var array */
    protected $paramGroups;

    /** @var array */
    protected $tags;

    /** @var array */
    protected $customAttributes;


    /** @var \Apigee\SmartDocs\Resource[] */
    protected $resources;

    /** @var int */
    protected $revisionNumber;

    /** @var int */
    protected $createdTime;

    /** @var int */
    protected $createdBy;

    /** @var string */
    protected $modifiedTime;

    /** @var string */
    protected $modifiedBy;

    /** @var  string */
    protected $apiId;

    /** @var string */
    protected $apiName;

    /**
     * Restores this Revision object to its pristine state.
     */
    protected function blankValues()
    {
        $this->displayName = '';
        $this->description = '';
        $this->releaseVersion = '';
        $this->changeLog = '';
        $this->baseUrl = '';
        $this->params = array();
        $this->paramGroups = array();
        $this->tags = array();
        $this->customAttributes = array();

        // Auto-generated fields (read-only)
        $this->id = '';
        $this->revisionNumber = 0;
        $this->createdTime = 0;
        $this->modifiedTime = 0;
        $this->createdBy = '';
        $this->modifiedBy = '';

        $this->resources = array();
        $this->apiId = '';
        $this->apiName = '';
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

    public function getReleaseVersion()
    {
        return $this->releaseVersion;
    }

    public function setReleaseVersion($ver)
    {
        $this->releaseVersion = $ver;
    }

    public function getChangeLog()
    {
        return $this->changeLog;
    }

    public function setChangeLog($log)
    {
        $this->changeLog = $log;
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;
    }

    public function getParameters()
    {
        return $this->params;
    }

    public function setParameters(array $params)
    {
        $this->params = $params;
    }

    public function getParamGroups()
    {
        return $this->paramGroups;
    }

    public function setParamGroups(array $groups)
    {
        $this->paramGroups = $groups;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function setTags(array $tags)
    {
        $this->tags = $tags;
    }

    public function getCustomAttributes()
    {
        return $this->customAttributes;
    }

    public function setCustomAttributes(array $attr)
    {
        $this->customAttributes = $attr;
    }

    public function getRevisionNumber()
    {
        return $this->revisionNumber;
    }

    public function &getResources()
    {
        return $this->resources;
    }

    public function getCreatedTime()
    {
        return floor($this->createdTime / 1000);
    }

    public function getModifiedTime()
    {
        return floor($this->modifiedTime / 1000);
    }


    /**
     * Takes values from an array and populates a Revision with them.
     *
     * @param Revision $revision
     * @param array $array
     */
    public static function fromArray(Revision $revision, array $array)
    {
        foreach ($array as $key => $value) {
            if (property_exists($revision, $key)) {
                $revision->$key = $value;
            }
        }
        // Fill in sub-objects
        if (!empty($revision->resources)) {
            foreach ($revision->resources as &$resource) {
                if (is_array($resource)) {
                    $resourceObj = new Resource($revision->getConfig(), $revision->apiId, $revision->id);
                    Resource::fromArray($resourceObj, $resource);
                    $resource = $resourceObj;
                }
            }
        }
    }

    /**
     * Persists the current Revision as an array.
     *
     * @param bool $verbose
     * @return array
     */
    public function toArray($verbose = true)
    {
        $payload_keys = array(
            'displayName', 'description', 'releaseVersion', 'changeLog',
            'baseUrl', 'params', 'paramGroups', 'tags', 'customAttributes',
        );
        if ($verbose) {
            $payload_keys = array_merge($payload_keys, array(
                'id', 'revisionNumber', 'createdTime', 'modifiedTime', 'apiId',
                'createdBy', 'modifiedBy'
            ));
        }
        $payload = array();
        foreach ($payload_keys as $key) {
            $payload[$key] = $this->$key;
        }
        if ($verbose && count($this->resources) > 0) {
            $payload['resources'] = array();
            foreach ($this->resources as $resource) {
                $payload['resources'][] = $resource->toArray();
            }
        }
        return $payload;
    }

    /**
     * Initializes this object.
     *
     * @param OrgConfig $config
     * @param string $modelId
     */
    public function __construct(OrgConfig $config, $modelId)
    {
        $this->blankValues();
        $this->apiId = $modelId;
        $baseUrl = '/o/' . rawurlencode($config->orgName)
            . '/apimodels/' . rawurlencode($this->apiId)
            . '/revisions';
        $this->init($config, $baseUrl);
    }

    /**
     * Returns an array of all revisions for this model. The order of items in
     * the array may be non-deterministic.
     *
     * @return Revision[]
     */
    public function listRevisions()
    {
        $revisions = array();
        $this->get();
        foreach ($this->responseObj as $key => $blob) {
            $revision = new Revision($this->getConfig(), $this->apiId);
            self::fromArray($revision, $blob);
            $revisions[$key] = $revision;
        }
        return $revisions;
    }

    /**
     * Loads a revision.
     *
     * @param string|int|null $revisionId
     *        This may be a UUID or a revision number.
     * @param bool $summaryOnly
     *        When true, resource objects are returned as stubs without full
     *        detail.
     * @throws ParameterException
     */
    public function load($revisionId = null, $summaryOnly = false)
    {
        $revisionId = $revisionId ?: $this->id;
        if (empty($revisionId)) {
            throw new ParameterException('Cannot load a revision with no Revision UUID.');
        }
        if (is_int($revisionId) && $revisionId < 1) {
            throw new ParameterException('Cannot load a revision number less than 1.');
        }
        $queryString = ($summaryOnly ? '' : '?expand=true');
        $this->get($revisionId . $queryString);
        self::fromArray($this, $this->responseObj);
    }

    /**
     * Saves the current revision. If $update is true we presume that this is
     * an update (PUT) operation; otherwise it's an insert (POST).
     *
     * @todo Validate that required fields are not empty.
     *
     * @param bool $update
     */
    public function save($update = false)
    {
        $payload = $this->toArray(false);
        $keys = array_keys($payload);
        foreach ($keys as $key) {
            if (empty($payload[$key])) {
                unset($payload[$key]);
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
     * Deletes a revision.
     *
     * @param string|int|null $revisionId
     *        This may be a UUID or a revision number.
     * @throws ParameterException
     */
    public function delete($revisionId = null)
    {
        $revisionId = $revisionId ?: $this->id;
        if (empty($revisionId)) {
            throw new ParameterException('Cannot delete a revision with no Revision UUID.');
        }
        $this->httpDelete($revisionId);
        // TODO: should we do this, or call blankValues()?
        self::fromArray($this, $this->responseObj);
    }

    /**
     * Exports a SmartDocs revision as JSON (default) or an XML-based format.
     *
     * @param string $format Export format, either 'wadl' or 'json', defaults
     *  to 'wadl'.
     * @param int|null $revision
     *
     * @return string
     */
    public function export($format = 'json', $revision = null)
    {
        $revision = $revision ?: 'latest';
        if ($format == 'json' || empty($format)) {
            $this->get($revision . '?expand=true');
        } else {
            // Export format is WADL.
            $this->get($revision . '?expand=true&format=' . $format, 'text/xml');
        }
        return $this->responseText;
    }
}
