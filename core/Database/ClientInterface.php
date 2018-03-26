<?php

namespace OAuth2\Database;

interface ClientInterface{

  public function addClient($clientId,
                          $clientSecret,
                          array $scope, 
                          string $redirectUri);
  public function checkClient($username, $password);
  public function getClient($username);

}

?>