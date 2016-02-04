<?php
/**
 * PHPExcel
 *
 * Copyright (C) 2006 - 2014 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.8.0, 2014-03-02
 */

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
set_time_limit (900000);

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

date_default_timezone_set('Asia/Manila');

/** Include PHPExcel */
// require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';
require_once 'Classes/PHPExcel.php';
include "DAO.php";
include "PensionerListDAO.php";
include "cfg.php";
define ("DB_HOST_NAME", EW_CONN_HOST);
define ("PORT_NUM", EW_CONN_PORT);
define ("DB_NAME", EW_CONN_DB);
define ("DB_USER_NAME", EW_CONN_USER);
define ("DB_PASSWORD", EW_CONN_PASS);

// Instatiate Class 
$PensionerListDAO = new PensionerListDAO();

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("SocPen NPMO")
							 ->setLastModifiedBy("Socpen NPMO")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");


// Create a first sheet
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A1', "PensionerID");
$objPHPExcel->getActiveSheet()->setCellValue('B1', "hh_id");
$objPHPExcel->getActiveSheet()->setCellValue('C1', "firstname");
$objPHPExcel->getActiveSheet()->setCellValue('D1', "middlename");
$objPHPExcel->getActiveSheet()->setCellValue('E1', "lastname");
$objPHPExcel->getActiveSheet()->setCellValue('F1', "extname");
$objPHPExcel->getActiveSheet()->setCellValue('G1', "DOB");

// Freeze panes
$objPHPExcel->getActiveSheet()->freezePane('A2');


// Add data to rows, starting at 2nd row
$Pensioners = $PensionerListDAO->retList($_REQUEST['codegenids']);
foreach($Pensioners as $i=>$PensionerData){
	$i += 1;
	$a = $i+1;
	$objPHPExcel->getActiveSheet()->setCellValue('A' . $a, "".$PensionerData['PensionerID']."")
	                              ->setCellValue('B' . $a, "".$PensionerData['hh_id']."")
	                              ->setCellValue('C' . $a, "".$PensionerData['firstname']."")
	                              ->setCellValue('D' . $a, "".$PensionerData['middlename']."")
								  ->setCellValue('E' . $a, "".$PensionerData['lastname']."")
								  ->setCellValue('F' . $a, "".$PensionerData['extname']."")
	                              ->setCellValue('G' . $a, "".$PensionerData['Birthdate']."");
}

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Save Excel 2007 file
$callStartTime = microtime(true);
$callEndTime = microtime(true);

$directory = "generatedfiles/";
$filename = "SocPen2" . "-" . date("His") . '.xlsx'; // filename
$savetodir = $directory . $filename;
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save($savetodir); // saving

// Echo done
echo date('H:i:s') , " Done writing file" , EOL;
echo "download file: " . "<a href=\"".$savetodir."\">" . "Click HERE" . "</a>", EOL;