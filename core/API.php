<?php

use OAuth2\Database\DB;

$constants_path = APPPATH."constants".DIRECTORY_SEPARATOR;
define("CONSTANTSPATH", $constants_path);

$config_path = APPPATH."configs".DIRECTORY_SEPARATOR;
define("CONFIGPATH", $config_path);

$request_path = APPPATH."requests".DIRECTORY_SEPARATOR;
define("REQUESTPATH", $request_path);

class API{
  private static $instance = NULL;
  protected $configs;
  protected $request;
  protected $db;

  public function __construct(){
    self::$instance = $this;
    $this->configs = new stdClass();
    $this->loadConfig("database");
    $this->loadConfig("requestRoutes");
    $this->db = new DB(
                    $this->configs->database["hostname"],
                    $this->configs->database["database"],
                    $this->configs->database["username"],
                    $this->configs->database["password"]
                  );
    $this->getRequestByRoute();
  }

  public function loadConstants(string $constants = ""){
    $this->requireFile(CONSTANTSPATH.$constants);
  }

  public function loadConfig(string $config = ""){
    $this->configs->$config = $this->requireFile(CONFIGPATH.$config);
  }

  public function setRequest(Request $request = NULL){
    $this->request = $request;
  }

  public function getRequestByRoute(){
    if(isset($_GET["params"])){
      if(isset($this->configs->requestRoutes[$_GET["params"]])){
        $class = $this->configs->requestRoutes[$_GET["params"]];
        $this->requireFile($this->configs->requestRoutes["directory"].
                            $class);
        $this->setRequest(new $class);
      }
    }
  }

  public function getConfigs(string $config = ""){
    if($config != "")
      return $this->configs->$config;
    return $this->configs;
  }

  public function getDB(){
    return $this->db;
  }

  protected function requireFile(string $file = ""){
    if(strpos( $file, "." ) === FALSE){
      $file .= ".php";
    }
    if(!is_file($file)){
      throw new Exception("Is not a file: $file", 1);
    }
    return require_once($file);
  }

  public function doResponse(){
    if($this->request === NULL)
      throw new Exception("Error Processing Request", 1);
    $this->request->setStorage($this->db);
    $response = Request::processRequest($this->request);
    $response->send();
  }

  static function getInstance(){
    if(self::$instance == NULL){
      self::$instance = new static();
    }
    return self::$instance;
  }
}

$API = new API();
$API->doResponse();

?>