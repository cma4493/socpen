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

$tbl_pension_payroll_edit = NULL; // Initialize page object first

class ctbl_pension_payroll_edit extends ctbl_pension_payroll {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Table name
	var $TableName = 'tbl_pension_payroll';

	// Page object name
	var $PageObjName = 'tbl_pension_payroll_edit';

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

		// Table object (tbl_pension_payroll)
		if (!isset($GLOBALS["tbl_pension_payroll"])) {
			$GLOBALS["tbl_pension_payroll"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["tbl_pension_payroll"];
		}

		// Table object (tbl_user)
		if (!isset($GLOBALS['tbl_user'])) $GLOBALS['tbl_user'] = new ctbl_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'edit', TRUE);

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
		if (!$Security->CanEdit()) {
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
		$this->PayrollID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
		if (@$_GET["PayrollID"] <> "") {
			$this->PayrollID->setQueryStringValue($_GET["PayrollID"]);
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
		if ($this->PayrollID->CurrentValue == "")
			$this->Page_Terminate("tbl_pension_payrolllist.php"); // Invalid key, return to list

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
					$this->Page_Terminate("tbl_pension_payrolllist.php"); // No matching record, return to list
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
		if (!$this->PayrollID->FldIsDetailKey)
			$this->PayrollID->setFormValue($objForm->GetValue("x_PayrollID"));
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
		$this->LoadRow();
		$this->PayrollID->CurrentValue = $this->PayrollID->FormValue;
		$this->PensionerID->CurrentValue = $this->PensionerID->FormValue;
		$this->PayrollYear->CurrentValue = $this->PayrollYear->FormValue;
		$this->cMonth->CurrentValue = $this->cMonth->FormValue;
		$this->amount->CurrentValue = $this->amount->FormValue;
		$this->paymentmodeID->CurrentValue = $this->paymentmodeID->FormValue;
		$this->Approved->CurrentValue = $this->Approved->FormValue;
		$this->Claimed->CurrentValue = $this->Claimed->FormValue;
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

			// PayrollID
			$this->PayrollID->LinkCustomAttributes = "";
			$this->PayrollID->HrefValue = "";
			$this->PayrollID->TooltipValue = "";

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

			// UpdatedBy
			$this->UpdatedBy->LinkCustomAttributes = "";
			$this->UpdatedBy->HrefValue = "";
			$this->UpdatedBy->TooltipValue = "";

			// UpdatedDate
			$this->UpdatedDate->LinkCustomAttributes = "";
			$this->UpdatedDate->HrefValue = "";
			$this->UpdatedDate->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// PayrollID
			$this->PayrollID->EditCustomAttributes = "";
			$this->PayrollID->EditValue = $this->PayrollID->CurrentValue;
			$this->PayrollID->ViewCustomAttributes = "";

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

			// UpdatedBy
			// UpdatedDate
			// Edit refer script
			// PayrollID

			$this->PayrollID->HrefValue = "";

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

	// Update record based on key values
	function EditRow() {
		global $conn, $Security, $Language;
		$sFilter = $this->KeyFilter();
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

			// PensionerID
			$this->PensionerID->SetDbValueDef($rsnew, $this->PensionerID->CurrentValue, NULL, $this->PensionerID->ReadOnly);

			// PayrollYear
			$this->PayrollYear->SetDbValueDef($rsnew, $this->PayrollYear->CurrentValue, 0, $this->PayrollYear->ReadOnly);

			// cMonth
			$this->cMonth->SetDbValueDef($rsnew, $this->cMonth->CurrentValue, NULL, $this->cMonth->ReadOnly);

			// amount
			$this->amount->SetDbValueDef($rsnew, $this->amount->CurrentValue, NULL, $this->amount->ReadOnly);

			// paymentmodeID
			$this->paymentmodeID->SetDbValueDef($rsnew, $this->paymentmodeID->CurrentValue, 0, $this->paymentmodeID->ReadOnly);

			// Approved
			$this->Approved->SetDbValueDef($rsnew, $this->Approved->CurrentValue, 0, $this->Approved->ReadOnly);

			// Claimed
			$this->Claimed->SetDbValueDef($rsnew, $this->Claimed->CurrentValue, 0, $this->Claimed->ReadOnly);

			// UpdatedBy
			$this->UpdatedBy->SetDbValueDef($rsnew, CurrentUserID(), NULL);
			$rsnew['UpdatedBy'] = &$this->UpdatedBy->DbValue;

			// UpdatedDate
			$this->UpdatedDate->SetDbValueDef($rsnew, ew_CurrentDateTime(), NULL);
			$rsnew['UpdatedDate'] = &$this->UpdatedDate->DbValue;

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
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "tbl_pension_payrolllist.php", $this->TableVar);
		$PageCaption = $Language->Phrase("edit");
		$Breadcrumb->Add("edit", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", ew_CurrentUrl(), $this->TableVar);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'tbl_pension_payroll';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (edit page)
	function WriteAuditTrailOnEdit(&$rsold, &$rsnew) {
		if (!$this->AuditTrailOnEdit) return;
		$table = 'tbl_pension_payroll';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rsold['PayrollID'];

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
if (!isset($tbl_pension_payroll_edit)) $tbl_pension_payroll_edit = new ctbl_pension_payroll_edit();

// Page init
$tbl_pension_payroll_edit->Page_Init();

// Page main
$tbl_pension_payroll_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$tbl_pension_payroll_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var tbl_pension_payroll_edit = new ew_Page("tbl_pension_payroll_edit");
tbl_pension_payroll_edit.PageID = "edit"; // Page ID
var EW_PAGE_ID = tbl_pension_payroll_edit.PageID; // For backward compatibility

// Form object
var ftbl_pension_payrolledit = new ew_Form("ftbl_pension_payrolledit");

// Validate form
ftbl_pension_payrolledit.Validate = function() {
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
ftbl_pension_payrolledit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftbl_pension_payrolledit.ValidateRequired = true;
<?php } else { ?>
ftbl_pension_payrolledit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
ftbl_pension_payrolledit.Lists["x_PensionerID"] = {"LinkField":"x_PensionerID","Ajax":true,"AutoFill":false,"DisplayFields":["x_lastname","x_firstname","x_middlename","x_extname"],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pension_payrolledit.Lists["x_PayrollYear"] = {"LinkField":"x_Year","Ajax":true,"AutoFill":false,"DisplayFields":["x_Year","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pension_payrolledit.Lists["x_cMonth"] = {"LinkField":"x_MonthID","Ajax":true,"AutoFill":false,"DisplayFields":["x_desc","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pension_payrolledit.Lists["x_paymentmodeID"] = {"LinkField":"x_paymentmodeID","Ajax":true,"AutoFill":false,"DisplayFields":["x_description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php //$Breadcrumb->Render(); ?>
<?php $tbl_pension_payroll_edit->ShowPageHeader(); ?>
<?php
$tbl_pension_payroll_edit->ShowMessage();
?>
<form name="ftbl_pension_payrolledit" id="ftbl_pension_payrolledit" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="tbl_pension_payroll">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<table cellspacing="0" class="ewGrid"><tr><td>
<table id="tbl_tbl_pension_payrolledit" class="table table-bordered table-striped">
<?php if ($tbl_pension_payroll->PayrollID->Visible) { // PayrollID ?>
	<tr id="r_PayrollID">
		<td><span id="elh_tbl_pension_payroll_PayrollID"><?php echo $tbl_pension_payroll->PayrollID->FldCaption() ?></span></td>
		<td<?php echo $tbl_pension_payroll->PayrollID->CellAttributes() ?>>
<span id="el_tbl_pension_payroll_PayrollID" class="control-group">
<span<?php echo $tbl_pension_payroll->PayrollID->ViewAttributes() ?>>
<?php echo $tbl_pension_payroll->PayrollID->EditValue ?></span>
</span>
<input type="hidden" data-field="x_PayrollID" name="x_PayrollID" id="x_PayrollID" value="<?php echo ew_HtmlEncode($tbl_pension_payroll->PayrollID->CurrentValue) ?>">
<?php echo $tbl_pension_payroll->PayrollID->CustomMsg ?></td>
	</tr>
<?php } ?>
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
<button class="btn btn-success btn-sm" name="btnAction" id="btnAction" type="submit"><?php echo " <i class='icon-save align-top bigger-125'></i> " . "Update Changes"; ?></button>
</form>
<script type="text/javascript">
ftbl_pension_payrolledit.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$tbl_pension_payroll_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$tbl_pension_payroll_edit->Page_Terminate();
?>
