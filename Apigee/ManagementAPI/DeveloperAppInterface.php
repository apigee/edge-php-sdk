<?php

namespace Apigee\ManagementAPI;

interface DeveloperAppInterface {


  public function load($name = NULL);
  public function validate($name = NULL);
  public function save($force_update = FALSE);
  public function setKeyStatus($status, $also_set_apiproduct = TRUE);
  public function delete($name = NULL);
  public function getList();
  public function getListDetail($developer_mail = NULL);
  public function createKey($consumer_key, $consumer_secret);
  public function deleteKey($consumer_key);
  public function listAllOrgApps();
  public function blankValues();

  public function getApiProducts();
  public function setApiProducts($products);

  public function getAttributes();
  public function hasAttribute($attr);
  public function getAttribute($attr);
  public function setAttribute($attr, $value);
  public function setName($name);
  public function getName();
  public function setCallbackUrl($url);
  public function getCallbackUrl();
  public function setDescription($descr);
  public function getDescription();
  public function setAccessType($type);
  public function getAccessType();
  public function getStatus();
  public function getDeveloperId();
  public function getDeveloperMail();
  public function getCredentialApiProducts();
  public function getConsumerKey();
  public function getConsumerSecret();
  public function getCredentialScopes();
  public function getCredentialStatus();
  public function getCreatedAt();
  public function getCreatedBy();
  public function getModifiedAt();
  public function getModifiedBy();
  public function getCredentialAttribute($attr_name);
  public function setCredentialAttribute($name, $value);
  public function getCredentialAttributes();
  public function clearCredentialAttributes();
  public function getAppId();
  public function getAppFamily();
  public function setAppFamily($family);
  public function getScopes();
  public function hasCredentialInfo();
}