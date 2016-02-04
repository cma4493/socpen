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

$tbl_updates_add = NULL; // Initialize page object first

class ctbl_updates_add extends ctbl_updates {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Table name
	var $TableName = 'tbl_updates';

	// Page object name
	var $PageObjName = 'tbl_updates_add';

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
			define("EW_PAGE_ID", 'add', TRUE);

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
		if (!$Security->CanAdd()) {
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

		// Set up master/detail parameters
		$this->SetUpMasterParms();

		// Process form if post back
		if (@$_POST["a_add"] <> "") {
			$this->CurrentAction = $_POST["a_add"]; // Get form action
			$this->CopyRecord = $this->LoadOldRecord(); // Load old recordset
			$this->LoadFormValues(); // Load form values
		} else { // Not post back

			// Load key values from QueryString
			$this->CopyRecord = TRUE;
			if (@$_GET["updatesID"] != "") {
				$this->updatesID->setQueryStringValue($_GET["updatesID"]);
				$this->setKey("updatesID", $this->updatesID->CurrentValue); // Set up key
			} else {
				$this->setKey("updatesID", ""); // Clear key
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
					$this->Page_Terminate("tbl_updateslist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "tbl_updatesview.php")
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
		$this->PensionerID->CurrentValue = NULL;
		$this->PensionerID->OldValue = $this->PensionerID->CurrentValue;
		$this->status->CurrentValue = 0;
		$this->Remarks->CurrentValue = NULL;
		$this->Remarks->OldValue = $this->Remarks->CurrentValue;
		$this->approved->CurrentValue = 0;
		$this->dateUpdated->CurrentValue = NULL;
		$this->dateUpdated->OldValue = $this->dateUpdated->CurrentValue;
		$this->_field->CurrentValue = NULL;
		$this->_field->OldValue = $this->_field->CurrentValue;
		$this->new_value->CurrentValue = NULL;
		$this->new_value->OldValue = $this->new_value->CurrentValue;
		$this->old_value->CurrentValue = NULL;
		$this->old_value->OldValue = $this->old_value->CurrentValue;
		$this->paymentmodeID->CurrentValue = NULL;
		$this->paymentmodeID->OldValue = $this->paymentmodeID->CurrentValue;
		$this->deathDate->CurrentValue = NULL;
		$this->deathDate->OldValue = $this->deathDate->CurrentValue;
		$this->Createdby->CurrentValue = NULL;
		$this->Createdby->OldValue = $this->Createdby->CurrentValue;
		$this->CreatedDate->CurrentValue = NULL;
		$this->CreatedDate->OldValue = $this->CreatedDate->CurrentValue;
		$this->UpdatedBy->CurrentValue = NULL;
		$this->UpdatedBy->OldValue = $this->UpdatedBy->CurrentValue;
		$this->UpdatedDate->CurrentValue = NULL;
		$this->UpdatedDate->OldValue = $this->UpdatedDate->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->PensionerID->FldIsDetailKey) {
			$this->PensionerID->setFormValue($objForm->GetValue("x_PensionerID"));
		}
		if (!$this->status->FldIsDetailKey) {
			$this->status->setFormValue($objForm->GetValue("x_status"));
		}
		if (!$this->Remarks->FldIsDetailKey) {
			$this->Remarks->setFormValue($objForm->GetValue("x_Remarks"));
		}
		if (!$this->approved->FldIsDetailKey) {
			$this->approved->setFormValue($objForm->GetValue("x_approved"));
		}
		if (!$this->dateUpdated->FldIsDetailKey) {
			$this->dateUpdated->setFormValue($objForm->GetValue("x_dateUpdated"));
			$this->dateUpdated->CurrentValue = ew_UnFormatDateTime($this->dateUpdated->CurrentValue, 6);
		}
		if (!$this->_field->FldIsDetailKey) {
			$this->_field->setFormValue($objForm->GetValue("x__field"));
		}
		if (!$this->new_value->FldIsDetailKey) {
			$this->new_value->setFormValue($objForm->GetValue("x_new_value"));
		}
		if (!$this->old_value->FldIsDetailKey) {
			$this->old_value->setFormValue($objForm->GetValue("x_old_value"));
		}
		if (!$this->paymentmodeID->FldIsDetailKey) {
			$this->paymentmodeID->setFormValue($objForm->GetValue("x_paymentmodeID"));
		}
		if (!$this->deathDate->FldIsDetailKey) {
			$this->deathDate->setFormValue($objForm->GetValue("x_deathDate"));
			$this->deathDate->CurrentValue = ew_UnFormatDateTime($this->deathDate->CurrentValue, 6);
		}
		if (!$this->Createdby->FldIsDetailKey) {
			$this->Createdby->setFormValue($objForm->GetValue("x_Createdby"));
		}
		if (!$this->CreatedDate->FldIsDetailKey) {
			$this->CreatedDate->setFormValue($objForm->GetValue("x_CreatedDate"));
			$this->CreatedDate->CurrentValue = ew_UnFormatDateTime($this->CreatedDate->CurrentValue, 6);
		}
		if (!$this->UpdatedBy->FldIsDetailKey) {
			$this->UpdatedBy->setFormValue($objForm->GetValue("x_UpdatedBy"));
		}
		if (!$this->UpdatedDate->FldIsDetailKey) {
			$this->UpdatedDate->setFormValue($objForm->GetValue("x_UpdatedDate"));
			$this->UpdatedDate->CurrentValue = ew_UnFormatDateTime($this->UpdatedDate->CurrentValue, 6);
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->PensionerID->CurrentValue = $this->PensionerID->FormValue;
		$this->status->CurrentValue = $this->status->FormValue;
		$this->Remarks->CurrentValue = $this->Remarks->FormValue;
		$this->approved->CurrentValue = $this->approved->FormValue;
		$this->dateUpdated->CurrentValue = $this->dateUpdated->FormValue;
		$this->dateUpdated->CurrentValue = ew_UnFormatDateTime($this->dateUpdated->CurrentValue, 6);
		$this->_field->CurrentValue = $this->_field->FormValue;
		$this->new_value->CurrentValue = $this->new_value->FormValue;
		$this->old_value->CurrentValue = $this->old_value->FormValue;
		$this->paymentmodeID->CurrentValue = $this->paymentmodeID->FormValue;
		$this->deathDate->CurrentValue = $this->deathDate->FormValue;
		$this->deathDate->CurrentValue = ew_UnFormatDateTime($this->deathDate->CurrentValue, 6);
		$this->Createdby->CurrentValue = $this->Createdby->FormValue;
		$this->CreatedDate->CurrentValue = $this->CreatedDate->FormValue;
		$this->CreatedDate->CurrentValue = ew_UnFormatDateTime($this->CreatedDate->CurrentValue, 6);
		$this->UpdatedBy->CurrentValue = $this->UpdatedBy->FormValue;
		$this->UpdatedDate->CurrentValue = $this->UpdatedDate->FormValue;
		$this->UpdatedDate->CurrentValue = ew_UnFormatDateTime($this->UpdatedDate->CurrentValue, 6);
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

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("updatesID")) <> "")
			$this->updatesID->CurrentValue = $this->getKey("updatesID"); // updatesID
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

			// Remarks
			$this->Remarks->ViewValue = $this->Remarks->CurrentValue;
			$this->Remarks->ViewCustomAttributes = "";

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

			// new_value
			$this->new_value->ViewValue = $this->new_value->CurrentValue;
			$this->new_value->ViewCustomAttributes = "";

			// old_value
			$this->old_value->ViewValue = $this->old_value->CurrentValue;
			$this->old_value->ViewCustomAttributes = "";

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

			// PensionerID
			$this->PensionerID->LinkCustomAttributes = "";
			$this->PensionerID->HrefValue = "";
			$this->PensionerID->TooltipValue = "";

			// status
			$this->status->LinkCustomAttributes = "";
			$this->status->HrefValue = "";
			$this->status->TooltipValue = "";

			// Remarks
			$this->Remarks->LinkCustomAttributes = "";
			$this->Remarks->HrefValue = "";
			$this->Remarks->TooltipValue = "";

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

			// new_value
			$this->new_value->LinkCustomAttributes = "";
			$this->new_value->HrefValue = "";
			$this->new_value->TooltipValue = "";

			// old_value
			$this->old_value->LinkCustomAttributes = "";
			$this->old_value->HrefValue = "";
			$this->old_value->TooltipValue = "";

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
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// PensionerID
			$this->PensionerID->EditCustomAttributes = "";
			if ($this->PensionerID->getSessionValue() <> "") {
				$this->PensionerID->CurrentValue = $this->PensionerID->getSessionValue();
			$this->PensionerID->ViewValue = $this->PensionerID->CurrentValue;
			$this->PensionerID->ViewCustomAttributes = "";
			} else {
			$this->PensionerID->EditValue = ew_HtmlEncode($this->PensionerID->CurrentValue);
			$this->PensionerID->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->PensionerID->FldCaption()));
			}

