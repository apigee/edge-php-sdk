<?php
namespace Apigee\ManagementAPI;

interface OrganizationInterface {
  public function load($org = NULL);
  public function getName();
  public function getDisplayName();
  public function getEnvironments();
  public function getProperties();
  public function getProperty($name);
  public function getType();
  public function getCreatedAt();
  public function getCreatedBy();
  public function getLastModifiedAt();
  public function getLastModifiedBy();
}