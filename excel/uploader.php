<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
set_time_limit (900000);
include "cfg.php";
include "DAO.php";
include "insertparse.excel.php";
include "simplexlsx.class.php";
define ("DB_HOST_NAME", EW_CONN_HOST);
define ("PORT_NUM", EW_CONN_PORT);
define ("DB_NAME", EW_CONN_DB);
define ("DB_USER_NAME", EW_CONN_USER);
define ("DB_PASSWORD", EW_CONN_PASS);
?>
<p><span class="genericpage" style="white-space: nowrap;">:: Merger File Uploader</span></p>
<table><tr><td>
<div id="datacontent">
	<br><br>
	<font color="red">
		<b>NOTE:</b><br>
		This module uses the <a href="merger_template/merger_template.xls" target="_blank">SWI Merger Template File</a> in excel format (xls, xlsx).Please download the Merger File in case you are using Offline SWI data entry tools such as excel. Arrange your data set following
		the merger file columns and make sure that it is located on the first worksheet. Upload your accomplished
		merger file on the form below and wait for the summary report. the larger your file the longer it takes to process and validate
		your SWI entries.
	</font>,<br><br><br>
<form method="post" enctype="multipart/form-data">
Target File: <input type="file" name="file"  />
<input type="submit" value="Start Processing" />
</form>
<?php //echo CurrentUserID(); ?>
</div>
<?php
$uploaderlog="";
if((!empty($_FILES["file"])) && ($_FILES['file']['error'] == 0)) {
	
	$limitSize	= 20000000; //(20 Mb) - Maximum size of uploaded file, change it to any size you want
	$fileName	= basename($_FILES['file']['name']);
	$fileSize	= $_FILES["file"]["size"];
	$fileExt	= substr($fileName, strrpos($fileName, '.') + 1);
	
	if(($fileExt == "xlsx" ) && ($fileSize < $limitSize)){ //for xlsx files
		
		
			//====begin for xlsx files
			// require_once "simplexlsx.class.php"; // class files for xlsx
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
			$cols = 3; //force checking to 31 columns only
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
						if($i == 0 && $current_column_value != "case_subcategory_lower_id"){ $failedfields = $failedfields + 1 ; }
						if($i == 1 && $current_column_value != "case_subcategory_lower_name"){ $failedfields = $failedfields + 1 ; }
						if($i == 2 && $current_column_value != "case_subcategory_id"){ $failedfields = $failedfields + 1 ; }
						// if($i == 3 && $current_column_value != "IA2"){ $failedfields = $failedfields + 1 ; }
						// if($i == 4 && $current_column_value != "IB1"){ $failedfields = $failedfields + 1 ; }
						// if($i == 5 && $current_column_value != "IC1"){ $failedfields = $failedfields + 1 ; }
						// if($i == 6 && $current_column_value != "ID1"){ $failedfields = $failedfields + 1 ; }
						// if($i == 7 && $current_column_value != "ID2"){ $failedfields = $failedfields + 1 ; }
						// if($i == 8 && $current_column_value != "IIA1"){ $failedfields = $failedfields + 1 ; }
						// if($i == 9 && $current_column_value != "IIA2"){ $failedfields = $failedfields + 1 ; }
						// if($i == 10 && $current_column_value != "IIA3"){ $failedfields = $failedfields + 1 ; }
						// if($i == 11 && $current_column_value != "IIB1"){ $failedfields = $failedfields + 1 ; }
						// if($i == 12 && $current_column_value != "IIB2"){ $failedfields = $failedfields + 1 ; }
						// if($i == 13 && $current_column_value != "IIB3"){ $failedfields = $failedfields + 1 ; }
						// if($i == 14 && $current_column_value != "IIC1"){ $failedfields = $failedfields + 1 ; }
						// if($i == 15 && $current_column_value != "IIC2"){ $failedfields = $failedfields + 1 ; }
						// if($i == 16 && $current_column_value != "IID1"){ $failedfields = $failedfields + 1 ; }
						// if($i == 17 && $current_column_value != "IIE1"){ $failedfields = $failedfields + 1 ; }
						// if($i == 18 && $current_column_value != "IIE2"){ $failedfields = $failedfields + 1 ; }
						// if($i == 19 && $current_column_value != "IIE3"){ $failedfields = $failedfields + 1 ; }
						// if($i == 20 && $current_column_value != "IIF1"){ $failedfields = $failedfields + 1 ; }
						// if($i == 21 && $current_column_value != "IIF2"){ $failedfields = $failedfields + 1 ; }
						// if($i == 22 && $current_column_value != "IIG1"){ $failedfields = $failedfields + 1 ; }
						// if($i == 23 && $current_column_value != "IIG2"){ $failedfields = $failedfields + 1 ; }
						// if($i == 24 && $current_column_value != "IIH1"){ $failedfields = $failedfields + 1 ; }
						// if($i == 25 && $current_column_value != "IIH2"){ $failedfields = $failedfields + 1 ; }
						// if($i == 26 && $current_column_value != "IIH3"){ $failedfields = $failedfields + 1 ; }
						// if($i == 27 && $current_column_value != "IIH4"){ $failedfields = $failedfields + 1 ; }
						// if($i == 28 && $current_column_value != "IIH5"){ $failedfields = $failedfields + 1 ; }
						// if($i == 29 && $current_column_value != "SWI_ADMIN"){ $failedfields = $failedfields + 1 ; }
						// if($i == 30 && $current_column_value != "SWI_WAVE"){ $failedfields = $failedfields + 1 ; }
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
					$swi_ind_total=0;
					$tablerow =  '<tr>';
					$swivars[] = array();
					for( $n = 0; $n < $cols; $n++){
						//Display data
						//====================begin validating values=========================
						$celldata = $m[$n];
						
						$swivars[$n] = $celldata;
						$tablerow .=  '<td>'. $celldata . '</td>';
						
					}
					echo "<pre>";
					PRINT_R($swivars[0]);
					echo "</pre>";
					
				}
				
			}
		}else{
			echo "<font color='red'>Invalid Merger File. Please make sure you followed the <br>
					downloadable <a href='merger_file/merger_template.xls'>Merger Template</a> and <br>
					had a all contents on the first worksheet.</font>,";
		}	
			
			//echo '</table>';
			$uploaderlog .= '</table>';
			$mylogfile = "merger_files/" . time() . "-" . "asdasd" . "-" . $fileName . ".xls";
			// CreateLog($mylogfile,$uploaderlog);
			echo "<br>Total rows = " . $total_rows;
			echo "<br>Total valid rows = " . $total_valid ;
			echo "<br>Total existing rows = " . $total_existing ;
			echo "<br>Total saved rows = " . $total_saved ;
			echo "<br>Total rows with saving error = " . $total_savingerror ;
			echo "<br>Total incomplete rows = " . $total_incomplete ;
			echo "<br>Total rows with invalid entries = " . ($total_rows - ($total_valid + $total_incomplete)) ;
			echo '<br><br>Done [<a href="' . $mylogfile . '" target="_blank">Download Log</a>]...';
			
			
		}
		echo '</div>';
//=====================================================================================
//=====================================================================================
//=====================================================================================
//=====================================================================================
//=====================================================================================
//=====================================================================================

	}
}
?>


</td></tr></table>