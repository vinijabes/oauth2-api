<?php

namespace OAuth2\Token;

use Exception;
use InvalidArgumentException;


/**
 * @link https://github.com/F21/jwt
 * @author F21
 */

class AuthorizationCode{
  /**
   * @param $payload
   * @param $key
   * @param string $algo
   * @return string
   */
  public function encode($payload, $key)
  {
    $baseAuthCode = json_encode($payload);
    $authCode = hash_hmac('sha512', $baseAuthCode, $key, true);
    return $this->urlSafeB64Encode($authCode);
  }

  /**
   * @param string $data
   * @return string
   */
  public function urlSafeB64Encode($data)
  {
    $b64 = base64_encode($data);
    $b64 = str_replace(array('+', '/', "\r", "\n", '='),
                       array('-', '_'),
                       $b64);

    return $b64;
  }

  /**
   * @param string $b64
   * @return mixed|string
   */
  public function urlSafeB64Decode($b64)
  {
    $b64 = str_replace(array('-', '_'),
            array('+', '/'),
            $b64);

    return base64_decode($b64);
  }
}


?>