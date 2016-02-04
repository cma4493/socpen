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

$lib_rule_edit = NULL; // Initialize page object first

class clib_rule_edit extends clib_rule {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Table name
	var $TableName = 'lib_rule';

	// Page object name
	var $PageObjName = 'lib_rule_edit';

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
	var $AuditTrailOnEdit = TRUE;

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
			define("EW_PAGE_ID", 'edit', TRUE);

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
		if (!$Security->CanEdit()) {
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
		$this->ruleID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
	var $DbMasterFilter;
	var $DbDetailFilter;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;

		// Load key from QueryString
		if (@$_GET["ruleID"] <> "") {
			$this->ruleID->setQueryStringValue($_GET["ruleID"]);
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Process form if post back
		if (@$_POST["a_edit"] <> "") {
			$this->CurrentAction = $_POST["a_edit"]; // Get action code
			$this->LoadFormValues(); // Get form values
		} else {
			$this->CurrentAction = "I"; // Default action is display
		}

		// Check if valid key
		if ($this->ruleID->CurrentValue == "")
			$this->Page_Terminate("lib_rulelist.php"); // Invalid key, return to list

		// Validate form if post back
		if (@$_POST["a_edit"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = ""; // Form error, reset action
				$this->setFailureMessage($gsFormError);
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues();
			}
		}
		switch ($this->CurrentAction) {
			case "I": // Get a record to display
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("lib_rulelist.php"); // No matching record, return to list
				}
				break;
			Case "U": // Update
				$this->SendEmail = TRUE; // Send email on update success
				if ($this->EditRow()) { // Update record based on key
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("UpdateSuccess")); // Update success
					$sReturnUrl = $this->getReturnUrl();
					$this->Page_Terminate($sReturnUrl); // Return to caller
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Restore form values if update failed
				}
		}

