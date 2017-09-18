<?php
/**
 * @file
 * Exposes Proxy Analytics data from the Management API.
 *
 * @author boobaa
 */

namespace Apigee\ManagementAPI;

/**
 * Exposes Proxy Analytics data from the Management API.
 *
 * @author boobaa
 */
class ProxyAnalytics extends Analytics
{
    /**
     * After ensuring params are valid, fetches analytics data.
     *
     * @param string $metric
     *    A value of 'sum(message_count)', 'sum(is_error)',
     *    'avg(target_response_time)', 'avg(total_response_time)',
     *    'max(total_response_time)'.
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
     * @param int $limit
     *    Defaults to 14400.
     * @return array
     */
    public function getProxyAnalytics($metric, $tStart, $tEnd, $tUnit, $sortBy, $sortOrder = 'ASC', $limit = 14400)
    {
        $params = static::validateParameters($metric, $tStart, $tEnd, $tUnit, $sortBy, $sortOrder);
        $params['limit'] = $limit;

        $url = 'apiproxy?';
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
                    if ($value == 'apiproxy') {
                        $itemCaption = $responseItem['identifier']['values'][$key];
                        break;
                    }
                }
                foreach ($responseItem['metric'] as $array) {
                    $env = $array['env'];
                    $name = $array['name'];
                    $i = 0;
                    foreach ($array['values'] as $metricValue) {
                        $datapoints[$itemCaption][$name][$env][$timestamps[$i++]] = $metricValue;
                    }
                }
            }
        }
        return $datapoints;
    }

    /**
     * Lists all valid metrics.
     *
     * @static
     * @return string[]
     */
    public static function getMetrics()
    {
        return array(
            'sum(message_count)' => 'Message Count',
            'sum(is_error)' => 'Sum of Errors',
            'avg(target_response_time)' => 'Average Target Response Time',
            'avg(total_response_time)' => 'Average Total Response Time',
            'max(total_response_time)' => 'Maximum Total Response Time',
        );
    }
}
