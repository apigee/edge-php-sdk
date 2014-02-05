<?php

/**
 * @file
 * Base class for API object classes. Handles some of the OrgConfig
 * invocation, which makes the actual HTTP calls.
 *
 * @author djohnson
 */

namespace Apigee\Util;

use Apigee\Exceptions\ResponseException as ResponseException;
use Apigee\Exceptions\IllegalMethodException as IllegalMethodException;

/**
 * Base class for API object classes. Handles some of the OrgConfig
 * invocation, which makes the actual HTTP calls.
 *
 * @author djohnson
 */
class APIObject {

  /**
   * The OrgConfig object.
   * @var \Apigee\Util\OrgConfig
   */
  protected $config;

  /**
   * The client object as an instance of 
   * {@link http://api.guzzlephp.org/class-Guzzle.Http.Client.html \Guzzle\Http\Client}. 
   * 
   * @var \Guzzle\Http\Client
   */
  protected $client;

  /**
   * @var array
   * Contains raw data from the Management API in a format defined by 
   * {@link Apigee\Util\DebugData}.
   * This format is compatible with older
   * PHP implementations of this library.
   */
  protected $debugData;

  /**
   * The HTTP response code returned by the request.
   * @var int
   */
  protected $responseCode;

  /**
   * The response body as a string.
   * @var string
   */
  protected $responseText;

  /**
   * The response object. 
   * @var array
   */
  protected $responseObj;

  /**
   * The response length.
   * @var int
   */
  protected $responseLength;

  /**
   * The response MIME type.
   * @var string
   */
  protected $responseMimeType;

  /**
   * A logger that implements the \Psr\Log\LoggerInterface interface.
   * See the {@link Apigee\Drupal\WatchdogLogger} class for an example that 
   * implements the Psr\Log\LoggerInterface.
   * @var \Psr\Log\LoggerInterface
   */
  public static $logger;

  /**
   * @var string
   */
  private $cachedBaseUrl;

  /**
   * Initializes the OrgConfig for this class.
   *
   * @param \Apigee\Util\OrgConfig $config
   * @param string $base_url
   */
  protected function init(\Apigee\Util\OrgConfig $config, $base_url) {
    $this->config =& $config;
    $base_url = rtrim($config->endpoint, '/') . '/' . ltrim($base_url, '/');
    $this->client = new \Guzzle\Http\Client($base_url);
    if (is_array($config->subscribers)) {
      foreach ($config->subscribers as $subscriber) {
        $this->client->addSubscriber($subscriber);
      }
    }
    self::$logger = $config->logger;
  }

  /**
   * Overwrites the base URL defined in $client.
   * You can restore the base URL by calling restoreBaseUrl().
   *
   * @param string $base_url
   */
  protected function setBaseUrl($base_url) {
    $this->cachedBaseUrl = $this->client->getBaseUrl();
    $base_url = rtrim($this->config->endpoint, '/') . '/' . ltrim($base_url, '/');
    $this->client->setBaseUrl($base_url);
  }

  /**
   * Restores the base URL in $client after a cal to setBaseUrl().
   */
  protected function restoreBaseUrl() {
    $this->client->setBaseUrl($this->cachedBaseUrl);
  }

  /**
   * Returns the OrgConfig in use by this class, so it can be reused by other
   * instances of Base.
   *
   * @return \Apigee\Util\OrgConfig
   * @see OrgConfig
   * @see Apigee\ManagementAPI\Base
   */
  public function getConfig() {
    return $this->config;
  }

