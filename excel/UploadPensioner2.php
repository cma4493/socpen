<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');
set_time_limit (900000);
include "cfg.php";
include "DAO.php";
include "insertparse.excel.php";
// include "simplexlsx.class.php";
include "reader.php";
define ("DB_HOST_NAME", EW_CONN_HOST);
define ("PORT_NUM", EW_CONN_PORT);
define ("DB_NAME", EW_CONN_DB);
define ("DB_USER_NAME", EW_CONN_USER);
define ("DB_PASSWORD", EW_CONN_PASS);
$InsertParseDAO = new InsertParseDAO('','','','','','','','','','','','','','','','','','');
?>
<p><span class="genericpage" style="white-space: nowrap;">:: Merger File Uploader</span></p>
<table><tr><td>
<div id="datacontent">
	<br><br>
	<font color="red">
		<b>NOTE:</b><br>
		This module uses the <a href="merger_template/merger_template.xls" target="_blank">SPIS Merger Template File</a> in excel format (xls, xlsx).Please download the Merger File for you to upload the Pensioners. Arrange your data set following
		the merger file columns and make sure that it is located on the first worksheet. Upload your accomplished
		merger file on the form below and wait for the summary report. the larger your file the longer it takes to process and validate
		your entries.
	</font>,<br><br><br>
