<?php
namespace Apigee\Exceptions;

class InstallException extends \Exception {

  private $uri;
  private $params;
  private $response_body;

  public function __construct($message, $code = 0, $uri = NULL, $params = NULL, $response_body = NULL) {
    parent::__construct($message, $code);
    $this->uri = $uri;
    $this->params = $params;
    $this->response_body = $response_body;
    if (function_exists("xdebug_break")) {
      xdebug_break();
    }
    if (function_exists("watchdog")) {
      watchdog(__CLASS__, $this->getMessage());
    } else {
      error_log($this->getMessage());
    }
    if (function_exists("install_display_output")) {
      install_display_output($this->getMessage(), $params);
    }
  }

  public function getUri() {
    return $this->uri;
  }
  public function getParams() {
    return $this->params;
  }
  public function getResponse() {
    return $this->response_body;
  }
  
  function __toString() {
    $params = "";
    foreach ($this->getParams() as $key => $value){
      $params .= "\t\t==>PARAM {$key} => {$value}\n";
    }
    $toReturn = "***********************************************************************************************";
    $toReturn .= sprintf("AN INSTALL EXCEPTION HAS OCCURRED: %s \n", $this->getUri());
    $toReturn .= sprintf("Message: %s \n", $this->getMessage());
    $toReturn .= sprintf("Params: \n %s", $params);
    $toReturn .= sprintf("Response: %s \n\n\n", $this->getResponse());
    $toReturn = "***********************************************************************************************";
    return $toReturn;
  }
  
}