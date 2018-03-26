<?php

use OAuth2\Database\DB;

class Request{

  protected $parameters;
  protected $onlyPost = false;
  protected $storage;

  public static function requiredParameters(){
    return array(

    );
  }

  public static function optionalParameters(){
    return array(

    );
  }

  public function __construct($parameters = array()){
    $this->parameters = $parameters;
  }

  public function setParameter($name, $value){
    $this->parameters[$name] = $value;
  }

  public function setParameters(array $parameters){
    $this->parameters = $parameters;
  }

  public function getParameters(){
    return $this->parameters;
  }

  public function getParameter($name, $default = NULL){
    return isset($this->parameters[$name]) ? $this->parameters[$name] : $default;
  }

  public function verifyParameters(){
    foreach (static::requiredParameters() as $key => $value) {
      if(!isset($this->parameters[$value])){
        throw new InvalidArgumentException("Argument $value necessary but doesn't given");
        
      }
    }
  }

  protected function process() : Response{
    return new Response();
  }

  public function onlyPost(){
    return $this->onlyPost;
  }

  public function setStorage(DB $storage){
    $this->storage = $storage;
  }

  public static function processRequest(Request $request){
    if(!$request->onlyPost())
      $data = array_merge($_GET, $_POST);
    else
      $data = $_POST;
    $needed = array_merge($request->requiredParameters(), 
                          $request->optionalParameters());
    foreach ($data as $key => $value) {
      if(in_array($key, $needed))
        $request->setParameter($key, $value);
    }
    return $request->process();
  }
}

?>