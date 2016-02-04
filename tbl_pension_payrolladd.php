<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "tbl_pension_payrollinfo.php" ?>
<?php include_once "tbl_userinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$tbl_pension_payroll_add = NULL; // Initialize page object first

class ctbl_pension_payroll_add extends ctbl_pension_payroll {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Table name
	var $TableName = 'tbl_pension_payroll';

	// Page object name
	var $PageObjName = 'tbl_pension_payroll_add';

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

		// Table object (tbl_pension_payroll)
		if (!isset($GLOBALS["tbl_pension_payroll"])) {
			$GLOBALS["tbl_pension_payroll"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["tbl_pension_payroll"];
		}

		// Table object (tbl_user)
		if (!isset($GLOBALS['tbl_user'])) $GLOBALS['tbl_user'] = new ctbl_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'tbl_pension_payroll', TRUE);

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
			$this->Page_Terminate("tbl_pension_payrolllist.php");
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
			if (@$_GET["PayrollID"] != "") {
				$this->PayrollID->setQueryStringValue($_GET["PayrollID"]);
				$this->setKey("PayrollID", $this->PayrollID->CurrentValue); // Set up key
			} else {
				$this->setKey("PayrollID", ""); // Clear key
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
					$this->Page_Terminate("tbl_pension_payrolllist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "tbl_pension_payrollview.php")
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
		$this->PayrollYear->CurrentValue = NULL;
		$this->PayrollYear->OldValue = $this->PayrollYear->CurrentValue;
		$this->cMonth->CurrentValue = NULL;
		$this->cMonth->OldValue = $this->cMonth->CurrentValue;
		$this->amount->CurrentValue = NULL;
		$this->amount->OldValue = $this->amount->CurrentValue;
		$this->paymentmodeID->CurrentValue = 0;
		$this->Approved->CurrentValue = 0;
		$this->Claimed->CurrentValue = 0;
		$this->Createdby->CurrentValue = NULL;
		$this->Createdby->OldValue = $this->Createdby->CurrentValue;
		$this->CreatedDate->CurrentValue = NULL;
		$this->CreatedDate->OldValue = $this->CreatedDate->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->PensionerID->FldIsDetailKey) {
			$this->PensionerID->setFormValue($objForm->GetValue("x_PensionerID"));
		}
		if (!$this->PayrollYear->FldIsDetailKey) {
			$this->PayrollYear->setFormValue($objForm->GetValue("x_PayrollYear"));
		}
		if (!$this->cMonth->FldIsDetailKey) {
			$this->cMonth->setFormValue($objForm->GetValue("x_cMonth"));
		}
		if (!$this->amount->FldIsDetailKey) {
			$this->amount->setFormValue($objForm->GetValue("x_amount"));
		}
		if (!$this->paymentmodeID->FldIsDetailKey) {
			$this->paymentmodeID->setFormValue($objForm->GetValue("x_paymentmodeID"));
		}
		if (!$this->Approved->FldIsDetailKey) {
			$this->Approved->setFormValue($objForm->GetValue("x_Approved"));
		}
		if (!$this->Claimed->FldIsDetailKey) {
			$this->Claimed->setFormValue($objForm->GetValue("x_Claimed"));
		}
		if (!$this->Createdby->FldIsDetailKey) {
			$this->Createdby->setFormValue($objForm->GetValue("x_Createdby"));
		}
		if (!$this->CreatedDate->FldIsDetailKey) {
			$this->CreatedDate->setFormValue($objForm->GetValue("x_CreatedDate"));
			$this->CreatedDate->CurrentValue = ew_UnFormatDateTime($this->CreatedDate->CurrentValue, 6);
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->PensionerID->CurrentValue = $this->PensionerID->FormValue;
		$this->PayrollYear->CurrentValue = $this->PayrollYear->FormValue;
		$this->cMonth->CurrentValue = $this->cMonth->FormValue;
		$this->amount->CurrentValue = $this->amount->FormValue;
		$this->paymentmodeID->CurrentValue = $this->paymentmodeID->FormValue;
		$this->Approved->CurrentValue = $this->Approved->FormValue;
		$this->Claimed->CurrentValue = $this->Claimed->FormValue;
		$this->Createdby->CurrentValue = $this->Createdby->FormValue;
		$this->CreatedDate->CurrentValue = $this->CreatedDate->FormValue;
		$this->CreatedDate->CurrentValue = ew_UnFormatDateTime($this->CreatedDate->CurrentValue, 6);
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
		$this->PayrollID->setDbValue($rs->fields('PayrollID'));
		$this->PensionerID->setDbValue($rs->fields('PensionerID'));
		$this->PayrollYear->setDbValue($rs->fields('PayrollYear'));
		$this->cMonth->setDbValue($rs->fields('cMonth'));
		$this->amount->setDbValue($rs->fields('amount'));
		$this->paymentmodeID->setDbValue($rs->fields('paymentmodeID'));
		$this->Approved->setDbValue($rs->fields('Approved'));
		$this->Claimed->setDbValue($rs->fields('Claimed'));
		$this->Createdby->setDbValue($rs->fields('Createdby'));
		$this->CreatedDate->setDbValue($rs->fields('CreatedDate'));
		$this->UpdatedBy->setDbValue($rs->fields('UpdatedBy'));
		$this->UpdatedDate->setDbValue($rs->fields('UpdatedDate'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->PayrollID->DbValue = $row['PayrollID'];
		$this->PensionerID->DbValue = $row['PensionerID'];
		$this->PayrollYear->DbValue = $row['PayrollYear'];
		$this->cMonth->DbValue = $row['cMonth'];
		$this->amount->DbValue = $row['amount'];
		$this->paymentmodeID->DbValue = $row['paymentmodeID'];
		$this->Approved->DbValue = $row['Approved'];
		$this->Claimed->DbValue = $row['Claimed'];
		$this->Createdby->DbValue = $row['Createdby'];
		$this->CreatedDate->DbValue = $row['CreatedDate'];
		$this->UpdatedBy->DbValue = $row['UpdatedBy'];
		$this->UpdatedDate->DbValue = $row['UpdatedDate'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("PayrollID")) <> "")
			$this->PayrollID->CurrentValue = $this->getKey("PayrollID"); // PayrollID
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
		// Convert decimal values if posted back

		if ($this->amount->FormValue == $this->amount->CurrentValue && is_numeric(ew_StrToFloat($this->amount->CurrentValue)))
			$this->amount->CurrentValue = ew_StrToFloat($this->amount->CurrentValue);

		// Call Row_Rendering event
		$this->Row_Rendering();

		// Common render codes for all row types
		// PayrollID
		// PensionerID
		// PayrollYear
		// cMonth
		// amount
		// paymentmodeID
		// Approved
		// Claimed
		// Createdby
		// CreatedDate
		// UpdatedBy
		// UpdatedDate

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// PayrollID
			$this->PayrollID->ViewValue = $this->PayrollID->CurrentValue;
			$this->PayrollID->ViewCustomAttributes = "";

			// PensionerID
			if (strval($this->PensionerID->CurrentValue) <> "") {
				$sFilterWrk = "`PensionerID`" . ew_SearchString("=", $this->PensionerID->CurrentValue, EW_DATATYPE_STRING);
			$sSqlWrk = "SELECT `PensionerID`, `lastname` AS `DispFld`, `firstname` AS `Disp2Fld`, `middlename` AS `Disp3Fld`, `extname` AS `Disp4Fld` FROM `tbl_pensioner`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->PensionerID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `lastname` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->PensionerID->ViewValue = $rswrk->fields('DispFld');
					$this->PensionerID->ViewValue .= ew_ValueSeparator(1,$this->PensionerID) . $rswrk->fields('Disp2Fld');
					$this->PensionerID->ViewValue .= ew_ValueSeparator(2,$this->PensionerID) . $rswrk->fields('Disp3Fld');
					$this->PensionerID->ViewValue .= ew_ValueSeparator(3,$this->PensionerID) . $rswrk->fields('Disp4Fld');
					$rswrk->Close();
				} else {
					$this->PensionerID->ViewValue = $this->PensionerID->CurrentValue;
				}
			} else {
				$this->PensionerID->ViewValue = NULL;
			}
			$this->PensionerID->ViewCustomAttributes = "";

			// PayrollYear
			if (strval($this->PayrollYear->CurrentValue) <> "") {
				$sFilterWrk = "`Year`" . ew_SearchString("=", $this->PayrollYear->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `Year`, `Year` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_year`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->PayrollYear, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `Year` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->PayrollYear->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->PayrollYear->ViewValue = $this->PayrollYear->CurrentValue;
				}
			} else {
				$this->PayrollYear->ViewValue = NULL;
			}
			$this->PayrollYear->ViewCustomAttributes = "";

			// cMonth
			if (strval($this->cMonth->CurrentValue) <> "") {
				$sFilterWrk = "`MonthID`" . ew_SearchString("=", $this->cMonth->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `MonthID`, `desc` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_month`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->cMonth, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `MonthID` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->cMonth->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->cMonth->ViewValue = $this->cMonth->CurrentValue;
				}
			} else {
				$this->cMonth->ViewValue = NULL;
			}
			$this->cMonth->ViewCustomAttributes = "";

			// amount
			$this->amount->ViewValue = $this->amount->CurrentValue;
			$this->amount->ViewCustomAttributes = "";

			// paymentmodeID
			if (strval($this->paymentmodeID->CurrentValue) <> "") {
				$sFilterWrk = "`paymentmodeID`" . ew_SearchString("=", $this->paymentmodeID->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `paymentmodeID`, `description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_paymentmode`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->paymentmodeID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `paymentmodeID` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->paymentmodeID->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->paymentmodeID->ViewValue = $this->paymentmodeID->CurrentValue;
				}
			} else {
				$this->paymentmodeID->ViewValue = NULL;
			}
			$this->paymentmodeID->ViewCustomAttributes = "";

			// Approved
			if (strval($this->Approved->CurrentValue) <> "") {
				switch ($this->Approved->CurrentValue) {
					case $this->Approved->FldTagValue(1):
						$this->Approved->ViewValue = $this->Approved->FldTagCaption(1) <> "" ? $this->Approved->FldTagCaption(1) : $this->Approved->CurrentValue;
						break;
					case $this->Approved->FldTagValue(2):
						$this->Approved->ViewValue = $this->Approved->FldTagCaption(2) <> "" ? $this->Approved->FldTagCaption(2) : $this->Approved->CurrentValue;
						break;
					default:
						$this->Approved->ViewValue = $this->Approved->CurrentValue;
				}
			} else {
				$this->Approved->ViewValue = NULL;
			}
			$this->Approved->ViewCustomAttributes = "";

			// Claimed
			if (strval($this->Claimed->CurrentValue) <> "") {
				switch ($this->Claimed->CurrentValue) {
					case $this->Claimed->FldTagValue(1):
						$this->Claimed->ViewValue = $this->Claimed->FldTagCaption(1) <> "" ? $this->Claimed->FldTagCaption(1) : $this->Claimed->CurrentValue;
						break;
					case $this->Claimed->FldTagValue(2):
						$this->Claimed->ViewValue = $this->Claimed->FldTagCaption(2) <> "" ? $this->Claimed->FldTagCaption(2) : $this->Claimed->CurrentValue;
						break;
					default:
						$this->Claimed->ViewValue = $this->Claimed->CurrentValue;
				}
			} else {
				$this->Claimed->ViewValue = NULL;
			}
			$this->Claimed->ViewCustomAttributes = "";

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

			// PayrollYear
			$this->PayrollYear->LinkCustomAttributes = "";
			$this->PayrollYear->HrefValue = "";
			$this->PayrollYear->TooltipValue = "";

			// cMonth
			$this->cMonth->LinkCustomAttributes = "";
			$this->cMonth->HrefValue = "";
			$this->cMonth->TooltipValue = "";

			// amount
			$this->amount->LinkCustomAttributes = "";
			$this->amount->HrefValue = "";
			$this->amount->TooltipValue = "";

			// paymentmodeID
			$this->paymentmodeID->LinkCustomAttributes = "";
			$this->paymentmodeID->HrefValue = "";
			$this->paymentmodeID->TooltipValue = "";

			// Approved
			$this->Approved->LinkCustomAttributes = "";
			$this->Approved->HrefValue = "";
			$this->Approved->TooltipValue = "";

			// Claimed
			$this->Claimed->LinkCustomAttributes = "";
			$this->Claimed->HrefValue = "";
			$this->Claimed->TooltipValue = "";

			// Createdby
			$this->Createdby->LinkCustomAttributes = "";
			$this->Createdby->HrefValue = "";
			$this->Createdby->TooltipValue = "";

			// CreatedDate
			$this->CreatedDate->LinkCustomAttributes = "";
			$this->CreatedDate->HrefValue = "";
			$this->CreatedDate->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// PensionerID
			$this->PensionerID->EditCustomAttributes = "";
			if (trim(strval($this->PensionerID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`PensionerID`" . ew_SearchString("=", $this->PensionerID->CurrentValue, EW_DATATYPE_STRING);
			}
			$sSqlWrk = "SELECT `PensionerID`, `lastname` AS `DispFld`, `firstname` AS `Disp2Fld`, `middlename` AS `Disp3Fld`, `extname` AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `tbl_pensioner`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->PensionerID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `lastname` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->PensionerID->EditValue = $arwrk;

			// PayrollYear
			$this->PayrollYear->EditCustomAttributes = "";
			if (trim(strval($this->PayrollYear->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Year`" . ew_SearchString("=", $this->PayrollYear->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `Year`, `Year` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_year`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->PayrollYear, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `Year` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->PayrollYear->EditValue = $arwrk;

			// cMonth
			$this->cMonth->EditCustomAttributes = "";
			if (trim(strval($this->cMonth->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`MonthID`" . ew_SearchString("=", $this->cMonth->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `MonthID`, `desc` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_month`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->cMonth, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `MonthID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->cMonth->EditValue = $arwrk;

			// amount
			$this->amount->EditCustomAttributes = "";
			$this->amount->EditValue = ew_HtmlEncode($this->amount->CurrentValue);
			$this->amount->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->amount->FldCaption()));
			if (strval($this->amount->EditValue) <> "" && is_numeric($this->amount->EditValue)) $this->amount->EditValue = ew_FormatNumber($this->amount->EditValue, -2, -1, -2, 0);

			// paymentmodeID
			$this->paymentmodeID->EditCustomAttributes = "";
			if (trim(strval($this->paymentmodeID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`paymentmodeID`" . ew_SearchString("=", $this->paymentmodeID->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `paymentmodeID`, `description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_paymentmode`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->paymentmodeID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `paymentmodeID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->paymentmodeID->EditValue = $arwrk;

			// Approved
			$this->Approved->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->Approved->FldTagValue(1), $this->Approved->FldTagCaption(1) <> "" ? $this->Approved->FldTagCaption(1) : $this->Approved->FldTagValue(1));
			$arwrk[] = array($this->Approved->FldTagValue(2), $this->Approved->FldTagCaption(2) <> "" ? $this->Approved->FldTagCaption(2) : $this->Approved->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->Approved->EditValue = $arwrk;

			// Claimed
			$this->Claimed->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->Claimed->FldTagValue(1), $this->Claimed->FldTagCaption(1) <> "" ? $this->Claimed->FldTagCaption(1) : $this->Claimed->FldTagValue(1));
			$arwrk[] = array($this->Claimed->FldTagValue(2), $this->Claimed->FldTagCaption(2) <> "" ? $this->Claimed->FldTagCaption(2) : $this->Claimed->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->Claimed->EditValue = $arwrk;

			// Createdby
			// CreatedDate
			// Edit refer script
			// PensionerID

			$this->PensionerID->HrefValue = "";

			// PayrollYear
			$this->PayrollYear->HrefValue = "";

			// cMonth
			$this->cMonth->HrefValue = "";

			// amount
			$this->amount->HrefValue = "";

			// paymentmodeID
			$this->paymentmodeID->HrefValue = "";

			// Approved
			$this->Approved->HrefValue = "";

			// Claimed
			$this->Claimed->HrefValue = "";

			// Createdby
			$this->Createdby->HrefValue = "";

			// CreatedDate
			$this->CreatedDate->HrefValue = "";
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
		if (!$this->PensionerID->FldIsDetailKey && !is_null($this->PensionerID->FormValue) && $this->PensionerID->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->PensionerID->FldCaption());
		}
		if (!$this->PayrollYear->FldIsDetailKey && !is_null($this->PayrollYear->FormValue) && $this->PayrollYear->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->PayrollYear->FldCaption());
		}
		if (!$this->cMonth->FldIsDetailKey && !is_null($this->cMonth->FormValue) && $this->cMonth->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->cMonth->FldCaption());
		}
		if (!$this->amount->FldIsDetailKey && !is_null($this->amount->FormValue) && $this->amount->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->amount->FldCaption());
		}
		if (!ew_CheckNumber($this->amount->FormValue)) {
			ew_AddMessage($gsFormError, $this->amount->FldErrMsg());
		}
		if (!$this->paymentmodeID->FldIsDetailKey && !is_null($this->paymentmodeID->FormValue) && $this->paymentmodeID->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->paymentmodeID->FldCaption());
		}
		if (!$this->Approved->FldIsDetailKey && !is_null($this->Approved->FormValue) && $this->Approved->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->Approved->FldCaption());
		}
		if (!$this->Claimed->FldIsDetailKey && !is_null($this->Claimed->FormValue) && $this->Claimed->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->Claimed->FldCaption());
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

		// PayrollYear
		$this->PayrollYear->SetDbValueDef($rsnew, $this->PayrollYear->CurrentValue, 0, FALSE);

		// cMonth
		$this->cMonth->SetDbValueDef($rsnew, $this->cMonth->CurrentValue, NULL, FALSE);

		// amount
		$this->amount->SetDbValueDef($rsnew, $this->amount->CurrentValue, NULL, FALSE);

		// paymentmodeID
		$this->paymentmodeID->SetDbValueDef($rsnew, $this->paymentmodeID->CurrentValue, 0, strval($this->paymentmodeID->CurrentValue) == "");

		// Approved
		$this->Approved->SetDbValueDef($rsnew, $this->Approved->CurrentValue, 0, strval($this->Approved->CurrentValue) == "");

		// Claimed
		$this->Claimed->SetDbValueDef($rsnew, $this->Claimed->CurrentValue, 0, strval($this->Claimed->CurrentValue) == "");

		// Createdby
		$this->Createdby->SetDbValueDef($rsnew, CurrentUserID(), NULL);
		$rsnew['Createdby'] = &$this->Createdby->DbValue;

		// CreatedDate
		$this->CreatedDate->SetDbValueDef($rsnew, ew_CurrentDateTime(), NULL);
		$rsnew['CreatedDate'] = &$this->CreatedDate->DbValue;

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
			$this->PayrollID->setDbValue($conn->Insert_ID());
			$rsnew['PayrollID'] = $this->PayrollID->DbValue;
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
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "tbl_pension_payrolllist.php", $this->TableVar);
		$PageCaption = ($this->CurrentAction == "C") ? $Language->Phrase("Copy") : $Language->Phrase("Add");
		$Breadcrumb->Add("add", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", ew_CurrentUrl(), $this->TableVar);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'tbl_pension_payroll';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (add page)
	function WriteAuditTrailOnAdd(&$rs) {
		if (!$this->AuditTrailOnAdd) return;
		$table = 'tbl_pension_payroll';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['PayrollID'];

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
if (!isset($tbl_pension_payroll_add)) $tbl_pension_payroll_add = new ctbl_pension_payroll_add();

// Page init
$tbl_pension_payroll_add->Page_Init();

// Page main
$tbl_pension_payroll_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$tbl_pension_payroll_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var tbl_pension_payroll_add = new ew_Page("tbl_pension_payroll_add");
tbl_pension_payroll_add.PageID = "add"; // Page ID
var EW_PAGE_ID = tbl_pension_payroll_add.PageID; // For backward compatibility

// Form object
var ftbl_pension_payrolladd = new ew_Form("ftbl_pension_payrolladd");

// Validate form
ftbl_pension_payrolladd.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_PensionerID");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($tbl_pension_payroll->PensionerID->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_PayrollYear");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($tbl_pension_payroll->PayrollYear->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_cMonth");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($tbl_pension_payroll->cMonth->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_amount");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($tbl_pension_payroll->amount->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_amount");
			if (elm && !ew_CheckNumber(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($tbl_pension_payroll->amount->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_paymentmodeID");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($tbl_pension_payroll->paymentmodeID->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_Approved");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($tbl_pension_payroll->Approved->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_Claimed");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($tbl_pension_payroll->Claimed->FldCaption()) ?>");

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
ftbl_pension_payrolladd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftbl_pension_payrolladd.ValidateRequired = true;
<?php } else { ?>
ftbl_pension_payrolladd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
ftbl_pension_payrolladd.Lists["x_PensionerID"] = {"LinkField":"x_PensionerID","Ajax":true,"AutoFill":false,"DisplayFields":["x_lastname","x_firstname","x_middlename","x_extname"],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pension_payrolladd.Lists["x_PayrollYear"] = {"LinkField":"x_Year","Ajax":true,"AutoFill":false,"DisplayFields":["x_Year","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pension_payrolladd.Lists["x_cMonth"] = {"LinkField":"x_MonthID","Ajax":true,"AutoFill":false,"DisplayFields":["x_desc","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pension_payrolladd.Lists["x_paymentmodeID"] = {"LinkField":"x_paymentmodeID","Ajax":true,"AutoFill":false,"DisplayFields":["x_description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php //$Breadcrumb->Render(); ?>
<?php $tbl_pension_payroll_add->ShowPageHeader(); ?>
<?php
$tbl_pension_payroll_add->ShowMessage();
?>
<form name="ftbl_pension_payrolladd" id="ftbl_pension_payrolladd" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="tbl_pension_payroll">
<input type="hidden" name="a_add" id="a_add" value="A">
<table cellspacing="0" class="ewGrid"><tr><td>
<table id="tbl_tbl_pension_payrolladd" class="table table-bordered table-striped">
<?php if ($tbl_pension_payroll->PensionerID->Visible) { // PensionerID ?>
	<tr id="r_PensionerID">
		<td><span id="elh_tbl_pension_payroll_PensionerID"><?php echo $tbl_pension_payroll->PensionerID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $tbl_pension_payroll->PensionerID->CellAttributes() ?>>
<span id="el_tbl_pension_payroll_PensionerID" class="control-group">
<select data-field="x_PensionerID" id="x_PensionerID" name="x_PensionerID"<?php echo $tbl_pension_payroll->PensionerID->EditAttributes() ?>>
<?php
if (is_array($tbl_pension_payroll->PensionerID->EditValue)) {
	$arwrk = $tbl_pension_payroll->PensionerID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_pension_payroll->PensionerID->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
<?php if ($arwrk[$rowcntwrk][2] <> "") { ?>
<?php echo ew_ValueSeparator(1,$tbl_pension_payroll->PensionerID) ?><?php echo $arwrk[$rowcntwrk][2] ?>
<?php } ?>
<?php if ($arwrk[$rowcntwrk][3] <> "") { ?>
<?php echo ew_ValueSeparator(2,$tbl_pension_payroll->PensionerID) ?><?php echo $arwrk[$rowcntwrk][3] ?>
<?php } ?>
<?php if ($arwrk[$rowcntwrk][4] <> "") { ?>
<?php echo ew_ValueSeparator(3,$tbl_pension_payroll->PensionerID) ?><?php echo $arwrk[$rowcntwrk][4] ?>
<?php } ?>
</option>
<?php
	}
}
?>
</select>
<?php
$sSqlWrk = "SELECT `PensionerID`, `lastname` AS `DispFld`, `firstname` AS `Disp2Fld`, `middlename` AS `Disp3Fld`, `extname` AS `Disp4Fld` FROM `tbl_pensioner`";
$sWhereWrk = "";

// Call Lookup selecting
$tbl_pension_payroll->Lookup_Selecting($tbl_pension_payroll->PensionerID, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `lastname` ASC";
?>
<input type="hidden" name="s_x_PensionerID" id="s_x_PensionerID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`PensionerID` = {filter_value}"); ?>&t0=200">
</span>
<?php echo $tbl_pension_payroll->PensionerID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_pension_payroll->PayrollYear->Visible) { // PayrollYear ?>
	<tr id="r_PayrollYear">
		<td><span id="elh_tbl_pension_payroll_PayrollYear"><?php echo $tbl_pension_payroll->PayrollYear->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $tbl_pension_payroll->PayrollYear->CellAttributes() ?>>
<span id="el_tbl_pension_payroll_PayrollYear" class="control-group">
<select data-field="x_PayrollYear" id="x_PayrollYear" name="x_PayrollYear"<?php echo $tbl_pension_payroll->PayrollYear->EditAttributes() ?>>
<?php
if (is_array($tbl_pension_payroll->PayrollYear->EditValue)) {
	$arwrk = $tbl_pension_payroll->PayrollYear->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_pension_payroll->PayrollYear->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
<?php
$sSqlWrk = "SELECT `Year`, `Year` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_year`";
$sWhereWrk = "";

// Call Lookup selecting
$tbl_pension_payroll->Lookup_Selecting($tbl_pension_payroll->PayrollYear, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `Year` ASC";
?>
<input type="hidden" name="s_x_PayrollYear" id="s_x_PayrollYear" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`Year` = {filter_value}"); ?>&t0=3">
</span>
<?php echo $tbl_pension_payroll->PayrollYear->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_pension_payroll->cMonth->Visible) { // cMonth ?>
	<tr id="r_cMonth">
		<td><span id="elh_tbl_pension_payroll_cMonth"><?php echo $tbl_pension_payroll->cMonth->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $tbl_pension_payroll->cMonth->CellAttributes() ?>>
<span id="el_tbl_pension_payroll_cMonth" class="control-group">
<select data-field="x_cMonth" id="x_cMonth" name="x_cMonth"<?php echo $tbl_pension_payroll->cMonth->EditAttributes() ?>>
<?php
if (is_array($tbl_pension_payroll->cMonth->EditValue)) {
	$arwrk = $tbl_pension_payroll->cMonth->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_pension_payroll->cMonth->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
<?php
$sSqlWrk = "SELECT `MonthID`, `desc` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_month`";
$sWhereWrk = "";

// Call Lookup selecting
$tbl_pension_payroll->Lookup_Selecting($tbl_pension_payroll->cMonth, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `MonthID` ASC";
?>
<input type="hidden" name="s_x_cMonth" id="s_x_cMonth" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`MonthID` = {filter_value}"); ?>&t0=3">
</span>
<?php echo $tbl_pension_payroll->cMonth->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_pension_payroll->amount->Visible) { // amount ?>
	<tr id="r_amount">
		<td><span id="elh_tbl_pension_payroll_amount"><?php echo $tbl_pension_payroll->amount->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $tbl_pension_payroll->amount->CellAttributes() ?>>
<span id="el_tbl_pension_payroll_amount" class="control-group">
<input type="text" data-field="x_amount" name="x_amount" id="x_amount" size="30" placeholder="<?php echo $tbl_pension_payroll->amount->PlaceHolder ?>" value="<?php echo $tbl_pension_payroll->amount->EditValue ?>"<?php echo $tbl_pension_payroll->amount->EditAttributes() ?>>
</span>
<?php echo $tbl_pension_payroll->amount->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_pension_payroll->paymentmodeID->Visible) { // paymentmodeID ?>
	<tr id="r_paymentmodeID">
		<td><span id="elh_tbl_pension_payroll_paymentmodeID"><?php echo $tbl_pension_payroll->paymentmodeID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $tbl_pension_payroll->paymentmodeID->CellAttributes() ?>>
<span id="el_tbl_pension_payroll_paymentmodeID" class="control-group">
<select data-field="x_paymentmodeID" id="x_paymentmodeID" name="x_paymentmodeID"<?php echo $tbl_pension_payroll->paymentmodeID->EditAttributes() ?>>
<?php
if (is_array($tbl_pension_payroll->paymentmodeID->EditValue)) {
	$arwrk = $tbl_pension_payroll->paymentmodeID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_pension_payroll->paymentmodeID->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
<?php
$sSqlWrk = "SELECT `paymentmodeID`, `description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_paymentmode`";
$sWhereWrk = "";

// Call Lookup selecting
$tbl_pension_payroll->Lookup_Selecting($tbl_pension_payroll->paymentmodeID, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `paymentmodeID` ASC";
?>
<input type="hidden" name="s_x_paymentmodeID" id="s_x_paymentmodeID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`paymentmodeID` = {filter_value}"); ?>&t0=3">
</span>
<?php echo $tbl_pension_payroll->paymentmodeID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_pension_payroll->Approved->Visible) { // Approved ?>
	<tr id="r_Approved">
		<td><span id="elh_tbl_pension_payroll_Approved"><?php echo $tbl_pension_payroll->Approved->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $tbl_pension_payroll->Approved->CellAttributes() ?>>
<span id="el_tbl_pension_payroll_Approved" class="control-group">
<select data-field="x_Approved" id="x_Approved" name="x_Approved"<?php echo $tbl_pension_payroll->Approved->EditAttributes() ?>>
<?php
if (is_array($tbl_pension_payroll->Approved->EditValue)) {
	$arwrk = $tbl_pension_payroll->Approved->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_pension_payroll->Approved->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
</span>
<?php echo $tbl_pension_payroll->Approved->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_pension_payroll->Claimed->Visible) { // Claimed ?>
	<tr id="r_Claimed">
		<td><span id="elh_tbl_pension_payroll_Claimed"><?php echo $tbl_pension_payroll->Claimed->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $tbl_pension_payroll->Claimed->CellAttributes() ?>>
<span id="el_tbl_pension_payroll_Claimed" class="control-group">
<select data-field="x_Claimed" id="x_Claimed" name="x_Claimed"<?php echo $tbl_pension_payroll->Claimed->EditAttributes() ?>>
<?php
if (is_array($tbl_pension_payroll->Claimed->EditValue)) {
	$arwrk = $tbl_pension_payroll->Claimed->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_pension_payroll->Claimed->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
</span>
<?php echo $tbl_pension_payroll->Claimed->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
</form>
<script type="text/javascript">
ftbl_pension_payrolladd.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$tbl_pension_payroll_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$tbl_pension_payroll_add->Page_Terminate();
?>
