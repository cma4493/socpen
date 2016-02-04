<?php
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
set_time_limit (900000);
include "cfg.php";
include "DAO.php";
include "insertparse.excel.php";
include "simplexlsx.class.php";
include "reader.php";
define ("DB_HOST_NAME", EW_CONN_HOST);
define ("PORT_NUM", EW_CONN_PORT);
define ("DB_NAME", EW_CONN_DB);
define ("DB_USER_NAME", EW_CONN_USER);
define ("DB_PASSWORD", EW_CONN_PASS);

function ExcelToPHP($dateValue = 0, $ExcelBaseDate=0) {
    if ($ExcelBaseDate == 0) {
        $myExcelBaseDate = 25569;
        //  Adjust for the spurious 29-Feb-1900 (Day 60)
        if ($dateValue < 60) {
            --$myExcelBaseDate;
        }
    } else {
        $myExcelBaseDate = 24107;
    }

    // Perform conversion
    if ($dateValue >= 1) {
        $utcDays = $dateValue - $myExcelBaseDate;
        $returnValue = round($utcDays * 86400);
        if (($returnValue <= PHP_INT_MAX) && ($returnValue >= -PHP_INT_MAX)) {
            $returnValue = (integer) $returnValue;
        }
    } else {
        $hours = round($dateValue * 24);
        $mins = round($dateValue * 1440) - round($hours * 60);
        $secs = round($dateValue * 86400) - round($hours * 3600) - round($mins * 60);
        $returnValue = (integer) gmmktime($hours, $mins, $secs);
    }

    // Return
    return $returnValue;
}

