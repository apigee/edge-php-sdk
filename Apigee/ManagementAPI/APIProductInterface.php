<?php
namespace Apigee\ManagementAPI;

interface APIProductInterface {
  public function load($name = NULL, $response = NULL);
  public function save();
  public function delete($name = NULL);
  public function listProducts();

  public function getAttributes();
  public function getAttribute($name);
  public function setAttribute($name, $value);
  public function clearAttributes();

  public function getCreatedAt();
  public function getCreatedBy();
  public function getModifiedAt();
  public function getModifiedBy();
  public function getEnvironments();
  public function getName();
  public function getProxies();
  public function getQuotaLimit();
  public function getQuotaInterval();
  public function getQuotaTimeUnit();
  public function getDisplayName();
  public function getDescription();

  public function addApiResource($resource);
  public function removeApiResource($resource);
  public function getApiResources();

  public function getApprovalType();
  public function setApprovalType($type);
}