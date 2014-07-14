<?php
namespace Apigee\Exceptions;

class ResponseException extends \Exception
{

    /**
     * @var null|string
     */
    private $uri;
    /**
     * @var null|array
     */
    private $params;
    /**
     * @var null|string
     */
    private $responseBody;

    /**
     * The request object as an instance of the
     * {@link http://api.guzzlephp.org/class-Guzzle.Http.Message.RequestInterface.html \Guzzle\Http\Message\RequestInterface}.
     *
     * @var \Guzzle\Http\Message\RequestInterface
     */
    public $requestObj;

    /**
     * The response object as an instance of
     * {@link http://api.guzzlephp.org/class-Guzzle.Http.Message.Response.html \Guzzle\Http\Message\Response}.
     *
     * @var \Guzzle\Http\Message\Response
     */
    public $responseObj;

    /**
     * Creates an exception object.
     */
    public function __construct($message, $code = 0, $uri = null, $params = null, $response_body = null)
    {
        parent::__construct($message, $code);

        if (strpos($uri, '@') !== false) {
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
        $this->responseObj = null;
        $this->requestObj = null;
    }

    /**
     * Returns URI which triggered the exception, if available.
     *
     * @return null|string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Returns an array of useful debug information, if available.
     *
     * Possible members of this array:
     *   - request_headers: string
     *   - response_headers: string
     *   - request_body: string
     * @return array|null
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Returns response text from the remote server, if available.
     *
     * @return null|string
     */
    public function getResponse()
    {
        return $this->responseBody;
    }

    /**
     * Returns an HTML-formatted string representation of the exception.
     *
     * @return string
     * @internal
     */
    public function __toString()
    {
        $msg = $this->getMessage();

        if (is_object($this->requestObj) && $this->requestObj instanceof \Guzzle\Http\Message\Request) {
            $request = array(
                'url' => $this->requestObj->getUrl(),
                'host' => $this->requestObj->getHost(),
                'headers' => $this->requestObj->getRawHeaders(),
                'query' => (string)$this->requestObj->getQuery()
            );
            if ($this->requestObj instanceof \Guzzle\Http\Message\EntityEnclosingRequestInterface) {
                $request_body = $this->requestObj->getBody();
                $request['content-type'] = $request_body->getContentType();
                $request['content-length'] = $request_body->getContentLength();
                $request['body'] = $request_body->__toString();
            }
            $msg .= "\n\nRequest: <pre>" . htmlspecialchars(print_r($request, true)) . '</pre>';
        }

        if (is_object($this->responseObj) && $this->responseObj instanceof \Guzzle\Http\Message\Response) {
            $response = array(
                'status' => $this->responseObj->getStatusCode(),
                'headers' => $this->responseObj->getRawHeaders(),
                'body' => $this->responseBody
            );
            $msg .= "\n\nResponse: <pre>" . htmlspecialchars(print_r($response, true)) . '</pre>';
        }

        return $msg;
    }

    /**
     * Poor man's replacement for PECL http_build_url().
     *
     * @param $components
     * @return string
     */
    private static function http_build_url($components)
    {
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