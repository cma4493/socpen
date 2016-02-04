<?php include_once "tbl_userinfo.php" ?>
<?php

// Create page object
if (!isset($tbl_support_grid)) $tbl_support_grid = new ctbl_support_grid();

// Page init
$tbl_support_grid->Page_Init();

// Page main
$tbl_support_grid->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$tbl_support_grid->Page_Render();
?>
<?php if ($tbl_support->Export == "") { ?>
<script type="text/javascript">

// Page object
var tbl_support_grid = new ew_Page("tbl_support_grid");
tbl_support_grid.PageID = "grid"; // Page ID
var EW_PAGE_ID = tbl_support_grid.PageID; // For backward compatibility

// Form object
var ftbl_supportgrid = new ew_Form("ftbl_supportgrid");
ftbl_supportgrid.FormKeyCountName = '<?php echo $tbl_support_grid->FormKeyCountName ?>';

// Validate form
ftbl_supportgrid.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_meals");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($tbl_support->meals->FldErrMsg()) ?>");

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
ftbl_supportgrid.EmptyRow = function(infix) {
	var fobj = this.Form;
	if (ew_ValueChanged(fobj, infix, "PensionerID", false)) return false;
	if (ew_ValueChanged(fobj, infix, "family_support", false)) return false;
	if (ew_ValueChanged(fobj, infix, "KindSupID", false)) return false;
	if (ew_ValueChanged(fobj, infix, "meals", false)) return false;
	if (ew_ValueChanged(fobj, infix, "disability", false)) return false;
	if (ew_ValueChanged(fobj, infix, "disabilityID", false)) return false;
	if (ew_ValueChanged(fobj, infix, "immobile", false)) return false;
	if (ew_ValueChanged(fobj, infix, "assistiveID", false)) return false;
	if (ew_ValueChanged(fobj, infix, "preEx_illness", false)) return false;
	if (ew_ValueChanged(fobj, infix, "illnessID", false)) return false;
	if (ew_ValueChanged(fobj, infix, "physconditionID", false)) return false;
	return true;
}

