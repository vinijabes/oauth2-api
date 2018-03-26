<?php
namespace OAuth2\Encryption;

require_once("EncryptionInterface.php");


class SHAEncryption implements EncryptionInterface{
  const SIZE = 32;

  public function generateKey(){
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!_-()$#@';
    $pass = array(); 
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < self::SIZE; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass);
  }
}

?>