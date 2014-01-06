<?php
namespace Apigee\DocGen;

interface DocGenTemplateInterface {
  public function getIndexTemplate($apiId);
  public function getOperationTemplate($apiId);
  public function saveTemplate($apiId, $type, $html);
}