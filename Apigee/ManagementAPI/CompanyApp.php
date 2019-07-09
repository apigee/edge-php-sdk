<?php

namespace Apigee\ManagementAPI;

use Apigee\Util\OrgConfig;

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
     * @param mixed $company
     */
    public function __construct(OrgConfig $config, $company)
    {
        $this->blankValues();
        $this->ownerIdentifierField = 'companyName';
        if ($company instanceof Company) {
            $this->companyName = $company->getName();
        } else {
            // $developer may be either an email or a developerId.
            $this->companyName = $company;
        }
        $baseUrl = '/o/' . rawurlencode($config->orgName) . '/companies/' . $this->companyName . '/apps';
        $this->init($config, $baseUrl);
    }

    /**
     * {@inheritDoc}
     */
    public function getListDetail($companyName = null)
    {
        $allApps = array();
        $companyName = $companyName ? : $this->companyName;

        // Per-company app listing paging is not enabled at this time.
        $this->get('?expand=true');
        $list = $this->responseObj;
        if (array_key_exists('app', $list)) {
            foreach ($list['app'] as $response) {
                $app = new CompanyApp($this->getConfig(), $companyName);
                self::loadFromResponse($app, $response, $companyName);
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
     * {@inheritdoc}
     */
    public static function afterLoad(AbstractApp &$obj, array $response, $ownerIdentifier)
    {
        $obj->companyName = $response['companyName'];
    }

    /**
     * {@inheritdoc}
     */
    protected function alterAttributes(array &$payload)
    {
        if (!$this->pagingEnabled || count($this->attributes) < self::MAX_ATTRIBUTE_COUNT) {
            $this->attributes['Company'] = $this->companyName;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAppProperties($class = __CLASS__)
    {
        $properties = parent::getAppProperties(__CLASS__);
        $properties[] = 'companyName';
        return $properties;
    }
}
