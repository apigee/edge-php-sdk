<?php

namespace Apigee\SmartDocs;

use Apigee\Util\OrgConfig;
use Apigee\Util\APIObject;
use Apigee\Exceptions\ParameterException;
use Apigee\Exceptions\ResponseException;

/**
 * Loads and saves template HTML.
 *
 * @package Apigee\SmartDocs
 * @author djohnson
 */
class Template extends APIObject
{

    /**
     * Initializes this object's base URL.
     *
     * @param \Apigee\Util\OrgConfig $config
     *   Object containing org configuration settings.
     * @param string $modelId
     *   UUID or machine-name of the model.
     */
    public function __construct(OrgConfig $config, $modelId)
    {
        $baseUrl = '/o/' . rawurlencode($config->orgName)
            . '/apimodels/' . rawurlencode($modelId)
            . '/templates';
        $this->init($config, $baseUrl);
    }

    /**
     * Loads a template of a given name and type.
     *
     * @param string $name
     *   Name of the template.
     * @param string $type
     *   Either 'index' or 'method'.
     *
     * @return string
     *   Returns the template HTML.
     *
     * @throws ParameterException
     */
    public function load($name, $type)
    {
        if ($type != 'index' && $type != 'method') {
            $message = 'Invalid template type ‘%s’ (valid values are ‘index’ and ‘method’).';
            throw new ParameterException(sprintf($message, $type));
        }
        $this->get($name . '?type=' . $type, 'text/html');
        return $this->responseText;
    }

    /**
     * Saves HTML to a template of a given name and type.
     *
     * If $update is true and the call results in a 404, we set $update to
     * false and re-try, since this indicates that the template did not exist
     * yet.
     *
     * @param string $name
     *   Name of the template to be saved.
     * @param string $type
     *   Either 'index' or 'method'.
     * @param string $html
     *   HTML to be saved to the template.
     * @param bool $update
     *   True if we are updating, false if we are inserting.
     *
     * @return string
     *   Returns the template HTML that was saved.
     *
     * @throws ParameterException
     */
    public function save($name, $type, $html, $update = false)
    {
        if ($type != 'index' && $type != 'method') {
            $message = 'Invalid template type ‘%s’ (valid values are ‘index’ and ‘method’).';
            throw new ParameterException(sprintf($message, $type));
        }
        if ($update) {
            $uri = rawurlencode($name) . '?type=' . $type;
            $method = 'put';
        } else {
            $uri = '?type=' . $type . '&name=' . urlencode($name);
            $method = 'post';
        }
        // Make sure that update 404 errors are not logged by replacing the
        // logger with a dummy that routes errors to /dev/null.
        if ($update) {
            if (!(self::$logger instanceof \Psr\Log\NullLogger)) {
                $cached_logger = self::$logger;
                self::$logger = new \Psr\Log\NullLogger();
            }
            $this->clearSubscribers();
        }

        try {
            $this->$method($uri, $html, 'text/html', 'text/html');
            // Restore logger if it was cached.
            if (isset($cached_logger)) {
                self::$logger = $cached_logger;
            }
            if ($update) {
               $this->restoreSubscribers();
            }
        } catch (ResponseException $e) {
            // Restore logger if it was cached.
            if (isset($cached_logger)) {
                self::$logger = $cached_logger;
            }
            if ($update) {
                $this->restoreSubscribers();
            }
            // If update failed, try insert.
            if ($update && $e->getCode() == 404) {
                $this->save($name, $type, $html, false);
            } else {
                throw $e;
            }
        }
        return $this->responseText;
    }

    // TODO: delete?
}
