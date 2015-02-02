<?php

namespace Apigee\ManagementAPI;

use Apigee\Exceptions\ResponseException;
use Apigee\Exceptions\ParameterException as ParameterException;

/**
 * Abstracts the Developer App object in the Management API and allows clients
 * to manipulate it.
 *
 * @author djohnson
 */
class DeveloperApp extends AbstractApp
{
    /**
     * @var string
     * The developer_id attribute of the developer who
     * owns this app.
     * This property is read-only.
     */
    protected $developerId;

    /**
     * @var string
     * The email address of the developer who created the app.
     */
    protected $developer;

    /* Accessors (getters/setters) */

    /**
     * {@inheritDoc}
     */

    public function getDeveloperId()
    {
        return $this->developerId;
    }

    /**
     * {@inheritDoc}
     */
    public function getDeveloperMail()
    {
        return $this->developer;
    }

    /**
     * Initializes this object
     *
     * @param \Apigee\Util\OrgConfig $config
     * @param mixed $developer
     */
    public function __construct(\Apigee\Util\OrgConfig $config, $developer)
    {
        $this->ownerIdentifierField = 'developerId';
        if ($developer instanceof Developer) {
            $this->developer = $developer->getEmail();
        } else {
            // $developer may be either an email or a developerId.
            $this->developer = $developer;
        }
        $baseUrl = '/o/' . rawurlencode($config->orgName) . '/developers/' . $this->developer . '/apps';
        $this->init($config, $baseUrl);
        $this->blankValues();
    }

    /**
     * {@inheritDoc}
     */
    public function getListDetail($developer_mail = null)
    {
        $developer_mail = $developer_mail ? : $this->developer;

        $this->setBaseUrl('/o/' . rawurlencode($this->config->orgName) . '/developers/' . rawurlencode($developer_mail) . '/apps');

        $this->get('?expand=true');
        $list = $this->responseObj;
        $this->restoreBaseUrl();

        $app_list = array();
        if (!array_key_exists('app', $list) || empty($list['app'])) {
            return $app_list;
        }
        foreach ($list['app'] as $response) {
            $app = new DeveloperApp($this->getConfig(), $developer_mail);
            self::loadFromResponse($app, $response, $developer_mail);
            $app_list[] = $app;
        }
        return $app_list;
    }

    /**
     * Alias for listAllApps().
     *
     * @deprecated
     * @return array
     */
    public function listAllOrgApps()
    {
        return $this->listAllApps();
    }

    /**
     * Lists all apps within the org or company. Each member of the returned
     * array is a fully-populated DeveloperApp/CompanyApp object.
     *
     * @return array
     */
    public function listAllApps()
    {
        $url = '/o/' . rawurlencode($this->config->orgName);
        $this->setBaseUrl($url);
        $this->get('apps?expand=true');
        $response = $this->responseObj;
        $this->restoreBaseUrl();
        $app_list = array();
        foreach ($response['app'] as $app_detail) {
            if (array_key_exists('developerId', $app_detail)) {
                $owner_id = $this->getDeveloperMailById($app_detail['developerId']);
                if (!isset($owner_id)) {
                    // Anomalous condition: app exists but owner is deleted.
                    // This occurs rarely.
                    self::$logger->warning('Attempted to load an app owned by nonexistent Developer ' . $app_detail['developerId'] . ' for App ' . $app_detail['appId'] . ' (' . $app_detail['name'] . ')');
                    continue;
                }
                $app = new self($this->config, $owner_id);
            } else {
                $owner_id = $app_detail['companyName'];
                $app = new CompanyApp($this->config, $owner_id);
            }
            self::loadFromResponse($app, $app_detail, $owner_id);
            $app_list[] = $app;
        }
        return $app_list;
    }

    /**
     * Loads a DeveloperApp/CompanyApp, given its appId (which is a UUID).
     *
     * Normally you'd find an app by listing its owner entity's apps and looking
     * for the name you want. However, if you already know the app's unique id,
     * you can load without knowing its owner.
     *
     * If you pass true as the second parameter here, the DeveloperApp/CompanyApp
     * object will be changed so that it pulls apps from this developer/company
     * by default.
     *
     * @param string $appId
     * @param bool $reset_developer
     * @return \Apigee\ManagementAPI\AbstractApp
     *
     * @throws \Apigee\Exceptions\ParameterException
     */
    public function loadByAppId($appId, $reset_developer = false)
    {
        if (!preg_match('!^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$!', $appId)) {
            throw new ParameterException('Invalid UUID “' . $appId . '” passed as appId.');
        }

        $url = '/o/' . rawurlencode($this->config->orgName) . '/apps';
        $this->setBaseUrl($url);
        $this->get($appId);
        $this->restoreBaseUrl();
        $response = $this->responseObj;
        if (array_key_exists('developerId', $response)) {
            $owner_id = $this->getDeveloperMailById($response['developerId']);
            $obj =& $this;
            $reset_eligible = true;
        } else {
            $owner_id = $response['companyName'];
            $obj = new CompanyApp($this->getConfig(), $owner_id);
            $reset_eligible = false;
        }

        self::loadFromResponse($obj, $response, $owner_id);
        // Must load developer to get email
        if ($reset_developer && $reset_eligible) {
            $this->setBaseUrl('/o/' . rawurlencode($this->config->orgName) . '/developers/' . rawurlencode($owner_id) . '/apps');
        }
        return $obj;
    }

    /**
     * Attempts to fetch the email address associated with a developerId.
     *
     * If no such developer exists, null is returned. We turn off all logging,
     * both by the main logger and by any subscribers. (The exception is that
     * non-404 ResponseExceptions are logged.) It is therefore the
     * responsibility of any client of this method to handle appropriate
     * logging.
     *
     * @param string $id
     *   The developerId of the developer in question
     *
     * @return string|null
     *   The email address of the developer, or null if no such developer
     *   exists.
     */
    private function getDeveloperMailById($id)
    {
        static $devs = array();
        if (!isset($devs[$id])) {
            $cached_logger = self::$logger;
            $config = clone $this->config;
            // Suppress (almost) all logging.
            $config->logger = new \Psr\Log\NullLogger();
            $config->subscribers = array();
            $dev = new Developer($config);
            try {
                $dev->load($id);
                $devs[$id] = $dev->getEmail();
            } catch (ResponseException $e) {
                $devs[$id] = null;
                // Log exceptions that are NOT 404s.
                if ($e->getCode() != 404) {
                    $cached_logger->warning('Attempting to load dev “' . $id . '” resulted in a response code of ' . $e->getCode() . '.');
                }
            }
            self::$logger = $cached_logger;
        }
        return $devs[$id];
    }

    /**
     * {@inheritDoc}
     */
    public function blankValues()
    {
        $this->developerId = null;
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
        $obj->developerId = $response['developerId'];
        $obj->developer = $obj->getDeveloperMailById($response['developerId']);
    }

    protected function alterAttributes(array &$payload)
    {
        $this->attributes['Developer'] = $this->developer;
    }

    public function getAppProperties($class = __CLASS__)
    {
        $properties = parent::getAppProperties(__CLASS__);
        $properties[] = 'developerId';
        $properties[] = 'developer';
        return $properties;
    }
}
