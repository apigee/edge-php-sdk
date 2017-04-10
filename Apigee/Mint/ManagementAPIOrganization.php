<?php

namespace Apigee\Mint;

use Apigee\Util\CacheFactory;
use Apigee\ManagementAPI\Organization as EdgeOrganization;

/**
 * ManagementAPIOrganization is used in monetization instead of
 * Organization so we can cache its response since it is somehow
 * more heavily used in this module
 */
class ManagementAPIOrganization extends EdgeOrganization
{
    public function load($org = null)
    {

        $org = isset($org) ? $org : $this->name;
        $cache_manager = CacheFactory::getCacheManager();
        $organization = $cache_manager->get('mngmt_organization:' . $org, null);
        if (!isset($organization)) {
            $url = rawurlencode($org);
            $this->get($url);
            $organization = $this->responseObj;
            $cache_manager->set('mngmt_organization:' . $org, $organization);
        }
        $this->name = $organization['name'];
        $this->displayName = $organization['displayName'];
        $this->environments = $organization['environments'];
        $this->type = $organization['type'];
        $this->createdAt = $organization['createdAt'];
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
