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

if(isset($_GET['auth_code'])){
  $client_id = "teste";
  $client_secret = "teste";
  $redirect_uri = "http://localhost/oauth2/test/test.php";
  $code = $_GET['auth_code'];

  $data= array("client_id" => $client_id,
               "client_secret" => $client_secret,
               "redirect_uri" => $redirect_uri,
               "code" => $code);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,"http://localhost/oauth2/token");
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,
              http_build_query($data));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $server_output = curl_exec ($ch);
  curl_close ($ch);

  echo $server_output;
}else if(isset($_GET["access_token"])){
  $db = new DB();
  $token = $db->getAccessToken($_GET["access_token"]);
  if(!$token) exit();
  $client = $db->getClient($token['client_id']);
  $accessToken = new JsonWebToken();
  echo json_encode($accessToken->decode($_GET["access_token"], $client['client_secret']));
}

?>