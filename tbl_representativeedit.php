<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "tbl_representativeinfo.php" ?>
<?php include_once "tbl_pensionerinfo.php" ?>
<?php include_once "tbl_userinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$tbl_representative_edit = NULL; // Initialize page object first

class ctbl_representative_edit extends ctbl_representative {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Table name
	var $TableName = 'tbl_representative';

	// Page object name
	var $PageObjName = 'tbl_representative_edit';

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

		// Table object (tbl_representative)
		if (!isset($GLOBALS["tbl_representative"])) {
			$GLOBALS["tbl_representative"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["tbl_representative"];
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
			define("EW_TABLE_NAME", 'tbl_representative', TRUE);

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
			$this->Page_Terminate("tbl_representativelist.php");
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
		$this->authID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
		if (@$_GET["authID"] <> "") {
			$this->authID->setQueryStringValue($_GET["authID"]);
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
		if ($this->authID->CurrentValue == "")
			$this->Page_Terminate("tbl_representativelist.php"); // Invalid key, return to list

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
					$this->Page_Terminate("tbl_representativelist.php"); // No matching record, return to list
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
		if (!$this->authID->FldIsDetailKey)
			$this->authID->setFormValue($objForm->GetValue("x_authID"));
		if (!$this->PensionerID->FldIsDetailKey) {
			$this->PensionerID->setFormValue($objForm->GetValue("x_PensionerID"));
		}
		if (!$this->fname->FldIsDetailKey) {
			$this->fname->setFormValue($objForm->GetValue("x_fname"));
		}
		if (!$this->mname->FldIsDetailKey) {
			$this->mname->setFormValue($objForm->GetValue("x_mname"));
		}
		if (!$this->lname->FldIsDetailKey) {
			$this->lname->setFormValue($objForm->GetValue("x_lname"));
		}
		if (!$this->relToPensioner->FldIsDetailKey) {
			$this->relToPensioner->setFormValue($objForm->GetValue("x_relToPensioner"));
		}
		if (!$this->ContactNo->FldIsDetailKey) {
			$this->ContactNo->setFormValue($objForm->GetValue("x_ContactNo"));
		}
		if (!$this->auth_Region->FldIsDetailKey) {
			$this->auth_Region->setFormValue($objForm->GetValue("x_auth_Region"));
		}
		if (!$this->auth_prov->FldIsDetailKey) {
			$this->auth_prov->setFormValue($objForm->GetValue("x_auth_prov"));
		}
		if (!$this->auth_city->FldIsDetailKey) {
			$this->auth_city->setFormValue($objForm->GetValue("x_auth_city"));
		}
		if (!$this->auth_brgy->FldIsDetailKey) {
			$this->auth_brgy->setFormValue($objForm->GetValue("x_auth_brgy"));
		}
		if (!$this->houseNo->FldIsDetailKey) {
			$this->houseNo->setFormValue($objForm->GetValue("x_houseNo"));
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
		$this->authID->CurrentValue = $this->authID->FormValue;
		$this->PensionerID->CurrentValue = $this->PensionerID->FormValue;
		$this->fname->CurrentValue = $this->fname->FormValue;
		$this->mname->CurrentValue = $this->mname->FormValue;
		$this->lname->CurrentValue = $this->lname->FormValue;
		$this->relToPensioner->CurrentValue = $this->relToPensioner->FormValue;
		$this->ContactNo->CurrentValue = $this->ContactNo->FormValue;
		$this->auth_Region->CurrentValue = $this->auth_Region->FormValue;
		$this->auth_prov->CurrentValue = $this->auth_prov->FormValue;
		$this->auth_city->CurrentValue = $this->auth_city->FormValue;
		$this->auth_brgy->CurrentValue = $this->auth_brgy->FormValue;
		$this->houseNo->CurrentValue = $this->houseNo->FormValue;
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
		$this->authID->setDbValue($rs->fields('authID'));
		$this->PensionerID->setDbValue($rs->fields('PensionerID'));
		$this->fname->setDbValue($rs->fields('fname'));
		$this->mname->setDbValue($rs->fields('mname'));
		$this->lname->setDbValue($rs->fields('lname'));
		$this->relToPensioner->setDbValue($rs->fields('relToPensioner'));
		$this->ContactNo->setDbValue($rs->fields('ContactNo'));
		$this->auth_Region->setDbValue($rs->fields('auth_Region'));
		$this->auth_prov->setDbValue($rs->fields('auth_prov'));
		$this->auth_city->setDbValue($rs->fields('auth_city'));
		$this->auth_brgy->setDbValue($rs->fields('auth_brgy'));
		$this->houseNo->setDbValue($rs->fields('houseNo'));
		$this->CreatedBy->setDbValue($rs->fields('CreatedBy'));
		$this->CreatedDate->setDbValue($rs->fields('CreatedDate'));
		$this->UpdatedBy->setDbValue($rs->fields('UpdatedBy'));
		$this->UpdatedDate->setDbValue($rs->fields('UpdatedDate'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->authID->DbValue = $row['authID'];
		$this->PensionerID->DbValue = $row['PensionerID'];
		$this->fname->DbValue = $row['fname'];
		$this->mname->DbValue = $row['mname'];
		$this->lname->DbValue = $row['lname'];
		$this->relToPensioner->DbValue = $row['relToPensioner'];
		$this->ContactNo->DbValue = $row['ContactNo'];
		$this->auth_Region->DbValue = $row['auth_Region'];
		$this->auth_prov->DbValue = $row['auth_prov'];
		$this->auth_city->DbValue = $row['auth_city'];
		$this->auth_brgy->DbValue = $row['auth_brgy'];
		$this->houseNo->DbValue = $row['houseNo'];
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
		// authID
		// PensionerID
		// fname
		// mname
		// lname
		// relToPensioner
		// ContactNo
		// auth_Region
		// auth_prov
		// auth_city
		// auth_brgy
		// houseNo
		// CreatedBy
		// CreatedDate
		// UpdatedBy
		// UpdatedDate

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// authID
			$this->authID->ViewValue = $this->authID->CurrentValue;
			$this->authID->ViewCustomAttributes = "";

			// PensionerID
			$this->PensionerID->ViewValue = $this->PensionerID->CurrentValue;
			$this->PensionerID->ViewCustomAttributes = "";

			// fname
			$this->fname->ViewValue = $this->fname->CurrentValue;
			$this->fname->ViewCustomAttributes = "";

			// mname
			$this->mname->ViewValue = $this->mname->CurrentValue;
			$this->mname->ViewCustomAttributes = "";

			// lname
			$this->lname->ViewValue = $this->lname->CurrentValue;
			$this->lname->ViewCustomAttributes = "";

			// relToPensioner
			if (strval($this->relToPensioner->CurrentValue) <> "") {
				$sFilterWrk = "`RelationID`" . ew_SearchString("=", $this->relToPensioner->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `RelationID`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_relationship`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->relToPensioner, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `Description` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->relToPensioner->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->relToPensioner->ViewValue = $this->relToPensioner->CurrentValue;
				}
			} else {
				$this->relToPensioner->ViewValue = NULL;
			}
			$this->relToPensioner->ViewCustomAttributes = "";

			// ContactNo
			$this->ContactNo->ViewValue = $this->ContactNo->CurrentValue;
			$this->ContactNo->ViewCustomAttributes = "";

			// auth_Region
			if (strval($this->auth_Region->CurrentValue) <> "") {
				$sFilterWrk = "`region_code`" . ew_SearchString("=", $this->auth_Region->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `region_code`, `region_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_regions`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->auth_Region, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `region_code` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->auth_Region->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->auth_Region->ViewValue = $this->auth_Region->CurrentValue;
				}
			} else {
				$this->auth_Region->ViewValue = NULL;
			}
			$this->auth_Region->ViewCustomAttributes = "";

			// auth_prov
			if (strval($this->auth_prov->CurrentValue) <> "") {
				$sFilterWrk = "`prov_code`" . ew_SearchString("=", $this->auth_prov->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `prov_code`, `prov_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_provinces`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->auth_prov, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `prov_name` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->auth_prov->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->auth_prov->ViewValue = $this->auth_prov->CurrentValue;
				}
			} else {
				$this->auth_prov->ViewValue = NULL;
			}
			$this->auth_prov->ViewCustomAttributes = "";

			// auth_city
			if (strval($this->auth_city->CurrentValue) <> "") {
				$sFilterWrk = "`city_code`" . ew_SearchString("=", $this->auth_city->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `city_code`, `city_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_cities`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->auth_city, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `city_name` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->auth_city->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->auth_city->ViewValue = $this->auth_city->CurrentValue;
				}
			} else {
				$this->auth_city->ViewValue = NULL;
			}
			$this->auth_city->ViewCustomAttributes = "";

			// auth_brgy
			if (strval($this->auth_brgy->CurrentValue) <> "") {
				$sFilterWrk = "`brgy_code`" . ew_SearchString("=", $this->auth_brgy->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `brgy_code`, `brgy_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_brgy`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->auth_brgy, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `brgy_name` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->auth_brgy->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->auth_brgy->ViewValue = $this->auth_brgy->CurrentValue;
				}
			} else {
				$this->auth_brgy->ViewValue = NULL;
			}
			$this->auth_brgy->ViewCustomAttributes = "";

			// houseNo
			$this->houseNo->ViewValue = $this->houseNo->CurrentValue;
			$this->houseNo->ViewCustomAttributes = "";

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

			// authID
			$this->authID->LinkCustomAttributes = "";
			$this->authID->HrefValue = "";
			$this->authID->TooltipValue = "";

			// PensionerID
			$this->PensionerID->LinkCustomAttributes = "";
			$this->PensionerID->HrefValue = "";
			$this->PensionerID->TooltipValue = "";

			// fname
			$this->fname->LinkCustomAttributes = "";
			$this->fname->HrefValue = "";
			$this->fname->TooltipValue = "";

			// mname
			$this->mname->LinkCustomAttributes = "";
			$this->mname->HrefValue = "";
			$this->mname->TooltipValue = "";

			// lname
			$this->lname->LinkCustomAttributes = "";
			$this->lname->HrefValue = "";
			$this->lname->TooltipValue = "";

			// relToPensioner
			$this->relToPensioner->LinkCustomAttributes = "";
			$this->relToPensioner->HrefValue = "";
			$this->relToPensioner->TooltipValue = "";

			// ContactNo
			$this->ContactNo->LinkCustomAttributes = "";
			$this->ContactNo->HrefValue = "";
			$this->ContactNo->TooltipValue = "";

			// auth_Region
			$this->auth_Region->LinkCustomAttributes = "";
			$this->auth_Region->HrefValue = "";
			$this->auth_Region->TooltipValue = "";

			// auth_prov
			$this->auth_prov->LinkCustomAttributes = "";
			$this->auth_prov->HrefValue = "";
			$this->auth_prov->TooltipValue = "";

			// auth_city
			$this->auth_city->LinkCustomAttributes = "";
			$this->auth_city->HrefValue = "";
			$this->auth_city->TooltipValue = "";

			// auth_brgy
			$this->auth_brgy->LinkCustomAttributes = "";
			$this->auth_brgy->HrefValue = "";
			$this->auth_brgy->TooltipValue = "";

			// houseNo
			$this->houseNo->LinkCustomAttributes = "";
			$this->houseNo->HrefValue = "";
			$this->houseNo->TooltipValue = "";

			// UpdatedBy
			$this->UpdatedBy->LinkCustomAttributes = "";
			$this->UpdatedBy->HrefValue = "";
			$this->UpdatedBy->TooltipValue = "";

			// UpdatedDate
			$this->UpdatedDate->LinkCustomAttributes = "";
			$this->UpdatedDate->HrefValue = "";
			$this->UpdatedDate->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// authID
			$this->authID->EditCustomAttributes = "";
			$this->authID->EditValue = $this->authID->CurrentValue;
			$this->authID->ViewCustomAttributes = "";

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

			// fname
			$this->fname->EditCustomAttributes = "";
			$this->fname->EditValue = ew_HtmlEncode($this->fname->CurrentValue);
			$this->fname->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->fname->FldCaption()));

			// mname
			$this->mname->EditCustomAttributes = "";
			$this->mname->EditValue = ew_HtmlEncode($this->mname->CurrentValue);
			$this->mname->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->mname->FldCaption()));

			// lname
			$this->lname->EditCustomAttributes = "";
			$this->lname->EditValue = ew_HtmlEncode($this->lname->CurrentValue);
			$this->lname->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->lname->FldCaption()));

			// relToPensioner
			$this->relToPensioner->EditCustomAttributes = "";
			if (trim(strval($this->relToPensioner->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`RelationID`" . ew_SearchString("=", $this->relToPensioner->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `RelationID`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_relationship`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->relToPensioner, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `Description` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->relToPensioner->EditValue = $arwrk;

			// ContactNo
			$this->ContactNo->EditCustomAttributes = "";
			$this->ContactNo->EditValue = ew_HtmlEncode($this->ContactNo->CurrentValue);
			$this->ContactNo->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->ContactNo->FldCaption()));

			// auth_Region
			$this->auth_Region->EditCustomAttributes = "";
			if (trim(strval($this->auth_Region->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`region_code`" . ew_SearchString("=", $this->auth_Region->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `region_code`, `region_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_regions`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->auth_Region, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `region_code` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->auth_Region->EditValue = $arwrk;

			// auth_prov
			$this->auth_prov->EditCustomAttributes = "";
			if (trim(strval($this->auth_prov->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`prov_code`" . ew_SearchString("=", $this->auth_prov->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `prov_code`, `prov_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `region_code` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_provinces`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->auth_prov, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `prov_name` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->auth_prov->EditValue = $arwrk;

			// auth_city
			$this->auth_city->EditCustomAttributes = "";
			if (trim(strval($this->auth_city->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`city_code`" . ew_SearchString("=", $this->auth_city->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `city_code`, `city_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `prov_code` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_cities`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->auth_city, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `city_name` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->auth_city->EditValue = $arwrk;

			// auth_brgy
			$this->auth_brgy->EditCustomAttributes = "";
			if (trim(strval($this->auth_brgy->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`brgy_code`" . ew_SearchString("=", $this->auth_brgy->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `brgy_code`, `brgy_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `city_code` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_brgy`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->auth_brgy, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `brgy_name` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->auth_brgy->EditValue = $arwrk;

			// houseNo
			$this->houseNo->EditCustomAttributes = "";
			$this->houseNo->EditValue = ew_HtmlEncode($this->houseNo->CurrentValue);
			$this->houseNo->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->houseNo->FldCaption()));

			// UpdatedBy
			// UpdatedDate
			// Edit refer script
			// authID

			$this->authID->HrefValue = "";

			// PensionerID
			$this->PensionerID->HrefValue = "";

			// fname
			$this->fname->HrefValue = "";

			// mname
			$this->mname->HrefValue = "";

			// lname
			$this->lname->HrefValue = "";

			// relToPensioner
			$this->relToPensioner->HrefValue = "";

			// ContactNo
			$this->ContactNo->HrefValue = "";

			// auth_Region
			$this->auth_Region->HrefValue = "";

			// auth_prov
			$this->auth_prov->HrefValue = "";

			// auth_city
			$this->auth_city->HrefValue = "";

			// auth_brgy
			$this->auth_brgy->HrefValue = "";

			// houseNo
			$this->houseNo->HrefValue = "";

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

			// fname
			$this->fname->SetDbValueDef($rsnew, $this->fname->CurrentValue, NULL, $this->fname->ReadOnly);

			// mname
			$this->mname->SetDbValueDef($rsnew, $this->mname->CurrentValue, NULL, $this->mname->ReadOnly);

			// lname
			$this->lname->SetDbValueDef($rsnew, $this->lname->CurrentValue, NULL, $this->lname->ReadOnly);

			// relToPensioner
			$this->relToPensioner->SetDbValueDef($rsnew, $this->relToPensioner->CurrentValue, NULL, $this->relToPensioner->ReadOnly);

			// ContactNo
			$this->ContactNo->SetDbValueDef($rsnew, $this->ContactNo->CurrentValue, NULL, $this->ContactNo->ReadOnly);

			// auth_Region
			$this->auth_Region->SetDbValueDef($rsnew, $this->auth_Region->CurrentValue, NULL, $this->auth_Region->ReadOnly);

			// auth_prov
			$this->auth_prov->SetDbValueDef($rsnew, $this->auth_prov->CurrentValue, NULL, $this->auth_prov->ReadOnly);

			// auth_city
			$this->auth_city->SetDbValueDef($rsnew, $this->auth_city->CurrentValue, NULL, $this->auth_city->ReadOnly);

			// auth_brgy
			$this->auth_brgy->SetDbValueDef($rsnew, $this->auth_brgy->CurrentValue, NULL, $this->auth_brgy->ReadOnly);

			// houseNo
			$this->houseNo->SetDbValueDef($rsnew, $this->houseNo->CurrentValue, NULL, $this->houseNo->ReadOnly);

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
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "tbl_representativelist.php", $this->TableVar);
		$PageCaption = $Language->Phrase("edit");
		$Breadcrumb->Add("edit", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", ew_CurrentUrl(), $this->TableVar);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'tbl_representative';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (edit page)
	function WriteAuditTrailOnEdit(&$rsold, &$rsnew) {
		if (!$this->AuditTrailOnEdit) return;
		$table = 'tbl_representative';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rsold['authID'];

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
if (!isset($tbl_representative_edit)) $tbl_representative_edit = new ctbl_representative_edit();

// Page init
$tbl_representative_edit->Page_Init();

// Page main
$tbl_representative_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$tbl_representative_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var tbl_representative_edit = new ew_Page("tbl_representative_edit");
tbl_representative_edit.PageID = "edit"; // Page ID
var EW_PAGE_ID = tbl_representative_edit.PageID; // For backward compatibility

// Form object
var ftbl_representativeedit = new ew_Form("ftbl_representativeedit");

// Validate form
ftbl_representativeedit.Validate = function() {
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
ftbl_representativeedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftbl_representativeedit.ValidateRequired = true;
<?php } else { ?>
ftbl_representativeedit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
ftbl_representativeedit.Lists["x_relToPensioner"] = {"LinkField":"x_RelationID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_representativeedit.Lists["x_auth_Region"] = {"LinkField":"x_region_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_region_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_representativeedit.Lists["x_auth_prov"] = {"LinkField":"x_prov_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_prov_name","","",""],"ParentFields":["x_auth_Region"],"FilterFields":["x_region_code"],"Options":[]};
ftbl_representativeedit.Lists["x_auth_city"] = {"LinkField":"x_city_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_city_name","","",""],"ParentFields":["x_auth_prov"],"FilterFields":["x_prov_code"],"Options":[]};
ftbl_representativeedit.Lists["x_auth_brgy"] = {"LinkField":"x_brgy_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_brgy_name","","",""],"ParentFields":["x_auth_city"],"FilterFields":["x_city_code"],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php //$Breadcrumb->Render(); ?>
<?php $tbl_representative_edit->ShowPageHeader(); ?>
<?php
$tbl_representative_edit->ShowMessage();
?>
<form name="ftbl_representativeedit" id="ftbl_representativeedit" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="tbl_representative">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<table cellspacing="0" class="ewGrid"><tr><td>
<table id="tbl_tbl_representativeedit" class="table table-bordered table-striped">
<?php if ($tbl_representative->authID->Visible) { // authID ?>
	<tr id="r_authID">
		<td><span id="elh_tbl_representative_authID"><?php echo $tbl_representative->authID->FldCaption() ?></span></td>
		<td<?php echo $tbl_representative->authID->CellAttributes() ?>>
<span id="el_tbl_representative_authID" class="control-group">
<span<?php echo $tbl_representative->authID->ViewAttributes() ?>>
<?php echo $tbl_representative->authID->EditValue ?></span>
</span>
<input type="hidden" data-field="x_authID" name="x_authID" id="x_authID" value="<?php echo ew_HtmlEncode($tbl_representative->authID->CurrentValue) ?>">
<?php echo $tbl_representative->authID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_representative->PensionerID->Visible) { // PensionerID ?>
	<tr id="r_PensionerID">
		<td><span id="elh_tbl_representative_PensionerID"><?php echo $tbl_representative->PensionerID->FldCaption() ?></span></td>
		<td<?php echo $tbl_representative->PensionerID->CellAttributes() ?>>
<?php if ($tbl_representative->PensionerID->getSessionValue() <> "") { ?>
<span<?php echo $tbl_representative->PensionerID->ViewAttributes() ?>>
<?php echo $tbl_representative->PensionerID->ViewValue ?></span>
<input type="hidden" id="x_PensionerID" name="x_PensionerID" value="<?php echo ew_HtmlEncode($tbl_representative->PensionerID->CurrentValue) ?>">
<?php } else { ?>
<input type="text" data-field="x_PensionerID" name="x_PensionerID" id="x_PensionerID" size="30" maxlength="25" placeholder="<?php echo $tbl_representative->PensionerID->PlaceHolder ?>" value="<?php echo $tbl_representative->PensionerID->EditValue ?>"<?php echo $tbl_representative->PensionerID->EditAttributes() ?>>
<?php } ?>
<?php echo $tbl_representative->PensionerID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_representative->fname->Visible) { // fname ?>
	<tr id="r_fname">
		<td><span id="elh_tbl_representative_fname"><?php echo $tbl_representative->fname->FldCaption() ?></span></td>
		<td<?php echo $tbl_representative->fname->CellAttributes() ?>>
<span id="el_tbl_representative_fname" class="control-group">
<input type="text" data-field="x_fname" name="x_fname" id="x_fname" size="30" maxlength="40" placeholder="<?php echo $tbl_representative->fname->PlaceHolder ?>" value="<?php echo $tbl_representative->fname->EditValue ?>"<?php echo $tbl_representative->fname->EditAttributes() ?>>
</span>
<?php echo $tbl_representative->fname->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_representative->mname->Visible) { // mname ?>
	<tr id="r_mname">
		<td><span id="elh_tbl_representative_mname"><?php echo $tbl_representative->mname->FldCaption() ?></span></td>
		<td<?php echo $tbl_representative->mname->CellAttributes() ?>>
<span id="el_tbl_representative_mname" class="control-group">
<input type="text" data-field="x_mname" name="x_mname" id="x_mname" size="30" maxlength="40" placeholder="<?php echo $tbl_representative->mname->PlaceHolder ?>" value="<?php echo $tbl_representative->mname->EditValue ?>"<?php echo $tbl_representative->mname->EditAttributes() ?>>
</span>
<?php echo $tbl_representative->mname->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_representative->lname->Visible) { // lname ?>
	<tr id="r_lname">
		<td><span id="elh_tbl_representative_lname"><?php echo $tbl_representative->lname->FldCaption() ?></span></td>
		<td<?php echo $tbl_representative->lname->CellAttributes() ?>>
<span id="el_tbl_representative_lname" class="control-group">
<input type="text" data-field="x_lname" name="x_lname" id="x_lname" size="30" maxlength="40" placeholder="<?php echo $tbl_representative->lname->PlaceHolder ?>" value="<?php echo $tbl_representative->lname->EditValue ?>"<?php echo $tbl_representative->lname->EditAttributes() ?>>
</span>
<?php echo $tbl_representative->lname->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_representative->relToPensioner->Visible) { // relToPensioner ?>
	<tr id="r_relToPensioner">
		<td><span id="elh_tbl_representative_relToPensioner"><?php echo $tbl_representative->relToPensioner->FldCaption() ?></span></td>
		<td<?php echo $tbl_representative->relToPensioner->CellAttributes() ?>>
<span id="el_tbl_representative_relToPensioner" class="control-group">
<select data-field="x_relToPensioner" id="x_relToPensioner" name="x_relToPensioner"<?php echo $tbl_representative->relToPensioner->EditAttributes() ?>>
<?php
if (is_array($tbl_representative->relToPensioner->EditValue)) {
	$arwrk = $tbl_representative->relToPensioner->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_representative->relToPensioner->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
$sSqlWrk = "SELECT `RelationID`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_relationship`";
$sWhereWrk = "";

// Call Lookup selecting
$tbl_representative->Lookup_Selecting($tbl_representative->relToPensioner, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `Description` ASC";
?>
<input type="hidden" name="s_x_relToPensioner" id="s_x_relToPensioner" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`RelationID` = {filter_value}"); ?>&t0=3">
</span>
<?php echo $tbl_representative->relToPensioner->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_representative->ContactNo->Visible) { // ContactNo ?>
	<tr id="r_ContactNo">
		<td><span id="elh_tbl_representative_ContactNo"><?php echo $tbl_representative->ContactNo->FldCaption() ?></span></td>
		<td<?php echo $tbl_representative->ContactNo->CellAttributes() ?>>
<span id="el_tbl_representative_ContactNo" class="control-group">
<input type="text" data-field="x_ContactNo" name="x_ContactNo" id="x_ContactNo" size="30" maxlength="10" placeholder="<?php echo $tbl_representative->ContactNo->PlaceHolder ?>" value="<?php echo $tbl_representative->ContactNo->EditValue ?>"<?php echo $tbl_representative->ContactNo->EditAttributes() ?>>
</span>
<?php echo $tbl_representative->ContactNo->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_representative->auth_Region->Visible) { // auth_Region ?>
	<tr id="r_auth_Region">
		<td><span id="elh_tbl_representative_auth_Region"><?php echo $tbl_representative->auth_Region->FldCaption() ?></span></td>
		<td<?php echo $tbl_representative->auth_Region->CellAttributes() ?>>
<span id="el_tbl_representative_auth_Region" class="control-group">
<?php $tbl_representative->auth_Region->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x_auth_prov']); " . @$tbl_representative->auth_Region->EditAttrs["onchange"]; ?>
<select data-field="x_auth_Region" id="x_auth_Region" name="x_auth_Region"<?php echo $tbl_representative->auth_Region->EditAttributes() ?>>
<?php
if (is_array($tbl_representative->auth_Region->EditValue)) {
	$arwrk = $tbl_representative->auth_Region->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_representative->auth_Region->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
$sSqlWrk = "SELECT `region_code`, `region_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_regions`";
$sWhereWrk = "";

// Call Lookup selecting
$tbl_representative->Lookup_Selecting($tbl_representative->auth_Region, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `region_code` ASC";
?>
<input type="hidden" name="s_x_auth_Region" id="s_x_auth_Region" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`region_code` = {filter_value}"); ?>&t0=21">
</span>
<?php echo $tbl_representative->auth_Region->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_representative->auth_prov->Visible) { // auth_prov ?>
	<tr id="r_auth_prov">
		<td><span id="elh_tbl_representative_auth_prov"><?php echo $tbl_representative->auth_prov->FldCaption() ?></span></td>
		<td<?php echo $tbl_representative->auth_prov->CellAttributes() ?>>
<span id="el_tbl_representative_auth_prov" class="control-group">
<?php $tbl_representative->auth_prov->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x_auth_city']); " . @$tbl_representative->auth_prov->EditAttrs["onchange"]; ?>
<select data-field="x_auth_prov" id="x_auth_prov" name="x_auth_prov"<?php echo $tbl_representative->auth_prov->EditAttributes() ?>>
<?php
if (is_array($tbl_representative->auth_prov->EditValue)) {
	$arwrk = $tbl_representative->auth_prov->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_representative->auth_prov->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
$sSqlWrk = "SELECT `prov_code`, `prov_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_provinces`";
$sWhereWrk = "{filter}";

// Call Lookup selecting
$tbl_representative->Lookup_Selecting($tbl_representative->auth_prov, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `prov_name` ASC";
?>
<input type="hidden" name="s_x_auth_prov" id="s_x_auth_prov" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`prov_code` = {filter_value}"); ?>&t0=21&f1=<?php echo ew_Encrypt("`region_code` IN ({filter_value})"); ?>&t1=21">
</span>
<?php echo $tbl_representative->auth_prov->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_representative->auth_city->Visible) { // auth_city ?>
	<tr id="r_auth_city">
		<td><span id="elh_tbl_representative_auth_city"><?php echo $tbl_representative->auth_city->FldCaption() ?></span></td>
		<td<?php echo $tbl_representative->auth_city->CellAttributes() ?>>
<span id="el_tbl_representative_auth_city" class="control-group">
<?php $tbl_representative->auth_city->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x_auth_brgy']); " . @$tbl_representative->auth_city->EditAttrs["onchange"]; ?>
<select data-field="x_auth_city" id="x_auth_city" name="x_auth_city"<?php echo $tbl_representative->auth_city->EditAttributes() ?>>
<?php
if (is_array($tbl_representative->auth_city->EditValue)) {
	$arwrk = $tbl_representative->auth_city->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_representative->auth_city->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
$sSqlWrk = "SELECT `city_code`, `city_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_cities`";
$sWhereWrk = "{filter}";

// Call Lookup selecting
$tbl_representative->Lookup_Selecting($tbl_representative->auth_city, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `city_name` ASC";
?>
<input type="hidden" name="s_x_auth_city" id="s_x_auth_city" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`city_code` = {filter_value}"); ?>&t0=21&f1=<?php echo ew_Encrypt("`prov_code` IN ({filter_value})"); ?>&t1=21">
</span>
<?php echo $tbl_representative->auth_city->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_representative->auth_brgy->Visible) { // auth_brgy ?>
	<tr id="r_auth_brgy">
		<td><span id="elh_tbl_representative_auth_brgy"><?php echo $tbl_representative->auth_brgy->FldCaption() ?></span></td>
		<td<?php echo $tbl_representative->auth_brgy->CellAttributes() ?>>
<span id="el_tbl_representative_auth_brgy" class="control-group">
<select data-field="x_auth_brgy" id="x_auth_brgy" name="x_auth_brgy"<?php echo $tbl_representative->auth_brgy->EditAttributes() ?>>
<?php
if (is_array($tbl_representative->auth_brgy->EditValue)) {
	$arwrk = $tbl_representative->auth_brgy->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_representative->auth_brgy->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
$sSqlWrk = "SELECT `brgy_code`, `brgy_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_brgy`";
$sWhereWrk = "{filter}";

// Call Lookup selecting
$tbl_representative->Lookup_Selecting($tbl_representative->auth_brgy, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `brgy_name` ASC";
?>
<input type="hidden" name="s_x_auth_brgy" id="s_x_auth_brgy" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`brgy_code` = {filter_value}"); ?>&t0=21&f1=<?php echo ew_Encrypt("`city_code` IN ({filter_value})"); ?>&t1=21">
</span>
<?php echo $tbl_representative->auth_brgy->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($tbl_representative->houseNo->Visible) { // houseNo ?>
	<tr id="r_houseNo">
		<td><span id="elh_tbl_representative_houseNo"><?php echo $tbl_representative->houseNo->FldCaption() ?></span></td>
		<td<?php echo $tbl_representative->houseNo->CellAttributes() ?>>
<span id="el_tbl_representative_houseNo" class="control-group">
<input type="text" data-field="x_houseNo" name="x_houseNo" id="x_houseNo" size="30" maxlength="255" placeholder="<?php echo $tbl_representative->houseNo->PlaceHolder ?>" value="<?php echo $tbl_representative->houseNo->EditValue ?>"<?php echo $tbl_representative->houseNo->EditAttributes() ?>>
</span>
<?php echo $tbl_representative->houseNo->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
<button class="btn btn-success btn-sm" name="btnAction" id="btnAction" type="submit"><?php echo " <i class='icon-save align-top bigger-125'></i> " . "Update Changes"; ?></button>
</form>
<script type="text/javascript">
ftbl_representativeedit.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$tbl_representative_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$tbl_representative_edit->Page_Terminate();
?>
