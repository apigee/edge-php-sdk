<?php
/**
 * @file
 * Exposes Developer App Analytics data from the Management API.
 *
 * @author djohnson
 */

namespace Apigee\ManagementAPI;

/**
 * Exposes Developer App Analytics data from the Management API.
 *
 * @author djohnson
 */
class DeveloperAppAnalytics extends Analytics
{
    /**
     * After ensuring params are valid, fetches analytics data.
     *
     * @param string $devId
     *    The ID of the developer who owns the app.
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
     * @return array
     */
    public function getByAppName($devId, $appName, $metric, $tStart, $tEnd, $tUnit, $sortBy, $sortOrder = 'ASC')
    {
        $params = self::validateParameters($metric, $tStart, $tEnd, $tUnit, $sortBy, $sortOrder);

        if (!empty($devId)) {
            $org = $this->config->orgName;
            $params['filter'] = "(developer eq '$org@@@$devId')";
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
}
