<?php
/**
 * Created By: JOSEF FRIEDRICH S. BALDO
 * Date & Time: 1/19/2016 3:35 PM
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');
include ("../ewcfg10.php");
define ("DB_HOST_NAME", EW_CONN_HOST);
define ("PORT_NUM", EW_CONN_PORT);
define ("DB_NAME", EW_CONN_DB);
define ("DB_USER_NAME", EW_CONN_USER);
define ("DB_PASSWORD", EW_CONN_PASS);

include ("DAO.php");
include ("payrollPrinting.php");
include ("psgcclass.php");

$region = $_REQUEST['region'];
$province = $_REQUEST['province'];
$city = $_REQUEST['city'];
$brgy = $_REQUEST['brgy'];
$year = $_REQUEST['year'];
$quarter = $_REQUEST['quarter'];
//$payrollPrinting = new payrollPrinting('170000000','174000000','174001000','174001045',2016,1,'josef friedrich baldo','','');
$payrollPrinting = new payrollPrinting($region,$province,$city,$brgy,$year,$quarter);

echo '<pre>';
print_r($payrollPrinting->renderPayroll('','','1',100,75));
echo '</pre>';