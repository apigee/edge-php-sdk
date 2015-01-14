<?php

namespace Apigee\Util;

/**
 * Class RequestInputReader
 * @package Apigee\Util
 * @deprecated
 */
class RequestInputReader
{

    public static function readRequest()
    {
        static $request;
        if (!isset($request)) {
            $headers = array();
            foreach ($_SERVER as $name => $value) {
              if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
              }
            }
            $request['headers'] = $headers;
            $handle = fopen('php://input', 'r');
            $buffer = null;
            $chunk_size = isset($request['headers']['Content-Length']) ? $request['headers']['Content-Length'] : 1025;
            while (!feof($handle)) {
                $chunk = fread($handle, $chunk_size);
                $buffer = isset($buffer) ? $buffer . $chunk : $chunk;
            }
            fclose($handle);
            $request['data'] = $buffer;
            $request['url'] = $_SERVER['SCRIPT_URL'];
            $request['query.params'] = $_GET;
            $request['query.string'] = $_SERVER['QUERY_STRING'];
            $request['http.method'] = $_SERVER['REQUEST_METHOD'];
        }
        return $request;
    }
}
