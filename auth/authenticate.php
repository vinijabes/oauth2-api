<?php

require("../core/JsonWebToken.php");
require("../core/AuthorizationCode.php");
require("../core/Encryption/SHAEncryption.php");
require("../core/Encryption/RSAEncryption.php");
require("../core/Database/DB.php");
use OAuth2\Token\JsonWebToken;
use OAuth2\Token\AuthorizationCode;
use OAuth2\Encryption\SHAEncryption;
use OAuth2\Encryption\RSAEncryption;
use OAuth2\Database\DB;

$username = isset($_GET['username']) ? $_GET['username'] : NULL;
$password = isset($_GET['password']) ? $_GET['password'] : NULL;
$clientId = isset($_GET['client_id']) ? $_GET['client_id'] : NULL;
$redirectUri = isset($_GET['redirect_uri']) ? $_GET['redirect_uri'] : NULL;
$scope = isset($_GET['scope']) ? explode(",", $_GET['scope']) : NULL;
$expiration = time() + 300;

if(!isset($clientId, $redirectUri, $scope, $username, $password)){
  header("HTTP/1.1 500");
  exit();
}

$db = new DB();
$user = $db->getUser($username);
if($db->checkUser($user, $password)){
  $authCode = new AuthorizationCode();
  $Key = new SHAEncryption();
  $Key = $Key->generateKey();

  $authCode = $authCode->encode([
                                  $clientId,
                                  $user["_id"], 
                                  $redirectUri, 
                                  json_encode($scope),
                                  $expiration
                                ], $Key);
  $db->setAuthorizationCode($authCode, 
                            $clientId,
                            $user["_id"],
                            $expiration,
                            $scope);

  header("Location: $redirectUri?auth_code=$authCode");
}else{
  header("HTTP/1.1 400");
  exit();
}


?>