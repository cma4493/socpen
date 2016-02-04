<?php
header("Content-type: application/vnd.ms-excel"); 
header("Content-Disposition: attachment; filename='" . $_POST["xlsfname"] . ".xls'"); 
ob_clean();
?>
<style type="text/css">
	body {
	font: normal 11px auto "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
	color: #4f6b72;
	
	}

a {
	color: #c75f3e;
}

#mytable {
	width: 100% !Important;
	padding: 0;
	margin: 0;
}

caption {
	padding: 0 0 5px 0;
	weight:bold;
	font: italic 14px "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
	text-align: left;
}

th {
	font: bold 10px "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
	color: #ffffff;
	border-right: 1px solid #000000;
	border-bottom: 1px solid #000000;
	border-top: 1px solid #000000;
	letter-spacing: 2px;
	text-transform: uppercase;
	text-align: left;
	padding: 6px 6px 6px 12px;
	background: #000000;
}

th.nobg {
	border-top: 0;
	border-left: 0;
	border-right: 1px solid #000000;
	background: none;
}

td {

	border-right: 1px solid #000000;
	border-bottom: 1px solid #000000;
	background: #fff;
	padding: 6px 6px 6px 12px;
	color: #4f6b72;
	font: 11px "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
}


td.alt {
	background: #F5FAFA;
	color: #797268;
}

th.spec {
	border-left: 1px solid #000000;
	border-top: 0;
	background: #fff url(images/bullet1.gif) no-repeat;
	font: bold 10px "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
}

th.specalt {
	border-left: 1px solid #000000;
	border-top: 0;
	background: #f5fafa url(images/bullet2.gif) no-repeat;
	font: bold 10px "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
	color: #797268;
}
</style>	
<?php echo $_POST["xlsdata"]; ?>