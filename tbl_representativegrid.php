<?php include_once "tbl_userinfo.php" ?>
<?php

// Create page object
if (!isset($tbl_representative_grid)) $tbl_representative_grid = new ctbl_representative_grid();

// Page init
$tbl_representative_grid->Page_Init();

// Page main
$tbl_representative_grid->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$tbl_representative_grid->Page_Render();
?>
<?php if ($tbl_representative->Export == "") { ?>
<script type="text/javascript">

// Page object
var tbl_representative_grid = new ew_Page("tbl_representative_grid");
tbl_representative_grid.PageID = "grid"; // Page ID
var EW_PAGE_ID = tbl_representative_grid.PageID; // For backward compatibility

// Form object
var ftbl_representativegrid = new ew_Form("ftbl_representativegrid");
ftbl_representativegrid.FormKeyCountName = '<?php echo $tbl_representative_grid->FormKeyCountName ?>';

// Validate form
ftbl_representativegrid.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	this.PostAutoSuggest();
	if ($fobj.find("#a_confirm").val() == "F")
		return true;
	var elm, felm, uelm, addcnt = 0;
	var $k = $fobj.find("#" + this.FormKeyCountName); // Get key_count
	var rowcnt = ($k[0]) ? parseInt($k.val(), 10) : 1;
	var startcnt = (rowcnt == 0) ? 0 : 1; // Check rowcnt == 0 => Inline-Add
	var gridinsert = $fobj.find("#a_list").val() == "gridinsert";
	for (var i = startcnt; i <= rowcnt; i++) {
		var infix = ($k[0]) ? String(i) : "";
		$fobj.data("rowindex", infix);
		var checkrow = (gridinsert) ? !this.EmptyRow(infix) : true;
		if (checkrow) {
			addcnt++;

			// Set up row object
			ew_ElementsToRow(fobj);

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
		} // End Grid Add checking
	}
	return true;
}

// Check empty row
ftbl_representativegrid.EmptyRow = function(infix) {
	var fobj = this.Form;
	if (ew_ValueChanged(fobj, infix, "PensionerID", false)) return false;
	if (ew_ValueChanged(fobj, infix, "fname", false)) return false;
	if (ew_ValueChanged(fobj, infix, "mname", false)) return false;
	if (ew_ValueChanged(fobj, infix, "lname", false)) return false;
	if (ew_ValueChanged(fobj, infix, "relToPensioner", false)) return false;
	if (ew_ValueChanged(fobj, infix, "ContactNo", false)) return false;
	if (ew_ValueChanged(fobj, infix, "auth_Region", false)) return false;
	if (ew_ValueChanged(fobj, infix, "auth_prov", false)) return false;
	if (ew_ValueChanged(fobj, infix, "auth_city", false)) return false;
	if (ew_ValueChanged(fobj, infix, "auth_brgy", false)) return false;
	if (ew_ValueChanged(fobj, infix, "houseNo", false)) return false;
	return true;
}

// Form_CustomValidate event
ftbl_representativegrid.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftbl_representativegrid.ValidateRequired = true;
<?php } else { ?>
ftbl_representativegrid.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
ftbl_representativegrid.Lists["x_relToPensioner"] = {"LinkField":"x_RelationID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_representativegrid.Lists["x_auth_Region"] = {"LinkField":"x_region_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_region_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_representativegrid.Lists["x_auth_prov"] = {"LinkField":"x_prov_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_prov_name","","",""],"ParentFields":["x_auth_Region"],"FilterFields":["x_region_code"],"Options":[]};
ftbl_representativegrid.Lists["x_auth_city"] = {"LinkField":"x_city_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_city_name","","",""],"ParentFields":["x_auth_prov"],"FilterFields":["x_prov_code"],"Options":[]};
ftbl_representativegrid.Lists["x_auth_brgy"] = {"LinkField":"x_brgy_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_brgy_name","","",""],"ParentFields":["x_auth_city"],"FilterFields":["x_city_code"],"Options":[]};

