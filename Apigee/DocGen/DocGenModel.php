<?php

/**
 * @file
 * Reads/Writes to and from the Apigee DocGen modeling API
 *
 * @author bhasselbeck
 */

namespace Apigee\DocGen;

use Apigee\Util\APIObject;
use Apigee\Util\OrgConfig;

class DocGenModel extends APIObject implements DocGenModelInterface
{

  /**
   * Constructs the proper values for the Apigee DocGen API.
   *
   * @param \Apigee\Util\OrgConfig $config
   */
  public function __construct(OrgConfig $config)
  {
    $this->init($config, '/o/' . rawurlencode($config->orgName) . '/apimodels');
  }

  /**
   * Creates a model with the given payload.
   *
   * {@inheritDoc}
   */
  public function createModel($payload = array())
  {
    $this->post(null, $payload);
    return $this->responseObj;
  }

  /**
   * Gets all of the models for the given org
   *
   * {@inheritDoc}
   */
  public function getModels()
  {
    $this->get();
    return $this->responseObj;
  }

  /**
   * Imports a WADL to a given model, returns JSON representation of the model
   *
   * {@inheritDoc}
   */
  public function importWADL($apiId, $xml)
  {
    $this->post(rawurlencode($apiId) . '/revisions?action=import&format=wadl', $xml, 'application/xml; charset=utf-8');
    return $this->responseObj;
  }

  /**
   * Gets information from a Swagger URL, and transforms it to a given model, returns JSON representation of the model
   *
   * {@inheritDoc}
   */
  public function importSwagger($apiId, $url)
  {
    $this->post(rawurlencode($apiId) . '/revisions?action=import&format=swagger', 'URL=' . $url, 'application/xml; charset=utf-8');
    return $this->responseObj;
  }

  /**
   * Gets a specific Model
   *
   * {@inheritDoc}
   */
  public function getModel($apiId)
  {
    $this->get(rawurlencode($apiId));
    return $this->responseObj;
  }

  /**
   * Updates a specific model
   *
   * {@inheritDoc}
   */
  public function updateModel($apiId, $update, $headers)
  {
    if (is_null($headers)) {
      $headers = array();
    }
    $this->put(rawurlencode($apiId), $update, 'text/html', 'text/html', $headers);
    return $this->responseObj;
  }

  /**
   * Deletes a specific model
   *
   * {@inheritDoc}
   */
  public function deleteModel($apiId)
  {
    $this->http_delete(rawurlencode($apiId));
    return $this->responseObj;
  }

}