<?php
/**
 * Created By: JOSEF FRIEDRICH S. BALDO
 * Date & Time: 1/20/2016 9:46 AM
 */
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
ini_set('max_execution_time', 9000000000);

function create_AR_pdf($MYURL,$MYFILENAME = ""){

    $executor_dir ='C:/wkhtmltopdf/bin/';
    $pdf_dir = 'C:/xampp/htdocs/socpen_access/generatedfiles/';
    // $executor_dir ="C:/ZENDSERVER/wkhtmltopdf/bin/";
    // $pdf_dir = "H:/VIRTUALHOST/hr.dswd.gov.ph/generatedfiles/";
    $executor_bin ='wkhtmltopdf.exe';
    $marginTop = ' -T 5mm';
    $marginBottom = ' -B 5mm';
    $marginLeft = ' -L 5mm';
    $marginRight = ' -R 5mm';
    $pageHeight = ' --page-height 297mm';
    $pageWidth = ' --page-width 210mm';

    if($MYFILENAME == ""){
        $fname = time() . "-" . rand(1000,9999) . "-" . rand(100,999) . ".pdf";
    }else{
        $fname = $MYFILENAME . ".pdf";

    }
    $pdf_tempname = $pdf_dir  . $fname ;

    $pdfcommand = $executor_dir . $executor_bin . $marginTop . $marginBottom . $marginLeft . $marginRight . $pageHeight . $pageWidth;
    // echo "<br>" . $pdfcommand . " $MYURL " . $pdf_tempname . "<br>";
    exec( "$pdfcommand $MYURL $pdf_tempname" );

    $fileurl = "http://localhost/socpen_access/generatedfiles/" . $fname;
    // $fileurl = "http://" . $_SERVER["SERVER_NAME"] . "/generatedfiles/" . $fname;
    return $fileurl;
}