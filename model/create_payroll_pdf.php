<?php
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
ini_set('max_execution_time', 9000000000);

function create_payroll_pdf($MYURL,$MYFILENAME = "",$signatory1 = "",$sigpos1 = "",$signatory2 = "",$sigpos2 = "",$signatory3 = "",$sigpos3 = "",$quarter = "",$year = ""){
	
	$executor_dir ='C:/wkhtmltopdf/bin/';
	$pdf_dir = 'C:/xampp/htdocs/socpen_access/generatedfiles/';
	// $executor_dir ='C:/ZENDSERVER/wkhtmltopdf/bin/';
	// $pdf_dir = 'H:/VIRTUALHOST/hr.dswd.gov.ph/generatedfiles/';
	$executor_bin ='wkhtmltopdf.exe';
	$marginTop = ' -T 5mm';
	$marginBottom = ' -B 40mm';
	$marginLeft = ' -L 5mm';
	$marginRight = ' -R 5mm';
	$pageHeight = ' --page-height 210mm';
	$pageWidth = ' --page-width 297mm';
	$pagingFooter = ' --footer-right "Page [page] of [topage]"';
	$pagFooterUrl = ' --footer-html "http://'.$_SERVER['SERVER_NAME'].'/model/footer.php?siga='.$signatory1.'&sigb='.$signatory2.'&sigc='.$signatory3.'&sigposa='.$sigpos1.'&sigposb='.$sigpos2.'&sigposc='.$sigpos3.'&quarter='.$quarter.'&year='.$year.'"';
	
	if($MYFILENAME == ""){
		$fname = time() . "-" . rand(1000,9999) . "-" . rand(100,999) . ".pdf";
	}else{
		$fname = $MYFILENAME . ".pdf";
		 
	}
	$pdf_tempname = $pdf_dir  . $fname ;
	
	$pdfcommand = $executor_dir . $executor_bin . $marginTop . $marginBottom . $marginLeft . $marginRight . $pageHeight . $pageWidth . $pagFooterUrl;
	// echo "<br>" . $pdfcommand . " $MYURL " . $pdf_tempname . "<br>";
	exec( "$pdfcommand $MYURL $pdf_tempname" );
	
	$fileurl = "http://localhost/socpen_access/generatedfiles/" . $fname;
	// $fileurl = "http://" . $_SERVER["SERVER_NAME"] . "/generatedfiles/" . $fname;
	return $fileurl;
	/*return $pdfcommand . ' ' . $MYURL . ' ' . $pdf_tempname;*/
}

/*echo '<pre>';*/
/*echo '<a href="'.create_payroll_pdf('"http://localhost/socpen_access/model/modeltester.php?region=170000000&province=174000000&city=174001000&brgy=174001045&year=2016&quarter=1"','','josef friedrich baldo','manuelito s bongabong').'">' . 'Download' . '</a>';*/
/*echo create_payroll_pdf('"http://localhost/socpen_access/model/modeltester.php?region=170000000&province=174000000&city=174001000&brgy=174001045"','','josef friedrich baldo','manuelito s bongabong');*/
/*echo '</pre>';*/
?>