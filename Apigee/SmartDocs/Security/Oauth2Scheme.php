<?php

namespace Apigee\SmartDocs\Security;

use Apigee\Exceptions\ParameterException;

/**
 * Class Oauth2Scheme
 *
 * @package Apigee\SmartDocs\Security
 */
class Oauth2Scheme extends SecurityScheme
{

    /**
     * @var string
     */
    protected $grantType;

    /**
     * @var string
     */
    protected $authorizationUrl;

    /**
     * @var string
     */
    protected $authorizationVerb;

    /**
     * @var string
     */
    protected $accessTokenUrl;

    /**
     * @var string
     */
    protected $accessTokenParamName;

    /**
     *
     * @var string
     */
    protected $in;

    /**
     *
     * @var string
     */
    protected $clientAuthenticationMethod;

    /**
     * @var object
     */
    protected $scopes;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $parameters)
    {
        // Ensure that scopes is saved as an object.
        if (array_key_exists('scopes', $parameters)) {
            $parameters['scopes'] = (object) $parameters['scopes'];
        } else {
            $parameters['scopes'] = new \stdClass();
        }
        parent::__construct($parameters);
    }

    /**
     * Returns the OAuth2 grant type.
     *
     * @return string
     */
    public function getGrantType()
    {
        return $this->grantType;
    }

    /**
     * Sets the OAuth2 grant type.
     *
     * @param string $type
     */
    public function setGrantType($type)
    {
        // TODO: validate $type
        $this->grantType = $type;
    }

    /**
     * Gets the OAuth2 authorization URL.
     *
     * @return string
     */
    public function getAuthorizationUrl()
    {
        return $this->authorizationUrl;
    }

    /**
     * Sets the OAuth2 authorization URL.
     *
     * @param $url
     */
    public function setAuthorizationUrl($url)
    {
        // TODO: validate $url
        $this->authorizationUrl = $url;
    }

    /**
     * Gets the OAuth2 authorization verb (GET or POST).
     *
     * @return string
     */
    public function getAuthorizationVerb()
    {
        return $this->authorizationVerb;
    }

    /**
     * Sets the OAuth2 authorization verb (GET or POST).
     *
     * @param string $verb
     */
    public function setAuthorizationVerb($verb)
    {
        $verb = strtoupper($verb);
        if ($verb != 'GET' && $verb != 'POST') {
            throw new ParameterException('Authorization verb must be either GET or POST.');
        }
        $this->authorizationVerb = $verb;
    }

    /**
     * Gets the OAuth2 Access Token URL.
     *
     * @return string
     */
    public function getAccessTokenUrl()
    {
        return $this->accessTokenUrl;
    }

    /**
     * Sets the OAuth2 Access Token URL.
     *
     * @param string $url
     */
    public function setAccessTokenUrl($url)
    {
        // TODO: validate $url
        $this->accessTokenUrl = $url;
    }

    /**
     * Gets the access token parameter name.
     *
     * @return string
     */
    public function getAccessTokenParamName()
    {
        return $this->accessTokenParamName;
    }

    /**
     * Sets the access token parameter name.
     *
     * @param string $name
     */
    public function setAccessTokenParamName($name)
    {
        // TODO: validate $name
        $this->accessTokenParamName = $name;
    }

    /**
     * Get the in (location) value of the scheme.
     *
     * @return string
     */
    public function getIn()
    {
        return $this->in;
    }

    /**
     * Sets the in (location) value of the scheme.
     *
     * @param string $in
     */
    public function setIn($in)
    {
        $this->in = $in;
    }

    /**
     * Gets the client authentication method.
     *
     * @return string
     */
    public function getClientAuthenticationMethod(){
        return $this->clientAuthenticationMethod;
    }

    /**
     * Sets the client authentication method.
     *
     * @param string $clientAuthenticationMethod
     */
    public function setClientAuthenticationMethod($clientAuthenticationMethod){
        $this->clientAuthenticationMethod = $clientAuthenticationMethod;
    }

    /**
     * Gets OAuth2 scopes.
     *
     * @return array
     */
    public function getScopes()
    {
        return (array) $this->scopes;
    }

    /**
     * Sets OAuth2 scopes.
     *
     * @param array $scopes
     */
    public function setScopes(array $scopes)
    {
        $this->scopes = (object) $scopes;
    }

    /**
     * {@inheritdoc}
     */
    public function getType($humanReadable = false)
    {
        if ($humanReadable) {
            return 'OAuth2: ' . ucwords(strtolower($this->getGrantType()));
        }
        return 'OAUTH2';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray($is_update = false)
    {
        $returnVal = parent::toArray($is_update);
        if (!$is_update) {
            $returnVal['grantType'] = $this->grantType;
        }
        $returnVal += array(
            'authorizationUrl' => $this->authorizationUrl,
            'authorizationVerb' => $this->authorizationVerb,
            'accessTokenUrl' => $this->accessTokenUrl,
            'accessTokenParamName' => $this->accessTokenParamName,
            'in' => $this->in,
            'clientAuthenticationMethod' => $this->clientAuthenticationMethod,
            'scopes' => $this->scopes
        );
        return $returnVal;
    }
}