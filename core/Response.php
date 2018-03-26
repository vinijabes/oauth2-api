<?php

require_once("ResponseInterface.php");

class Response implements ResponseInteface{

  protected $httpVersion;

  protected $parameters = array();

  protected $headers = array();

  protected $statusCode;

  protected $statusText;

  public $statusTexts;

  public function __construct(
                    $parameters = array(), 
                    $statusCode = 200, 
                    $headers = array()
                  ){
    $this->setParameters($parameters);
    $this->setStatusCode($statusCode);
    $this->setHttpHeaders($headers);

    $this->httpVersion = "1.1";
    $API = API::getInstance();
    $API->loadConfig("statusTexts");
    $this->statusTexts = $API->getConfigs()->statusTexts;
  }

  public function getStatusCode(){
    return $this->statusCode;
  }

  public function getStatusText(){
    return $this->statusText;
  }

  public function setStatusCode(int $code, string $text = NULL){
    $this->statusCode = $code;
    $this->statusText = $text ? $text : $this->statusTexts[$code];

  }

  public function setParameter($name, $value){
    $this->parameters[$name] = $value;
  }

  public function setParameters(array $parameters){
    $this->parameters = $parameters;
  }

  public function addParameters(array $parameters){
    $this->parameters = array_merge($this->parameters, $parameters);
  }

  public function getParameters(){
    return $this->parameters;
  }

  public function getParameter($name, $default = NULL){
    return isset($this->parameters[$name]) ? $this->parameters[$name] : $default;
  }

  public function setHttpHeader($name, $value){
    $this->httpHeaders[$name] = $value;
  }

  public function setHttpHeaders(array $parameters){
    $this->httpHeaders = $parameters;
  }

  public function addHttpHeaders(array $parameters){
    $this->httpHeaders = array_merge($this->httpHeaders, $parameters);
  }

  public function getHttpHeaders(){
    return $this->httpHeaders;
  }

  public function getHttpHeader($name, $default = NULL){
    return isset($this->httpHeaders[$name]) ? 
            $this->httpHeaders[$name] : $default;
  }

  public function getResponseBody($format = "json"){
    switch($format){
      case "json":
        return $this->parameters ? json_encode($this->parameters, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) : "";
        break;
    }
  }

  public function setError($statusCode, $error, $description = null){
    $parameters = array(
      "error" => $error,
      "error_description" => $description
    );

    $headers = array(
      "Cache-Control" => "no-store"
    );

    $this->setStatusCode($statusCode);
    $this->addParameters($parameters);
    $this->addHttpHeaders($headers);

    if(!$this->isClientError() && !$this->isServerError()){
      throw new InvalidArgumentException("The given status code $statusCode is not an error");
    }
  }

  public function setRedirect( 
                    $statusCode, 
                    $url, 
                    $error = NULL, 
                    $errorDescription = NULL 
                  ){
    if($url == ""){
      throw new InvalidArgumentException("Can't redirect to empty URL");
    }

    if(!is_null($error)){
      $this->setError(400, $error, $errorDescription);
    }

    if(count($this->parameters) > 0){
      $parts = parse_url($url);
      $separator = (isset($parts["query"]) && count($parts["query"]) > 0) 
                    ? '&' : '?';
      $url .= $separator.http_build_query($this->parameters);
    }

    $this->setStatusCode($statusCode);
    $this->addHttpHeaders(array("Location" => $url));

    if(!$this->isRedirection()){
      throw new InvalidArgumentException("The given status code $statusCode is not an redirect");
      
    }
  }
  public function isInvalid(){
    return $this->statusCode < 100 || $this->statusCode >= 600;
  }

  public function isInformational(){
    return $this->statusCode >= 100 && $this->setStatusCode < 200;
  }

  public function isSuccessful(){
    return $this->statusCode >= 200 && $this->statusCode < 300;
  }

  public function isRedirection(){
    return $this->statusCode >= 300 && $this->setStatusCode < 400;
  }

  public function isClientError(){
    return $this->statusCode >= 400 && $this->statusCode < 500;
  }

  public function isServerError(){
    return $this->statusCode >= 500 && $this->statusCode < 600;
  }

  public function send($format = 'json'){
    switch($format){
      case "json":
        $this->setHttpHeader("Content-Type", "application/json");
        break;
    }
    header(sprintf('HTTP/%s %s %s', $this->httpVersion, $this->statusCode, $this->statusText));
    foreach ($this->getHttpHeaders() as $name => $header) {
      header(sprintf('%s: %s', $name, $header));
    }
    echo $this->getResponseBody($format);
  }
}
?>