  private function exec(\Guzzle\Http\Message\RequestInterface $request) {
    $this->responseCode = 0;
    $request->setAuth($this->config->user, $this->config->pass);
    try {
      $response = $request->send();
    }
    // catch ClientErrorResponseException, ServerErrorResponseException
    catch (\Guzzle\Http\Exception\BadResponseException $e) {
      $response = $e->getResponse();
    }
    catch (\Guzzle\Http\Exception\CurlException $e) {
      // Timeouts etc.
      DebugData::$raw = '';
      DebugData::$code = $e->getErrorNo();
      DebugData::$code_status = $e->getError();
      DebugData::$code_class = 0;
      DebugData::$exception = $e->getMessage();
      DebugData::$opts = array('request_headers' => $request->getRawHeaders());
      DebugData::$data = NULL;
      $exception = new ResponseException($e->getError(), $e->getErrorNo(), $request->getUrl(), DebugData::$opts);
      $exception->requestObj = $request;
      throw $exception;
    }
    $this->responseCode = $response->getStatusCode();
    $this->responseText = trim($response->getBody(TRUE));
    $this->responseLength = $response->getContentLength();
    $this->responseMimeType = $response->getContentType();
    $this->responseObj = array();
    $content_type = $response->getContentType();
    if (strpos($content_type, '/json') !== FALSE && (substr($this->responseText, 0, 1) == '{' || substr($this->responseText, 0, 1) == '[')) {
      $response_obj = @json_decode($this->responseText, TRUE);
      if (is_array($response_obj)) {
        $this->responseObj = $response_obj;
      }
    }

    $status = self::getStatusMessage($this->responseCode);
    $code_class = floor($this->responseCode / 100);

    DebugData::$raw = $this->responseText;
    DebugData::$opts = array(
      'request_headers' => $request->getRawHeaders(),
      'response_headers' => $response->getRawHeaders()
    );

    if ($request instanceof \Guzzle\Http\Message\EntityEnclosingRequestInterface) {
      DebugData::$opts['request_body'] = (string) $request->getBody();
    }
    DebugData::$opts['request_type'] = class_implements($request);
    DebugData::$data = $this->responseObj;
    DebugData::$code = $this->responseCode;
    DebugData::$code_status = $status;
    DebugData::$code_class = $code_class;
    DebugData::$exception = NULL;

    if ($code_class != 2) {
      $uri = $request->getUrl();
      if (!empty($this->responseCode) && isset($this->responseObj['message'])) {
        $message = 'Code: ' . $this->responseCode . '; Message: ' . $this->responseObj['message'];
      }
      else {
        $message = 'API returned HTTP code of ' . $this->responseCode . ' when fetching from ' . $uri;
      }
      DebugData::$exception = $message;
      self::$logger->error($this->responseText);

      // Create better status to show up in logs
      $status .= ': ' . $request->getMethod() . ' ' . $uri;
      if ($request instanceof \Guzzle\Http\Message\EntityEnclosingRequestInterface) {
        $body = $request->getBody();
        if ($body instanceof \Guzzle\Http\EntityBodyInterface) {
          $status .= ' with Content-Length of ' . $body->getContentLength()
            . ' and Content-Type of ' . $body->getContentType();
        }
      }

      $exception = new ResponseException($status, $this->responseCode, $uri, DebugData::$opts, $this->responseText);
      $exception->requestObj = $request;
      $exception->responseObj = $response;
      throw $exception;
    }
  }

  /**
   * Performs an HTTP GET on a URI. The result can be read from
   * $this->response* variables.
   *
   * @param string|null $uri
   * @param string $accept_mime_type
   * @param array $custom_headers
   */
  public function get($uri = NULL, $accept_mime_type = 'application/json; charset=utf-8', $custom_headers = array(), $options = array()) {
    $headers = array('accept' => $accept_mime_type);
    foreach ($custom_headers as $key => $value) {
      $headers[strtolower($key)] = $value;
    }
    $options += $this->config->http_options;
    $request = $this->client->get($uri, $headers, $options);
    $this->exec($request);
  }

  /**
   * Performs an HTTP POST on a URI. The result can be read from
   * $this->response* variables.
   *
   * @param string $uri
   * @param mixed $payload
   * @param string $content_type
   * @param string $accept_type
   * @param array $custom_headers
   */
  public function post($uri = NULL, $payload = '', $content_type = 'application/json; charset=utf-8', $accept_type = 'application/json; charset=utf-8', $custom_headers = array(), $options = array()) {
    self::preparePayload($content_type, $payload);
    $headers = array(
      'accept' => $accept_type,
      'content-type' => $content_type
    );
    foreach ($custom_headers as $key => $value) {
      $headers[strtolower($key)] = $value;
    }
    if (strlen($payload) == 0) {
      $headers['content-type'] = '';
    }
    $options += $this->config->http_options;
    $request = $this->client->post($uri, $headers, $payload, $options);
    $this->exec($request);
  }

  /**
   * Performs an HTTP DELETE on a URI. The result can be read from
   * $this->response* variables.
   *
   * This method is named http_delete() to avoid a name clash with objects that inherit
   * from this one, which usually have a delete() method.
   *
   * @param string $uri
   * @param string $accept
   * @param array $custom_headers
   */
  public function http_delete($uri = NULL, $accept = 'application/json; charset=utf-8', $custom_headers = array(), $options = array()) {
    $headers = array('accept' => $accept);
    foreach ($custom_headers as $key => $value) {
      $headers[strtolower($key)] = $value;
    }
    $options += $this->config->http_options;
    $request = $this->client->delete($uri, $headers, $options);
    $this->exec($request);
  }

