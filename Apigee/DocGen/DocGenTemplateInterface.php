<?php
namespace Apigee\DocGen;

interface DocGenTemplateInterface {
  /**
   * Gets the inndex template.
   *
   * @param $apiId
   * @return array|string
   */
  public function getIndexTemplate($apiId);

  /**
   * Gets the operation HTML.
   *
   * @param $apiId
   * @return array|string
   */
  public function getOperationTemplate($apiId);

  /**
   * Saves the template back to the modeling API.
   */
  public function saveTemplate($apiId, $type, $html);
}