			// status
			$this->status->EditCustomAttributes = "";
			$this->status->EditValue = ew_HtmlEncode($this->status->CurrentValue);
			$this->status->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->status->FldCaption()));

			// Remarks
			$this->Remarks->EditCustomAttributes = "";
			$this->Remarks->EditValue = $this->Remarks->CurrentValue;
			$this->Remarks->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->Remarks->FldCaption()));

			// approved
			$this->approved->EditCustomAttributes = "";
			$this->approved->EditValue = ew_HtmlEncode($this->approved->CurrentValue);
			$this->approved->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->approved->FldCaption()));

			// dateUpdated
			$this->dateUpdated->EditCustomAttributes = "";
			$this->dateUpdated->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->dateUpdated->CurrentValue, 6));
			$this->dateUpdated->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->dateUpdated->FldCaption()));

			// field
			$this->_field->EditCustomAttributes = "";
			$this->_field->EditValue = ew_HtmlEncode($this->_field->CurrentValue);
			$this->_field->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->_field->FldCaption()));

			// new_value
			$this->new_value->EditCustomAttributes = "";
			$this->new_value->EditValue = $this->new_value->CurrentValue;
			$this->new_value->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->new_value->FldCaption()));

			// old_value
			$this->old_value->EditCustomAttributes = "";
			$this->old_value->EditValue = $this->old_value->CurrentValue;
			$this->old_value->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->old_value->FldCaption()));

			// paymentmodeID
			$this->paymentmodeID->EditCustomAttributes = "";
			$this->paymentmodeID->EditValue = ew_HtmlEncode($this->paymentmodeID->CurrentValue);
			$this->paymentmodeID->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->paymentmodeID->FldCaption()));

			// deathDate
			$this->deathDate->EditCustomAttributes = "";
			$this->deathDate->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->deathDate->CurrentValue, 6));
			$this->deathDate->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->deathDate->FldCaption()));

			// Createdby
			$this->Createdby->EditCustomAttributes = "";
			$this->Createdby->EditValue = ew_HtmlEncode($this->Createdby->CurrentValue);
			$this->Createdby->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->Createdby->FldCaption()));

			// CreatedDate
			$this->CreatedDate->EditCustomAttributes = "";
			$this->CreatedDate->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->CreatedDate->CurrentValue, 6));
			$this->CreatedDate->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->CreatedDate->FldCaption()));

			// UpdatedBy
			$this->UpdatedBy->EditCustomAttributes = "";
			$this->UpdatedBy->EditValue = ew_HtmlEncode($this->UpdatedBy->CurrentValue);
			$this->UpdatedBy->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->UpdatedBy->FldCaption()));

			// UpdatedDate
			$this->UpdatedDate->EditCustomAttributes = "";
			$this->UpdatedDate->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->UpdatedDate->CurrentValue, 6));
			$this->UpdatedDate->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->UpdatedDate->FldCaption()));

			// Edit refer script
			// PensionerID

			$this->PensionerID->HrefValue = "";

			// status
			$this->status->HrefValue = "";

			// Remarks
			$this->Remarks->HrefValue = "";

			// approved
			$this->approved->HrefValue = "";

			// dateUpdated
			$this->dateUpdated->HrefValue = "";

			// field
			$this->_field->HrefValue = "";

			// new_value
			$this->new_value->HrefValue = "";

			// old_value
			$this->old_value->HrefValue = "";

			// paymentmodeID
			$this->paymentmodeID->HrefValue = "";

			// deathDate
			$this->deathDate->HrefValue = "";

			// Createdby
			$this->Createdby->HrefValue = "";

			// CreatedDate
			$this->CreatedDate->HrefValue = "";

			// UpdatedBy
			$this->UpdatedBy->HrefValue = "";

			// UpdatedDate
			$this->UpdatedDate->HrefValue = "";
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
		if (!$this->status->FldIsDetailKey && !is_null($this->status->FormValue) && $this->status->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->status->FldCaption());
		}
		if (!ew_CheckInteger($this->status->FormValue)) {
			ew_AddMessage($gsFormError, $this->status->FldErrMsg());
		}
		if (!$this->approved->FldIsDetailKey && !is_null($this->approved->FormValue) && $this->approved->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->approved->FldCaption());
		}
		if (!ew_CheckInteger($this->approved->FormValue)) {
			ew_AddMessage($gsFormError, $this->approved->FldErrMsg());
		}
		if (!ew_CheckUSDate($this->dateUpdated->FormValue)) {
			ew_AddMessage($gsFormError, $this->dateUpdated->FldErrMsg());
		}
		if (!$this->_field->FldIsDetailKey && !is_null($this->_field->FormValue) && $this->_field->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->_field->FldCaption());
		}
		if (!$this->new_value->FldIsDetailKey && !is_null($this->new_value->FormValue) && $this->new_value->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->new_value->FldCaption());
		}
		if (!$this->old_value->FldIsDetailKey && !is_null($this->old_value->FormValue) && $this->old_value->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->old_value->FldCaption());
		}
		if (!ew_CheckInteger($this->paymentmodeID->FormValue)) {
			ew_AddMessage($gsFormError, $this->paymentmodeID->FldErrMsg());
		}
		if (!ew_CheckUSDate($this->deathDate->FormValue)) {
			ew_AddMessage($gsFormError, $this->deathDate->FldErrMsg());
		}
		if (!ew_CheckInteger($this->Createdby->FormValue)) {
			ew_AddMessage($gsFormError, $this->Createdby->FldErrMsg());
		}
		if (!ew_CheckUSDate($this->CreatedDate->FormValue)) {
			ew_AddMessage($gsFormError, $this->CreatedDate->FldErrMsg());
		}
		if (!ew_CheckInteger($this->UpdatedBy->FormValue)) {
			ew_AddMessage($gsFormError, $this->UpdatedBy->FldErrMsg());
		}
		if (!ew_CheckUSDate($this->UpdatedDate->FormValue)) {
			ew_AddMessage($gsFormError, $this->UpdatedDate->FldErrMsg());
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

		// PensionerID
		$this->PensionerID->SetDbValueDef($rsnew, $this->PensionerID->CurrentValue, NULL, FALSE);

		// status
		$this->status->SetDbValueDef($rsnew, $this->status->CurrentValue, 0, strval($this->status->CurrentValue) == "");

		// Remarks
		$this->Remarks->SetDbValueDef($rsnew, $this->Remarks->CurrentValue, NULL, FALSE);

		// approved
		$this->approved->SetDbValueDef($rsnew, $this->approved->CurrentValue, 0, strval($this->approved->CurrentValue) == "");

		// dateUpdated
		$this->dateUpdated->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->dateUpdated->CurrentValue, 6), NULL, FALSE);

		// field
		$this->_field->SetDbValueDef($rsnew, $this->_field->CurrentValue, "", FALSE);

		// new_value
		$this->new_value->SetDbValueDef($rsnew, $this->new_value->CurrentValue, "", FALSE);

		// old_value
		$this->old_value->SetDbValueDef($rsnew, $this->old_value->CurrentValue, "", FALSE);

		// paymentmodeID
		$this->paymentmodeID->SetDbValueDef($rsnew, $this->paymentmodeID->CurrentValue, NULL, FALSE);

		// deathDate
		$this->deathDate->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->deathDate->CurrentValue, 6), NULL, FALSE);

		// Createdby
		$this->Createdby->SetDbValueDef($rsnew, $this->Createdby->CurrentValue, NULL, FALSE);

		// CreatedDate
		$this->CreatedDate->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->CreatedDate->CurrentValue, 6), NULL, FALSE);

		// UpdatedBy
		$this->UpdatedBy->SetDbValueDef($rsnew, $this->UpdatedBy->CurrentValue, NULL, FALSE);

		// UpdatedDate
		$this->UpdatedDate->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->UpdatedDate->CurrentValue, 6), NULL, FALSE);

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
			$this->updatesID->setDbValue($conn->Insert_ID());
			$rsnew['updatesID'] = $this->updatesID->DbValue;
		}
		if ($AddRow) {

			// Call Row Inserted event
			$rs = ($rsold == NULL) ? NULL : $rsold->fields;
			$this->Row_Inserted($rs, $rsnew);
			$this->WriteAuditTrailOnAdd($rsnew);
		}
		return $AddRow;
	}

	// Set up master/detail based on QueryString
	function SetUpMasterParms() {
		$bValidMaster = FALSE;

		// Get the keys for master table
		if (isset($_GET[EW_TABLE_SHOW_MASTER])) {
			$sMasterTblVar = $_GET[EW_TABLE_SHOW_MASTER];
			if ($sMasterTblVar == "") {
				$bValidMaster = TRUE;
				$this->DbMasterFilter = "";
				$this->DbDetailFilter = "";
			}
			if ($sMasterTblVar == "tbl_pensioner") {
				$bValidMaster = TRUE;
				if (@$_GET["PensionerID"] <> "") {
					$GLOBALS["tbl_pensioner"]->PensionerID->setQueryStringValue($_GET["PensionerID"]);
					$this->PensionerID->setQueryStringValue($GLOBALS["tbl_pensioner"]->PensionerID->QueryStringValue);
					$this->PensionerID->setSessionValue($this->PensionerID->QueryStringValue);
				} else {
					$bValidMaster = FALSE;
				}
			}
		}
		if ($bValidMaster) {

			// Save current master table
			$this->setCurrentMasterTable($sMasterTblVar);

			// Reset start record counter (new master key)
			$this->StartRec = 1;
			$this->setStartRecordNumber($this->StartRec);

			// Clear previous master key from Session
			if ($sMasterTblVar <> "tbl_pensioner") {
				if ($this->PensionerID->QueryStringValue == "") $this->PensionerID->setSessionValue("");
			}
		}
		$this->DbMasterFilter = $this->GetMasterFilter(); //  Get master filter
		$this->DbDetailFilter = $this->GetDetailFilter(); // Get detail filter
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$PageCaption = $this->TableCaption();
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "tbl_updateslist.php", $this->TableVar);
		$PageCaption = ($this->CurrentAction == "C") ? $Language->Phrase("Copy") : $Language->Phrase("Add");
		$Breadcrumb->Add("add", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", ew_CurrentUrl(), $this->TableVar);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'tbl_updates';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (add page)
	function WriteAuditTrailOnAdd(&$rs) {
		if (!$this->AuditTrailOnAdd) return;
		$table = 'tbl_updates';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['updatesID'];

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
if (!isset($tbl_updates_add)) $tbl_updates_add = new ctbl_updates_add();

// Page init
$tbl_updates_add->Page_Init();

// Page main
$tbl_updates_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$tbl_updates_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var tbl_updates_add = new ew_Page("tbl_updates_add");
tbl_updates_add.PageID = "add"; // Page ID
var EW_PAGE_ID = tbl_updates_add.PageID; // For backward compatibility

// Form object
var ftbl_updatesadd = new ew_Form("ftbl_updatesadd");

// Validate form
ftbl_updatesadd.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_new_value");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($tbl_updates->new_value->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_old_value");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($tbl_updates->old_value->FldCaption()) ?>");
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
ftbl_updatesadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftbl_updatesadd.ValidateRequired = true;
<?php } else { ?>
ftbl_updatesadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php //$Breadcrumb->Render(); ?>
<?php $tbl_updates_add->ShowPageHeader(); ?>
<?php
$tbl_updates_add->ShowMessage();
?>
<form name="ftbl_updatesadd" id="ftbl_updatesadd" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="tbl_updates">
<input type="hidden" name="a_add" id="a_add" value="A">
<table cellspacing="0" class="ewGrid"><tr><td>
<table id="tbl_tbl_updatesadd" class="table table-bordered table-striped">
<?php if ($tbl_updates->PensionerID->Visible) { // PensionerID ?>
	<tr id="r_PensionerID">
		<td><span id="elh_tbl_updates_PensionerID"><?php echo $tbl_updates->PensionerID->FldCaption() ?></span></td>
		<td<?php echo $tbl_updates->PensionerID->CellAttributes() ?>>
<?php if ($tbl_updates->PensionerID->getSessionValue() <> "") { ?>
<span<?php echo $tbl_updates->PensionerID->ViewAttributes() ?>>
<?php echo $tbl_updates->PensionerID->ViewValue ?></span>
<input type="hidden" id="x_PensionerID" name="x_PensionerID" value="<?php echo ew_HtmlEncode($tbl_updates->PensionerID->CurrentValue) ?>">
<?php } else { ?>
<input type="text" data-field="x_PensionerID" name="x_PensionerID" id="x_PensionerID" size="30" maxlength="25" placeholder="<?php echo $tbl_updates->PensionerID->PlaceHolder ?>" value="<?php echo $tbl_updates->PensionerID->EditValue ?>"<?php echo $tbl_updates->PensionerID->EditAttributes() ?>>
<?php } ?>
<?php echo $tbl_updates->PensionerID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_updates->status->Visible) { // status ?>
	<tr id="r_status">
		<td><span id="elh_tbl_updates_status"><?php echo $tbl_updates->status->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $tbl_updates->status->CellAttributes() ?>>
<span id="el_tbl_updates_status" class="control-group">
<input type="text" data-field="x_status" name="x_status" id="x_status" size="30" placeholder="<?php echo $tbl_updates->status->PlaceHolder ?>" value="<?php echo $tbl_updates->status->EditValue ?>"<?php echo $tbl_updates->status->EditAttributes() ?>>
</span>
<?php echo $tbl_updates->status->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_updates->Remarks->Visible) { // Remarks ?>
	<tr id="r_Remarks">
		<td><span id="elh_tbl_updates_Remarks"><?php echo $tbl_updates->Remarks->FldCaption() ?></span></td>
		<td<?php echo $tbl_updates->Remarks->CellAttributes() ?>>
<span id="el_tbl_updates_Remarks" class="control-group">
<textarea data-field="x_Remarks" name="x_Remarks" id="x_Remarks" cols="35" rows="4" placeholder="<?php echo $tbl_updates->Remarks->PlaceHolder ?>"<?php echo $tbl_updates->Remarks->EditAttributes() ?>><?php echo $tbl_updates->Remarks->EditValue ?></textarea>
</span>
<?php echo $tbl_updates->Remarks->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_updates->approved->Visible) { // approved ?>
	<tr id="r_approved">
		<td><span id="elh_tbl_updates_approved"><?php echo $tbl_updates->approved->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $tbl_updates->approved->CellAttributes() ?>>
<span id="el_tbl_updates_approved" class="control-group">
<input type="text" data-field="x_approved" name="x_approved" id="x_approved" size="30" placeholder="<?php echo $tbl_updates->approved->PlaceHolder ?>" value="<?php echo $tbl_updates->approved->EditValue ?>"<?php echo $tbl_updates->approved->EditAttributes() ?>>
</span>
<?php echo $tbl_updates->approved->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_updates->dateUpdated->Visible) { // dateUpdated ?>
	<tr id="r_dateUpdated">
		<td><span id="elh_tbl_updates_dateUpdated"><?php echo $tbl_updates->dateUpdated->FldCaption() ?></span></td>
		<td<?php echo $tbl_updates->dateUpdated->CellAttributes() ?>>
<span id="el_tbl_updates_dateUpdated" class="control-group">
<input type="text" data-field="x_dateUpdated" name="x_dateUpdated" id="x_dateUpdated" placeholder="<?php echo $tbl_updates->dateUpdated->PlaceHolder ?>" value="<?php echo $tbl_updates->dateUpdated->EditValue ?>"<?php echo $tbl_updates->dateUpdated->EditAttributes() ?>>
</span>
<?php echo $tbl_updates->dateUpdated->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_updates->_field->Visible) { // field ?>
	<tr id="r__field">
		<td><span id="elh_tbl_updates__field"><?php echo $tbl_updates->_field->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $tbl_updates->_field->CellAttributes() ?>>
<span id="el_tbl_updates__field" class="control-group">
<input type="text" data-field="x__field" name="x__field" id="x__field" size="30" maxlength="20" placeholder="<?php echo $tbl_updates->_field->PlaceHolder ?>" value="<?php echo $tbl_updates->_field->EditValue ?>"<?php echo $tbl_updates->_field->EditAttributes() ?>>
</span>
<?php echo $tbl_updates->_field->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_updates->new_value->Visible) { // new_value ?>
	<tr id="r_new_value">
		<td><span id="elh_tbl_updates_new_value"><?php echo $tbl_updates->new_value->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $tbl_updates->new_value->CellAttributes() ?>>
<span id="el_tbl_updates_new_value" class="control-group">
<textarea data-field="x_new_value" name="x_new_value" id="x_new_value" cols="35" rows="4" placeholder="<?php echo $tbl_updates->new_value->PlaceHolder ?>"<?php echo $tbl_updates->new_value->EditAttributes() ?>><?php echo $tbl_updates->new_value->EditValue ?></textarea>
</span>
<?php echo $tbl_updates->new_value->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_updates->old_value->Visible) { // old_value ?>
	<tr id="r_old_value">
		<td><span id="elh_tbl_updates_old_value"><?php echo $tbl_updates->old_value->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $tbl_updates->old_value->CellAttributes() ?>>
<span id="el_tbl_updates_old_value" class="control-group">
<textarea data-field="x_old_value" name="x_old_value" id="x_old_value" cols="35" rows="4" placeholder="<?php echo $tbl_updates->old_value->PlaceHolder ?>"<?php echo $tbl_updates->old_value->EditAttributes() ?>><?php echo $tbl_updates->old_value->EditValue ?></textarea>
</span>
<?php echo $tbl_updates->old_value->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_updates->paymentmodeID->Visible) { // paymentmodeID ?>
	<tr id="r_paymentmodeID">
		<td><span id="elh_tbl_updates_paymentmodeID"><?php echo $tbl_updates->paymentmodeID->FldCaption() ?></span></td>
		<td<?php echo $tbl_updates->paymentmodeID->CellAttributes() ?>>
<span id="el_tbl_updates_paymentmodeID" class="control-group">
<input type="text" data-field="x_paymentmodeID" name="x_paymentmodeID" id="x_paymentmodeID" size="30" placeholder="<?php echo $tbl_updates->paymentmodeID->PlaceHolder ?>" value="<?php echo $tbl_updates->paymentmodeID->EditValue ?>"<?php echo $tbl_updates->paymentmodeID->EditAttributes() ?>>
</span>
<?php echo $tbl_updates->paymentmodeID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_updates->deathDate->Visible) { // deathDate ?>
	<tr id="r_deathDate">
		<td><span id="elh_tbl_updates_deathDate"><?php echo $tbl_updates->deathDate->FldCaption() ?></span></td>
		<td<?php echo $tbl_updates->deathDate->CellAttributes() ?>>
<span id="el_tbl_updates_deathDate" class="control-group">
<input type="text" data-field="x_deathDate" name="x_deathDate" id="x_deathDate" placeholder="<?php echo $tbl_updates->deathDate->PlaceHolder ?>" value="<?php echo $tbl_updates->deathDate->EditValue ?>"<?php echo $tbl_updates->deathDate->EditAttributes() ?>>
</span>
<?php echo $tbl_updates->deathDate->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_updates->Createdby->Visible) { // Createdby ?>
	<tr id="r_Createdby">
		<td><span id="elh_tbl_updates_Createdby"><?php echo $tbl_updates->Createdby->FldCaption() ?></span></td>
		<td<?php echo $tbl_updates->Createdby->CellAttributes() ?>>
<span id="el_tbl_updates_Createdby" class="control-group">
<input type="text" data-field="x_Createdby" name="x_Createdby" id="x_Createdby" size="30" placeholder="<?php echo $tbl_updates->Createdby->PlaceHolder ?>" value="<?php echo $tbl_updates->Createdby->EditValue ?>"<?php echo $tbl_updates->Createdby->EditAttributes() ?>>
</span>
<?php echo $tbl_updates->Createdby->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_updates->CreatedDate->Visible) { // CreatedDate ?>
	<tr id="r_CreatedDate">
		<td><span id="elh_tbl_updates_CreatedDate"><?php echo $tbl_updates->CreatedDate->FldCaption() ?></span></td>
		<td<?php echo $tbl_updates->CreatedDate->CellAttributes() ?>>
<span id="el_tbl_updates_CreatedDate" class="control-group">
<input type="text" data-field="x_CreatedDate" name="x_CreatedDate" id="x_CreatedDate" placeholder="<?php echo $tbl_updates->CreatedDate->PlaceHolder ?>" value="<?php echo $tbl_updates->CreatedDate->EditValue ?>"<?php echo $tbl_updates->CreatedDate->EditAttributes() ?>>
</span>
<?php echo $tbl_updates->CreatedDate->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_updates->UpdatedBy->Visible) { // UpdatedBy ?>
	<tr id="r_UpdatedBy">
		<td><span id="elh_tbl_updates_UpdatedBy"><?php echo $tbl_updates->UpdatedBy->FldCaption() ?></span></td>
		<td<?php echo $tbl_updates->UpdatedBy->CellAttributes() ?>>
<span id="el_tbl_updates_UpdatedBy" class="control-group">
<input type="text" data-field="x_UpdatedBy" name="x_UpdatedBy" id="x_UpdatedBy" size="30" placeholder="<?php echo $tbl_updates->UpdatedBy->PlaceHolder ?>" value="<?php echo $tbl_updates->UpdatedBy->EditValue ?>"<?php echo $tbl_updates->UpdatedBy->EditAttributes() ?>>
</span>
<?php echo $tbl_updates->UpdatedBy->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_updates->UpdatedDate->Visible) { // UpdatedDate ?>
	<tr id="r_UpdatedDate">
		<td><span id="elh_tbl_updates_UpdatedDate"><?php echo $tbl_updates->UpdatedDate->FldCaption() ?></span></td>
		<td<?php echo $tbl_updates->UpdatedDate->CellAttributes() ?>>
<span id="el_tbl_updates_UpdatedDate" class="control-group">
<input type="text" data-field="x_UpdatedDate" name="x_UpdatedDate" id="x_UpdatedDate" placeholder="<?php echo $tbl_updates->UpdatedDate->PlaceHolder ?>" value="<?php echo $tbl_updates->UpdatedDate->EditValue ?>"<?php echo $tbl_updates->UpdatedDate->EditAttributes() ?>>
</span>
<?php echo $tbl_updates->UpdatedDate->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
</form>
<script type="text/javascript">
ftbl_updatesadd.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$tbl_updates_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$tbl_updates_add->Page_Terminate();
?>
