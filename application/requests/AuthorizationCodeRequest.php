<?php

use OAuth2\Encryption\SHAEncryption;
use OAuth2\Token\AuthorizationCode;

class AuthorizationCodeRequest extends Request{
  public static function requiredParameters(){
    return array(
      "client_id",
      "redirect_uri",
      "scope"
    );
  }

  protected function process() : Response{
    $this->verifyParameters();
    $response = new Response();

    $user = $this->storage->getUser($this->getParameter("username"));
    
    $Key = new SHAEncryption();
    $Key = $Key->generateKey(); 
    
    $expiration = time() + 300;
    $scope = explode(",",$this->getParameter("scope"));

    $authCode = new AuthorizationCode();
    $authCode = $authCode->encode(
                                [
                                  $this->getParameter("client_id"),
                                  $user["_id"],
                                  $this->getParameter("redirect_uri"),
                                  $this->getParameter("scope"),
                                  $expiration
                                ],
                                $Key
                              );
    $this->storage->setAuthorizationCode(
                                      $authCode,
                                      $this->getParameter("client_id"),
                                      $user["_id"],
                                      $expiration,
                                      $scope
                                    );

    $response->setParameter("auth_code", $authCode);
    $response->setRedirect(302, $this->parameters["redirect_uri"]);
    return $response;
  }
}

?>