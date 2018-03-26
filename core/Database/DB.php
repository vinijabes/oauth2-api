<?php
namespace OAuth2\Database;

require_once(__DIR__.'/../../vendor/autoload.php');

use MongoDB\Client;
use MongoDB\Database;

require_once("ClientInterface.php");
require_once("AccessTokenInterface.php");
require_once("AuthorizationCodeInterface.php");
require_once("UserInterface.php");

class DB implements ClientInterface,
                    AccessTokenInterface,
                    AuthorizationCodeInterface,
                    UserInterface{
  protected $db;

  const CLIENTS_TABLE = "oauth_clients";
  const USERS_TABLE = "oauth_users";
  const ACCESS_TOKEN_TABLE = "oauth_access_tokens";
  const AUTHORIZATION_TOKEN_TABLE = "oauth_authorization_codes";

  public function __construct(
                            $hostname, 
                            $database, 
                            $username = NULL,
                            $password = NULL
                          ){
    if(!is_null($username) && $username != "")
      $this->db = new Client("mongodb://$username:$password@$hostname");
    else
      $this->db = new Client("mongodb://$hostname");
    $this->db = $this->db->$database;
  }

  //UserInterface
  public function addClient($clientId,
                            $clientSecret, 
                            array $scope = [],
                            string $redirectUri = ""){
    $data = array();
    $data['client_id'] = $clientId;
    $data['client_secret'] = $clientSecret;
    $data['scope'] = $scope;
    if($redirectUri != ""){
      $data['redirect_uri'] = $redirectUri;
    }

    $result = $this->collection(self::CLIENTS_TABLE)->insertOne($data);
    return $result->getInsertedCount();
  }

  public function checkClient($username, $password){
    $result = $this->collection(self::CLIENTS_TABLE)
                ->find(['client_id' => $username]);
    foreach ($result as $user) {
       if($user["client_secret"] == $password) return true;
    };
    return false;
  }

  public function getClient($username){
    $result = $this->collection(self::CLIENTS_TABLE)
                ->findOne(['client_id' => $username]);
    return is_null($result) ? false : $result;
  }

  //AccessTokenInteface
  public function getAccessToken($oauthToken){
    $token = $this->collection(self::ACCESS_TOKEN_TABLE)
                ->findOne(['oauth_token' => $oauthToken]);
    return is_null($token) ? false : $token;
  }

  public function setAccessToken($oauthToken,
                                 $clientId,
                                 $userId,
                                 $expires,
                                 $scope = null){
    if($this->getAccessToken($oauthToken)){
      $result = $this->collection(self::ACCESS_TOKEN_TABLE)
                  ->updateOne(['oauth_token' => $oauthToken],
                              ['set'=>[
                                'client_id' => $clientId,
                                'user_id'   => $userId,
                                'expires'   => $expires,
                                'scope'     => $scope
                              ]]);
      return $result->getMatchedCount() > 0;
    }

    $token = [
      'oauth_token' => $oauthToken,
      'client_id' => $clientId,
      'user_id' => $userId,
      'expires' => $expires,
      'scope' => $scope
    ];
    $result = $this->collection(self::ACCESS_TOKEN_TABLE)->insertOne($token);
    return $result->getInsertedCount() > 0;
  }

  //AuthorizationCodeInterface
  public function getAuthorizationCode($oauthAuthCode){
    $authToken = $this->collection(self::AUTHORIZATION_TOKEN_TABLE)
                ->findOne(['oauth_code' => $oauthAuthCode]);
    return is_null($authToken) ? false : $authToken;
  }

  public function setAuthorizationCode($oauthAuthCode,
                                       $clientId,
                                       $userId,
                                       $expires,
                                       $scope = null){
    if($this->getAccessToken($oauthAuthCode)){
      $result = $this->collection(self::AUTHORIZATION_TOKEN_TABLE)
                  ->updateOne(['access_token' => $oauthAuthCode],
                              ['$set'=>[
                                'client_id' => $clientId,
                                'user_id'   => $userId,
                                'expires'   => $expires,
                                'scope'     => $scope
                              ]]);
      return $result->getMatchedCount() > 0;
    }

    $token = [
      'oauth_code' => $oauthAuthCode,
      'client_id' => $clientId,
      'user_id' => $userId,
      'expires' => $expires,
      'scope' => $scope
    ];
    $result = $this->collection(self::AUTHORIZATION_TOKEN_TABLE)->insertOne($token);
    return $result->getInsertedCount() > 0;
  }

  public function setAsUsed($oauthAuthCode){
    $authCode = $this->getAuthorizationCode($oauthAuthCode);
    if(isset($authCode["used"])){
      //return false;
    }
    $result = $this->collection(self::AUTHORIZATION_TOKEN_TABLE)
                  ->updateOne(['oauth_code' => $oauthAuthCode],
                              ['$set'=>[
                                'used' => true
                              ]]);
    return $result->getMatchedCount() > 0;
  }

  //UserInterface
  public function setUser($username,
                          $password,
                          $firstName, 
                          $lastName){
    if($this->getUser($username)){
      $result = $this->collection(self::USERS_TABLE)
                  ->updateOne(['username' => $username],
                              ['$set' =>[
                                'password' => $password,
                                'firstName' => $firstName,
                                'lastName' => $lastName
                              ]]);
      return $result->getMatchedCount() > 0;
    }
    $user = [
      'username' => $username,
      'password' => $password,
      'firstName' => $firstName,
      'lastName' => $lastName
    ];

    $result = $this->collection(self::USERS_TABLE)->insertOne($user);
    return $result->getInsertedCount() > 0;
  }

  public function checkUser($user, $password){
    if($user == NULL) return false;
    return $user['password'] == $password;
  }

  public function getUser($username){
    $result = $this->collection(self::USERS_TABLE)->findOne(array('username' => $username));
    return is_null($result) ? false : $result;
  }

  protected function collection($name)
  {
      return $this->db->{$name};
  }
}

?>