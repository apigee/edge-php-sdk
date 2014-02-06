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
     * @param string $metric
     * @param string $time_start
     * @param string $time_end
     * @param string $time_unit
     * @param string $sort_by
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