<?php

namespace Apigee\ManagementAPI;

use Apigee\Exceptions\ResponseException;
use Apigee\Exceptions\ParameterException;
use Apigee\Util\OrgConfig;
use Psr\Log\NullLogger;

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
    public function __construct(OrgConfig $config, $developer)
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
    public function getListDetail($developerMail = null)
    {
        $allApps = array();
        $developerMail = $developerMail ? : $this->developer;
        $newBaseUrl = '/o/'
            . rawurlencode($this->config->orgName)
            . '/developers/'
            . rawurlencode($developerMail)
            . '/apps';
        $this->setBaseUrl($newBaseUrl);

        // Per-developer app listing paging is not enabled at this time.
        $this->get('?expand=true');
        $list = $this->responseObj;
        if (array_key_exists('app', $list)) {
            foreach ($list['app'] as $response) {
                $app = new DeveloperApp($this->getConfig(), $developerMail);
                self::loadFromResponse($app, $response, $developerMail);
                $allApps[] = $app;
            }
        }

        $this->restoreBaseUrl();
        return $allApps;
    }

    /**
     * Lists all apps within the org. Each member of the returned array is a
     * fully-populated DeveloperApp/CompanyApp object.
     *
     * @return AbstractApp[]
     */
    public function listAllApps()
    {
        $url = '/o/' . rawurlencode($this->config->orgName) . '/apps';
        $this->setBaseUrl($url);
        $appList = array();
        if ($this->pagingEnabled) {
            $lastKey = null;
            while (true) {
                $queryString = '?expand=true&rows=' . $this->pageSize;
                if (isset($lastKey)) {
                    $queryString .= '&lastKey=' . urlencode($lastKey);
                }
                $this->get($queryString);
                $appSubset = $this->responseObj;
                if (!array_key_exists('app', $appSubset)) {
                    break;
                }
                $subsetCount = count($appSubset['app']);
                if ($subsetCount == 0) {
                    break;
                }
                if (isset($lastKey)) {
                    // Avoid duplicating the last key, which is the first key
                    // on this page.
                    array_shift($appSubset['app']);
                }
                foreach ($appSubset['app'] as $appDetail) {
                    if (array_key_exists('developerId', $appDetail)) {
                        $ownerId = $this->getDeveloperMailById($appDetail['developerId']);
                        if (!isset($ownerId)) {
                            // Anomalous condition: app exists but owner is deleted.
                            // This occurs rarely.
                            $warning = 'Attempted to load an app owned by nonexistent Developer %s for App %s (%s)';
                            $warningArgs = array($appDetail['developerId'], $appDetail['appId'], $appDetail['name']);
                            self::$logger->warning(vsprintf($warning, $warningArgs));
                            continue;
                        }
                        $app = new self($this->config, $ownerId);
                    } else {
                        $ownerId = $appDetail['companyName'];
                        $app = new CompanyApp($this->config, $ownerId);
                    }
                    self::loadFromResponse($app, $appDetail, $ownerId);
                    $appList[] = $app;
                }
                if ($subsetCount == $this->pageSize) {
                    $lastApp = end($appSubset['app']);
                    $lastKey = $lastApp['appId'];
                } else {
                    break;
                }
            }
        } else {
            $this->get('?expand=true');
            $response = $this->responseObj;
            $this->restoreBaseUrl();
            foreach ($response['app'] as $appDetail) {
                if (array_key_exists('developerId', $appDetail)) {
                    $ownerId = $this->getDeveloperMailById($appDetail['developerId']);
                    if (!isset($ownerId)) {
                        // Anomalous condition: app exists but owner is deleted.
                        // This occurs rarely.
                        $warning = 'Attempted to load an app owned by nonexistent Developer %s for App %s (%s)';
                        $warningArgs = array($appDetail['developerId'], $appDetail['appId'], $appDetail['name']);
                        self::$logger->warning(vsprintf($warning, $warningArgs));
                        continue;
                    }
                    $app = new self($this->config, $ownerId);
                } else {
                    $ownerId = $appDetail['companyName'];
                    $app = new CompanyApp($this->config, $ownerId);
                }
                self::loadFromResponse($app, $appDetail, $ownerId);
                $appList[] = $app;
            }
        }
        return $appList;
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
     * @param bool $resetDeveloper
     * @return \Apigee\ManagementAPI\AbstractApp
     *
     * @throws \Apigee\Exceptions\ParameterException
     */
    public function loadByAppId($appId, $resetDeveloper = false)
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
            $ownerId = $this->getDeveloperMailById($response['developerId']);
            $obj =& $this;
            $resetEligible = true;
        } else {
            $ownerId = $response['companyName'];
            $obj = new CompanyApp($this->getConfig(), $ownerId);
            $resetEligible = false;
        }

        self::loadFromResponse($obj, $response, $ownerId);
        // Must load developer to get email
        if ($resetDeveloper && $resetEligible) {
            $resetBaseUrl = '/o/'
                . rawurlencode($this->config->orgName)
                . '/developers/'
                . rawurlencode($ownerId)
                . '/apps';
            $this->setBaseUrl($resetBaseUrl);
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
            $config->logger = new NullLogger();
            $config->subscribers = array();
            $dev = new Developer($config);
            try {
                $dev->load($id);
                $devs[$id] = $dev->getEmail();
            } catch (ResponseException $e) {
                $devs[$id] = null;
                // Log exceptions that are NOT 404s.
                if ($e->getCode() != 404) {
                    $warning = 'Attempt to load dev “%s” resulted in response code of %d.';
                    $warningArgs = array($id, $e->getCode());
                    $cached_logger->warning(vsprintf($warning, $warningArgs));
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
     * {@inheritdoc}
     */
    public static function afterLoad(AbstractApp &$obj, array $response, $owner_identifier)
    {
        if ($obj instanceof DeveloperApp) {
            $obj->developerId = $response['developerId'];
            $obj->developer = $obj->getDeveloperMailById($response['developerId']);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function alterAttributes(array &$payload)
    {
        if (!$this->pagingEnabled || count($this->attributes) < self::MAX_ATTRIBUTE_COUNT) {
            $this->attributes['Developer'] = $this->developer;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAppProperties($class = __CLASS__)
    {
        $properties = parent::getAppProperties(__CLASS__);
        $properties[] = 'developerId';
        $properties[] = 'developer';
        return $properties;
    }
}
