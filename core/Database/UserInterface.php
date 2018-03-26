<?php

namespace OAuth2\Database;

interface UserInterface{

  public function setUser($username,
                          $password,
                          $firstName, 
                          $lastName);
  public function checkUser($username, $password);
  public function getUser($username);

}

?>