  /**
   * Performs an HTTP PUT on a URI. The result can be read from
   * $this->response* variables.
   *
   * @param string|null $uri
   * @param mixed $payload
   * @param string $content_type
   * @param array $custom_headers
   */
  public function put($uri = NULL, $payload = '', $content_type = 'application/json; charset=utf-8', $custom_headers = array(), $options = array()) {
    self::preparePayload($content_type, $payload);
    $headers = array('content-type' => $content_type);
    foreach ($custom_headers as $key => $value) {
      $headers[strtolower($key)] = $value;
    }
    if (strlen($payload) == 0) {
      $headers['content-type'] = '';
    }
    $options += $this->config->http_options;
    $request = $this->client->put($uri, $headers, $payload, $options);
    $this->exec($request);
  }

  /**
   * Performs an HTTP HEAD on a URI. $this->responseText and $this->responseObj
   * empty after this call, but you can read the content-length from
   * $this->responseLength.
   *
   * @param string $uri
   * @param string $accept_mime_type
   * @param array $custom_headers
   */
  public function head($uri = NULL, $accept_mime_type = 'application/json; charset=utf-8', $custom_headers = array(), $options = array()) {
    $headers = array('accept' => $accept_mime_type);
    foreach ($custom_headers as $key => $value) {
      $headers[strtolower($key)] = $value;
    }
    $options += $this->config->http_options;
    $request = $this->client->head($uri, $headers, $options);
    $this->exec($request);
  }

  /**
   * Intercepts any snake_case method invocations that aren't already
   * defined, turns them into camelCase, and tries to invoke them.
   *
   * Incoming snake_case method names must contain no uppercase to
   * qualify for this transmogrification. In the interest of efficiency,
   * we also don't process any method names not containing an underscore.
   *
   * @TODO When we require PHP 5.4, make this a mix-in.
   *
   * @param string $method
   * @param array $args
   * @return mixed
   * @throws \Apigee\Exceptions\IllegalMethodException
   * @internal
   */
  public function __call($method, $args) {
    $class = get_class();

    if ($method == strtolower($method) && strpos($method, '_') !== FALSE) {
      $parts = explode('_', $method);
      $camel_case = array_shift($parts);
      foreach ($parts as $part) {
        $camel_case .= ucfirst($part);
      }
      if (method_exists($this, $camel_case)) {
        self::warnDeprecated($class, $method);
        return call_user_func_array(array($this, $camel_case), $args);
      }
      throw new IllegalMethodException('Class “' . $class . '” contains no such method “' . $method . '” (even after camelCasing)');
    }
    throw new IllegalMethodException('Class “' . $class . '” contains no such method “' . $method . '”');
  }

  /**
   * Same as above, except for static methods
   *
   * @param $method
   * @param $args
   * @return mixed
   * @throws \Apigee\Exceptions\IllegalMethodException
   * @internal
   */
  public static function __callstatic($method, $args) {
    $class = get_class();

    if ($method == strtolower($method) && strpos($method, '_') !== FALSE) {
      $parts = explode('_', $method);
      $camel_case = array_shift($parts);
      foreach ($parts as $part) {
        $camel_case .= ucfirst($part);
      }
      if (method_exists($class, $camel_case)) {
        self::warnDeprecated($class, $method);
        return forward_static_call_array(array($class, $camel_case), $args);
      }
      throw new IllegalMethodException('Class “' . $class . '” contains no such static method “' . $method . '” (even after camelCasing)');
    }
    throw new IllegalMethodException('Class “' . $class . '” contains no such static method “' . $method . '”');
  }

