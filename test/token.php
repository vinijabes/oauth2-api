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

$db = new DB();

$token = new JsonWebToken();
$Key = new SHAEncryption();
$Key = $Key->generateKey();

$clientId = isset($_POST['client_id']) ? $_POST['client_id'] : NULL;
$clientSecret = isset($_POST['client_secret']) ? $_POST['client_secret'] : NULL;
$redirectUri = isset($_POST['redirect_uri']) ? $_POST['redirect_uri'] : NULL;
$authCode = isset($_POST['code']) ? $_POST['code'] : NULL;

if(!isset($clientId, $redirectUri, $authCode, $clientSecret)){
  header("HTTP/1.1 400");
  exit();
}

$client = $db->getClient($clientId);
if($client["client_secret"] == $clientSecret){
  if(!$db->setAsUsed($authCode)){
    echo $authCode;
    exit();
  }
  $data = array();
  $data['exp'] = time() + 6000;
  $data['token'] = $token->encode($data, $clientSecret);
  $code = $db->getAuthorizationCode($authCode);
  if($db->setAccessToken($data['token'], $clientId, $client["_id"], $data['exp'], $code["scope"]));
  echo json_encode($data);
}else{
  var_dump($client);
}

//header("Location: $redirectUri?auth_code=$authCode")

?>