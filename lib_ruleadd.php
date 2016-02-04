<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "lib_ruleinfo.php" ?>
<?php include_once "tbl_userinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$lib_rule_add = NULL; // Initialize page object first

class clib_rule_add extends clib_rule {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Table name
	var $TableName = 'lib_rule';

	// Page object name
	var $PageObjName = 'lib_rule_add';

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

		// Table object (lib_rule)
		if (!isset($GLOBALS["lib_rule"])) {
			$GLOBALS["lib_rule"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["lib_rule"];
		}

		// Table object (tbl_user)
		if (!isset($GLOBALS['tbl_user'])) $GLOBALS['tbl_user'] = new ctbl_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'lib_rule', TRUE);

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
			$this->Page_Terminate("lib_rulelist.php");
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
			if (@$_GET["ruleID"] != "") {
				$this->ruleID->setQueryStringValue($_GET["ruleID"]);
				$this->setKey("ruleID", $this->ruleID->CurrentValue); // Set up key
			} else {
				$this->setKey("ruleID", ""); // Clear key
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
					$this->Page_Terminate("lib_rulelist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "lib_ruleview.php")
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
		$this->rule_age->CurrentValue = NULL;
		$this->rule_age->OldValue = $this->rule_age->CurrentValue;
		$this->rule_affiliation->CurrentValue = NULL;
		$this->rule_affiliation->OldValue = $this->rule_affiliation->CurrentValue;
		$this->rule_active->CurrentValue = NULL;
		$this->rule_active->OldValue = $this->rule_active->CurrentValue;
		$this->created_by->CurrentValue = NULL;
		$this->created_by->OldValue = $this->created_by->CurrentValue;
		$this->date_created->CurrentValue = NULL;
		$this->date_created->OldValue = $this->date_created->CurrentValue;
		$this->modified_by->CurrentValue = NULL;
		$this->modified_by->OldValue = $this->modified_by->CurrentValue;
		$this->date_modified->CurrentValue = NULL;
		$this->date_modified->OldValue = $this->date_modified->CurrentValue;
		$this->DELETED->CurrentValue = 0;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->rule_age->FldIsDetailKey) {
			$this->rule_age->setFormValue($objForm->GetValue("x_rule_age"));
		}
		if (!$this->rule_affiliation->FldIsDetailKey) {
			$this->rule_affiliation->setFormValue($objForm->GetValue("x_rule_affiliation"));
		}
		if (!$this->rule_active->FldIsDetailKey) {
			$this->rule_active->setFormValue($objForm->GetValue("x_rule_active"));
		}
		if (!$this->created_by->FldIsDetailKey) {
			$this->created_by->setFormValue($objForm->GetValue("x_created_by"));
		}
		if (!$this->date_created->FldIsDetailKey) {
			$this->date_created->setFormValue($objForm->GetValue("x_date_created"));
			$this->date_created->CurrentValue = ew_UnFormatDateTime($this->date_created->CurrentValue, 6);
		}
		if (!$this->modified_by->FldIsDetailKey) {
			$this->modified_by->setFormValue($objForm->GetValue("x_modified_by"));
		}
		if (!$this->date_modified->FldIsDetailKey) {
			$this->date_modified->setFormValue($objForm->GetValue("x_date_modified"));
			$this->date_modified->CurrentValue = ew_UnFormatDateTime($this->date_modified->CurrentValue, 6);
		}
		if (!$this->DELETED->FldIsDetailKey) {
			$this->DELETED->setFormValue($objForm->GetValue("x_DELETED"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->rule_age->CurrentValue = $this->rule_age->FormValue;
		$this->rule_affiliation->CurrentValue = $this->rule_affiliation->FormValue;
		$this->rule_active->CurrentValue = $this->rule_active->FormValue;
		$this->created_by->CurrentValue = $this->created_by->FormValue;
		$this->date_created->CurrentValue = $this->date_created->FormValue;
		$this->date_created->CurrentValue = ew_UnFormatDateTime($this->date_created->CurrentValue, 6);
		$this->modified_by->CurrentValue = $this->modified_by->FormValue;
		$this->date_modified->CurrentValue = $this->date_modified->FormValue;
		$this->date_modified->CurrentValue = ew_UnFormatDateTime($this->date_modified->CurrentValue, 6);
		$this->DELETED->CurrentValue = $this->DELETED->FormValue;
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
		$this->ruleID->setDbValue($rs->fields('ruleID'));
		$this->rule_age->setDbValue($rs->fields('rule_age'));
		$this->rule_affiliation->setDbValue($rs->fields('rule_affiliation'));
		$this->rule_active->setDbValue($rs->fields('rule_active'));
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
		$this->ruleID->DbValue = $row['ruleID'];
		$this->rule_age->DbValue = $row['rule_age'];
		$this->rule_affiliation->DbValue = $row['rule_affiliation'];
		$this->rule_active->DbValue = $row['rule_active'];
		$this->created_by->DbValue = $row['created_by'];
		$this->date_created->DbValue = $row['date_created'];
		$this->modified_by->DbValue = $row['modified_by'];
		$this->date_modified->DbValue = $row['date_modified'];
		$this->DELETED->DbValue = $row['DELETED'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("ruleID")) <> "")
			$this->ruleID->CurrentValue = $this->getKey("ruleID"); // ruleID
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
		// ruleID
		// rule_age
		// rule_affiliation
		// rule_active
		// created_by
		// date_created
		// modified_by
		// date_modified
		// DELETED

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// ruleID
			$this->ruleID->ViewValue = $this->ruleID->CurrentValue;
			$this->ruleID->ViewCustomAttributes = "";

			// rule_age
			$this->rule_age->ViewValue = $this->rule_age->CurrentValue;
			$this->rule_age->ViewCustomAttributes = "";

			// rule_affiliation
			$this->rule_affiliation->ViewValue = $this->rule_affiliation->CurrentValue;
			$this->rule_affiliation->ViewCustomAttributes = "";

			// rule_active
			$this->rule_active->ViewValue = $this->rule_active->CurrentValue;
			$this->rule_active->ViewCustomAttributes = "";

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

			// rule_age
			$this->rule_age->LinkCustomAttributes = "";
			$this->rule_age->HrefValue = "";
			$this->rule_age->TooltipValue = "";

			// rule_affiliation
			$this->rule_affiliation->LinkCustomAttributes = "";
			$this->rule_affiliation->HrefValue = "";
			$this->rule_affiliation->TooltipValue = "";

			// rule_active
			$this->rule_active->LinkCustomAttributes = "";
			$this->rule_active->HrefValue = "";
			$this->rule_active->TooltipValue = "";

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
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// rule_age
			$this->rule_age->EditCustomAttributes = "";
			$this->rule_age->EditValue = ew_HtmlEncode($this->rule_age->CurrentValue);
			$this->rule_age->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->rule_age->FldCaption()));

			// rule_affiliation
			$this->rule_affiliation->EditCustomAttributes = "";
			$this->rule_affiliation->EditValue = ew_HtmlEncode($this->rule_affiliation->CurrentValue);
			$this->rule_affiliation->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->rule_affiliation->FldCaption()));

			// rule_active
			$this->rule_active->EditCustomAttributes = "";
			$this->rule_active->EditValue = ew_HtmlEncode($this->rule_active->CurrentValue);
			$this->rule_active->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->rule_active->FldCaption()));

			// created_by
			$this->created_by->EditCustomAttributes = "";
			$this->created_by->EditValue = ew_HtmlEncode($this->created_by->CurrentValue);
			$this->created_by->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->created_by->FldCaption()));

			// date_created
			$this->date_created->EditCustomAttributes = "";
			$this->date_created->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->date_created->CurrentValue, 6));
			$this->date_created->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->date_created->FldCaption()));

			// modified_by
			$this->modified_by->EditCustomAttributes = "";
			$this->modified_by->EditValue = ew_HtmlEncode($this->modified_by->CurrentValue);
			$this->modified_by->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->modified_by->FldCaption()));

			// date_modified
			$this->date_modified->EditCustomAttributes = "";
			$this->date_modified->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->date_modified->CurrentValue, 6));
			$this->date_modified->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->date_modified->FldCaption()));

			// DELETED
			$this->DELETED->EditCustomAttributes = "";
			$this->DELETED->EditValue = ew_HtmlEncode($this->DELETED->CurrentValue);
			$this->DELETED->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->DELETED->FldCaption()));

			// Edit refer script
			// rule_age

			$this->rule_age->HrefValue = "";

			// rule_affiliation
			$this->rule_affiliation->HrefValue = "";

			// rule_active
			$this->rule_active->HrefValue = "";

			// created_by
			$this->created_by->HrefValue = "";

			// date_created
			$this->date_created->HrefValue = "";

			// modified_by
			$this->modified_by->HrefValue = "";

			// date_modified
			$this->date_modified->HrefValue = "";

			// DELETED
			$this->DELETED->HrefValue = "";
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
		if (!$this->rule_age->FldIsDetailKey && !is_null($this->rule_age->FormValue) && $this->rule_age->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->rule_age->FldCaption());
		}
		if (!ew_CheckInteger($this->rule_age->FormValue)) {
			ew_AddMessage($gsFormError, $this->rule_age->FldErrMsg());
		}
		if (!$this->rule_affiliation->FldIsDetailKey && !is_null($this->rule_affiliation->FormValue) && $this->rule_affiliation->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->rule_affiliation->FldCaption());
		}
		if (!ew_CheckInteger($this->rule_affiliation->FormValue)) {
			ew_AddMessage($gsFormError, $this->rule_affiliation->FldErrMsg());
		}
		if (!ew_CheckInteger($this->rule_active->FormValue)) {
			ew_AddMessage($gsFormError, $this->rule_active->FldErrMsg());
		}
		if (!ew_CheckInteger($this->created_by->FormValue)) {
			ew_AddMessage($gsFormError, $this->created_by->FldErrMsg());
		}
		if (!ew_CheckUSDate($this->date_created->FormValue)) {
			ew_AddMessage($gsFormError, $this->date_created->FldErrMsg());
		}
		if (!ew_CheckInteger($this->modified_by->FormValue)) {
			ew_AddMessage($gsFormError, $this->modified_by->FldErrMsg());
		}
		if (!ew_CheckUSDate($this->date_modified->FormValue)) {
			ew_AddMessage($gsFormError, $this->date_modified->FldErrMsg());
		}
		if (!$this->DELETED->FldIsDetailKey && !is_null($this->DELETED->FormValue) && $this->DELETED->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->DELETED->FldCaption());
		}
		if (!ew_CheckInteger($this->DELETED->FormValue)) {
			ew_AddMessage($gsFormError, $this->DELETED->FldErrMsg());
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
		if ($this->rule_active->CurrentValue <> "") { // Check field with unique index
			$sFilter = "(rule_active = " . ew_AdjustSql($this->rule_active->CurrentValue) . ")";
			$rsChk = $this->LoadRs($sFilter);
			if ($rsChk && !$rsChk->EOF) {
				$sIdxErrMsg = str_replace("%f", $this->rule_active->FldCaption(), $Language->Phrase("DupIndex"));
				$sIdxErrMsg = str_replace("%v", $this->rule_active->CurrentValue, $sIdxErrMsg);
				$this->setFailureMessage($sIdxErrMsg);
				$rsChk->Close();
				return FALSE;
			}
		}

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
		}
		$rsnew = array();

		// rule_age
		$this->rule_age->SetDbValueDef($rsnew, $this->rule_age->CurrentValue, 0, FALSE);

		// rule_affiliation
		$this->rule_affiliation->SetDbValueDef($rsnew, $this->rule_affiliation->CurrentValue, 0, FALSE);

		// rule_active
		$this->rule_active->SetDbValueDef($rsnew, $this->rule_active->CurrentValue, NULL, FALSE);

		// created_by
		$this->created_by->SetDbValueDef($rsnew, $this->created_by->CurrentValue, NULL, FALSE);

		// date_created
		$this->date_created->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->date_created->CurrentValue, 6), NULL, FALSE);

		// modified_by
		$this->modified_by->SetDbValueDef($rsnew, $this->modified_by->CurrentValue, NULL, FALSE);

		// date_modified
		$this->date_modified->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->date_modified->CurrentValue, 6), NULL, FALSE);

		// DELETED
		$this->DELETED->SetDbValueDef($rsnew, $this->DELETED->CurrentValue, 0, strval($this->DELETED->CurrentValue) == "");

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);
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
			$this->ruleID->setDbValue($conn->Insert_ID());
			$rsnew['ruleID'] = $this->ruleID->DbValue;
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
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "lib_rulelist.php", $this->TableVar);
		$PageCaption = ($this->CurrentAction == "C") ? $Language->Phrase("Copy") : $Language->Phrase("Add");
		$Breadcrumb->Add("add", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", ew_CurrentUrl(), $this->TableVar);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'lib_rule';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (add page)
	function WriteAuditTrailOnAdd(&$rs) {
		if (!$this->AuditTrailOnAdd) return;
		$table = 'lib_rule';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['ruleID'];

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
if (!isset($lib_rule_add)) $lib_rule_add = new clib_rule_add();

// Page init
$lib_rule_add->Page_Init();

// Page main
$lib_rule_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$lib_rule_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var lib_rule_add = new ew_Page("lib_rule_add");
lib_rule_add.PageID = "add"; // Page ID
var EW_PAGE_ID = lib_rule_add.PageID; // For backward compatibility

// Form object
var flib_ruleadd = new ew_Form("flib_ruleadd");

// Validate form
flib_ruleadd.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_rule_age");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($lib_rule->rule_age->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_rule_age");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($lib_rule->rule_age->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_rule_affiliation");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($lib_rule->rule_affiliation->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_rule_affiliation");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($lib_rule->rule_affiliation->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_rule_active");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($lib_rule->rule_active->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_created_by");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($lib_rule->created_by->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_date_created");
			if (elm && !ew_CheckUSDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($lib_rule->date_created->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_modified_by");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($lib_rule->modified_by->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_date_modified");
			if (elm && !ew_CheckUSDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($lib_rule->date_modified->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_DELETED");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($lib_rule->DELETED->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_DELETED");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($lib_rule->DELETED->FldErrMsg()) ?>");

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
flib_ruleadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
flib_ruleadd.ValidateRequired = true;
<?php } else { ?>
flib_ruleadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php //$Breadcrumb->Render(); ?>
<?php $lib_rule_add->ShowPageHeader(); ?>
<?php
$lib_rule_add->ShowMessage();
?>
<form name="flib_ruleadd" id="flib_ruleadd" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="lib_rule">
<input type="hidden" name="a_add" id="a_add" value="A">
<table cellspacing="0" class="ewGrid"><tr><td>
<table id="tbl_lib_ruleadd" class="table table-bordered table-striped">
<?php if ($lib_rule->rule_age->Visible) { // rule_age ?>
	<tr id="r_rule_age">
		<td><span id="elh_lib_rule_rule_age"><?php echo $lib_rule->rule_age->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $lib_rule->rule_age->CellAttributes() ?>>
<span id="el_lib_rule_rule_age" class="control-group">
<input type="text" data-field="x_rule_age" name="x_rule_age" id="x_rule_age" size="30" placeholder="<?php echo $lib_rule->rule_age->PlaceHolder ?>" value="<?php echo $lib_rule->rule_age->EditValue ?>"<?php echo $lib_rule->rule_age->EditAttributes() ?>>
</span>
<?php echo $lib_rule->rule_age->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($lib_rule->rule_affiliation->Visible) { // rule_affiliation ?>
	<tr id="r_rule_affiliation">
		<td><span id="elh_lib_rule_rule_affiliation"><?php echo $lib_rule->rule_affiliation->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $lib_rule->rule_affiliation->CellAttributes() ?>>
<span id="el_lib_rule_rule_affiliation" class="control-group">
<input type="text" data-field="x_rule_affiliation" name="x_rule_affiliation" id="x_rule_affiliation" size="30" placeholder="<?php echo $lib_rule->rule_affiliation->PlaceHolder ?>" value="<?php echo $lib_rule->rule_affiliation->EditValue ?>"<?php echo $lib_rule->rule_affiliation->EditAttributes() ?>>
</span>
<?php echo $lib_rule->rule_affiliation->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($lib_rule->rule_active->Visible) { // rule_active ?>
	<tr id="r_rule_active">
		<td><span id="elh_lib_rule_rule_active"><?php echo $lib_rule->rule_active->FldCaption() ?></span></td>
		<td<?php echo $lib_rule->rule_active->CellAttributes() ?>>
<span id="el_lib_rule_rule_active" class="control-group">
<input type="text" data-field="x_rule_active" name="x_rule_active" id="x_rule_active" size="30" placeholder="<?php echo $lib_rule->rule_active->PlaceHolder ?>" value="<?php echo $lib_rule->rule_active->EditValue ?>"<?php echo $lib_rule->rule_active->EditAttributes() ?>>
</span>
<?php echo $lib_rule->rule_active->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($lib_rule->created_by->Visible) { // created_by ?>
	<tr id="r_created_by">
		<td><span id="elh_lib_rule_created_by"><?php echo $lib_rule->created_by->FldCaption() ?></span></td>
		<td<?php echo $lib_rule->created_by->CellAttributes() ?>>
<span id="el_lib_rule_created_by" class="control-group">
<input type="text" data-field="x_created_by" name="x_created_by" id="x_created_by" size="30" placeholder="<?php echo $lib_rule->created_by->PlaceHolder ?>" value="<?php echo $lib_rule->created_by->EditValue ?>"<?php echo $lib_rule->created_by->EditAttributes() ?>>
</span>
<?php echo $lib_rule->created_by->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($lib_rule->date_created->Visible) { // date_created ?>
	<tr id="r_date_created">
		<td><span id="elh_lib_rule_date_created"><?php echo $lib_rule->date_created->FldCaption() ?></span></td>
		<td<?php echo $lib_rule->date_created->CellAttributes() ?>>
<span id="el_lib_rule_date_created" class="control-group">
<input type="text" data-field="x_date_created" name="x_date_created" id="x_date_created" placeholder="<?php echo $lib_rule->date_created->PlaceHolder ?>" value="<?php echo $lib_rule->date_created->EditValue ?>"<?php echo $lib_rule->date_created->EditAttributes() ?>>
</span>
<?php echo $lib_rule->date_created->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($lib_rule->modified_by->Visible) { // modified_by ?>
	<tr id="r_modified_by">
		<td><span id="elh_lib_rule_modified_by"><?php echo $lib_rule->modified_by->FldCaption() ?></span></td>
		<td<?php echo $lib_rule->modified_by->CellAttributes() ?>>
<span id="el_lib_rule_modified_by" class="control-group">
<input type="text" data-field="x_modified_by" name="x_modified_by" id="x_modified_by" size="30" placeholder="<?php echo $lib_rule->modified_by->PlaceHolder ?>" value="<?php echo $lib_rule->modified_by->EditValue ?>"<?php echo $lib_rule->modified_by->EditAttributes() ?>>
</span>
<?php echo $lib_rule->modified_by->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($lib_rule->date_modified->Visible) { // date_modified ?>
	<tr id="r_date_modified">
		<td><span id="elh_lib_rule_date_modified"><?php echo $lib_rule->date_modified->FldCaption() ?></span></td>
		<td<?php echo $lib_rule->date_modified->CellAttributes() ?>>
<span id="el_lib_rule_date_modified" class="control-group">
<input type="text" data-field="x_date_modified" name="x_date_modified" id="x_date_modified" placeholder="<?php echo $lib_rule->date_modified->PlaceHolder ?>" value="<?php echo $lib_rule->date_modified->EditValue ?>"<?php echo $lib_rule->date_modified->EditAttributes() ?>>
</span>
<?php echo $lib_rule->date_modified->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($lib_rule->DELETED->Visible) { // DELETED ?>
	<tr id="r_DELETED">
		<td><span id="elh_lib_rule_DELETED"><?php echo $lib_rule->DELETED->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $lib_rule->DELETED->CellAttributes() ?>>
<span id="el_lib_rule_DELETED" class="control-group">
<input type="text" data-field="x_DELETED" name="x_DELETED" id="x_DELETED" size="30" placeholder="<?php echo $lib_rule->DELETED->PlaceHolder ?>" value="<?php echo $lib_rule->DELETED->EditValue ?>"<?php echo $lib_rule->DELETED->EditAttributes() ?>>
</span>
<?php echo $lib_rule->DELETED->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
</form>
<script type="text/javascript">
flib_ruleadd.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$lib_rule_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$lib_rule_add->Page_Terminate();
?>
