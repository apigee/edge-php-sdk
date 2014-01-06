<?php
namespace Apigee\Exceptions;

class ResponseException extends \Exception {

  private $uri;
  private $params;
  private $responseBody;

  /**
   * @var \Guzzle\Http\Message\RequestInterface
   */
  public $requestObj;

  /**
   * @var \Guzzle\Http\Message\Response
   */
  public $responseObj;

  public function __construct($message, $code = 0, $uri = NULL, $params = NULL, $response_body = NULL) {
    parent::__construct($message, $code);

    if (strpos($uri, '@') !== FALSE) {
      // strip out username/password
      $components = parse_url($uri);
      unset ($components['user']);
      unset ($components['pass']);
      // Use PECL http functions when available
      $uri = (function_exists('http_build_url') ? http_build_url($components) : self::http_build_url($components));
    }

    $this->uri = $uri;
    $this->params = $params;
    $this->responseBody = $response_body;
    $this->responseObj = NULL;
    $this->requestObj = NULL;
  }

  public function getUri() {
    return $this->uri;
  }
  public function getParams() {
    return $this->params;
  }
  public function getResponse() {
    return $this->responseBody;
  }

  /**
   * Returns an HTML-formatted string representation of the exception.
   * 
   * @return string
   */
  public function __toString() {
    $msg = $this->getMessage();

    if (is_object($this->requestObj) && $this->requestObj instanceof \Guzzle\Http\Message\Request) {
      $request = array(
        'url' => $this->requestObj->getUrl(),
        'host' => $this->requestObj->getHost(),
        'headers' => $this->requestObj->getRawHeaders(),
        'query' => $this->requestObj->getQuery()
      );
      if ($this->requestObj instanceof \Guzzle\Http\Message\EntityEnclosingRequestInterface) {
        $request_body = $this->requestObj->getBody();
        $request['content-type'] = $request_body->getContentType();
        $request['content-length'] = $request_body->getContentLength();
        $request['body'] = $request_body->__toString();
      }
      $msg .= "\n\nRequest: <pre>" . htmlspecialchars(print_r($request, TRUE)) . '</pre>';
    }

    if (is_object($this->responseObj) && $this->responseObj instanceof \Guzzle\Http\Message\Response) {
      $response = array(
        'status' => $this->responseObj->getStatusCode(),
        'headers' => $this->responseObj->getRawHeaders(),
        'body' => $this->responseBody
      );
      $msg .= "\n\nResponse: <pre>" . htmlspecialchars(print_r($response, TRUE)) . '</pre>';
    }

    return $msg;
  }

  /**
   * Poor man's replacement for PECL http_build_url().
   *
   * @param $components
   * @return string
   */
  private static function http_build_url($components) {
    $uri = $components['scheme'] . '://' . $components['host'];
    if (array_key_exists('port', $components) && !empty($components['port'])) {
      $uri .= ':' . $components['port'];
    }
    $uri .= $components['path'];
    if (array_key_exists('query', $components) && !empty($components['query'])) {
      $uri .= '?' . $components['query'];
    }
    if (array_key_exists('fragment', $components) && !empty($components['fragment'])) {
      $uri .= '#' . $components['fragment'];
    }
    return $uri;
  }
}