<form method="post" enctype="multipart/form-data">
Target File: <input type="file" name="file"  />
<input type="submit" value="Start Processing" />
</form>
<?php //echo CurrentUserID(); ?>
</div>
<?php
$uploaderlog="";
if((!empty($_FILES["file"]))) { // && ($_FILES['file']['error'] == 0)
	
	$limitSize	= 20000000; //(20 Mb) - Maximum size of uploaded file, change it to any size you want
	$fileName	= basename($_FILES['file']['name']);
	$fileSize	= $_FILES["file"]["size"];
	$fileExt	= substr($fileName, strrpos($fileName, '.') + 1);
	$codeGen 	= date("His");
	
	if(($fileExt == "xlsx" ) && ($fileSize < $limitSize)){ //for xlsx files
		
		
			//====begin for xlsx files
			require_once "simplexlsx.class.php"; // class files for xlsx
			$getWorksheetName = array();
			$xlsx = new SimpleXLSX( $_FILES['file']['tmp_name'] );
			$getWorksheetName = $xlsx->getWorksheetName();
			//====end for xlsx files
		
		
		//display file information
		echo '<hr><div id="datacontent">';
		echo '<h4>File Info:</h1><ul><li><b>File Name:  </b>'.$fileName.'</li>';
		echo '<li><b>File Size:</b> '.($fileSize/1000).' kb</li></li></ul><hr>';
		echo '<div id="datacontent">';
		
		for($j=1;$j <= 1 ;$j++){ //process first sheet only
			echo '<h4>Worksheet Name: '.$getWorksheetName[$j-1].'</h1>';
			$htmltable = '<table border="1" id="xlsxTable">';
			list($cols,) = $xlsx->dimension($j);
			$cols = 17; //force checking to 17 columns only
			//Prepare table
			//process column headers
			$ch[] = array();
			$total_rows=0;
			$total_valid =0;
			$total_existing =0;
			$total_saved =0;
			$total_savingerror =0;
			$total_incomplete =0;
			$total_witherrors =0;
			foreach( $xlsx->rows($j) as $k => $r) { //process first row
				if ($k == 0){
					$htmltable .= "<thead><tr>";
					$failedfields = 0;
					for( $i = 0; $i < $cols; $i++){
						//Display column headers
						
						// $current_column_value = trim(strtoupper($r[$i]));
						$current_column_value = $r[$i];
						//begin checking column titles
						if($i == 0 && $current_column_value != "inclusion_date"){ $failedfields = $failedfields + 1 ; }
						if($i == 1 && $current_column_value != "hh_id"){ $failedfields = $failedfields + 1 ; }
						if($i == 2 && $current_column_value != "osca_id"){ $failedfields = $failedfields + 1 ; }
						if($i == 3 && $current_column_value != "osca_place"){ $failedfields = $failedfields + 1 ; }
						if($i == 4 && $current_column_value != "osca_date"){ $failedfields = $failedfields + 1 ; }
						if($i == 5 && $current_column_value != "first_name"){ $failedfields = $failedfields + 1 ; }
						if($i == 6 && $current_column_value != "middle_name"){ $failedfields = $failedfields + 1 ; }
						if($i == 7 && $current_column_value != "last_name"){ $failedfields = $failedfields + 1 ; }
						if($i == 8 && $current_column_value != "ext_name"){ $failedfields = $failedfields + 1 ; }
						if($i == 9 && $current_column_value != "birthdate"){ $failedfields = $failedfields + 1 ; }
						if($i == 10 && $current_column_value != "sex"){ $failedfields = $failedfields + 1 ; }
						if($i == 11 && $current_column_value != "marital_status"){ $failedfields = $failedfields + 1 ; }
						if($i == 12 && $current_column_value != "region_psgc"){ $failedfields = $failedfields + 1 ; }
						if($i == 13 && $current_column_value != "province_psgc"){ $failedfields = $failedfields + 1 ; }
						if($i == 14 && $current_column_value != "municipality_psgc"){ $failedfields = $failedfields + 1 ; }
						if($i == 15 && $current_column_value != "brgy_psgc"){ $failedfields = $failedfields + 1 ; }
						if($i == 16 && $current_column_value != "street_address"){ $failedfields = $failedfields + 1 ; }
						//end checking column titles
						$ch[$i] = $current_column_value; //save current column name for user in error messages
						$htmltable .=  '<td><b>' . $current_column_value . '</b></td>';
					}
					$htmltable .=  "<th><b>Remarks</b></th><th><b>Excel Row No.</b></th></tr></thead>";
				}
				
			}
			// echo $failedfields . "<br>";
		if($failedfields ==0){
			//echo $htmltable;
			$uploaderlog = $htmltable;
			//process each record
			foreach( $xlsx->rows($j) as $l => $m) {
				flush();
				sleep(1);
				
				if ($l >= 1){
					$field_error = ""; //contains remarks on field errors
					// $swi_ind_total=0;
					$tablerow =  '<tr>';
					$swivars[] = array();
					for( $n = 0; $n < $cols; $n++){
						//Display data
						//====================begin validating values=========================
						$celldata = $m[$n];
						
						if($n ==0) { //date checking
							
							//$celldatanew = date("Y-m-d",ExcelToPHP($celldata));
							$celldatanew = date("Y-m-d",strtotime($celldata)); //for normal date only
							if($celldatanew  == "1970-01-01"){
								
								$celldatanew2 = date("Y-m-d",ExcelToPHP($celldata));
								if(!is_numeric($celldata)){
									$celldatanew = $celldata;
									$field_error .= "<font color='red'>[" . $ch[$n] ."]invalid</font>,";
								}else{
									$celldatanew = $celldatanew2;
								}	
							
							}
							$celldata = $celldatanew;
							
						}
						
						if($n ==9) { //date checking
							
							//$celldatanew = date("Y-m-d",ExcelToPHP($celldata));
							$celldatanew = date("Y-m-d",strtotime($celldata)); //for normal date only
							if($celldatanew  == "1970-01-01"){
								
								$celldatanew2 = date("Y-m-d",ExcelToPHP($celldata));
								if(!is_numeric($celldata)){
									$celldatanew = $celldata;
									$field_error .= "<font color='red'>[" . $ch[$n] ."]invalid</font>,";
								}else{
									$celldatanew = $celldatanew2;
								}	
							
							}
							$celldata = $celldatanew;
							
						}
						
						//====================end validating values========================
						$swivars[$n] = $celldata;
						$tablerow .=  '<td>'. $celldata . '</td>';
						
					}
					$bindkeys = "";
					if($field_error == ""){ // if no error on all fields
							$field_error = "<font color='green'>VALID ENTRY</font>,";
							$total_valid = $total_valid + 1;
							// $bindkeys = "<tr bgcolor='green'>";
							// $swidup = new BeneficiaryDAO();
								
							foreach($swivars as $varid => $varvalue){ //swi vars enumeration
								$bindkeys .= "<td>" . $varvalue . "</td>";
							}
							// echo "<font color='red'>(existing" . $swivars[5] . "," . $swivars[6] . "," . $swivars[8] . "," . $swivars[9] . "," . $swivars[10] . ")</font>";
							if($InsertParseDAO->entryChecker($swivars[5],$swivars[6],$swivars[7],$swivars[8],$swivars[9],$swivars[10])){ //duplicate swi checker
								$field_error .= "<font color='red'>(existing)</font>";
								$total_existing = $total_existing + 1;
							}else{
								
								// $switrans = new BeneficiaryDAO();
								// $CurrentUser = CurrentUserID();
								$InsertParseDAO23 = new InsertParseDAO($swivars[0],$swivars[1],$swivars[2],$swivars[3],$swivars[4],$swivars[5],$swivars[6],$swivars[7],$swivars[8],$swivars[9],$swivars[10],$swivars[11],$swivars[12],$swivars[13],$swivars[14],$swivars[15],$swivars[16],$codeGen);
								$swisaved = $InsertParseDAO23->_InsertParse();
								
								if($swisaved == true){
									$field_error .= "<font color='green'>(saved)</font>";
									$total_saved = $total_saved +1 ;
								}else{
									$field_error .= "<font color='red'>(error saving / duplicate)</font>";
									$total_savingerror = $total_savingerror +1 ;
								}	
							}	
							//$bindkeys .= "</tr>";
							
					}
					// if($swi_ind_total ==0 || $swi_ind_total ==1){
							// $field_error = "<font color='red'>incomplete data</font>,";
							// $total_incomplete = $total_incomplete + 1;
					// }
					
					$curent_row = $l + 1;
					$tablerow .=  '<td>'. $field_error . '</td><td>' . $curent_row . '</td></tr>';
					
					//echo $tablerow ;
					$uploaderlog .= $tablerow;
					//echo $bindkeys;
					$total_rows=$total_rows + 1; //row counting
				}
				
			}
		}else{
			echo "<font color='red'>Invalid Merger File. Please make sure you followed the <br>
					downloadable <a href='merger_file/merger_template.xls'>Merger Template</a> and <br>
					had a all contents on the first worksheet.</font>,";
		}	
			
			//echo '</table>';
			$uploaderlog .= '</table>';
			$mylogfile = "merger_files/" . time() . "-" . "999" . "-" . $fileName . ".xls"; // change 999 to CurrentUserID()
			CreateLog($mylogfile,$uploaderlog);
			echo "<br>Total rows = " . $total_rows;
			echo "<br>Total valid rows = " . $total_valid ;
			echo "<br>Total existing rows = " . $total_existing ;
			echo "<br>Total saved rows = " . $total_saved ;
			echo "<br>Total rows with saving error = " . $total_savingerror ;
			echo "<form id=\"generator\" method=\"get\" action=\"generatepensionerid.php\">";
			echo "<input type=\"hidden\" name=\"codegen\" value=\"".$codeGen."\">";
			echo "<input type=\"submit\" value=\"Generate IDs\">";
			echo "</form>";
			// echo "<br>Total incomplete rows = " . $total_incomplete ;
			// echo "<br>Total rows with invalid entries = " . ($total_rows - ($total_valid )) ; // + $total_incomplete
			echo '<br><br>Done [<a href="' . $mylogfile . '" target="_blank">Download Log</a>]...';
			
			
		}
		echo '</div>';
//=====================================================================================
//=====================================================================================
//=====================================================================================
//=====================================================================================
//=====================================================================================
//=====================================================================================

	}elseif(($fileExt == "xls" ) && ($fileSize < $limitSize)){ //for xls files
			
			require_once 'reader.php';
			$data = new Spreadsheet_Excel_Reader();
			// Set output Encoding.
			$data->setOutputEncoding('CP1251');
			$data->read($_FILES['file']['tmp_name']);
			$cols = 17; //force checking to 31 columns only
			
			//display file information
			echo '<hr><div id="datacontent">';
			echo '<h4>File Info:</h1><ul><li><b>File Name:  </b>'.$fileName.'</li>';
			echo '<li><b>File Size:</b> '.($fileSize/1000).' kb</li></li></ul><hr>';
			echo '<div id="datacontent">';
			echo '<h4>Worksheet Name: Sheet1</h1>';
			
			$htmltable = "<table border='1'>"; //used for xls value matrix contruction
	
			//loop on all check column headers
			$ch[] = array();
			$total_rows=0;
			$total_valid =0;
			$total_existing =0;
			$total_saved =0;
			$total_savingerror =0;
			$total_incomplete =0;
			$total_witherrors =0;
			
			for ($i = 1; $i <= 1 ; $i++) {
				$failedfields = 0;
				$htmltable .= "<thead><tr>";
				for ($j = 1; $j <= $cols; $j++) { //$data->sheets[0]['numCols']
						
						$current_column_value = $data->sheets[0]['cells'][$i][$j];
						//begin checking column titles
						if($j == 1 && $current_column_value != "inclusion_date"){ $failedfields = $failedfields + 1 ; }
						if($j == 2 && $current_column_value != "hh_id"){ $failedfields = $failedfields + 1 ; }
						if($j == 3 && $current_column_value != "osca_id"){ $failedfields = $failedfields + 1 ; }
						if($j == 4 && $current_column_value != "osca_place"){ $failedfields = $failedfields + 1 ; }
						if($j == 5 && $current_column_value != "osca_date"){ $failedfields = $failedfields + 1 ; }
						if($j == 6 && $current_column_value != "first_name"){ $failedfields = $failedfields + 1 ; }
						if($j == 7 && $current_column_value != "middle_name"){ $failedfields = $failedfields + 1 ; }
						if($j == 8 && $current_column_value != "last_name"){ $failedfields = $failedfields + 1 ; }
						if($j == 9 && $current_column_value != "ext_name"){ $failedfields = $failedfields + 1 ; }
						if($j == 10 && $current_column_value != "birthdate"){ $failedfields = $failedfields + 1 ; }
						if($j == 11 && $current_column_value != "sex"){ $failedfields = $failedfields + 1 ; }
						if($j == 12 && $current_column_value != "marital_status"){ $failedfields = $failedfields + 1 ; }
						if($j == 13 && $current_column_value != "region_psgc"){ $failedfields = $failedfields + 1 ; }
						if($j == 14 && $current_column_value != "province_psgc"){ $failedfields = $failedfields + 1 ; }
						if($j == 15 && $current_column_value != "municipality_psgc"){ $failedfields = $failedfields + 1 ; }
						if($j == 16 && $current_column_value != "brgy_psgc"){ $failedfields = $failedfields + 1 ; }
						if($j == 17 && $current_column_value != "street_address"){ $failedfields = $failedfields + 1 ; }
						//end checking column titles
						$ch[$j] = $current_column_value; //save current column name for user in error messages
						$htmltable .= '<td><b>' . $current_column_value . '</b></td>';
					
				}
				$htmltable .= "<th><b>Remarks</b></th><th><b>Excel Row No.</b></th></tr></thead>";
			}
			// echo $failedfields . "<br>";
		if($failedfields ==0){
			//echo $htmltable;
			$uploaderlog .= $htmltable;
			//loop on all records
			for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
				flush();
				sleep(1);
				$field_error = ""; //contains remarks on field errors
				$swi_ind_total=0;
				
				$tablerow = "<tr>";
				$swivars[] = array();
				for ($n = 1; $n <= $cols; $n++) {
					
					//Display data
						//====================begin validating values=========================
						$celldata = $data->sheets[0]['cells'][$i][$n];
						
						if($n ==1) { //date checking
							
							//$celldatanew = date("Y-m-d",ExcelToPHP($celldata));
							$celldatanew = date("Y-m-d",strtotime($celldata)); //for normal date only
							if($celldatanew  == "1970-01-01"){
								
								$celldatanew2 = date("Y-m-d",ExcelToPHP($celldata));
								if(!is_numeric($celldata)){
									$celldatanew = $celldata;
									$field_error .= "<font color='red'>[" . $ch[$n] ."]invalid</font>,";
								}else{
									$celldatanew = $celldatanew2;
								}	
							
							}
							$celldata = $celldatanew;
							
						}
						
						if($n ==10) { //date checking
							
							//$celldatanew = date("Y-m-d",ExcelToPHP($celldata));
							$celldatanew = date("Y-m-d",strtotime($celldata)); //for normal date only
							if($celldatanew  == "1970-01-01"){
								
								$celldatanew2 = date("Y-m-d",ExcelToPHP($celldata));
								if(!is_numeric($celldata)){
									$celldatanew = $celldata;
									$field_error .= "<font color='red'>[" . $ch[$n] ."]invalid</font>,";
								}else{
									$celldatanew = $celldatanew2;
								}	
							
							}
							$celldata = $celldatanew;
							
						}
						
						//====================end validating values========================
						$swivars[$n] = $celldata;
						$tablerow .= '<td>'. $celldata . '</td>';
					
				}
					
					$bindkeys = "";
					if($field_error == "" ){ // if no error on all fields  // && $swi_ind_total !=0
							$field_error = "<font color='green'>VALID ENTRY</font>,";
							$total_valid = $total_valid + 1;
					
							//$bindkeys = "<tr bgcolor='green'>";
							// $swidup = new BeneficiaryDAO();
								
							foreach($swivars as $varid => $varvalue){ //swi vars enumeration
								$bindkeys .= "<td>" . $varvalue . "</td>";
							}
							// echo "<font color='red'>(existing" . $swivars[1] . "," . $swivars[31] . ")</font>";
							if($InsertParseDAO->entryChecker($swivars[6],$swivars[7],$swivars[8],$swivars[9],$swivars[10],$swivars[11])){ //duplicate swi checker
								$field_error .= "<font color='red'>(existing)</font>";
								$total_existing = $total_existing + 1;
							}else{
								
								// $switrans = new BeneficiaryDAO();
								// $CurrentUser = CurrentUserID();
								$InsertParseDAO23 = new InsertParseDAO($swivars[1],$swivars[2],$swivars[3],$swivars[4],$swivars[5],$swivars[6],$swivars[7],$swivars[8],$swivars[9],$swivars[10],$swivars[11],$swivars[12],$swivars[13],$swivars[14],$swivars[15],$swivars[16],$swivars[17],$codeGen);
								$swisaved = $InsertParseDAO23->_InsertParse();
								
								if($swisaved == true){
									$field_error .= "<font color='green'>(saved)</font>";
									$total_saved = $total_saved +1 ;
								}else{
									$field_error .= "<font color='red'>(error saving / duplicate)</font>";
									$total_savingerror = $total_savingerror +1 ;
								}	
							}	
							//$bindkeys .= "</tr>";
					}
					// if($swi_ind_total ==0 || $swi_ind_total ==1){
							// $field_error = "<font color='red'>incomplete swi</font>,";
							// $total_incomplete = $total_incomplete + 1;
					// }
					$curent_row = $i;
					$tablerow .= '<td>'. $field_error . '</td><td>' . $curent_row . '</td></tr>';
					$uploaderlog .= $tablerow;
					$total_rows=$total_rows + 1; //row counting
			}
			$uploaderlog .= "</table>";
			
			$mylogfile = "merger_files/" . time() . "-" . "999" . "-" . $fileName . ".xls";
			CreateLog($mylogfile,$uploaderlog);
			echo "<br>Total rows = " . $total_rows;
			echo "<br>Total valid rows = " . $total_valid ;
			echo "<br>Total existing rows = " . $total_existing ;
			echo "<br>Total saved rows = " . $total_saved ;
			echo "<br>Total rows with saving error = " . $total_savingerror ;
			echo "<form id=\"generator\" method=\"get\" action=\"generatepensionerid.php\">";
			echo "<input type=\"hidden\" name=\"codegen\" value=\"".$codeGen."\">";
			echo "<input type=\"submit\" value=\"Generate IDs\">";
			echo "</form>";
			// echo "<br>Total incomplete rows = " . $total_incomplete ;
			// echo "<br>Total rows with invalid entries = " . ($total_rows - ($total_valid )) ; // + $total_incomplete
			echo '<br><br>Done [<a href="' . $mylogfile . '" target="_blank">Download Log</a>]...';
			}else{
			echo "<font color='red'>Invalid Merger File. Please make sure you followed the <br>
					downloadable <a href='merger_file/merger_template.xls'>Merger Template</a> and <br>
					had a all contents on the first worksheet.</font>,";
		}	
	}else{
		echo '<script>alert("Sorry, only [xls] and [xlsx] SWI Merger Template files under '.($limitSize/1000000).' Mb are allowed!")</script>';
	}
}
?>
</td></tr></table>
<?php //function goes here
function checkDateFormat($date){
  //match the format of the date
  if (preg_match ("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $date, $parts)){
    //check weather the date is valid of not
        if(checkdate($parts[2],$parts[3],$parts[1]))
          return true;
        else
         return false;
  }
  else
    return false;
}

function is_date( $str ) { //check if valid date
    try {
        $dt = new DateTime( trim($str) );
    }
    catch( Exception $e ) {
        return false;
    }
    $month = $dt->format('m');
    $day = $dt->format('d');
    $year = $dt->format('Y');
    if( checkdate($month, $day, $year) ) {
        return true;
    }
    else {
        return false;
    }
}

function inRange($number, $a, $b){ //check number within range
   $min = min($a, $b);
   $max = max($a, $b);
   if ($number < $min) return FALSE;
   if ($number > $max) return FALSE;
   return TRUE;
}

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
function CreateLog($filename,$filecontent){
$myFile = $filename;
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $filecontent);
fclose($fh);		
}		
?>