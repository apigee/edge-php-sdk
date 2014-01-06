<?php
namespace Apigee\DocGen;

interface DocGenModelInterface {
  public function getModels();
  public function createModel($payload = array());
  public function importWADL($apiId, $xml);
  public function getModel($apiId);
  public function updateModel($apiId, $update);
  public function deleteModel($apiId);
}