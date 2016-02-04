<?php 
include "DAO.php";
include "insertparse.excel.php";
include "cfg.php";
define ("DB_HOST_NAME", EW_CONN_HOST);
define ("PORT_NUM", EW_CONN_PORT);
define ("DB_NAME", EW_CONN_DB);
define ("DB_USER_NAME", EW_CONN_USER);
define ("DB_PASSWORD", EW_CONN_PASS);

$InsertParseDAO = new InsertParseDAO('2014/8/5','174004005-8012-0000','1','1','2014/8/5','MARTINA','M.','LABAGUIS','jr','1922/1/30','1','','170000000','174000000','174001000','174001045','a','999');

echo "<pre>";
print_r($InsertParseDAO->_InsertParse());
echo "</pre>";

?>