if (isset($_FILES['file'])) {
	
	$limitSize	= 20000000; //(20 Mb) - Maximum size of uploaded file, change it to any size you want
	$fileName	= basename($_FILES['file']['name']);
	$fileSize	= $_FILES["file"]["size"];
	$fileExt	= substr($fileName, strrpos($fileName, '.') + 1);
	$codeGen 	= date("His");
	
	if(($fileExt == "xlsx" ) && ($fileSize < $limitSize)){ // xlsx
		echo "<pre>";
		echo "limitSize: " . $limitSize . "<br>";
		echo "fileName: " . $fileName . "<br>";
		echo "fileSize: " . $fileSize . "<br>";
		echo "fileExt: " . $fileExt . "<br>";
	
		$xlsx = new SimpleXLSX( $_FILES['file']['tmp_name'] );
		
		// list($cols,) = $xlsx->dimension(); // use to count columns and rows
		$cols = 17;
		$columnHeaders[] = array();
		$totalSaved = 0;
		$totalSaveError = 0;
		$discarder = 0;
		$totalRows = 0;
		
		// echo "<pre>";
		// echo $cols . "<br>";
		// echo "</pre>";
		
		foreach($xlsx->rows(1) as $k=>$r) {
			if ($k == 0){ // process first row only (column headers)
				$failedfields = 0;
				// echo "<table border=\"1px\">";
				// echo "<tr>";
				for( $i = 0; $i < $cols; $i++){
					$currentColumnHeader = $r[$i];
					
					if ($i == 0 && $currentColumnHeader != 'inclusion_date') { $failedfields += 1; }
					if ($i == 1 && $currentColumnHeader != 'hh_id') { $failedfields += 1; }
					if ($i == 2 && $currentColumnHeader != 'osca_id') { $failedfields += 1; }
					if ($i == 3 && $currentColumnHeader != 'osca_place') { $failedfields += 1; }
					if ($i == 4 && $currentColumnHeader != 'osca_date') { $failedfields += 1; }
					if ($i == 5 && $currentColumnHeader != 'first_name') { $failedfields += 1; }
					if ($i == 6 && $currentColumnHeader != 'middle_name') { $failedfields += 1; }
					if ($i == 7 && $currentColumnHeader != 'last_name') { $failedfields += 1; }
					if ($i == 8 && $currentColumnHeader != 'ext_name') { $failedfields += 1; }
					if ($i == 9 && $currentColumnHeader != 'birthdate') { $failedfields += 1; }
					if ($i == 10 && $currentColumnHeader != 'sex') { $failedfields += 1; }
					if ($i == 11 && $currentColumnHeader != 'marital_status') { $failedfields += 1; }
					if ($i == 12 && $currentColumnHeader != 'region_psgc') { $failedfields += 1; }
					if ($i == 13 && $currentColumnHeader != 'province_psgc') { $failedfields += 1; }
					if ($i == 14 && $currentColumnHeader != 'municipality_psgc') { $failedfields += 1; }
					if ($i == 15 && $currentColumnHeader != 'brgy_psgc') { $failedfields += 1; }
					if ($i == 16 && $currentColumnHeader != 'street_address') { $failedfields += 1; }
					
					$columnHeaders[$i] = $currentColumnHeader;
					
					// echo "<td>" . $currentColumnHeader . "</td>";
					
				}
				// echo "</tr>";
				
			}
		}
		// echo "<pre>";
		// print_r($failedfields);
		// echo "</pre>";
		
		if ($failedfields == 0){
			// validate values/contents of columns and rows
			foreach($xlsx->rows(1) as $x=>$y) {
				if ($x >= 1){
					$fielderror = "";
					$columnContent[] = array();
					// echo "<tr>";
					for( $n = 0; $n < $cols; $n++){
						$currentColumnContent = $y[$n];
						
						if ($n == 0){
							$celldatanew = date("Y-m-d",strtotime($currentColumnContent)); //for normal date only
							if($celldatanew  == "1970-01-01"){
								
								$celldatanew2 = date("Y-m-d",ExcelToPHP($currentColumnContent));
								if(!is_numeric($currentColumnContent)){
									$celldatanew = $currentColumnContent;
									$fielderror .= "error " . $columnHeaders[$n] . "...<br>";
								}else{
									$celldatanew = $celldatanew2;
								}	
							
							}
							$currentColumnContent = $celldatanew;
						}
						if ($n == 9){
							$celldatanew = date("Y-m-d",strtotime($currentColumnContent)); //for normal date only
							if($celldatanew  == "1970-01-01"){
								
								$celldatanew2 = date("Y-m-d",ExcelToPHP($currentColumnContent));
								if(!is_numeric($currentColumnContent)){
									$celldatanew = $currentColumnContent;
									$fielderror .= "error " . $columnHeaders[$n] . "...<br>";
								}else{
									$celldatanew = $celldatanew2;
								}	
							
							}
							$currentColumnContent = $celldatanew;
						}
						$columnContent[$n] = $currentColumnContent;
						
						
						// echo "<td>" . $columnContent[$n] . "</td>";
						
						
					}
					// echo "</tr>";
					// echo "<pre>";
					// print_r($columnContent);
					// echo "</pre>";
					
					if ($fielderror == "") { // if no error on all fields
						$fielderror = "Valid entries...<br>";
						// echo "<pre>";
						// print_r($columnContent[3]);
						// echo "</pre>";
						$InsertParsedData = new InsertParseDAO($columnContent[0],$columnContent[1],$columnContent[2],$columnContent[3],
												$columnContent[4],utf8_decode($columnContent[5]),utf8_decode($columnContent[6]),utf8_decode($columnContent[7]),utf8_decode($columnContent[8]),$columnContent[9],
												$columnContent[10],$columnContent[11],$columnContent[12],$columnContent[13],$columnContent[14],$columnContent[15],
												$columnContent[16],$codeGen);
						$insertResult = $InsertParsedData->_InsertParse();
						
						if ($insertResult == 1){ // count success saved
							$fielderror .= "saved<br>";
							$totalSaved += 1;
						} else {
							$fielderror .= "error saving<br>";
							$totalSaveError += 1;
						}
					} else {
						$fielderror .= "Invalid entries...<br>";
					}
					$totalRows += 1; // row counting
				}
			}
			// echo "</table>";
		}
		echo "==============================================================<br>";
		echo "Parse Result:<br>";
		echo "Total Rows: " . $totalRows . "<br>";
		echo "Total Saved: " . $totalSaved . "<br>";
		echo "Total Errors: " . $totalSaveError . "<br>";
		// echo "messages: " . $fielderror . "<br>";
		echo "<form id=\"generator\" method=\"get\" action=\"generatepensionerid.php\">";
		echo "<input type=\"hidden\" name=\"codegen\" value=\"".$codeGen."\">";
		echo "<input type=\"submit\" value=\"Generate IDs\">";
		echo "</form>";
		echo "</pre>";
	// ./xlsx
	} elseif(($fileExt == "xls" ) && ($fileSize < $limitSize)){ // xls
		echo "<pre>";
		echo "limitSize: " . $limitSize . "<br>";
		echo "fileName: " . $fileName . "<br>";
		echo "fileSize: " . $fileSize . "<br>";
		echo "fileExt: " . $fileExt . "<br>";
	
		$data = new Spreadsheet_Excel_Reader();
		// Set output Encoding.
		$data->setOutputEncoding('CP1251');
		$data->read($_FILES['file']['tmp_name']);
		
		$cols = 17;
		$columnHeaders[] = array();
		$totalSaved = 0;
		$totalSaveError = 0;
		$discarder = 0;
		$totalRows = 0;
		
		for($d = 1; $d <= 1; $d++) {
				$failedfields = 0;
				
				for( $l = 1; $l <= $cols; $l++){
					$currentColumnHeader = $data->sheets[0]['cells'][$d][$l];
					// echo "<pre>";
					// print_r($currentColumnHeader);
					// echo "</pre>";
					if ($i == 1 && $currentColumnHeader != 'inclusion_date') { $failedfields += 1; }
					if ($i == 2 && $currentColumnHeader != 'hh_id') { $failedfields += 1; }
					if ($i == 3 && $currentColumnHeader != 'osca_id') { $failedfields += 1; }
					if ($i == 4 && $currentColumnHeader != 'osca_place') { $failedfields += 1; }
					if ($i == 5 && $currentColumnHeader != 'osca_date') { $failedfields += 1; }
					if ($i == 6 && $currentColumnHeader != 'first_name') { $failedfields += 1; }
					if ($i == 7 && $currentColumnHeader != 'middle_name') { $failedfields += 1; }
					if ($i == 8 && $currentColumnHeader != 'last_name') { $failedfields += 1; }
					if ($i == 9 && $currentColumnHeader != 'ext_name') { $failedfields += 1; }
					if ($i == 10 && $currentColumnHeader != 'birthdate') { $failedfields += 1; }
					if ($i == 11 && $currentColumnHeader != 'sex') { $failedfields += 1; }
					if ($i == 12 && $currentColumnHeader != 'marital_status') { $failedfields += 1; }
					if ($i == 13 && $currentColumnHeader != 'region_psgc') { $failedfields += 1; }
					if ($i == 14 && $currentColumnHeader != 'province_psgc') { $failedfields += 1; }
					if ($i == 15 && $currentColumnHeader != 'municipality_psgc') { $failedfields += 1; }
					if ($i == 16 && $currentColumnHeader != 'brgy_psgc') { $failedfields += 1; }
					if ($i == 17 && $currentColumnHeader != 'street_address') { $failedfields += 1; }
					
					$columnHeaders[$l] = $currentColumnHeader;
				}
		}
		// echo "<pre>";
		// print_r($failedfields);
		// echo "</pre>";
		
		if ($failedfields == 0){
			// validate values/contents of columns and rows
			// echo "<pre>";
			// print_r($data->sheets[0]['numRows']);
			// echo "</pre>";
			for($h = 2; $h <= $data->sheets[0]['numRows']; $h++) {
					$fielderror = "";
					$columnContent[] = array();
					
					for( $g = 1; $g <= $cols; $g++){
						$currentColumnContent = $data->sheets[0]['cells'][$h][$g];
						// echo "<pre>";
						// print_r($currentColumnContent);
						// echo "</pre>";
						
						if ($g == 10){
							$celldatanew = date("Y-m-d",strtotime($currentColumnContent)); //for normal date only
							if($celldatanew  == "1970-01-01"){
								
								$celldatanew2 = date("Y-m-d",ExcelToPHP($currentColumnContent));
								if(!is_numeric($currentColumnContent)){
									$celldatanew = $currentColumnContent;
									$fielderror .= "error date...<br>";
								}else{
									$celldatanew = $celldatanew2;
								}	
							
							}
							$currentColumnContent = $celldatanew;
						}
						$columnContent[$g] = $currentColumnContent;
					}
					// echo "<pre>";
					// print_r($columnContent);
					// echo "</pre>";
					
					if ($fielderror == "") { // if no error on all fields
						$fielderror = "Valid entries...<br>";
						// echo "<pre>";
						// print_r($columnContent);
						// echo "</pre>";
						$InsertParsedData = new InsertParseDAO($columnContent[1],$columnContent[2],$columnContent[3],$columnContent[4],
												$columnContent[5],utf8_decode(utf8_encode($columnContent[6])),utf8_decode(utf8_encode($columnContent[7])),utf8_decode(utf8_encode($columnContent[8])),utf8_decode(utf8_encode($columnContent[9])),$columnContent[10],
												$columnContent[11],$columnContent[12],$columnContent[13],$columnContent[14],$columnContent[15],$columnContent[16],
												$columnContent[17]);
						$insertResult = $InsertParsedData->_InsertParse();
						// $insertResult = 1;
						
						if ($insertResult == 1){ // count success saved
							$fielderror .= "saved<br>";
							$totalSaved += 1;
						} else {
							$fielderror .= "error saving<br>";
							$totalSaveError += 1;
						}
					} else {
						$fielderror .= "Invalid entries...<br>";
					}
					$totalRows += 1; // row counting
			}
		}
		echo "==============================================================<br>";
		echo "Parse Result:<br>";
		echo "Total Rows: " . $totalRows . "<br>";
		echo "Total Saved: " . $totalSaved . "<br>";
		echo "Total Errors: " . $totalSaveError . "<br>";
		// echo "messages: " . $fielderror . "<br>";
		echo "</pre>";
	} // ./xls
} else {
	echo "<pre>";
	echo "There are no files uploaded, please make sure you have attached the appropriate Excel file";
	echo "</pre>";
}
?>
<pre>
<h1>Upload</h1>
<form id="uploader" method="post" enctype="multipart/form-data">
*.XLSX / *.XLS <input type="file" name="file"  />&nbsp;&nbsp;<input type="submit" value="Parse" />
</form>
</pre>