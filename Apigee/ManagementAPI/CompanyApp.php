<?php

namespace Apigee\ManagementAPI;

use Apigee\Exceptions\ParameterException as ParameterException;

/**
 * Abstracts the Developer App object in the Management API and allows clients
 * to manipulate it.
 *
 * @author djohnson
 */
class CompanyApp extends AbstractApp
{
    /**
     * @var string
     * The developer_id attribute of the developer who
     * owns this app.
     * This property is read-only.
     */
    protected $companyName;

    /* Accessors (getters/setters) */

    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * Initializes this object
     *
     * @param \Apigee\Util\OrgConfig $config
     * @param mixed $developer
     */
    public function __construct(\Apigee\Util\OrgConfig $config, $company)
    {
        $this->ownerIdentifierField = 'companyName';
        if ($company instanceof Company) {
            $this->companyName = $company->getName();
        } else {
            // $developer may be either an email or a developerId.
            $this->companyName = $company;
        }
        $baseUrl = '/o/' . rawurlencode($config->orgName) . '/companies/' . $this->companyName . '/apps';
        $this->init($config, $baseUrl);
        $this->blankValues();
    }

    /**
     * {@inheritDoc}
     */
    public function getListDetail($company_name = null)
    {
        $allApps = array();
        $company_name = $company_name ? : $this->companyName;

        // Per-company app listing paging is not enabled at this time.
        $this->get('?expand=true');
        $list = $this->responseObj;
        if (array_key_exists('app', $list)) {
            foreach ($list['app'] as $response) {
                $app = new CompanyApp($this->getConfig(), $company_name);
                self::loadFromResponse($app, $response, $company_name);
                $allApps[] = $app;
            }
        }

        return $allApps;
    }

    /**
     * {@inheritDoc}
     */
    public function blankValues()
    {
        $this->companyName = '';
        parent::blankValues();
    }

    /**
     * Set properties specific to DeveloperApps right after they are loaded.
     *
     * @param AbstractApp $obj
     * @param array $response
     */
    public static function afterLoad(AbstractApp &$obj, array $response, $owner_identifier)
    {
        $obj->companyName = $response['companyName'];
    }

    protected function alterAttributes(array &$payload)
    {
        if (!$this->pagingEnabled || count($this->attributes) < self::MAX_ATTRIBUTE_COUNT) {
            $this->attributes['Company'] = $this->companyName;
        }
    }

    public function getAppProperties($class = __CLASS__)
    {
        $properties = parent::getAppProperties(__CLASS__);
        $properties[] = 'companyName';
        return $properties;
    }
}
