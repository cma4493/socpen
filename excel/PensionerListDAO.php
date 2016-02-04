<?php 
class PensionerListDAO extends DAO{
	function retList($codeGen){
		$sql = "SELECT PensionerID,hh_id,firstname,middlename,lastname,extname,Birthdate FROM tbl_pensioner WHERE codeGen=:codeGen";
		$this->openDB();
		$this->prepareQuery($sql);
		$this->bindQueryParam(':codeGen',$codeGen);
		$result = $this->executeQuery();
		$recordlist = array();
		$this->closeDB();
		foreach($result as $i=>$rowData){
			$recordlist[$i] = array(
				"PensionerID"=>$rowData['PensionerID'],
				"hh_id"=>$rowData['hh_id'],
				"firstname"=>$rowData['firstname'],
				"middlename"=>$rowData['middlename'],
				"lastname"=>$rowData['lastname'],
				"extname"=>$rowData['extname'],
				"Birthdate"=>$rowData['Birthdate']
			);
		}
		return $recordlist;
	}
}
?>