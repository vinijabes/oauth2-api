<?php
$clientId = isset($_GET['client_id']) ? $_GET['client_id'] : NULL;
$redirectUri = isset($_GET['redirect_uri']) ? $_GET['redirect_uri'] : NULL;
$scope = isset($_GET['scope']) ? $_GET['scope'] : NULL;
$expiration = time() + 300;


if(!isset($clientId, $redirectUri, $scope)){
  header("HTTP/1.1 400");
  exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title></title>
</head>
<body>
  <form action="http://localhost/oauth2/authorization">
    <input type="hidden" name="client_id" value="<?=$clientId?>">
    <input type="hidden" name="redirect_uri" value="<?=$redirectUri?>">
    <input type="hidden" name="scope" value="<?=$scope?>">
    <div>
      <label>Username:</label>
      <input type="text" name="username">
    </div>
    <div>
      <label>Password:</label>
      <input type="password" name="password">
    </div>
    <div>
      <input type="submit" name="">
    </div>
  </form>
</body>
</html>