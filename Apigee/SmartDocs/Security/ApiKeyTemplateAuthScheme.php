<?php

namespace Apigee\SmartDocs\Security;


class ApiKeyTemplateAuthScheme extends TemplateAuthScheme
{
    /**
     *
     * @var string
     */
    protected $keyName;

    /**
     *
     * @var string
     */
    protected $keyValue;

    /**
     * Gets the type of the template auth scheme.
     *
     * @return string
     */
    public function getType()
    {
        return 'APIKEY';
    }

    /**
     * Gets the key name of the scheme.
     *
     * @return string
     */
    public function getKeyName()
    {
        return $this->keyName;
    }

    /**
     * Sets the keyname of the scheme.
     *
     * @param string
     */
    public function setKeyName($keyName)
    {
        $this->keyName = $keyName;
    }

    /**
     * Gets the key value of the scheme.
     *
     * @return string
     */
    public function getKeyValue()
    {
        return $this->keyValue;
    }

    /**
     * Sets the key value of the scheme.
     *
     * @param string
     */
    public function setKeyValue($keyValue)
    {
        $this->keyValue = $keyValue;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray($is_update = false)
    {
        $retVal = parent::toArray($is_update);
        $retVal += array(
            'keyName' => $this->keyName,
            'keyValue' => $this->keyValue
        );
        return $retVal;
    }
}