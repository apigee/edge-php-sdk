<?php
namespace Apigee\SmartDocs\Security;

/**
 * Class Oauth2TemplateAuthScheme.
 *
 * @package Apigee\SmartDocs\Security
 */
class Oauth2TemplateAuthScheme extends TemplateAuthScheme
{

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * Gets the type of the scheme.
     *
     * @return string
     */
    public function getType()
    {
        return 'OAUTH2';
    }

    /**
     * Gets the clientId of the scheme.
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Sets the clientId of the scheme.
     *
     * @param string
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * Gets the client secret of the scheme.
     *
     * @return string
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * Sets the client secret of the scheme.
     *
     * @param string
     */
    public function setClientSecret($clientSecret)
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray($is_update = false)
    {
        $retVal = parent::toArray($is_update);
        $retVal += array(
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret

        );
        return $retVal;
    }
}
