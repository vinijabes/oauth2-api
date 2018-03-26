<?php
require 'vendor/autoload.php';

define("BASEPATH", __DIR__.DIRECTORY_SEPARATOR);
define("COREPATH", BASEPATH."core".DIRECTORY_SEPARATOR);
define("APPPATH", BASEPATH."application".DIRECTORY_SEPARATOR);

require_once(COREPATH."autoload.php");
require_once(COREPATH."API.php");

?>