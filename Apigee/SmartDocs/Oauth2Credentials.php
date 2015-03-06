<?php

namespace Apigee\SmartDocs;

use Apigee\Exceptions\ParameterException;

/**
 * Holds information for OAuth2 credentials.
 *
 * @package Apigee\SmartDocs
 * @author djohnson
 */
class Oauth2Credentials
{
    /** @var string */
    private $clientId;
    /** @var string */
    private $clientSecret;
    /** @var string */
    private $authorizationUrl;
    /** @var string */
    private $authorizationVerb; // {GET,POST}
    /** @var string */
    private $accessTokenUrl;
    /** @var string */
    private $accessTokenType; // {header,query}
    /** @var string */
    private $accessTokenParamName;
    /** @var string */
    private $clientAuthScheme; // {header,body}
    /** @var string */
    private $callbackURL;
    /** @var string */
    private $name;

    /* Accessors (getters/setters) */
    public function getClientId()
    {
        return $this->clientId;
    }

    public function setClientId($id)
    {
        $this->clientId = $id;
    }

    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    public function setClientSecret($secret)
    {
        $this->clientSecret = $secret;
    }

    public function getAuthUrl()
    {
        return $this->authorizationUrl;
    }

    public function setAuthUrl($url)
    {
        $this->authorizationUrl = $url;
    }

    public function getAuthVerb()
    {
        return $this->authorizationVerb;
    }

    public function setAuthVerb($verb)
    {
        $verb = strtoupper($verb);
        if ($verb != 'GET' && $verb != 'POST') {
            throw new ParameterException('Invalid authorization verb ‘' . $verb . '’ (valid values are GET and POST).');
        }
        $this->authorizationVerb = $verb;
    }

    public function getAccessTokenUrl()
    {
        return $this->accessTokenUrl;
    }

    public function setAccessTokenUrl($url)
    {
        $this->accessTokenUrl = $url;
    }

    public function getAccessTokenType()
    {
        return $this->accessTokenType;
    }

    public function setAccessTokenType($type)
    {
        if ($type != 'header' && $type != 'query') {
            throw new ParameterException('Invalid access token type ‘' . $type . '’ (valid values are ‘header’ and ‘query’).');
        }
        $this->accessTokenType = $type;
    }

    public function getAccessTokenParamName()
    {
        return $this->accessTokenParamName;
    }

    public function setAccessTokenParamName($name)
    {
        $this->accessTokenParamName = $name;
    }

    public function getClientAuthScheme()
    {
        return $this->clientAuthScheme;
    }

    public function setClientAuthScheme($scheme)
    {
        if ($scheme != 'header' && $scheme != 'body') {
            throw new ParameterException('Invalid client auth scheme ‘' . $scheme . '’ (valid values are ‘header’ and ‘body’).');
        }
        $this->clientAuthScheme = $scheme;
    }

    public function getCallbackUrl()
    {
        return $this->callbackURL;
    }

    public function setCallbackUrl($url)
    {
        $this->callbackURL = $url;
    }

    /**
     * Persists this object as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $vars = get_object_vars($this);
        $vars['name'] = 'oauth2webserverflow';
        return $vars;
    }

    /**
     * Populates this object with values from a payload returned from SmartDocs.
     *
     * @param array $payload
     */
    public function __construct(array $payload = null)
    {
        foreach (get_object_vars($this) as $key => $value) {
            if (array_key_exists($key, $payload)) {
                $this->$key = $payload[$key];
            }
        }
    }
}