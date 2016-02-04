<?php
class DashBoardDAO extends DAO {
	
	//Behavior
	public function getReportQueryList(){
		$sql = "SELECT * FROM tbl_reports_list";
		$this->openDB();	
		$this->prepareQuery($sql);	
		$result = $this->executeQuery(); 
		$this->closeDB();

		$reportQueryList = array();
		$trash = array_pop($reportQueryList);
		
		foreach($result as $i=>$row){	
			$reportQueryData = array(
						"report_id"=>$row["report_id"],
						"chart_type"=>$row["chart_type"],
						"chart_vax"=>$row["chart_vax"],
						"chart_width"=>$row["chart_width"],
						"chart_height"=>$row["chart_height"],
						"transpose_result"=>$row["transpose_result"],
						"mycaption"=>$row["mycaption"],
						"query"=>$row["query"],
						"group"=>$row["group"],
						"stack"=>$row["stack"]
					);
				$reportQueryList[$i] = $reportQueryData;
		}

		return $reportQueryList;
	}
}
?>
