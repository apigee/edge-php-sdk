<?php
namespace Apigee\ManagementAPI;

/**
 * The interface that must beimplementd by DeveloperAppAnalytics objects, 
 which exposes data from the Management API.
 *
 * @author djohnson
 */
 interface DeveloperAppAnalyticsInterface
{
    /**
     * Validates and sets the environment.
     *
     * @param string The environment, such as 'test'or 'prod'.
     * A value of the asterisk, &#42;, wildcard means all environments.
     * @throws \Apigee\Exceptions\EnvironmentException
     */
    public function setEnvironment($env);

    /**
     * Returns the current environment.
     *
     * @return string
     */
    public function getEnvironment();

    /**
     * Returns a list of all environments for this org.
     *
     * @return array
     */
    public function getAllEnvironments();

    /**
     * After ensuring params are valid, fetches analytics data.
     *
     * @param string $developer_id The ID of the developer who owns the app.
     * @param string $app_name The name of the app.
     * @param string $metric A value of 'message_count', 
     * 'message_count-first24hrs', 'message_count-second24hrs', 'error_count', 
     * 'error_count-first24hrs', 'user_count', 'user_count-first24hrs', 
     * 'total_response_time', 'max_response_time', 'min_response_time', or 
     * 'end_point_response_time'.
     * @param string $time_start Expressed as:
     * <ul>
     *   <li>UNIX timestamp</li>
     *   <li>mm/dd/YYYY hh:ii</li>
     *   <li>Any other format that the underlying strtotime() PHP function 
     *     can parse. See {@link http://php.net/strtotime}.
     *     It parses them out to a UNIX timestamp if possible, otherwise 
     *     it throws an exception.</li>
     * </ul>
     * @param string $time_end Expressed as:
     * <ul>
     *   <li>UNIX timestamp</li>
     *   <li>mm/dd/YYYY hh:ii</li>
     *   <li>Any other format that the underlying strtotime() PHP function 
     *     can parse. See {@link http://php.net/strtotime}.
     *     It parses them out to a UNIX timestamp if possible, otherwise 
     *     it throws an exception.</li>
     * </ul>
     * @param string $time_unit A value of 'second', 'minute', 'hour', 'day', 
     * 'week', 'month', 'quarter', or 'year'.
     * @param string $sort_by A comma separated list of the same values 
     * as $metric.
     * @param string $sort_order Either 'ASC' or 'DESC'.
     * @return array
     */
    public function getByAppName($developer_id, $app_name, $metric, $time_start, $time_end, $time_unit, $sort_by, $sort_order = 'ASC');

    /**
     * Queries Management API to get a list of all environments configured for the org.
     *
     * @return array
     */
    public function queryEnvironments();

    /**
     * Lists all metrics valid for Developer Apps.
     *
     * @static
     * @return array
     */
    public static function getMetrics();

    /**
     * Returns a keyed array of allowable time units. Array keys are machine names
     * and values are human-readable names.
     *
     * @return array
     */
    public static function getTimeUnits();
}