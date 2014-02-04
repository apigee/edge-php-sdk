<?php
namespace Apigee\DocGen;

interface DocGenModelInterface {
  /**
   * Returns the descriptions of all APIs in the organization.
   *
   * @return array
   */
  public function getModels();

  /**
   * Adds an API resource, with a name and a description.
   * The actual API description is added using a different set of methods
   * that are described in the following sections.
   *
   * @param array $payload
   * @return array
   */
  public function createModel($payload = array());

  /**
   * Imports the given API description to apihub repository.
   *
   * @param string $apiId
   * @param string $xml
   * @return array
   */
  public function importWADL($apiId, $xml);

  /**
   * Returns the details of an API, such as its name, description, list of revisions and metadata.
   *
   * @param string $apiId
   * @return array
   */
  public function getModel($apiId);

  /**
   * Updates an API resource.
   *
   * @param $apiId
   * @param $update
   * @return array
   */
  public function updateModel($apiId, $update);

  /**
   * Deletes an API resource and all its associated data.
   *
   * @param $apiId
   * @return array
   */
  public function deleteModel($apiId);
}