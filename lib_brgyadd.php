<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "lib_brgyinfo.php" ?>
<?php include_once "tbl_userinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$lib_brgy_add = NULL; // Initialize page object first

class clib_brgy_add extends clib_brgy {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Table name
	var $TableName = 'lib_brgy';

	// Page object name
	var $PageObjName = 'lib_brgy_add';

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
	var $AuditTrailOnAdd = TRUE;

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

		// Table object (lib_brgy)
		if (!isset($GLOBALS["lib_brgy"])) {
			$GLOBALS["lib_brgy"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["lib_brgy"];
		}

		// Table object (tbl_user)
		if (!isset($GLOBALS['tbl_user'])) $GLOBALS['tbl_user'] = new ctbl_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'lib_brgy', TRUE);

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
		if (!$Security->CanAdd()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate("lib_brgylist.php");
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

		// Create form object
		$objForm = new cFormObj();
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
	var $DbMasterFilter = "";
	var $DbDetailFilter = "";
	var $Priv = 0;
	var $OldRecordset;
	var $CopyRecord;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;

		// Process form if post back
		if (@$_POST["a_add"] <> "") {
			$this->CurrentAction = $_POST["a_add"]; // Get form action
			$this->CopyRecord = $this->LoadOldRecord(); // Load old recordset
			$this->LoadFormValues(); // Load form values
		} else { // Not post back

			// Load key values from QueryString
			$this->CopyRecord = TRUE;
			if (@$_GET["brgy_code"] != "") {
				$this->brgy_code->setQueryStringValue($_GET["brgy_code"]);
				$this->setKey("brgy_code", $this->brgy_code->CurrentValue); // Set up key
			} else {
				$this->setKey("brgy_code", ""); // Clear key
				$this->CopyRecord = FALSE;
			}
			if ($this->CopyRecord) {
				$this->CurrentAction = "C"; // Copy record
			} else {
				$this->CurrentAction = "I"; // Display blank record
				$this->LoadDefaultValues(); // Load default values
			}
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Validate form if post back
		if (@$_POST["a_add"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = "I"; // Form error, reset action
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues(); // Restore form values
				$this->setFailureMessage($gsFormError);
			}
		}

		// Perform action based on action code
		switch ($this->CurrentAction) {
			case "I": // Blank record, no action required
				break;
			case "C": // Copy an existing record
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("lib_brgylist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "lib_brgyview.php")
						$sReturnUrl = $this->GetViewUrl(); // View paging, return to view page with keyurl directly
					$this->Page_Terminate($sReturnUrl); // Clean up and return
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Add failed, restore form values
				}
		}

		// Render row based on row type
		$this->RowType = EW_ROWTYPE_ADD;  // Render add type

		// Render row
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Get upload files
	function GetUploadFiles() {
		global $objForm;

		// Get upload data
	}

	// Load default values
	function LoadDefaultValues() {
		$this->brgy_name->CurrentValue = NULL;
		$this->brgy_name->OldValue = $this->brgy_name->CurrentValue;
		$this->city_code->CurrentValue = NULL;
		$this->city_code->OldValue = $this->city_code->CurrentValue;
		$this->brgy_code->CurrentValue = NULL;
		$this->brgy_code->OldValue = $this->brgy_code->CurrentValue;
		$this->district_no->CurrentValue = NULL;
		$this->district_no->OldValue = $this->district_no->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->brgy_name->FldIsDetailKey) {
			$this->brgy_name->setFormValue($objForm->GetValue("x_brgy_name"));
		}
		if (!$this->city_code->FldIsDetailKey) {
			$this->city_code->setFormValue($objForm->GetValue("x_city_code"));
		}
		if (!$this->brgy_code->FldIsDetailKey) {
			$this->brgy_code->setFormValue($objForm->GetValue("x_brgy_code"));
		}
		if (!$this->district_no->FldIsDetailKey) {
			$this->district_no->setFormValue($objForm->GetValue("x_district_no"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->brgy_name->CurrentValue = $this->brgy_name->FormValue;
		$this->city_code->CurrentValue = $this->city_code->FormValue;
		$this->brgy_code->CurrentValue = $this->brgy_code->FormValue;
		$this->district_no->CurrentValue = $this->district_no->FormValue;
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
		$this->brgy_name->setDbValue($rs->fields('brgy_name'));
		$this->city_code->setDbValue($rs->fields('city_code'));
		$this->brgy_code->setDbValue($rs->fields('brgy_code'));
		$this->district_no->setDbValue($rs->fields('district_no'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->brgy_name->DbValue = $row['brgy_name'];
		$this->city_code->DbValue = $row['city_code'];
		$this->brgy_code->DbValue = $row['brgy_code'];
		$this->district_no->DbValue = $row['district_no'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("brgy_code")) <> "")
			$this->brgy_code->CurrentValue = $this->getKey("brgy_code"); // brgy_code
		else
			$bValidKey = FALSE;

		// Load old recordset
		if ($bValidKey) {
			$this->CurrentFilter = $this->KeyFilter();
			$sSql = $this->SQL();
			$this->OldRecordset = ew_LoadRecordset($sSql);
			$this->LoadRowValues($this->OldRecordset); // Load row values
		} else {
			$this->OldRecordset = NULL;
		}
		return $bValidKey;
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// brgy_name
		// city_code
		// brgy_code
		// district_no

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// brgy_name
			$this->brgy_name->ViewValue = $this->brgy_name->CurrentValue;
			$this->brgy_name->ViewCustomAttributes = "";

			// city_code
			$this->city_code->ViewValue = $this->city_code->CurrentValue;
			$this->city_code->ViewCustomAttributes = "";

			// brgy_code
			$this->brgy_code->ViewValue = $this->brgy_code->CurrentValue;
			$this->brgy_code->ViewCustomAttributes = "";

			// district_no
			$this->district_no->ViewValue = $this->district_no->CurrentValue;
			$this->district_no->ViewCustomAttributes = "";

			// brgy_name
			$this->brgy_name->LinkCustomAttributes = "";
			$this->brgy_name->HrefValue = "";
			$this->brgy_name->TooltipValue = "";

			// city_code
			$this->city_code->LinkCustomAttributes = "";
			$this->city_code->HrefValue = "";
			$this->city_code->TooltipValue = "";

			// brgy_code
			$this->brgy_code->LinkCustomAttributes = "";
			$this->brgy_code->HrefValue = "";
			$this->brgy_code->TooltipValue = "";

			// district_no
			$this->district_no->LinkCustomAttributes = "";
			$this->district_no->HrefValue = "";
			$this->district_no->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// brgy_name
			$this->brgy_name->EditCustomAttributes = "";
			$this->brgy_name->EditValue = ew_HtmlEncode($this->brgy_name->CurrentValue);
			$this->brgy_name->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->brgy_name->FldCaption()));

			// city_code
			$this->city_code->EditCustomAttributes = "";
			$this->city_code->EditValue = ew_HtmlEncode($this->city_code->CurrentValue);
			$this->city_code->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->city_code->FldCaption()));

			// brgy_code
			$this->brgy_code->EditCustomAttributes = "";
			$this->brgy_code->EditValue = ew_HtmlEncode($this->brgy_code->CurrentValue);
			$this->brgy_code->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->brgy_code->FldCaption()));

