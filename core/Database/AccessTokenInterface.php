<?php

namespace OAuth2\Database;

interface AccessTokenInterface{

  public function getAccessToken($oauthToken);
  public function setAccessToken($oauthToken,
                                 $clientId,
                                 $userId,
                                 $expires,
                                 $scope = null);

}

?>