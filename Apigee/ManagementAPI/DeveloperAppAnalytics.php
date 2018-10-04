<?php
/**
 * @file
 * Exposes Developer App Analytics data from the Management API.
 *
 * @author djohnson
 */

namespace Apigee\ManagementAPI;

use Apigee\Exceptions\ParameterException;
use Apigee\Exceptions\ResponseException;
use Apigee\Util\OrgConfig;
use Psr\Log\NullLogger;

/**
 * Exposes Developer App Analytics data from the Management API.
 *
 * @author djohnson
 */
class DeveloperAppAnalytics extends Base
{
    /**
     * @var string
     *    The environment of the app, such as 'test'or 'prod'.
     */
    protected $environment;

    /**
     * Initializes the environment and sets up the OrgConfig object.
     *
     * @param OrgConfig $config
     * @param string $env
     *    The environment, such as 'test'or 'prod'. An asterisk wildcard means
     *    all environments.
     */
    public function __construct(OrgConfig $config, $env = '*')
    {
        $this->init($config, '');
        $this->setEnvironment($env);
    }

    /**
     * Sets the environment.
     *
     * We avoid validating the environment here because it is rather expensive.
     * If validation is needed, call isEnvironmentValid() after setting the
     * environment here.
     *
     * @param string
     *    The environment, such as 'test'or 'prod'. An asterisk wildcard means
     *    all environments. NOTE: API endpoint's handling of asterisks is
     *    somewhat bug-prone; use of asterisks is highly discouraged.
     */
    public function setEnvironment($env)
    {
        $this->environment = $env;
        $envUrl = '/o/' . rawurlencode($this->config->orgName) . '/environments/' . rawurlencode($env) . '/stats/';
        $this->setBaseUrl($envUrl);
    }

    /**
     * Determines whether the current configured environment is valid.
     *
     * @return bool
     */
    public function isEnvironmentValid()
    {
        if ($this->environment == '*') {
            // Very reluctantly. Asterisk usage is bug-prone.
            return true;
        }
        $tempUrl = '/o/' . rawurlencode($this->config->orgName) . '/environments/' . rawurlencode($this->environment);
        $tempConfig = clone $this->config;
        // Disable logging and all subscribers for this validation attempt.
        $tempConfig->logger = new NullLogger();
        $tempConfig->subscribers = array();
        $cachedConfig = $this->config;
        $this->config = $tempConfig;
        // Default to invalid; a GET must succeed for it to be valid.
        $valid = false;
        try {
            $this->get($tempUrl);
            $valid = true;
        } catch (ResponseException $e) {
            // Do nothing; this environment is invalid.
        }
        $this->config = $cachedConfig;
        return $valid;
    }

    /**
     * Returns the current environment.
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Alias of DeveloperAppAnalytics::queryEnvironments().
     *
     * We used to use a caching class here, but it was non-portable.
     *
     * @return string[]
     */
    public function getAllEnvironments()
    {
        return $this->queryEnvironments();
    }

    /**
     * After ensuring params are valid, fetches analytics data.
     *
     * @param string $devEmailOrCompany
     *    The email of the developer or company name that owns the app. If you
     *    do not pass in this parameter you will get analytics for any app
     *    in the org with this app name, since app name is not unique across
     *    developers.
     * @param string $appName
     *    The name of the app.
     * @param string $metric
     *    A value of 'message_count', 'message_count-first24hrs',
     *    'message_count-second24hrs', 'error_count', 'error_count-first24hrs',
     *    'total_response_time', 'max_response_time', or 'min_response_time'.
     * @param string $tStart
     *    Time start, expressed as:
     *    <ul>
     *      <li>UNIX timestamp</li>
     *      <li>mm/dd/YYYY hh:ii</li>
     *      <li>Any other format that the underlying strtotime() PHP function
     *        can parse. See {@link http://php.net/strtotime}.
     *        It parses them out to a UNIX timestamp if possible, otherwise
     *        it throws an exception.</li>
     *    </ul>
     * @param string $tEnd
     *    Time end, expressed as:
     *    <ul>
     *      <li>UNIX timestamp</li>
     *      <li>mm/dd/YYYY hh:ii</li>
     *      <li>Any other format that the underlying strtotime() PHP function
     *        can parse. See {@link http://php.net/strtotime}.
     *        It parses them out to a UNIX timestamp if possible, otherwise
     *        it throws an exception.</li>
     *    </ul>
     * @param string $tUnit
     *    Time unit: a value of 'second', 'minute', 'hour', 'day', 'week',
     *    'month', 'quarter', or 'year'.
     * @param string $sortBy
     *    A comma separated list of the same values as $metric.
     * @param string $sortOrder
     *    Either 'ASC' or 'DESC'.
     * @param bool $is_company
     *    If TRUE get app for company, otherwise for developer.
     *
     * @return array
     *   An array of analytic data points.
     *
     * @throws \Apigee\Exceptions\ParameterException
     *   Thrown in case of an invalid parameter.
     */
    public function getByAppName($devEmailOrCompany, $appName, $metric, $tStart, $tEnd, $tUnit, $sortBy, $sortOrder = 'ASC', $is_company = FALSE)
    {
        $params = self::validateParameters($metric, $tStart, $tEnd, $tUnit, $sortBy, $sortOrder);

        // We need to filter analytics by the developer or company name. If we do not
        // we will get back data for all apps with the app name, since they are not
        // unique.  For example, two developers can make an app named "test".
        if (!empty($devEmailOrCompany)) {
            $filter_property = $is_company ? 'developer' : 'developer_email';
            $params['filter'] = "({$filter_property} eq '{$devEmailOrCompany}')";
        }

        $params['developer_app'] = $appName;

        $url = 'apps?';
        $first = true;
        foreach ($params as $name => $val) {
            if ($first) {
                $first = false;
            } else {
                $url .= '&';
            }
            $url .= $name . '=' . urlencode($val);
        }
        $this->get($url);
        $response = $this->responseObj['Response'];

        $datapoints = array();
        $timestamps = array();
        foreach ($response['TimeUnit'] as $timestamp) {
            $timestamps[] = floor($timestamp / 1000);
        }
        if (array_key_exists('stats', $response) && array_key_exists('data', $response['stats'])) {
            foreach ($response['stats']['data'] as $responseItem) {
                $itemCaption = '';
                foreach ($responseItem['identifier']['names'] as $key => $value) {
                    if ($value == 'developer_app') {
                        $itemCaption = $responseItem['identifier']['values'][$key];
                        break;
                    }
                }
                foreach ($responseItem['metric'] as $array) {
                    $env = $array['env'];
                    $i = 0;
                    foreach ($array['values'] as $metricValue) {
                        $datapoints[$itemCaption][$env][$timestamps[$i++]] = $metricValue;
                    }
                }
            }
        }
        return $datapoints;
    }

