<?php
namespace OAuth2\Encryption;

require_once("EncryptionInterface.php");


class RSAEncryption implements EncryptionInterface{
  const SIZE = 2048;
  const DIGEST = "sha512";

  public function generateKey(){
    $config = array(
        "digest_alg" => self::DIGEST,
        "private_key_bits" => self::SIZE,
        "private_key_type" => OPENSSL_KEYTYPE_RSA,
    );
    $res = openssl_pkey_new($config);
    openssl_pkey_export($res, $privKey);
    $pubKey = openssl_pkey_get_details($res);
    $pubKey = $pubKey["key"];
    return array("pubKey" => $pubKey, "privKey" => $privKey);
  }
}

?>