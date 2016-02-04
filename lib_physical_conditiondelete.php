<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "lib_physical_conditioninfo.php" ?>
<?php include_once "tbl_userinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$lib_physical_condition_delete = NULL; // Initialize page object first

class clib_physical_condition_delete extends clib_physical_condition {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Table name
	var $TableName = 'lib_physical_condition';

	// Page object name
	var $PageObjName = 'lib_physical_condition_delete';

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

		// Table object (lib_physical_condition)
		if (!isset($GLOBALS["lib_physical_condition"])) {
			$GLOBALS["lib_physical_condition"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["lib_physical_condition"];
		}

		// Table object (tbl_user)
		if (!isset($GLOBALS['tbl_user'])) $GLOBALS['tbl_user'] = new ctbl_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'delete', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'lib_physical_condition', TRUE);

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
			$this->Page_Terminate("lib_physical_conditionlist.php");
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
		$this->physconditionID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
			$this->Page_Terminate("lib_physical_conditionlist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in lib_physical_condition class, lib_physical_conditioninfo.php

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
		$this->physconditionID->setDbValue($rs->fields('physconditionID'));
		$this->physconditionName->setDbValue($rs->fields('physconditionName'));
		$this->created_by->setDbValue($rs->fields('created_by'));
		$this->date_created->setDbValue($rs->fields('date_created'));
		$this->modified_by->setDbValue($rs->fields('modified_by'));
		$this->date_modified->setDbValue($rs->fields('date_modified'));
		$this->DELETED->setDbValue($rs->fields('DELETED'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->physconditionID->DbValue = $row['physconditionID'];
		$this->physconditionName->DbValue = $row['physconditionName'];
		$this->created_by->DbValue = $row['created_by'];
		$this->date_created->DbValue = $row['date_created'];
		$this->modified_by->DbValue = $row['modified_by'];
		$this->date_modified->DbValue = $row['date_modified'];
		$this->DELETED->DbValue = $row['DELETED'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// physconditionID
		// physconditionName
		// created_by
		// date_created
		// modified_by
		// date_modified
		// DELETED

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// physconditionID
			$this->physconditionID->ViewValue = $this->physconditionID->CurrentValue;
			$this->physconditionID->ViewCustomAttributes = "";

			// physconditionName
			$this->physconditionName->ViewValue = $this->physconditionName->CurrentValue;
			$this->physconditionName->ViewCustomAttributes = "";

			// created_by
			$this->created_by->ViewValue = $this->created_by->CurrentValue;
			$this->created_by->ViewCustomAttributes = "";

			// date_created
			$this->date_created->ViewValue = $this->date_created->CurrentValue;
			$this->date_created->ViewValue = ew_FormatDateTime($this->date_created->ViewValue, 6);
			$this->date_created->ViewCustomAttributes = "";

			// modified_by
			$this->modified_by->ViewValue = $this->modified_by->CurrentValue;
			$this->modified_by->ViewCustomAttributes = "";

			// date_modified
			$this->date_modified->ViewValue = $this->date_modified->CurrentValue;
			$this->date_modified->ViewValue = ew_FormatDateTime($this->date_modified->ViewValue, 6);
			$this->date_modified->ViewCustomAttributes = "";

			// DELETED
			$this->DELETED->ViewValue = $this->DELETED->CurrentValue;
			$this->DELETED->ViewCustomAttributes = "";

			// physconditionID
			$this->physconditionID->LinkCustomAttributes = "";
			$this->physconditionID->HrefValue = "";
			$this->physconditionID->TooltipValue = "";

			// physconditionName
			$this->physconditionName->LinkCustomAttributes = "";
			$this->physconditionName->HrefValue = "";
			$this->physconditionName->TooltipValue = "";

			// created_by
			$this->created_by->LinkCustomAttributes = "";
			$this->created_by->HrefValue = "";
			$this->created_by->TooltipValue = "";

			// date_created
			$this->date_created->LinkCustomAttributes = "";
			$this->date_created->HrefValue = "";
			$this->date_created->TooltipValue = "";

			// modified_by
			$this->modified_by->LinkCustomAttributes = "";
			$this->modified_by->HrefValue = "";
			$this->modified_by->TooltipValue = "";

			// date_modified
			$this->date_modified->LinkCustomAttributes = "";
			$this->date_modified->HrefValue = "";
			$this->date_modified->TooltipValue = "";

			// DELETED
			$this->DELETED->LinkCustomAttributes = "";
			$this->DELETED->HrefValue = "";
			$this->DELETED->TooltipValue = "";
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
				$sThisKey .= $row['physconditionID'];
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
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "lib_physical_conditionlist.php", $this->TableVar);
		$PageCaption = $Language->Phrase("delete");
		$Breadcrumb->Add("delete", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", ew_CurrentUrl(), $this->TableVar);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'lib_physical_condition';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (delete page)
	function WriteAuditTrailOnDelete(&$rs) {
		if (!$this->AuditTrailOnDelete) return;
		$table = 'lib_physical_condition';

		// Get key value
		$key = "";
		if ($key <> "")
			$key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['physconditionID'];

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
if (!isset($lib_physical_condition_delete)) $lib_physical_condition_delete = new clib_physical_condition_delete();

// Page init
$lib_physical_condition_delete->Page_Init();

// Page main
$lib_physical_condition_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$lib_physical_condition_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var lib_physical_condition_delete = new ew_Page("lib_physical_condition_delete");
lib_physical_condition_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = lib_physical_condition_delete.PageID; // For backward compatibility

// Form object
var flib_physical_conditiondelete = new ew_Form("flib_physical_conditiondelete");

// Form_CustomValidate event
flib_physical_conditiondelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
flib_physical_conditiondelete.ValidateRequired = true;
<?php } else { ?>
flib_physical_conditiondelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($lib_physical_condition_delete->Recordset = $lib_physical_condition_delete->LoadRecordset())
	$lib_physical_condition_deleteTotalRecs = $lib_physical_condition_delete->Recordset->RecordCount(); // Get record count
if ($lib_physical_condition_deleteTotalRecs <= 0) { // No record found, exit
	if ($lib_physical_condition_delete->Recordset)
		$lib_physical_condition_delete->Recordset->Close();
	$lib_physical_condition_delete->Page_Terminate("lib_physical_conditionlist.php"); // Return to list
}
?>
<?php //$Breadcrumb->Render(); ?>
<?php $lib_physical_condition_delete->ShowPageHeader(); ?>
<?php
$lib_physical_condition_delete->ShowMessage();
?>
<form name="flib_physical_conditiondelete" id="flib_physical_conditiondelete" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="lib_physical_condition">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($lib_physical_condition_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_lib_physical_conditiondelete" class="ewTable ewTableSeparate">
<?php echo $lib_physical_condition->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($lib_physical_condition->physconditionID->Visible) { // physconditionID ?>
		<td><span id="elh_lib_physical_condition_physconditionID" class="lib_physical_condition_physconditionID"><?php echo $lib_physical_condition->physconditionID->FldCaption() ?></span></td>
<?php } ?>
<?php if ($lib_physical_condition->physconditionName->Visible) { // physconditionName ?>
		<td><span id="elh_lib_physical_condition_physconditionName" class="lib_physical_condition_physconditionName"><?php echo $lib_physical_condition->physconditionName->FldCaption() ?></span></td>
<?php } ?>
<?php if ($lib_physical_condition->created_by->Visible) { // created_by ?>
		<td><span id="elh_lib_physical_condition_created_by" class="lib_physical_condition_created_by"><?php echo $lib_physical_condition->created_by->FldCaption() ?></span></td>
<?php } ?>
<?php if ($lib_physical_condition->date_created->Visible) { // date_created ?>
		<td><span id="elh_lib_physical_condition_date_created" class="lib_physical_condition_date_created"><?php echo $lib_physical_condition->date_created->FldCaption() ?></span></td>
<?php } ?>
<?php if ($lib_physical_condition->modified_by->Visible) { // modified_by ?>
		<td><span id="elh_lib_physical_condition_modified_by" class="lib_physical_condition_modified_by"><?php echo $lib_physical_condition->modified_by->FldCaption() ?></span></td>
<?php } ?>
<?php if ($lib_physical_condition->date_modified->Visible) { // date_modified ?>
		<td><span id="elh_lib_physical_condition_date_modified" class="lib_physical_condition_date_modified"><?php echo $lib_physical_condition->date_modified->FldCaption() ?></span></td>
<?php } ?>
<?php if ($lib_physical_condition->DELETED->Visible) { // DELETED ?>
		<td><span id="elh_lib_physical_condition_DELETED" class="lib_physical_condition_DELETED"><?php echo $lib_physical_condition->DELETED->FldCaption() ?></span></td>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$lib_physical_condition_delete->RecCnt = 0;
$i = 0;
while (!$lib_physical_condition_delete->Recordset->EOF) {
	$lib_physical_condition_delete->RecCnt++;
	$lib_physical_condition_delete->RowCnt++;

	// Set row properties
	$lib_physical_condition->ResetAttrs();
	$lib_physical_condition->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$lib_physical_condition_delete->LoadRowValues($lib_physical_condition_delete->Recordset);

	// Render row
	$lib_physical_condition_delete->RenderRow();
?>
	<tr<?php echo $lib_physical_condition->RowAttributes() ?>>
<?php if ($lib_physical_condition->physconditionID->Visible) { // physconditionID ?>
		<td<?php echo $lib_physical_condition->physconditionID->CellAttributes() ?>>
<span id="el<?php echo $lib_physical_condition_delete->RowCnt ?>_lib_physical_condition_physconditionID" class="control-group lib_physical_condition_physconditionID">
<span<?php echo $lib_physical_condition->physconditionID->ViewAttributes() ?>>
<?php echo $lib_physical_condition->physconditionID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($lib_physical_condition->physconditionName->Visible) { // physconditionName ?>
		<td<?php echo $lib_physical_condition->physconditionName->CellAttributes() ?>>
<span id="el<?php echo $lib_physical_condition_delete->RowCnt ?>_lib_physical_condition_physconditionName" class="control-group lib_physical_condition_physconditionName">
<span<?php echo $lib_physical_condition->physconditionName->ViewAttributes() ?>>
<?php echo $lib_physical_condition->physconditionName->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($lib_physical_condition->created_by->Visible) { // created_by ?>
		<td<?php echo $lib_physical_condition->created_by->CellAttributes() ?>>
<span id="el<?php echo $lib_physical_condition_delete->RowCnt ?>_lib_physical_condition_created_by" class="control-group lib_physical_condition_created_by">
<span<?php echo $lib_physical_condition->created_by->ViewAttributes() ?>>
<?php echo $lib_physical_condition->created_by->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($lib_physical_condition->date_created->Visible) { // date_created ?>
		<td<?php echo $lib_physical_condition->date_created->CellAttributes() ?>>
<span id="el<?php echo $lib_physical_condition_delete->RowCnt ?>_lib_physical_condition_date_created" class="control-group lib_physical_condition_date_created">
<span<?php echo $lib_physical_condition->date_created->ViewAttributes() ?>>
<?php echo $lib_physical_condition->date_created->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($lib_physical_condition->modified_by->Visible) { // modified_by ?>
		<td<?php echo $lib_physical_condition->modified_by->CellAttributes() ?>>
<span id="el<?php echo $lib_physical_condition_delete->RowCnt ?>_lib_physical_condition_modified_by" class="control-group lib_physical_condition_modified_by">
<span<?php echo $lib_physical_condition->modified_by->ViewAttributes() ?>>
<?php echo $lib_physical_condition->modified_by->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($lib_physical_condition->date_modified->Visible) { // date_modified ?>
		<td<?php echo $lib_physical_condition->date_modified->CellAttributes() ?>>
<span id="el<?php echo $lib_physical_condition_delete->RowCnt ?>_lib_physical_condition_date_modified" class="control-group lib_physical_condition_date_modified">
<span<?php echo $lib_physical_condition->date_modified->ViewAttributes() ?>>
<?php echo $lib_physical_condition->date_modified->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($lib_physical_condition->DELETED->Visible) { // DELETED ?>
		<td<?php echo $lib_physical_condition->DELETED->CellAttributes() ?>>
<span id="el<?php echo $lib_physical_condition_delete->RowCnt ?>_lib_physical_condition_DELETED" class="control-group lib_physical_condition_DELETED">
<span<?php echo $lib_physical_condition->DELETED->ViewAttributes() ?>>
<?php echo $lib_physical_condition->DELETED->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$lib_physical_condition_delete->Recordset->MoveNext();
}
$lib_physical_condition_delete->Recordset->Close();
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
flib_physical_conditiondelete.Init();
</script>
<?php
$lib_physical_condition_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$lib_physical_condition_delete->Page_Terminate();
?>
