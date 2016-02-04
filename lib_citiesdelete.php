<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "lib_citiesinfo.php" ?>
<?php include_once "tbl_userinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$lib_cities_delete = NULL; // Initialize page object first

class clib_cities_delete extends clib_cities {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Table name
	var $TableName = 'lib_cities';

	// Page object name
	var $PageObjName = 'lib_cities_delete';

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

		// Table object (lib_cities)
		if (!isset($GLOBALS["lib_cities"])) {
			$GLOBALS["lib_cities"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["lib_cities"];
		}

		// Table object (tbl_user)
		if (!isset($GLOBALS['tbl_user'])) $GLOBALS['tbl_user'] = new ctbl_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'delete', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'lib_cities', TRUE);

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
			$this->Page_Terminate("lib_citieslist.php");
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
			$this->Page_Terminate("lib_citieslist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in lib_cities class, lib_citiesinfo.php

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
		$this->city_code->setDbValue($rs->fields('city_code'));
		$this->city_name->setDbValue($rs->fields('city_name'));
		$this->prov_code->setDbValue($rs->fields('prov_code'));
		$this->district_no->setDbValue($rs->fields('district_no'));
		$this->district_name->setDbValue($rs->fields('district_name'));
		$this->is_Urban->setDbValue($rs->fields('is_Urban'));
		$this->locked->setDbValue($rs->fields('locked'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->city_code->DbValue = $row['city_code'];
		$this->city_name->DbValue = $row['city_name'];
		$this->prov_code->DbValue = $row['prov_code'];
		$this->district_no->DbValue = $row['district_no'];
		$this->district_name->DbValue = $row['district_name'];
		$this->is_Urban->DbValue = $row['is_Urban'];
		$this->locked->DbValue = $row['locked'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// city_code
		// city_name
		// prov_code
		// district_no
		// district_name
		// is_Urban
		// locked

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// city_code
			$this->city_code->ViewValue = $this->city_code->CurrentValue;
			$this->city_code->ViewCustomAttributes = "";

			// city_name
			$this->city_name->ViewValue = $this->city_name->CurrentValue;
			$this->city_name->ViewCustomAttributes = "";

			// prov_code
			$this->prov_code->ViewValue = $this->prov_code->CurrentValue;
			$this->prov_code->ViewCustomAttributes = "";

			// district_no
			$this->district_no->ViewValue = $this->district_no->CurrentValue;
			$this->district_no->ViewCustomAttributes = "";

			// district_name
			$this->district_name->ViewValue = $this->district_name->CurrentValue;
			$this->district_name->ViewCustomAttributes = "";

			// is_Urban
			$this->is_Urban->ViewValue = $this->is_Urban->CurrentValue;
			$this->is_Urban->ViewCustomAttributes = "";

			// locked
			$this->locked->ViewValue = $this->locked->CurrentValue;
			$this->locked->ViewCustomAttributes = "";

			// city_code
			$this->city_code->LinkCustomAttributes = "";
			$this->city_code->HrefValue = "";
			$this->city_code->TooltipValue = "";

			// city_name
			$this->city_name->LinkCustomAttributes = "";
			$this->city_name->HrefValue = "";
			$this->city_name->TooltipValue = "";

			// prov_code
			$this->prov_code->LinkCustomAttributes = "";
			$this->prov_code->HrefValue = "";
			$this->prov_code->TooltipValue = "";

			// district_no
			$this->district_no->LinkCustomAttributes = "";
			$this->district_no->HrefValue = "";
			$this->district_no->TooltipValue = "";

			// district_name
			$this->district_name->LinkCustomAttributes = "";
			$this->district_name->HrefValue = "";
			$this->district_name->TooltipValue = "";

			// is_Urban
			$this->is_Urban->LinkCustomAttributes = "";
			$this->is_Urban->HrefValue = "";
			$this->is_Urban->TooltipValue = "";

			// locked
			$this->locked->LinkCustomAttributes = "";
			$this->locked->HrefValue = "";
			$this->locked->TooltipValue = "";
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
				$sThisKey .= $row['city_code'];
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
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "lib_citieslist.php", $this->TableVar);
		$PageCaption = $Language->Phrase("delete");
		$Breadcrumb->Add("delete", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", ew_CurrentUrl(), $this->TableVar);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'lib_cities';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (delete page)
	function WriteAuditTrailOnDelete(&$rs) {
		if (!$this->AuditTrailOnDelete) return;
		$table = 'lib_cities';

		// Get key value
		$key = "";
		if ($key <> "")
			$key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['city_code'];

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
if (!isset($lib_cities_delete)) $lib_cities_delete = new clib_cities_delete();

// Page init
$lib_cities_delete->Page_Init();

// Page main
$lib_cities_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$lib_cities_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var lib_cities_delete = new ew_Page("lib_cities_delete");
lib_cities_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = lib_cities_delete.PageID; // For backward compatibility

// Form object
var flib_citiesdelete = new ew_Form("flib_citiesdelete");

// Form_CustomValidate event
flib_citiesdelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
flib_citiesdelete.ValidateRequired = true;
<?php } else { ?>
flib_citiesdelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($lib_cities_delete->Recordset = $lib_cities_delete->LoadRecordset())
	$lib_cities_deleteTotalRecs = $lib_cities_delete->Recordset->RecordCount(); // Get record count
if ($lib_cities_deleteTotalRecs <= 0) { // No record found, exit
	if ($lib_cities_delete->Recordset)
		$lib_cities_delete->Recordset->Close();
	$lib_cities_delete->Page_Terminate("lib_citieslist.php"); // Return to list
}
?>
<?php //$Breadcrumb->Render(); ?>
<?php $lib_cities_delete->ShowPageHeader(); ?>
<?php
$lib_cities_delete->ShowMessage();
?>
<form name="flib_citiesdelete" id="flib_citiesdelete" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="lib_cities">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($lib_cities_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_lib_citiesdelete" class="ewTable ewTableSeparate">
<?php echo $lib_cities->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($lib_cities->city_code->Visible) { // city_code ?>
		<td><span id="elh_lib_cities_city_code" class="lib_cities_city_code"><?php echo $lib_cities->city_code->FldCaption() ?></span></td>
<?php } ?>
<?php if ($lib_cities->city_name->Visible) { // city_name ?>
		<td><span id="elh_lib_cities_city_name" class="lib_cities_city_name"><?php echo $lib_cities->city_name->FldCaption() ?></span></td>
<?php } ?>
<?php if ($lib_cities->prov_code->Visible) { // prov_code ?>
		<td><span id="elh_lib_cities_prov_code" class="lib_cities_prov_code"><?php echo $lib_cities->prov_code->FldCaption() ?></span></td>
<?php } ?>
<?php if ($lib_cities->district_no->Visible) { // district_no ?>
		<td><span id="elh_lib_cities_district_no" class="lib_cities_district_no"><?php echo $lib_cities->district_no->FldCaption() ?></span></td>
<?php } ?>
<?php if ($lib_cities->district_name->Visible) { // district_name ?>
		<td><span id="elh_lib_cities_district_name" class="lib_cities_district_name"><?php echo $lib_cities->district_name->FldCaption() ?></span></td>
<?php } ?>
<?php if ($lib_cities->is_Urban->Visible) { // is_Urban ?>
		<td><span id="elh_lib_cities_is_Urban" class="lib_cities_is_Urban"><?php echo $lib_cities->is_Urban->FldCaption() ?></span></td>
<?php } ?>
<?php if ($lib_cities->locked->Visible) { // locked ?>
		<td><span id="elh_lib_cities_locked" class="lib_cities_locked"><?php echo $lib_cities->locked->FldCaption() ?></span></td>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$lib_cities_delete->RecCnt = 0;
$i = 0;
while (!$lib_cities_delete->Recordset->EOF) {
	$lib_cities_delete->RecCnt++;
	$lib_cities_delete->RowCnt++;

	// Set row properties
	$lib_cities->ResetAttrs();
	$lib_cities->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$lib_cities_delete->LoadRowValues($lib_cities_delete->Recordset);

	// Render row
	$lib_cities_delete->RenderRow();
?>
	<tr<?php echo $lib_cities->RowAttributes() ?>>
<?php if ($lib_cities->city_code->Visible) { // city_code ?>
		<td<?php echo $lib_cities->city_code->CellAttributes() ?>>
<span id="el<?php echo $lib_cities_delete->RowCnt ?>_lib_cities_city_code" class="control-group lib_cities_city_code">
<span<?php echo $lib_cities->city_code->ViewAttributes() ?>>
<?php echo $lib_cities->city_code->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($lib_cities->city_name->Visible) { // city_name ?>
		<td<?php echo $lib_cities->city_name->CellAttributes() ?>>
<span id="el<?php echo $lib_cities_delete->RowCnt ?>_lib_cities_city_name" class="control-group lib_cities_city_name">
<span<?php echo $lib_cities->city_name->ViewAttributes() ?>>
<?php echo $lib_cities->city_name->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($lib_cities->prov_code->Visible) { // prov_code ?>
		<td<?php echo $lib_cities->prov_code->CellAttributes() ?>>
<span id="el<?php echo $lib_cities_delete->RowCnt ?>_lib_cities_prov_code" class="control-group lib_cities_prov_code">
<span<?php echo $lib_cities->prov_code->ViewAttributes() ?>>
<?php echo $lib_cities->prov_code->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($lib_cities->district_no->Visible) { // district_no ?>
		<td<?php echo $lib_cities->district_no->CellAttributes() ?>>
<span id="el<?php echo $lib_cities_delete->RowCnt ?>_lib_cities_district_no" class="control-group lib_cities_district_no">
<span<?php echo $lib_cities->district_no->ViewAttributes() ?>>
<?php echo $lib_cities->district_no->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($lib_cities->district_name->Visible) { // district_name ?>
		<td<?php echo $lib_cities->district_name->CellAttributes() ?>>
<span id="el<?php echo $lib_cities_delete->RowCnt ?>_lib_cities_district_name" class="control-group lib_cities_district_name">
<span<?php echo $lib_cities->district_name->ViewAttributes() ?>>
<?php echo $lib_cities->district_name->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($lib_cities->is_Urban->Visible) { // is_Urban ?>
		<td<?php echo $lib_cities->is_Urban->CellAttributes() ?>>
<span id="el<?php echo $lib_cities_delete->RowCnt ?>_lib_cities_is_Urban" class="control-group lib_cities_is_Urban">
<span<?php echo $lib_cities->is_Urban->ViewAttributes() ?>>
<?php echo $lib_cities->is_Urban->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($lib_cities->locked->Visible) { // locked ?>
		<td<?php echo $lib_cities->locked->CellAttributes() ?>>
<span id="el<?php echo $lib_cities_delete->RowCnt ?>_lib_cities_locked" class="control-group lib_cities_locked">
<span<?php echo $lib_cities->locked->ViewAttributes() ?>>
<?php echo $lib_cities->locked->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$lib_cities_delete->Recordset->MoveNext();
}
$lib_cities_delete->Recordset->Close();
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
flib_citiesdelete.Init();
</script>
<?php
$lib_cities_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$lib_cities_delete->Page_Terminate();
?>