			// district_no
			$this->district_no->EditCustomAttributes = "";
			$this->district_no->EditValue = ew_HtmlEncode($this->district_no->CurrentValue);
			$this->district_no->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->district_no->FldCaption()));

			// Edit refer script
			// brgy_name

			$this->brgy_name->HrefValue = "";

			// city_code
			$this->city_code->HrefValue = "";

			// brgy_code
			$this->brgy_code->HrefValue = "";

			// district_no
			$this->district_no->HrefValue = "";
		}
		if ($this->RowType == EW_ROWTYPE_ADD ||
			$this->RowType == EW_ROWTYPE_EDIT ||
			$this->RowType == EW_ROWTYPE_SEARCH) { // Add / Edit / Search row
			$this->SetupFieldTitles();
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Validate form
	function ValidateForm() {
		global $Language, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return ($gsFormError == "");
		if (!$this->brgy_name->FldIsDetailKey && !is_null($this->brgy_name->FormValue) && $this->brgy_name->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->brgy_name->FldCaption());
		}
		if (!$this->city_code->FldIsDetailKey && !is_null($this->city_code->FormValue) && $this->city_code->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->city_code->FldCaption());
		}
		if (!ew_CheckInteger($this->city_code->FormValue)) {
			ew_AddMessage($gsFormError, $this->city_code->FldErrMsg());
		}
		if (!$this->brgy_code->FldIsDetailKey && !is_null($this->brgy_code->FormValue) && $this->brgy_code->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->brgy_code->FldCaption());
		}
		if (!ew_CheckInteger($this->brgy_code->FormValue)) {
			ew_AddMessage($gsFormError, $this->brgy_code->FldErrMsg());
		}
		if (!ew_CheckInteger($this->district_no->FormValue)) {
			ew_AddMessage($gsFormError, $this->district_no->FldErrMsg());
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsFormError, $sFormCustomError);
		}
		return $ValidateForm;
	}

	// Add record
	function AddRow($rsold = NULL) {
		global $conn, $Language, $Security;

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
		}
		$rsnew = array();

		// brgy_name
		$this->brgy_name->SetDbValueDef($rsnew, $this->brgy_name->CurrentValue, "", FALSE);

		// city_code
		$this->city_code->SetDbValueDef($rsnew, $this->city_code->CurrentValue, 0, FALSE);

		// brgy_code
		$this->brgy_code->SetDbValueDef($rsnew, $this->brgy_code->CurrentValue, 0, FALSE);

		// district_no
		$this->district_no->SetDbValueDef($rsnew, $this->district_no->CurrentValue, NULL, FALSE);

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);

		// Check if key value entered
		if ($bInsertRow && $this->ValidateKey && $this->brgy_code->CurrentValue == "" && $this->brgy_code->getSessionValue() == "") {
			$this->setFailureMessage($Language->Phrase("InvalidKeyValue"));
			$bInsertRow = FALSE;
		}

		// Check for duplicate key
		if ($bInsertRow && $this->ValidateKey) {
			$sFilter = $this->KeyFilter();
			$rsChk = $this->LoadRs($sFilter);
			if ($rsChk && !$rsChk->EOF) {
				$sKeyErrMsg = str_replace("%f", $sFilter, $Language->Phrase("DupKey"));
				$this->setFailureMessage($sKeyErrMsg);
				$rsChk->Close();
				$bInsertRow = FALSE;
			}
		}
		if ($bInsertRow) {
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$AddRow = $this->Insert($rsnew);
			$conn->raiseErrorFn = '';
			if ($AddRow) {
			}
		} else {
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("InsertCancelled"));
			}
			$AddRow = FALSE;
		}

		// Get insert id if necessary
		if ($AddRow) {
		}
		if ($AddRow) {

			// Call Row Inserted event
			$rs = ($rsold == NULL) ? NULL : $rsold->fields;
			$this->Row_Inserted($rs, $rsnew);
			$this->WriteAuditTrailOnAdd($rsnew);
		}
		return $AddRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$PageCaption = $this->TableCaption();
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "lib_brgylist.php", $this->TableVar);
		$PageCaption = ($this->CurrentAction == "C") ? $Language->Phrase("Copy") : $Language->Phrase("Add");
		$Breadcrumb->Add("add", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", ew_CurrentUrl(), $this->TableVar);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'lib_brgy';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (add page)
	function WriteAuditTrailOnAdd(&$rs) {
		if (!$this->AuditTrailOnAdd) return;
		$table = 'lib_brgy';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['brgy_code'];

		// Write Audit Trail
		$dt = ew_StdCurrentDateTime();
		$id = ew_ScriptName();
	  $usr = CurrentUserID();
		foreach (array_keys($rs) as $fldname) {
			if ($this->fields[$fldname]->FldDataType <> EW_DATATYPE_BLOB) { // Ignore BLOB fields
				if ($this->fields[$fldname]->FldDataType == EW_DATATYPE_MEMO) {
					if (EW_AUDIT_TRAIL_TO_DATABASE)
						$newvalue = $rs[$fldname];
					else
						$newvalue = "[MEMO]"; // Memo Field
				} elseif ($this->fields[$fldname]->FldDataType == EW_DATATYPE_XML) {
					$newvalue = "[XML]"; // XML Field
				} else {
					$newvalue = $rs[$fldname];
				}
				ew_WriteAuditTrail("log", $dt, $id, $usr, "A", $table, $fldname, $key, "", $newvalue);
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

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($lib_brgy_add)) $lib_brgy_add = new clib_brgy_add();

// Page init
$lib_brgy_add->Page_Init();

// Page main
$lib_brgy_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$lib_brgy_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var lib_brgy_add = new ew_Page("lib_brgy_add");
lib_brgy_add.PageID = "add"; // Page ID
var EW_PAGE_ID = lib_brgy_add.PageID; // For backward compatibility

// Form object
var flib_brgyadd = new ew_Form("flib_brgyadd");

// Validate form
flib_brgyadd.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_brgy_name");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($lib_brgy->brgy_name->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_city_code");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($lib_brgy->city_code->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_city_code");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($lib_brgy->city_code->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_brgy_code");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($lib_brgy->brgy_code->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_brgy_code");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($lib_brgy->brgy_code->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_district_no");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($lib_brgy->district_no->FldErrMsg()) ?>");

			// Set up row object
			ew_ElementsToRow(fobj);

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
	}

	// Process detail forms
	var dfs = $fobj.find("input[name='detailpage']").get();
	for (var i = 0; i < dfs.length; i++) {
		var df = dfs[i], val = df.value;
		if (val && ewForms[val])
			if (!ewForms[val].Validate())
				return false;
	}
	return true;
}

