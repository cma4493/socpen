<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "tbl_updatesinfo.php" ?>
<?php include_once "tbl_pensionerinfo.php" ?>
<?php include_once "tbl_userinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$tbl_updates_delete = NULL; // Initialize page object first

class ctbl_updates_delete extends ctbl_updates {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Table name
	var $TableName = 'tbl_updates';

	// Page object name
	var $PageObjName = 'tbl_updates_delete';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}
	var $AuditTrailOnDelete = TRUE;

	// Message
	function getMessage() {
		return @$_SESSION[EW_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EW_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EW_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EW_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_WARNING_MESSAGE], $v);
	}

	// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sMessage . "</div>";
			$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EW_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-error ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EW_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<table class=\"ewStdTable\"><tr><td><div class=\"ewMessageDialog\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div></td></tr></table>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") { // Header exists, display
			echo "<p>" . $sHeader . "</p>";
		}
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") { // Footer exists, display
			echo "<p>" . $sFooter . "</p>";
		}
	}

	// Validate page request
	function IsPageRequest() {
		global $objForm;
		if ($this->UseTokenInUrl) {
			if ($objForm)
				return ($this->TableVar == $objForm->GetValue("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == $_GET["t"]);
		} else {
			return TRUE;
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $Language, $UserAgent;

		// User agent
		$UserAgent = ew_UserAgent();
		$GLOBALS["Page"] = &$this;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (tbl_updates)
		if (!isset($GLOBALS["tbl_updates"])) {
			$GLOBALS["tbl_updates"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["tbl_updates"];
		}

		// Table object (tbl_pensioner)
		if (!isset($GLOBALS['tbl_pensioner'])) $GLOBALS['tbl_pensioner'] = new ctbl_pensioner();

		// Table object (tbl_user)
		if (!isset($GLOBALS['tbl_user'])) $GLOBALS['tbl_user'] = new ctbl_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'delete', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'tbl_updates', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();
	}

	// 
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;

		// User profile
		$UserProfile = new cUserProfile();
		$UserProfile->LoadProfile(@$_SESSION[EW_SESSION_USER_PROFILE]);

		// Security
		$Security = new cAdvancedSecurity();
		if (IsPasswordExpired())
			$this->Page_Terminate("changepwd.php");
		if (!$Security->IsLoggedIn()) $Security->AutoLogin();
		if (!$Security->IsLoggedIn()) {
			$Security->SaveLastUrl();
			$this->Page_Terminate("login.php");
		}
		$Security->TablePermission_Loading();
		$Security->LoadCurrentUserLevel($this->ProjectID . $this->TableName);
		$Security->TablePermission_Loaded();
		if (!$Security->IsLoggedIn()) {
			$Security->SaveLastUrl();
			$this->Page_Terminate("login.php");
		}
		if (!$Security->CanDelete()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate("tbl_updateslist.php");
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();

		// Update last accessed time
		if ($UserProfile->IsValidUser(session_id())) {
			if (!$Security->IsSysAdmin())
				$UserProfile->SaveProfileToDatabase(CurrentUserName()); // Update last accessed time to user profile
		} else {
			echo $Language->Phrase("UserProfileCorrupted");
		}
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up curent action
		$this->updatesID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $conn;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();
		$this->Page_Redirecting($url);

		 // Close connection
		$conn->Close();

		// Go to URL if specified
		if ($url <> "") {
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}
	var $TotalRecs = 0;
	var $RecCnt;
	var $RecKeys = array();
	var $Recordset;
	var $StartRowCnt = 1;
	var $RowCnt = 0;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Load key parameters
		$this->RecKeys = $this->GetRecordKeys(); // Load record keys
		$sFilter = $this->GetKeyFilter();
		if ($sFilter == "")
			$this->Page_Terminate("tbl_updateslist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in tbl_updates class, tbl_updatesinfo.php

		$this->CurrentFilter = $sFilter;

		// Get action
		if (@$_POST["a_delete"] <> "") {
			$this->CurrentAction = $_POST["a_delete"];
		} else {
			$this->CurrentAction = "D"; // Delete record directly
		}
		switch ($this->CurrentAction) {
			case "D": // Delete
				$this->SendEmail = TRUE; // Send email on delete success
				if ($this->DeleteRows()) { // Delete rows
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("DeleteSuccess")); // Set up success message
					$this->Page_Terminate($this->getReturnUrl()); // Return to caller
				}
		}
	}

// No functions
	// Load recordset
	function LoadRecordset($offset = -1, $rowcnt = -1) {
		global $conn;

		// Call Recordset Selecting event
		$this->Recordset_Selecting($this->CurrentFilter);

		// Load List page SQL
		$sSql = $this->SelectSQL();
		if ($offset > -1 && $rowcnt > -1)
			$sSql .= " LIMIT $rowcnt OFFSET $offset";

		// Load recordset
		$rs = ew_LoadRecordset($sSql);

		// Call Recordset Selected event
		$this->Recordset_Selected($rs);
		return $rs;
	}

	// Load row based on key values
	function LoadRow() {
		global $conn, $Security, $Language;
		$sFilter = $this->KeyFilter();

		// Call Row Selecting event
		$this->Row_Selecting($sFilter);

		// Load SQL based on filter
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$res = FALSE;
		$rs = ew_LoadRecordset($sSql);
		if ($rs && !$rs->EOF) {
			$res = TRUE;
			$this->LoadRowValues($rs); // Load row values
			$rs->Close();
		}
		return $res;
	}

	// Load row values from recordset
	function LoadRowValues(&$rs) {
		global $conn;
		if (!$rs || $rs->EOF) return;

		// Call Row Selected event
		$row = &$rs->fields;
		$this->Row_Selected($row);
		$this->updatesID->setDbValue($rs->fields('updatesID'));
		$this->PensionerID->setDbValue($rs->fields('PensionerID'));
		$this->status->setDbValue($rs->fields('status'));
		$this->Remarks->setDbValue($rs->fields('Remarks'));
		$this->approved->setDbValue($rs->fields('approved'));
		$this->dateUpdated->setDbValue($rs->fields('dateUpdated'));
		$this->_field->setDbValue($rs->fields('field'));
		$this->new_value->setDbValue($rs->fields('new_value'));
		$this->old_value->setDbValue($rs->fields('old_value'));
		$this->paymentmodeID->setDbValue($rs->fields('paymentmodeID'));
		$this->deathDate->setDbValue($rs->fields('deathDate'));
		$this->Createdby->setDbValue($rs->fields('Createdby'));
		$this->CreatedDate->setDbValue($rs->fields('CreatedDate'));
		$this->UpdatedBy->setDbValue($rs->fields('UpdatedBy'));
		$this->UpdatedDate->setDbValue($rs->fields('UpdatedDate'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->updatesID->DbValue = $row['updatesID'];
		$this->PensionerID->DbValue = $row['PensionerID'];
		$this->status->DbValue = $row['status'];
		$this->Remarks->DbValue = $row['Remarks'];
		$this->approved->DbValue = $row['approved'];
		$this->dateUpdated->DbValue = $row['dateUpdated'];
		$this->_field->DbValue = $row['field'];
		$this->new_value->DbValue = $row['new_value'];
		$this->old_value->DbValue = $row['old_value'];
		$this->paymentmodeID->DbValue = $row['paymentmodeID'];
		$this->deathDate->DbValue = $row['deathDate'];
		$this->Createdby->DbValue = $row['Createdby'];
		$this->CreatedDate->DbValue = $row['CreatedDate'];
		$this->UpdatedBy->DbValue = $row['UpdatedBy'];
		$this->UpdatedDate->DbValue = $row['UpdatedDate'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// updatesID
		// PensionerID
		// status
		// Remarks
		// approved
		// dateUpdated
		// field
		// new_value
		// old_value
		// paymentmodeID
		// deathDate
		// Createdby
		// CreatedDate
		// UpdatedBy
		// UpdatedDate

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// updatesID
			$this->updatesID->ViewValue = $this->updatesID->CurrentValue;
			$this->updatesID->ViewCustomAttributes = "";

			// PensionerID
			$this->PensionerID->ViewValue = $this->PensionerID->CurrentValue;
			$this->PensionerID->ViewCustomAttributes = "";

			// status
			$this->status->ViewValue = $this->status->CurrentValue;
			$this->status->ViewCustomAttributes = "";

			// approved
			$this->approved->ViewValue = $this->approved->CurrentValue;
			$this->approved->ViewCustomAttributes = "";

			// dateUpdated
			$this->dateUpdated->ViewValue = $this->dateUpdated->CurrentValue;
			$this->dateUpdated->ViewValue = ew_FormatDateTime($this->dateUpdated->ViewValue, 6);
			$this->dateUpdated->ViewCustomAttributes = "";

			// field
			$this->_field->ViewValue = $this->_field->CurrentValue;
			$this->_field->ViewCustomAttributes = "";

			// paymentmodeID
			$this->paymentmodeID->ViewValue = $this->paymentmodeID->CurrentValue;
			$this->paymentmodeID->ViewCustomAttributes = "";

			// deathDate
			$this->deathDate->ViewValue = $this->deathDate->CurrentValue;
			$this->deathDate->ViewValue = ew_FormatDateTime($this->deathDate->ViewValue, 6);
			$this->deathDate->ViewCustomAttributes = "";

			// Createdby
			$this->Createdby->ViewValue = $this->Createdby->CurrentValue;
			$this->Createdby->ViewCustomAttributes = "";

			// CreatedDate
			$this->CreatedDate->ViewValue = $this->CreatedDate->CurrentValue;
			$this->CreatedDate->ViewValue = ew_FormatDateTime($this->CreatedDate->ViewValue, 6);
			$this->CreatedDate->ViewCustomAttributes = "";

			// UpdatedBy
			$this->UpdatedBy->ViewValue = $this->UpdatedBy->CurrentValue;
			$this->UpdatedBy->ViewCustomAttributes = "";

			// UpdatedDate
			$this->UpdatedDate->ViewValue = $this->UpdatedDate->CurrentValue;
			$this->UpdatedDate->ViewValue = ew_FormatDateTime($this->UpdatedDate->ViewValue, 6);
			$this->UpdatedDate->ViewCustomAttributes = "";

			// updatesID
			$this->updatesID->LinkCustomAttributes = "";
			$this->updatesID->HrefValue = "";
			$this->updatesID->TooltipValue = "";

			// PensionerID
			$this->PensionerID->LinkCustomAttributes = "";
			$this->PensionerID->HrefValue = "";
			$this->PensionerID->TooltipValue = "";

			// status
			$this->status->LinkCustomAttributes = "";
			$this->status->HrefValue = "";
			$this->status->TooltipValue = "";

			// approved
			$this->approved->LinkCustomAttributes = "";
			$this->approved->HrefValue = "";
			$this->approved->TooltipValue = "";

			// dateUpdated
			$this->dateUpdated->LinkCustomAttributes = "";
			$this->dateUpdated->HrefValue = "";
			$this->dateUpdated->TooltipValue = "";

			// field
			$this->_field->LinkCustomAttributes = "";
			$this->_field->HrefValue = "";
			$this->_field->TooltipValue = "";

			// paymentmodeID
			$this->paymentmodeID->LinkCustomAttributes = "";
			$this->paymentmodeID->HrefValue = "";
			$this->paymentmodeID->TooltipValue = "";

			// deathDate
			$this->deathDate->LinkCustomAttributes = "";
			$this->deathDate->HrefValue = "";
			$this->deathDate->TooltipValue = "";

			// Createdby
			$this->Createdby->LinkCustomAttributes = "";
			$this->Createdby->HrefValue = "";
			$this->Createdby->TooltipValue = "";

			// CreatedDate
			$this->CreatedDate->LinkCustomAttributes = "";
			$this->CreatedDate->HrefValue = "";
			$this->CreatedDate->TooltipValue = "";

			// UpdatedBy
			$this->UpdatedBy->LinkCustomAttributes = "";
			$this->UpdatedBy->HrefValue = "";
			$this->UpdatedBy->TooltipValue = "";

			// UpdatedDate
			$this->UpdatedDate->LinkCustomAttributes = "";
			$this->UpdatedDate->HrefValue = "";
			$this->UpdatedDate->TooltipValue = "";
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	//
	// Delete records based on current filter
	//
	function DeleteRows() {
		global $conn, $Language, $Security;
		if (!$Security->CanDelete()) {
			$this->setFailureMessage($Language->Phrase("NoDeletePermission")); // No delete permission
			return FALSE;
		}
		$DeleteRows = TRUE;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = 'ew_ErrorFn';
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE) {
			return FALSE;
		} elseif ($rs->EOF) {
			$this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
			$rs->Close();
			return FALSE;

		//} else {
		//	$this->LoadRowValues($rs); // Load row values

		}
		$conn->BeginTrans();
		if ($this->AuditTrailOnDelete) $this->WriteAuditTrailDummy($Language->Phrase("BatchDeleteBegin")); // Batch delete begin

		// Clone old rows
		$rsold = ($rs) ? $rs->GetRows() : array();
		if ($rs)
			$rs->Close();

		// Call row deleting event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$DeleteRows = $this->Row_Deleting($row);
				if (!$DeleteRows) break;
			}
		}
		if ($DeleteRows) {
			$sKey = "";
			foreach ($rsold as $row) {
				$sThisKey = "";
				if ($sThisKey <> "") $sThisKey .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
				$sThisKey .= $row['updatesID'];
				$conn->raiseErrorFn = 'ew_ErrorFn';
				$DeleteRows = $this->Delete($row); // Delete
				$conn->raiseErrorFn = '';
				if ($DeleteRows === FALSE)
					break;
				if ($sKey <> "") $sKey .= ", ";
				$sKey .= $sThisKey;
			}
		} else {

			// Set up error message
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("DeleteCancelled"));
			}
		}
		if ($DeleteRows) {
			$conn->CommitTrans(); // Commit the changes
			if ($DeleteRows) {
				foreach ($rsold as $row)
					$this->WriteAuditTrailOnDelete($row);
			}
			if ($this->AuditTrailOnDelete) $this->WriteAuditTrailDummy($Language->Phrase("BatchDeleteSuccess")); // Batch delete success
		} else {
			$conn->RollbackTrans(); // Rollback changes
			if ($this->AuditTrailOnDelete) $this->WriteAuditTrailDummy($Language->Phrase("BatchDeleteRollback")); // Batch delete rollback
		}

		// Call Row Deleted event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$this->Row_Deleted($row);
			}
		}
		return $DeleteRows;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$PageCaption = $this->TableCaption();
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "tbl_updateslist.php", $this->TableVar);
		$PageCaption = $Language->Phrase("delete");
		$Breadcrumb->Add("delete", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", ew_CurrentUrl(), $this->TableVar);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'tbl_updates';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (delete page)
	function WriteAuditTrailOnDelete(&$rs) {
		if (!$this->AuditTrailOnDelete) return;
		$table = 'tbl_updates';

		// Get key value
		$key = "";
		if ($key <> "")
			$key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['updatesID'];

		// Write Audit Trail
		$dt = ew_StdCurrentDateTime();
		$id = ew_ScriptName();
	  $curUser = CurrentUserID();
		foreach (array_keys($rs) as $fldname) {
			if (array_key_exists($fldname, $this->fields) && $this->fields[$fldname]->FldDataType <> EW_DATATYPE_BLOB) { // Ignore BLOB fields
				if ($this->fields[$fldname]->FldDataType == EW_DATATYPE_MEMO) {
					if (EW_AUDIT_TRAIL_TO_DATABASE)
						$oldvalue = $rs[$fldname];
					else
						$oldvalue = "[MEMO]"; // Memo field
				} elseif ($this->fields[$fldname]->FldDataType == EW_DATATYPE_XML) {
					$oldvalue = "[XML]"; // XML field
				} else {
					$oldvalue = $rs[$fldname];
				}
				ew_WriteAuditTrail("log", $dt, $id, $curUser, "D", $table, $fldname, $key, $oldvalue, "");
			}
		}
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Page Redirecting event
	function Page_Redirecting(&$url) {

		// Example:
		//$url = "your URL";

	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Render event
	function Page_Render() {

		//echo "Page Render";
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($tbl_updates_delete)) $tbl_updates_delete = new ctbl_updates_delete();

// Page init
$tbl_updates_delete->Page_Init();

// Page main
$tbl_updates_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$tbl_updates_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var tbl_updates_delete = new ew_Page("tbl_updates_delete");
tbl_updates_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = tbl_updates_delete.PageID; // For backward compatibility

// Form object
var ftbl_updatesdelete = new ew_Form("ftbl_updatesdelete");

// Form_CustomValidate event
ftbl_updatesdelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftbl_updatesdelete.ValidateRequired = true;
<?php } else { ?>
ftbl_updatesdelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($tbl_updates_delete->Recordset = $tbl_updates_delete->LoadRecordset())
	$tbl_updates_deleteTotalRecs = $tbl_updates_delete->Recordset->RecordCount(); // Get record count
if ($tbl_updates_deleteTotalRecs <= 0) { // No record found, exit
	if ($tbl_updates_delete->Recordset)
		$tbl_updates_delete->Recordset->Close();
	$tbl_updates_delete->Page_Terminate("tbl_updateslist.php"); // Return to list
}
?>
<?php //$Breadcrumb->Render(); ?>
<?php $tbl_updates_delete->ShowPageHeader(); ?>
<?php
$tbl_updates_delete->ShowMessage();
?>
<form name="ftbl_updatesdelete" id="ftbl_updatesdelete" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="tbl_updates">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($tbl_updates_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_tbl_updatesdelete" class="ewTable ewTableSeparate">
<?php echo $tbl_updates->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($tbl_updates->updatesID->Visible) { // updatesID ?>
		<td><span id="elh_tbl_updates_updatesID" class="tbl_updates_updatesID"><?php echo $tbl_updates->updatesID->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_updates->PensionerID->Visible) { // PensionerID ?>
		<td><span id="elh_tbl_updates_PensionerID" class="tbl_updates_PensionerID"><?php echo $tbl_updates->PensionerID->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_updates->status->Visible) { // status ?>
		<td><span id="elh_tbl_updates_status" class="tbl_updates_status"><?php echo $tbl_updates->status->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_updates->approved->Visible) { // approved ?>
		<td><span id="elh_tbl_updates_approved" class="tbl_updates_approved"><?php echo $tbl_updates->approved->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_updates->dateUpdated->Visible) { // dateUpdated ?>
		<td><span id="elh_tbl_updates_dateUpdated" class="tbl_updates_dateUpdated"><?php echo $tbl_updates->dateUpdated->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_updates->_field->Visible) { // field ?>
		<td><span id="elh_tbl_updates__field" class="tbl_updates__field"><?php echo $tbl_updates->_field->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_updates->paymentmodeID->Visible) { // paymentmodeID ?>
		<td><span id="elh_tbl_updates_paymentmodeID" class="tbl_updates_paymentmodeID"><?php echo $tbl_updates->paymentmodeID->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_updates->deathDate->Visible) { // deathDate ?>
		<td><span id="elh_tbl_updates_deathDate" class="tbl_updates_deathDate"><?php echo $tbl_updates->deathDate->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_updates->Createdby->Visible) { // Createdby ?>
		<td><span id="elh_tbl_updates_Createdby" class="tbl_updates_Createdby"><?php echo $tbl_updates->Createdby->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_updates->CreatedDate->Visible) { // CreatedDate ?>
		<td><span id="elh_tbl_updates_CreatedDate" class="tbl_updates_CreatedDate"><?php echo $tbl_updates->CreatedDate->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_updates->UpdatedBy->Visible) { // UpdatedBy ?>
		<td><span id="elh_tbl_updates_UpdatedBy" class="tbl_updates_UpdatedBy"><?php echo $tbl_updates->UpdatedBy->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_updates->UpdatedDate->Visible) { // UpdatedDate ?>
		<td><span id="elh_tbl_updates_UpdatedDate" class="tbl_updates_UpdatedDate"><?php echo $tbl_updates->UpdatedDate->FldCaption() ?></span></td>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$tbl_updates_delete->RecCnt = 0;
$i = 0;
while (!$tbl_updates_delete->Recordset->EOF) {
	$tbl_updates_delete->RecCnt++;
	$tbl_updates_delete->RowCnt++;

	// Set row properties
	$tbl_updates->ResetAttrs();
	$tbl_updates->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$tbl_updates_delete->LoadRowValues($tbl_updates_delete->Recordset);

	// Render row
	$tbl_updates_delete->RenderRow();
?>
	<tr<?php echo $tbl_updates->RowAttributes() ?>>
<?php if ($tbl_updates->updatesID->Visible) { // updatesID ?>
		<td<?php echo $tbl_updates->updatesID->CellAttributes() ?>>
<span id="el<?php echo $tbl_updates_delete->RowCnt ?>_tbl_updates_updatesID" class="control-group tbl_updates_updatesID">
<span<?php echo $tbl_updates->updatesID->ViewAttributes() ?>>
<?php echo $tbl_updates->updatesID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_updates->PensionerID->Visible) { // PensionerID ?>
		<td<?php echo $tbl_updates->PensionerID->CellAttributes() ?>>
<span id="el<?php echo $tbl_updates_delete->RowCnt ?>_tbl_updates_PensionerID" class="control-group tbl_updates_PensionerID">
<span<?php echo $tbl_updates->PensionerID->ViewAttributes() ?>>
<?php echo $tbl_updates->PensionerID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_updates->status->Visible) { // status ?>
		<td<?php echo $tbl_updates->status->CellAttributes() ?>>
<span id="el<?php echo $tbl_updates_delete->RowCnt ?>_tbl_updates_status" class="control-group tbl_updates_status">
<span<?php echo $tbl_updates->status->ViewAttributes() ?>>
<?php echo $tbl_updates->status->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_updates->approved->Visible) { // approved ?>
		<td<?php echo $tbl_updates->approved->CellAttributes() ?>>
<span id="el<?php echo $tbl_updates_delete->RowCnt ?>_tbl_updates_approved" class="control-group tbl_updates_approved">
<span<?php echo $tbl_updates->approved->ViewAttributes() ?>>
<?php echo $tbl_updates->approved->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_updates->dateUpdated->Visible) { // dateUpdated ?>
		<td<?php echo $tbl_updates->dateUpdated->CellAttributes() ?>>
<span id="el<?php echo $tbl_updates_delete->RowCnt ?>_tbl_updates_dateUpdated" class="control-group tbl_updates_dateUpdated">
<span<?php echo $tbl_updates->dateUpdated->ViewAttributes() ?>>
<?php echo $tbl_updates->dateUpdated->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_updates->_field->Visible) { // field ?>
		<td<?php echo $tbl_updates->_field->CellAttributes() ?>>
<span id="el<?php echo $tbl_updates_delete->RowCnt ?>_tbl_updates__field" class="control-group tbl_updates__field">
<span<?php echo $tbl_updates->_field->ViewAttributes() ?>>
<?php echo $tbl_updates->_field->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_updates->paymentmodeID->Visible) { // paymentmodeID ?>
		<td<?php echo $tbl_updates->paymentmodeID->CellAttributes() ?>>
<span id="el<?php echo $tbl_updates_delete->RowCnt ?>_tbl_updates_paymentmodeID" class="control-group tbl_updates_paymentmodeID">
<span<?php echo $tbl_updates->paymentmodeID->ViewAttributes() ?>>
<?php echo $tbl_updates->paymentmodeID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_updates->deathDate->Visible) { // deathDate ?>
		<td<?php echo $tbl_updates->deathDate->CellAttributes() ?>>
<span id="el<?php echo $tbl_updates_delete->RowCnt ?>_tbl_updates_deathDate" class="control-group tbl_updates_deathDate">
<span<?php echo $tbl_updates->deathDate->ViewAttributes() ?>>
<?php echo $tbl_updates->deathDate->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_updates->Createdby->Visible) { // Createdby ?>
		<td<?php echo $tbl_updates->Createdby->CellAttributes() ?>>
<span id="el<?php echo $tbl_updates_delete->RowCnt ?>_tbl_updates_Createdby" class="control-group tbl_updates_Createdby">
<span<?php echo $tbl_updates->Createdby->ViewAttributes() ?>>
<?php echo $tbl_updates->Createdby->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_updates->CreatedDate->Visible) { // CreatedDate ?>
		<td<?php echo $tbl_updates->CreatedDate->CellAttributes() ?>>
<span id="el<?php echo $tbl_updates_delete->RowCnt ?>_tbl_updates_CreatedDate" class="control-group tbl_updates_CreatedDate">
<span<?php echo $tbl_updates->CreatedDate->ViewAttributes() ?>>
<?php echo $tbl_updates->CreatedDate->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_updates->UpdatedBy->Visible) { // UpdatedBy ?>
		<td<?php echo $tbl_updates->UpdatedBy->CellAttributes() ?>>
<span id="el<?php echo $tbl_updates_delete->RowCnt ?>_tbl_updates_UpdatedBy" class="control-group tbl_updates_UpdatedBy">
<span<?php echo $tbl_updates->UpdatedBy->ViewAttributes() ?>>
<?php echo $tbl_updates->UpdatedBy->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_updates->UpdatedDate->Visible) { // UpdatedDate ?>
		<td<?php echo $tbl_updates->UpdatedDate->CellAttributes() ?>>
<span id="el<?php echo $tbl_updates_delete->RowCnt ?>_tbl_updates_UpdatedDate" class="control-group tbl_updates_UpdatedDate">
<span<?php echo $tbl_updates->UpdatedDate->ViewAttributes() ?>>
<?php echo $tbl_updates->UpdatedDate->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$tbl_updates_delete->Recordset->MoveNext();
}
$tbl_updates_delete->Recordset->Close();
?>
</tbody>
</table>
</div>
</td></tr></table>
<div class="btn-group ewButtonGroup">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("DeleteBtn") ?></button>
</div>
</form>
<script type="text/javascript">
ftbl_updatesdelete.Init();
</script>
<?php
$tbl_updates_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$tbl_updates_delete->Page_Terminate();
?>