// Form_CustomValidate event
ftbl_supportgrid.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftbl_supportgrid.ValidateRequired = true;
<?php } else { ?>
ftbl_supportgrid.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
ftbl_supportgrid.Lists["x_KindSupID"] = {"LinkField":"x_SupportID","Ajax":true,"AutoFill":false,"DisplayFields":["x_SupportKind","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_supportgrid.Lists["x_disabilityID"] = {"LinkField":"x_disabilityID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_supportgrid.Lists["x_assistiveID"] = {"LinkField":"x_assistiveID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Device","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_supportgrid.Lists["x_illnessID"] = {"LinkField":"x_illnessID","Ajax":true,"AutoFill":false,"DisplayFields":["x_description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_supportgrid.Lists["x_physconditionID"] = {"LinkField":"x_physconditionID","Ajax":true,"AutoFill":false,"DisplayFields":["x_physconditionName","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<?php } ?>
<?php if ($tbl_support->getCurrentMasterTable() == "" && $tbl_support_grid->ExportOptions->Visible()) { ?>
<div class="ewListExportOptions"><?php $tbl_support_grid->ExportOptions->Render("body") ?></div>
<?php } ?>
<?php
if ($tbl_support->CurrentAction == "gridadd") {
	if ($tbl_support->CurrentMode == "copy") {
		$bSelectLimit = EW_SELECT_LIMIT;
		if ($bSelectLimit) {
			$tbl_support_grid->TotalRecs = $tbl_support->SelectRecordCount();
			$tbl_support_grid->Recordset = $tbl_support_grid->LoadRecordset($tbl_support_grid->StartRec-1, $tbl_support_grid->DisplayRecs);
		} else {
			if ($tbl_support_grid->Recordset = $tbl_support_grid->LoadRecordset())
				$tbl_support_grid->TotalRecs = $tbl_support_grid->Recordset->RecordCount();
		}
		$tbl_support_grid->StartRec = 1;
		$tbl_support_grid->DisplayRecs = $tbl_support_grid->TotalRecs;
	} else {
		$tbl_support->CurrentFilter = "0=1";
		$tbl_support_grid->StartRec = 1;
		$tbl_support_grid->DisplayRecs = $tbl_support->GridAddRowCount;
	}
	$tbl_support_grid->TotalRecs = $tbl_support_grid->DisplayRecs;
	$tbl_support_grid->StopRec = $tbl_support_grid->DisplayRecs;
} else {
	$bSelectLimit = EW_SELECT_LIMIT;
	if ($bSelectLimit) {
		$tbl_support_grid->TotalRecs = $tbl_support->SelectRecordCount();
	} else {
		if ($tbl_support_grid->Recordset = $tbl_support_grid->LoadRecordset())
			$tbl_support_grid->TotalRecs = $tbl_support_grid->Recordset->RecordCount();
	}
	$tbl_support_grid->StartRec = 1;
	$tbl_support_grid->DisplayRecs = $tbl_support_grid->TotalRecs; // Display all records
	if ($bSelectLimit)
		$tbl_support_grid->Recordset = $tbl_support_grid->LoadRecordset($tbl_support_grid->StartRec-1, $tbl_support_grid->DisplayRecs);
}
$tbl_support_grid->RenderOtherOptions();
?>
<?php $tbl_support_grid->ShowPageHeader(); ?>
<?php
$tbl_support_grid->ShowMessage();
?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div id="ftbl_supportgrid" class="ewForm form-horizontal">
<?php if ($tbl_support_grid->ShowOtherOptions) { ?>
<div class="ewGridUpperPanel ewListOtherOptions">
<?php
	foreach ($tbl_support_grid->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
<?php } ?>
<div id="gmp_tbl_support" class="ewGridMiddlePanel">
<table id="tbl_tbl_supportgrid" class="ewTable ewTableSeparate">
<?php echo $tbl_support->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$tbl_support_grid->RenderListOptions();

// Render list options (header, left)
$tbl_support_grid->ListOptions->Render("header", "left");
?>
<?php if ($tbl_support->supportID->Visible) { // supportID ?>
	<?php if ($tbl_support->SortUrl($tbl_support->supportID) == "") { ?>
		<td><div id="elh_tbl_support_supportID" class="tbl_support_supportID"><div class="ewTableHeaderCaption"><?php echo $tbl_support->supportID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_support_supportID" class="tbl_support_supportID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->supportID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->supportID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->supportID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_support->PensionerID->Visible) { // PensionerID ?>
	<?php if ($tbl_support->SortUrl($tbl_support->PensionerID) == "") { ?>
		<td><div id="elh_tbl_support_PensionerID" class="tbl_support_PensionerID"><div class="ewTableHeaderCaption"><?php echo $tbl_support->PensionerID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_support_PensionerID" class="tbl_support_PensionerID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->PensionerID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->PensionerID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->PensionerID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_support->family_support->Visible) { // family_support ?>
	<?php if ($tbl_support->SortUrl($tbl_support->family_support) == "") { ?>
		<td><div id="elh_tbl_support_family_support" class="tbl_support_family_support"><div class="ewTableHeaderCaption"><?php echo $tbl_support->family_support->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_support_family_support" class="tbl_support_family_support">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->family_support->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->family_support->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->family_support->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_support->KindSupID->Visible) { // KindSupID ?>
	<?php if ($tbl_support->SortUrl($tbl_support->KindSupID) == "") { ?>
		<td><div id="elh_tbl_support_KindSupID" class="tbl_support_KindSupID"><div class="ewTableHeaderCaption"><?php echo $tbl_support->KindSupID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_support_KindSupID" class="tbl_support_KindSupID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->KindSupID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->KindSupID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->KindSupID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_support->meals->Visible) { // meals ?>
	<?php if ($tbl_support->SortUrl($tbl_support->meals) == "") { ?>
		<td><div id="elh_tbl_support_meals" class="tbl_support_meals"><div class="ewTableHeaderCaption"><?php echo $tbl_support->meals->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_support_meals" class="tbl_support_meals">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->meals->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->meals->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->meals->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_support->disability->Visible) { // disability ?>
	<?php if ($tbl_support->SortUrl($tbl_support->disability) == "") { ?>
		<td><div id="elh_tbl_support_disability" class="tbl_support_disability"><div class="ewTableHeaderCaption"><?php echo $tbl_support->disability->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_support_disability" class="tbl_support_disability">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->disability->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->disability->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->disability->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_support->disabilityID->Visible) { // disabilityID ?>
	<?php if ($tbl_support->SortUrl($tbl_support->disabilityID) == "") { ?>
		<td><div id="elh_tbl_support_disabilityID" class="tbl_support_disabilityID"><div class="ewTableHeaderCaption"><?php echo $tbl_support->disabilityID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_support_disabilityID" class="tbl_support_disabilityID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->disabilityID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->disabilityID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->disabilityID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_support->immobile->Visible) { // immobile ?>
	<?php if ($tbl_support->SortUrl($tbl_support->immobile) == "") { ?>
		<td><div id="elh_tbl_support_immobile" class="tbl_support_immobile"><div class="ewTableHeaderCaption"><?php echo $tbl_support->immobile->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_support_immobile" class="tbl_support_immobile">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->immobile->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->immobile->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->immobile->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_support->assistiveID->Visible) { // assistiveID ?>
	<?php if ($tbl_support->SortUrl($tbl_support->assistiveID) == "") { ?>
		<td><div id="elh_tbl_support_assistiveID" class="tbl_support_assistiveID"><div class="ewTableHeaderCaption"><?php echo $tbl_support->assistiveID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_support_assistiveID" class="tbl_support_assistiveID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->assistiveID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->assistiveID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->assistiveID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_support->preEx_illness->Visible) { // preEx_illness ?>
	<?php if ($tbl_support->SortUrl($tbl_support->preEx_illness) == "") { ?>
		<td><div id="elh_tbl_support_preEx_illness" class="tbl_support_preEx_illness"><div class="ewTableHeaderCaption"><?php echo $tbl_support->preEx_illness->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_support_preEx_illness" class="tbl_support_preEx_illness">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->preEx_illness->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->preEx_illness->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->preEx_illness->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_support->illnessID->Visible) { // illnessID ?>
	<?php if ($tbl_support->SortUrl($tbl_support->illnessID) == "") { ?>
		<td><div id="elh_tbl_support_illnessID" class="tbl_support_illnessID"><div class="ewTableHeaderCaption"><?php echo $tbl_support->illnessID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_support_illnessID" class="tbl_support_illnessID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->illnessID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->illnessID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->illnessID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_support->physconditionID->Visible) { // physconditionID ?>
	<?php if ($tbl_support->SortUrl($tbl_support->physconditionID) == "") { ?>
		<td><div id="elh_tbl_support_physconditionID" class="tbl_support_physconditionID"><div class="ewTableHeaderCaption"><?php echo $tbl_support->physconditionID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_support_physconditionID" class="tbl_support_physconditionID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->physconditionID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->physconditionID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->physconditionID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_support->CreatedBy->Visible) { // CreatedBy ?>
	<?php if ($tbl_support->SortUrl($tbl_support->CreatedBy) == "") { ?>
		<td><div id="elh_tbl_support_CreatedBy" class="tbl_support_CreatedBy"><div class="ewTableHeaderCaption"><?php echo $tbl_support->CreatedBy->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_support_CreatedBy" class="tbl_support_CreatedBy">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->CreatedBy->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->CreatedBy->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->CreatedBy->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_support->CreatedDate->Visible) { // CreatedDate ?>
	<?php if ($tbl_support->SortUrl($tbl_support->CreatedDate) == "") { ?>
		<td><div id="elh_tbl_support_CreatedDate" class="tbl_support_CreatedDate"><div class="ewTableHeaderCaption"><?php echo $tbl_support->CreatedDate->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_support_CreatedDate" class="tbl_support_CreatedDate">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->CreatedDate->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->CreatedDate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->CreatedDate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_support->UpdatedBy->Visible) { // UpdatedBy ?>
	<?php if ($tbl_support->SortUrl($tbl_support->UpdatedBy) == "") { ?>
		<td><div id="elh_tbl_support_UpdatedBy" class="tbl_support_UpdatedBy"><div class="ewTableHeaderCaption"><?php echo $tbl_support->UpdatedBy->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_support_UpdatedBy" class="tbl_support_UpdatedBy">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->UpdatedBy->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->UpdatedBy->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->UpdatedBy->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_support->UpdatedDate->Visible) { // UpdatedDate ?>
	<?php if ($tbl_support->SortUrl($tbl_support->UpdatedDate) == "") { ?>
		<td><div id="elh_tbl_support_UpdatedDate" class="tbl_support_UpdatedDate"><div class="ewTableHeaderCaption"><?php echo $tbl_support->UpdatedDate->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_support_UpdatedDate" class="tbl_support_UpdatedDate">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->UpdatedDate->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->UpdatedDate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->UpdatedDate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$tbl_support_grid->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
$tbl_support_grid->StartRec = 1;
$tbl_support_grid->StopRec = $tbl_support_grid->TotalRecs; // Show all records

// Restore number of post back records
if ($objForm) {
	$objForm->Index = -1;
	if ($objForm->HasValue($tbl_support_grid->FormKeyCountName) && ($tbl_support->CurrentAction == "gridadd" || $tbl_support->CurrentAction == "gridedit" || $tbl_support->CurrentAction == "F")) {
		$tbl_support_grid->KeyCount = $objForm->GetValue($tbl_support_grid->FormKeyCountName);
		$tbl_support_grid->StopRec = $tbl_support_grid->StartRec + $tbl_support_grid->KeyCount - 1;
	}
}
$tbl_support_grid->RecCnt = $tbl_support_grid->StartRec - 1;
if ($tbl_support_grid->Recordset && !$tbl_support_grid->Recordset->EOF) {
	$tbl_support_grid->Recordset->MoveFirst();
	if (!$bSelectLimit && $tbl_support_grid->StartRec > 1)
		$tbl_support_grid->Recordset->Move($tbl_support_grid->StartRec - 1);
} elseif (!$tbl_support->AllowAddDeleteRow && $tbl_support_grid->StopRec == 0) {
	$tbl_support_grid->StopRec = $tbl_support->GridAddRowCount;
}

// Initialize aggregate
$tbl_support->RowType = EW_ROWTYPE_AGGREGATEINIT;
$tbl_support->ResetAttrs();
$tbl_support_grid->RenderRow();
if ($tbl_support->CurrentAction == "gridadd")
	$tbl_support_grid->RowIndex = 0;
if ($tbl_support->CurrentAction == "gridedit")
	$tbl_support_grid->RowIndex = 0;
while ($tbl_support_grid->RecCnt < $tbl_support_grid->StopRec) {
	$tbl_support_grid->RecCnt++;
	if (intval($tbl_support_grid->RecCnt) >= intval($tbl_support_grid->StartRec)) {
		$tbl_support_grid->RowCnt++;
		if ($tbl_support->CurrentAction == "gridadd" || $tbl_support->CurrentAction == "gridedit" || $tbl_support->CurrentAction == "F") {
			$tbl_support_grid->RowIndex++;
			$objForm->Index = $tbl_support_grid->RowIndex;
			if ($objForm->HasValue($tbl_support_grid->FormActionName))
				$tbl_support_grid->RowAction = strval($objForm->GetValue($tbl_support_grid->FormActionName));
			elseif ($tbl_support->CurrentAction == "gridadd")
				$tbl_support_grid->RowAction = "insert";
			else
				$tbl_support_grid->RowAction = "";
		}

		// Set up key count
		$tbl_support_grid->KeyCount = $tbl_support_grid->RowIndex;

		// Init row class and style
		$tbl_support->ResetAttrs();
		$tbl_support->CssClass = "";
		if ($tbl_support->CurrentAction == "gridadd") {
			if ($tbl_support->CurrentMode == "copy") {
				$tbl_support_grid->LoadRowValues($tbl_support_grid->Recordset); // Load row values
				$tbl_support_grid->SetRecordKey($tbl_support_grid->RowOldKey, $tbl_support_grid->Recordset); // Set old record key
			} else {
				$tbl_support_grid->LoadDefaultValues(); // Load default values
				$tbl_support_grid->RowOldKey = ""; // Clear old key value
			}
		} else {
			$tbl_support_grid->LoadRowValues($tbl_support_grid->Recordset); // Load row values
		}
		$tbl_support->RowType = EW_ROWTYPE_VIEW; // Render view
		if ($tbl_support->CurrentAction == "gridadd") // Grid add
			$tbl_support->RowType = EW_ROWTYPE_ADD; // Render add
		if ($tbl_support->CurrentAction == "gridadd" && $tbl_support->EventCancelled && !$objForm->HasValue("k_blankrow")) // Insert failed
			$tbl_support_grid->RestoreCurrentRowFormValues($tbl_support_grid->RowIndex); // Restore form values
		if ($tbl_support->CurrentAction == "gridedit") { // Grid edit
			if ($tbl_support->EventCancelled) {
				$tbl_support_grid->RestoreCurrentRowFormValues($tbl_support_grid->RowIndex); // Restore form values
			}
			if ($tbl_support_grid->RowAction == "insert")
				$tbl_support->RowType = EW_ROWTYPE_ADD; // Render add
			else
				$tbl_support->RowType = EW_ROWTYPE_EDIT; // Render edit
		}
		if ($tbl_support->CurrentAction == "gridedit" && ($tbl_support->RowType == EW_ROWTYPE_EDIT || $tbl_support->RowType == EW_ROWTYPE_ADD) && $tbl_support->EventCancelled) // Update failed
			$tbl_support_grid->RestoreCurrentRowFormValues($tbl_support_grid->RowIndex); // Restore form values
		if ($tbl_support->RowType == EW_ROWTYPE_EDIT) // Edit row
			$tbl_support_grid->EditRowCnt++;
		if ($tbl_support->CurrentAction == "F") // Confirm row
			$tbl_support_grid->RestoreCurrentRowFormValues($tbl_support_grid->RowIndex); // Restore form values

		// Set up row id / data-rowindex
		$tbl_support->RowAttrs = array_merge($tbl_support->RowAttrs, array('data-rowindex'=>$tbl_support_grid->RowCnt, 'id'=>'r' . $tbl_support_grid->RowCnt . '_tbl_support', 'data-rowtype'=>$tbl_support->RowType));

		// Render row
		$tbl_support_grid->RenderRow();

		// Render list options
		$tbl_support_grid->RenderListOptions();

		// Skip delete row / empty row for confirm page
		if ($tbl_support_grid->RowAction <> "delete" && $tbl_support_grid->RowAction <> "insertdelete" && !($tbl_support_grid->RowAction == "insert" && $tbl_support->CurrentAction == "F" && $tbl_support_grid->EmptyRow())) {
?>
	<tr<?php echo $tbl_support->RowAttributes() ?>>
<?php

// Render list options (body, left)
$tbl_support_grid->ListOptions->Render("body", "left", $tbl_support_grid->RowCnt);
?>
	<?php if ($tbl_support->supportID->Visible) { // supportID ?>
		<td<?php echo $tbl_support->supportID->CellAttributes() ?>>
<?php if ($tbl_support->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<input type="hidden" data-field="x_supportID" name="o<?php echo $tbl_support_grid->RowIndex ?>_supportID" id="o<?php echo $tbl_support_grid->RowIndex ?>_supportID" value="<?php echo ew_HtmlEncode($tbl_support->supportID->OldValue) ?>">
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_support_grid->RowCnt ?>_tbl_support_supportID" class="control-group tbl_support_supportID">
<span<?php echo $tbl_support->supportID->ViewAttributes() ?>>
<?php echo $tbl_support->supportID->EditValue ?></span>
</span>
<input type="hidden" data-field="x_supportID" name="x<?php echo $tbl_support_grid->RowIndex ?>_supportID" id="x<?php echo $tbl_support_grid->RowIndex ?>_supportID" value="<?php echo ew_HtmlEncode($tbl_support->supportID->CurrentValue) ?>">
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_support->supportID->ViewAttributes() ?>>
<?php echo $tbl_support->supportID->ListViewValue() ?></span>
<input type="hidden" data-field="x_supportID" name="x<?php echo $tbl_support_grid->RowIndex ?>_supportID" id="x<?php echo $tbl_support_grid->RowIndex ?>_supportID" value="<?php echo ew_HtmlEncode($tbl_support->supportID->FormValue) ?>">
<input type="hidden" data-field="x_supportID" name="o<?php echo $tbl_support_grid->RowIndex ?>_supportID" id="o<?php echo $tbl_support_grid->RowIndex ?>_supportID" value="<?php echo ew_HtmlEncode($tbl_support->supportID->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_support_grid->PageObjName . "_row_" . $tbl_support_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_support->PensionerID->Visible) { // PensionerID ?>
		<td<?php echo $tbl_support->PensionerID->CellAttributes() ?>>
<?php if ($tbl_support->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<?php if ($tbl_support->PensionerID->getSessionValue() <> "") { ?>
<span<?php echo $tbl_support->PensionerID->ViewAttributes() ?>>
<?php echo $tbl_support->PensionerID->ListViewValue() ?></span>
<input type="hidden" id="x<?php echo $tbl_support_grid->RowIndex ?>_PensionerID" name="x<?php echo $tbl_support_grid->RowIndex ?>_PensionerID" value="<?php echo ew_HtmlEncode($tbl_support->PensionerID->CurrentValue) ?>">
<?php } else { ?>
<input type="text" data-field="x_PensionerID" name="x<?php echo $tbl_support_grid->RowIndex ?>_PensionerID" id="x<?php echo $tbl_support_grid->RowIndex ?>_PensionerID" size="30" maxlength="25" placeholder="<?php echo $tbl_support->PensionerID->PlaceHolder ?>" value="<?php echo $tbl_support->PensionerID->EditValue ?>"<?php echo $tbl_support->PensionerID->EditAttributes() ?>>
<?php } ?>
<input type="hidden" data-field="x_PensionerID" name="o<?php echo $tbl_support_grid->RowIndex ?>_PensionerID" id="o<?php echo $tbl_support_grid->RowIndex ?>_PensionerID" value="<?php echo ew_HtmlEncode($tbl_support->PensionerID->OldValue) ?>">
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<?php if ($tbl_support->PensionerID->getSessionValue() <> "") { ?>
<span<?php echo $tbl_support->PensionerID->ViewAttributes() ?>>
<?php echo $tbl_support->PensionerID->ListViewValue() ?></span>
<input type="hidden" id="x<?php echo $tbl_support_grid->RowIndex ?>_PensionerID" name="x<?php echo $tbl_support_grid->RowIndex ?>_PensionerID" value="<?php echo ew_HtmlEncode($tbl_support->PensionerID->CurrentValue) ?>">
<?php } else { ?>
<input type="text" data-field="x_PensionerID" name="x<?php echo $tbl_support_grid->RowIndex ?>_PensionerID" id="x<?php echo $tbl_support_grid->RowIndex ?>_PensionerID" size="30" maxlength="25" placeholder="<?php echo $tbl_support->PensionerID->PlaceHolder ?>" value="<?php echo $tbl_support->PensionerID->EditValue ?>"<?php echo $tbl_support->PensionerID->EditAttributes() ?>>
<?php } ?>
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_support->PensionerID->ViewAttributes() ?>>
<?php echo $tbl_support->PensionerID->ListViewValue() ?></span>
<input type="hidden" data-field="x_PensionerID" name="x<?php echo $tbl_support_grid->RowIndex ?>_PensionerID" id="x<?php echo $tbl_support_grid->RowIndex ?>_PensionerID" value="<?php echo ew_HtmlEncode($tbl_support->PensionerID->FormValue) ?>">
<input type="hidden" data-field="x_PensionerID" name="o<?php echo $tbl_support_grid->RowIndex ?>_PensionerID" id="o<?php echo $tbl_support_grid->RowIndex ?>_PensionerID" value="<?php echo ew_HtmlEncode($tbl_support->PensionerID->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_support_grid->PageObjName . "_row_" . $tbl_support_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_support->family_support->Visible) { // family_support ?>
		<td<?php echo $tbl_support->family_support->CellAttributes() ?>>
<?php if ($tbl_support->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $tbl_support_grid->RowCnt ?>_tbl_support_family_support" class="control-group tbl_support_family_support">
<select data-field="x_family_support" id="x<?php echo $tbl_support_grid->RowIndex ?>_family_support" name="x<?php echo $tbl_support_grid->RowIndex ?>_family_support"<?php echo $tbl_support->family_support->EditAttributes() ?>>
<?php
if (is_array($tbl_support->family_support->EditValue)) {
	$arwrk = $tbl_support->family_support->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->family_support->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_support->family_support->OldValue = "";
?>
</select>
</span>
<input type="hidden" data-field="x_family_support" name="o<?php echo $tbl_support_grid->RowIndex ?>_family_support" id="o<?php echo $tbl_support_grid->RowIndex ?>_family_support" value="<?php echo ew_HtmlEncode($tbl_support->family_support->OldValue) ?>">
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_support_grid->RowCnt ?>_tbl_support_family_support" class="control-group tbl_support_family_support">
<select data-field="x_family_support" id="x<?php echo $tbl_support_grid->RowIndex ?>_family_support" name="x<?php echo $tbl_support_grid->RowIndex ?>_family_support"<?php echo $tbl_support->family_support->EditAttributes() ?>>
<?php
if (is_array($tbl_support->family_support->EditValue)) {
	$arwrk = $tbl_support->family_support->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->family_support->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_support->family_support->OldValue = "";
?>
</select>
</span>
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_support->family_support->ViewAttributes() ?>>
<?php echo $tbl_support->family_support->ListViewValue() ?></span>
<input type="hidden" data-field="x_family_support" name="x<?php echo $tbl_support_grid->RowIndex ?>_family_support" id="x<?php echo $tbl_support_grid->RowIndex ?>_family_support" value="<?php echo ew_HtmlEncode($tbl_support->family_support->FormValue) ?>">
<input type="hidden" data-field="x_family_support" name="o<?php echo $tbl_support_grid->RowIndex ?>_family_support" id="o<?php echo $tbl_support_grid->RowIndex ?>_family_support" value="<?php echo ew_HtmlEncode($tbl_support->family_support->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_support_grid->PageObjName . "_row_" . $tbl_support_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_support->KindSupID->Visible) { // KindSupID ?>
		<td<?php echo $tbl_support->KindSupID->CellAttributes() ?>>
<?php if ($tbl_support->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $tbl_support_grid->RowCnt ?>_tbl_support_KindSupID" class="control-group tbl_support_KindSupID">
<select data-field="x_KindSupID" id="x<?php echo $tbl_support_grid->RowIndex ?>_KindSupID" name="x<?php echo $tbl_support_grid->RowIndex ?>_KindSupID"<?php echo $tbl_support->KindSupID->EditAttributes() ?>>
<?php
if (is_array($tbl_support->KindSupID->EditValue)) {
	$arwrk = $tbl_support->KindSupID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->KindSupID->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_support->KindSupID->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `SupportID`, `SupportKind` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_support`";
 $sWhereWrk = "";

 // Call Lookup selecting
 $tbl_support->Lookup_Selecting($tbl_support->KindSupID, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
 $sSqlWrk .= " ORDER BY `SupportID` ASC";
?>
<input type="hidden" name="s_x<?php echo $tbl_support_grid->RowIndex ?>_KindSupID" id="s_x<?php echo $tbl_support_grid->RowIndex ?>_KindSupID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`SupportID` = {filter_value}"); ?>&t0=3">
</span>
<input type="hidden" data-field="x_KindSupID" name="o<?php echo $tbl_support_grid->RowIndex ?>_KindSupID" id="o<?php echo $tbl_support_grid->RowIndex ?>_KindSupID" value="<?php echo ew_HtmlEncode($tbl_support->KindSupID->OldValue) ?>">
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_support_grid->RowCnt ?>_tbl_support_KindSupID" class="control-group tbl_support_KindSupID">
<select data-field="x_KindSupID" id="x<?php echo $tbl_support_grid->RowIndex ?>_KindSupID" name="x<?php echo $tbl_support_grid->RowIndex ?>_KindSupID"<?php echo $tbl_support->KindSupID->EditAttributes() ?>>
<?php
if (is_array($tbl_support->KindSupID->EditValue)) {
	$arwrk = $tbl_support->KindSupID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->KindSupID->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_support->KindSupID->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `SupportID`, `SupportKind` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_support`";
 $sWhereWrk = "";

 // Call Lookup selecting
 $tbl_support->Lookup_Selecting($tbl_support->KindSupID, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
 $sSqlWrk .= " ORDER BY `SupportID` ASC";
?>
<input type="hidden" name="s_x<?php echo $tbl_support_grid->RowIndex ?>_KindSupID" id="s_x<?php echo $tbl_support_grid->RowIndex ?>_KindSupID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`SupportID` = {filter_value}"); ?>&t0=3">
</span>
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_support->KindSupID->ViewAttributes() ?>>
<?php echo $tbl_support->KindSupID->ListViewValue() ?></span>
<input type="hidden" data-field="x_KindSupID" name="x<?php echo $tbl_support_grid->RowIndex ?>_KindSupID" id="x<?php echo $tbl_support_grid->RowIndex ?>_KindSupID" value="<?php echo ew_HtmlEncode($tbl_support->KindSupID->FormValue) ?>">
<input type="hidden" data-field="x_KindSupID" name="o<?php echo $tbl_support_grid->RowIndex ?>_KindSupID" id="o<?php echo $tbl_support_grid->RowIndex ?>_KindSupID" value="<?php echo ew_HtmlEncode($tbl_support->KindSupID->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_support_grid->PageObjName . "_row_" . $tbl_support_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_support->meals->Visible) { // meals ?>
		<td<?php echo $tbl_support->meals->CellAttributes() ?>>
<?php if ($tbl_support->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $tbl_support_grid->RowCnt ?>_tbl_support_meals" class="control-group tbl_support_meals">
<input type="text" data-field="x_meals" name="x<?php echo $tbl_support_grid->RowIndex ?>_meals" id="x<?php echo $tbl_support_grid->RowIndex ?>_meals" size="30" placeholder="<?php echo $tbl_support->meals->PlaceHolder ?>" value="<?php echo $tbl_support->meals->EditValue ?>"<?php echo $tbl_support->meals->EditAttributes() ?>>
</span>
<input type="hidden" data-field="x_meals" name="o<?php echo $tbl_support_grid->RowIndex ?>_meals" id="o<?php echo $tbl_support_grid->RowIndex ?>_meals" value="<?php echo ew_HtmlEncode($tbl_support->meals->OldValue) ?>">
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_support_grid->RowCnt ?>_tbl_support_meals" class="control-group tbl_support_meals">
<input type="text" data-field="x_meals" name="x<?php echo $tbl_support_grid->RowIndex ?>_meals" id="x<?php echo $tbl_support_grid->RowIndex ?>_meals" size="30" placeholder="<?php echo $tbl_support->meals->PlaceHolder ?>" value="<?php echo $tbl_support->meals->EditValue ?>"<?php echo $tbl_support->meals->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_support->meals->ViewAttributes() ?>>
<?php echo $tbl_support->meals->ListViewValue() ?></span>
<input type="hidden" data-field="x_meals" name="x<?php echo $tbl_support_grid->RowIndex ?>_meals" id="x<?php echo $tbl_support_grid->RowIndex ?>_meals" value="<?php echo ew_HtmlEncode($tbl_support->meals->FormValue) ?>">
<input type="hidden" data-field="x_meals" name="o<?php echo $tbl_support_grid->RowIndex ?>_meals" id="o<?php echo $tbl_support_grid->RowIndex ?>_meals" value="<?php echo ew_HtmlEncode($tbl_support->meals->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_support_grid->PageObjName . "_row_" . $tbl_support_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_support->disability->Visible) { // disability ?>
		<td<?php echo $tbl_support->disability->CellAttributes() ?>>
<?php if ($tbl_support->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $tbl_support_grid->RowCnt ?>_tbl_support_disability" class="control-group tbl_support_disability">
<select data-field="x_disability" id="x<?php echo $tbl_support_grid->RowIndex ?>_disability" name="x<?php echo $tbl_support_grid->RowIndex ?>_disability"<?php echo $tbl_support->disability->EditAttributes() ?>>
<?php
if (is_array($tbl_support->disability->EditValue)) {
	$arwrk = $tbl_support->disability->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->disability->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_support->disability->OldValue = "";
?>
</select>
</span>
<input type="hidden" data-field="x_disability" name="o<?php echo $tbl_support_grid->RowIndex ?>_disability" id="o<?php echo $tbl_support_grid->RowIndex ?>_disability" value="<?php echo ew_HtmlEncode($tbl_support->disability->OldValue) ?>">
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_support_grid->RowCnt ?>_tbl_support_disability" class="control-group tbl_support_disability">
<select data-field="x_disability" id="x<?php echo $tbl_support_grid->RowIndex ?>_disability" name="x<?php echo $tbl_support_grid->RowIndex ?>_disability"<?php echo $tbl_support->disability->EditAttributes() ?>>
<?php
if (is_array($tbl_support->disability->EditValue)) {
	$arwrk = $tbl_support->disability->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->disability->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_support->disability->OldValue = "";
?>
</select>
</span>
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_support->disability->ViewAttributes() ?>>
<?php echo $tbl_support->disability->ListViewValue() ?></span>
<input type="hidden" data-field="x_disability" name="x<?php echo $tbl_support_grid->RowIndex ?>_disability" id="x<?php echo $tbl_support_grid->RowIndex ?>_disability" value="<?php echo ew_HtmlEncode($tbl_support->disability->FormValue) ?>">
<input type="hidden" data-field="x_disability" name="o<?php echo $tbl_support_grid->RowIndex ?>_disability" id="o<?php echo $tbl_support_grid->RowIndex ?>_disability" value="<?php echo ew_HtmlEncode($tbl_support->disability->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_support_grid->PageObjName . "_row_" . $tbl_support_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_support->disabilityID->Visible) { // disabilityID ?>
		<td<?php echo $tbl_support->disabilityID->CellAttributes() ?>>
<?php if ($tbl_support->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $tbl_support_grid->RowCnt ?>_tbl_support_disabilityID" class="control-group tbl_support_disabilityID">
<select data-field="x_disabilityID" id="x<?php echo $tbl_support_grid->RowIndex ?>_disabilityID" name="x<?php echo $tbl_support_grid->RowIndex ?>_disabilityID"<?php echo $tbl_support->disabilityID->EditAttributes() ?>>
<?php
if (is_array($tbl_support->disabilityID->EditValue)) {
	$arwrk = $tbl_support->disabilityID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->disabilityID->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_support->disabilityID->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `disabilityID`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_disability`";
 $sWhereWrk = "";

 // Call Lookup selecting
 $tbl_support->Lookup_Selecting($tbl_support->disabilityID, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
 $sSqlWrk .= " ORDER BY `disabilityID` ASC";
?>
<input type="hidden" name="s_x<?php echo $tbl_support_grid->RowIndex ?>_disabilityID" id="s_x<?php echo $tbl_support_grid->RowIndex ?>_disabilityID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`disabilityID` = {filter_value}"); ?>&t0=3">
</span>
<input type="hidden" data-field="x_disabilityID" name="o<?php echo $tbl_support_grid->RowIndex ?>_disabilityID" id="o<?php echo $tbl_support_grid->RowIndex ?>_disabilityID" value="<?php echo ew_HtmlEncode($tbl_support->disabilityID->OldValue) ?>">
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_support_grid->RowCnt ?>_tbl_support_disabilityID" class="control-group tbl_support_disabilityID">
<select data-field="x_disabilityID" id="x<?php echo $tbl_support_grid->RowIndex ?>_disabilityID" name="x<?php echo $tbl_support_grid->RowIndex ?>_disabilityID"<?php echo $tbl_support->disabilityID->EditAttributes() ?>>
<?php
if (is_array($tbl_support->disabilityID->EditValue)) {
	$arwrk = $tbl_support->disabilityID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->disabilityID->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_support->disabilityID->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `disabilityID`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_disability`";
 $sWhereWrk = "";

 // Call Lookup selecting
 $tbl_support->Lookup_Selecting($tbl_support->disabilityID, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
 $sSqlWrk .= " ORDER BY `disabilityID` ASC";
?>
<input type="hidden" name="s_x<?php echo $tbl_support_grid->RowIndex ?>_disabilityID" id="s_x<?php echo $tbl_support_grid->RowIndex ?>_disabilityID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`disabilityID` = {filter_value}"); ?>&t0=3">
</span>
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_support->disabilityID->ViewAttributes() ?>>
<?php echo $tbl_support->disabilityID->ListViewValue() ?></span>
<input type="hidden" data-field="x_disabilityID" name="x<?php echo $tbl_support_grid->RowIndex ?>_disabilityID" id="x<?php echo $tbl_support_grid->RowIndex ?>_disabilityID" value="<?php echo ew_HtmlEncode($tbl_support->disabilityID->FormValue) ?>">
<input type="hidden" data-field="x_disabilityID" name="o<?php echo $tbl_support_grid->RowIndex ?>_disabilityID" id="o<?php echo $tbl_support_grid->RowIndex ?>_disabilityID" value="<?php echo ew_HtmlEncode($tbl_support->disabilityID->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_support_grid->PageObjName . "_row_" . $tbl_support_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_support->immobile->Visible) { // immobile ?>
		<td<?php echo $tbl_support->immobile->CellAttributes() ?>>
<?php if ($tbl_support->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $tbl_support_grid->RowCnt ?>_tbl_support_immobile" class="control-group tbl_support_immobile">
<select data-field="x_immobile" id="x<?php echo $tbl_support_grid->RowIndex ?>_immobile" name="x<?php echo $tbl_support_grid->RowIndex ?>_immobile"<?php echo $tbl_support->immobile->EditAttributes() ?>>
<?php
if (is_array($tbl_support->immobile->EditValue)) {
	$arwrk = $tbl_support->immobile->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->immobile->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_support->immobile->OldValue = "";
?>
</select>
</span>
<input type="hidden" data-field="x_immobile" name="o<?php echo $tbl_support_grid->RowIndex ?>_immobile" id="o<?php echo $tbl_support_grid->RowIndex ?>_immobile" value="<?php echo ew_HtmlEncode($tbl_support->immobile->OldValue) ?>">
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_support_grid->RowCnt ?>_tbl_support_immobile" class="control-group tbl_support_immobile">
<select data-field="x_immobile" id="x<?php echo $tbl_support_grid->RowIndex ?>_immobile" name="x<?php echo $tbl_support_grid->RowIndex ?>_immobile"<?php echo $tbl_support->immobile->EditAttributes() ?>>
<?php
if (is_array($tbl_support->immobile->EditValue)) {
	$arwrk = $tbl_support->immobile->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->immobile->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_support->immobile->OldValue = "";
?>
</select>
</span>
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_support->immobile->ViewAttributes() ?>>
<?php echo $tbl_support->immobile->ListViewValue() ?></span>
<input type="hidden" data-field="x_immobile" name="x<?php echo $tbl_support_grid->RowIndex ?>_immobile" id="x<?php echo $tbl_support_grid->RowIndex ?>_immobile" value="<?php echo ew_HtmlEncode($tbl_support->immobile->FormValue) ?>">
<input type="hidden" data-field="x_immobile" name="o<?php echo $tbl_support_grid->RowIndex ?>_immobile" id="o<?php echo $tbl_support_grid->RowIndex ?>_immobile" value="<?php echo ew_HtmlEncode($tbl_support->immobile->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_support_grid->PageObjName . "_row_" . $tbl_support_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_support->assistiveID->Visible) { // assistiveID ?>
		<td<?php echo $tbl_support->assistiveID->CellAttributes() ?>>
<?php if ($tbl_support->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $tbl_support_grid->RowCnt ?>_tbl_support_assistiveID" class="control-group tbl_support_assistiveID">
<select data-field="x_assistiveID" id="x<?php echo $tbl_support_grid->RowIndex ?>_assistiveID" name="x<?php echo $tbl_support_grid->RowIndex ?>_assistiveID"<?php echo $tbl_support->assistiveID->EditAttributes() ?>>
<?php
if (is_array($tbl_support->assistiveID->EditValue)) {
	$arwrk = $tbl_support->assistiveID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->assistiveID->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_support->assistiveID->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `assistiveID`, `Device` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_assistive`";
 $sWhereWrk = "";

 // Call Lookup selecting
 $tbl_support->Lookup_Selecting($tbl_support->assistiveID, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
 $sSqlWrk .= " ORDER BY `assistiveID` ASC";
?>
<input type="hidden" name="s_x<?php echo $tbl_support_grid->RowIndex ?>_assistiveID" id="s_x<?php echo $tbl_support_grid->RowIndex ?>_assistiveID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`assistiveID` = {filter_value}"); ?>&t0=3">
</span>
<input type="hidden" data-field="x_assistiveID" name="o<?php echo $tbl_support_grid->RowIndex ?>_assistiveID" id="o<?php echo $tbl_support_grid->RowIndex ?>_assistiveID" value="<?php echo ew_HtmlEncode($tbl_support->assistiveID->OldValue) ?>">
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_support_grid->RowCnt ?>_tbl_support_assistiveID" class="control-group tbl_support_assistiveID">
<select data-field="x_assistiveID" id="x<?php echo $tbl_support_grid->RowIndex ?>_assistiveID" name="x<?php echo $tbl_support_grid->RowIndex ?>_assistiveID"<?php echo $tbl_support->assistiveID->EditAttributes() ?>>
<?php
if (is_array($tbl_support->assistiveID->EditValue)) {
	$arwrk = $tbl_support->assistiveID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->assistiveID->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_support->assistiveID->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `assistiveID`, `Device` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_assistive`";
 $sWhereWrk = "";

 // Call Lookup selecting
 $tbl_support->Lookup_Selecting($tbl_support->assistiveID, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
 $sSqlWrk .= " ORDER BY `assistiveID` ASC";
?>
<input type="hidden" name="s_x<?php echo $tbl_support_grid->RowIndex ?>_assistiveID" id="s_x<?php echo $tbl_support_grid->RowIndex ?>_assistiveID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`assistiveID` = {filter_value}"); ?>&t0=3">
</span>
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_support->assistiveID->ViewAttributes() ?>>
<?php echo $tbl_support->assistiveID->ListViewValue() ?></span>
<input type="hidden" data-field="x_assistiveID" name="x<?php echo $tbl_support_grid->RowIndex ?>_assistiveID" id="x<?php echo $tbl_support_grid->RowIndex ?>_assistiveID" value="<?php echo ew_HtmlEncode($tbl_support->assistiveID->FormValue) ?>">
<input type="hidden" data-field="x_assistiveID" name="o<?php echo $tbl_support_grid->RowIndex ?>_assistiveID" id="o<?php echo $tbl_support_grid->RowIndex ?>_assistiveID" value="<?php echo ew_HtmlEncode($tbl_support->assistiveID->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_support_grid->PageObjName . "_row_" . $tbl_support_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_support->preEx_illness->Visible) { // preEx_illness ?>
		<td<?php echo $tbl_support->preEx_illness->CellAttributes() ?>>
<?php if ($tbl_support->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $tbl_support_grid->RowCnt ?>_tbl_support_preEx_illness" class="control-group tbl_support_preEx_illness">
<select data-field="x_preEx_illness" id="x<?php echo $tbl_support_grid->RowIndex ?>_preEx_illness" name="x<?php echo $tbl_support_grid->RowIndex ?>_preEx_illness"<?php echo $tbl_support->preEx_illness->EditAttributes() ?>>
<?php
if (is_array($tbl_support->preEx_illness->EditValue)) {
	$arwrk = $tbl_support->preEx_illness->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->preEx_illness->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_support->preEx_illness->OldValue = "";
?>
</select>
</span>
<input type="hidden" data-field="x_preEx_illness" name="o<?php echo $tbl_support_grid->RowIndex ?>_preEx_illness" id="o<?php echo $tbl_support_grid->RowIndex ?>_preEx_illness" value="<?php echo ew_HtmlEncode($tbl_support->preEx_illness->OldValue) ?>">
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_support_grid->RowCnt ?>_tbl_support_preEx_illness" class="control-group tbl_support_preEx_illness">
<select data-field="x_preEx_illness" id="x<?php echo $tbl_support_grid->RowIndex ?>_preEx_illness" name="x<?php echo $tbl_support_grid->RowIndex ?>_preEx_illness"<?php echo $tbl_support->preEx_illness->EditAttributes() ?>>
<?php
if (is_array($tbl_support->preEx_illness->EditValue)) {
	$arwrk = $tbl_support->preEx_illness->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->preEx_illness->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_support->preEx_illness->OldValue = "";
?>
</select>
</span>
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_support->preEx_illness->ViewAttributes() ?>>
<?php echo $tbl_support->preEx_illness->ListViewValue() ?></span>
<input type="hidden" data-field="x_preEx_illness" name="x<?php echo $tbl_support_grid->RowIndex ?>_preEx_illness" id="x<?php echo $tbl_support_grid->RowIndex ?>_preEx_illness" value="<?php echo ew_HtmlEncode($tbl_support->preEx_illness->FormValue) ?>">
<input type="hidden" data-field="x_preEx_illness" name="o<?php echo $tbl_support_grid->RowIndex ?>_preEx_illness" id="o<?php echo $tbl_support_grid->RowIndex ?>_preEx_illness" value="<?php echo ew_HtmlEncode($tbl_support->preEx_illness->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_support_grid->PageObjName . "_row_" . $tbl_support_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_support->illnessID->Visible) { // illnessID ?>
		<td<?php echo $tbl_support->illnessID->CellAttributes() ?>>
<?php if ($tbl_support->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $tbl_support_grid->RowCnt ?>_tbl_support_illnessID" class="control-group tbl_support_illnessID">
<select data-field="x_illnessID" id="x<?php echo $tbl_support_grid->RowIndex ?>_illnessID" name="x<?php echo $tbl_support_grid->RowIndex ?>_illnessID"<?php echo $tbl_support->illnessID->EditAttributes() ?>>
<?php
if (is_array($tbl_support->illnessID->EditValue)) {
	$arwrk = $tbl_support->illnessID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->illnessID->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_support->illnessID->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `illnessID`, `description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_illness`";
 $sWhereWrk = "";

 // Call Lookup selecting
 $tbl_support->Lookup_Selecting($tbl_support->illnessID, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
 $sSqlWrk .= " ORDER BY `illnessID` ASC";
?>
<input type="hidden" name="s_x<?php echo $tbl_support_grid->RowIndex ?>_illnessID" id="s_x<?php echo $tbl_support_grid->RowIndex ?>_illnessID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`illnessID` = {filter_value}"); ?>&t0=3">
</span>
<input type="hidden" data-field="x_illnessID" name="o<?php echo $tbl_support_grid->RowIndex ?>_illnessID" id="o<?php echo $tbl_support_grid->RowIndex ?>_illnessID" value="<?php echo ew_HtmlEncode($tbl_support->illnessID->OldValue) ?>">
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_support_grid->RowCnt ?>_tbl_support_illnessID" class="control-group tbl_support_illnessID">
<select data-field="x_illnessID" id="x<?php echo $tbl_support_grid->RowIndex ?>_illnessID" name="x<?php echo $tbl_support_grid->RowIndex ?>_illnessID"<?php echo $tbl_support->illnessID->EditAttributes() ?>>
<?php
if (is_array($tbl_support->illnessID->EditValue)) {
	$arwrk = $tbl_support->illnessID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->illnessID->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_support->illnessID->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `illnessID`, `description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_illness`";
 $sWhereWrk = "";

 // Call Lookup selecting
 $tbl_support->Lookup_Selecting($tbl_support->illnessID, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
 $sSqlWrk .= " ORDER BY `illnessID` ASC";
?>
<input type="hidden" name="s_x<?php echo $tbl_support_grid->RowIndex ?>_illnessID" id="s_x<?php echo $tbl_support_grid->RowIndex ?>_illnessID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`illnessID` = {filter_value}"); ?>&t0=3">
</span>
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_support->illnessID->ViewAttributes() ?>>
<?php echo $tbl_support->illnessID->ListViewValue() ?></span>
<input type="hidden" data-field="x_illnessID" name="x<?php echo $tbl_support_grid->RowIndex ?>_illnessID" id="x<?php echo $tbl_support_grid->RowIndex ?>_illnessID" value="<?php echo ew_HtmlEncode($tbl_support->illnessID->FormValue) ?>">
<input type="hidden" data-field="x_illnessID" name="o<?php echo $tbl_support_grid->RowIndex ?>_illnessID" id="o<?php echo $tbl_support_grid->RowIndex ?>_illnessID" value="<?php echo ew_HtmlEncode($tbl_support->illnessID->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_support_grid->PageObjName . "_row_" . $tbl_support_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_support->physconditionID->Visible) { // physconditionID ?>
		<td<?php echo $tbl_support->physconditionID->CellAttributes() ?>>
<?php if ($tbl_support->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $tbl_support_grid->RowCnt ?>_tbl_support_physconditionID" class="control-group tbl_support_physconditionID">
<select data-field="x_physconditionID" id="x<?php echo $tbl_support_grid->RowIndex ?>_physconditionID" name="x<?php echo $tbl_support_grid->RowIndex ?>_physconditionID"<?php echo $tbl_support->physconditionID->EditAttributes() ?>>
<?php
if (is_array($tbl_support->physconditionID->EditValue)) {
	$arwrk = $tbl_support->physconditionID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->physconditionID->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_support->physconditionID->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `physconditionID`, `physconditionName` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_physical_condition`";
 $sWhereWrk = "";

 // Call Lookup selecting
 $tbl_support->Lookup_Selecting($tbl_support->physconditionID, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
 $sSqlWrk .= " ORDER BY `physconditionID` ASC";
?>
<input type="hidden" name="s_x<?php echo $tbl_support_grid->RowIndex ?>_physconditionID" id="s_x<?php echo $tbl_support_grid->RowIndex ?>_physconditionID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`physconditionID` = {filter_value}"); ?>&t0=3">
</span>
<input type="hidden" data-field="x_physconditionID" name="o<?php echo $tbl_support_grid->RowIndex ?>_physconditionID" id="o<?php echo $tbl_support_grid->RowIndex ?>_physconditionID" value="<?php echo ew_HtmlEncode($tbl_support->physconditionID->OldValue) ?>">
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_support_grid->RowCnt ?>_tbl_support_physconditionID" class="control-group tbl_support_physconditionID">
<select data-field="x_physconditionID" id="x<?php echo $tbl_support_grid->RowIndex ?>_physconditionID" name="x<?php echo $tbl_support_grid->RowIndex ?>_physconditionID"<?php echo $tbl_support->physconditionID->EditAttributes() ?>>
<?php
if (is_array($tbl_support->physconditionID->EditValue)) {
	$arwrk = $tbl_support->physconditionID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->physconditionID->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_support->physconditionID->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `physconditionID`, `physconditionName` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_physical_condition`";
 $sWhereWrk = "";

 // Call Lookup selecting
 $tbl_support->Lookup_Selecting($tbl_support->physconditionID, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
 $sSqlWrk .= " ORDER BY `physconditionID` ASC";
?>
<input type="hidden" name="s_x<?php echo $tbl_support_grid->RowIndex ?>_physconditionID" id="s_x<?php echo $tbl_support_grid->RowIndex ?>_physconditionID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`physconditionID` = {filter_value}"); ?>&t0=3">
</span>
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_support->physconditionID->ViewAttributes() ?>>
<?php echo $tbl_support->physconditionID->ListViewValue() ?></span>
<input type="hidden" data-field="x_physconditionID" name="x<?php echo $tbl_support_grid->RowIndex ?>_physconditionID" id="x<?php echo $tbl_support_grid->RowIndex ?>_physconditionID" value="<?php echo ew_HtmlEncode($tbl_support->physconditionID->FormValue) ?>">
<input type="hidden" data-field="x_physconditionID" name="o<?php echo $tbl_support_grid->RowIndex ?>_physconditionID" id="o<?php echo $tbl_support_grid->RowIndex ?>_physconditionID" value="<?php echo ew_HtmlEncode($tbl_support->physconditionID->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_support_grid->PageObjName . "_row_" . $tbl_support_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_support->CreatedBy->Visible) { // CreatedBy ?>
		<td<?php echo $tbl_support->CreatedBy->CellAttributes() ?>>
<?php if ($tbl_support->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<input type="hidden" data-field="x_CreatedBy" name="o<?php echo $tbl_support_grid->RowIndex ?>_CreatedBy" id="o<?php echo $tbl_support_grid->RowIndex ?>_CreatedBy" value="<?php echo ew_HtmlEncode($tbl_support->CreatedBy->OldValue) ?>">
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_support->CreatedBy->ViewAttributes() ?>>
<?php echo $tbl_support->CreatedBy->ListViewValue() ?></span>
<input type="hidden" data-field="x_CreatedBy" name="x<?php echo $tbl_support_grid->RowIndex ?>_CreatedBy" id="x<?php echo $tbl_support_grid->RowIndex ?>_CreatedBy" value="<?php echo ew_HtmlEncode($tbl_support->CreatedBy->FormValue) ?>">
<input type="hidden" data-field="x_CreatedBy" name="o<?php echo $tbl_support_grid->RowIndex ?>_CreatedBy" id="o<?php echo $tbl_support_grid->RowIndex ?>_CreatedBy" value="<?php echo ew_HtmlEncode($tbl_support->CreatedBy->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_support_grid->PageObjName . "_row_" . $tbl_support_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_support->CreatedDate->Visible) { // CreatedDate ?>
		<td<?php echo $tbl_support->CreatedDate->CellAttributes() ?>>
<?php if ($tbl_support->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<input type="hidden" data-field="x_CreatedDate" name="o<?php echo $tbl_support_grid->RowIndex ?>_CreatedDate" id="o<?php echo $tbl_support_grid->RowIndex ?>_CreatedDate" value="<?php echo ew_HtmlEncode($tbl_support->CreatedDate->OldValue) ?>">
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_support->CreatedDate->ViewAttributes() ?>>
<?php echo $tbl_support->CreatedDate->ListViewValue() ?></span>
<input type="hidden" data-field="x_CreatedDate" name="x<?php echo $tbl_support_grid->RowIndex ?>_CreatedDate" id="x<?php echo $tbl_support_grid->RowIndex ?>_CreatedDate" value="<?php echo ew_HtmlEncode($tbl_support->CreatedDate->FormValue) ?>">
<input type="hidden" data-field="x_CreatedDate" name="o<?php echo $tbl_support_grid->RowIndex ?>_CreatedDate" id="o<?php echo $tbl_support_grid->RowIndex ?>_CreatedDate" value="<?php echo ew_HtmlEncode($tbl_support->CreatedDate->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_support_grid->PageObjName . "_row_" . $tbl_support_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_support->UpdatedBy->Visible) { // UpdatedBy ?>
		<td<?php echo $tbl_support->UpdatedBy->CellAttributes() ?>>
<?php if ($tbl_support->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<input type="hidden" data-field="x_UpdatedBy" name="o<?php echo $tbl_support_grid->RowIndex ?>_UpdatedBy" id="o<?php echo $tbl_support_grid->RowIndex ?>_UpdatedBy" value="<?php echo ew_HtmlEncode($tbl_support->UpdatedBy->OldValue) ?>">
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_support->UpdatedBy->ViewAttributes() ?>>
<?php echo $tbl_support->UpdatedBy->ListViewValue() ?></span>
<input type="hidden" data-field="x_UpdatedBy" name="x<?php echo $tbl_support_grid->RowIndex ?>_UpdatedBy" id="x<?php echo $tbl_support_grid->RowIndex ?>_UpdatedBy" value="<?php echo ew_HtmlEncode($tbl_support->UpdatedBy->FormValue) ?>">
<input type="hidden" data-field="x_UpdatedBy" name="o<?php echo $tbl_support_grid->RowIndex ?>_UpdatedBy" id="o<?php echo $tbl_support_grid->RowIndex ?>_UpdatedBy" value="<?php echo ew_HtmlEncode($tbl_support->UpdatedBy->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_support_grid->PageObjName . "_row_" . $tbl_support_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_support->UpdatedDate->Visible) { // UpdatedDate ?>
		<td<?php echo $tbl_support->UpdatedDate->CellAttributes() ?>>
<?php if ($tbl_support->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<input type="hidden" data-field="x_UpdatedDate" name="o<?php echo $tbl_support_grid->RowIndex ?>_UpdatedDate" id="o<?php echo $tbl_support_grid->RowIndex ?>_UpdatedDate" value="<?php echo ew_HtmlEncode($tbl_support->UpdatedDate->OldValue) ?>">
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<?php } ?>
<?php if ($tbl_support->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_support->UpdatedDate->ViewAttributes() ?>>
<?php echo $tbl_support->UpdatedDate->ListViewValue() ?></span>
<input type="hidden" data-field="x_UpdatedDate" name="x<?php echo $tbl_support_grid->RowIndex ?>_UpdatedDate" id="x<?php echo $tbl_support_grid->RowIndex ?>_UpdatedDate" value="<?php echo ew_HtmlEncode($tbl_support->UpdatedDate->FormValue) ?>">
<input type="hidden" data-field="x_UpdatedDate" name="o<?php echo $tbl_support_grid->RowIndex ?>_UpdatedDate" id="o<?php echo $tbl_support_grid->RowIndex ?>_UpdatedDate" value="<?php echo ew_HtmlEncode($tbl_support->UpdatedDate->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_support_grid->PageObjName . "_row_" . $tbl_support_grid->RowCnt ?>"></a></td>
	<?php } ?>
<?php

// Render list options (body, right)
$tbl_support_grid->ListOptions->Render("body", "right", $tbl_support_grid->RowCnt);
?>
	</tr>
<?php if ($tbl_support->RowType == EW_ROWTYPE_ADD || $tbl_support->RowType == EW_ROWTYPE_EDIT) { ?>
<script type="text/javascript">
ftbl_supportgrid.UpdateOpts(<?php echo $tbl_support_grid->RowIndex ?>);
</script>
<?php } ?>
<?php
	}
	} // End delete row checking
	if ($tbl_support->CurrentAction <> "gridadd" || $tbl_support->CurrentMode == "copy")
		if (!$tbl_support_grid->Recordset->EOF) $tbl_support_grid->Recordset->MoveNext();
}
?>
<?php
	if ($tbl_support->CurrentMode == "add" || $tbl_support->CurrentMode == "copy" || $tbl_support->CurrentMode == "edit") {
		$tbl_support_grid->RowIndex = '$rowindex$';
		$tbl_support_grid->LoadDefaultValues();

		// Set row properties
		$tbl_support->ResetAttrs();
		$tbl_support->RowAttrs = array_merge($tbl_support->RowAttrs, array('data-rowindex'=>$tbl_support_grid->RowIndex, 'id'=>'r0_tbl_support', 'data-rowtype'=>EW_ROWTYPE_ADD));
		ew_AppendClass($tbl_support->RowAttrs["class"], "ewTemplate");
		$tbl_support->RowType = EW_ROWTYPE_ADD;

		// Render row
		$tbl_support_grid->RenderRow();

		// Render list options
		$tbl_support_grid->RenderListOptions();
		$tbl_support_grid->StartRowCnt = 0;
?>
	<tr<?php echo $tbl_support->RowAttributes() ?>>
<?php

// Render list options (body, left)
$tbl_support_grid->ListOptions->Render("body", "left", $tbl_support_grid->RowIndex);
?>
	<?php if ($tbl_support->supportID->Visible) { // supportID ?>
		<td>
<?php if ($tbl_support->CurrentAction <> "F") { ?>
<?php } else { ?>
<span id="el$rowindex$_tbl_support_supportID" class="control-group tbl_support_supportID">
<span<?php echo $tbl_support->supportID->ViewAttributes() ?>>
<?php echo $tbl_support->supportID->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_supportID" name="x<?php echo $tbl_support_grid->RowIndex ?>_supportID" id="x<?php echo $tbl_support_grid->RowIndex ?>_supportID" value="<?php echo ew_HtmlEncode($tbl_support->supportID->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_supportID" name="o<?php echo $tbl_support_grid->RowIndex ?>_supportID" id="o<?php echo $tbl_support_grid->RowIndex ?>_supportID" value="<?php echo ew_HtmlEncode($tbl_support->supportID->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_support->PensionerID->Visible) { // PensionerID ?>
		<td>
<?php if ($tbl_support->CurrentAction <> "F") { ?>
<?php if ($tbl_support->PensionerID->getSessionValue() <> "") { ?>
<span<?php echo $tbl_support->PensionerID->ViewAttributes() ?>>
<?php echo $tbl_support->PensionerID->ListViewValue() ?></span>
<input type="hidden" id="x<?php echo $tbl_support_grid->RowIndex ?>_PensionerID" name="x<?php echo $tbl_support_grid->RowIndex ?>_PensionerID" value="<?php echo ew_HtmlEncode($tbl_support->PensionerID->CurrentValue) ?>">
<?php } else { ?>
<input type="text" data-field="x_PensionerID" name="x<?php echo $tbl_support_grid->RowIndex ?>_PensionerID" id="x<?php echo $tbl_support_grid->RowIndex ?>_PensionerID" size="30" maxlength="25" placeholder="<?php echo $tbl_support->PensionerID->PlaceHolder ?>" value="<?php echo $tbl_support->PensionerID->EditValue ?>"<?php echo $tbl_support->PensionerID->EditAttributes() ?>>
<?php } ?>
<?php } else { ?>
<span<?php echo $tbl_support->PensionerID->ViewAttributes() ?>>
<?php echo $tbl_support->PensionerID->ViewValue ?></span>
<input type="hidden" data-field="x_PensionerID" name="x<?php echo $tbl_support_grid->RowIndex ?>_PensionerID" id="x<?php echo $tbl_support_grid->RowIndex ?>_PensionerID" value="<?php echo ew_HtmlEncode($tbl_support->PensionerID->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_PensionerID" name="o<?php echo $tbl_support_grid->RowIndex ?>_PensionerID" id="o<?php echo $tbl_support_grid->RowIndex ?>_PensionerID" value="<?php echo ew_HtmlEncode($tbl_support->PensionerID->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_support->family_support->Visible) { // family_support ?>
		<td>
<?php if ($tbl_support->CurrentAction <> "F") { ?>
<span id="el$rowindex$_tbl_support_family_support" class="control-group tbl_support_family_support">
<select data-field="x_family_support" id="x<?php echo $tbl_support_grid->RowIndex ?>_family_support" name="x<?php echo $tbl_support_grid->RowIndex ?>_family_support"<?php echo $tbl_support->family_support->EditAttributes() ?>>
<?php
if (is_array($tbl_support->family_support->EditValue)) {
	$arwrk = $tbl_support->family_support->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->family_support->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_support->family_support->OldValue = "";
?>
</select>
</span>
<?php } else { ?>
<span id="el$rowindex$_tbl_support_family_support" class="control-group tbl_support_family_support">
<span<?php echo $tbl_support->family_support->ViewAttributes() ?>>
<?php echo $tbl_support->family_support->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_family_support" name="x<?php echo $tbl_support_grid->RowIndex ?>_family_support" id="x<?php echo $tbl_support_grid->RowIndex ?>_family_support" value="<?php echo ew_HtmlEncode($tbl_support->family_support->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_family_support" name="o<?php echo $tbl_support_grid->RowIndex ?>_family_support" id="o<?php echo $tbl_support_grid->RowIndex ?>_family_support" value="<?php echo ew_HtmlEncode($tbl_support->family_support->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_support->KindSupID->Visible) { // KindSupID ?>
		<td>
<?php if ($tbl_support->CurrentAction <> "F") { ?>
<span id="el$rowindex$_tbl_support_KindSupID" class="control-group tbl_support_KindSupID">
<select data-field="x_KindSupID" id="x<?php echo $tbl_support_grid->RowIndex ?>_KindSupID" name="x<?php echo $tbl_support_grid->RowIndex ?>_KindSupID"<?php echo $tbl_support->KindSupID->EditAttributes() ?>>
<?php
if (is_array($tbl_support->KindSupID->EditValue)) {
	$arwrk = $tbl_support->KindSupID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->KindSupID->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_support->KindSupID->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `SupportID`, `SupportKind` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_support`";
 $sWhereWrk = "";

 // Call Lookup selecting
 $tbl_support->Lookup_Selecting($tbl_support->KindSupID, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
 $sSqlWrk .= " ORDER BY `SupportID` ASC";
?>
<input type="hidden" name="s_x<?php echo $tbl_support_grid->RowIndex ?>_KindSupID" id="s_x<?php echo $tbl_support_grid->RowIndex ?>_KindSupID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`SupportID` = {filter_value}"); ?>&t0=3">
</span>
<?php } else { ?>
<span id="el$rowindex$_tbl_support_KindSupID" class="control-group tbl_support_KindSupID">
<span<?php echo $tbl_support->KindSupID->ViewAttributes() ?>>
<?php echo $tbl_support->KindSupID->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_KindSupID" name="x<?php echo $tbl_support_grid->RowIndex ?>_KindSupID" id="x<?php echo $tbl_support_grid->RowIndex ?>_KindSupID" value="<?php echo ew_HtmlEncode($tbl_support->KindSupID->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_KindSupID" name="o<?php echo $tbl_support_grid->RowIndex ?>_KindSupID" id="o<?php echo $tbl_support_grid->RowIndex ?>_KindSupID" value="<?php echo ew_HtmlEncode($tbl_support->KindSupID->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_support->meals->Visible) { // meals ?>
		<td>
<?php if ($tbl_support->CurrentAction <> "F") { ?>
<span id="el$rowindex$_tbl_support_meals" class="control-group tbl_support_meals">
<input type="text" data-field="x_meals" name="x<?php echo $tbl_support_grid->RowIndex ?>_meals" id="x<?php echo $tbl_support_grid->RowIndex ?>_meals" size="30" placeholder="<?php echo $tbl_support->meals->PlaceHolder ?>" value="<?php echo $tbl_support->meals->EditValue ?>"<?php echo $tbl_support->meals->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el$rowindex$_tbl_support_meals" class="control-group tbl_support_meals">
<span<?php echo $tbl_support->meals->ViewAttributes() ?>>
<?php echo $tbl_support->meals->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_meals" name="x<?php echo $tbl_support_grid->RowIndex ?>_meals" id="x<?php echo $tbl_support_grid->RowIndex ?>_meals" value="<?php echo ew_HtmlEncode($tbl_support->meals->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_meals" name="o<?php echo $tbl_support_grid->RowIndex ?>_meals" id="o<?php echo $tbl_support_grid->RowIndex ?>_meals" value="<?php echo ew_HtmlEncode($tbl_support->meals->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_support->disability->Visible) { // disability ?>
		<td>
<?php if ($tbl_support->CurrentAction <> "F") { ?>
<span id="el$rowindex$_tbl_support_disability" class="control-group tbl_support_disability">
<select data-field="x_disability" id="x<?php echo $tbl_support_grid->RowIndex ?>_disability" name="x<?php echo $tbl_support_grid->RowIndex ?>_disability"<?php echo $tbl_support->disability->EditAttributes() ?>>
<?php
if (is_array($tbl_support->disability->EditValue)) {
	$arwrk = $tbl_support->disability->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->disability->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_support->disability->OldValue = "";
?>
</select>
</span>
<?php } else { ?>
<span id="el$rowindex$_tbl_support_disability" class="control-group tbl_support_disability">
<span<?php echo $tbl_support->disability->ViewAttributes() ?>>
<?php echo $tbl_support->disability->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_disability" name="x<?php echo $tbl_support_grid->RowIndex ?>_disability" id="x<?php echo $tbl_support_grid->RowIndex ?>_disability" value="<?php echo ew_HtmlEncode($tbl_support->disability->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_disability" name="o<?php echo $tbl_support_grid->RowIndex ?>_disability" id="o<?php echo $tbl_support_grid->RowIndex ?>_disability" value="<?php echo ew_HtmlEncode($tbl_support->disability->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_support->disabilityID->Visible) { // disabilityID ?>
		<td>
<?php if ($tbl_support->CurrentAction <> "F") { ?>
<span id="el$rowindex$_tbl_support_disabilityID" class="control-group tbl_support_disabilityID">
<select data-field="x_disabilityID" id="x<?php echo $tbl_support_grid->RowIndex ?>_disabilityID" name="x<?php echo $tbl_support_grid->RowIndex ?>_disabilityID"<?php echo $tbl_support->disabilityID->EditAttributes() ?>>
<?php
if (is_array($tbl_support->disabilityID->EditValue)) {
	$arwrk = $tbl_support->disabilityID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->disabilityID->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_support->disabilityID->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `disabilityID`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_disability`";
 $sWhereWrk = "";

 // Call Lookup selecting
 $tbl_support->Lookup_Selecting($tbl_support->disabilityID, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
 $sSqlWrk .= " ORDER BY `disabilityID` ASC";
?>
<input type="hidden" name="s_x<?php echo $tbl_support_grid->RowIndex ?>_disabilityID" id="s_x<?php echo $tbl_support_grid->RowIndex ?>_disabilityID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`disabilityID` = {filter_value}"); ?>&t0=3">
</span>
<?php } else { ?>
<span id="el$rowindex$_tbl_support_disabilityID" class="control-group tbl_support_disabilityID">
<span<?php echo $tbl_support->disabilityID->ViewAttributes() ?>>
<?php echo $tbl_support->disabilityID->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_disabilityID" name="x<?php echo $tbl_support_grid->RowIndex ?>_disabilityID" id="x<?php echo $tbl_support_grid->RowIndex ?>_disabilityID" value="<?php echo ew_HtmlEncode($tbl_support->disabilityID->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_disabilityID" name="o<?php echo $tbl_support_grid->RowIndex ?>_disabilityID" id="o<?php echo $tbl_support_grid->RowIndex ?>_disabilityID" value="<?php echo ew_HtmlEncode($tbl_support->disabilityID->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_support->immobile->Visible) { // immobile ?>
		<td>
<?php if ($tbl_support->CurrentAction <> "F") { ?>
<span id="el$rowindex$_tbl_support_immobile" class="control-group tbl_support_immobile">
<select data-field="x_immobile" id="x<?php echo $tbl_support_grid->RowIndex ?>_immobile" name="x<?php echo $tbl_support_grid->RowIndex ?>_immobile"<?php echo $tbl_support->immobile->EditAttributes() ?>>
<?php
if (is_array($tbl_support->immobile->EditValue)) {
	$arwrk = $tbl_support->immobile->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->immobile->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_support->immobile->OldValue = "";
?>
</select>
</span>
<?php } else { ?>
<span id="el$rowindex$_tbl_support_immobile" class="control-group tbl_support_immobile">
<span<?php echo $tbl_support->immobile->ViewAttributes() ?>>
<?php echo $tbl_support->immobile->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_immobile" name="x<?php echo $tbl_support_grid->RowIndex ?>_immobile" id="x<?php echo $tbl_support_grid->RowIndex ?>_immobile" value="<?php echo ew_HtmlEncode($tbl_support->immobile->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_immobile" name="o<?php echo $tbl_support_grid->RowIndex ?>_immobile" id="o<?php echo $tbl_support_grid->RowIndex ?>_immobile" value="<?php echo ew_HtmlEncode($tbl_support->immobile->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_support->assistiveID->Visible) { // assistiveID ?>
		<td>
<?php if ($tbl_support->CurrentAction <> "F") { ?>
<span id="el$rowindex$_tbl_support_assistiveID" class="control-group tbl_support_assistiveID">
<select data-field="x_assistiveID" id="x<?php echo $tbl_support_grid->RowIndex ?>_assistiveID" name="x<?php echo $tbl_support_grid->RowIndex ?>_assistiveID"<?php echo $tbl_support->assistiveID->EditAttributes() ?>>
<?php
if (is_array($tbl_support->assistiveID->EditValue)) {
	$arwrk = $tbl_support->assistiveID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->assistiveID->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_support->assistiveID->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `assistiveID`, `Device` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_assistive`";
 $sWhereWrk = "";

 // Call Lookup selecting
 $tbl_support->Lookup_Selecting($tbl_support->assistiveID, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
 $sSqlWrk .= " ORDER BY `assistiveID` ASC";
?>
<input type="hidden" name="s_x<?php echo $tbl_support_grid->RowIndex ?>_assistiveID" id="s_x<?php echo $tbl_support_grid->RowIndex ?>_assistiveID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`assistiveID` = {filter_value}"); ?>&t0=3">
</span>
<?php } else { ?>
<span id="el$rowindex$_tbl_support_assistiveID" class="control-group tbl_support_assistiveID">
<span<?php echo $tbl_support->assistiveID->ViewAttributes() ?>>
<?php echo $tbl_support->assistiveID->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_assistiveID" name="x<?php echo $tbl_support_grid->RowIndex ?>_assistiveID" id="x<?php echo $tbl_support_grid->RowIndex ?>_assistiveID" value="<?php echo ew_HtmlEncode($tbl_support->assistiveID->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_assistiveID" name="o<?php echo $tbl_support_grid->RowIndex ?>_assistiveID" id="o<?php echo $tbl_support_grid->RowIndex ?>_assistiveID" value="<?php echo ew_HtmlEncode($tbl_support->assistiveID->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_support->preEx_illness->Visible) { // preEx_illness ?>
		<td>
<?php if ($tbl_support->CurrentAction <> "F") { ?>
<span id="el$rowindex$_tbl_support_preEx_illness" class="control-group tbl_support_preEx_illness">
<select data-field="x_preEx_illness" id="x<?php echo $tbl_support_grid->RowIndex ?>_preEx_illness" name="x<?php echo $tbl_support_grid->RowIndex ?>_preEx_illness"<?php echo $tbl_support->preEx_illness->EditAttributes() ?>>
<?php
if (is_array($tbl_support->preEx_illness->EditValue)) {
	$arwrk = $tbl_support->preEx_illness->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->preEx_illness->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_support->preEx_illness->OldValue = "";
?>
</select>
</span>
<?php } else { ?>
<span id="el$rowindex$_tbl_support_preEx_illness" class="control-group tbl_support_preEx_illness">
<span<?php echo $tbl_support->preEx_illness->ViewAttributes() ?>>
<?php echo $tbl_support->preEx_illness->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_preEx_illness" name="x<?php echo $tbl_support_grid->RowIndex ?>_preEx_illness" id="x<?php echo $tbl_support_grid->RowIndex ?>_preEx_illness" value="<?php echo ew_HtmlEncode($tbl_support->preEx_illness->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_preEx_illness" name="o<?php echo $tbl_support_grid->RowIndex ?>_preEx_illness" id="o<?php echo $tbl_support_grid->RowIndex ?>_preEx_illness" value="<?php echo ew_HtmlEncode($tbl_support->preEx_illness->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_support->illnessID->Visible) { // illnessID ?>
		<td>
<?php if ($tbl_support->CurrentAction <> "F") { ?>
<span id="el$rowindex$_tbl_support_illnessID" class="control-group tbl_support_illnessID">
<select data-field="x_illnessID" id="x<?php echo $tbl_support_grid->RowIndex ?>_illnessID" name="x<?php echo $tbl_support_grid->RowIndex ?>_illnessID"<?php echo $tbl_support->illnessID->EditAttributes() ?>>
<?php
if (is_array($tbl_support->illnessID->EditValue)) {
	$arwrk = $tbl_support->illnessID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->illnessID->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_support->illnessID->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `illnessID`, `description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_illness`";
 $sWhereWrk = "";

 // Call Lookup selecting
 $tbl_support->Lookup_Selecting($tbl_support->illnessID, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
 $sSqlWrk .= " ORDER BY `illnessID` ASC";
?>
<input type="hidden" name="s_x<?php echo $tbl_support_grid->RowIndex ?>_illnessID" id="s_x<?php echo $tbl_support_grid->RowIndex ?>_illnessID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`illnessID` = {filter_value}"); ?>&t0=3">
</span>
<?php } else { ?>
<span id="el$rowindex$_tbl_support_illnessID" class="control-group tbl_support_illnessID">
<span<?php echo $tbl_support->illnessID->ViewAttributes() ?>>
<?php echo $tbl_support->illnessID->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_illnessID" name="x<?php echo $tbl_support_grid->RowIndex ?>_illnessID" id="x<?php echo $tbl_support_grid->RowIndex ?>_illnessID" value="<?php echo ew_HtmlEncode($tbl_support->illnessID->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_illnessID" name="o<?php echo $tbl_support_grid->RowIndex ?>_illnessID" id="o<?php echo $tbl_support_grid->RowIndex ?>_illnessID" value="<?php echo ew_HtmlEncode($tbl_support->illnessID->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_support->physconditionID->Visible) { // physconditionID ?>
		<td>
<?php if ($tbl_support->CurrentAction <> "F") { ?>
<span id="el$rowindex$_tbl_support_physconditionID" class="control-group tbl_support_physconditionID">
<select data-field="x_physconditionID" id="x<?php echo $tbl_support_grid->RowIndex ?>_physconditionID" name="x<?php echo $tbl_support_grid->RowIndex ?>_physconditionID"<?php echo $tbl_support->physconditionID->EditAttributes() ?>>
<?php
if (is_array($tbl_support->physconditionID->EditValue)) {
	$arwrk = $tbl_support->physconditionID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->physconditionID->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
if (@$emptywrk) $tbl_support->physconditionID->OldValue = "";
?>
</select>
<?php
 $sSqlWrk = "SELECT `physconditionID`, `physconditionName` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_physical_condition`";
 $sWhereWrk = "";

 // Call Lookup selecting
 $tbl_support->Lookup_Selecting($tbl_support->physconditionID, $sWhereWrk);
 if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
 $sSqlWrk .= " ORDER BY `physconditionID` ASC";
?>
<input type="hidden" name="s_x<?php echo $tbl_support_grid->RowIndex ?>_physconditionID" id="s_x<?php echo $tbl_support_grid->RowIndex ?>_physconditionID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`physconditionID` = {filter_value}"); ?>&t0=3">
</span>
<?php } else { ?>
<span id="el$rowindex$_tbl_support_physconditionID" class="control-group tbl_support_physconditionID">
<span<?php echo $tbl_support->physconditionID->ViewAttributes() ?>>
<?php echo $tbl_support->physconditionID->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_physconditionID" name="x<?php echo $tbl_support_grid->RowIndex ?>_physconditionID" id="x<?php echo $tbl_support_grid->RowIndex ?>_physconditionID" value="<?php echo ew_HtmlEncode($tbl_support->physconditionID->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_physconditionID" name="o<?php echo $tbl_support_grid->RowIndex ?>_physconditionID" id="o<?php echo $tbl_support_grid->RowIndex ?>_physconditionID" value="<?php echo ew_HtmlEncode($tbl_support->physconditionID->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_support->CreatedBy->Visible) { // CreatedBy ?>
		<td>
<?php if ($tbl_support->CurrentAction <> "F") { ?>
<?php } else { ?>
<span id="el$rowindex$_tbl_support_CreatedBy" class="control-group tbl_support_CreatedBy">
<span<?php echo $tbl_support->CreatedBy->ViewAttributes() ?>>
<?php echo $tbl_support->CreatedBy->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_CreatedBy" name="x<?php echo $tbl_support_grid->RowIndex ?>_CreatedBy" id="x<?php echo $tbl_support_grid->RowIndex ?>_CreatedBy" value="<?php echo ew_HtmlEncode($tbl_support->CreatedBy->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_CreatedBy" name="o<?php echo $tbl_support_grid->RowIndex ?>_CreatedBy" id="o<?php echo $tbl_support_grid->RowIndex ?>_CreatedBy" value="<?php echo ew_HtmlEncode($tbl_support->CreatedBy->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_support->CreatedDate->Visible) { // CreatedDate ?>
		<td>
<?php if ($tbl_support->CurrentAction <> "F") { ?>
<?php } else { ?>
<span id="el$rowindex$_tbl_support_CreatedDate" class="control-group tbl_support_CreatedDate">
<span<?php echo $tbl_support->CreatedDate->ViewAttributes() ?>>
<?php echo $tbl_support->CreatedDate->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_CreatedDate" name="x<?php echo $tbl_support_grid->RowIndex ?>_CreatedDate" id="x<?php echo $tbl_support_grid->RowIndex ?>_CreatedDate" value="<?php echo ew_HtmlEncode($tbl_support->CreatedDate->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_CreatedDate" name="o<?php echo $tbl_support_grid->RowIndex ?>_CreatedDate" id="o<?php echo $tbl_support_grid->RowIndex ?>_CreatedDate" value="<?php echo ew_HtmlEncode($tbl_support->CreatedDate->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_support->UpdatedBy->Visible) { // UpdatedBy ?>
		<td>
<?php if ($tbl_support->CurrentAction <> "F") { ?>
<?php } else { ?>
<span id="el$rowindex$_tbl_support_UpdatedBy" class="control-group tbl_support_UpdatedBy">
<span<?php echo $tbl_support->UpdatedBy->ViewAttributes() ?>>
<?php echo $tbl_support->UpdatedBy->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_UpdatedBy" name="x<?php echo $tbl_support_grid->RowIndex ?>_UpdatedBy" id="x<?php echo $tbl_support_grid->RowIndex ?>_UpdatedBy" value="<?php echo ew_HtmlEncode($tbl_support->UpdatedBy->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_UpdatedBy" name="o<?php echo $tbl_support_grid->RowIndex ?>_UpdatedBy" id="o<?php echo $tbl_support_grid->RowIndex ?>_UpdatedBy" value="<?php echo ew_HtmlEncode($tbl_support->UpdatedBy->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_support->UpdatedDate->Visible) { // UpdatedDate ?>
		<td>
<?php if ($tbl_support->CurrentAction <> "F") { ?>
<?php } else { ?>
<span id="el$rowindex$_tbl_support_UpdatedDate" class="control-group tbl_support_UpdatedDate">
<span<?php echo $tbl_support->UpdatedDate->ViewAttributes() ?>>
<?php echo $tbl_support->UpdatedDate->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_UpdatedDate" name="x<?php echo $tbl_support_grid->RowIndex ?>_UpdatedDate" id="x<?php echo $tbl_support_grid->RowIndex ?>_UpdatedDate" value="<?php echo ew_HtmlEncode($tbl_support->UpdatedDate->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_UpdatedDate" name="o<?php echo $tbl_support_grid->RowIndex ?>_UpdatedDate" id="o<?php echo $tbl_support_grid->RowIndex ?>_UpdatedDate" value="<?php echo ew_HtmlEncode($tbl_support->UpdatedDate->OldValue) ?>">
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$tbl_support_grid->ListOptions->Render("body", "right", $tbl_support_grid->RowCnt);
?>
<script type="text/javascript">
ftbl_supportgrid.UpdateOpts(<?php echo $tbl_support_grid->RowIndex ?>);
</script>
	</tr>
<?php
}
?>
</tbody>
</table>
<?php if ($tbl_support->CurrentMode == "add" || $tbl_support->CurrentMode == "copy") { ?>
<input type="hidden" name="a_list" id="a_list" value="gridinsert">
<input type="hidden" name="<?php echo $tbl_support_grid->FormKeyCountName ?>" id="<?php echo $tbl_support_grid->FormKeyCountName ?>" value="<?php echo $tbl_support_grid->KeyCount ?>">
<?php echo $tbl_support_grid->MultiSelectKey ?>
<?php } ?>
<?php if ($tbl_support->CurrentMode == "edit") { ?>
<input type="hidden" name="a_list" id="a_list" value="gridupdate">
<input type="hidden" name="<?php echo $tbl_support_grid->FormKeyCountName ?>" id="<?php echo $tbl_support_grid->FormKeyCountName ?>" value="<?php echo $tbl_support_grid->KeyCount ?>">
<?php echo $tbl_support_grid->MultiSelectKey ?>
<?php } ?>
<?php if ($tbl_support->CurrentMode == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
<input type="hidden" name="detailpage" value="ftbl_supportgrid">
</div>
<?php

// Close recordset
if ($tbl_support_grid->Recordset)
	$tbl_support_grid->Recordset->Close();
?>
<?php if ($tbl_support_grid->ShowOtherOptions) { ?>
<div class="ewGridLowerPanel ewListOtherOptions">
<?php
	foreach ($tbl_support_grid->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
<?php } ?>
</div>
</td></tr></table>
<?php if ($tbl_support->Export == "") { ?>
<script type="text/javascript">
ftbl_supportgrid.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php } ?>
<?php
$tbl_support_grid->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php
$tbl_support_grid->Page_Terminate();
?>
