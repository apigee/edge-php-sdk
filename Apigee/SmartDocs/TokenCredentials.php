<?php
namespace Apigee\SmartDocs;

use Apigee\Exceptions\ParameterException;

/**
 * Holds information for token-based credentials.
 *
 * @package Apigee\SmartDocs
 * @author djohnson
 */
class TokenCredentials
{
    private $tokenMap;
    private $tokenType;

    public function getTokenMap()
    {
        return $this->tokenMap;
    }

    public function setTokenMap(array $map)
    {
        $this->tokenMap = $map;
    }

    public function getTokenType()
    {
        return $this->tokenType;
    }

    public function setTokenType($type)
    {
        if ($type != 'query' && $type != 'header') {
            throw new ParameterException('Illegal auth scheme token type ‘' . $type . '’ (valid values are ‘query’ and ‘header’).');
        }
        $this->tokenType = $type;
    }

    public function toArray()
    {
        $vars = get_object_vars($this);
        $vars['name'] = 'custom';
        return $vars;
    }

    public function __construct(array $payload = null)
    {
        $this->tokenMap = array();
        $this->tokenType = 'query';
        if (is_array($payload)) {
            if (array_key_exists('tokenMap', $payload) && is_array($payload['tokenMap'])) {
                $this->tokenMap = $payload['tokenMap'];
            }
            if (array_key_exists('tokenType', $payload) && ($payload['tokenType'] == 'query' || $payload['tokenType'] == 'header')) {
                $this->tokenType = $payload['tokenType'];
            }
        }
    }
}
