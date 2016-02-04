<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include ("../ewcfg10.php");
define ("DB_HOST_NAME", EW_CONN_HOST);
define ("PORT_NUM", EW_CONN_PORT);
define ("DB_NAME", EW_CONN_DB);
define ("DB_USER_NAME", EW_CONN_USER);
define ("DB_PASSWORD", EW_CONN_PASS);

include ("DAO.php");
include "UserClass.php";

$UserClass = new UserClass(15);

echo '<pre>';
print_r($UserClass->getUserRegion());
echo '</pre>';