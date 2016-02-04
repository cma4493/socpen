<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "tbl_supportinfo.php" ?>
<?php include_once "tbl_pensionerinfo.php" ?>
<?php include_once "tbl_userinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$tbl_support_edit = NULL; // Initialize page object first

class ctbl_support_edit extends ctbl_support {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Table name
	var $TableName = 'tbl_support';

	// Page object name
	var $PageObjName = 'tbl_support_edit';

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

		// Table object (tbl_support)
		if (!isset($GLOBALS["tbl_support"])) {
			$GLOBALS["tbl_support"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["tbl_support"];
		}

		// Table object (tbl_pensioner)
		if (!isset($GLOBALS['tbl_pensioner'])) $GLOBALS['tbl_pensioner'] = new ctbl_pensioner();

		// Table object (tbl_user)
		if (!isset($GLOBALS['tbl_user'])) $GLOBALS['tbl_user'] = new ctbl_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'edit', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'tbl_support', TRUE);

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
			$this->Page_Terminate("tbl_supportlist.php");
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
		$this->supportID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
		if (@$_GET["supportID"] <> "") {
			$this->supportID->setQueryStringValue($_GET["supportID"]);
		}

		// Set up master detail parameters
		$this->SetUpMasterParms();

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
		if ($this->supportID->CurrentValue == "")
			$this->Page_Terminate("tbl_supportlist.php"); // Invalid key, return to list

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
					$this->Page_Terminate("tbl_supportlist.php"); // No matching record, return to list
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
		if (!$this->supportID->FldIsDetailKey)
			$this->supportID->setFormValue($objForm->GetValue("x_supportID"));
		if (!$this->PensionerID->FldIsDetailKey) {
			$this->PensionerID->setFormValue($objForm->GetValue("x_PensionerID"));
		}
		if (!$this->family_support->FldIsDetailKey) {
			$this->family_support->setFormValue($objForm->GetValue("x_family_support"));
		}
		if (!$this->KindSupID->FldIsDetailKey) {
			$this->KindSupID->setFormValue($objForm->GetValue("x_KindSupID"));
		}
		if (!$this->meals->FldIsDetailKey) {
			$this->meals->setFormValue($objForm->GetValue("x_meals"));
		}
		if (!$this->disability->FldIsDetailKey) {
			$this->disability->setFormValue($objForm->GetValue("x_disability"));
		}
		if (!$this->disabilityID->FldIsDetailKey) {
			$this->disabilityID->setFormValue($objForm->GetValue("x_disabilityID"));
		}
		if (!$this->immobile->FldIsDetailKey) {
			$this->immobile->setFormValue($objForm->GetValue("x_immobile"));
		}
		if (!$this->assistiveID->FldIsDetailKey) {
			$this->assistiveID->setFormValue($objForm->GetValue("x_assistiveID"));
		}
		if (!$this->preEx_illness->FldIsDetailKey) {
			$this->preEx_illness->setFormValue($objForm->GetValue("x_preEx_illness"));
		}
		if (!$this->illnessID->FldIsDetailKey) {
			$this->illnessID->setFormValue($objForm->GetValue("x_illnessID"));
		}
		if (!$this->physconditionID->FldIsDetailKey) {
			$this->physconditionID->setFormValue($objForm->GetValue("x_physconditionID"));
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
		$this->supportID->CurrentValue = $this->supportID->FormValue;
		$this->PensionerID->CurrentValue = $this->PensionerID->FormValue;
		$this->family_support->CurrentValue = $this->family_support->FormValue;
		$this->KindSupID->CurrentValue = $this->KindSupID->FormValue;
		$this->meals->CurrentValue = $this->meals->FormValue;
		$this->disability->CurrentValue = $this->disability->FormValue;
		$this->disabilityID->CurrentValue = $this->disabilityID->FormValue;
		$this->immobile->CurrentValue = $this->immobile->FormValue;
		$this->assistiveID->CurrentValue = $this->assistiveID->FormValue;
		$this->preEx_illness->CurrentValue = $this->preEx_illness->FormValue;
		$this->illnessID->CurrentValue = $this->illnessID->FormValue;
		$this->physconditionID->CurrentValue = $this->physconditionID->FormValue;
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
		$this->supportID->setDbValue($rs->fields('supportID'));
		$this->PensionerID->setDbValue($rs->fields('PensionerID'));
		$this->family_support->setDbValue($rs->fields('family_support'));
		$this->KindSupID->setDbValue($rs->fields('KindSupID'));
		$this->meals->setDbValue($rs->fields('meals'));
		$this->disability->setDbValue($rs->fields('disability'));
		$this->disabilityID->setDbValue($rs->fields('disabilityID'));
		$this->immobile->setDbValue($rs->fields('immobile'));
		$this->assistiveID->setDbValue($rs->fields('assistiveID'));
		$this->preEx_illness->setDbValue($rs->fields('preEx_illness'));
		$this->illnessID->setDbValue($rs->fields('illnessID'));
		$this->physconditionID->setDbValue($rs->fields('physconditionID'));
		$this->CreatedBy->setDbValue($rs->fields('CreatedBy'));
		$this->CreatedDate->setDbValue($rs->fields('CreatedDate'));
		$this->UpdatedBy->setDbValue($rs->fields('UpdatedBy'));
		$this->UpdatedDate->setDbValue($rs->fields('UpdatedDate'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->supportID->DbValue = $row['supportID'];
		$this->PensionerID->DbValue = $row['PensionerID'];
		$this->family_support->DbValue = $row['family_support'];
		$this->KindSupID->DbValue = $row['KindSupID'];
		$this->meals->DbValue = $row['meals'];
		$this->disability->DbValue = $row['disability'];
		$this->disabilityID->DbValue = $row['disabilityID'];
		$this->immobile->DbValue = $row['immobile'];
		$this->assistiveID->DbValue = $row['assistiveID'];
		$this->preEx_illness->DbValue = $row['preEx_illness'];
		$this->illnessID->DbValue = $row['illnessID'];
		$this->physconditionID->DbValue = $row['physconditionID'];
		$this->CreatedBy->DbValue = $row['CreatedBy'];
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
		// supportID
		// PensionerID
		// family_support
		// KindSupID
		// meals
		// disability
		// disabilityID
		// immobile
		// assistiveID
		// preEx_illness
		// illnessID
		// physconditionID
		// CreatedBy
		// CreatedDate
		// UpdatedBy
		// UpdatedDate

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// supportID
			$this->supportID->ViewValue = $this->supportID->CurrentValue;
			$this->supportID->ViewCustomAttributes = "";

			// PensionerID
			$this->PensionerID->ViewValue = $this->PensionerID->CurrentValue;
			$this->PensionerID->ViewCustomAttributes = "";

			// family_support
			if (strval($this->family_support->CurrentValue) <> "") {
				switch ($this->family_support->CurrentValue) {
					case $this->family_support->FldTagValue(1):
						$this->family_support->ViewValue = $this->family_support->FldTagCaption(1) <> "" ? $this->family_support->FldTagCaption(1) : $this->family_support->CurrentValue;
						break;
					case $this->family_support->FldTagValue(2):
						$this->family_support->ViewValue = $this->family_support->FldTagCaption(2) <> "" ? $this->family_support->FldTagCaption(2) : $this->family_support->CurrentValue;
						break;
					default:
						$this->family_support->ViewValue = $this->family_support->CurrentValue;
				}
			} else {
				$this->family_support->ViewValue = NULL;
			}
			$this->family_support->ViewCustomAttributes = "";

			// KindSupID
			if (strval($this->KindSupID->CurrentValue) <> "") {
				$sFilterWrk = "`SupportID`" . ew_SearchString("=", $this->KindSupID->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `SupportID`, `SupportKind` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_support`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->KindSupID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `SupportID` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->KindSupID->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->KindSupID->ViewValue = $this->KindSupID->CurrentValue;
				}
			} else {
				$this->KindSupID->ViewValue = NULL;
			}
			$this->KindSupID->ViewCustomAttributes = "";

			// meals
			$this->meals->ViewValue = $this->meals->CurrentValue;
			$this->meals->ViewCustomAttributes = "";

			// disability
			if (strval($this->disability->CurrentValue) <> "") {
				switch ($this->disability->CurrentValue) {
					case $this->disability->FldTagValue(1):
						$this->disability->ViewValue = $this->disability->FldTagCaption(1) <> "" ? $this->disability->FldTagCaption(1) : $this->disability->CurrentValue;
						break;
					case $this->disability->FldTagValue(2):
						$this->disability->ViewValue = $this->disability->FldTagCaption(2) <> "" ? $this->disability->FldTagCaption(2) : $this->disability->CurrentValue;
						break;
					default:
						$this->disability->ViewValue = $this->disability->CurrentValue;
				}
			} else {
				$this->disability->ViewValue = NULL;
			}
			$this->disability->ViewCustomAttributes = "";

			// disabilityID
			if (strval($this->disabilityID->CurrentValue) <> "") {
				$sFilterWrk = "`disabilityID`" . ew_SearchString("=", $this->disabilityID->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `disabilityID`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_disability`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->disabilityID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `disabilityID` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->disabilityID->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->disabilityID->ViewValue = $this->disabilityID->CurrentValue;
				}
			} else {
				$this->disabilityID->ViewValue = NULL;
			}
			$this->disabilityID->ViewCustomAttributes = "";

			// immobile
			if (strval($this->immobile->CurrentValue) <> "") {
				switch ($this->immobile->CurrentValue) {
					case $this->immobile->FldTagValue(1):
						$this->immobile->ViewValue = $this->immobile->FldTagCaption(1) <> "" ? $this->immobile->FldTagCaption(1) : $this->immobile->CurrentValue;
						break;
					case $this->immobile->FldTagValue(2):
						$this->immobile->ViewValue = $this->immobile->FldTagCaption(2) <> "" ? $this->immobile->FldTagCaption(2) : $this->immobile->CurrentValue;
						break;
					default:
						$this->immobile->ViewValue = $this->immobile->CurrentValue;
				}
			} else {
				$this->immobile->ViewValue = NULL;
			}
			$this->immobile->ViewCustomAttributes = "";

			// assistiveID
			if (strval($this->assistiveID->CurrentValue) <> "") {
				$sFilterWrk = "`assistiveID`" . ew_SearchString("=", $this->assistiveID->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `assistiveID`, `Device` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_assistive`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->assistiveID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `assistiveID` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->assistiveID->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->assistiveID->ViewValue = $this->assistiveID->CurrentValue;
				}
			} else {
				$this->assistiveID->ViewValue = NULL;
			}
			$this->assistiveID->ViewCustomAttributes = "";

			// preEx_illness
			if (strval($this->preEx_illness->CurrentValue) <> "") {
				switch ($this->preEx_illness->CurrentValue) {
					case $this->preEx_illness->FldTagValue(1):
						$this->preEx_illness->ViewValue = $this->preEx_illness->FldTagCaption(1) <> "" ? $this->preEx_illness->FldTagCaption(1) : $this->preEx_illness->CurrentValue;
						break;
					case $this->preEx_illness->FldTagValue(2):
						$this->preEx_illness->ViewValue = $this->preEx_illness->FldTagCaption(2) <> "" ? $this->preEx_illness->FldTagCaption(2) : $this->preEx_illness->CurrentValue;
						break;
					default:
						$this->preEx_illness->ViewValue = $this->preEx_illness->CurrentValue;
				}
			} else {
				$this->preEx_illness->ViewValue = NULL;
			}
			$this->preEx_illness->ViewCustomAttributes = "";

			// illnessID
			if (strval($this->illnessID->CurrentValue) <> "") {
				$sFilterWrk = "`illnessID`" . ew_SearchString("=", $this->illnessID->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `illnessID`, `description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_illness`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->illnessID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `illnessID` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->illnessID->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->illnessID->ViewValue = $this->illnessID->CurrentValue;
				}
			} else {
				$this->illnessID->ViewValue = NULL;
			}
			$this->illnessID->ViewCustomAttributes = "";

			// physconditionID
			if (strval($this->physconditionID->CurrentValue) <> "") {
				$sFilterWrk = "`physconditionID`" . ew_SearchString("=", $this->physconditionID->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `physconditionID`, `physconditionName` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_physical_condition`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->physconditionID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `physconditionID` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->physconditionID->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->physconditionID->ViewValue = $this->physconditionID->CurrentValue;
				}
			} else {
				$this->physconditionID->ViewValue = NULL;
			}
			$this->physconditionID->ViewCustomAttributes = "";

			// CreatedBy
			$this->CreatedBy->ViewValue = $this->CreatedBy->CurrentValue;
			$this->CreatedBy->ViewCustomAttributes = "";

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

			// supportID
			$this->supportID->LinkCustomAttributes = "";
			$this->supportID->HrefValue = "";
			$this->supportID->TooltipValue = "";

			// PensionerID
			$this->PensionerID->LinkCustomAttributes = "";
			$this->PensionerID->HrefValue = "";
			$this->PensionerID->TooltipValue = "";

			// family_support
			$this->family_support->LinkCustomAttributes = "";
			$this->family_support->HrefValue = "";
			$this->family_support->TooltipValue = "";

			// KindSupID
			$this->KindSupID->LinkCustomAttributes = "";
			$this->KindSupID->HrefValue = "";
			$this->KindSupID->TooltipValue = "";

			// meals
			$this->meals->LinkCustomAttributes = "";
			$this->meals->HrefValue = "";
			$this->meals->TooltipValue = "";

			// disability
			$this->disability->LinkCustomAttributes = "";
			$this->disability->HrefValue = "";
			$this->disability->TooltipValue = "";

			// disabilityID
			$this->disabilityID->LinkCustomAttributes = "";
			$this->disabilityID->HrefValue = "";
			$this->disabilityID->TooltipValue = "";

			// immobile
			$this->immobile->LinkCustomAttributes = "";
			$this->immobile->HrefValue = "";
			$this->immobile->TooltipValue = "";

			// assistiveID
			$this->assistiveID->LinkCustomAttributes = "";
			$this->assistiveID->HrefValue = "";
			$this->assistiveID->TooltipValue = "";

			// preEx_illness
			$this->preEx_illness->LinkCustomAttributes = "";
			$this->preEx_illness->HrefValue = "";
			$this->preEx_illness->TooltipValue = "";

			// illnessID
			$this->illnessID->LinkCustomAttributes = "";
			$this->illnessID->HrefValue = "";
			$this->illnessID->TooltipValue = "";

			// physconditionID
			$this->physconditionID->LinkCustomAttributes = "";
			$this->physconditionID->HrefValue = "";
			$this->physconditionID->TooltipValue = "";

			// UpdatedBy
			$this->UpdatedBy->LinkCustomAttributes = "";
			$this->UpdatedBy->HrefValue = "";
			$this->UpdatedBy->TooltipValue = "";

			// UpdatedDate
			$this->UpdatedDate->LinkCustomAttributes = "";
			$this->UpdatedDate->HrefValue = "";
			$this->UpdatedDate->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// supportID
			$this->supportID->EditCustomAttributes = "";
			$this->supportID->EditValue = $this->supportID->CurrentValue;
			$this->supportID->ViewCustomAttributes = "";

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

			// family_support
			$this->family_support->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->family_support->FldTagValue(1), $this->family_support->FldTagCaption(1) <> "" ? $this->family_support->FldTagCaption(1) : $this->family_support->FldTagValue(1));
			$arwrk[] = array($this->family_support->FldTagValue(2), $this->family_support->FldTagCaption(2) <> "" ? $this->family_support->FldTagCaption(2) : $this->family_support->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->family_support->EditValue = $arwrk;

			// KindSupID
			$this->KindSupID->EditCustomAttributes = "";
			if (trim(strval($this->KindSupID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`SupportID`" . ew_SearchString("=", $this->KindSupID->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `SupportID`, `SupportKind` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_support`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->KindSupID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `SupportID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->KindSupID->EditValue = $arwrk;

			// meals
			$this->meals->EditCustomAttributes = "";
			$this->meals->EditValue = ew_HtmlEncode($this->meals->CurrentValue);
			$this->meals->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->meals->FldCaption()));

			// disability
			$this->disability->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->disability->FldTagValue(1), $this->disability->FldTagCaption(1) <> "" ? $this->disability->FldTagCaption(1) : $this->disability->FldTagValue(1));
			$arwrk[] = array($this->disability->FldTagValue(2), $this->disability->FldTagCaption(2) <> "" ? $this->disability->FldTagCaption(2) : $this->disability->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->disability->EditValue = $arwrk;

			// disabilityID
			$this->disabilityID->EditCustomAttributes = "";
			if (trim(strval($this->disabilityID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`disabilityID`" . ew_SearchString("=", $this->disabilityID->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `disabilityID`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_disability`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->disabilityID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `disabilityID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->disabilityID->EditValue = $arwrk;

			// immobile
			$this->immobile->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->immobile->FldTagValue(1), $this->immobile->FldTagCaption(1) <> "" ? $this->immobile->FldTagCaption(1) : $this->immobile->FldTagValue(1));
			$arwrk[] = array($this->immobile->FldTagValue(2), $this->immobile->FldTagCaption(2) <> "" ? $this->immobile->FldTagCaption(2) : $this->immobile->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->immobile->EditValue = $arwrk;

			// assistiveID
			$this->assistiveID->EditCustomAttributes = "";
			if (trim(strval($this->assistiveID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`assistiveID`" . ew_SearchString("=", $this->assistiveID->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `assistiveID`, `Device` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_assistive`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->assistiveID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `assistiveID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->assistiveID->EditValue = $arwrk;

			// preEx_illness
			$this->preEx_illness->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->preEx_illness->FldTagValue(1), $this->preEx_illness->FldTagCaption(1) <> "" ? $this->preEx_illness->FldTagCaption(1) : $this->preEx_illness->FldTagValue(1));
			$arwrk[] = array($this->preEx_illness->FldTagValue(2), $this->preEx_illness->FldTagCaption(2) <> "" ? $this->preEx_illness->FldTagCaption(2) : $this->preEx_illness->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->preEx_illness->EditValue = $arwrk;

			// illnessID
			$this->illnessID->EditCustomAttributes = "";
			if (trim(strval($this->illnessID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`illnessID`" . ew_SearchString("=", $this->illnessID->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `illnessID`, `description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_illness`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->illnessID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `illnessID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->illnessID->EditValue = $arwrk;

			// physconditionID
			$this->physconditionID->EditCustomAttributes = "";
			if (trim(strval($this->physconditionID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`physconditionID`" . ew_SearchString("=", $this->physconditionID->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `physconditionID`, `physconditionName` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_physical_condition`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->physconditionID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `physconditionID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->physconditionID->EditValue = $arwrk;

			// UpdatedBy
			// UpdatedDate
			// Edit refer script
			// supportID

			$this->supportID->HrefValue = "";

			// PensionerID
			$this->PensionerID->HrefValue = "";

			// family_support
			$this->family_support->HrefValue = "";

			// KindSupID
			$this->KindSupID->HrefValue = "";

			// meals
			$this->meals->HrefValue = "";

			// disability
			$this->disability->HrefValue = "";

			// disabilityID
			$this->disabilityID->HrefValue = "";

			// immobile
			$this->immobile->HrefValue = "";

			// assistiveID
			$this->assistiveID->HrefValue = "";

			// preEx_illness
			$this->preEx_illness->HrefValue = "";

			// illnessID
			$this->illnessID->HrefValue = "";

			// physconditionID
			$this->physconditionID->HrefValue = "";

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
		if (!ew_CheckInteger($this->meals->FormValue)) {
			ew_AddMessage($gsFormError, $this->meals->FldErrMsg());
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
			if ($this->PensionerID->CurrentValue <> "") { // Check field with unique index
			$sFilterChk = "(`PensionerID` = '" . ew_AdjustSql($this->PensionerID->CurrentValue) . "')";
			$sFilterChk .= " AND NOT (" . $sFilter . ")";
			$this->CurrentFilter = $sFilterChk;
			$sSqlChk = $this->SQL();
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$rsChk = $conn->Execute($sSqlChk);
			$conn->raiseErrorFn = '';
			if ($rsChk === FALSE) {
				return FALSE;
			} elseif (!$rsChk->EOF) {
				$sIdxErrMsg = str_replace("%f", $this->PensionerID->FldCaption(), $Language->Phrase("DupIndex"));
				$sIdxErrMsg = str_replace("%v", $this->PensionerID->CurrentValue, $sIdxErrMsg);
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

			// PensionerID
			$this->PensionerID->SetDbValueDef($rsnew, $this->PensionerID->CurrentValue, NULL, $this->PensionerID->ReadOnly);

			// family_support
			$this->family_support->SetDbValueDef($rsnew, $this->family_support->CurrentValue, NULL, $this->family_support->ReadOnly);

			// KindSupID
			$this->KindSupID->SetDbValueDef($rsnew, $this->KindSupID->CurrentValue, NULL, $this->KindSupID->ReadOnly);

			// meals
			$this->meals->SetDbValueDef($rsnew, $this->meals->CurrentValue, NULL, $this->meals->ReadOnly);

			// disability
			$this->disability->SetDbValueDef($rsnew, $this->disability->CurrentValue, NULL, $this->disability->ReadOnly);

			// disabilityID
			$this->disabilityID->SetDbValueDef($rsnew, $this->disabilityID->CurrentValue, NULL, $this->disabilityID->ReadOnly);

			// immobile
			$this->immobile->SetDbValueDef($rsnew, $this->immobile->CurrentValue, NULL, $this->immobile->ReadOnly);

			// assistiveID
			$this->assistiveID->SetDbValueDef($rsnew, $this->assistiveID->CurrentValue, NULL, $this->assistiveID->ReadOnly);

			// preEx_illness
			$this->preEx_illness->SetDbValueDef($rsnew, $this->preEx_illness->CurrentValue, NULL, $this->preEx_illness->ReadOnly);

			// illnessID
			$this->illnessID->SetDbValueDef($rsnew, $this->illnessID->CurrentValue, NULL, $this->illnessID->ReadOnly);

			// physconditionID
			$this->physconditionID->SetDbValueDef($rsnew, $this->physconditionID->CurrentValue, NULL, $this->physconditionID->ReadOnly);

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
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "tbl_supportlist.php", $this->TableVar);
		$PageCaption = $Language->Phrase("edit");
		$Breadcrumb->Add("edit", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", ew_CurrentUrl(), $this->TableVar);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'tbl_support';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (edit page)
	function WriteAuditTrailOnEdit(&$rsold, &$rsnew) {
		if (!$this->AuditTrailOnEdit) return;
		$table = 'tbl_support';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rsold['supportID'];

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
if (!isset($tbl_support_edit)) $tbl_support_edit = new ctbl_support_edit();

// Page init
$tbl_support_edit->Page_Init();

// Page main
$tbl_support_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$tbl_support_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var tbl_support_edit = new ew_Page("tbl_support_edit");
tbl_support_edit.PageID = "edit"; // Page ID
var EW_PAGE_ID = tbl_support_edit.PageID; // For backward compatibility

// Form object
var ftbl_supportedit = new ew_Form("ftbl_supportedit");

// Validate form
ftbl_supportedit.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_meals");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($tbl_support->meals->FldErrMsg()) ?>");

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
ftbl_supportedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftbl_supportedit.ValidateRequired = true;
<?php } else { ?>
ftbl_supportedit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
ftbl_supportedit.Lists["x_KindSupID"] = {"LinkField":"x_SupportID","Ajax":true,"AutoFill":false,"DisplayFields":["x_SupportKind","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_supportedit.Lists["x_disabilityID"] = {"LinkField":"x_disabilityID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_supportedit.Lists["x_assistiveID"] = {"LinkField":"x_assistiveID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Device","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_supportedit.Lists["x_illnessID"] = {"LinkField":"x_illnessID","Ajax":true,"AutoFill":false,"DisplayFields":["x_description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_supportedit.Lists["x_physconditionID"] = {"LinkField":"x_physconditionID","Ajax":true,"AutoFill":false,"DisplayFields":["x_physconditionName","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php //$Breadcrumb->Render(); ?>
<?php $tbl_support_edit->ShowPageHeader(); ?>
<?php
$tbl_support_edit->ShowMessage();
?>
<form name="ftbl_supportedit" id="ftbl_supportedit" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="tbl_support">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<table cellspacing="0" class="ewGrid"><tr><td>
<table id="tbl_tbl_supportedit" class="table table-bordered table-striped">
<?php if ($tbl_support->supportID->Visible) { // supportID ?>
	<tr id="r_supportID">
		<td><span id="elh_tbl_support_supportID"><?php echo $tbl_support->supportID->FldCaption() ?></span></td>
		<td<?php echo $tbl_support->supportID->CellAttributes() ?>>
<span id="el_tbl_support_supportID" class="control-group">
<span<?php echo $tbl_support->supportID->ViewAttributes() ?>>
<?php echo $tbl_support->supportID->EditValue ?></span>
</span>
<input type="hidden" data-field="x_supportID" name="x_supportID" id="x_supportID" value="<?php echo ew_HtmlEncode($tbl_support->supportID->CurrentValue) ?>">
<?php echo $tbl_support->supportID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_support->PensionerID->Visible) { // PensionerID ?>
	<tr id="r_PensionerID">
		<td><span id="elh_tbl_support_PensionerID"><?php echo $tbl_support->PensionerID->FldCaption() ?></span></td>
		<td<?php echo $tbl_support->PensionerID->CellAttributes() ?>>
<?php if ($tbl_support->PensionerID->getSessionValue() <> "") { ?>
<span<?php echo $tbl_support->PensionerID->ViewAttributes() ?>>
<?php echo $tbl_support->PensionerID->ViewValue ?></span>
<input type="hidden" id="x_PensionerID" name="x_PensionerID" value="<?php echo ew_HtmlEncode($tbl_support->PensionerID->CurrentValue) ?>">
<?php } else { ?>
<input type="text" data-field="x_PensionerID" name="x_PensionerID" id="x_PensionerID" size="30" maxlength="25" placeholder="<?php echo $tbl_support->PensionerID->PlaceHolder ?>" value="<?php echo $tbl_support->PensionerID->EditValue ?>"<?php echo $tbl_support->PensionerID->EditAttributes() ?>>
<?php } ?>
<?php echo $tbl_support->PensionerID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_support->family_support->Visible) { // family_support ?>
	<tr id="r_family_support">
		<td><span id="elh_tbl_support_family_support"><?php echo $tbl_support->family_support->FldCaption() ?></span></td>
		<td<?php echo $tbl_support->family_support->CellAttributes() ?>>
<span id="el_tbl_support_family_support" class="control-group">
<select data-field="x_family_support" id="x_family_support" name="x_family_support"<?php echo $tbl_support->family_support->EditAttributes() ?>>
<?php
if (is_array($tbl_support->family_support->EditValue)) {
	$arwrk = $tbl_support->family_support->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->family_support->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php echo $tbl_support->family_support->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_support->KindSupID->Visible) { // KindSupID ?>
	<tr id="r_KindSupID">
		<td><span id="elh_tbl_support_KindSupID"><?php echo $tbl_support->KindSupID->FldCaption() ?></span></td>
		<td<?php echo $tbl_support->KindSupID->CellAttributes() ?>>
<span id="el_tbl_support_KindSupID" class="control-group">
<select data-field="x_KindSupID" id="x_KindSupID" name="x_KindSupID"<?php echo $tbl_support->KindSupID->EditAttributes() ?>>
<?php
if (is_array($tbl_support->KindSupID->EditValue)) {
	$arwrk = $tbl_support->KindSupID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->KindSupID->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
$sSqlWrk = "SELECT `SupportID`, `SupportKind` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_support`";
$sWhereWrk = "";

// Call Lookup selecting
$tbl_support->Lookup_Selecting($tbl_support->KindSupID, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `SupportID` ASC";
?>
<input type="hidden" name="s_x_KindSupID" id="s_x_KindSupID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`SupportID` = {filter_value}"); ?>&t0=3">
</span>
<?php echo $tbl_support->KindSupID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_support->meals->Visible) { // meals ?>
	<tr id="r_meals">
		<td><span id="elh_tbl_support_meals"><?php echo $tbl_support->meals->FldCaption() ?></span></td>
		<td<?php echo $tbl_support->meals->CellAttributes() ?>>
<span id="el_tbl_support_meals" class="control-group">
<input type="text" data-field="x_meals" name="x_meals" id="x_meals" size="30" placeholder="<?php echo $tbl_support->meals->PlaceHolder ?>" value="<?php echo $tbl_support->meals->EditValue ?>"<?php echo $tbl_support->meals->EditAttributes() ?>>
</span>
<?php echo $tbl_support->meals->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_support->disability->Visible) { // disability ?>
	<tr id="r_disability">
		<td><span id="elh_tbl_support_disability"><?php echo $tbl_support->disability->FldCaption() ?></span></td>
		<td<?php echo $tbl_support->disability->CellAttributes() ?>>
<span id="el_tbl_support_disability" class="control-group">
<select data-field="x_disability" id="x_disability" name="x_disability"<?php echo $tbl_support->disability->EditAttributes() ?>>
<?php
if (is_array($tbl_support->disability->EditValue)) {
	$arwrk = $tbl_support->disability->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->disability->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php echo $tbl_support->disability->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_support->disabilityID->Visible) { // disabilityID ?>
	<tr id="r_disabilityID">
		<td><span id="elh_tbl_support_disabilityID"><?php echo $tbl_support->disabilityID->FldCaption() ?></span></td>
		<td<?php echo $tbl_support->disabilityID->CellAttributes() ?>>
<span id="el_tbl_support_disabilityID" class="control-group">
<select data-field="x_disabilityID" id="x_disabilityID" name="x_disabilityID"<?php echo $tbl_support->disabilityID->EditAttributes() ?>>
<?php
if (is_array($tbl_support->disabilityID->EditValue)) {
	$arwrk = $tbl_support->disabilityID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->disabilityID->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
$sSqlWrk = "SELECT `disabilityID`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_disability`";
$sWhereWrk = "";

// Call Lookup selecting
$tbl_support->Lookup_Selecting($tbl_support->disabilityID, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `disabilityID` ASC";
?>
<input type="hidden" name="s_x_disabilityID" id="s_x_disabilityID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`disabilityID` = {filter_value}"); ?>&t0=3">
</span>
<?php echo $tbl_support->disabilityID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_support->immobile->Visible) { // immobile ?>
	<tr id="r_immobile">
		<td><span id="elh_tbl_support_immobile"><?php echo $tbl_support->immobile->FldCaption() ?></span></td>
		<td<?php echo $tbl_support->immobile->CellAttributes() ?>>
<span id="el_tbl_support_immobile" class="control-group">
<select data-field="x_immobile" id="x_immobile" name="x_immobile"<?php echo $tbl_support->immobile->EditAttributes() ?>>
<?php
if (is_array($tbl_support->immobile->EditValue)) {
	$arwrk = $tbl_support->immobile->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->immobile->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php echo $tbl_support->immobile->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_support->assistiveID->Visible) { // assistiveID ?>
	<tr id="r_assistiveID">
		<td><span id="elh_tbl_support_assistiveID"><?php echo $tbl_support->assistiveID->FldCaption() ?></span></td>
		<td<?php echo $tbl_support->assistiveID->CellAttributes() ?>>
<span id="el_tbl_support_assistiveID" class="control-group">
<select data-field="x_assistiveID" id="x_assistiveID" name="x_assistiveID"<?php echo $tbl_support->assistiveID->EditAttributes() ?>>
<?php
if (is_array($tbl_support->assistiveID->EditValue)) {
	$arwrk = $tbl_support->assistiveID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->assistiveID->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
$sSqlWrk = "SELECT `assistiveID`, `Device` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_assistive`";
$sWhereWrk = "";

// Call Lookup selecting
$tbl_support->Lookup_Selecting($tbl_support->assistiveID, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `assistiveID` ASC";
?>
<input type="hidden" name="s_x_assistiveID" id="s_x_assistiveID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`assistiveID` = {filter_value}"); ?>&t0=3">
</span>
<?php echo $tbl_support->assistiveID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_support->preEx_illness->Visible) { // preEx_illness ?>
	<tr id="r_preEx_illness">
		<td><span id="elh_tbl_support_preEx_illness"><?php echo $tbl_support->preEx_illness->FldCaption() ?></span></td>
		<td<?php echo $tbl_support->preEx_illness->CellAttributes() ?>>
<span id="el_tbl_support_preEx_illness" class="control-group">
<select data-field="x_preEx_illness" id="x_preEx_illness" name="x_preEx_illness"<?php echo $tbl_support->preEx_illness->EditAttributes() ?>>
<?php
if (is_array($tbl_support->preEx_illness->EditValue)) {
	$arwrk = $tbl_support->preEx_illness->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->preEx_illness->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php echo $tbl_support->preEx_illness->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_support->illnessID->Visible) { // illnessID ?>
	<tr id="r_illnessID">
		<td><span id="elh_tbl_support_illnessID"><?php echo $tbl_support->illnessID->FldCaption() ?></span></td>
		<td<?php echo $tbl_support->illnessID->CellAttributes() ?>>
<span id="el_tbl_support_illnessID" class="control-group">
<select data-field="x_illnessID" id="x_illnessID" name="x_illnessID"<?php echo $tbl_support->illnessID->EditAttributes() ?>>
<?php
if (is_array($tbl_support->illnessID->EditValue)) {
	$arwrk = $tbl_support->illnessID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->illnessID->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
$sSqlWrk = "SELECT `illnessID`, `description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_illness`";
$sWhereWrk = "";

// Call Lookup selecting
$tbl_support->Lookup_Selecting($tbl_support->illnessID, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `illnessID` ASC";
?>
<input type="hidden" name="s_x_illnessID" id="s_x_illnessID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`illnessID` = {filter_value}"); ?>&t0=3">
</span>
<?php echo $tbl_support->illnessID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_support->physconditionID->Visible) { // physconditionID ?>
	<tr id="r_physconditionID">
		<td><span id="elh_tbl_support_physconditionID"><?php echo $tbl_support->physconditionID->FldCaption() ?></span></td>
		<td<?php echo $tbl_support->physconditionID->CellAttributes() ?>>
<span id="el_tbl_support_physconditionID" class="control-group">
<select data-field="x_physconditionID" id="x_physconditionID" name="x_physconditionID"<?php echo $tbl_support->physconditionID->EditAttributes() ?>>
<?php
if (is_array($tbl_support->physconditionID->EditValue)) {
	$arwrk = $tbl_support->physconditionID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->physconditionID->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
$sSqlWrk = "SELECT `physconditionID`, `physconditionName` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_physical_condition`";
$sWhereWrk = "";

// Call Lookup selecting
$tbl_support->Lookup_Selecting($tbl_support->physconditionID, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `physconditionID` ASC";
?>
<input type="hidden" name="s_x_physconditionID" id="s_x_physconditionID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`physconditionID` = {filter_value}"); ?>&t0=3">
</span>
<?php echo $tbl_support->physconditionID->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
<button class="btn btn-success btn-sm" name="btnAction" id="btnAction" type="submit"><?php echo " <i class='icon-save align-top bigger-125'></i> " . "Update Changes"; ?></button>
</form>
<script type="text/javascript">
ftbl_supportedit.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$tbl_support_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$tbl_support_edit->Page_Terminate();
?>
