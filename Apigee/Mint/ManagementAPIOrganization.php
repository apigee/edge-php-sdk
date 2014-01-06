<?php

namespace Apigee\Mint;

use Apigee\Util\CacheFactory;

use Apigee\Util\Cache as Cache;

/**
 * ManagementAPIOrganization is used in monetization instead of
 * Organization so we can cache its response since it is somehow
 * more heavily used in this module
 */
class ManagementAPIOrganization extends \Apigee\ManagementAPI\Organization {
  public function load($org = NULL) {

    $org = isset($org) ? $org : $this->name;
    $cache_manager = CacheFactory::getCacheManager(NULL);
    $organization = $cache_manager->get('mngmt_organization:' . $org, NULL);
    if (!isset($organization)) {
      $url = $this->urlEncode($org);
      $this->get($url);
      $organization = $this->responseObj;
      $cache_manager->set('mngmt_organization:' . $org, $organization);
    }
    $this->name = $organization['name'];
    $this->displayName = $organization['displayName'];
    $this->environments = $organization['environments'];
    $this->type = $organization['type'];
    $this->createAt = $organization['createdAt'];
    $this->createdBy = $organization['createdBy'];
    $this->lastModifiedAt = $organization['lastModifiedAt'];
    $this->lastModifiedBy = $organization['lastModifiedBy'];
    $this->properties = array();

    if (isset($organization['properties'])) {
      foreach ($organization['properties'] as $prop) {
        list($property) = $prop;
        $this->properties[$property['name']] = $property['value'];
      }
    }
  }
}