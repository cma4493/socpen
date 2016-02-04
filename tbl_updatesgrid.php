<?php include_once "tbl_userinfo.php" ?>
<?php

// Create page object
if (!isset($tbl_updates_grid)) $tbl_updates_grid = new ctbl_updates_grid();

// Page init
$tbl_updates_grid->Page_Init();

// Page main
$tbl_updates_grid->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$tbl_updates_grid->Page_Render();
?>
<?php if ($tbl_updates->Export == "") { ?>
<script type="text/javascript">

// Page object
var tbl_updates_grid = new ew_Page("tbl_updates_grid");
tbl_updates_grid.PageID = "grid"; // Page ID
var EW_PAGE_ID = tbl_updates_grid.PageID; // For backward compatibility

// Form object
var ftbl_updatesgrid = new ew_Form("ftbl_updatesgrid");
ftbl_updatesgrid.FormKeyCountName = '<?php echo $tbl_updates_grid->FormKeyCountName ?>';

// Validate form
ftbl_updatesgrid.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_status");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($tbl_updates->status->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_status");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($tbl_updates->status->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_approved");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($tbl_updates->approved->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_approved");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($tbl_updates->approved->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_dateUpdated");
			if (elm && !ew_CheckUSDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($tbl_updates->dateUpdated->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "__field");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($tbl_updates->_field->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_paymentmodeID");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($tbl_updates->paymentmodeID->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_deathDate");
			if (elm && !ew_CheckUSDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($tbl_updates->deathDate->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Createdby");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($tbl_updates->Createdby->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_CreatedDate");
			if (elm && !ew_CheckUSDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($tbl_updates->CreatedDate->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_UpdatedBy");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($tbl_updates->UpdatedBy->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_UpdatedDate");
			if (elm && !ew_CheckUSDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($tbl_updates->UpdatedDate->FldErrMsg()) ?>");

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
ftbl_updatesgrid.EmptyRow = function(infix) {
	var fobj = this.Form;
	if (ew_ValueChanged(fobj, infix, "PensionerID", false)) return false;
	if (ew_ValueChanged(fobj, infix, "status", false)) return false;
	if (ew_ValueChanged(fobj, infix, "approved", false)) return false;
	if (ew_ValueChanged(fobj, infix, "dateUpdated", false)) return false;
	if (ew_ValueChanged(fobj, infix, "_field", false)) return false;
	if (ew_ValueChanged(fobj, infix, "paymentmodeID", false)) return false;
	if (ew_ValueChanged(fobj, infix, "deathDate", false)) return false;
	if (ew_ValueChanged(fobj, infix, "Createdby", false)) return false;
	if (ew_ValueChanged(fobj, infix, "CreatedDate", false)) return false;
	if (ew_ValueChanged(fobj, infix, "UpdatedBy", false)) return false;
	if (ew_ValueChanged(fobj, infix, "UpdatedDate", false)) return false;
	return true;
}

// Form_CustomValidate event
ftbl_updatesgrid.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftbl_updatesgrid.ValidateRequired = true;
<?php } else { ?>
ftbl_updatesgrid.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<?php } ?>
<?php if ($tbl_updates->getCurrentMasterTable() == "" && $tbl_updates_grid->ExportOptions->Visible()) { ?>
<div class="ewListExportOptions"><?php $tbl_updates_grid->ExportOptions->Render("body") ?></div>
<?php } ?>
<?php
if ($tbl_updates->CurrentAction == "gridadd") {
	if ($tbl_updates->CurrentMode == "copy") {
		$bSelectLimit = EW_SELECT_LIMIT;
		if ($bSelectLimit) {
			$tbl_updates_grid->TotalRecs = $tbl_updates->SelectRecordCount();
			$tbl_updates_grid->Recordset = $tbl_updates_grid->LoadRecordset($tbl_updates_grid->StartRec-1, $tbl_updates_grid->DisplayRecs);
		} else {
			if ($tbl_updates_grid->Recordset = $tbl_updates_grid->LoadRecordset())
				$tbl_updates_grid->TotalRecs = $tbl_updates_grid->Recordset->RecordCount();
		}
		$tbl_updates_grid->StartRec = 1;
		$tbl_updates_grid->DisplayRecs = $tbl_updates_grid->TotalRecs;
	} else {
		$tbl_updates->CurrentFilter = "0=1";
		$tbl_updates_grid->StartRec = 1;
		$tbl_updates_grid->DisplayRecs = $tbl_updates->GridAddRowCount;
	}
	$tbl_updates_grid->TotalRecs = $tbl_updates_grid->DisplayRecs;
	$tbl_updates_grid->StopRec = $tbl_updates_grid->DisplayRecs;
} else {
	$bSelectLimit = EW_SELECT_LIMIT;
	if ($bSelectLimit) {
		$tbl_updates_grid->TotalRecs = $tbl_updates->SelectRecordCount();
	} else {
		if ($tbl_updates_grid->Recordset = $tbl_updates_grid->LoadRecordset())
			$tbl_updates_grid->TotalRecs = $tbl_updates_grid->Recordset->RecordCount();
	}
	$tbl_updates_grid->StartRec = 1;
	$tbl_updates_grid->DisplayRecs = $tbl_updates_grid->TotalRecs; // Display all records
	if ($bSelectLimit)
		$tbl_updates_grid->Recordset = $tbl_updates_grid->LoadRecordset($tbl_updates_grid->StartRec-1, $tbl_updates_grid->DisplayRecs);
}
$tbl_updates_grid->RenderOtherOptions();
?>
<?php $tbl_updates_grid->ShowPageHeader(); ?>
<?php
$tbl_updates_grid->ShowMessage();
?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div id="ftbl_updatesgrid" class="ewForm form-horizontal">
<?php if ($tbl_updates_grid->ShowOtherOptions) { ?>
<div class="ewGridUpperPanel ewListOtherOptions">
<?php
	foreach ($tbl_updates_grid->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
<?php } ?>
<div id="gmp_tbl_updates" class="ewGridMiddlePanel">
<table id="tbl_tbl_updatesgrid" class="ewTable ewTableSeparate">
<?php echo $tbl_updates->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$tbl_updates_grid->RenderListOptions();

// Render list options (header, left)
$tbl_updates_grid->ListOptions->Render("header", "left");
?>
<?php if ($tbl_updates->updatesID->Visible) { // updatesID ?>
	<?php if ($tbl_updates->SortUrl($tbl_updates->updatesID) == "") { ?>
		<td><div id="elh_tbl_updates_updatesID" class="tbl_updates_updatesID"><div class="ewTableHeaderCaption"><?php echo $tbl_updates->updatesID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_updates_updatesID" class="tbl_updates_updatesID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_updates->updatesID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_updates->updatesID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_updates->updatesID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_updates->PensionerID->Visible) { // PensionerID ?>
	<?php if ($tbl_updates->SortUrl($tbl_updates->PensionerID) == "") { ?>
		<td><div id="elh_tbl_updates_PensionerID" class="tbl_updates_PensionerID"><div class="ewTableHeaderCaption"><?php echo $tbl_updates->PensionerID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_updates_PensionerID" class="tbl_updates_PensionerID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_updates->PensionerID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_updates->PensionerID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_updates->PensionerID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_updates->status->Visible) { // status ?>
	<?php if ($tbl_updates->SortUrl($tbl_updates->status) == "") { ?>
		<td><div id="elh_tbl_updates_status" class="tbl_updates_status"><div class="ewTableHeaderCaption"><?php echo $tbl_updates->status->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_updates_status" class="tbl_updates_status">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_updates->status->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_updates->status->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_updates->status->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_updates->approved->Visible) { // approved ?>
	<?php if ($tbl_updates->SortUrl($tbl_updates->approved) == "") { ?>
		<td><div id="elh_tbl_updates_approved" class="tbl_updates_approved"><div class="ewTableHeaderCaption"><?php echo $tbl_updates->approved->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_updates_approved" class="tbl_updates_approved">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_updates->approved->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_updates->approved->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_updates->approved->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_updates->dateUpdated->Visible) { // dateUpdated ?>
	<?php if ($tbl_updates->SortUrl($tbl_updates->dateUpdated) == "") { ?>
		<td><div id="elh_tbl_updates_dateUpdated" class="tbl_updates_dateUpdated"><div class="ewTableHeaderCaption"><?php echo $tbl_updates->dateUpdated->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_updates_dateUpdated" class="tbl_updates_dateUpdated">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_updates->dateUpdated->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_updates->dateUpdated->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_updates->dateUpdated->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_updates->_field->Visible) { // field ?>
	<?php if ($tbl_updates->SortUrl($tbl_updates->_field) == "") { ?>
		<td><div id="elh_tbl_updates__field" class="tbl_updates__field"><div class="ewTableHeaderCaption"><?php echo $tbl_updates->_field->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_updates__field" class="tbl_updates__field">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_updates->_field->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_updates->_field->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_updates->_field->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_updates->paymentmodeID->Visible) { // paymentmodeID ?>
	<?php if ($tbl_updates->SortUrl($tbl_updates->paymentmodeID) == "") { ?>
		<td><div id="elh_tbl_updates_paymentmodeID" class="tbl_updates_paymentmodeID"><div class="ewTableHeaderCaption"><?php echo $tbl_updates->paymentmodeID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_updates_paymentmodeID" class="tbl_updates_paymentmodeID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_updates->paymentmodeID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_updates->paymentmodeID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_updates->paymentmodeID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_updates->deathDate->Visible) { // deathDate ?>
	<?php if ($tbl_updates->SortUrl($tbl_updates->deathDate) == "") { ?>
		<td><div id="elh_tbl_updates_deathDate" class="tbl_updates_deathDate"><div class="ewTableHeaderCaption"><?php echo $tbl_updates->deathDate->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_updates_deathDate" class="tbl_updates_deathDate">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_updates->deathDate->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_updates->deathDate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_updates->deathDate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_updates->Createdby->Visible) { // Createdby ?>
	<?php if ($tbl_updates->SortUrl($tbl_updates->Createdby) == "") { ?>
		<td><div id="elh_tbl_updates_Createdby" class="tbl_updates_Createdby"><div class="ewTableHeaderCaption"><?php echo $tbl_updates->Createdby->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_updates_Createdby" class="tbl_updates_Createdby">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_updates->Createdby->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_updates->Createdby->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_updates->Createdby->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_updates->CreatedDate->Visible) { // CreatedDate ?>
	<?php if ($tbl_updates->SortUrl($tbl_updates->CreatedDate) == "") { ?>
		<td><div id="elh_tbl_updates_CreatedDate" class="tbl_updates_CreatedDate"><div class="ewTableHeaderCaption"><?php echo $tbl_updates->CreatedDate->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_updates_CreatedDate" class="tbl_updates_CreatedDate">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_updates->CreatedDate->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_updates->CreatedDate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_updates->CreatedDate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_updates->UpdatedBy->Visible) { // UpdatedBy ?>
	<?php if ($tbl_updates->SortUrl($tbl_updates->UpdatedBy) == "") { ?>
		<td><div id="elh_tbl_updates_UpdatedBy" class="tbl_updates_UpdatedBy"><div class="ewTableHeaderCaption"><?php echo $tbl_updates->UpdatedBy->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_updates_UpdatedBy" class="tbl_updates_UpdatedBy">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_updates->UpdatedBy->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_updates->UpdatedBy->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_updates->UpdatedBy->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_updates->UpdatedDate->Visible) { // UpdatedDate ?>
	<?php if ($tbl_updates->SortUrl($tbl_updates->UpdatedDate) == "") { ?>
		<td><div id="elh_tbl_updates_UpdatedDate" class="tbl_updates_UpdatedDate"><div class="ewTableHeaderCaption"><?php echo $tbl_updates->UpdatedDate->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_tbl_updates_UpdatedDate" class="tbl_updates_UpdatedDate">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_updates->UpdatedDate->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_updates->UpdatedDate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_updates->UpdatedDate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$tbl_updates_grid->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
$tbl_updates_grid->StartRec = 1;
$tbl_updates_grid->StopRec = $tbl_updates_grid->TotalRecs; // Show all records

// Restore number of post back records
if ($objForm) {
	$objForm->Index = -1;
	if ($objForm->HasValue($tbl_updates_grid->FormKeyCountName) && ($tbl_updates->CurrentAction == "gridadd" || $tbl_updates->CurrentAction == "gridedit" || $tbl_updates->CurrentAction == "F")) {
		$tbl_updates_grid->KeyCount = $objForm->GetValue($tbl_updates_grid->FormKeyCountName);
		$tbl_updates_grid->StopRec = $tbl_updates_grid->StartRec + $tbl_updates_grid->KeyCount - 1;
	}
}
$tbl_updates_grid->RecCnt = $tbl_updates_grid->StartRec - 1;
if ($tbl_updates_grid->Recordset && !$tbl_updates_grid->Recordset->EOF) {
	$tbl_updates_grid->Recordset->MoveFirst();
	if (!$bSelectLimit && $tbl_updates_grid->StartRec > 1)
		$tbl_updates_grid->Recordset->Move($tbl_updates_grid->StartRec - 1);
} elseif (!$tbl_updates->AllowAddDeleteRow && $tbl_updates_grid->StopRec == 0) {
	$tbl_updates_grid->StopRec = $tbl_updates->GridAddRowCount;
}

// Initialize aggregate
$tbl_updates->RowType = EW_ROWTYPE_AGGREGATEINIT;
$tbl_updates->ResetAttrs();
$tbl_updates_grid->RenderRow();
if ($tbl_updates->CurrentAction == "gridadd")
	$tbl_updates_grid->RowIndex = 0;
if ($tbl_updates->CurrentAction == "gridedit")
	$tbl_updates_grid->RowIndex = 0;
while ($tbl_updates_grid->RecCnt < $tbl_updates_grid->StopRec) {
	$tbl_updates_grid->RecCnt++;
	if (intval($tbl_updates_grid->RecCnt) >= intval($tbl_updates_grid->StartRec)) {
		$tbl_updates_grid->RowCnt++;
		if ($tbl_updates->CurrentAction == "gridadd" || $tbl_updates->CurrentAction == "gridedit" || $tbl_updates->CurrentAction == "F") {
			$tbl_updates_grid->RowIndex++;
			$objForm->Index = $tbl_updates_grid->RowIndex;
			if ($objForm->HasValue($tbl_updates_grid->FormActionName))
				$tbl_updates_grid->RowAction = strval($objForm->GetValue($tbl_updates_grid->FormActionName));
			elseif ($tbl_updates->CurrentAction == "gridadd")
				$tbl_updates_grid->RowAction = "insert";
			else
				$tbl_updates_grid->RowAction = "";
		}

		// Set up key count
		$tbl_updates_grid->KeyCount = $tbl_updates_grid->RowIndex;

		// Init row class and style
		$tbl_updates->ResetAttrs();
		$tbl_updates->CssClass = "";
		if ($tbl_updates->CurrentAction == "gridadd") {
			if ($tbl_updates->CurrentMode == "copy") {
				$tbl_updates_grid->LoadRowValues($tbl_updates_grid->Recordset); // Load row values
				$tbl_updates_grid->SetRecordKey($tbl_updates_grid->RowOldKey, $tbl_updates_grid->Recordset); // Set old record key
			} else {
				$tbl_updates_grid->LoadDefaultValues(); // Load default values
				$tbl_updates_grid->RowOldKey = ""; // Clear old key value
			}
		} else {
			$tbl_updates_grid->LoadRowValues($tbl_updates_grid->Recordset); // Load row values
		}
		$tbl_updates->RowType = EW_ROWTYPE_VIEW; // Render view
		if ($tbl_updates->CurrentAction == "gridadd") // Grid add
			$tbl_updates->RowType = EW_ROWTYPE_ADD; // Render add
		if ($tbl_updates->CurrentAction == "gridadd" && $tbl_updates->EventCancelled && !$objForm->HasValue("k_blankrow")) // Insert failed
			$tbl_updates_grid->RestoreCurrentRowFormValues($tbl_updates_grid->RowIndex); // Restore form values
		if ($tbl_updates->CurrentAction == "gridedit") { // Grid edit
			if ($tbl_updates->EventCancelled) {
				$tbl_updates_grid->RestoreCurrentRowFormValues($tbl_updates_grid->RowIndex); // Restore form values
			}
			if ($tbl_updates_grid->RowAction == "insert")
				$tbl_updates->RowType = EW_ROWTYPE_ADD; // Render add
			else
				$tbl_updates->RowType = EW_ROWTYPE_EDIT; // Render edit
		}
		if ($tbl_updates->CurrentAction == "gridedit" && ($tbl_updates->RowType == EW_ROWTYPE_EDIT || $tbl_updates->RowType == EW_ROWTYPE_ADD) && $tbl_updates->EventCancelled) // Update failed
			$tbl_updates_grid->RestoreCurrentRowFormValues($tbl_updates_grid->RowIndex); // Restore form values
		if ($tbl_updates->RowType == EW_ROWTYPE_EDIT) // Edit row
			$tbl_updates_grid->EditRowCnt++;
		if ($tbl_updates->CurrentAction == "F") // Confirm row
			$tbl_updates_grid->RestoreCurrentRowFormValues($tbl_updates_grid->RowIndex); // Restore form values

		// Set up row id / data-rowindex
		$tbl_updates->RowAttrs = array_merge($tbl_updates->RowAttrs, array('data-rowindex'=>$tbl_updates_grid->RowCnt, 'id'=>'r' . $tbl_updates_grid->RowCnt . '_tbl_updates', 'data-rowtype'=>$tbl_updates->RowType));

		// Render row
		$tbl_updates_grid->RenderRow();

		// Render list options
		$tbl_updates_grid->RenderListOptions();

		// Skip delete row / empty row for confirm page
		if ($tbl_updates_grid->RowAction <> "delete" && $tbl_updates_grid->RowAction <> "insertdelete" && !($tbl_updates_grid->RowAction == "insert" && $tbl_updates->CurrentAction == "F" && $tbl_updates_grid->EmptyRow())) {
?>
	<tr<?php echo $tbl_updates->RowAttributes() ?>>
<?php

// Render list options (body, left)
$tbl_updates_grid->ListOptions->Render("body", "left", $tbl_updates_grid->RowCnt);
?>
	<?php if ($tbl_updates->updatesID->Visible) { // updatesID ?>
		<td<?php echo $tbl_updates->updatesID->CellAttributes() ?>>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<input type="hidden" data-field="x_updatesID" name="o<?php echo $tbl_updates_grid->RowIndex ?>_updatesID" id="o<?php echo $tbl_updates_grid->RowIndex ?>_updatesID" value="<?php echo ew_HtmlEncode($tbl_updates->updatesID->OldValue) ?>">
<?php } ?>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_updates_grid->RowCnt ?>_tbl_updates_updatesID" class="control-group tbl_updates_updatesID">
<span<?php echo $tbl_updates->updatesID->ViewAttributes() ?>>
<?php echo $tbl_updates->updatesID->EditValue ?></span>
</span>
<input type="hidden" data-field="x_updatesID" name="x<?php echo $tbl_updates_grid->RowIndex ?>_updatesID" id="x<?php echo $tbl_updates_grid->RowIndex ?>_updatesID" value="<?php echo ew_HtmlEncode($tbl_updates->updatesID->CurrentValue) ?>">
<?php } ?>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_updates->updatesID->ViewAttributes() ?>>
<?php echo $tbl_updates->updatesID->ListViewValue() ?></span>
<input type="hidden" data-field="x_updatesID" name="x<?php echo $tbl_updates_grid->RowIndex ?>_updatesID" id="x<?php echo $tbl_updates_grid->RowIndex ?>_updatesID" value="<?php echo ew_HtmlEncode($tbl_updates->updatesID->FormValue) ?>">
<input type="hidden" data-field="x_updatesID" name="o<?php echo $tbl_updates_grid->RowIndex ?>_updatesID" id="o<?php echo $tbl_updates_grid->RowIndex ?>_updatesID" value="<?php echo ew_HtmlEncode($tbl_updates->updatesID->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_updates_grid->PageObjName . "_row_" . $tbl_updates_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_updates->PensionerID->Visible) { // PensionerID ?>
		<td<?php echo $tbl_updates->PensionerID->CellAttributes() ?>>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<?php if ($tbl_updates->PensionerID->getSessionValue() <> "") { ?>
<span<?php echo $tbl_updates->PensionerID->ViewAttributes() ?>>
<?php echo $tbl_updates->PensionerID->ListViewValue() ?></span>
<input type="hidden" id="x<?php echo $tbl_updates_grid->RowIndex ?>_PensionerID" name="x<?php echo $tbl_updates_grid->RowIndex ?>_PensionerID" value="<?php echo ew_HtmlEncode($tbl_updates->PensionerID->CurrentValue) ?>">
<?php } else { ?>
<input type="text" data-field="x_PensionerID" name="x<?php echo $tbl_updates_grid->RowIndex ?>_PensionerID" id="x<?php echo $tbl_updates_grid->RowIndex ?>_PensionerID" size="30" maxlength="25" placeholder="<?php echo $tbl_updates->PensionerID->PlaceHolder ?>" value="<?php echo $tbl_updates->PensionerID->EditValue ?>"<?php echo $tbl_updates->PensionerID->EditAttributes() ?>>
<?php } ?>
<input type="hidden" data-field="x_PensionerID" name="o<?php echo $tbl_updates_grid->RowIndex ?>_PensionerID" id="o<?php echo $tbl_updates_grid->RowIndex ?>_PensionerID" value="<?php echo ew_HtmlEncode($tbl_updates->PensionerID->OldValue) ?>">
<?php } ?>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<?php if ($tbl_updates->PensionerID->getSessionValue() <> "") { ?>
<span<?php echo $tbl_updates->PensionerID->ViewAttributes() ?>>
<?php echo $tbl_updates->PensionerID->ListViewValue() ?></span>
<input type="hidden" id="x<?php echo $tbl_updates_grid->RowIndex ?>_PensionerID" name="x<?php echo $tbl_updates_grid->RowIndex ?>_PensionerID" value="<?php echo ew_HtmlEncode($tbl_updates->PensionerID->CurrentValue) ?>">
<?php } else { ?>
<input type="text" data-field="x_PensionerID" name="x<?php echo $tbl_updates_grid->RowIndex ?>_PensionerID" id="x<?php echo $tbl_updates_grid->RowIndex ?>_PensionerID" size="30" maxlength="25" placeholder="<?php echo $tbl_updates->PensionerID->PlaceHolder ?>" value="<?php echo $tbl_updates->PensionerID->EditValue ?>"<?php echo $tbl_updates->PensionerID->EditAttributes() ?>>
<?php } ?>
<?php } ?>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_updates->PensionerID->ViewAttributes() ?>>
<?php echo $tbl_updates->PensionerID->ListViewValue() ?></span>
<input type="hidden" data-field="x_PensionerID" name="x<?php echo $tbl_updates_grid->RowIndex ?>_PensionerID" id="x<?php echo $tbl_updates_grid->RowIndex ?>_PensionerID" value="<?php echo ew_HtmlEncode($tbl_updates->PensionerID->FormValue) ?>">
<input type="hidden" data-field="x_PensionerID" name="o<?php echo $tbl_updates_grid->RowIndex ?>_PensionerID" id="o<?php echo $tbl_updates_grid->RowIndex ?>_PensionerID" value="<?php echo ew_HtmlEncode($tbl_updates->PensionerID->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_updates_grid->PageObjName . "_row_" . $tbl_updates_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_updates->status->Visible) { // status ?>
		<td<?php echo $tbl_updates->status->CellAttributes() ?>>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $tbl_updates_grid->RowCnt ?>_tbl_updates_status" class="control-group tbl_updates_status">
<input type="text" data-field="x_status" name="x<?php echo $tbl_updates_grid->RowIndex ?>_status" id="x<?php echo $tbl_updates_grid->RowIndex ?>_status" size="30" placeholder="<?php echo $tbl_updates->status->PlaceHolder ?>" value="<?php echo $tbl_updates->status->EditValue ?>"<?php echo $tbl_updates->status->EditAttributes() ?>>
</span>
<input type="hidden" data-field="x_status" name="o<?php echo $tbl_updates_grid->RowIndex ?>_status" id="o<?php echo $tbl_updates_grid->RowIndex ?>_status" value="<?php echo ew_HtmlEncode($tbl_updates->status->OldValue) ?>">
<?php } ?>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_updates_grid->RowCnt ?>_tbl_updates_status" class="control-group tbl_updates_status">
<input type="text" data-field="x_status" name="x<?php echo $tbl_updates_grid->RowIndex ?>_status" id="x<?php echo $tbl_updates_grid->RowIndex ?>_status" size="30" placeholder="<?php echo $tbl_updates->status->PlaceHolder ?>" value="<?php echo $tbl_updates->status->EditValue ?>"<?php echo $tbl_updates->status->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_updates->status->ViewAttributes() ?>>
<?php echo $tbl_updates->status->ListViewValue() ?></span>
<input type="hidden" data-field="x_status" name="x<?php echo $tbl_updates_grid->RowIndex ?>_status" id="x<?php echo $tbl_updates_grid->RowIndex ?>_status" value="<?php echo ew_HtmlEncode($tbl_updates->status->FormValue) ?>">
<input type="hidden" data-field="x_status" name="o<?php echo $tbl_updates_grid->RowIndex ?>_status" id="o<?php echo $tbl_updates_grid->RowIndex ?>_status" value="<?php echo ew_HtmlEncode($tbl_updates->status->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_updates_grid->PageObjName . "_row_" . $tbl_updates_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_updates->approved->Visible) { // approved ?>
		<td<?php echo $tbl_updates->approved->CellAttributes() ?>>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $tbl_updates_grid->RowCnt ?>_tbl_updates_approved" class="control-group tbl_updates_approved">
<input type="text" data-field="x_approved" name="x<?php echo $tbl_updates_grid->RowIndex ?>_approved" id="x<?php echo $tbl_updates_grid->RowIndex ?>_approved" size="30" placeholder="<?php echo $tbl_updates->approved->PlaceHolder ?>" value="<?php echo $tbl_updates->approved->EditValue ?>"<?php echo $tbl_updates->approved->EditAttributes() ?>>
</span>
<input type="hidden" data-field="x_approved" name="o<?php echo $tbl_updates_grid->RowIndex ?>_approved" id="o<?php echo $tbl_updates_grid->RowIndex ?>_approved" value="<?php echo ew_HtmlEncode($tbl_updates->approved->OldValue) ?>">
<?php } ?>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_updates_grid->RowCnt ?>_tbl_updates_approved" class="control-group tbl_updates_approved">
<input type="text" data-field="x_approved" name="x<?php echo $tbl_updates_grid->RowIndex ?>_approved" id="x<?php echo $tbl_updates_grid->RowIndex ?>_approved" size="30" placeholder="<?php echo $tbl_updates->approved->PlaceHolder ?>" value="<?php echo $tbl_updates->approved->EditValue ?>"<?php echo $tbl_updates->approved->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_updates->approved->ViewAttributes() ?>>
<?php echo $tbl_updates->approved->ListViewValue() ?></span>
<input type="hidden" data-field="x_approved" name="x<?php echo $tbl_updates_grid->RowIndex ?>_approved" id="x<?php echo $tbl_updates_grid->RowIndex ?>_approved" value="<?php echo ew_HtmlEncode($tbl_updates->approved->FormValue) ?>">
<input type="hidden" data-field="x_approved" name="o<?php echo $tbl_updates_grid->RowIndex ?>_approved" id="o<?php echo $tbl_updates_grid->RowIndex ?>_approved" value="<?php echo ew_HtmlEncode($tbl_updates->approved->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_updates_grid->PageObjName . "_row_" . $tbl_updates_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_updates->dateUpdated->Visible) { // dateUpdated ?>
		<td<?php echo $tbl_updates->dateUpdated->CellAttributes() ?>>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $tbl_updates_grid->RowCnt ?>_tbl_updates_dateUpdated" class="control-group tbl_updates_dateUpdated">
<input type="text" data-field="x_dateUpdated" name="x<?php echo $tbl_updates_grid->RowIndex ?>_dateUpdated" id="x<?php echo $tbl_updates_grid->RowIndex ?>_dateUpdated" placeholder="<?php echo $tbl_updates->dateUpdated->PlaceHolder ?>" value="<?php echo $tbl_updates->dateUpdated->EditValue ?>"<?php echo $tbl_updates->dateUpdated->EditAttributes() ?>>
</span>
<input type="hidden" data-field="x_dateUpdated" name="o<?php echo $tbl_updates_grid->RowIndex ?>_dateUpdated" id="o<?php echo $tbl_updates_grid->RowIndex ?>_dateUpdated" value="<?php echo ew_HtmlEncode($tbl_updates->dateUpdated->OldValue) ?>">
<?php } ?>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_updates_grid->RowCnt ?>_tbl_updates_dateUpdated" class="control-group tbl_updates_dateUpdated">
<input type="text" data-field="x_dateUpdated" name="x<?php echo $tbl_updates_grid->RowIndex ?>_dateUpdated" id="x<?php echo $tbl_updates_grid->RowIndex ?>_dateUpdated" placeholder="<?php echo $tbl_updates->dateUpdated->PlaceHolder ?>" value="<?php echo $tbl_updates->dateUpdated->EditValue ?>"<?php echo $tbl_updates->dateUpdated->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_updates->dateUpdated->ViewAttributes() ?>>
<?php echo $tbl_updates->dateUpdated->ListViewValue() ?></span>
<input type="hidden" data-field="x_dateUpdated" name="x<?php echo $tbl_updates_grid->RowIndex ?>_dateUpdated" id="x<?php echo $tbl_updates_grid->RowIndex ?>_dateUpdated" value="<?php echo ew_HtmlEncode($tbl_updates->dateUpdated->FormValue) ?>">
<input type="hidden" data-field="x_dateUpdated" name="o<?php echo $tbl_updates_grid->RowIndex ?>_dateUpdated" id="o<?php echo $tbl_updates_grid->RowIndex ?>_dateUpdated" value="<?php echo ew_HtmlEncode($tbl_updates->dateUpdated->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_updates_grid->PageObjName . "_row_" . $tbl_updates_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_updates->_field->Visible) { // field ?>
		<td<?php echo $tbl_updates->_field->CellAttributes() ?>>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $tbl_updates_grid->RowCnt ?>_tbl_updates__field" class="control-group tbl_updates__field">
<input type="text" data-field="x__field" name="x<?php echo $tbl_updates_grid->RowIndex ?>__field" id="x<?php echo $tbl_updates_grid->RowIndex ?>__field" size="30" maxlength="20" placeholder="<?php echo $tbl_updates->_field->PlaceHolder ?>" value="<?php echo $tbl_updates->_field->EditValue ?>"<?php echo $tbl_updates->_field->EditAttributes() ?>>
</span>
<input type="hidden" data-field="x__field" name="o<?php echo $tbl_updates_grid->RowIndex ?>__field" id="o<?php echo $tbl_updates_grid->RowIndex ?>__field" value="<?php echo ew_HtmlEncode($tbl_updates->_field->OldValue) ?>">
<?php } ?>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_updates_grid->RowCnt ?>_tbl_updates__field" class="control-group tbl_updates__field">
<input type="text" data-field="x__field" name="x<?php echo $tbl_updates_grid->RowIndex ?>__field" id="x<?php echo $tbl_updates_grid->RowIndex ?>__field" size="30" maxlength="20" placeholder="<?php echo $tbl_updates->_field->PlaceHolder ?>" value="<?php echo $tbl_updates->_field->EditValue ?>"<?php echo $tbl_updates->_field->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_updates->_field->ViewAttributes() ?>>
<?php echo $tbl_updates->_field->ListViewValue() ?></span>
<input type="hidden" data-field="x__field" name="x<?php echo $tbl_updates_grid->RowIndex ?>__field" id="x<?php echo $tbl_updates_grid->RowIndex ?>__field" value="<?php echo ew_HtmlEncode($tbl_updates->_field->FormValue) ?>">
<input type="hidden" data-field="x__field" name="o<?php echo $tbl_updates_grid->RowIndex ?>__field" id="o<?php echo $tbl_updates_grid->RowIndex ?>__field" value="<?php echo ew_HtmlEncode($tbl_updates->_field->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_updates_grid->PageObjName . "_row_" . $tbl_updates_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_updates->paymentmodeID->Visible) { // paymentmodeID ?>
		<td<?php echo $tbl_updates->paymentmodeID->CellAttributes() ?>>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $tbl_updates_grid->RowCnt ?>_tbl_updates_paymentmodeID" class="control-group tbl_updates_paymentmodeID">
<input type="text" data-field="x_paymentmodeID" name="x<?php echo $tbl_updates_grid->RowIndex ?>_paymentmodeID" id="x<?php echo $tbl_updates_grid->RowIndex ?>_paymentmodeID" size="30" placeholder="<?php echo $tbl_updates->paymentmodeID->PlaceHolder ?>" value="<?php echo $tbl_updates->paymentmodeID->EditValue ?>"<?php echo $tbl_updates->paymentmodeID->EditAttributes() ?>>
</span>
<input type="hidden" data-field="x_paymentmodeID" name="o<?php echo $tbl_updates_grid->RowIndex ?>_paymentmodeID" id="o<?php echo $tbl_updates_grid->RowIndex ?>_paymentmodeID" value="<?php echo ew_HtmlEncode($tbl_updates->paymentmodeID->OldValue) ?>">
<?php } ?>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_updates_grid->RowCnt ?>_tbl_updates_paymentmodeID" class="control-group tbl_updates_paymentmodeID">
<input type="text" data-field="x_paymentmodeID" name="x<?php echo $tbl_updates_grid->RowIndex ?>_paymentmodeID" id="x<?php echo $tbl_updates_grid->RowIndex ?>_paymentmodeID" size="30" placeholder="<?php echo $tbl_updates->paymentmodeID->PlaceHolder ?>" value="<?php echo $tbl_updates->paymentmodeID->EditValue ?>"<?php echo $tbl_updates->paymentmodeID->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_updates->paymentmodeID->ViewAttributes() ?>>
<?php echo $tbl_updates->paymentmodeID->ListViewValue() ?></span>
<input type="hidden" data-field="x_paymentmodeID" name="x<?php echo $tbl_updates_grid->RowIndex ?>_paymentmodeID" id="x<?php echo $tbl_updates_grid->RowIndex ?>_paymentmodeID" value="<?php echo ew_HtmlEncode($tbl_updates->paymentmodeID->FormValue) ?>">
<input type="hidden" data-field="x_paymentmodeID" name="o<?php echo $tbl_updates_grid->RowIndex ?>_paymentmodeID" id="o<?php echo $tbl_updates_grid->RowIndex ?>_paymentmodeID" value="<?php echo ew_HtmlEncode($tbl_updates->paymentmodeID->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_updates_grid->PageObjName . "_row_" . $tbl_updates_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_updates->deathDate->Visible) { // deathDate ?>
		<td<?php echo $tbl_updates->deathDate->CellAttributes() ?>>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $tbl_updates_grid->RowCnt ?>_tbl_updates_deathDate" class="control-group tbl_updates_deathDate">
<input type="text" data-field="x_deathDate" name="x<?php echo $tbl_updates_grid->RowIndex ?>_deathDate" id="x<?php echo $tbl_updates_grid->RowIndex ?>_deathDate" placeholder="<?php echo $tbl_updates->deathDate->PlaceHolder ?>" value="<?php echo $tbl_updates->deathDate->EditValue ?>"<?php echo $tbl_updates->deathDate->EditAttributes() ?>>
</span>
<input type="hidden" data-field="x_deathDate" name="o<?php echo $tbl_updates_grid->RowIndex ?>_deathDate" id="o<?php echo $tbl_updates_grid->RowIndex ?>_deathDate" value="<?php echo ew_HtmlEncode($tbl_updates->deathDate->OldValue) ?>">
<?php } ?>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_updates_grid->RowCnt ?>_tbl_updates_deathDate" class="control-group tbl_updates_deathDate">
<input type="text" data-field="x_deathDate" name="x<?php echo $tbl_updates_grid->RowIndex ?>_deathDate" id="x<?php echo $tbl_updates_grid->RowIndex ?>_deathDate" placeholder="<?php echo $tbl_updates->deathDate->PlaceHolder ?>" value="<?php echo $tbl_updates->deathDate->EditValue ?>"<?php echo $tbl_updates->deathDate->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_updates->deathDate->ViewAttributes() ?>>
<?php echo $tbl_updates->deathDate->ListViewValue() ?></span>
<input type="hidden" data-field="x_deathDate" name="x<?php echo $tbl_updates_grid->RowIndex ?>_deathDate" id="x<?php echo $tbl_updates_grid->RowIndex ?>_deathDate" value="<?php echo ew_HtmlEncode($tbl_updates->deathDate->FormValue) ?>">
<input type="hidden" data-field="x_deathDate" name="o<?php echo $tbl_updates_grid->RowIndex ?>_deathDate" id="o<?php echo $tbl_updates_grid->RowIndex ?>_deathDate" value="<?php echo ew_HtmlEncode($tbl_updates->deathDate->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_updates_grid->PageObjName . "_row_" . $tbl_updates_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_updates->Createdby->Visible) { // Createdby ?>
		<td<?php echo $tbl_updates->Createdby->CellAttributes() ?>>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $tbl_updates_grid->RowCnt ?>_tbl_updates_Createdby" class="control-group tbl_updates_Createdby">
<input type="text" data-field="x_Createdby" name="x<?php echo $tbl_updates_grid->RowIndex ?>_Createdby" id="x<?php echo $tbl_updates_grid->RowIndex ?>_Createdby" size="30" placeholder="<?php echo $tbl_updates->Createdby->PlaceHolder ?>" value="<?php echo $tbl_updates->Createdby->EditValue ?>"<?php echo $tbl_updates->Createdby->EditAttributes() ?>>
</span>
<input type="hidden" data-field="x_Createdby" name="o<?php echo $tbl_updates_grid->RowIndex ?>_Createdby" id="o<?php echo $tbl_updates_grid->RowIndex ?>_Createdby" value="<?php echo ew_HtmlEncode($tbl_updates->Createdby->OldValue) ?>">
<?php } ?>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_updates_grid->RowCnt ?>_tbl_updates_Createdby" class="control-group tbl_updates_Createdby">
<input type="text" data-field="x_Createdby" name="x<?php echo $tbl_updates_grid->RowIndex ?>_Createdby" id="x<?php echo $tbl_updates_grid->RowIndex ?>_Createdby" size="30" placeholder="<?php echo $tbl_updates->Createdby->PlaceHolder ?>" value="<?php echo $tbl_updates->Createdby->EditValue ?>"<?php echo $tbl_updates->Createdby->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_updates->Createdby->ViewAttributes() ?>>
<?php echo $tbl_updates->Createdby->ListViewValue() ?></span>
<input type="hidden" data-field="x_Createdby" name="x<?php echo $tbl_updates_grid->RowIndex ?>_Createdby" id="x<?php echo $tbl_updates_grid->RowIndex ?>_Createdby" value="<?php echo ew_HtmlEncode($tbl_updates->Createdby->FormValue) ?>">
<input type="hidden" data-field="x_Createdby" name="o<?php echo $tbl_updates_grid->RowIndex ?>_Createdby" id="o<?php echo $tbl_updates_grid->RowIndex ?>_Createdby" value="<?php echo ew_HtmlEncode($tbl_updates->Createdby->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_updates_grid->PageObjName . "_row_" . $tbl_updates_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_updates->CreatedDate->Visible) { // CreatedDate ?>
		<td<?php echo $tbl_updates->CreatedDate->CellAttributes() ?>>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $tbl_updates_grid->RowCnt ?>_tbl_updates_CreatedDate" class="control-group tbl_updates_CreatedDate">
<input type="text" data-field="x_CreatedDate" name="x<?php echo $tbl_updates_grid->RowIndex ?>_CreatedDate" id="x<?php echo $tbl_updates_grid->RowIndex ?>_CreatedDate" placeholder="<?php echo $tbl_updates->CreatedDate->PlaceHolder ?>" value="<?php echo $tbl_updates->CreatedDate->EditValue ?>"<?php echo $tbl_updates->CreatedDate->EditAttributes() ?>>
</span>
<input type="hidden" data-field="x_CreatedDate" name="o<?php echo $tbl_updates_grid->RowIndex ?>_CreatedDate" id="o<?php echo $tbl_updates_grid->RowIndex ?>_CreatedDate" value="<?php echo ew_HtmlEncode($tbl_updates->CreatedDate->OldValue) ?>">
<?php } ?>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_updates_grid->RowCnt ?>_tbl_updates_CreatedDate" class="control-group tbl_updates_CreatedDate">
<input type="text" data-field="x_CreatedDate" name="x<?php echo $tbl_updates_grid->RowIndex ?>_CreatedDate" id="x<?php echo $tbl_updates_grid->RowIndex ?>_CreatedDate" placeholder="<?php echo $tbl_updates->CreatedDate->PlaceHolder ?>" value="<?php echo $tbl_updates->CreatedDate->EditValue ?>"<?php echo $tbl_updates->CreatedDate->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_updates->CreatedDate->ViewAttributes() ?>>
<?php echo $tbl_updates->CreatedDate->ListViewValue() ?></span>
<input type="hidden" data-field="x_CreatedDate" name="x<?php echo $tbl_updates_grid->RowIndex ?>_CreatedDate" id="x<?php echo $tbl_updates_grid->RowIndex ?>_CreatedDate" value="<?php echo ew_HtmlEncode($tbl_updates->CreatedDate->FormValue) ?>">
<input type="hidden" data-field="x_CreatedDate" name="o<?php echo $tbl_updates_grid->RowIndex ?>_CreatedDate" id="o<?php echo $tbl_updates_grid->RowIndex ?>_CreatedDate" value="<?php echo ew_HtmlEncode($tbl_updates->CreatedDate->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_updates_grid->PageObjName . "_row_" . $tbl_updates_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_updates->UpdatedBy->Visible) { // UpdatedBy ?>
		<td<?php echo $tbl_updates->UpdatedBy->CellAttributes() ?>>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $tbl_updates_grid->RowCnt ?>_tbl_updates_UpdatedBy" class="control-group tbl_updates_UpdatedBy">
<input type="text" data-field="x_UpdatedBy" name="x<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedBy" id="x<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedBy" size="30" placeholder="<?php echo $tbl_updates->UpdatedBy->PlaceHolder ?>" value="<?php echo $tbl_updates->UpdatedBy->EditValue ?>"<?php echo $tbl_updates->UpdatedBy->EditAttributes() ?>>
</span>
<input type="hidden" data-field="x_UpdatedBy" name="o<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedBy" id="o<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedBy" value="<?php echo ew_HtmlEncode($tbl_updates->UpdatedBy->OldValue) ?>">
<?php } ?>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_updates_grid->RowCnt ?>_tbl_updates_UpdatedBy" class="control-group tbl_updates_UpdatedBy">
<input type="text" data-field="x_UpdatedBy" name="x<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedBy" id="x<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedBy" size="30" placeholder="<?php echo $tbl_updates->UpdatedBy->PlaceHolder ?>" value="<?php echo $tbl_updates->UpdatedBy->EditValue ?>"<?php echo $tbl_updates->UpdatedBy->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_updates->UpdatedBy->ViewAttributes() ?>>
<?php echo $tbl_updates->UpdatedBy->ListViewValue() ?></span>
<input type="hidden" data-field="x_UpdatedBy" name="x<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedBy" id="x<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedBy" value="<?php echo ew_HtmlEncode($tbl_updates->UpdatedBy->FormValue) ?>">
<input type="hidden" data-field="x_UpdatedBy" name="o<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedBy" id="o<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedBy" value="<?php echo ew_HtmlEncode($tbl_updates->UpdatedBy->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_updates_grid->PageObjName . "_row_" . $tbl_updates_grid->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_updates->UpdatedDate->Visible) { // UpdatedDate ?>
		<td<?php echo $tbl_updates->UpdatedDate->CellAttributes() ?>>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $tbl_updates_grid->RowCnt ?>_tbl_updates_UpdatedDate" class="control-group tbl_updates_UpdatedDate">
<input type="text" data-field="x_UpdatedDate" name="x<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedDate" id="x<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedDate" placeholder="<?php echo $tbl_updates->UpdatedDate->PlaceHolder ?>" value="<?php echo $tbl_updates->UpdatedDate->EditValue ?>"<?php echo $tbl_updates->UpdatedDate->EditAttributes() ?>>
</span>
<input type="hidden" data-field="x_UpdatedDate" name="o<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedDate" id="o<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedDate" value="<?php echo ew_HtmlEncode($tbl_updates->UpdatedDate->OldValue) ?>">
<?php } ?>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tbl_updates_grid->RowCnt ?>_tbl_updates_UpdatedDate" class="control-group tbl_updates_UpdatedDate">
<input type="text" data-field="x_UpdatedDate" name="x<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedDate" id="x<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedDate" placeholder="<?php echo $tbl_updates->UpdatedDate->PlaceHolder ?>" value="<?php echo $tbl_updates->UpdatedDate->EditValue ?>"<?php echo $tbl_updates->UpdatedDate->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tbl_updates->UpdatedDate->ViewAttributes() ?>>
<?php echo $tbl_updates->UpdatedDate->ListViewValue() ?></span>
<input type="hidden" data-field="x_UpdatedDate" name="x<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedDate" id="x<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedDate" value="<?php echo ew_HtmlEncode($tbl_updates->UpdatedDate->FormValue) ?>">
<input type="hidden" data-field="x_UpdatedDate" name="o<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedDate" id="o<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedDate" value="<?php echo ew_HtmlEncode($tbl_updates->UpdatedDate->OldValue) ?>">
<?php } ?>
<a id="<?php echo $tbl_updates_grid->PageObjName . "_row_" . $tbl_updates_grid->RowCnt ?>"></a></td>
	<?php } ?>
<?php

// Render list options (body, right)
$tbl_updates_grid->ListOptions->Render("body", "right", $tbl_updates_grid->RowCnt);
?>
	</tr>
<?php if ($tbl_updates->RowType == EW_ROWTYPE_ADD || $tbl_updates->RowType == EW_ROWTYPE_EDIT) { ?>
<script type="text/javascript">
ftbl_updatesgrid.UpdateOpts(<?php echo $tbl_updates_grid->RowIndex ?>);
</script>
<?php } ?>
<?php
	}
	} // End delete row checking
	if ($tbl_updates->CurrentAction <> "gridadd" || $tbl_updates->CurrentMode == "copy")
		if (!$tbl_updates_grid->Recordset->EOF) $tbl_updates_grid->Recordset->MoveNext();
}
?>
<?php
	if ($tbl_updates->CurrentMode == "add" || $tbl_updates->CurrentMode == "copy" || $tbl_updates->CurrentMode == "edit") {
		$tbl_updates_grid->RowIndex = '$rowindex$';
		$tbl_updates_grid->LoadDefaultValues();

		// Set row properties
		$tbl_updates->ResetAttrs();
		$tbl_updates->RowAttrs = array_merge($tbl_updates->RowAttrs, array('data-rowindex'=>$tbl_updates_grid->RowIndex, 'id'=>'r0_tbl_updates', 'data-rowtype'=>EW_ROWTYPE_ADD));
		ew_AppendClass($tbl_updates->RowAttrs["class"], "ewTemplate");
		$tbl_updates->RowType = EW_ROWTYPE_ADD;

		// Render row
		$tbl_updates_grid->RenderRow();

		// Render list options
		$tbl_updates_grid->RenderListOptions();
		$tbl_updates_grid->StartRowCnt = 0;
?>
	<tr<?php echo $tbl_updates->RowAttributes() ?>>
<?php

// Render list options (body, left)
$tbl_updates_grid->ListOptions->Render("body", "left", $tbl_updates_grid->RowIndex);
?>
	<?php if ($tbl_updates->updatesID->Visible) { // updatesID ?>
		<td>
<?php if ($tbl_updates->CurrentAction <> "F") { ?>
<?php } else { ?>
<span id="el$rowindex$_tbl_updates_updatesID" class="control-group tbl_updates_updatesID">
<span<?php echo $tbl_updates->updatesID->ViewAttributes() ?>>
<?php echo $tbl_updates->updatesID->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_updatesID" name="x<?php echo $tbl_updates_grid->RowIndex ?>_updatesID" id="x<?php echo $tbl_updates_grid->RowIndex ?>_updatesID" value="<?php echo ew_HtmlEncode($tbl_updates->updatesID->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_updatesID" name="o<?php echo $tbl_updates_grid->RowIndex ?>_updatesID" id="o<?php echo $tbl_updates_grid->RowIndex ?>_updatesID" value="<?php echo ew_HtmlEncode($tbl_updates->updatesID->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_updates->PensionerID->Visible) { // PensionerID ?>
		<td>
<?php if ($tbl_updates->CurrentAction <> "F") { ?>
<?php if ($tbl_updates->PensionerID->getSessionValue() <> "") { ?>
<span<?php echo $tbl_updates->PensionerID->ViewAttributes() ?>>
<?php echo $tbl_updates->PensionerID->ListViewValue() ?></span>
<input type="hidden" id="x<?php echo $tbl_updates_grid->RowIndex ?>_PensionerID" name="x<?php echo $tbl_updates_grid->RowIndex ?>_PensionerID" value="<?php echo ew_HtmlEncode($tbl_updates->PensionerID->CurrentValue) ?>">
<?php } else { ?>
<input type="text" data-field="x_PensionerID" name="x<?php echo $tbl_updates_grid->RowIndex ?>_PensionerID" id="x<?php echo $tbl_updates_grid->RowIndex ?>_PensionerID" size="30" maxlength="25" placeholder="<?php echo $tbl_updates->PensionerID->PlaceHolder ?>" value="<?php echo $tbl_updates->PensionerID->EditValue ?>"<?php echo $tbl_updates->PensionerID->EditAttributes() ?>>
<?php } ?>
<?php } else { ?>
<span<?php echo $tbl_updates->PensionerID->ViewAttributes() ?>>
<?php echo $tbl_updates->PensionerID->ViewValue ?></span>
<input type="hidden" data-field="x_PensionerID" name="x<?php echo $tbl_updates_grid->RowIndex ?>_PensionerID" id="x<?php echo $tbl_updates_grid->RowIndex ?>_PensionerID" value="<?php echo ew_HtmlEncode($tbl_updates->PensionerID->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_PensionerID" name="o<?php echo $tbl_updates_grid->RowIndex ?>_PensionerID" id="o<?php echo $tbl_updates_grid->RowIndex ?>_PensionerID" value="<?php echo ew_HtmlEncode($tbl_updates->PensionerID->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_updates->status->Visible) { // status ?>
		<td>
<?php if ($tbl_updates->CurrentAction <> "F") { ?>
<span id="el$rowindex$_tbl_updates_status" class="control-group tbl_updates_status">
<input type="text" data-field="x_status" name="x<?php echo $tbl_updates_grid->RowIndex ?>_status" id="x<?php echo $tbl_updates_grid->RowIndex ?>_status" size="30" placeholder="<?php echo $tbl_updates->status->PlaceHolder ?>" value="<?php echo $tbl_updates->status->EditValue ?>"<?php echo $tbl_updates->status->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el$rowindex$_tbl_updates_status" class="control-group tbl_updates_status">
<span<?php echo $tbl_updates->status->ViewAttributes() ?>>
<?php echo $tbl_updates->status->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_status" name="x<?php echo $tbl_updates_grid->RowIndex ?>_status" id="x<?php echo $tbl_updates_grid->RowIndex ?>_status" value="<?php echo ew_HtmlEncode($tbl_updates->status->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_status" name="o<?php echo $tbl_updates_grid->RowIndex ?>_status" id="o<?php echo $tbl_updates_grid->RowIndex ?>_status" value="<?php echo ew_HtmlEncode($tbl_updates->status->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_updates->approved->Visible) { // approved ?>
		<td>
<?php if ($tbl_updates->CurrentAction <> "F") { ?>
<span id="el$rowindex$_tbl_updates_approved" class="control-group tbl_updates_approved">
<input type="text" data-field="x_approved" name="x<?php echo $tbl_updates_grid->RowIndex ?>_approved" id="x<?php echo $tbl_updates_grid->RowIndex ?>_approved" size="30" placeholder="<?php echo $tbl_updates->approved->PlaceHolder ?>" value="<?php echo $tbl_updates->approved->EditValue ?>"<?php echo $tbl_updates->approved->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el$rowindex$_tbl_updates_approved" class="control-group tbl_updates_approved">
<span<?php echo $tbl_updates->approved->ViewAttributes() ?>>
<?php echo $tbl_updates->approved->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_approved" name="x<?php echo $tbl_updates_grid->RowIndex ?>_approved" id="x<?php echo $tbl_updates_grid->RowIndex ?>_approved" value="<?php echo ew_HtmlEncode($tbl_updates->approved->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_approved" name="o<?php echo $tbl_updates_grid->RowIndex ?>_approved" id="o<?php echo $tbl_updates_grid->RowIndex ?>_approved" value="<?php echo ew_HtmlEncode($tbl_updates->approved->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_updates->dateUpdated->Visible) { // dateUpdated ?>
		<td>
<?php if ($tbl_updates->CurrentAction <> "F") { ?>
<span id="el$rowindex$_tbl_updates_dateUpdated" class="control-group tbl_updates_dateUpdated">
<input type="text" data-field="x_dateUpdated" name="x<?php echo $tbl_updates_grid->RowIndex ?>_dateUpdated" id="x<?php echo $tbl_updates_grid->RowIndex ?>_dateUpdated" placeholder="<?php echo $tbl_updates->dateUpdated->PlaceHolder ?>" value="<?php echo $tbl_updates->dateUpdated->EditValue ?>"<?php echo $tbl_updates->dateUpdated->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el$rowindex$_tbl_updates_dateUpdated" class="control-group tbl_updates_dateUpdated">
<span<?php echo $tbl_updates->dateUpdated->ViewAttributes() ?>>
<?php echo $tbl_updates->dateUpdated->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_dateUpdated" name="x<?php echo $tbl_updates_grid->RowIndex ?>_dateUpdated" id="x<?php echo $tbl_updates_grid->RowIndex ?>_dateUpdated" value="<?php echo ew_HtmlEncode($tbl_updates->dateUpdated->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_dateUpdated" name="o<?php echo $tbl_updates_grid->RowIndex ?>_dateUpdated" id="o<?php echo $tbl_updates_grid->RowIndex ?>_dateUpdated" value="<?php echo ew_HtmlEncode($tbl_updates->dateUpdated->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_updates->_field->Visible) { // field ?>
		<td>
<?php if ($tbl_updates->CurrentAction <> "F") { ?>
<span id="el$rowindex$_tbl_updates__field" class="control-group tbl_updates__field">
<input type="text" data-field="x__field" name="x<?php echo $tbl_updates_grid->RowIndex ?>__field" id="x<?php echo $tbl_updates_grid->RowIndex ?>__field" size="30" maxlength="20" placeholder="<?php echo $tbl_updates->_field->PlaceHolder ?>" value="<?php echo $tbl_updates->_field->EditValue ?>"<?php echo $tbl_updates->_field->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el$rowindex$_tbl_updates__field" class="control-group tbl_updates__field">
<span<?php echo $tbl_updates->_field->ViewAttributes() ?>>
<?php echo $tbl_updates->_field->ViewValue ?></span>
</span>
<input type="hidden" data-field="x__field" name="x<?php echo $tbl_updates_grid->RowIndex ?>__field" id="x<?php echo $tbl_updates_grid->RowIndex ?>__field" value="<?php echo ew_HtmlEncode($tbl_updates->_field->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x__field" name="o<?php echo $tbl_updates_grid->RowIndex ?>__field" id="o<?php echo $tbl_updates_grid->RowIndex ?>__field" value="<?php echo ew_HtmlEncode($tbl_updates->_field->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_updates->paymentmodeID->Visible) { // paymentmodeID ?>
		<td>
<?php if ($tbl_updates->CurrentAction <> "F") { ?>
<span id="el$rowindex$_tbl_updates_paymentmodeID" class="control-group tbl_updates_paymentmodeID">
<input type="text" data-field="x_paymentmodeID" name="x<?php echo $tbl_updates_grid->RowIndex ?>_paymentmodeID" id="x<?php echo $tbl_updates_grid->RowIndex ?>_paymentmodeID" size="30" placeholder="<?php echo $tbl_updates->paymentmodeID->PlaceHolder ?>" value="<?php echo $tbl_updates->paymentmodeID->EditValue ?>"<?php echo $tbl_updates->paymentmodeID->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el$rowindex$_tbl_updates_paymentmodeID" class="control-group tbl_updates_paymentmodeID">
<span<?php echo $tbl_updates->paymentmodeID->ViewAttributes() ?>>
<?php echo $tbl_updates->paymentmodeID->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_paymentmodeID" name="x<?php echo $tbl_updates_grid->RowIndex ?>_paymentmodeID" id="x<?php echo $tbl_updates_grid->RowIndex ?>_paymentmodeID" value="<?php echo ew_HtmlEncode($tbl_updates->paymentmodeID->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_paymentmodeID" name="o<?php echo $tbl_updates_grid->RowIndex ?>_paymentmodeID" id="o<?php echo $tbl_updates_grid->RowIndex ?>_paymentmodeID" value="<?php echo ew_HtmlEncode($tbl_updates->paymentmodeID->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_updates->deathDate->Visible) { // deathDate ?>
		<td>
<?php if ($tbl_updates->CurrentAction <> "F") { ?>
<span id="el$rowindex$_tbl_updates_deathDate" class="control-group tbl_updates_deathDate">
<input type="text" data-field="x_deathDate" name="x<?php echo $tbl_updates_grid->RowIndex ?>_deathDate" id="x<?php echo $tbl_updates_grid->RowIndex ?>_deathDate" placeholder="<?php echo $tbl_updates->deathDate->PlaceHolder ?>" value="<?php echo $tbl_updates->deathDate->EditValue ?>"<?php echo $tbl_updates->deathDate->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el$rowindex$_tbl_updates_deathDate" class="control-group tbl_updates_deathDate">
<span<?php echo $tbl_updates->deathDate->ViewAttributes() ?>>
<?php echo $tbl_updates->deathDate->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_deathDate" name="x<?php echo $tbl_updates_grid->RowIndex ?>_deathDate" id="x<?php echo $tbl_updates_grid->RowIndex ?>_deathDate" value="<?php echo ew_HtmlEncode($tbl_updates->deathDate->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_deathDate" name="o<?php echo $tbl_updates_grid->RowIndex ?>_deathDate" id="o<?php echo $tbl_updates_grid->RowIndex ?>_deathDate" value="<?php echo ew_HtmlEncode($tbl_updates->deathDate->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_updates->Createdby->Visible) { // Createdby ?>
		<td>
<?php if ($tbl_updates->CurrentAction <> "F") { ?>
<span id="el$rowindex$_tbl_updates_Createdby" class="control-group tbl_updates_Createdby">
<input type="text" data-field="x_Createdby" name="x<?php echo $tbl_updates_grid->RowIndex ?>_Createdby" id="x<?php echo $tbl_updates_grid->RowIndex ?>_Createdby" size="30" placeholder="<?php echo $tbl_updates->Createdby->PlaceHolder ?>" value="<?php echo $tbl_updates->Createdby->EditValue ?>"<?php echo $tbl_updates->Createdby->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el$rowindex$_tbl_updates_Createdby" class="control-group tbl_updates_Createdby">
<span<?php echo $tbl_updates->Createdby->ViewAttributes() ?>>
<?php echo $tbl_updates->Createdby->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_Createdby" name="x<?php echo $tbl_updates_grid->RowIndex ?>_Createdby" id="x<?php echo $tbl_updates_grid->RowIndex ?>_Createdby" value="<?php echo ew_HtmlEncode($tbl_updates->Createdby->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_Createdby" name="o<?php echo $tbl_updates_grid->RowIndex ?>_Createdby" id="o<?php echo $tbl_updates_grid->RowIndex ?>_Createdby" value="<?php echo ew_HtmlEncode($tbl_updates->Createdby->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_updates->CreatedDate->Visible) { // CreatedDate ?>
		<td>
<?php if ($tbl_updates->CurrentAction <> "F") { ?>
<span id="el$rowindex$_tbl_updates_CreatedDate" class="control-group tbl_updates_CreatedDate">
<input type="text" data-field="x_CreatedDate" name="x<?php echo $tbl_updates_grid->RowIndex ?>_CreatedDate" id="x<?php echo $tbl_updates_grid->RowIndex ?>_CreatedDate" placeholder="<?php echo $tbl_updates->CreatedDate->PlaceHolder ?>" value="<?php echo $tbl_updates->CreatedDate->EditValue ?>"<?php echo $tbl_updates->CreatedDate->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el$rowindex$_tbl_updates_CreatedDate" class="control-group tbl_updates_CreatedDate">
<span<?php echo $tbl_updates->CreatedDate->ViewAttributes() ?>>
<?php echo $tbl_updates->CreatedDate->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_CreatedDate" name="x<?php echo $tbl_updates_grid->RowIndex ?>_CreatedDate" id="x<?php echo $tbl_updates_grid->RowIndex ?>_CreatedDate" value="<?php echo ew_HtmlEncode($tbl_updates->CreatedDate->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_CreatedDate" name="o<?php echo $tbl_updates_grid->RowIndex ?>_CreatedDate" id="o<?php echo $tbl_updates_grid->RowIndex ?>_CreatedDate" value="<?php echo ew_HtmlEncode($tbl_updates->CreatedDate->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_updates->UpdatedBy->Visible) { // UpdatedBy ?>
		<td>
<?php if ($tbl_updates->CurrentAction <> "F") { ?>
<span id="el$rowindex$_tbl_updates_UpdatedBy" class="control-group tbl_updates_UpdatedBy">
<input type="text" data-field="x_UpdatedBy" name="x<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedBy" id="x<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedBy" size="30" placeholder="<?php echo $tbl_updates->UpdatedBy->PlaceHolder ?>" value="<?php echo $tbl_updates->UpdatedBy->EditValue ?>"<?php echo $tbl_updates->UpdatedBy->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el$rowindex$_tbl_updates_UpdatedBy" class="control-group tbl_updates_UpdatedBy">
<span<?php echo $tbl_updates->UpdatedBy->ViewAttributes() ?>>
<?php echo $tbl_updates->UpdatedBy->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_UpdatedBy" name="x<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedBy" id="x<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedBy" value="<?php echo ew_HtmlEncode($tbl_updates->UpdatedBy->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_UpdatedBy" name="o<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedBy" id="o<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedBy" value="<?php echo ew_HtmlEncode($tbl_updates->UpdatedBy->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tbl_updates->UpdatedDate->Visible) { // UpdatedDate ?>
		<td>
<?php if ($tbl_updates->CurrentAction <> "F") { ?>
<span id="el$rowindex$_tbl_updates_UpdatedDate" class="control-group tbl_updates_UpdatedDate">
<input type="text" data-field="x_UpdatedDate" name="x<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedDate" id="x<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedDate" placeholder="<?php echo $tbl_updates->UpdatedDate->PlaceHolder ?>" value="<?php echo $tbl_updates->UpdatedDate->EditValue ?>"<?php echo $tbl_updates->UpdatedDate->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el$rowindex$_tbl_updates_UpdatedDate" class="control-group tbl_updates_UpdatedDate">
<span<?php echo $tbl_updates->UpdatedDate->ViewAttributes() ?>>
<?php echo $tbl_updates->UpdatedDate->ViewValue ?></span>
</span>
<input type="hidden" data-field="x_UpdatedDate" name="x<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedDate" id="x<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedDate" value="<?php echo ew_HtmlEncode($tbl_updates->UpdatedDate->FormValue) ?>">
<?php } ?>
<input type="hidden" data-field="x_UpdatedDate" name="o<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedDate" id="o<?php echo $tbl_updates_grid->RowIndex ?>_UpdatedDate" value="<?php echo ew_HtmlEncode($tbl_updates->UpdatedDate->OldValue) ?>">
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$tbl_updates_grid->ListOptions->Render("body", "right", $tbl_updates_grid->RowCnt);
?>
<script type="text/javascript">
ftbl_updatesgrid.UpdateOpts(<?php echo $tbl_updates_grid->RowIndex ?>);
</script>
	</tr>
<?php
}
?>
</tbody>
</table>
<?php if ($tbl_updates->CurrentMode == "add" || $tbl_updates->CurrentMode == "copy") { ?>
<input type="hidden" name="a_list" id="a_list" value="gridinsert">
<input type="hidden" name="<?php echo $tbl_updates_grid->FormKeyCountName ?>" id="<?php echo $tbl_updates_grid->FormKeyCountName ?>" value="<?php echo $tbl_updates_grid->KeyCount ?>">
<?php echo $tbl_updates_grid->MultiSelectKey ?>
<?php } ?>
<?php if ($tbl_updates->CurrentMode == "edit") { ?>
<input type="hidden" name="a_list" id="a_list" value="gridupdate">
<input type="hidden" name="<?php echo $tbl_updates_grid->FormKeyCountName ?>" id="<?php echo $tbl_updates_grid->FormKeyCountName ?>" value="<?php echo $tbl_updates_grid->KeyCount ?>">
<?php echo $tbl_updates_grid->MultiSelectKey ?>
<?php } ?>
<?php if ($tbl_updates->CurrentMode == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
<input type="hidden" name="detailpage" value="ftbl_updatesgrid">
</div>
<?php

// Close recordset
if ($tbl_updates_grid->Recordset)
	$tbl_updates_grid->Recordset->Close();
?>
<?php if ($tbl_updates_grid->ShowOtherOptions) { ?>
<div class="ewGridLowerPanel ewListOtherOptions">
<?php
	foreach ($tbl_updates_grid->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
<?php } ?>
</div>
</td></tr></table>
<?php if ($tbl_updates->Export == "") { ?>
<script type="text/javascript">
ftbl_updatesgrid.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php } ?>
<?php
$tbl_updates_grid->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php
$tbl_updates_grid->Page_Terminate();
?>