    /**
     * Queries Edge to get a list of all environments configured for the org.
     *
     * @return string[]
     */
    public function queryEnvironments()
    {
        $env_url = '/o/' . rawurlencode($this->config->orgName) . '/environments';
        $this->setBaseUrl($env_url);
        $this->get();
        $this->restoreBaseUrl();
        return $this->responseObj;
    }

    /**
     * Lists all metrics valid for Developer Apps.
     *
     * @static
     * @return string[]
     */
    public static function getMetrics()
    {
        return array(
            'message_count' => 'Message Count',
            'message_count-first24hrs' => 'Message Count - First 24 Hours',
            'message_count-second24hrs' => 'Message Count - Second 24 Hours',
            'error_count' => 'Error Count',
            'error_count-first24hrs' => 'Error Count - First 24 Hours',
            'total_response_time' => 'Total Response Time',
            'max_response_time' => 'Maximum Response Time',
            'min_response_time' => 'Minimum Response Time'
        );
    }

    /**
     * Returns a keyed array of allowable time units. Array keys are machine
     * names and values are human-readable names.
     *
     * @return string[]
     */
    public static function getTimeUnits()
    {
        return array(
            'second' => 'Second',
            'minute' => 'Minute',
            'hour' => 'Hour',
            'day' => 'Day',
            'week' => 'Week',
            'month' => 'Month',
            'quarter' => 'Quarter',
            'year' => 'Year',
        );
    }

    /**
     * Validates common parameters for analytics API calls, and bundles them
     * into an appropriately-structured array to be passed into an HTTP call.
     *
     * @static
     * @param string $metric
     * @param string $timeStart
     * @param string $timeEnd
     * @param string $timeUnit
     * @param string $sortBy
     * @param string $sortOrder
     *    Either 'ASC' or 'DESC'.
     * @return string[]
     * @throws ParameterException
     */
    protected static function validateParameters($metric, $timeStart, $timeEnd, $timeUnit, $sortBy, $sortOrder)
    {
        $metricItems = preg_split('!\s*,\s*!', $metric);
        if (count($metricItems) == 0) {
            throw new ParameterException('Missing metric.');
        }
        $validMetrics = array_keys(self::getMetrics());
        foreach ($metricItems as $metricItem) {
            if (!in_array($metricItem, $validMetrics)) {
                throw new ParameterException('Invalid metric.');
            }
        }
        $sortByItems = preg_split('!\s*,\s*!', $sortBy);
        if (count($sortByItems) == 0) {
            throw new ParameterException('Missing sort-by metric');
        }
        foreach ($sortByItems as $sortByItem) {
            if (!in_array($sortByItem, $validMetrics)) {
                throw new ParameterException('Invalid sort-by metric.');
            }
        }
        if (!in_array($timeUnit, array_keys(self::getTimeUnits()))) {
            throw new ParameterException('Invalid time unit.');
        }
        $sortOrder = strtoupper($sortOrder);
        if ($sortOrder != 'ASC' && $sortOrder != 'DESC') {
            throw new ParameterException('Invalid sort order.');
        }
        $timeRange = self::parseTime($timeStart) . '~' . self::parseTime($timeEnd);
        $payload = array(
            'timeRange' => $timeRange,
            'timeUnit' => $timeUnit,
            'sortby' => join(',', $sortByItems),
            'sort' => $sortOrder,
            '_optimized' => 'js',
            'select' => join(',', $metricItems),
        );

        return $payload;
    }

    /**
     * Parses an incoming time string or Unix timestamp into an internally-acceptable time format.
     *
     * If the time input cannot be parsed, an exception is thrown.
     *
     * @static
     * @param string|int $time
     * @return string
     * @throws ParameterException
     */
    protected static function parseTime($time)
    {
        $intTime = false;
        if (is_int($time)) {
            $intTime = $time;
        }
        if (preg_match('!^([0-9]{1,2})/([0-9]{1,2})/([0-9]{4}) ([0-9]{2}):([0-9]{2})$!', $time, $matches)) {
            list (, $m, $d, $y, $h, $i) = $matches;
            if ($m >= 0 && $m <= 12 && $d >= 1 && $d <= 31 && $h >= 0 && $h <= 12 && $i >= 0 && $i <= 59) {
                $intTime = mktime($h, $i, 0, $m, $d, $y);
            }
        }
        if ($intTime === false) {
            $intTime = @strtotime($time);
        }
        if ($intTime === false) {
            throw new ParameterException('Invalid time format.');
        }
        return date('m/d/Y H:i', $intTime);
    }
}