  /**
   * Given a status code, returns the proper human-readable message which
   * corresponds to that code.
   *
   * @param int $code
   * @return string
   */
  private static function getStatusMessage($code) {
    static $responses = array(
      100 => 'Continue',
      101 => 'Switching Protocols',
      102 => 'Processing', // WebDAV

      200 => 'OK',
      201 => 'Created',
      202 => 'Accepted',
      203 => 'Non-Authoritative Information',
      204 => 'No Content',
      205 => 'Reset Content',
      206 => 'Partial Content',
      207 => 'Multi-Status', // WebDAV
      208 => 'Already Reported', // WebDAV
      226 => 'IM Used',

      300 => 'Multiple Choices',
      301 => 'Moved Permanently',
      302 => 'Found',
      303 => 'See Other',
      304 => 'Not Modified',
      305 => 'Use Proxy',
      306 => 'Switch Proxy',
      307 => 'Temporary Redirect',
      308 => 'Permanent Redirect',

      400 => 'Bad Request',
      401 => 'Unauthorized',
      402 => 'Payment Required',
      403 => 'Forbidden',
      404 => 'Not Found',
      405 => 'Method Not Allowed',
      406 => 'Not Acceptable',
      407 => 'Proxy Authentication Required',
      408 => 'Request Time-out',
      409 => 'Conflict',
      410 => 'Gone',
      411 => 'Length Required',
      412 => 'Precondition Failed',
      413 => 'Request Entity Too Large',
      414 => 'Request-URI Too Large',
      415 => 'Unsupported Media Type',
      416 => 'Requested Range Not Satisfiable',
      417 => 'Expectation Failed',
      418 => 'I\'m a teapot', // RFC 2324 ;-)
      420 => 'Enhance Your Calm', // Twitter ;-)
      422 => 'Unprocessable Entity', // WebDAV
      423 => 'Locked', // WebDAV
      424 => 'Failed Dependency', // WebDAV
      425 => 'Unordered Collection',
      426 => 'Upgrade Required',
      428 => 'Precondition Required',
      429 => 'Too Many Requests',
      431 => 'Request Header Fields Too Large',
      444 => 'No Response', // nginx
      449 => 'Retry With', // Microsoft
      450 => 'Blocked By Parental Controls', // Microsoft
      451 => 'Unavailable for Legal Reasons',
      494 => 'Request Header Too Large', // nginx
      495 => 'Cert Error', // nginx
      496 => 'No Cert', // nginx
      497 => 'HTTP to HTTPS', // nginx
      499 => 'Client Closed Request', // nginx

      500 => 'Internal Server Error',
      501 => 'Not Implemented',
      502 => 'Bad Gateway',
      503 => 'Service Unavailable',
      504 => 'Gateway Timeout',
      505 => 'HTTP Version not supported',
      506 => 'Variant Also Negotiates',
      507 => 'Insufficient Storage', // WebDAV
      508 => 'Loop Detected', // WebDAV
      509 => 'Bandwidth Limit Exceeded', // apache?
      510 => 'Not Extended',
      511 => 'Network Authentication Required',
      598 => 'Network read timeout error', // Microsoft
      599 => 'Network connect timeout error', // Microsoft
    );

    if (!isset($responses[$code])) {
      // According to RFC 2616, all unknown HTTP codes must be treated the same
      // as the base code in their class.
      $code = floor($code / 100) * 100;
    }
    if (!isset($responses[$code])) {
      // Something is seriously screwy; treat it as an internal server error.
      $code = 500;
    }
    return $responses[$code];
  }


  /**
   * If payload is not already a string, convert it to a string based on its content-type.
   *
   * @static
   * @param $content_type
   * @param $payload
   */
  protected static function preparePayload($content_type, &$payload) {
    // If content_type includes charset, strip it off.
    if (($i = strpos($content_type, ';')) !== FALSE) {
      $content_type = trim(substr($content_type, 0, $i));
    }
    if ($content_type == 'application/json' && (is_object($payload) || is_array($payload))) {
      // Turn objects/arrays into JSON strings.
      $payload = json_encode($payload);
    }
    elseif ($content_type == 'application/xml') {
      // Turn XML document representations into strings.
      if ($payload instanceof \DOMDocument) {
        $payload = $payload->saveXML($payload->documentElement);
      }
      elseif ($payload instanceof \SimpleXMLElement) {
        $payload = $payload->asXML();
        // strip off processing instruction if present
        $payload = preg_replace('!^<\?[^?]+\?>!', '', $payload);
      }
    }
  }

  private static function warnDeprecated($class, $method) {
    if (version_compare(PHP_VERSION, '5.4.0', 'ge')) {
      $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 4);
    }
    else {
      $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    }
    $frame = $backtrace[3];
    $message = 'Deprecated method ' . $class . '::' . $method . ' was invoked in file ' . $frame['file'] . ', line ' . $frame['line'] . '. Please use camelCase method name instead.';
    self::$logger->notice($message, array('type' => $class));
  }

}
