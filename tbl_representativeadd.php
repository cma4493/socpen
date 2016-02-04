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

$tbl_representative_add = NULL; // Initialize page object first

class ctbl_representative_add extends ctbl_representative {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Table name
	var $TableName = 'tbl_representative';

	// Page object name
	var $PageObjName = 'tbl_representative_add';

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
			define("EW_PAGE_ID", 'add', TRUE);

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
		if (!$Security->CanAdd()) {
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
			if (@$_GET["authID"] != "") {
				$this->authID->setQueryStringValue($_GET["authID"]);
				$this->setKey("authID", $this->authID->CurrentValue); // Set up key
			} else {
				$this->setKey("authID", ""); // Clear key
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
					$this->Page_Terminate("tbl_representativelist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "tbl_representativeview.php")
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
		$this->fname->CurrentValue = NULL;
		$this->fname->OldValue = $this->fname->CurrentValue;
		$this->mname->CurrentValue = NULL;
		$this->mname->OldValue = $this->mname->CurrentValue;
		$this->lname->CurrentValue = NULL;
		$this->lname->OldValue = $this->lname->CurrentValue;
		$this->relToPensioner->CurrentValue = NULL;
		$this->relToPensioner->OldValue = $this->relToPensioner->CurrentValue;
		$this->ContactNo->CurrentValue = NULL;
		$this->ContactNo->OldValue = $this->ContactNo->CurrentValue;
		$this->auth_Region->CurrentValue = NULL;
		$this->auth_Region->OldValue = $this->auth_Region->CurrentValue;
		$this->auth_prov->CurrentValue = NULL;
		$this->auth_prov->OldValue = $this->auth_prov->CurrentValue;
		$this->auth_city->CurrentValue = NULL;
		$this->auth_city->OldValue = $this->auth_city->CurrentValue;
		$this->auth_brgy->CurrentValue = NULL;
		$this->auth_brgy->OldValue = $this->auth_brgy->CurrentValue;
		$this->houseNo->CurrentValue = NULL;
		$this->houseNo->OldValue = $this->houseNo->CurrentValue;
		$this->CreatedBy->CurrentValue = NULL;
		$this->CreatedBy->OldValue = $this->CreatedBy->CurrentValue;
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
		if (!$this->CreatedBy->FldIsDetailKey) {
			$this->CreatedBy->setFormValue($objForm->GetValue("x_CreatedBy"));
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
		$this->CreatedBy->CurrentValue = $this->CreatedBy->FormValue;
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

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("authID")) <> "")
			$this->authID->CurrentValue = $this->getKey("authID"); // authID
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

			// CreatedBy
			$this->CreatedBy->LinkCustomAttributes = "";
			$this->CreatedBy->HrefValue = "";
			$this->CreatedBy->TooltipValue = "";

			// CreatedDate
			$this->CreatedDate->LinkCustomAttributes = "";
			$this->CreatedDate->HrefValue = "";
			$this->CreatedDate->TooltipValue = "";
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

			// CreatedBy
			// CreatedDate
			// Edit refer script
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

			// CreatedBy
			$this->CreatedBy->HrefValue = "";

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
		if ($this->PensionerID->CurrentValue <> "") { // Check field with unique index
			$sFilter = "(PensionerID = '" . ew_AdjustSql($this->PensionerID->CurrentValue) . "')";
			$rsChk = $this->LoadRs($sFilter);
			if ($rsChk && !$rsChk->EOF) {
				$sIdxErrMsg = str_replace("%f", $this->PensionerID->FldCaption(), $Language->Phrase("DupIndex"));
				$sIdxErrMsg = str_replace("%v", $this->PensionerID->CurrentValue, $sIdxErrMsg);
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

		// PensionerID
		$this->PensionerID->SetDbValueDef($rsnew, $this->PensionerID->CurrentValue, NULL, FALSE);

		// fname
		$this->fname->SetDbValueDef($rsnew, $this->fname->CurrentValue, NULL, FALSE);

		// mname
		$this->mname->SetDbValueDef($rsnew, $this->mname->CurrentValue, NULL, FALSE);

		// lname
		$this->lname->SetDbValueDef($rsnew, $this->lname->CurrentValue, NULL, FALSE);

		// relToPensioner
		$this->relToPensioner->SetDbValueDef($rsnew, $this->relToPensioner->CurrentValue, NULL, FALSE);

		// ContactNo
		$this->ContactNo->SetDbValueDef($rsnew, $this->ContactNo->CurrentValue, NULL, FALSE);

		// auth_Region
		$this->auth_Region->SetDbValueDef($rsnew, $this->auth_Region->CurrentValue, NULL, FALSE);

		// auth_prov
		$this->auth_prov->SetDbValueDef($rsnew, $this->auth_prov->CurrentValue, NULL, FALSE);

		// auth_city
		$this->auth_city->SetDbValueDef($rsnew, $this->auth_city->CurrentValue, NULL, FALSE);

		// auth_brgy
		$this->auth_brgy->SetDbValueDef($rsnew, $this->auth_brgy->CurrentValue, NULL, FALSE);

		// houseNo
		$this->houseNo->SetDbValueDef($rsnew, $this->houseNo->CurrentValue, NULL, FALSE);

		// CreatedBy
		$this->CreatedBy->SetDbValueDef($rsnew, CurrentUserID(), NULL);
		$rsnew['CreatedBy'] = &$this->CreatedBy->DbValue;

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
			$this->authID->setDbValue($conn->Insert_ID());
			$rsnew['authID'] = $this->authID->DbValue;
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
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "tbl_representativelist.php", $this->TableVar);
		$PageCaption = ($this->CurrentAction == "C") ? $Language->Phrase("Copy") : $Language->Phrase("Add");
		$Breadcrumb->Add("add", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", ew_CurrentUrl(), $this->TableVar);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'tbl_representative';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (add page)
	function WriteAuditTrailOnAdd(&$rs) {
		if (!$this->AuditTrailOnAdd) return;
		$table = 'tbl_representative';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['authID'];

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
if (!isset($tbl_representative_add)) $tbl_representative_add = new ctbl_representative_add();

// Page init
$tbl_representative_add->Page_Init();

// Page main
$tbl_representative_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$tbl_representative_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var tbl_representative_add = new ew_Page("tbl_representative_add");
tbl_representative_add.PageID = "add"; // Page ID
var EW_PAGE_ID = tbl_representative_add.PageID; // For backward compatibility

// Form object
var ftbl_representativeadd = new ew_Form("ftbl_representativeadd");

// Validate form
ftbl_representativeadd.Validate = function() {
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
ftbl_representativeadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftbl_representativeadd.ValidateRequired = true;
<?php } else { ?>
ftbl_representativeadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
ftbl_representativeadd.Lists["x_relToPensioner"] = {"LinkField":"x_RelationID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_representativeadd.Lists["x_auth_Region"] = {"LinkField":"x_region_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_region_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_representativeadd.Lists["x_auth_prov"] = {"LinkField":"x_prov_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_prov_name","","",""],"ParentFields":["x_auth_Region"],"FilterFields":["x_region_code"],"Options":[]};
ftbl_representativeadd.Lists["x_auth_city"] = {"LinkField":"x_city_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_city_name","","",""],"ParentFields":["x_auth_prov"],"FilterFields":["x_prov_code"],"Options":[]};
ftbl_representativeadd.Lists["x_auth_brgy"] = {"LinkField":"x_brgy_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_brgy_name","","",""],"ParentFields":["x_auth_city"],"FilterFields":["x_city_code"],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php //$Breadcrumb->Render(); ?>
<?php $tbl_representative_add->ShowPageHeader(); ?>
<?php
$tbl_representative_add->ShowMessage();
?>
<form name="ftbl_representativeadd" id="ftbl_representativeadd" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="tbl_representative">
<input type="hidden" name="a_add" id="a_add" value="A">
<table cellspacing="0" class="ewGrid"><tr><td>
<table id="tbl_tbl_representativeadd" class="table table-bordered table-striped">
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
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
</form>
<script type="text/javascript">
ftbl_representativeadd.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$tbl_representative_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$tbl_representative_add->Page_Terminate();
?>
