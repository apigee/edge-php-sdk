<?php

namespace Apigee\SmartDocs\Security;

/**
 * Class SecurityScheme
 *
 * @package Apigee\SmartDocs\Security
 */
abstract class SecurityScheme
{
    /**
     * @var string
     */
    protected $name;

    /**
     * Gets the unique name of this security scheme.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the unique name of this security scheme.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Gets the type of security scheme.
     *
     * @return string
     */
    public abstract function getType($humanReadable = false);

    /**
     * Populates this security scheme from a JSON payload.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        foreach ($parameters as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Returns array that can be used to save this scheme to the Modeling API.
     *
     * @return array
     */
    public function toArray($is_update = false)
    {
        $returnVal = array();
        if (!$is_update) {
            $returnVal['type'] = $this->getType();
        }
        $returnVal['name'] = $this->name;
        return $returnVal;
    }

    /**
     * Given response payload from Modeling API, returns appropriate object.
     *
     * @param array $payload
     *
     * @return SecurityScheme
     */
    public static function fromArray(array $payload)
    {
        switch ($payload['type']) {
            case 'OAUTH2':
                return new Oauth2Scheme($payload);

            case 'APIKEY':
                return new ApiKeyScheme($payload);

            default:
                return new BasicScheme($payload);
        }
    }
}