<?php
include "cfg.php";
include "DAO.php";
define ("DB_HOST_NAME", EW_CONN_HOST);
define ("PORT_NUM", EW_CONN_PORT);
define ("DB_NAME", EW_CONN_DB);
define ("DB_USER_NAME", EW_CONN_USER);
define ("DB_PASSWORD", EW_CONN_PASS);

class GeneratePensionerID extends DAO{
	function retListing4IDs(){
		$sql = "SELECT psgc_brgy,Birthdate,SeniorID FROM tbl_pensioner WHERE PensionerID IS NULL OR PensionerID = ''";
		$this->openDB();
		$this->prepareQuery($sql);
		$result = $this->executeQuery();
		$recordlist = array();
		$this->closeDB();
		foreach($result as $i=>$rowData){
			$recordlist[$i] = array(
				"psgc_brgy"=>$rowData['psgc_brgy'],
				"Birthdate"=>$rowData['Birthdate'],
				"SeniorID"=>$rowData['SeniorID'],
				"PensionerID"=>substr($rowData['psgc_brgy'],0,2) . substr($rowData['psgc_brgy'],-3) . date("ymd",strtotime($rowData['Birthdate'])) . "-" . $rowData['SeniorID']
			);
		}
		return $recordlist;
	}
	function updatePensionerID($SeniorID,$PensionerID,$codeGen){
		$sql = "UPDATE tbl_pensioner SET PensionerID=:PensionerID WHERE SeniorID=:SeniorID AND PensionerID IS NULL or PensionerID = '' AND codeGen = :codeGen";
		$this->openDB();
		$this->prepareQuery($sql);
		$this->bindQueryParam(':PensionerID',$PensionerID);
		$this->bindQueryParam(':SeniorID',$SeniorID);
		$this->bindQueryParam(':codeGen',$codeGen);
		$this->beginTrans();
		$result = $this->executeUpdate();
		if ($result){
			$this->commitTrans();
			$resultValue = "1";
		} else {
			$this->rollbackTrans();
			$resultValue = "0";
		}
		$this->closeDB();
		return $resultValue;
	}
}

$GeneratePensionerID = new GeneratePensionerID();
$initData = $GeneratePensionerID->retListing4IDs();
echo "<pre>";
foreach($initData as $listData):
// print_r($GeneratePensionerID->updatePensionerID($listData['SeniorID'],$listData['PensionerID']));
$result = $GeneratePensionerID->updatePensionerID($listData['SeniorID'],$listData['PensionerID'],$_REQUEST['codegen']);
$result += 1;
endforeach;
echo "IDs successfully generated!" . "<br>";
echo "Download generated file" . "<br>";
echo "<form id=\"downloadpensioner\" method=\"get\" action=\"downloadpensionerid.php\">";
echo "<input type=\"hidden\" value=\"".$_REQUEST['codegen']."\" name=\"codegenids\">" . "<br>";
echo "<input type=\"submit\" value=\"Download\">";
echo "</form>";
echo "</pre>";
?>