		// Render the record
		$this->RowType = EW_ROWTYPE_EDIT; // Render as Edit
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Set up starting record parameters
	function SetUpStartRec() {
		if ($this->DisplayRecs == 0)
			return;
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET[EW_TABLE_START_REC] <> "") { // Check for "start" parameter
				$this->StartRec = $_GET[EW_TABLE_START_REC];
				$this->setStartRecordNumber($this->StartRec);
			} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
				$PageNo = $_GET[EW_TABLE_PAGE_NO];
				if (is_numeric($PageNo)) {
					$this->StartRec = ($PageNo-1)*$this->DisplayRecs+1;
					if ($this->StartRec <= 0) {
						$this->StartRec = 1;
					} elseif ($this->StartRec >= intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1) {
						$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1;
					}
					$this->setStartRecordNumber($this->StartRec);
				}
			}
		}
		$this->StartRec = $this->getStartRecordNumber();

		// Check if correct start record counter
		if (!is_numeric($this->StartRec) || $this->StartRec == "") { // Avoid invalid start record counter
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} elseif (intval($this->StartRec) > intval($this->TotalRecs)) { // Avoid starting record > total records
			$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to last page first record
			$this->setStartRecordNumber($this->StartRec);
		} elseif (($this->StartRec-1) % $this->DisplayRecs <> 0) {
			$this->StartRec = intval(($this->StartRec-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to page boundary
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Get upload files
	function GetUploadFiles() {
		global $objForm;

		// Get upload data
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->ruleID->FldIsDetailKey)
			$this->ruleID->setFormValue($objForm->GetValue("x_ruleID"));
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
		$this->LoadRow();
		$this->ruleID->CurrentValue = $this->ruleID->FormValue;
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

			// ruleID
			$this->ruleID->LinkCustomAttributes = "";
			$this->ruleID->HrefValue = "";
			$this->ruleID->TooltipValue = "";

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
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// ruleID
			$this->ruleID->EditCustomAttributes = "";
			$this->ruleID->EditValue = $this->ruleID->CurrentValue;
			$this->ruleID->ViewCustomAttributes = "";

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
			// ruleID

			$this->ruleID->HrefValue = "";

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

	// Update record based on key values
	function EditRow() {
		global $conn, $Security, $Language;
		$sFilter = $this->KeyFilter();
			if ($this->rule_active->CurrentValue <> "") { // Check field with unique index
			$sFilterChk = "(`rule_active` = " . ew_AdjustSql($this->rule_active->CurrentValue) . ")";
			$sFilterChk .= " AND NOT (" . $sFilter . ")";
			$this->CurrentFilter = $sFilterChk;
			$sSqlChk = $this->SQL();
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$rsChk = $conn->Execute($sSqlChk);
			$conn->raiseErrorFn = '';
			if ($rsChk === FALSE) {
				return FALSE;
			} elseif (!$rsChk->EOF) {
				$sIdxErrMsg = str_replace("%f", $this->rule_active->FldCaption(), $Language->Phrase("DupIndex"));
				$sIdxErrMsg = str_replace("%v", $this->rule_active->CurrentValue, $sIdxErrMsg);
				$this->setFailureMessage($sIdxErrMsg);
				$rsChk->Close();
				return FALSE;
			}
			$rsChk->Close();
		}
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = 'ew_ErrorFn';
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE)
			return FALSE;
		if ($rs->EOF) {
			$EditRow = FALSE; // Update Failed
		} else {

			// Save old values
			$rsold = &$rs->fields;
			$this->LoadDbValues($rsold);
			$rsnew = array();

			// rule_age
			$this->rule_age->SetDbValueDef($rsnew, $this->rule_age->CurrentValue, 0, $this->rule_age->ReadOnly);

			// rule_affiliation
			$this->rule_affiliation->SetDbValueDef($rsnew, $this->rule_affiliation->CurrentValue, 0, $this->rule_affiliation->ReadOnly);

			// rule_active
			$this->rule_active->SetDbValueDef($rsnew, $this->rule_active->CurrentValue, NULL, $this->rule_active->ReadOnly);

			// created_by
			$this->created_by->SetDbValueDef($rsnew, $this->created_by->CurrentValue, NULL, $this->created_by->ReadOnly);

			// date_created
			$this->date_created->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->date_created->CurrentValue, 6), NULL, $this->date_created->ReadOnly);

			// modified_by
			$this->modified_by->SetDbValueDef($rsnew, $this->modified_by->CurrentValue, NULL, $this->modified_by->ReadOnly);

			// date_modified
			$this->date_modified->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->date_modified->CurrentValue, 6), NULL, $this->date_modified->ReadOnly);

			// DELETED
			$this->DELETED->SetDbValueDef($rsnew, $this->DELETED->CurrentValue, 0, $this->DELETED->ReadOnly);

			// Call Row Updating event
			$bUpdateRow = $this->Row_Updating($rsold, $rsnew);
			if ($bUpdateRow) {
				$conn->raiseErrorFn = 'ew_ErrorFn';
				if (count($rsnew) > 0)
					$EditRow = $this->Update($rsnew, "", $rsold);
				else
					$EditRow = TRUE; // No field to update
				$conn->raiseErrorFn = '';
				if ($EditRow) {
				}
			} else {
				if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

					// Use the message, do nothing
				} elseif ($this->CancelMessage <> "") {
					$this->setFailureMessage($this->CancelMessage);
					$this->CancelMessage = "";
				} else {
					$this->setFailureMessage($Language->Phrase("UpdateCancelled"));
				}
				$EditRow = FALSE;
			}
		}

		// Call Row_Updated event
		if ($EditRow)
			$this->Row_Updated($rsold, $rsnew);
		if ($EditRow) {
			$this->WriteAuditTrailOnEdit($rsold, $rsnew);
		}
		$rs->Close();
		return $EditRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$PageCaption = $this->TableCaption();
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "lib_rulelist.php", $this->TableVar);
		$PageCaption = $Language->Phrase("edit");
		$Breadcrumb->Add("edit", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", ew_CurrentUrl(), $this->TableVar);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'lib_rule';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (edit page)
	function WriteAuditTrailOnEdit(&$rsold, &$rsnew) {
		if (!$this->AuditTrailOnEdit) return;
		$table = 'lib_rule';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rsold['ruleID'];

		// Write Audit Trail
		$dt = ew_StdCurrentDateTime();
		$id = ew_ScriptName();
	  $usr = CurrentUserID();
		foreach (array_keys($rsnew) as $fldname) {
			if ($this->fields[$fldname]->FldDataType <> EW_DATATYPE_BLOB) { // Ignore BLOB fields
				if ($this->fields[$fldname]->FldDataType == EW_DATATYPE_DATE) { // DateTime field
					$modified = (ew_FormatDateTime($rsold[$fldname], 0) <> ew_FormatDateTime($rsnew[$fldname], 0));
				} else {
					$modified = !ew_CompareValue($rsold[$fldname], $rsnew[$fldname]);
				}
				if ($modified) {
					if ($this->fields[$fldname]->FldDataType == EW_DATATYPE_MEMO) { // Memo field
						if (EW_AUDIT_TRAIL_TO_DATABASE) {
							$oldvalue = $rsold[$fldname];
							$newvalue = $rsnew[$fldname];
						} else {
							$oldvalue = "[MEMO]";
							$newvalue = "[MEMO]";
						}
					} elseif ($this->fields[$fldname]->FldDataType == EW_DATATYPE_XML) { // XML field
						$oldvalue = "[XML]";
						$newvalue = "[XML]";
					} else {
						$oldvalue = $rsold[$fldname];
						$newvalue = $rsnew[$fldname];
					}
					ew_WriteAuditTrail("log", $dt, $id, $usr, "U", $table, $fldname, $key, $oldvalue, $newvalue);
				}
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
if (!isset($lib_rule_edit)) $lib_rule_edit = new clib_rule_edit();

// Page init
$lib_rule_edit->Page_Init();

// Page main
$lib_rule_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$lib_rule_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var lib_rule_edit = new ew_Page("lib_rule_edit");
lib_rule_edit.PageID = "edit"; // Page ID
var EW_PAGE_ID = lib_rule_edit.PageID; // For backward compatibility

// Form object
var flib_ruleedit = new ew_Form("flib_ruleedit");

// Validate form
flib_ruleedit.Validate = function() {
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
flib_ruleedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
flib_ruleedit.ValidateRequired = true;
<?php } else { ?>
flib_ruleedit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php //$Breadcrumb->Render(); ?>
<?php $lib_rule_edit->ShowPageHeader(); ?>
<?php
$lib_rule_edit->ShowMessage();
?>
<form name="flib_ruleedit" id="flib_ruleedit" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="lib_rule">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<table cellspacing="0" class="ewGrid"><tr><td>
<table id="tbl_lib_ruleedit" class="table table-bordered table-striped">
<?php if ($lib_rule->ruleID->Visible) { // ruleID ?>
	<tr id="r_ruleID">
		<td><span id="elh_lib_rule_ruleID"><?php echo $lib_rule->ruleID->FldCaption() ?></span></td>
		<td<?php echo $lib_rule->ruleID->CellAttributes() ?>>
<span id="el_lib_rule_ruleID" class="control-group">
<span<?php echo $lib_rule->ruleID->ViewAttributes() ?>>
<?php echo $lib_rule->ruleID->EditValue ?></span>
</span>
<input type="hidden" data-field="x_ruleID" name="x_ruleID" id="x_ruleID" value="<?php echo ew_HtmlEncode($lib_rule->ruleID->CurrentValue) ?>">
<?php echo $lib_rule->ruleID->CustomMsg ?></td>
	</tr>
<?php } ?>
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
<button class="btn btn-success btn-sm" name="btnAction" id="btnAction" type="submit"><?php echo " <i class='icon-save align-top bigger-125'></i> " . "Update Changes"; ?></button>
</form>
<script type="text/javascript">
flib_ruleedit.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$lib_rule_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$lib_rule_edit->Page_Terminate();
?>
