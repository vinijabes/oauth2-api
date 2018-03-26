<?php

interface ResponseInteface{

  public function getStatusCode();
  public function getStatusText();
  public function setStatusCode(int $code, string $text = NULL);
  public function setParameter($name, $value);
  public function setParameters(array $parameters);
  public function addParameters(array $parameters);
  public function getParameters();
  public function getParameter($name, $default = NULL);
  public function setHttpHeader($name, $value);
  public function setHttpHeaders(array $parameters);
  public function addHttpHeaders(array $parameters);
  public function getHttpHeaders();
  public function getHttpHeader($name, $default = NULL);
  public function getResponseBody($format = "json");
  public function setError($statusCode, $error, $description = null);
  public function setRedirect( 
                    $statusCode, 
                    $url, 
                    $error = NULL, 
                    $errorDescription = NULL
                  );
  public function isInvalid();
  public function isInformational();
  public function isSuccessful();
  public function isRedirection();
  public function isClientError();
  public function isServerError();
  public function send($format = "json");
}

?>