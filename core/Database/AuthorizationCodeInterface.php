<?php

namespace OAuth2\Database;

interface AuthorizationCodeInterface{

  public function getAuthorizationCode($oauthAuthCode);
  public function setAuthorizationCode($oauthAuthCode,
                                       $clientId,
                                       $userId,
                                       $expires,
                                       $scope = null);
  public function setAsUsed($oauthAuthCode);

}

?>