<?php
namespace Apigee\SmartDocs\Security;



/**
 * Class TemplateAuthScheme
 *
 * @package Apigee\SmartDocs\Security
 */
abstract class TemplateAuthScheme
{
    /**
     * @var string
     */
    protected $name;

    /**
     * Gets the name of the template auth scheme.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name of the template auth scheme.
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Gets the type of the template auth scheme.
     *
     * @return string
     */
    public abstract function getType();

    /**
     * Populates this template auth scheme from JSON payload
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
     * Returns array that can be used to save this template auth scheme to the Modeling API.
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
     * @return TemplateAuthScheme
     */
    public static function fromArray(array $payload)
    {
        switch ($payload['type']) {
            case 'OAUTH2':
                return new Oauth2TemplateAuthScheme($payload);

            default:
                return new ApiKeyTemplateAuthScheme($payload);
        }
    }
}
