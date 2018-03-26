<?php

use OAuth2\Token\JsonWebToken;

class TokenRequest extends Request{

  protected $onlyPost = true;

  public static function requiredParameters(){
    return array(
      "client_id",
      "client_secret",
      "redirect_uri",
      "code"
    );
  }

  protected function process() : Response{
    $this->verifyParameters();
    $response = new Response();

    $client = $this->storage->getClient($this->getParameter("client_id"));
    if($client["client_secret"] == $this->getParameter("client_secret")){
      $this->storage->setAsUsed($this->getParameter("code"));

      $token = new JsonWebToken();

      $data = array();
      $data['exp'] = time() + 3600;
      $data['token'] = $token->encode(
                                    $data, 
                                    $this->getParameter("client_secret")
                                  );
      $authCode = $this->storage->
                    getAuthorizationCode($this->getParameter("code"));  
      if($this->storage->setAccessToken(
                                    $data["token"],
                                    $this->getParameter("client_id"),
                                    $client["_id"],
                                    $data["exp"],
                                    $authCode["scope"]
                                  )){
        $response->setParameters($data);
      }else{
        $response->setStatusCode(500);
      }
    }else{
      var_dump($this->getParameter("client_id"));
      $response->setError(401, "Unauthorized access", 
                          "Invalid client_secret");
    }
    
    return $response;
  }
}

?>