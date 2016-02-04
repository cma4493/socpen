<?php
/**
 * Created By: JOSEF FRIEDRICH S. BALDO
 * Date & Time: 1/18/2016 10:28 AM
 */
include "src/Pdf.php";
include "src/Command.php";
include "src/Image.php";

use mikehaertl\wkhtmlto\Pdf;

$pdf = new Pdf;
$pdf->addPage('http://localhost/model/modeltester.php');
$pdf->saveAs('model/samplepdf.pdf');