// Form_CustomValidate event
flib_brgyadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
flib_brgyadd.ValidateRequired = true;
<?php } else { ?>
flib_brgyadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php //$Breadcrumb->Render(); ?>
<?php $lib_brgy_add->ShowPageHeader(); ?>
<?php
$lib_brgy_add->ShowMessage();
?>
<form name="flib_brgyadd" id="flib_brgyadd" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="lib_brgy">
<input type="hidden" name="a_add" id="a_add" value="A">
<table cellspacing="0" class="ewGrid"><tr><td>
<table id="tbl_lib_brgyadd" class="table table-bordered table-striped">
<?php if ($lib_brgy->brgy_name->Visible) { // brgy_name ?>
	<tr id="r_brgy_name">
		<td><span id="elh_lib_brgy_brgy_name"><?php echo $lib_brgy->brgy_name->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $lib_brgy->brgy_name->CellAttributes() ?>>
<span id="el_lib_brgy_brgy_name" class="control-group">
<input type="text" data-field="x_brgy_name" name="x_brgy_name" id="x_brgy_name" size="30" maxlength="80" placeholder="<?php echo $lib_brgy->brgy_name->PlaceHolder ?>" value="<?php echo $lib_brgy->brgy_name->EditValue ?>"<?php echo $lib_brgy->brgy_name->EditAttributes() ?>>
</span>
<?php echo $lib_brgy->brgy_name->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($lib_brgy->city_code->Visible) { // city_code ?>
	<tr id="r_city_code">
		<td><span id="elh_lib_brgy_city_code"><?php echo $lib_brgy->city_code->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $lib_brgy->city_code->CellAttributes() ?>>
<span id="el_lib_brgy_city_code" class="control-group">
<input type="text" data-field="x_city_code" name="x_city_code" id="x_city_code" size="30" placeholder="<?php echo $lib_brgy->city_code->PlaceHolder ?>" value="<?php echo $lib_brgy->city_code->EditValue ?>"<?php echo $lib_brgy->city_code->EditAttributes() ?>>
</span>
<?php echo $lib_brgy->city_code->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($lib_brgy->brgy_code->Visible) { // brgy_code ?>
	<tr id="r_brgy_code">
		<td><span id="elh_lib_brgy_brgy_code"><?php echo $lib_brgy->brgy_code->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $lib_brgy->brgy_code->CellAttributes() ?>>
<span id="el_lib_brgy_brgy_code" class="control-group">
<input type="text" data-field="x_brgy_code" name="x_brgy_code" id="x_brgy_code" size="30" placeholder="<?php echo $lib_brgy->brgy_code->PlaceHolder ?>" value="<?php echo $lib_brgy->brgy_code->EditValue ?>"<?php echo $lib_brgy->brgy_code->EditAttributes() ?>>
</span>
<?php echo $lib_brgy->brgy_code->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($lib_brgy->district_no->Visible) { // district_no ?>
	<tr id="r_district_no">
		<td><span id="elh_lib_brgy_district_no"><?php echo $lib_brgy->district_no->FldCaption() ?></span></td>
		<td<?php echo $lib_brgy->district_no->CellAttributes() ?>>
<span id="el_lib_brgy_district_no" class="control-group">
<input type="text" data-field="x_district_no" name="x_district_no" id="x_district_no" size="30" placeholder="<?php echo $lib_brgy->district_no->PlaceHolder ?>" value="<?php echo $lib_brgy->district_no->EditValue ?>"<?php echo $lib_brgy->district_no->EditAttributes() ?>>
</span>
<?php echo $lib_brgy->district_no->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
</form>
<script type="text/javascript">
flib_brgyadd.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$lib_brgy_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$lib_brgy_add->Page_Terminate();
?>