// Form object for search
</script>
<?php } ?>
<?php if ($tbl_representative->getCurrentMasterTable() == "" && $tbl_representative_grid->ExportOptions->Visible()) { ?>
<div class="ewListExportOptions"><?php $tbl_representative_grid->ExportOptions->Render("body") ?></div>
<?php } ?>
<?php
if ($tbl_representative->CurrentAction == "gridadd") {
	if ($tbl_representative->CurrentMode == "copy") {
		$bSelectLimit = EW_SELECT_LIMIT;
		if ($bSelectLimit) {
			$tbl_representative_grid->TotalRecs = $tbl_representative->SelectRecordCount();
			$tbl_representative_grid->Recordset = $tbl_representative_grid->LoadRecordset($tbl_representative_grid->StartRec-1, $tbl_representative_grid->DisplayRecs);
		} else {
			if ($tbl_representative_grid->Recordset = $tbl_representative_grid->LoadRecordset())
				$tbl_representative_grid->TotalRecs = $tbl_representative_grid->Recordset->RecordCount();
		}
		$tbl_representative_grid->StartRec = 1;
		$tbl_representative_grid->DisplayRecs = $tbl_representative_grid->TotalRecs;
	} else {
		$tbl_representative->CurrentFilter = "0=1";
		$tbl_representative_grid->StartRec = 1;
		$tbl_representative_grid->DisplayRecs = $tbl_representative->GridAddRowCount;
	}
	$tbl_representative_grid->TotalRecs = $tbl_representative_grid->DisplayRecs;
	$tbl_representative_grid->StopRec = $tbl_representative_grid->DisplayRecs;
} else {
	$bSelectLimit = EW_SELECT_LIMIT;
	if ($bSelectLimit) {
		$tbl_representative_grid->TotalRecs = $tbl_representative->SelectRecordCount();
	} else {
		if ($tbl_representative_grid->Recordset = $tbl_representative_grid->LoadRecordset())
			$tbl_representative_grid->TotalRecs = $tbl_representative_grid->Recordset->RecordCount();
	}
	$tbl_representative_grid->StartRec = 1;
	$tbl_representative_grid->DisplayRecs = $tbl_representative_grid->TotalRecs; // Display all records
	if ($bSelectLimit)
		$tbl_representative_grid->Recordset = $tbl_representative_grid->LoadRecordset($tbl_representative_grid->StartRec-1, $tbl_representative_grid->DisplayRecs);
}
$tbl_representative_grid->RenderOtherOptions();
?>
<?php $tbl_representative_grid->ShowPageHeader(); ?>
<?php
$tbl_representative_grid->ShowMessage();
?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div id="ftbl_representativegrid" class="ewForm form-horizontal">
<?php if ($tbl_representative_grid->ShowOtherOptions) { ?>
<div class="ewGridUpperPanel ewListOtherOptions">
<?php
	foreach ($tbl_representative_grid->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
<?php } ?>
<div id="gmp_tbl_representative" class="ewGridMiddlePanel">
<table id="tbl_tbl_representativegrid" class="ewTable ewTableSeparate">
<?php echo $tbl_representative->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$tbl_representative_grid->RenderListOptions();

// Render list options (header, left)
$tbl_representative_grid->ListOptions->Render("header", "left");
?>
<?php if ($tbl_representative->authID->Visible) { // authID ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->authID) == "") { ?>
		<td><div id="elh_tbl_representative_authID" class="tbl_representative_authID"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->authID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_representative_authID" class="tbl_representative_authID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->authID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->authID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->authID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_representative->PensionerID->Visible) { // PensionerID ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->PensionerID) == "") { ?>
		<td><div id="elh_tbl_representative_PensionerID" class="tbl_representative_PensionerID"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->PensionerID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_representative_PensionerID" class="tbl_representative_PensionerID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->PensionerID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->PensionerID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->PensionerID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_representative->fname->Visible) { // fname ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->fname) == "") { ?>
		<td><div id="elh_tbl_representative_fname" class="tbl_representative_fname"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->fname->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_representative_fname" class="tbl_representative_fname">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->fname->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->fname->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->fname->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_representative->mname->Visible) { // mname ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->mname) == "") { ?>
		<td><div id="elh_tbl_representative_mname" class="tbl_representative_mname"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->mname->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_representative_mname" class="tbl_representative_mname">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->mname->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->mname->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->mname->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_representative->lname->Visible) { // lname ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->lname) == "") { ?>
		<td><div id="elh_tbl_representative_lname" class="tbl_representative_lname"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->lname->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_representative_lname" class="tbl_representative_lname">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->lname->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->lname->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->lname->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_representative->relToPensioner->Visible) { // relToPensioner ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->relToPensioner) == "") { ?>
		<td><div id="elh_tbl_representative_relToPensioner" class="tbl_representative_relToPensioner"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->relToPensioner->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_representative_relToPensioner" class="tbl_representative_relToPensioner">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->relToPensioner->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->relToPensioner->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->relToPensioner->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_representative->ContactNo->Visible) { // ContactNo ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->ContactNo) == "") { ?>
		<td><div id="elh_tbl_representative_ContactNo" class="tbl_representative_ContactNo"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->ContactNo->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_representative_ContactNo" class="tbl_representative_ContactNo">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->ContactNo->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->ContactNo->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->ContactNo->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_representative->auth_Region->Visible) { // auth_Region ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->auth_Region) == "") { ?>
		<td><div id="elh_tbl_representative_auth_Region" class="tbl_representative_auth_Region"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->auth_Region->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_representative_auth_Region" class="tbl_representative_auth_Region">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->auth_Region->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->auth_Region->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->auth_Region->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_representative->auth_prov->Visible) { // auth_prov ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->auth_prov) == "") { ?>
		<td><div id="elh_tbl_representative_auth_prov" class="tbl_representative_auth_prov"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->auth_prov->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_representative_auth_prov" class="tbl_representative_auth_prov">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->auth_prov->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->auth_prov->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->auth_prov->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_representative->auth_city->Visible) { // auth_city ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->auth_city) == "") { ?>
		<td><div id="elh_tbl_representative_auth_city" class="tbl_representative_auth_city"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->auth_city->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_representative_auth_city" class="tbl_representative_auth_city">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->auth_city->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->auth_city->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->auth_city->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_representative->auth_brgy->Visible) { // auth_brgy ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->auth_brgy) == "") { ?>
		<td><div id="elh_tbl_representative_auth_brgy" class="tbl_representative_auth_brgy"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->auth_brgy->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_representative_auth_brgy" class="tbl_representative_auth_brgy">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->auth_brgy->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->auth_brgy->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->auth_brgy->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_representative->houseNo->Visible) { // houseNo ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->houseNo) == "") { ?>
		<td><div id="elh_tbl_representative_houseNo" class="tbl_representative_houseNo"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->houseNo->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_representative_houseNo" class="tbl_representative_houseNo">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->houseNo->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->houseNo->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->houseNo->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_representative->CreatedBy->Visible) { // CreatedBy ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->CreatedBy) == "") { ?>
		<td><div id="elh_tbl_representative_CreatedBy" class="tbl_representative_CreatedBy"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->CreatedBy->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_representative_CreatedBy" class="tbl_representative_CreatedBy">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->CreatedBy->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->CreatedBy->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->CreatedBy->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_representative->CreatedDate->Visible) { // CreatedDate ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->CreatedDate) == "") { ?>
		<td><div id="elh_tbl_representative_CreatedDate" class="tbl_representative_CreatedDate"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->CreatedDate->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_representative_CreatedDate" class="tbl_representative_CreatedDate">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->CreatedDate->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->CreatedDate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->CreatedDate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_representative->UpdatedBy->Visible) { // UpdatedBy ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->UpdatedBy) == "") { ?>
		<td><div id="elh_tbl_representative_UpdatedBy" class="tbl_representative_UpdatedBy"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->UpdatedBy->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_representative_UpdatedBy" class="tbl_representative_UpdatedBy">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->UpdatedBy->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->UpdatedBy->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->UpdatedBy->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_representative->UpdatedDate->Visible) { // UpdatedDate ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->UpdatedDate) == "") { ?>
		<td><div id="elh_tbl_representative_UpdatedDate" class="tbl_representative_UpdatedDate"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->UpdatedDate->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_representative_UpdatedDate" class="tbl_representative_UpdatedDate">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->UpdatedDate->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->UpdatedDate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->UpdatedDate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$tbl_representative_grid->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
$tbl_representative_grid->StartRec = 1;
$tbl_representative_grid->StopRec = $tbl_representative_grid->TotalRecs; // Show all records

// Restore number of post back records
if ($objForm) {
	$objForm->Index = -1;
	if ($objForm->HasValue($tbl_representative_grid->FormKeyCountName) && ($tbl_representative->CurrentAction == "gridadd" || $tbl_representative->CurrentAction == "gridedit" || $tbl_representative->CurrentAction == "F")) {
		$tbl_representative_grid->KeyCount = $objForm->GetValue($tbl_representative_grid->FormKeyCountName);
		$tbl_representative_grid->StopRec = $tbl_representative_grid->StartRec + $tbl_representative_grid->KeyCount - 1;
	}
}
$tbl_representative_grid->RecCnt = $tbl_representative_grid->StartRec - 1;
if ($tbl_representative_grid->Recordset && !$tbl_representative_grid->Recordset->EOF) {
	$tbl_representative_grid->Recordset->MoveFirst();
	if (!$bSelectLimit && $tbl_representative_grid->StartRec > 1)
		$tbl_representative_grid->Recordset->Move($tbl_representative_grid->StartRec - 1);
} elseif (!$tbl_representative->AllowAddDeleteRow && $tbl_representative_grid->StopRec == 0) {
	$tbl_representative_grid->StopRec = $tbl_representative->GridAddRowCount;
}

// Initialize aggregate
$tbl_representative->RowType = EW_ROWTYPE_AGGREGATEINIT;
$tbl_representative->ResetAttrs();
$tbl_representative_grid->RenderRow();
if ($tbl_representative->CurrentAction == "gridadd")
	$tbl_representative_grid->RowIndex = 0;
if ($tbl_representative->CurrentAction == "gridedit")
	$tbl_representative_grid->RowIndex = 0;
while ($tbl_representative_grid->RecCnt < $tbl_representative_grid->StopRec) {
	$tbl_representative_grid->RecCnt++;
	if (intval($tbl_representative_grid->RecCnt) >= intval($tbl_representative_grid->StartRec)) {
		$tbl_representative_grid->RowCnt++;
		if ($tbl_representative->CurrentAction == "gridadd" || $tbl_representative->CurrentAction == "gridedit" || $tbl_representative->CurrentAction == "F") {
			$tbl_representative_grid->RowIndex++;
			$objForm->Index = $tbl_representative_grid->RowIndex;
			if ($objForm->HasValue($tbl_representative_grid->FormActionName))
				$tbl_representative_grid->RowAction = strval($objForm->GetValue($tbl_representative_grid->FormActionName));
			elseif ($tbl_representative->CurrentAction == "gridadd")
				$tbl_representative_grid->RowAction = "insert";
			else
				$tbl_representative_grid->RowAction = "";
		}

		// Set up key count
		$tbl_representative_grid->KeyCount = $tbl_representative_grid->RowIndex;

		// Init row class and style
		$tbl_representative->ResetAttrs();
		$tbl_representative->CssClass = "";
		if ($tbl_representative->CurrentAction == "gridadd") {
			if ($tbl_representative->CurrentMode == "copy") {
				$tbl_representative_grid->LoadRowValues($tbl_representative_grid->Recordset); // Load row values
				$tbl_representative_grid->SetRecordKey($tbl_representative_grid->RowOldKey, $tbl_representative_grid->Recordset); // Set old record key
			} else {
				$tbl_representative_grid->LoadDefaultValues(); // Load default values
				$tbl_representative_grid->RowOldKey = ""; // Clear old key value
			}
		} else {
			$tbl_representative_grid->LoadRowValues($tbl_representative_grid->Recordset); // Load row values
		}
		$tbl_representative->RowType = EW_ROWTYPE_VIEW; // Render view
		if ($tbl_representative->CurrentAction == "gridadd") // Grid add
			$tbl_representative->RowType = EW_ROWTYPE_ADD; // Render add
		if ($tbl_representative->CurrentAction == "gridadd" && $tbl_representative->EventCancelled && !$objForm->HasValue("k_blankrow")) // Insert failed
			$tbl_representative_grid->RestoreCurrentRowFormValues($tbl_representative_grid->RowIndex); // Restore form values
		if ($tbl_representative->CurrentAction == "gridedit") { // Grid edit
			if ($tbl_representative->EventCancelled) {
				$tbl_representative_grid->RestoreCurrentRowFormValues($tbl_representative_grid->RowIndex); // Restore form values
			}
			if ($tbl_representative_grid->RowAction == "insert")
				$tbl_representative->RowType = EW_ROWTYPE_ADD; // Render add
			else
				$tbl_representative->RowType = EW_ROWTYPE_EDIT; // Render edit
		}
		if ($tbl_representative->CurrentAction == "gridedit" && ($tbl_representative->RowType == EW_ROWTYPE_EDIT || $tbl_representative->RowType == EW_ROWTYPE_ADD) && $tbl_representative->EventCancelled) // Update failed
			$tbl_representative_grid->RestoreCurrentRowFormValues($tbl_representative_grid->RowIndex); // Restore form values
		if ($tbl_representative->RowType == EW_ROWTYPE_EDIT) // Edit row
			$tbl_representative_grid->EditRowCnt++;
		if ($tbl_representative->CurrentAction == "F") // Confirm row
			$tbl_representative_grid->RestoreCurrentRowFormValues($tbl_representative_grid->RowIndex); // Restore form values

		// Set up row id / data-rowindex
		$tbl_representative->RowAttrs = array_merge($tbl_representative->RowAttrs, array('data-rowindex'=>$tbl_representative_grid->RowCnt, 'id'=>'r' . $tbl_representative_grid->RowCnt . '_tbl_representative', 'data-rowtype'=>$tbl_representative->RowType));

		// Render row
		$tbl_representative_grid->RenderRow();

		// Render list options
		$tbl_representative_grid->RenderListOptions();

		// Skip delete row / empty row for confirm page
		if ($tbl_representative_grid->RowAction <> "delete" && $tbl_representative_grid->RowAction <> "insertdelete" && !($tbl_representative_grid->RowAction == "insert" && $tbl_representative->CurrentAction == "F" && $tbl_representative_grid->EmptyRow())) {
?>
	<tr<?php echo $tbl_representative->RowAttributes() ?>>
<?php

// Render list options (body, left)
$tbl_representative_grid->ListOptions->Render("body", "left", $tbl_representative_grid->RowCnt);
?>
	<?php if ($tbl_representative->authID->Visible) { // authID ?>
		<td<?php echo $tbl_representative->authID->CellAttributes() ?>>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<input type="hidden" data-field="x_authID" name="o<?php echo $tbl_representative_grid->RowIndex ?>_authID" id="o<?php echo $tbl_representative_grid->RowIndex ?>_authID" value="<?php echo ew_HtmlEncode($tbl_representative->authID->OldValue) ?>">
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_representative_grid->RowCnt ?>_tbl_representative_authID" class="control-group tbl_representative_authID">
<span<?php echo $tbl_representative->authID->ViewAttributes() ?>>
<?php echo $tbl_representative->authID->EditValue ?></span>
</span>
<input type="hidden" data-field="x_authID" name="x<?php echo $tbl_representative_grid->RowIndex ?>_authID" id="x<?php echo $tbl_representative_grid->RowIndex ?>_authID" value="<?php echo ew_HtmlEncode($tbl_representative->authID->CurrentValue) ?>">
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_representative->authID->ViewAttributes() ?>>
<?php echo $tbl_representative->authID->ListViewValue() ?></span>
<input type="hidden" data-field="x_authID" name="x<?php echo $tbl_representative_grid->RowIndex ?>_authID" id="x<?php echo $tbl_representative_grid->RowIndex ?>_authID" value="<?php echo ew_HtmlEncode($tbl_representative->authID->FormValue) ?>">
<input type="hidden" data-field="x_authID" name="o<?php echo $tbl_representative_grid->RowIndex ?>_authID" id="o<?php echo $tbl_representative_grid->RowIndex ?>_authID" value="<?php echo ew_HtmlEncode($tbl_representative->authID->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_representative_grid->PageObjName . "_row_" . $tbl_representative_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_representative->PensionerID->Visible) { // PensionerID ?>
		<td<?php echo $tbl_representative->PensionerID->CellAttributes() ?>>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<?php if ($tbl_representative->PensionerID->getSessionValue() <> "") { ?>
<span<?php echo $tbl_representative->PensionerID->ViewAttributes() ?>>
<?php echo $tbl_representative->PensionerID->ListViewValue() ?></span>
<input type="hidden" id="x<?php echo $tbl_representative_grid->RowIndex ?>_PensionerID" name="x<?php echo $tbl_representative_grid->RowIndex ?>_PensionerID" value="<?php echo ew_HtmlEncode($tbl_representative->PensionerID->CurrentValue) ?>">
<?php } else { ?>
<input type="text" data-field="x_PensionerID" name="x<?php echo $tbl_representative_grid->RowIndex ?>_PensionerID" id="x<?php echo $tbl_representative_grid->RowIndex ?>_PensionerID" size="30" maxlength="25" placeholder="<?php echo $tbl_representative->PensionerID->PlaceHolder ?>" value="<?php echo $tbl_representative->PensionerID->EditValue ?>"<?php echo $tbl_representative->PensionerID->EditAttributes() ?>>
<?php } ?>
<input type="hidden" data-field="x_PensionerID" name="o<?php echo $tbl_representative_grid->RowIndex ?>_PensionerID" id="o<?php echo $tbl_representative_grid->RowIndex ?>_PensionerID" value="<?php echo ew_HtmlEncode($tbl_representative->PensionerID->OldValue) ?>">
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<?php if ($tbl_representative->PensionerID->getSessionValue() <> "") { ?>
<span<?php echo $tbl_representative->PensionerID->ViewAttributes() ?>>
<?php echo $tbl_representative->PensionerID->ListViewValue() ?></span>
<input type="hidden" id="x<?php echo $tbl_representative_grid->RowIndex ?>_PensionerID" name="x<?php echo $tbl_representative_grid->RowIndex ?>_PensionerID" value="<?php echo ew_HtmlEncode($tbl_representative->PensionerID->CurrentValue) ?>">
<?php } else { ?>
<input type="text" data-field="x_PensionerID" name="x<?php echo $tbl_representative_grid->RowIndex ?>_PensionerID" id="x<?php echo $tbl_representative_grid->RowIndex ?>_PensionerID" size="30" maxlength="25" placeholder="<?php echo $tbl_representative->PensionerID->PlaceHolder ?>" value="<?php echo $tbl_representative->PensionerID->EditValue ?>"<?php echo $tbl_representative->PensionerID->EditAttributes() ?>>
<?php } ?>
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_representative->PensionerID->ViewAttributes() ?>>
<?php echo $tbl_representative->PensionerID->ListViewValue() ?></span>
<input type="hidden" data-field="x_PensionerID" name="x<?php echo $tbl_representative_grid->RowIndex ?>_PensionerID" id="x<?php echo $tbl_representative_grid->RowIndex ?>_PensionerID" value="<?php echo ew_HtmlEncode($tbl_representative->PensionerID->FormValue) ?>">
<input type="hidden" data-field="x_PensionerID" name="o<?php echo $tbl_representative_grid->RowIndex ?>_PensionerID" id="o<?php echo $tbl_representative_grid->RowIndex ?>_PensionerID" value="<?php echo ew_HtmlEncode($tbl_representative->PensionerID->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_representative_grid->PageObjName . "_row_" . $tbl_representative_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_representative->fname->Visible) { // fname ?>
		<td<?php echo $tbl_representative->fname->CellAttributes() ?>>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $tbl_representative_grid->RowCnt ?>_tbl_representative_fname" class="control-group tbl_representative_fname">
<input type="text" data-field="x_fname" name="x<?php echo $tbl_representative_grid->RowIndex ?>_fname" id="x<?php echo $tbl_representative_grid->RowIndex ?>_fname" size="30" maxlength="40" placeholder="<?php echo $tbl_representative->fname->PlaceHolder ?>" value="<?php echo $tbl_representative->fname->EditValue ?>"<?php echo $tbl_representative->fname->EditAttributes() ?>>
</span>
<input type="hidden" data-field="x_fname" name="o<?php echo $tbl_representative_grid->RowIndex ?>_fname" id="o<?php echo $tbl_representative_grid->RowIndex ?>_fname" value="<?php echo ew_HtmlEncode($tbl_representative->fname->OldValue) ?>">
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_representative_grid->RowCnt ?>_tbl_representative_fname" class="control-group tbl_representative_fname">
<input type="text" data-field="x_fname" name="x<?php echo $tbl_representative_grid->RowIndex ?>_fname" id="x<?php echo $tbl_representative_grid->RowIndex ?>_fname" size="30" maxlength="40" placeholder="<?php echo $tbl_representative->fname->PlaceHolder ?>" value="<?php echo $tbl_representative->fname->EditValue ?>"<?php echo $tbl_representative->fname->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_representative->fname->ViewAttributes() ?>>
<?php echo $tbl_representative->fname->ListViewValue() ?></span>
<input type="hidden" data-field="x_fname" name="x<?php echo $tbl_representative_grid->RowIndex ?>_fname" id="x<?php echo $tbl_representative_grid->RowIndex ?>_fname" value="<?php echo ew_HtmlEncode($tbl_representative->fname->FormValue) ?>">
<input type="hidden" data-field="x_fname" name="o<?php echo $tbl_representative_grid->RowIndex ?>_fname" id="o<?php echo $tbl_representative_grid->RowIndex ?>_fname" value="<?php echo ew_HtmlEncode($tbl_representative->fname->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_representative_grid->PageObjName . "_row_" . $tbl_representative_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_representative->mname->Visible) { // mname ?>
		<td<?php echo $tbl_representative->mname->CellAttributes() ?>>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $tbl_representative_grid->RowCnt ?>_tbl_representative_mname" class="control-group tbl_representative_mname">
<input type="text" data-field="x_mname" name="x<?php echo $tbl_representative_grid->RowIndex ?>_mname" id="x<?php echo $tbl_representative_grid->RowIndex ?>_mname" size="30" maxlength="40" placeholder="<?php echo $tbl_representative->mname->PlaceHolder ?>" value="<?php echo $tbl_representative->mname->EditValue ?>"<?php echo $tbl_representative->mname->EditAttributes() ?>>
</span>
<input type="hidden" data-field="x_mname" name="o<?php echo $tbl_representative_grid->RowIndex ?>_mname" id="o<?php echo $tbl_representative_grid->RowIndex ?>_mname" value="<?php echo ew_HtmlEncode($tbl_representative->mname->OldValue) ?>">
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_representative_grid->RowCnt ?>_tbl_representative_mname" class="control-group tbl_representative_mname">
<input type="text" data-field="x_mname" name="x<?php echo $tbl_representative_grid->RowIndex ?>_mname" id="x<?php echo $tbl_representative_grid->RowIndex ?>_mname" size="30" maxlength="40" placeholder="<?php echo $tbl_representative->mname->PlaceHolder ?>" value="<?php echo $tbl_representative->mname->EditValue ?>"<?php echo $tbl_representative->mname->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_representative->mname->ViewAttributes() ?>>
<?php echo $tbl_representative->mname->ListViewValue() ?></span>
<input type="hidden" data-field="x_mname" name="x<?php echo $tbl_representative_grid->RowIndex ?>_mname" id="x<?php echo $tbl_representative_grid->RowIndex ?>_mname" value="<?php echo ew_HtmlEncode($tbl_representative->mname->FormValue) ?>">
<input type="hidden" data-field="x_mname" name="o<?php echo $tbl_representative_grid->RowIndex ?>_mname" id="o<?php echo $tbl_representative_grid->RowIndex ?>_mname" value="<?php echo ew_HtmlEncode($tbl_representative->mname->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_representative_grid->PageObjName . "_row_" . $tbl_representative_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_representative->lname->Visible) { // lname ?>
		<td<?php echo $tbl_representative->lname->CellAttributes() ?>>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $tbl_representative_grid->RowCnt ?>_tbl_representative_lname" class="control-group tbl_representative_lname">
<input type="text" data-field="x_lname" name="x<?php echo $tbl_representative_grid->RowIndex ?>_lname" id="x<?php echo $tbl_representative_grid->RowIndex ?>_lname" size="30" maxlength="40" placeholder="<?php echo $tbl_representative->lname->PlaceHolder ?>" value="<?php echo $tbl_representative->lname->EditValue ?>"<?php echo $tbl_representative->lname->EditAttributes() ?>>
</span>
<input type="hidden" data-field="x_lname" name="o<?php echo $tbl_representative_grid->RowIndex ?>_lname" id="o<?php echo $tbl_representative_grid->RowIndex ?>_lname" value="<?php echo ew_HtmlEncode($tbl_representative->lname->OldValue) ?>">
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_representative_grid->RowCnt ?>_tbl_representative_lname" class="control-group tbl_representative_lname">
<input type="text" data-field="x_lname" name="x<?php echo $tbl_representative_grid->RowIndex ?>_lname" id="x<?php echo $tbl_representative_grid->RowIndex ?>_lname" size="30" maxlength="40" placeholder="<?php echo $tbl_representative->lname->PlaceHolder ?>" value="<?php echo $tbl_representative->lname->EditValue ?>"<?php echo $tbl_representative->lname->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_representative->lname->ViewAttributes() ?>>
<?php echo $tbl_representative->lname->ListViewValue() ?></span>
<input type="hidden" data-field="x_lname" name="x<?php echo $tbl_representative_grid->RowIndex ?>_lname" id="x<?php echo $tbl_representative_grid->RowIndex ?>_lname" value="<?php echo ew_HtmlEncode($tbl_representative->lname->FormValue) ?>">
<input type="hidden" data-field="x_lname" name="o<?php echo $tbl_representative_grid->RowIndex ?>_lname" id="o<?php echo $tbl_representative_grid->RowIndex ?>_lname" value="<?php echo ew_HtmlEncode($tbl_representative->lname->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_representative_grid->PageObjName . "_row_" . $tbl_representative_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_representative->relToPensioner->Visible) { // relToPensioner ?>
		<td<?php echo $tbl_representative->relToPensioner->CellAttributes() ?>>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $tbl_representative_grid->RowCnt ?>_tbl_representative_relToPensioner" class="control-group tbl_representative_relToPensioner">
<select data-field="x_relToPensioner" id="x<?php echo $tbl_representative_grid->RowIndex ?>_relToPensioner" name="x<?php echo $tbl_representative_grid->RowIndex ?>_relToPensioner"<?php echo $tbl_representative->relToPensioner->EditAttributes() ?>>
<?php
if (is_array($tbl_representative->relToPensioner->EditValue)) {
	$arwrk = $tbl_representative->relToPensioner->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_representative->relToPensioner->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_representative->relToPensioner->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `RelationID`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_relationship`";
 $sWhereWrk = "";

 // Call Lookup selecting
 $tbl_representative->Lookup_Selecting($tbl_representative->relToPensioner, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
 $sSqlWrk .= " ORDER BY `Description` ASC";
?>
<input type="hidden" name="s_x<?php echo $tbl_representative_grid->RowIndex ?>_relToPensioner" id="s_x<?php echo $tbl_representative_grid->RowIndex ?>_relToPensioner" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`RelationID` = {filter_value}"); ?>&t0=3">
</span>
<input type="hidden" data-field="x_relToPensioner" name="o<?php echo $tbl_representative_grid->RowIndex ?>_relToPensioner" id="o<?php echo $tbl_representative_grid->RowIndex ?>_relToPensioner" value="<?php echo ew_HtmlEncode($tbl_representative->relToPensioner->OldValue) ?>">
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_representative_grid->RowCnt ?>_tbl_representative_relToPensioner" class="control-group tbl_representative_relToPensioner">
<select data-field="x_relToPensioner" id="x<?php echo $tbl_representative_grid->RowIndex ?>_relToPensioner" name="x<?php echo $tbl_representative_grid->RowIndex ?>_relToPensioner"<?php echo $tbl_representative->relToPensioner->EditAttributes() ?>>
<?php
if (is_array($tbl_representative->relToPensioner->EditValue)) {
	$arwrk = $tbl_representative->relToPensioner->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_representative->relToPensioner->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_representative->relToPensioner->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `RelationID`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_relationship`";
 $sWhereWrk = "";

 // Call Lookup selecting
 $tbl_representative->Lookup_Selecting($tbl_representative->relToPensioner, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
 $sSqlWrk .= " ORDER BY `Description` ASC";
?>
<input type="hidden" name="s_x<?php echo $tbl_representative_grid->RowIndex ?>_relToPensioner" id="s_x<?php echo $tbl_representative_grid->RowIndex ?>_relToPensioner" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`RelationID` = {filter_value}"); ?>&t0=3">
</span>
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_representative->relToPensioner->ViewAttributes() ?>>
<?php echo $tbl_representative->relToPensioner->ListViewValue() ?></span>
<input type="hidden" data-field="x_relToPensioner" name="x<?php echo $tbl_representative_grid->RowIndex ?>_relToPensioner" id="x<?php echo $tbl_representative_grid->RowIndex ?>_relToPensioner" value="<?php echo ew_HtmlEncode($tbl_representative->relToPensioner->FormValue) ?>">
<input type="hidden" data-field="x_relToPensioner" name="o<?php echo $tbl_representative_grid->RowIndex ?>_relToPensioner" id="o<?php echo $tbl_representative_grid->RowIndex ?>_relToPensioner" value="<?php echo ew_HtmlEncode($tbl_representative->relToPensioner->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_representative_grid->PageObjName . "_row_" . $tbl_representative_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_representative->ContactNo->Visible) { // ContactNo ?>
		<td<?php echo $tbl_representative->ContactNo->CellAttributes() ?>>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $tbl_representative_grid->RowCnt ?>_tbl_representative_ContactNo" class="control-group tbl_representative_ContactNo">
<input type="text" data-field="x_ContactNo" name="x<?php echo $tbl_representative_grid->RowIndex ?>_ContactNo" id="x<?php echo $tbl_representative_grid->RowIndex ?>_ContactNo" size="30" maxlength="10" placeholder="<?php echo $tbl_representative->ContactNo->PlaceHolder ?>" value="<?php echo $tbl_representative->ContactNo->EditValue ?>"<?php echo $tbl_representative->ContactNo->EditAttributes() ?>>
</span>
<input type="hidden" data-field="x_ContactNo" name="o<?php echo $tbl_representative_grid->RowIndex ?>_ContactNo" id="o<?php echo $tbl_representative_grid->RowIndex ?>_ContactNo" value="<?php echo ew_HtmlEncode($tbl_representative->ContactNo->OldValue) ?>">
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_representative_grid->RowCnt ?>_tbl_representative_ContactNo" class="control-group tbl_representative_ContactNo">
<input type="text" data-field="x_ContactNo" name="x<?php echo $tbl_representative_grid->RowIndex ?>_ContactNo" id="x<?php echo $tbl_representative_grid->RowIndex ?>_ContactNo" size="30" maxlength="10" placeholder="<?php echo $tbl_representative->ContactNo->PlaceHolder ?>" value="<?php echo $tbl_representative->ContactNo->EditValue ?>"<?php echo $tbl_representative->ContactNo->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_representative->ContactNo->ViewAttributes() ?>>
<?php echo $tbl_representative->ContactNo->ListViewValue() ?></span>
<input type="hidden" data-field="x_ContactNo" name="x<?php echo $tbl_representative_grid->RowIndex ?>_ContactNo" id="x<?php echo $tbl_representative_grid->RowIndex ?>_ContactNo" value="<?php echo ew_HtmlEncode($tbl_representative->ContactNo->FormValue) ?>">
<input type="hidden" data-field="x_ContactNo" name="o<?php echo $tbl_representative_grid->RowIndex ?>_ContactNo" id="o<?php echo $tbl_representative_grid->RowIndex ?>_ContactNo" value="<?php echo ew_HtmlEncode($tbl_representative->ContactNo->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_representative_grid->PageObjName . "_row_" . $tbl_representative_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_representative->auth_Region->Visible) { // auth_Region ?>
		<td<?php echo $tbl_representative->auth_Region->CellAttributes() ?>>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $tbl_representative_grid->RowCnt ?>_tbl_representative_auth_Region" class="control-group tbl_representative_auth_Region">
<?php $tbl_representative->auth_Region->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x" . $tbl_representative_grid->RowIndex . "_auth_prov']); " . @$tbl_representative->auth_Region->EditAttrs["onchange"]; ?>
<select data-field="x_auth_Region" id="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_Region" name="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_Region"<?php echo $tbl_representative->auth_Region->EditAttributes() ?>>
<?php
if (is_array($tbl_representative->auth_Region->EditValue)) {
	$arwrk = $tbl_representative->auth_Region->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_representative->auth_Region->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_representative->auth_Region->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `region_code`, `region_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_regions`";
 $sWhereWrk = "";

 // Call Lookup selecting
 $tbl_representative->Lookup_Selecting($tbl_representative->auth_Region, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
 $sSqlWrk .= " ORDER BY `region_code` ASC";
?>
<input type="hidden" name="s_x<?php echo $tbl_representative_grid->RowIndex ?>_auth_Region" id="s_x<?php echo $tbl_representative_grid->RowIndex ?>_auth_Region" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`region_code` = {filter_value}"); ?>&t0=21">
</span>
<input type="hidden" data-field="x_auth_Region" name="o<?php echo $tbl_representative_grid->RowIndex ?>_auth_Region" id="o<?php echo $tbl_representative_grid->RowIndex ?>_auth_Region" value="<?php echo ew_HtmlEncode($tbl_representative->auth_Region->OldValue) ?>">
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_representative_grid->RowCnt ?>_tbl_representative_auth_Region" class="control-group tbl_representative_auth_Region">
<?php $tbl_representative->auth_Region->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x" . $tbl_representative_grid->RowIndex . "_auth_prov']); " . @$tbl_representative->auth_Region->EditAttrs["onchange"]; ?>
<select data-field="x_auth_Region" id="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_Region" name="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_Region"<?php echo $tbl_representative->auth_Region->EditAttributes() ?>>
<?php
if (is_array($tbl_representative->auth_Region->EditValue)) {
	$arwrk = $tbl_representative->auth_Region->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_representative->auth_Region->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_representative->auth_Region->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `region_code`, `region_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_regions`";
 $sWhereWrk = "";

 // Call Lookup selecting
 $tbl_representative->Lookup_Selecting($tbl_representative->auth_Region, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
 $sSqlWrk .= " ORDER BY `region_code` ASC";
?>
<input type="hidden" name="s_x<?php echo $tbl_representative_grid->RowIndex ?>_auth_Region" id="s_x<?php echo $tbl_representative_grid->RowIndex ?>_auth_Region" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`region_code` = {filter_value}"); ?>&t0=21">
</span>
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_representative->auth_Region->ViewAttributes() ?>>
<?php echo $tbl_representative->auth_Region->ListViewValue() ?></span>
<input type="hidden" data-field="x_auth_Region" name="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_Region" id="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_Region" value="<?php echo ew_HtmlEncode($tbl_representative->auth_Region->FormValue) ?>">
<input type="hidden" data-field="x_auth_Region" name="o<?php echo $tbl_representative_grid->RowIndex ?>_auth_Region" id="o<?php echo $tbl_representative_grid->RowIndex ?>_auth_Region" value="<?php echo ew_HtmlEncode($tbl_representative->auth_Region->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_representative_grid->PageObjName . "_row_" . $tbl_representative_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_representative->auth_prov->Visible) { // auth_prov ?>
		<td<?php echo $tbl_representative->auth_prov->CellAttributes() ?>>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $tbl_representative_grid->RowCnt ?>_tbl_representative_auth_prov" class="control-group tbl_representative_auth_prov">
<?php $tbl_representative->auth_prov->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x" . $tbl_representative_grid->RowIndex . "_auth_city']); " . @$tbl_representative->auth_prov->EditAttrs["onchange"]; ?>
<select data-field="x_auth_prov" id="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_prov" name="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_prov"<?php echo $tbl_representative->auth_prov->EditAttributes() ?>>
<?php
if (is_array($tbl_representative->auth_prov->EditValue)) {
	$arwrk = $tbl_representative->auth_prov->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_representative->auth_prov->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_representative->auth_prov->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `prov_code`, `prov_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_provinces`";
 $sWhereWrk = "{filter}";

 // Call Lookup selecting
 $tbl_representative->Lookup_Selecting($tbl_representative->auth_prov, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
 $sSqlWrk .= " ORDER BY `prov_name` ASC";
?>
<input type="hidden" name="s_x<?php echo $tbl_representative_grid->RowIndex ?>_auth_prov" id="s_x<?php echo $tbl_representative_grid->RowIndex ?>_auth_prov" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`prov_code` = {filter_value}"); ?>&t0=21&f1=<?php echo ew_Encrypt("`region_code` IN ({filter_value})"); ?>&t1=21">
</span>
<input type="hidden" data-field="x_auth_prov" name="o<?php echo $tbl_representative_grid->RowIndex ?>_auth_prov" id="o<?php echo $tbl_representative_grid->RowIndex ?>_auth_prov" value="<?php echo ew_HtmlEncode($tbl_representative->auth_prov->OldValue) ?>">
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_representative_grid->RowCnt ?>_tbl_representative_auth_prov" class="control-group tbl_representative_auth_prov">
<?php $tbl_representative->auth_prov->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x" . $tbl_representative_grid->RowIndex . "_auth_city']); " . @$tbl_representative->auth_prov->EditAttrs["onchange"]; ?>
<select data-field="x_auth_prov" id="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_prov" name="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_prov"<?php echo $tbl_representative->auth_prov->EditAttributes() ?>>
<?php
if (is_array($tbl_representative->auth_prov->EditValue)) {
	$arwrk = $tbl_representative->auth_prov->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_representative->auth_prov->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_representative->auth_prov->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `prov_code`, `prov_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_provinces`";
 $sWhereWrk = "{filter}";

 // Call Lookup selecting
 $tbl_representative->Lookup_Selecting($tbl_representative->auth_prov, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
 $sSqlWrk .= " ORDER BY `prov_name` ASC";
?>
<input type="hidden" name="s_x<?php echo $tbl_representative_grid->RowIndex ?>_auth_prov" id="s_x<?php echo $tbl_representative_grid->RowIndex ?>_auth_prov" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`prov_code` = {filter_value}"); ?>&t0=21&f1=<?php echo ew_Encrypt("`region_code` IN ({filter_value})"); ?>&t1=21">
</span>
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_representative->auth_prov->ViewAttributes() ?>>
<?php echo $tbl_representative->auth_prov->ListViewValue() ?></span>
<input type="hidden" data-field="x_auth_prov" name="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_prov" id="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_prov" value="<?php echo ew_HtmlEncode($tbl_representative->auth_prov->FormValue) ?>">
<input type="hidden" data-field="x_auth_prov" name="o<?php echo $tbl_representative_grid->RowIndex ?>_auth_prov" id="o<?php echo $tbl_representative_grid->RowIndex ?>_auth_prov" value="<?php echo ew_HtmlEncode($tbl_representative->auth_prov->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_representative_grid->PageObjName . "_row_" . $tbl_representative_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_representative->auth_city->Visible) { // auth_city ?>
		<td<?php echo $tbl_representative->auth_city->CellAttributes() ?>>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $tbl_representative_grid->RowCnt ?>_tbl_representative_auth_city" class="control-group tbl_representative_auth_city">
<?php $tbl_representative->auth_city->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x" . $tbl_representative_grid->RowIndex . "_auth_brgy']); " . @$tbl_representative->auth_city->EditAttrs["onchange"]; ?>
<select data-field="x_auth_city" id="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_city" name="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_city"<?php echo $tbl_representative->auth_city->EditAttributes() ?>>
<?php
if (is_array($tbl_representative->auth_city->EditValue)) {
	$arwrk = $tbl_representative->auth_city->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_representative->auth_city->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_representative->auth_city->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `city_code`, `city_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_cities`";
 $sWhereWrk = "{filter}";

 // Call Lookup selecting
 $tbl_representative->Lookup_Selecting($tbl_representative->auth_city, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
 $sSqlWrk .= " ORDER BY `city_name` ASC";
?>
<input type="hidden" name="s_x<?php echo $tbl_representative_grid->RowIndex ?>_auth_city" id="s_x<?php echo $tbl_representative_grid->RowIndex ?>_auth_city" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`city_code` = {filter_value}"); ?>&t0=21&f1=<?php echo ew_Encrypt("`prov_code` IN ({filter_value})"); ?>&t1=21">
</span>
<input type="hidden" data-field="x_auth_city" name="o<?php echo $tbl_representative_grid->RowIndex ?>_auth_city" id="o<?php echo $tbl_representative_grid->RowIndex ?>_auth_city" value="<?php echo ew_HtmlEncode($tbl_representative->auth_city->OldValue) ?>">
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_representative_grid->RowCnt ?>_tbl_representative_auth_city" class="control-group tbl_representative_auth_city">
<?php $tbl_representative->auth_city->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x" . $tbl_representative_grid->RowIndex . "_auth_brgy']); " . @$tbl_representative->auth_city->EditAttrs["onchange"]; ?>
<select data-field="x_auth_city" id="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_city" name="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_city"<?php echo $tbl_representative->auth_city->EditAttributes() ?>>
<?php
if (is_array($tbl_representative->auth_city->EditValue)) {
	$arwrk = $tbl_representative->auth_city->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_representative->auth_city->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_representative->auth_city->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `city_code`, `city_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_cities`";
 $sWhereWrk = "{filter}";

 // Call Lookup selecting
 $tbl_representative->Lookup_Selecting($tbl_representative->auth_city, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
 $sSqlWrk .= " ORDER BY `city_name` ASC";
?>
<input type="hidden" name="s_x<?php echo $tbl_representative_grid->RowIndex ?>_auth_city" id="s_x<?php echo $tbl_representative_grid->RowIndex ?>_auth_city" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`city_code` = {filter_value}"); ?>&t0=21&f1=<?php echo ew_Encrypt("`prov_code` IN ({filter_value})"); ?>&t1=21">
</span>
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_representative->auth_city->ViewAttributes() ?>>
<?php echo $tbl_representative->auth_city->ListViewValue() ?></span>
<input type="hidden" data-field="x_auth_city" name="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_city" id="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_city" value="<?php echo ew_HtmlEncode($tbl_representative->auth_city->FormValue) ?>">
<input type="hidden" data-field="x_auth_city" name="o<?php echo $tbl_representative_grid->RowIndex ?>_auth_city" id="o<?php echo $tbl_representative_grid->RowIndex ?>_auth_city" value="<?php echo ew_HtmlEncode($tbl_representative->auth_city->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_representative_grid->PageObjName . "_row_" . $tbl_representative_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_representative->auth_brgy->Visible) { // auth_brgy ?>
		<td<?php echo $tbl_representative->auth_brgy->CellAttributes() ?>>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $tbl_representative_grid->RowCnt ?>_tbl_representative_auth_brgy" class="control-group tbl_representative_auth_brgy">
<select data-field="x_auth_brgy" id="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_brgy" name="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_brgy"<?php echo $tbl_representative->auth_brgy->EditAttributes() ?>>
<?php
if (is_array($tbl_representative->auth_brgy->EditValue)) {
	$arwrk = $tbl_representative->auth_brgy->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_representative->auth_brgy->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_representative->auth_brgy->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `brgy_code`, `brgy_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_brgy`";
 $sWhereWrk = "{filter}";

 // Call Lookup selecting
 $tbl_representative->Lookup_Selecting($tbl_representative->auth_brgy, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
 $sSqlWrk .= " ORDER BY `brgy_name` ASC";
?>
<input type="hidden" name="s_x<?php echo $tbl_representative_grid->RowIndex ?>_auth_brgy" id="s_x<?php echo $tbl_representative_grid->RowIndex ?>_auth_brgy" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`brgy_code` = {filter_value}"); ?>&t0=21&f1=<?php echo ew_Encrypt("`city_code` IN ({filter_value})"); ?>&t1=21">
</span>
<input type="hidden" data-field="x_auth_brgy" name="o<?php echo $tbl_representative_grid->RowIndex ?>_auth_brgy" id="o<?php echo $tbl_representative_grid->RowIndex ?>_auth_brgy" value="<?php echo ew_HtmlEncode($tbl_representative->auth_brgy->OldValue) ?>">
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_representative_grid->RowCnt ?>_tbl_representative_auth_brgy" class="control-group tbl_representative_auth_brgy">
<select data-field="x_auth_brgy" id="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_brgy" name="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_brgy"<?php echo $tbl_representative->auth_brgy->EditAttributes() ?>>
<?php
if (is_array($tbl_representative->auth_brgy->EditValue)) {
	$arwrk = $tbl_representative->auth_brgy->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_representative->auth_brgy->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_representative->auth_brgy->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `brgy_code`, `brgy_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_brgy`";
 $sWhereWrk = "{filter}";

 // Call Lookup selecting
 $tbl_representative->Lookup_Selecting($tbl_representative->auth_brgy, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
 $sSqlWrk .= " ORDER BY `brgy_name` ASC";
?>
<input type="hidden" name="s_x<?php echo $tbl_representative_grid->RowIndex ?>_auth_brgy" id="s_x<?php echo $tbl_representative_grid->RowIndex ?>_auth_brgy" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`brgy_code` = {filter_value}"); ?>&t0=21&f1=<?php echo ew_Encrypt("`city_code` IN ({filter_value})"); ?>&t1=21">
</span>
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_representative->auth_brgy->ViewAttributes() ?>>
<?php echo $tbl_representative->auth_brgy->ListViewValue() ?></span>
<input type="hidden" data-field="x_auth_brgy" name="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_brgy" id="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_brgy" value="<?php echo ew_HtmlEncode($tbl_representative->auth_brgy->FormValue) ?>">
<input type="hidden" data-field="x_auth_brgy" name="o<?php echo $tbl_representative_grid->RowIndex ?>_auth_brgy" id="o<?php echo $tbl_representative_grid->RowIndex ?>_auth_brgy" value="<?php echo ew_HtmlEncode($tbl_representative->auth_brgy->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_representative_grid->PageObjName . "_row_" . $tbl_representative_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_representative->houseNo->Visible) { // houseNo ?>
		<td<?php echo $tbl_representative->houseNo->CellAttributes() ?>>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $tbl_representative_grid->RowCnt ?>_tbl_representative_houseNo" class="control-group tbl_representative_houseNo">
<input type="text" data-field="x_houseNo" name="x<?php echo $tbl_representative_grid->RowIndex ?>_houseNo" id="x<?php echo $tbl_representative_grid->RowIndex ?>_houseNo" size="30" maxlength="255" placeholder="<?php echo $tbl_representative->houseNo->PlaceHolder ?>" value="<?php echo $tbl_representative->houseNo->EditValue ?>"<?php echo $tbl_representative->houseNo->EditAttributes() ?>>
</span>
<input type="hidden" data-field="x_houseNo" name="o<?php echo $tbl_representative_grid->RowIndex ?>_houseNo" id="o<?php echo $tbl_representative_grid->RowIndex ?>_houseNo" value="<?php echo ew_HtmlEncode($tbl_representative->houseNo->OldValue) ?>">
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_representative_grid->RowCnt ?>_tbl_representative_houseNo" class="control-group tbl_representative_houseNo">
<input type="text" data-field="x_houseNo" name="x<?php echo $tbl_representative_grid->RowIndex ?>_houseNo" id="x<?php echo $tbl_representative_grid->RowIndex ?>_houseNo" size="30" maxlength="255" placeholder="<?php echo $tbl_representative->houseNo->PlaceHolder ?>" value="<?php echo $tbl_representative->houseNo->EditValue ?>"<?php echo $tbl_representative->houseNo->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_representative->houseNo->ViewAttributes() ?>>
<?php echo $tbl_representative->houseNo->ListViewValue() ?></span>
<input type="hidden" data-field="x_houseNo" name="x<?php echo $tbl_representative_grid->RowIndex ?>_houseNo" id="x<?php echo $tbl_representative_grid->RowIndex ?>_houseNo" value="<?php echo ew_HtmlEncode($tbl_representative->houseNo->FormValue) ?>">
<input type="hidden" data-field="x_houseNo" name="o<?php echo $tbl_representative_grid->RowIndex ?>_houseNo" id="o<?php echo $tbl_representative_grid->RowIndex ?>_houseNo" value="<?php echo ew_HtmlEncode($tbl_representative->houseNo->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_representative_grid->PageObjName . "_row_" . $tbl_representative_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_representative->CreatedBy->Visible) { // CreatedBy ?>
		<td<?php echo $tbl_representative->CreatedBy->CellAttributes() ?>>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<input type="hidden" data-field="x_CreatedBy" name="o<?php echo $tbl_representative_grid->RowIndex ?>_CreatedBy" id="o<?php echo $tbl_representative_grid->RowIndex ?>_CreatedBy" value="<?php echo ew_HtmlEncode($tbl_representative->CreatedBy->OldValue) ?>">
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_representative->CreatedBy->ViewAttributes() ?>>
<?php echo $tbl_representative->CreatedBy->ListViewValue() ?></span>
<input type="hidden" data-field="x_CreatedBy" name="x<?php echo $tbl_representative_grid->RowIndex ?>_CreatedBy" id="x<?php echo $tbl_representative_grid->RowIndex ?>_CreatedBy" value="<?php echo ew_HtmlEncode($tbl_representative->CreatedBy->FormValue) ?>">
<input type="hidden" data-field="x_CreatedBy" name="o<?php echo $tbl_representative_grid->RowIndex ?>_CreatedBy" id="o<?php echo $tbl_representative_grid->RowIndex ?>_CreatedBy" value="<?php echo ew_HtmlEncode($tbl_representative->CreatedBy->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_representative_grid->PageObjName . "_row_" . $tbl_representative_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_representative->CreatedDate->Visible) { // CreatedDate ?>
		<td<?php echo $tbl_representative->CreatedDate->CellAttributes() ?>>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<input type="hidden" data-field="x_CreatedDate" name="o<?php echo $tbl_representative_grid->RowIndex ?>_CreatedDate" id="o<?php echo $tbl_representative_grid->RowIndex ?>_CreatedDate" value="<?php echo ew_HtmlEncode($tbl_representative->CreatedDate->OldValue) ?>">
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_representative->CreatedDate->ViewAttributes() ?>>
<?php echo $tbl_representative->CreatedDate->ListViewValue() ?></span>
<input type="hidden" data-field="x_CreatedDate" name="x<?php echo $tbl_representative_grid->RowIndex ?>_CreatedDate" id="x<?php echo $tbl_representative_grid->RowIndex ?>_CreatedDate" value="<?php echo ew_HtmlEncode($tbl_representative->CreatedDate->FormValue) ?>">
<input type="hidden" data-field="x_CreatedDate" name="o<?php echo $tbl_representative_grid->RowIndex ?>_CreatedDate" id="o<?php echo $tbl_representative_grid->RowIndex ?>_CreatedDate" value="<?php echo ew_HtmlEncode($tbl_representative->CreatedDate->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_representative_grid->PageObjName . "_row_" . $tbl_representative_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_representative->UpdatedBy->Visible) { // UpdatedBy ?>
		<td<?php echo $tbl_representative->UpdatedBy->CellAttributes() ?>>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<input type="hidden" data-field="x_UpdatedBy" name="o<?php echo $tbl_representative_grid->RowIndex ?>_UpdatedBy" id="o<?php echo $tbl_representative_grid->RowIndex ?>_UpdatedBy" value="<?php echo ew_HtmlEncode($tbl_representative->UpdatedBy->OldValue) ?>">
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_representative->UpdatedBy->ViewAttributes() ?>>
<?php echo $tbl_representative->UpdatedBy->ListViewValue() ?></span>
<input type="hidden" data-field="x_UpdatedBy" name="x<?php echo $tbl_representative_grid->RowIndex ?>_UpdatedBy" id="x<?php echo $tbl_representative_grid->RowIndex ?>_UpdatedBy" value="<?php echo ew_HtmlEncode($tbl_representative->UpdatedBy->FormValue) ?>">
<input type="hidden" data-field="x_UpdatedBy" name="o<?php echo $tbl_representative_grid->RowIndex ?>_UpdatedBy" id="o<?php echo $tbl_representative_grid->RowIndex ?>_UpdatedBy" value="<?php echo ew_HtmlEncode($tbl_representative->UpdatedBy->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_representative_grid->PageObjName . "_row_" . $tbl_representative_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_representative->UpdatedDate->Visible) { // UpdatedDate ?>
		<td<?php echo $tbl_representative->UpdatedDate->CellAttributes() ?>>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<input type="hidden" data-field="x_UpdatedDate" name="o<?php echo $tbl_representative_grid->RowIndex ?>_UpdatedDate" id="o<?php echo $tbl_representative_grid->RowIndex ?>_UpdatedDate" value="<?php echo ew_HtmlEncode($tbl_representative->UpdatedDate->OldValue) ?>">
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<?php } ?>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_representative->UpdatedDate->ViewAttributes() ?>>
<?php echo $tbl_representative->UpdatedDate->ListViewValue() ?></span>
<input type="hidden" data-field="x_UpdatedDate" name="x<?php echo $tbl_representative_grid->RowIndex ?>_UpdatedDate" id="x<?php echo $tbl_representative_grid->RowIndex ?>_UpdatedDate" value="<?php echo ew_HtmlEncode($tbl_representative->UpdatedDate->FormValue) ?>">
<input type="hidden" data-field="x_UpdatedDate" name="o<?php echo $tbl_representative_grid->RowIndex ?>_UpdatedDate" id="o<?php echo $tbl_representative_grid->RowIndex ?>_UpdatedDate" value="<?php echo ew_HtmlEncode($tbl_representative->UpdatedDate->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_representative_grid->PageObjName . "_row_" . $tbl_representative_grid->RowCnt ?>"></a></td>
	<?php } ?>
<?php

// Render list options (body, right)
$tbl_representative_grid->ListOptions->Render("body", "right", $tbl_representative_grid->RowCnt);
?>
	</tr>
<?php if ($tbl_representative->RowType == EW_ROWTYPE_ADD || $tbl_representative->RowType == EW_ROWTYPE_EDIT) { ?>
<script type="text/javascript">
ftbl_representativegrid.UpdateOpts(<?php echo $tbl_representative_grid->RowIndex ?>);
</script>
<?php } ?>
<?php
	}
	} // End delete row checking
	if ($tbl_representative->CurrentAction <> "gridadd" || $tbl_representative->CurrentMode == "copy")
		if (!$tbl_representative_grid->Recordset->EOF) $tbl_representative_grid->Recordset->MoveNext();
}
?>
<?php
	if ($tbl_representative->CurrentMode == "add" || $tbl_representative->CurrentMode == "copy" || $tbl_representative->CurrentMode == "edit") {
		$tbl_representative_grid->RowIndex = '$rowindex$';
		$tbl_representative_grid->LoadDefaultValues();

		// Set row properties
		$tbl_representative->ResetAttrs();
		$tbl_representative->RowAttrs = array_merge($tbl_representative->RowAttrs, array('data-rowindex'=>$tbl_representative_grid->RowIndex, 'id'=>'r0_tbl_representative', 'data-rowtype'=>EW_ROWTYPE_ADD));
		ew_AppendClass($tbl_representative->RowAttrs["class"], "ewTemplate");
		$tbl_representative->RowType = EW_ROWTYPE_ADD;

		// Render row
		$tbl_representative_grid->RenderRow();

		// Render list options
		$tbl_representative_grid->RenderListOptions();
		$tbl_representative_grid->StartRowCnt = 0;
?>
	<tr<?php echo $tbl_representative->RowAttributes() ?>>
<?php

// Render list options (body, left)
$tbl_representative_grid->ListOptions->Render("body", "left", $tbl_representative_grid->RowIndex);
?>
	<?php if ($tbl_representative->authID->Visible) { // authID ?>
		<td>
<?php if ($tbl_representative->CurrentAction <> "F") { ?>
<?php } else { ?>
<span id="el$rowindex$_tbl_representative_authID" class="control-group tbl_representative_authID">
<span<?php echo $tbl_representative->authID->ViewAttributes() ?>>
<?php echo $tbl_representative->authID->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_authID" name="x<?php echo $tbl_representative_grid->RowIndex ?>_authID" id="x<?php echo $tbl_representative_grid->RowIndex ?>_authID" value="<?php echo ew_HtmlEncode($tbl_representative->authID->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_authID" name="o<?php echo $tbl_representative_grid->RowIndex ?>_authID" id="o<?php echo $tbl_representative_grid->RowIndex ?>_authID" value="<?php echo ew_HtmlEncode($tbl_representative->authID->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_representative->PensionerID->Visible) { // PensionerID ?>
		<td>
<?php if ($tbl_representative->CurrentAction <> "F") { ?>
<?php if ($tbl_representative->PensionerID->getSessionValue() <> "") { ?>
<span<?php echo $tbl_representative->PensionerID->ViewAttributes() ?>>
<?php echo $tbl_representative->PensionerID->ListViewValue() ?></span>
<input type="hidden" id="x<?php echo $tbl_representative_grid->RowIndex ?>_PensionerID" name="x<?php echo $tbl_representative_grid->RowIndex ?>_PensionerID" value="<?php echo ew_HtmlEncode($tbl_representative->PensionerID->CurrentValue) ?>">
<?php } else { ?>
<input type="text" data-field="x_PensionerID" name="x<?php echo $tbl_representative_grid->RowIndex ?>_PensionerID" id="x<?php echo $tbl_representative_grid->RowIndex ?>_PensionerID" size="30" maxlength="25" placeholder="<?php echo $tbl_representative->PensionerID->PlaceHolder ?>" value="<?php echo $tbl_representative->PensionerID->EditValue ?>"<?php echo $tbl_representative->PensionerID->EditAttributes() ?>>
<?php } ?>
<?php } else { ?>
<span<?php echo $tbl_representative->PensionerID->ViewAttributes() ?>>
<?php echo $tbl_representative->PensionerID->ViewValue ?></span>
<input type="hidden" data-field="x_PensionerID" name="x<?php echo $tbl_representative_grid->RowIndex ?>_PensionerID" id="x<?php echo $tbl_representative_grid->RowIndex ?>_PensionerID" value="<?php echo ew_HtmlEncode($tbl_representative->PensionerID->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_PensionerID" name="o<?php echo $tbl_representative_grid->RowIndex ?>_PensionerID" id="o<?php echo $tbl_representative_grid->RowIndex ?>_PensionerID" value="<?php echo ew_HtmlEncode($tbl_representative->PensionerID->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_representative->fname->Visible) { // fname ?>
		<td>
<?php if ($tbl_representative->CurrentAction <> "F") { ?>
<span id="el$rowindex$_tbl_representative_fname" class="control-group tbl_representative_fname">
<input type="text" data-field="x_fname" name="x<?php echo $tbl_representative_grid->RowIndex ?>_fname" id="x<?php echo $tbl_representative_grid->RowIndex ?>_fname" size="30" maxlength="40" placeholder="<?php echo $tbl_representative->fname->PlaceHolder ?>" value="<?php echo $tbl_representative->fname->EditValue ?>"<?php echo $tbl_representative->fname->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el$rowindex$_tbl_representative_fname" class="control-group tbl_representative_fname">
<span<?php echo $tbl_representative->fname->ViewAttributes() ?>>
<?php echo $tbl_representative->fname->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_fname" name="x<?php echo $tbl_representative_grid->RowIndex ?>_fname" id="x<?php echo $tbl_representative_grid->RowIndex ?>_fname" value="<?php echo ew_HtmlEncode($tbl_representative->fname->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_fname" name="o<?php echo $tbl_representative_grid->RowIndex ?>_fname" id="o<?php echo $tbl_representative_grid->RowIndex ?>_fname" value="<?php echo ew_HtmlEncode($tbl_representative->fname->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_representative->mname->Visible) { // mname ?>
		<td>
<?php if ($tbl_representative->CurrentAction <> "F") { ?>
<span id="el$rowindex$_tbl_representative_mname" class="control-group tbl_representative_mname">
<input type="text" data-field="x_mname" name="x<?php echo $tbl_representative_grid->RowIndex ?>_mname" id="x<?php echo $tbl_representative_grid->RowIndex ?>_mname" size="30" maxlength="40" placeholder="<?php echo $tbl_representative->mname->PlaceHolder ?>" value="<?php echo $tbl_representative->mname->EditValue ?>"<?php echo $tbl_representative->mname->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el$rowindex$_tbl_representative_mname" class="control-group tbl_representative_mname">
<span<?php echo $tbl_representative->mname->ViewAttributes() ?>>
<?php echo $tbl_representative->mname->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_mname" name="x<?php echo $tbl_representative_grid->RowIndex ?>_mname" id="x<?php echo $tbl_representative_grid->RowIndex ?>_mname" value="<?php echo ew_HtmlEncode($tbl_representative->mname->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_mname" name="o<?php echo $tbl_representative_grid->RowIndex ?>_mname" id="o<?php echo $tbl_representative_grid->RowIndex ?>_mname" value="<?php echo ew_HtmlEncode($tbl_representative->mname->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_representative->lname->Visible) { // lname ?>
		<td>
<?php if ($tbl_representative->CurrentAction <> "F") { ?>
<span id="el$rowindex$_tbl_representative_lname" class="control-group tbl_representative_lname">
<input type="text" data-field="x_lname" name="x<?php echo $tbl_representative_grid->RowIndex ?>_lname" id="x<?php echo $tbl_representative_grid->RowIndex ?>_lname" size="30" maxlength="40" placeholder="<?php echo $tbl_representative->lname->PlaceHolder ?>" value="<?php echo $tbl_representative->lname->EditValue ?>"<?php echo $tbl_representative->lname->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el$rowindex$_tbl_representative_lname" class="control-group tbl_representative_lname">
<span<?php echo $tbl_representative->lname->ViewAttributes() ?>>
<?php echo $tbl_representative->lname->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_lname" name="x<?php echo $tbl_representative_grid->RowIndex ?>_lname" id="x<?php echo $tbl_representative_grid->RowIndex ?>_lname" value="<?php echo ew_HtmlEncode($tbl_representative->lname->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_lname" name="o<?php echo $tbl_representative_grid->RowIndex ?>_lname" id="o<?php echo $tbl_representative_grid->RowIndex ?>_lname" value="<?php echo ew_HtmlEncode($tbl_representative->lname->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_representative->relToPensioner->Visible) { // relToPensioner ?>
		<td>
<?php if ($tbl_representative->CurrentAction <> "F") { ?>
<span id="el$rowindex$_tbl_representative_relToPensioner" class="control-group tbl_representative_relToPensioner">
<select data-field="x_relToPensioner" id="x<?php echo $tbl_representative_grid->RowIndex ?>_relToPensioner" name="x<?php echo $tbl_representative_grid->RowIndex ?>_relToPensioner"<?php echo $tbl_representative->relToPensioner->EditAttributes() ?>>
<?php
if (is_array($tbl_representative->relToPensioner->EditValue)) {
	$arwrk = $tbl_representative->relToPensioner->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_representative->relToPensioner->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_representative->relToPensioner->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `RelationID`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_relationship`";
 $sWhereWrk = "";

 // Call Lookup selecting
 $tbl_representative->Lookup_Selecting($tbl_representative->relToPensioner, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
 $sSqlWrk .= " ORDER BY `Description` ASC";
?>
<input type="hidden" name="s_x<?php echo $tbl_representative_grid->RowIndex ?>_relToPensioner" id="s_x<?php echo $tbl_representative_grid->RowIndex ?>_relToPensioner" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`RelationID` = {filter_value}"); ?>&t0=3">
</span>
<?php } else { ?>
<span id="el$rowindex$_tbl_representative_relToPensioner" class="control-group tbl_representative_relToPensioner">
<span<?php echo $tbl_representative->relToPensioner->ViewAttributes() ?>>
<?php echo $tbl_representative->relToPensioner->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_relToPensioner" name="x<?php echo $tbl_representative_grid->RowIndex ?>_relToPensioner" id="x<?php echo $tbl_representative_grid->RowIndex ?>_relToPensioner" value="<?php echo ew_HtmlEncode($tbl_representative->relToPensioner->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_relToPensioner" name="o<?php echo $tbl_representative_grid->RowIndex ?>_relToPensioner" id="o<?php echo $tbl_representative_grid->RowIndex ?>_relToPensioner" value="<?php echo ew_HtmlEncode($tbl_representative->relToPensioner->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_representative->ContactNo->Visible) { // ContactNo ?>
		<td>
<?php if ($tbl_representative->CurrentAction <> "F") { ?>
<span id="el$rowindex$_tbl_representative_ContactNo" class="control-group tbl_representative_ContactNo">
<input type="text" data-field="x_ContactNo" name="x<?php echo $tbl_representative_grid->RowIndex ?>_ContactNo" id="x<?php echo $tbl_representative_grid->RowIndex ?>_ContactNo" size="30" maxlength="10" placeholder="<?php echo $tbl_representative->ContactNo->PlaceHolder ?>" value="<?php echo $tbl_representative->ContactNo->EditValue ?>"<?php echo $tbl_representative->ContactNo->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el$rowindex$_tbl_representative_ContactNo" class="control-group tbl_representative_ContactNo">
<span<?php echo $tbl_representative->ContactNo->ViewAttributes() ?>>
<?php echo $tbl_representative->ContactNo->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_ContactNo" name="x<?php echo $tbl_representative_grid->RowIndex ?>_ContactNo" id="x<?php echo $tbl_representative_grid->RowIndex ?>_ContactNo" value="<?php echo ew_HtmlEncode($tbl_representative->ContactNo->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_ContactNo" name="o<?php echo $tbl_representative_grid->RowIndex ?>_ContactNo" id="o<?php echo $tbl_representative_grid->RowIndex ?>_ContactNo" value="<?php echo ew_HtmlEncode($tbl_representative->ContactNo->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_representative->auth_Region->Visible) { // auth_Region ?>
		<td>
<?php if ($tbl_representative->CurrentAction <> "F") { ?>
<span id="el$rowindex$_tbl_representative_auth_Region" class="control-group tbl_representative_auth_Region">
<?php $tbl_representative->auth_Region->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x" . $tbl_representative_grid->RowIndex . "_auth_prov']); " . @$tbl_representative->auth_Region->EditAttrs["onchange"]; ?>
<select data-field="x_auth_Region" id="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_Region" name="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_Region"<?php echo $tbl_representative->auth_Region->EditAttributes() ?>>
<?php
if (is_array($tbl_representative->auth_Region->EditValue)) {
	$arwrk = $tbl_representative->auth_Region->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_representative->auth_Region->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_representative->auth_Region->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `region_code`, `region_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_regions`";
 $sWhereWrk = "";

 // Call Lookup selecting
 $tbl_representative->Lookup_Selecting($tbl_representative->auth_Region, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
 $sSqlWrk .= " ORDER BY `region_code` ASC";
?>
<input type="hidden" name="s_x<?php echo $tbl_representative_grid->RowIndex ?>_auth_Region" id="s_x<?php echo $tbl_representative_grid->RowIndex ?>_auth_Region" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`region_code` = {filter_value}"); ?>&t0=21">
</span>
<?php } else { ?>
<span id="el$rowindex$_tbl_representative_auth_Region" class="control-group tbl_representative_auth_Region">
<span<?php echo $tbl_representative->auth_Region->ViewAttributes() ?>>
<?php echo $tbl_representative->auth_Region->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_auth_Region" name="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_Region" id="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_Region" value="<?php echo ew_HtmlEncode($tbl_representative->auth_Region->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_auth_Region" name="o<?php echo $tbl_representative_grid->RowIndex ?>_auth_Region" id="o<?php echo $tbl_representative_grid->RowIndex ?>_auth_Region" value="<?php echo ew_HtmlEncode($tbl_representative->auth_Region->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_representative->auth_prov->Visible) { // auth_prov ?>
		<td>
<?php if ($tbl_representative->CurrentAction <> "F") { ?>
<span id="el$rowindex$_tbl_representative_auth_prov" class="control-group tbl_representative_auth_prov">
<?php $tbl_representative->auth_prov->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x" . $tbl_representative_grid->RowIndex . "_auth_city']); " . @$tbl_representative->auth_prov->EditAttrs["onchange"]; ?>
<select data-field="x_auth_prov" id="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_prov" name="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_prov"<?php echo $tbl_representative->auth_prov->EditAttributes() ?>>
<?php
if (is_array($tbl_representative->auth_prov->EditValue)) {
	$arwrk = $tbl_representative->auth_prov->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_representative->auth_prov->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_representative->auth_prov->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `prov_code`, `prov_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_provinces`";
 $sWhereWrk = "{filter}";

 // Call Lookup selecting
 $tbl_representative->Lookup_Selecting($tbl_representative->auth_prov, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
 $sSqlWrk .= " ORDER BY `prov_name` ASC";
?>
<input type="hidden" name="s_x<?php echo $tbl_representative_grid->RowIndex ?>_auth_prov" id="s_x<?php echo $tbl_representative_grid->RowIndex ?>_auth_prov" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`prov_code` = {filter_value}"); ?>&t0=21&f1=<?php echo ew_Encrypt("`region_code` IN ({filter_value})"); ?>&t1=21">
</span>
<?php } else { ?>
<span id="el$rowindex$_tbl_representative_auth_prov" class="control-group tbl_representative_auth_prov">
<span<?php echo $tbl_representative->auth_prov->ViewAttributes() ?>>
<?php echo $tbl_representative->auth_prov->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_auth_prov" name="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_prov" id="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_prov" value="<?php echo ew_HtmlEncode($tbl_representative->auth_prov->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_auth_prov" name="o<?php echo $tbl_representative_grid->RowIndex ?>_auth_prov" id="o<?php echo $tbl_representative_grid->RowIndex ?>_auth_prov" value="<?php echo ew_HtmlEncode($tbl_representative->auth_prov->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_representative->auth_city->Visible) { // auth_city ?>
		<td>
<?php if ($tbl_representative->CurrentAction <> "F") { ?>
<span id="el$rowindex$_tbl_representative_auth_city" class="control-group tbl_representative_auth_city">
<?php $tbl_representative->auth_city->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x" . $tbl_representative_grid->RowIndex . "_auth_brgy']); " . @$tbl_representative->auth_city->EditAttrs["onchange"]; ?>
<select data-field="x_auth_city" id="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_city" name="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_city"<?php echo $tbl_representative->auth_city->EditAttributes() ?>>
<?php
if (is_array($tbl_representative->auth_city->EditValue)) {
	$arwrk = $tbl_representative->auth_city->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_representative->auth_city->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_representative->auth_city->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `city_code`, `city_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_cities`";
 $sWhereWrk = "{filter}";

 // Call Lookup selecting
 $tbl_representative->Lookup_Selecting($tbl_representative->auth_city, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
 $sSqlWrk .= " ORDER BY `city_name` ASC";
?>
<input type="hidden" name="s_x<?php echo $tbl_representative_grid->RowIndex ?>_auth_city" id="s_x<?php echo $tbl_representative_grid->RowIndex ?>_auth_city" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`city_code` = {filter_value}"); ?>&t0=21&f1=<?php echo ew_Encrypt("`prov_code` IN ({filter_value})"); ?>&t1=21">
</span>
<?php } else { ?>
<span id="el$rowindex$_tbl_representative_auth_city" class="control-group tbl_representative_auth_city">
<span<?php echo $tbl_representative->auth_city->ViewAttributes() ?>>
<?php echo $tbl_representative->auth_city->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_auth_city" name="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_city" id="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_city" value="<?php echo ew_HtmlEncode($tbl_representative->auth_city->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_auth_city" name="o<?php echo $tbl_representative_grid->RowIndex ?>_auth_city" id="o<?php echo $tbl_representative_grid->RowIndex ?>_auth_city" value="<?php echo ew_HtmlEncode($tbl_representative->auth_city->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_representative->auth_brgy->Visible) { // auth_brgy ?>
		<td>
<?php if ($tbl_representative->CurrentAction <> "F") { ?>
<span id="el$rowindex$_tbl_representative_auth_brgy" class="control-group tbl_representative_auth_brgy">
<select data-field="x_auth_brgy" id="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_brgy" name="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_brgy"<?php echo $tbl_representative->auth_brgy->EditAttributes() ?>>
<?php
if (is_array($tbl_representative->auth_brgy->EditValue)) {
	$arwrk = $tbl_representative->auth_brgy->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_representative->auth_brgy->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_representative->auth_brgy->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `brgy_code`, `brgy_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_brgy`";
 $sWhereWrk = "{filter}";

 // Call Lookup selecting
 $tbl_representative->Lookup_Selecting($tbl_representative->auth_brgy, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
 $sSqlWrk .= " ORDER BY `brgy_name` ASC";
?>
<input type="hidden" name="s_x<?php echo $tbl_representative_grid->RowIndex ?>_auth_brgy" id="s_x<?php echo $tbl_representative_grid->RowIndex ?>_auth_brgy" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`brgy_code` = {filter_value}"); ?>&t0=21&f1=<?php echo ew_Encrypt("`city_code` IN ({filter_value})"); ?>&t1=21">
</span>
<?php } else { ?>
<span id="el$rowindex$_tbl_representative_auth_brgy" class="control-group tbl_representative_auth_brgy">
<span<?php echo $tbl_representative->auth_brgy->ViewAttributes() ?>>
<?php echo $tbl_representative->auth_brgy->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_auth_brgy" name="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_brgy" id="x<?php echo $tbl_representative_grid->RowIndex ?>_auth_brgy" value="<?php echo ew_HtmlEncode($tbl_representative->auth_brgy->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_auth_brgy" name="o<?php echo $tbl_representative_grid->RowIndex ?>_auth_brgy" id="o<?php echo $tbl_representative_grid->RowIndex ?>_auth_brgy" value="<?php echo ew_HtmlEncode($tbl_representative->auth_brgy->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_representative->houseNo->Visible) { // houseNo ?>
		<td>
<?php if ($tbl_representative->CurrentAction <> "F") { ?>
<span id="el$rowindex$_tbl_representative_houseNo" class="control-group tbl_representative_houseNo">
<input type="text" data-field="x_houseNo" name="x<?php echo $tbl_representative_grid->RowIndex ?>_houseNo" id="x<?php echo $tbl_representative_grid->RowIndex ?>_houseNo" size="30" maxlength="255" placeholder="<?php echo $tbl_representative->houseNo->PlaceHolder ?>" value="<?php echo $tbl_representative->houseNo->EditValue ?>"<?php echo $tbl_representative->houseNo->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el$rowindex$_tbl_representative_houseNo" class="control-group tbl_representative_houseNo">
<span<?php echo $tbl_representative->houseNo->ViewAttributes() ?>>
<?php echo $tbl_representative->houseNo->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_houseNo" name="x<?php echo $tbl_representative_grid->RowIndex ?>_houseNo" id="x<?php echo $tbl_representative_grid->RowIndex ?>_houseNo" value="<?php echo ew_HtmlEncode($tbl_representative->houseNo->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_houseNo" name="o<?php echo $tbl_representative_grid->RowIndex ?>_houseNo" id="o<?php echo $tbl_representative_grid->RowIndex ?>_houseNo" value="<?php echo ew_HtmlEncode($tbl_representative->houseNo->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_representative->CreatedBy->Visible) { // CreatedBy ?>
		<td>
<?php if ($tbl_representative->CurrentAction <> "F") { ?>
<?php } else { ?>
<span id="el$rowindex$_tbl_representative_CreatedBy" class="control-group tbl_representative_CreatedBy">
<span<?php echo $tbl_representative->CreatedBy->ViewAttributes() ?>>
<?php echo $tbl_representative->CreatedBy->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_CreatedBy" name="x<?php echo $tbl_representative_grid->RowIndex ?>_CreatedBy" id="x<?php echo $tbl_representative_grid->RowIndex ?>_CreatedBy" value="<?php echo ew_HtmlEncode($tbl_representative->CreatedBy->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_CreatedBy" name="o<?php echo $tbl_representative_grid->RowIndex ?>_CreatedBy" id="o<?php echo $tbl_representative_grid->RowIndex ?>_CreatedBy" value="<?php echo ew_HtmlEncode($tbl_representative->CreatedBy->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_representative->CreatedDate->Visible) { // CreatedDate ?>
		<td>
<?php if ($tbl_representative->CurrentAction <> "F") { ?>
<?php } else { ?>
<span id="el$rowindex$_tbl_representative_CreatedDate" class="control-group tbl_representative_CreatedDate">
<span<?php echo $tbl_representative->CreatedDate->ViewAttributes() ?>>
<?php echo $tbl_representative->CreatedDate->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_CreatedDate" name="x<?php echo $tbl_representative_grid->RowIndex ?>_CreatedDate" id="x<?php echo $tbl_representative_grid->RowIndex ?>_CreatedDate" value="<?php echo ew_HtmlEncode($tbl_representative->CreatedDate->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_CreatedDate" name="o<?php echo $tbl_representative_grid->RowIndex ?>_CreatedDate" id="o<?php echo $tbl_representative_grid->RowIndex ?>_CreatedDate" value="<?php echo ew_HtmlEncode($tbl_representative->CreatedDate->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_representative->UpdatedBy->Visible) { // UpdatedBy ?>
		<td>
<?php if ($tbl_representative->CurrentAction <> "F") { ?>
<?php } else { ?>
<span id="el$rowindex$_tbl_representative_UpdatedBy" class="control-group tbl_representative_UpdatedBy">
<span<?php echo $tbl_representative->UpdatedBy->ViewAttributes() ?>>
<?php echo $tbl_representative->UpdatedBy->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_UpdatedBy" name="x<?php echo $tbl_representative_grid->RowIndex ?>_UpdatedBy" id="x<?php echo $tbl_representative_grid->RowIndex ?>_UpdatedBy" value="<?php echo ew_HtmlEncode($tbl_representative->UpdatedBy->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_UpdatedBy" name="o<?php echo $tbl_representative_grid->RowIndex ?>_UpdatedBy" id="o<?php echo $tbl_representative_grid->RowIndex ?>_UpdatedBy" value="<?php echo ew_HtmlEncode($tbl_representative->UpdatedBy->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_representative->UpdatedDate->Visible) { // UpdatedDate ?>
		<td>
<?php if ($tbl_representative->CurrentAction <> "F") { ?>
<?php } else { ?>
<span id="el$rowindex$_tbl_representative_UpdatedDate" class="control-group tbl_representative_UpdatedDate">
<span<?php echo $tbl_representative->UpdatedDate->ViewAttributes() ?>>
<?php echo $tbl_representative->UpdatedDate->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_UpdatedDate" name="x<?php echo $tbl_representative_grid->RowIndex ?>_UpdatedDate" id="x<?php echo $tbl_representative_grid->RowIndex ?>_UpdatedDate" value="<?php echo ew_HtmlEncode($tbl_representative->UpdatedDate->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_UpdatedDate" name="o<?php echo $tbl_representative_grid->RowIndex ?>_UpdatedDate" id="o<?php echo $tbl_representative_grid->RowIndex ?>_UpdatedDate" value="<?php echo ew_HtmlEncode($tbl_representative->UpdatedDate->OldValue) ?>">
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$tbl_representative_grid->ListOptions->Render("body", "right", $tbl_representative_grid->RowCnt);
?>
<script type="text/javascript">
ftbl_representativegrid.UpdateOpts(<?php echo $tbl_representative_grid->RowIndex ?>);
</script>
	</tr>
<?php
}
?>
</tbody>
</table>
<?php if ($tbl_representative->CurrentMode == "add" || $tbl_representative->CurrentMode == "copy") { ?>
<input type="hidden" name="a_list" id="a_list" value="gridinsert">
<input type="hidden" name="<?php echo $tbl_representative_grid->FormKeyCountName ?>" id="<?php echo $tbl_representative_grid->FormKeyCountName ?>" value="<?php echo $tbl_representative_grid->KeyCount ?>">
<?php echo $tbl_representative_grid->MultiSelectKey ?>
<?php } ?>
<?php if ($tbl_representative->CurrentMode == "edit") { ?>
<input type="hidden" name="a_list" id="a_list" value="gridupdate">
<input type="hidden" name="<?php echo $tbl_representative_grid->FormKeyCountName ?>" id="<?php echo $tbl_representative_grid->FormKeyCountName ?>" value="<?php echo $tbl_representative_grid->KeyCount ?>">
<?php echo $tbl_representative_grid->MultiSelectKey ?>
<?php } ?>
<?php if ($tbl_representative->CurrentMode == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
<input type="hidden" name="detailpage" value="ftbl_representativegrid">
</div>
<?php

// Close recordset
if ($tbl_representative_grid->Recordset)
	$tbl_representative_grid->Recordset->Close();
?>
<?php if ($tbl_representative_grid->ShowOtherOptions) { ?>
<div class="ewGridLowerPanel ewListOtherOptions">
<?php
	foreach ($tbl_representative_grid->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
<?php } ?>
</div>
</td></tr></table>
<?php if ($tbl_representative->Export == "") { ?>
<script type="text/javascript">
ftbl_representativegrid.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php } ?>
<?php
$tbl_representative_grid->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php
$tbl_representative_grid->Page_Terminate();
?>
