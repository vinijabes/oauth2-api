<?php
$requestRoutes["directory"] = BASEPATH
                             ."application"
                             .DIRECTORY_SEPARATOR
                             ."requests"
                             .DIRECTORY_SEPARATOR;

$requestRoutes["authorization"] = "AuthorizationCodeRequest";
$requestRoutes["token"] = "TokenRequest";

return $requestRoutes;
?>