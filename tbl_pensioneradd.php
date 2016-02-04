<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "tbl_pensionerinfo.php" ?>
<?php include_once "tbl_userinfo.php" ?>
<?php include_once "tbl_representativegridcls.php" ?>
<?php include_once "tbl_supportgridcls.php" ?>
<?php include_once "tbl_updatesgridcls.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$tbl_pensioner_add = NULL; // Initialize page object first

class ctbl_pensioner_add extends ctbl_pensioner {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Table name
	var $TableName = 'tbl_pensioner';

	// Page object name
	var $PageObjName = 'tbl_pensioner_add';

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

		// Table object (tbl_pensioner)
		if (!isset($GLOBALS["tbl_pensioner"])) {
			$GLOBALS["tbl_pensioner"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["tbl_pensioner"];
		}

		// Table object (tbl_user)
		if (!isset($GLOBALS['tbl_user'])) $GLOBALS['tbl_user'] = new ctbl_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'tbl_pensioner', TRUE);

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
			$this->Page_Terminate("tbl_pensionerlist.php");
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
			if (@$_GET["SeniorID"] != "") {
				$this->SeniorID->setQueryStringValue($_GET["SeniorID"]);
				$this->setKey("SeniorID", $this->SeniorID->CurrentValue); // Set up key
			} else {
				$this->setKey("SeniorID", ""); // Clear key
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

		// Set up detail parameters
		$this->SetUpDetailParms();

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
					$this->Page_Terminate("tbl_pensionerlist.php"); // No matching record, return to list
				}

				// Set up detail parameters
				$this->SetUpDetailParms();
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					if ($this->getCurrentDetailTable() <> "") // Master/detail add
						$sReturnUrl = $this->GetDetailUrl();
					else
						$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "tbl_pensionerview.php")
						$sReturnUrl = $this->GetViewUrl(); // View paging, return to view page with keyurl directly
					$this->Page_Terminate($sReturnUrl); // Clean up and return
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Add failed, restore form values

					// Set up detail parameters
					$this->SetUpDetailParms();
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
		$this->picture->Upload->Index = $objForm->Index;
		if ($this->picture->Upload->UploadFile()) {

			// No action required
		} else {
			echo $this->picture->Upload->Message;
			$this->Page_Terminate();
			exit();
		}
		$this->picturename->CurrentValue = $this->picture->Upload->FileName;
		$this->picturetype->CurrentValue = $this->picture->Upload->ContentType;
		$this->picturesize->CurrentValue = $this->picture->Upload->FileSize;
		$this->picturewidth->CurrentValue = $this->picture->Upload->ImageWidth;
		$this->pictureheight->CurrentValue = $this->picture->Upload->ImageHeight;
	}

	// Load default values
	function LoadDefaultValues() {
		$this->InclusionDate->CurrentValue = NULL;
		$this->InclusionDate->OldValue = $this->InclusionDate->CurrentValue;
		$this->hh_id->CurrentValue = NULL;
		$this->hh_id->OldValue = $this->hh_id->CurrentValue;
		$this->osca_ID->CurrentValue = NULL;
		$this->osca_ID->OldValue = $this->osca_ID->CurrentValue;
		$this->PlaceIssued->CurrentValue = NULL;
		$this->PlaceIssued->OldValue = $this->PlaceIssued->CurrentValue;
		$this->DateIssued->CurrentValue = NULL;
		$this->DateIssued->OldValue = $this->DateIssued->CurrentValue;
		$this->firstname->CurrentValue = NULL;
		$this->firstname->OldValue = $this->firstname->CurrentValue;
		$this->middlename->CurrentValue = NULL;
		$this->middlename->OldValue = $this->middlename->CurrentValue;
		$this->lastname->CurrentValue = NULL;
		$this->lastname->OldValue = $this->lastname->CurrentValue;
		$this->extname->CurrentValue = NULL;
		$this->extname->OldValue = $this->extname->CurrentValue;
		$this->Birthdate->CurrentValue = NULL;
		$this->Birthdate->OldValue = $this->Birthdate->CurrentValue;
		$this->sex->CurrentValue = NULL;
		$this->sex->OldValue = $this->sex->CurrentValue;
		$this->MaritalID->CurrentValue = NULL;
		$this->MaritalID->OldValue = $this->MaritalID->CurrentValue;
		$this->affliationID->CurrentValue = 0;
		$this->psgc_region->CurrentValue = NULL;
		$this->psgc_region->OldValue = $this->psgc_region->CurrentValue;
		$this->psgc_province->CurrentValue = NULL;
		$this->psgc_province->OldValue = $this->psgc_province->CurrentValue;
		$this->psgc_municipality->CurrentValue = NULL;
		$this->psgc_municipality->OldValue = $this->psgc_municipality->CurrentValue;
		$this->psgc_brgy->CurrentValue = NULL;
		$this->psgc_brgy->OldValue = $this->psgc_brgy->CurrentValue;
		$this->given_add->CurrentValue = NULL;
		$this->given_add->OldValue = $this->given_add->CurrentValue;
		$this->Status->CurrentValue = 1;
		$this->paymentmodeID->CurrentValue = NULL;
		$this->paymentmodeID->OldValue = $this->paymentmodeID->CurrentValue;
		$this->ArrangementID->CurrentValue = NULL;
		$this->ArrangementID->OldValue = $this->ArrangementID->CurrentValue;
		$this->is_4ps->CurrentValue = 0;
		$this->abandoned->CurrentValue = 0;
		$this->Createdby->CurrentValue = NULL;
		$this->Createdby->OldValue = $this->Createdby->CurrentValue;
		$this->CreatedDate->CurrentValue = NULL;
		$this->CreatedDate->OldValue = $this->CreatedDate->CurrentValue;
		$this->picture->Upload->DbValue = NULL;
		$this->picture->OldValue = $this->picture->Upload->DbValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		$this->GetUploadFiles(); // Get upload files
		if (!$this->InclusionDate->FldIsDetailKey) {
			$this->InclusionDate->setFormValue($objForm->GetValue("x_InclusionDate"));
			$this->InclusionDate->CurrentValue = ew_UnFormatDateTime($this->InclusionDate->CurrentValue, 6);
		}
		if (!$this->hh_id->FldIsDetailKey) {
			$this->hh_id->setFormValue($objForm->GetValue("x_hh_id"));
		}
		if (!$this->osca_ID->FldIsDetailKey) {
			$this->osca_ID->setFormValue($objForm->GetValue("x_osca_ID"));
		}
		if (!$this->PlaceIssued->FldIsDetailKey) {
			$this->PlaceIssued->setFormValue($objForm->GetValue("x_PlaceIssued"));
		}
		if (!$this->DateIssued->FldIsDetailKey) {
			$this->DateIssued->setFormValue($objForm->GetValue("x_DateIssued"));
			$this->DateIssued->CurrentValue = ew_UnFormatDateTime($this->DateIssued->CurrentValue, 6);
		}
		if (!$this->firstname->FldIsDetailKey) {
			$this->firstname->setFormValue($objForm->GetValue("x_firstname"));
		}
		if (!$this->middlename->FldIsDetailKey) {
			$this->middlename->setFormValue($objForm->GetValue("x_middlename"));
		}
		if (!$this->lastname->FldIsDetailKey) {
			$this->lastname->setFormValue($objForm->GetValue("x_lastname"));
		}
		if (!$this->extname->FldIsDetailKey) {
			$this->extname->setFormValue($objForm->GetValue("x_extname"));
		}
		if (!$this->Birthdate->FldIsDetailKey) {
			$this->Birthdate->setFormValue($objForm->GetValue("x_Birthdate"));
			$this->Birthdate->CurrentValue = ew_UnFormatDateTime($this->Birthdate->CurrentValue, 6);
		}
		if (!$this->sex->FldIsDetailKey) {
			$this->sex->setFormValue($objForm->GetValue("x_sex"));
		}
		if (!$this->MaritalID->FldIsDetailKey) {
			$this->MaritalID->setFormValue($objForm->GetValue("x_MaritalID"));
		}
		if (!$this->affliationID->FldIsDetailKey) {
			$this->affliationID->setFormValue($objForm->GetValue("x_affliationID"));
		}
		if (!$this->psgc_region->FldIsDetailKey) {
			$this->psgc_region->setFormValue($objForm->GetValue("x_psgc_region"));
		}
		if (!$this->psgc_province->FldIsDetailKey) {
			$this->psgc_province->setFormValue($objForm->GetValue("x_psgc_province"));
		}
		if (!$this->psgc_municipality->FldIsDetailKey) {
			$this->psgc_municipality->setFormValue($objForm->GetValue("x_psgc_municipality"));
		}
		if (!$this->psgc_brgy->FldIsDetailKey) {
			$this->psgc_brgy->setFormValue($objForm->GetValue("x_psgc_brgy"));
		}
		if (!$this->given_add->FldIsDetailKey) {
			$this->given_add->setFormValue($objForm->GetValue("x_given_add"));
		}
		if (!$this->Status->FldIsDetailKey) {
			$this->Status->setFormValue($objForm->GetValue("x_Status"));
		}
		if (!$this->paymentmodeID->FldIsDetailKey) {
			$this->paymentmodeID->setFormValue($objForm->GetValue("x_paymentmodeID"));
		}
		if (!$this->ArrangementID->FldIsDetailKey) {
			$this->ArrangementID->setFormValue($objForm->GetValue("x_ArrangementID"));
		}
		if (!$this->is_4ps->FldIsDetailKey) {
			$this->is_4ps->setFormValue($objForm->GetValue("x_is_4ps"));
		}
		if (!$this->abandoned->FldIsDetailKey) {
			$this->abandoned->setFormValue($objForm->GetValue("x_abandoned"));
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
		$this->InclusionDate->CurrentValue = $this->InclusionDate->FormValue;
		$this->InclusionDate->CurrentValue = ew_UnFormatDateTime($this->InclusionDate->CurrentValue, 6);
		$this->hh_id->CurrentValue = $this->hh_id->FormValue;
		$this->osca_ID->CurrentValue = $this->osca_ID->FormValue;
		$this->PlaceIssued->CurrentValue = $this->PlaceIssued->FormValue;
		$this->DateIssued->CurrentValue = $this->DateIssued->FormValue;
		$this->DateIssued->CurrentValue = ew_UnFormatDateTime($this->DateIssued->CurrentValue, 6);
		$this->firstname->CurrentValue = $this->firstname->FormValue;
		$this->middlename->CurrentValue = $this->middlename->FormValue;
		$this->lastname->CurrentValue = $this->lastname->FormValue;
		$this->extname->CurrentValue = $this->extname->FormValue;
		$this->Birthdate->CurrentValue = $this->Birthdate->FormValue;
		$this->Birthdate->CurrentValue = ew_UnFormatDateTime($this->Birthdate->CurrentValue, 6);
		$this->sex->CurrentValue = $this->sex->FormValue;
		$this->MaritalID->CurrentValue = $this->MaritalID->FormValue;
		$this->affliationID->CurrentValue = $this->affliationID->FormValue;
		$this->psgc_region->CurrentValue = $this->psgc_region->FormValue;
		$this->psgc_province->CurrentValue = $this->psgc_province->FormValue;
		$this->psgc_municipality->CurrentValue = $this->psgc_municipality->FormValue;
		$this->psgc_brgy->CurrentValue = $this->psgc_brgy->FormValue;
		$this->given_add->CurrentValue = $this->given_add->FormValue;
		$this->Status->CurrentValue = $this->Status->FormValue;
		$this->paymentmodeID->CurrentValue = $this->paymentmodeID->FormValue;
		$this->ArrangementID->CurrentValue = $this->ArrangementID->FormValue;
		$this->is_4ps->CurrentValue = $this->is_4ps->FormValue;
		$this->abandoned->CurrentValue = $this->abandoned->FormValue;
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
		$this->SeniorID->setDbValue($rs->fields('SeniorID'));
		$this->PensionerID->setDbValue($rs->fields('PensionerID'));
		$this->InclusionDate->setDbValue($rs->fields('InclusionDate'));
		$this->hh_id->setDbValue($rs->fields('hh_id'));
		$this->osca_ID->setDbValue($rs->fields('osca_ID'));
		$this->PlaceIssued->setDbValue($rs->fields('PlaceIssued'));
		$this->DateIssued->setDbValue($rs->fields('DateIssued'));
		$this->firstname->setDbValue($rs->fields('firstname'));
		$this->middlename->setDbValue($rs->fields('middlename'));
		$this->lastname->setDbValue($rs->fields('lastname'));
		$this->extname->setDbValue($rs->fields('extname'));
		$this->Birthdate->setDbValue($rs->fields('Birthdate'));
		$this->sex->setDbValue($rs->fields('sex'));
		$this->MaritalID->setDbValue($rs->fields('MaritalID'));
		$this->affliationID->setDbValue($rs->fields('affliationID'));
		$this->psgc_region->setDbValue($rs->fields('psgc_region'));
		$this->psgc_province->setDbValue($rs->fields('psgc_province'));
		$this->psgc_municipality->setDbValue($rs->fields('psgc_municipality'));
		$this->psgc_brgy->setDbValue($rs->fields('psgc_brgy'));
		$this->given_add->setDbValue($rs->fields('given_add'));
		$this->Status->setDbValue($rs->fields('Status'));
		$this->paymentmodeID->setDbValue($rs->fields('paymentmodeID'));
		$this->approved->setDbValue($rs->fields('approved'));
		$this->approvedby->setDbValue($rs->fields('approvedby'));
		$this->DateApproved->setDbValue($rs->fields('DateApproved'));
		$this->ArrangementID->setDbValue($rs->fields('ArrangementID'));
		$this->is_4ps->setDbValue($rs->fields('is_4ps'));
		$this->abandoned->setDbValue($rs->fields('abandoned'));
		$this->Createdby->setDbValue($rs->fields('Createdby'));
		$this->CreatedDate->setDbValue($rs->fields('CreatedDate'));
		$this->UpdatedBy->setDbValue($rs->fields('UpdatedBy'));
		$this->UpdatedDate->setDbValue($rs->fields('UpdatedDate'));
		$this->UpdateRemarks->setDbValue($rs->fields('UpdateRemarks'));
		$this->codeGen->setDbValue($rs->fields('codeGen'));
		$this->picture->Upload->DbValue = $rs->fields('picture');
		$this->picturename->setDbValue($rs->fields('picturename'));
		$this->picturetype->setDbValue($rs->fields('picturetype'));
		$this->picturewidth->setDbValue($rs->fields('picturewidth'));
		$this->pictureheight->setDbValue($rs->fields('pictureheight'));
		$this->picturesize->setDbValue($rs->fields('picturesize'));
		$this->hyperlink->setDbValue($rs->fields('hyperlink'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->SeniorID->DbValue = $row['SeniorID'];
		$this->PensionerID->DbValue = $row['PensionerID'];
		$this->InclusionDate->DbValue = $row['InclusionDate'];
		$this->hh_id->DbValue = $row['hh_id'];
		$this->osca_ID->DbValue = $row['osca_ID'];
		$this->PlaceIssued->DbValue = $row['PlaceIssued'];
		$this->DateIssued->DbValue = $row['DateIssued'];
		$this->firstname->DbValue = $row['firstname'];
		$this->middlename->DbValue = $row['middlename'];
		$this->lastname->DbValue = $row['lastname'];
		$this->extname->DbValue = $row['extname'];
		$this->Birthdate->DbValue = $row['Birthdate'];
		$this->sex->DbValue = $row['sex'];
		$this->MaritalID->DbValue = $row['MaritalID'];
		$this->affliationID->DbValue = $row['affliationID'];
		$this->psgc_region->DbValue = $row['psgc_region'];
		$this->psgc_province->DbValue = $row['psgc_province'];
		$this->psgc_municipality->DbValue = $row['psgc_municipality'];
		$this->psgc_brgy->DbValue = $row['psgc_brgy'];
		$this->given_add->DbValue = $row['given_add'];
		$this->Status->DbValue = $row['Status'];
		$this->paymentmodeID->DbValue = $row['paymentmodeID'];
		$this->approved->DbValue = $row['approved'];
		$this->approvedby->DbValue = $row['approvedby'];
		$this->DateApproved->DbValue = $row['DateApproved'];
		$this->ArrangementID->DbValue = $row['ArrangementID'];
		$this->is_4ps->DbValue = $row['is_4ps'];
		$this->abandoned->DbValue = $row['abandoned'];
		$this->Createdby->DbValue = $row['Createdby'];
		$this->CreatedDate->DbValue = $row['CreatedDate'];
		$this->UpdatedBy->DbValue = $row['UpdatedBy'];
		$this->UpdatedDate->DbValue = $row['UpdatedDate'];
		$this->UpdateRemarks->DbValue = $row['UpdateRemarks'];
		$this->codeGen->DbValue = $row['codeGen'];
		$this->picture->Upload->DbValue = $row['picture'];
		$this->picturename->DbValue = $row['picturename'];
		$this->picturetype->DbValue = $row['picturetype'];
		$this->picturewidth->DbValue = $row['picturewidth'];
		$this->pictureheight->DbValue = $row['pictureheight'];
		$this->picturesize->DbValue = $row['picturesize'];
		$this->hyperlink->DbValue = $row['hyperlink'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("SeniorID")) <> "")
			$this->SeniorID->CurrentValue = $this->getKey("SeniorID"); // SeniorID
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
		// SeniorID
		// PensionerID
		// InclusionDate
		// hh_id
		// osca_ID
		// PlaceIssued
		// DateIssued
		// firstname
		// middlename
		// lastname
		// extname
		// Birthdate
		// sex
		// MaritalID
		// affliationID
		// psgc_region
		// psgc_province
		// psgc_municipality
		// psgc_brgy
		// given_add
		// Status
		// paymentmodeID
		// approved
		// approvedby
		// DateApproved
		// ArrangementID
		// is_4ps
		// abandoned
		// Createdby
		// CreatedDate
		// UpdatedBy
		// UpdatedDate
		// UpdateRemarks
		// codeGen
		// picture
		// picturename
		// picturetype
		// picturewidth
		// pictureheight
		// picturesize
		// hyperlink

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// SeniorID
			$this->SeniorID->ViewValue = $this->SeniorID->CurrentValue;
			$this->SeniorID->ViewCustomAttributes = "";

			// PensionerID
			$this->PensionerID->ViewValue = $this->PensionerID->CurrentValue;
			$this->PensionerID->ViewCustomAttributes = "";

			// InclusionDate
			$this->InclusionDate->ViewValue = $this->InclusionDate->CurrentValue;
			$this->InclusionDate->ViewValue = ew_FormatDateTime($this->InclusionDate->ViewValue, 6);
			$this->InclusionDate->ViewCustomAttributes = "";

			// hh_id
			$this->hh_id->ViewValue = $this->hh_id->CurrentValue;
			$this->hh_id->ViewCustomAttributes = "";

			// osca_ID
			$this->osca_ID->ViewValue = $this->osca_ID->CurrentValue;
			$this->osca_ID->ViewCustomAttributes = "";

			// PlaceIssued
			$this->PlaceIssued->ViewValue = $this->PlaceIssued->CurrentValue;
			$this->PlaceIssued->ViewCustomAttributes = "";

			// DateIssued
			$this->DateIssued->ViewValue = $this->DateIssued->CurrentValue;
			$this->DateIssued->ViewValue = ew_FormatDateTime($this->DateIssued->ViewValue, 6);
			$this->DateIssued->ViewCustomAttributes = "";

			// firstname
			$this->firstname->ViewValue = $this->firstname->CurrentValue;
			$this->firstname->ViewCustomAttributes = "";

			// middlename
			$this->middlename->ViewValue = $this->middlename->CurrentValue;
			$this->middlename->ViewCustomAttributes = "";

			// lastname
			$this->lastname->ViewValue = $this->lastname->CurrentValue;
			$this->lastname->ViewCustomAttributes = "";

			// extname
			$this->extname->ViewValue = $this->extname->CurrentValue;
			$this->extname->ViewCustomAttributes = "";

			// Birthdate
			$this->Birthdate->ViewValue = $this->Birthdate->CurrentValue;
			$this->Birthdate->ViewValue = ew_FormatDateTime($this->Birthdate->ViewValue, 6);
			$this->Birthdate->ViewCustomAttributes = "";

			// sex
			if (strval($this->sex->CurrentValue) <> "") {
				switch ($this->sex->CurrentValue) {
					case $this->sex->FldTagValue(1):
						$this->sex->ViewValue = $this->sex->FldTagCaption(1) <> "" ? $this->sex->FldTagCaption(1) : $this->sex->CurrentValue;
						break;
					case $this->sex->FldTagValue(2):
						$this->sex->ViewValue = $this->sex->FldTagCaption(2) <> "" ? $this->sex->FldTagCaption(2) : $this->sex->CurrentValue;
						break;
					default:
						$this->sex->ViewValue = $this->sex->CurrentValue;
				}
			} else {
				$this->sex->ViewValue = NULL;
			}
			$this->sex->ViewCustomAttributes = "";

			// MaritalID
			if (strval($this->MaritalID->CurrentValue) <> "") {
				$sFilterWrk = "`MaritalID`" . ew_SearchString("=", $this->MaritalID->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `MaritalID`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_civilstatus`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->MaritalID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `MaritalID` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->MaritalID->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->MaritalID->ViewValue = $this->MaritalID->CurrentValue;
				}
			} else {
				$this->MaritalID->ViewValue = NULL;
			}
			$this->MaritalID->ViewCustomAttributes = "";

			// affliationID
			if (strval($this->affliationID->CurrentValue) <> "") {
				$sFilterWrk = "`affliationID`" . ew_SearchString("=", $this->affliationID->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `affliationID`, `aff_description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_affliation`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->affliationID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `affliationID` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->affliationID->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->affliationID->ViewValue = $this->affliationID->CurrentValue;
				}
			} else {
				$this->affliationID->ViewValue = NULL;
			}
			$this->affliationID->ViewCustomAttributes = "";

			// psgc_region
			if (strval($this->psgc_region->CurrentValue) <> "") {
				$sFilterWrk = "`region_code`" . ew_SearchString("=", $this->psgc_region->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `region_code`, `region_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_regions`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->psgc_region, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `region_code` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->psgc_region->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->psgc_region->ViewValue = $this->psgc_region->CurrentValue;
				}
			} else {
				$this->psgc_region->ViewValue = NULL;
			}
			$this->psgc_region->ViewCustomAttributes = "";

			// psgc_province
			if (strval($this->psgc_province->CurrentValue) <> "") {
				$sFilterWrk = "`prov_code`" . ew_SearchString("=", $this->psgc_province->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `prov_code`, `prov_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_provinces`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->psgc_province, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `prov_name` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->psgc_province->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->psgc_province->ViewValue = $this->psgc_province->CurrentValue;
				}
			} else {
				$this->psgc_province->ViewValue = NULL;
			}
			$this->psgc_province->ViewCustomAttributes = "";

			// psgc_municipality
			if (strval($this->psgc_municipality->CurrentValue) <> "") {
				$sFilterWrk = "`city_code`" . ew_SearchString("=", $this->psgc_municipality->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `city_code`, `city_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_cities`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->psgc_municipality, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `city_name` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->psgc_municipality->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->psgc_municipality->ViewValue = $this->psgc_municipality->CurrentValue;
				}
			} else {
				$this->psgc_municipality->ViewValue = NULL;
			}
			$this->psgc_municipality->ViewCustomAttributes = "";

			// psgc_brgy
			if (strval($this->psgc_brgy->CurrentValue) <> "") {
				$sFilterWrk = "`brgy_code`" . ew_SearchString("=", $this->psgc_brgy->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `brgy_code`, `brgy_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_brgy`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->psgc_brgy, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `brgy_name` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->psgc_brgy->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->psgc_brgy->ViewValue = $this->psgc_brgy->CurrentValue;
				}
			} else {
				$this->psgc_brgy->ViewValue = NULL;
			}
			$this->psgc_brgy->ViewCustomAttributes = "";

			// given_add
			$this->given_add->ViewValue = $this->given_add->CurrentValue;
			$this->given_add->ViewCustomAttributes = "";

			// Status
			if (strval($this->Status->CurrentValue) <> "") {
				$sFilterWrk = "`statusID`" . ew_SearchString("=", $this->Status->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `statusID`, `status` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_status`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Status, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `statusID` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->Status->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->Status->ViewValue = $this->Status->CurrentValue;
				}
			} else {
				$this->Status->ViewValue = NULL;
			}
			$this->Status->ViewCustomAttributes = "";

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

			// approved
			$this->approved->ViewValue = $this->approved->CurrentValue;
			$this->approved->ViewCustomAttributes = "";

			// approvedby
			$this->approvedby->ViewValue = $this->approvedby->CurrentValue;
			$this->approvedby->ViewCustomAttributes = "";

			// DateApproved
			$this->DateApproved->ViewValue = $this->DateApproved->CurrentValue;
			$this->DateApproved->ViewValue = ew_FormatDateTime($this->DateApproved->ViewValue, 6);
			$this->DateApproved->ViewCustomAttributes = "";

			// ArrangementID
			if (strval($this->ArrangementID->CurrentValue) <> "") {
				$sFilterWrk = "`ArrangementID`" . ew_SearchString("=", $this->ArrangementID->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `ArrangementID`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_arrangement`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->ArrangementID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `ArrangementID` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->ArrangementID->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->ArrangementID->ViewValue = $this->ArrangementID->CurrentValue;
				}
			} else {
				$this->ArrangementID->ViewValue = NULL;
			}
			$this->ArrangementID->ViewCustomAttributes = "";

			// is_4ps
			if (strval($this->is_4ps->CurrentValue) <> "") {
				switch ($this->is_4ps->CurrentValue) {
					case $this->is_4ps->FldTagValue(1):
						$this->is_4ps->ViewValue = $this->is_4ps->FldTagCaption(1) <> "" ? $this->is_4ps->FldTagCaption(1) : $this->is_4ps->CurrentValue;
						break;
					case $this->is_4ps->FldTagValue(2):
						$this->is_4ps->ViewValue = $this->is_4ps->FldTagCaption(2) <> "" ? $this->is_4ps->FldTagCaption(2) : $this->is_4ps->CurrentValue;
						break;
					default:
						$this->is_4ps->ViewValue = $this->is_4ps->CurrentValue;
				}
			} else {
				$this->is_4ps->ViewValue = NULL;
			}
			$this->is_4ps->ViewCustomAttributes = "";

			// abandoned
			if (strval($this->abandoned->CurrentValue) <> "") {
				switch ($this->abandoned->CurrentValue) {
					case $this->abandoned->FldTagValue(1):
						$this->abandoned->ViewValue = $this->abandoned->FldTagCaption(1) <> "" ? $this->abandoned->FldTagCaption(1) : $this->abandoned->CurrentValue;
						break;
					case $this->abandoned->FldTagValue(2):
						$this->abandoned->ViewValue = $this->abandoned->FldTagCaption(2) <> "" ? $this->abandoned->FldTagCaption(2) : $this->abandoned->CurrentValue;
						break;
					default:
						$this->abandoned->ViewValue = $this->abandoned->CurrentValue;
				}
			} else {
				$this->abandoned->ViewValue = NULL;
			}
			$this->abandoned->ViewCustomAttributes = "";

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

			// UpdateRemarks
			$this->UpdateRemarks->ViewValue = $this->UpdateRemarks->CurrentValue;
			$this->UpdateRemarks->ViewCustomAttributes = "";

			// codeGen
			$this->codeGen->ViewValue = $this->codeGen->CurrentValue;
			$this->codeGen->ViewCustomAttributes = "";

			// picture
			if (!ew_Empty($this->picture->Upload->DbValue)) {
				if (!is_null($this->picturewidth->CurrentValue)) {
					$this->picture->ImageWidth = $this->picturewidth->CurrentValue;
				} else {
					$this->picture->ImageWidth = 0;
				}
				if (!is_null($this->pictureheight->CurrentValue)) {
					$this->picture->ImageHeight = $this->pictureheight->CurrentValue;
				} else {
					$this->picture->ImageHeight = 0;
				}
				$this->picture->ImageAlt = $this->picture->FldAlt();
				$this->picture->ViewValue = "tbl_pensioner_picture_bv.php?" . "SeniorID=" . $this->SeniorID->CurrentValue;
			} else {
				$this->picture->ViewValue = "";
			}
			$this->picture->ViewCustomAttributes = "";

			// InclusionDate
			$this->InclusionDate->LinkCustomAttributes = "";
			$this->InclusionDate->HrefValue = "";
			$this->InclusionDate->TooltipValue = "";

			// hh_id
			$this->hh_id->LinkCustomAttributes = "";
			$this->hh_id->HrefValue = "";
			$this->hh_id->TooltipValue = "";

			// osca_ID
			$this->osca_ID->LinkCustomAttributes = "";
			$this->osca_ID->HrefValue = "";
			$this->osca_ID->TooltipValue = "";

			// PlaceIssued
			$this->PlaceIssued->LinkCustomAttributes = "";
			$this->PlaceIssued->HrefValue = "";
			$this->PlaceIssued->TooltipValue = "";

			// DateIssued
			$this->DateIssued->LinkCustomAttributes = "";
			$this->DateIssued->HrefValue = "";
			$this->DateIssued->TooltipValue = "";

			// firstname
			$this->firstname->LinkCustomAttributes = "";
			$this->firstname->HrefValue = "";
			$this->firstname->TooltipValue = "";

			// middlename
			$this->middlename->LinkCustomAttributes = "";
			$this->middlename->HrefValue = "";
			$this->middlename->TooltipValue = "";

			// lastname
			$this->lastname->LinkCustomAttributes = "";
			$this->lastname->HrefValue = "";
			$this->lastname->TooltipValue = "";

			// extname
			$this->extname->LinkCustomAttributes = "";
			$this->extname->HrefValue = "";
			$this->extname->TooltipValue = "";

			// Birthdate
			$this->Birthdate->LinkCustomAttributes = "";
			$this->Birthdate->HrefValue = "";
			$this->Birthdate->TooltipValue = "";

			// sex
			$this->sex->LinkCustomAttributes = "";
			$this->sex->HrefValue = "";
			$this->sex->TooltipValue = "";

			// MaritalID
			$this->MaritalID->LinkCustomAttributes = "";
			$this->MaritalID->HrefValue = "";
			$this->MaritalID->TooltipValue = "";

			// affliationID
			$this->affliationID->LinkCustomAttributes = "";
			$this->affliationID->HrefValue = "";
			$this->affliationID->TooltipValue = "";

			// psgc_region
			$this->psgc_region->LinkCustomAttributes = "";
			$this->psgc_region->HrefValue = "";
			$this->psgc_region->TooltipValue = "";

			// psgc_province
			$this->psgc_province->LinkCustomAttributes = "";
			$this->psgc_province->HrefValue = "";
			$this->psgc_province->TooltipValue = "";

			// psgc_municipality
			$this->psgc_municipality->LinkCustomAttributes = "";
			$this->psgc_municipality->HrefValue = "";
			$this->psgc_municipality->TooltipValue = "";

			// psgc_brgy
			$this->psgc_brgy->LinkCustomAttributes = "";
			$this->psgc_brgy->HrefValue = "";
			$this->psgc_brgy->TooltipValue = "";

			// given_add
			$this->given_add->LinkCustomAttributes = "";
			$this->given_add->HrefValue = "";
			$this->given_add->TooltipValue = "";

			// Status
			$this->Status->LinkCustomAttributes = "";
			$this->Status->HrefValue = "";
			$this->Status->TooltipValue = "";

			// paymentmodeID
			$this->paymentmodeID->LinkCustomAttributes = "";
			$this->paymentmodeID->HrefValue = "";
			$this->paymentmodeID->TooltipValue = "";

			// ArrangementID
			$this->ArrangementID->LinkCustomAttributes = "";
			$this->ArrangementID->HrefValue = "";
			$this->ArrangementID->TooltipValue = "";

			// is_4ps
			$this->is_4ps->LinkCustomAttributes = "";
			$this->is_4ps->HrefValue = "";
			$this->is_4ps->TooltipValue = "";

			// abandoned
			$this->abandoned->LinkCustomAttributes = "";
			$this->abandoned->HrefValue = "";
			$this->abandoned->TooltipValue = "";

			// Createdby
			$this->Createdby->LinkCustomAttributes = "";
			$this->Createdby->HrefValue = "";
			$this->Createdby->TooltipValue = "";

			// CreatedDate
			$this->CreatedDate->LinkCustomAttributes = "";
			$this->CreatedDate->HrefValue = "";
			$this->CreatedDate->TooltipValue = "";

			// picture
			$this->picture->LinkCustomAttributes = "";
			if (!ew_Empty($this->hyperlink->CurrentValue)) {
				$this->picture->HrefValue = ((!empty($this->hyperlink->ViewValue)) ? $this->hyperlink->ViewValue : $this->hyperlink->CurrentValue); // Add prefix/suffix
				$this->picture->LinkAttrs["target"] = ""; // Add target
				if ($this->Export <> "") $this->picture->HrefValue = ew_ConvertFullUrl($this->picture->HrefValue);
			} else {
				$this->picture->HrefValue = "";
			}
			$this->picture->HrefValue2 = "tbl_pensioner_picture_bv.php?SeniorID=" . $this->SeniorID->CurrentValue;
			$this->picture->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// InclusionDate
			$this->InclusionDate->EditCustomAttributes = "";
			$this->InclusionDate->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->InclusionDate->CurrentValue, 6));
			$this->InclusionDate->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->InclusionDate->FldCaption()));

			// hh_id
			$this->hh_id->EditCustomAttributes = "";
			$this->hh_id->EditValue = ew_HtmlEncode($this->hh_id->CurrentValue);
			$this->hh_id->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->hh_id->FldCaption()));

			// osca_ID
			$this->osca_ID->EditCustomAttributes = "";
			$this->osca_ID->EditValue = ew_HtmlEncode($this->osca_ID->CurrentValue);
			$this->osca_ID->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->osca_ID->FldCaption()));

			// PlaceIssued
			$this->PlaceIssued->EditCustomAttributes = "";
			$this->PlaceIssued->EditValue = ew_HtmlEncode($this->PlaceIssued->CurrentValue);
			$this->PlaceIssued->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->PlaceIssued->FldCaption()));

			// DateIssued
			$this->DateIssued->EditCustomAttributes = "";
			$this->DateIssued->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->DateIssued->CurrentValue, 6));
			$this->DateIssued->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->DateIssued->FldCaption()));

			// firstname
			$this->firstname->EditCustomAttributes = "";
			$this->firstname->EditValue = ew_HtmlEncode($this->firstname->CurrentValue);
			$this->firstname->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->firstname->FldCaption()));

			// middlename
			$this->middlename->EditCustomAttributes = "";
			$this->middlename->EditValue = ew_HtmlEncode($this->middlename->CurrentValue);
			$this->middlename->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->middlename->FldCaption()));

			// lastname
			$this->lastname->EditCustomAttributes = "";
			$this->lastname->EditValue = ew_HtmlEncode($this->lastname->CurrentValue);
			$this->lastname->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->lastname->FldCaption()));

			// extname
			$this->extname->EditCustomAttributes = "";
			$this->extname->EditValue = ew_HtmlEncode($this->extname->CurrentValue);
			$this->extname->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->extname->FldCaption()));

			// Birthdate
			$this->Birthdate->EditCustomAttributes = "";
			$this->Birthdate->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->Birthdate->CurrentValue, 6));
			$this->Birthdate->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->Birthdate->FldCaption()));

			// sex
			$this->sex->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->sex->FldTagValue(1), $this->sex->FldTagCaption(1) <> "" ? $this->sex->FldTagCaption(1) : $this->sex->FldTagValue(1));
			$arwrk[] = array($this->sex->FldTagValue(2), $this->sex->FldTagCaption(2) <> "" ? $this->sex->FldTagCaption(2) : $this->sex->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->sex->EditValue = $arwrk;

			// MaritalID
			$this->MaritalID->EditCustomAttributes = "";
			if (trim(strval($this->MaritalID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`MaritalID`" . ew_SearchString("=", $this->MaritalID->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `MaritalID`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_civilstatus`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->MaritalID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `MaritalID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->MaritalID->EditValue = $arwrk;

			// affliationID
			$this->affliationID->EditCustomAttributes = "";
			if (trim(strval($this->affliationID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`affliationID`" . ew_SearchString("=", $this->affliationID->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `affliationID`, `aff_description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_affliation`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->affliationID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `affliationID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->affliationID->EditValue = $arwrk;

			// psgc_region
			$this->psgc_region->EditCustomAttributes = "";
			if (trim(strval($this->psgc_region->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`region_code`" . ew_SearchString("=", $this->psgc_region->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `region_code`, `region_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_regions`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->psgc_region, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `region_code` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->psgc_region->EditValue = $arwrk;

			// psgc_province
			$this->psgc_province->EditCustomAttributes = "";
			if (trim(strval($this->psgc_province->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`prov_code`" . ew_SearchString("=", $this->psgc_province->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `prov_code`, `prov_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `region_code` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_provinces`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->psgc_province, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `prov_name` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->psgc_province->EditValue = $arwrk;

			// psgc_municipality
			$this->psgc_municipality->EditCustomAttributes = "";
			if (trim(strval($this->psgc_municipality->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`city_code`" . ew_SearchString("=", $this->psgc_municipality->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `city_code`, `city_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `prov_code` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_cities`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->psgc_municipality, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `city_name` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->psgc_municipality->EditValue = $arwrk;

			// psgc_brgy
			$this->psgc_brgy->EditCustomAttributes = "";
			if (trim(strval($this->psgc_brgy->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`brgy_code`" . ew_SearchString("=", $this->psgc_brgy->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `brgy_code`, `brgy_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `city_code` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_brgy`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->psgc_brgy, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `brgy_name` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->psgc_brgy->EditValue = $arwrk;

			// given_add
			$this->given_add->EditCustomAttributes = "";
			$this->given_add->EditValue = ew_HtmlEncode($this->given_add->CurrentValue);
			$this->given_add->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->given_add->FldCaption()));

			// Status
			$this->Status->EditCustomAttributes = "";
			if (trim(strval($this->Status->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`statusID`" . ew_SearchString("=", $this->Status->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `statusID`, `status` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_status`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Status, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `statusID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->Status->EditValue = $arwrk;

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

			// ArrangementID
			$this->ArrangementID->EditCustomAttributes = "";
			if (trim(strval($this->ArrangementID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`ArrangementID`" . ew_SearchString("=", $this->ArrangementID->CurrentValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `ArrangementID`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_arrangement`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->ArrangementID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `ArrangementID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->ArrangementID->EditValue = $arwrk;

			// is_4ps
			$this->is_4ps->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->is_4ps->FldTagValue(1), $this->is_4ps->FldTagCaption(1) <> "" ? $this->is_4ps->FldTagCaption(1) : $this->is_4ps->FldTagValue(1));
			$arwrk[] = array($this->is_4ps->FldTagValue(2), $this->is_4ps->FldTagCaption(2) <> "" ? $this->is_4ps->FldTagCaption(2) : $this->is_4ps->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->is_4ps->EditValue = $arwrk;

			// abandoned
			$this->abandoned->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->abandoned->FldTagValue(1), $this->abandoned->FldTagCaption(1) <> "" ? $this->abandoned->FldTagCaption(1) : $this->abandoned->FldTagValue(1));
			$arwrk[] = array($this->abandoned->FldTagValue(2), $this->abandoned->FldTagCaption(2) <> "" ? $this->abandoned->FldTagCaption(2) : $this->abandoned->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->abandoned->EditValue = $arwrk;

			// Createdby
			// CreatedDate
			// picture

			$this->picture->EditCustomAttributes = "";
			if (!ew_Empty($this->picture->Upload->DbValue)) {
				if (!is_null($this->picturewidth->CurrentValue)) {
					$this->picture->ImageWidth = $this->picturewidth->CurrentValue;
				} else {
					$this->picture->ImageWidth = 0;
				}
				if (!is_null($this->pictureheight->CurrentValue)) {
					$this->picture->ImageHeight = $this->pictureheight->CurrentValue;
				} else {
					$this->picture->ImageHeight = 0;
				}
				$this->picture->ImageAlt = $this->picture->FldAlt();
				$this->picture->EditValue = "tbl_pensioner_picture_bv.php?" . "SeniorID=" . $this->SeniorID->CurrentValue;
			} else {
				$this->picture->EditValue = "";
			}
			if (($this->CurrentAction == "I" || $this->CurrentAction == "C") && !$this->EventCancelled) ew_RenderUploadField($this->picture);

			// Edit refer script
			// InclusionDate

			$this->InclusionDate->HrefValue = "";

			// hh_id
			$this->hh_id->HrefValue = "";

			// osca_ID
			$this->osca_ID->HrefValue = "";

			// PlaceIssued
			$this->PlaceIssued->HrefValue = "";

			// DateIssued
			$this->DateIssued->HrefValue = "";

			// firstname
			$this->firstname->HrefValue = "";

			// middlename
			$this->middlename->HrefValue = "";

			// lastname
			$this->lastname->HrefValue = "";

			// extname
			$this->extname->HrefValue = "";

			// Birthdate
			$this->Birthdate->HrefValue = "";

			// sex
			$this->sex->HrefValue = "";

			// MaritalID
			$this->MaritalID->HrefValue = "";

			// affliationID
			$this->affliationID->HrefValue = "";

			// psgc_region
			$this->psgc_region->HrefValue = "";

			// psgc_province
			$this->psgc_province->HrefValue = "";

			// psgc_municipality
			$this->psgc_municipality->HrefValue = "";

			// psgc_brgy
			$this->psgc_brgy->HrefValue = "";

			// given_add
			$this->given_add->HrefValue = "";

			// Status
			$this->Status->HrefValue = "";

			// paymentmodeID
			$this->paymentmodeID->HrefValue = "";

			// ArrangementID
			$this->ArrangementID->HrefValue = "";

			// is_4ps
			$this->is_4ps->HrefValue = "";

			// abandoned
			$this->abandoned->HrefValue = "";

			// Createdby
			$this->Createdby->HrefValue = "";

			// CreatedDate
			$this->CreatedDate->HrefValue = "";

			// picture
			if (!ew_Empty($this->hyperlink->CurrentValue)) {
				$this->picture->HrefValue = ((!empty($this->hyperlink->EditValue)) ? $this->hyperlink->EditValue : $this->hyperlink->CurrentValue); // Add prefix/suffix
				$this->picture->LinkAttrs["target"] = ""; // Add target
				if ($this->Export <> "") $this->picture->HrefValue = ew_ConvertFullUrl($this->picture->HrefValue);
			} else {
				$this->picture->HrefValue = "";
			}
			$this->picture->HrefValue2 = "tbl_pensioner_picture_bv.php?SeniorID=" . $this->SeniorID->CurrentValue;
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
		if (!ew_CheckUSDate($this->InclusionDate->FormValue)) {
			ew_AddMessage($gsFormError, $this->InclusionDate->FldErrMsg());
		}
		if (!ew_CheckUSDate($this->DateIssued->FormValue)) {
			ew_AddMessage($gsFormError, $this->DateIssued->FldErrMsg());
		}
		if (!$this->firstname->FldIsDetailKey && !is_null($this->firstname->FormValue) && $this->firstname->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->firstname->FldCaption());
		}
		if (!$this->lastname->FldIsDetailKey && !is_null($this->lastname->FormValue) && $this->lastname->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->lastname->FldCaption());
		}
		if (!ew_CheckUSDate($this->Birthdate->FormValue)) {
			ew_AddMessage($gsFormError, $this->Birthdate->FldErrMsg());
		}
		if (!$this->sex->FldIsDetailKey && !is_null($this->sex->FormValue) && $this->sex->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->sex->FldCaption());
		}
		/*if (!$this->affliationID->FldIsDetailKey && !is_null($this->affliationID->FormValue) && $this->affliationID->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->affliationID->FldCaption());
		}*/
		if (!$this->psgc_region->FldIsDetailKey && !is_null($this->psgc_region->FormValue) && $this->psgc_region->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->psgc_region->FldCaption());
		}
		if (!$this->psgc_province->FldIsDetailKey && !is_null($this->psgc_province->FormValue) && $this->psgc_province->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->psgc_province->FldCaption());
		}
		if (!$this->psgc_municipality->FldIsDetailKey && !is_null($this->psgc_municipality->FormValue) && $this->psgc_municipality->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->psgc_municipality->FldCaption());
		}
		if (!$this->psgc_brgy->FldIsDetailKey && !is_null($this->psgc_brgy->FormValue) && $this->psgc_brgy->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->psgc_brgy->FldCaption());
		}
		if (!$this->Status->FldIsDetailKey && !is_null($this->Status->FormValue) && $this->Status->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->Status->FldCaption());
		}
		if (!$this->is_4ps->FldIsDetailKey && !is_null($this->is_4ps->FormValue) && $this->is_4ps->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->is_4ps->FldCaption());
		}
		if (!$this->abandoned->FldIsDetailKey && !is_null($this->abandoned->FormValue) && $this->abandoned->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->abandoned->FldCaption());
		}

		// Validate detail grid
		$DetailTblVar = explode(",", $this->getCurrentDetailTable());
		if (in_array("tbl_representative", $DetailTblVar) && $GLOBALS["tbl_representative"]->DetailAdd) {
			if (!isset($GLOBALS["tbl_representative_grid"])) $GLOBALS["tbl_representative_grid"] = new ctbl_representative_grid(); // get detail page object
			$GLOBALS["tbl_representative_grid"]->ValidateGridForm();
		}
		if (in_array("tbl_support", $DetailTblVar) && $GLOBALS["tbl_support"]->DetailAdd) {
			if (!isset($GLOBALS["tbl_support_grid"])) $GLOBALS["tbl_support_grid"] = new ctbl_support_grid(); // get detail page object
			$GLOBALS["tbl_support_grid"]->ValidateGridForm();
		}
		if (in_array("tbl_updates", $DetailTblVar) && $GLOBALS["tbl_updates"]->DetailAdd) {
			if (!isset($GLOBALS["tbl_updates_grid"])) $GLOBALS["tbl_updates_grid"] = new ctbl_updates_grid(); // get detail page object
			$GLOBALS["tbl_updates_grid"]->ValidateGridForm();
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

		// Begin transaction
		if ($this->getCurrentDetailTable() <> "")
			$conn->BeginTrans();

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
		}
		$rsnew = array();

		// InclusionDate
		$this->InclusionDate->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->InclusionDate->CurrentValue, 6), NULL, FALSE);

		// hh_id
		$this->hh_id->SetDbValueDef($rsnew, $this->hh_id->CurrentValue, NULL, FALSE);

		// osca_ID
		$this->osca_ID->SetDbValueDef($rsnew, $this->osca_ID->CurrentValue, NULL, FALSE);

		// PlaceIssued
		$this->PlaceIssued->SetDbValueDef($rsnew, $this->PlaceIssued->CurrentValue, NULL, FALSE);

		// DateIssued
		$this->DateIssued->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->DateIssued->CurrentValue, 6), NULL, FALSE);

		// firstname
		$this->firstname->SetDbValueDef($rsnew, $this->firstname->CurrentValue, "", FALSE);

		// middlename
		$this->middlename->SetDbValueDef($rsnew, $this->middlename->CurrentValue, NULL, FALSE);

		// lastname
		$this->lastname->SetDbValueDef($rsnew, $this->lastname->CurrentValue, "", FALSE);

		// extname
		$this->extname->SetDbValueDef($rsnew, $this->extname->CurrentValue, NULL, FALSE);

		// Birthdate
		$this->Birthdate->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->Birthdate->CurrentValue, 6), NULL, FALSE);

		// sex
		$this->sex->SetDbValueDef($rsnew, $this->sex->CurrentValue, 0, FALSE);

		// MaritalID
		$this->MaritalID->SetDbValueDef($rsnew, $this->MaritalID->CurrentValue, NULL, FALSE);

		// affliationID
		$this->affliationID->SetDbValueDef($rsnew, $this->affliationID->CurrentValue, 0, strval($this->affliationID->CurrentValue) == "");

		// psgc_region
		$this->psgc_region->SetDbValueDef($rsnew, $this->psgc_region->CurrentValue, 0, FALSE);

		// psgc_province
		$this->psgc_province->SetDbValueDef($rsnew, $this->psgc_province->CurrentValue, 0, FALSE);

		// psgc_municipality
		$this->psgc_municipality->SetDbValueDef($rsnew, $this->psgc_municipality->CurrentValue, 0, FALSE);

		// psgc_brgy
		$this->psgc_brgy->SetDbValueDef($rsnew, $this->psgc_brgy->CurrentValue, 0, FALSE);

		// given_add
		$this->given_add->SetDbValueDef($rsnew, $this->given_add->CurrentValue, NULL, FALSE);

		// Status
		$this->Status->SetDbValueDef($rsnew, $this->Status->CurrentValue, 0, strval($this->Status->CurrentValue) == "");

		// paymentmodeID
		$this->paymentmodeID->SetDbValueDef($rsnew, $this->paymentmodeID->CurrentValue, NULL, FALSE);

		// ArrangementID
		$this->ArrangementID->SetDbValueDef($rsnew, $this->ArrangementID->CurrentValue, NULL, FALSE);

		// is_4ps
		$this->is_4ps->SetDbValueDef($rsnew, $this->is_4ps->CurrentValue, 0, strval($this->is_4ps->CurrentValue) == "");

		// abandoned
		$this->abandoned->SetDbValueDef($rsnew, $this->abandoned->CurrentValue, 0, strval($this->abandoned->CurrentValue) == "");

		// Createdby
		$this->Createdby->SetDbValueDef($rsnew, CurrentUserID(), NULL);
		$rsnew['Createdby'] = &$this->Createdby->DbValue;

		// CreatedDate
		$this->CreatedDate->SetDbValueDef($rsnew, ew_CurrentDateTime(), NULL);
		$rsnew['CreatedDate'] = &$this->CreatedDate->DbValue;

		// picture
		if (!$this->picture->Upload->KeepFile) {
			if (is_null($this->picture->Upload->Value)) {
				$rsnew['picture'] = NULL;
			} else {
				$rsnew['picture'] = $this->picture->Upload->Value;
			}
			$this->picturename->SetDbValueDef($rsnew, $this->picture->Upload->FileName, NULL, FALSE);
			$this->picturetype->SetDbValueDef($rsnew, trim($this->picture->Upload->ContentType), NULL, FALSE);
			$this->picturesize->SetDbValueDef($rsnew, $this->picture->Upload->FileSize, NULL, FALSE);
			$this->picturewidth->SetDbValueDef($rsnew, $this->picture->Upload->ImageWidth, NULL, FALSE);
			$this->pictureheight->SetDbValueDef($rsnew, $this->picture->Upload->ImageHeight, NULL, FALSE);
		}

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);
		if ($bInsertRow) {
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$AddRow = $this->Insert($rsnew);
			$conn->raiseErrorFn = '';
			if ($AddRow) {
				if (!$this->picture->Upload->KeepFile) {
				}
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
			/**
			 * JFSBALDO 12152015: Custom Pensioner ID with algorithm of Barangay PSGC + UserID + Auto Increment Value of Senior ID
			 */
			$this->SeniorID->setDbValue($conn->Insert_ID());
			$rsnew['SeniorID'] = $this->SeniorID->DbValue;
			$rsnew['psgc_brgy'] = $this->psgc_brgy->DbValue;

			$Custom_Pensioner_ID = $rsnew['psgc_brgy'] . '-' . CurrentUserID() . '-' . $rsnew['SeniorID'];

			$conn->Execute("UPDATE tbl_pensioner SET PensionerID = '".$Custom_Pensioner_ID."' WHERE SeniorID='".$rsnew['SeniorID']."'");
		}


		// Add detail records
		if ($AddRow) {
			$DetailTblVar = explode(",", $this->getCurrentDetailTable());
			if (in_array("tbl_representative", $DetailTblVar) && $GLOBALS["tbl_representative"]->DetailAdd) {
				$GLOBALS["tbl_representative"]->PensionerID->setSessionValue($this->PensionerID->CurrentValue); // Set master key
				if (!isset($GLOBALS["tbl_representative_grid"])) $GLOBALS["tbl_representative_grid"] = new ctbl_representative_grid(); // Get detail page object
				$AddRow = $GLOBALS["tbl_representative_grid"]->GridInsert();
				if (!$AddRow)
					$GLOBALS["tbl_representative"]->PensionerID->setSessionValue(""); // Clear master key if insert failed
			}
			if (in_array("tbl_support", $DetailTblVar) && $GLOBALS["tbl_support"]->DetailAdd) {
				$GLOBALS["tbl_support"]->PensionerID->setSessionValue($this->PensionerID->CurrentValue); // Set master key
				if (!isset($GLOBALS["tbl_support_grid"])) $GLOBALS["tbl_support_grid"] = new ctbl_support_grid(); // Get detail page object
				$AddRow = $GLOBALS["tbl_support_grid"]->GridInsert();
				if (!$AddRow)
					$GLOBALS["tbl_support"]->PensionerID->setSessionValue(""); // Clear master key if insert failed
			}
			if (in_array("tbl_updates", $DetailTblVar) && $GLOBALS["tbl_updates"]->DetailAdd) {
				$GLOBALS["tbl_updates"]->PensionerID->setSessionValue($this->PensionerID->CurrentValue); // Set master key
				if (!isset($GLOBALS["tbl_updates_grid"])) $GLOBALS["tbl_updates_grid"] = new ctbl_updates_grid(); // Get detail page object
				$AddRow = $GLOBALS["tbl_updates_grid"]->GridInsert();
				if (!$AddRow)
					$GLOBALS["tbl_updates"]->PensionerID->setSessionValue(""); // Clear master key if insert failed
			}
		}

		// Commit/Rollback transaction
		if ($this->getCurrentDetailTable() <> "") {
			if ($AddRow) {
				$conn->CommitTrans(); // Commit transaction
			} else {
				$conn->RollbackTrans(); // Rollback transaction
			}
		}
		if ($AddRow) {

			// Call Row Inserted event
			$rs = ($rsold == NULL) ? NULL : $rsold->fields;
			$this->Row_Inserted($rs, $rsnew);
			$this->WriteAuditTrailOnAdd($rsnew);
		}

		// picture
		ew_CleanUploadTempPath($this->picture, $this->picture->Upload->Index);
		return $AddRow;
	}

	// Set up detail parms based on QueryString
	function SetUpDetailParms() {

		// Get the keys for master table
		if (isset($_GET[EW_TABLE_SHOW_DETAIL])) {
			$sDetailTblVar = $_GET[EW_TABLE_SHOW_DETAIL];
			$this->setCurrentDetailTable($sDetailTblVar);
		} else {
			$sDetailTblVar = $this->getCurrentDetailTable();
		}
		if ($sDetailTblVar <> "") {
			$DetailTblVar = explode(",", $sDetailTblVar);
			if (in_array("tbl_representative", $DetailTblVar)) {
				if (!isset($GLOBALS["tbl_representative_grid"]))
					$GLOBALS["tbl_representative_grid"] = new ctbl_representative_grid;
				if ($GLOBALS["tbl_representative_grid"]->DetailAdd) {
					if ($this->CopyRecord)
						$GLOBALS["tbl_representative_grid"]->CurrentMode = "copy";
					else
						$GLOBALS["tbl_representative_grid"]->CurrentMode = "add";
					$GLOBALS["tbl_representative_grid"]->CurrentAction = "gridadd";

					// Save current master table to detail table
					$GLOBALS["tbl_representative_grid"]->setCurrentMasterTable($this->TableVar);
					$GLOBALS["tbl_representative_grid"]->setStartRecordNumber(1);
					$GLOBALS["tbl_representative_grid"]->PensionerID->FldIsDetailKey = TRUE;
					$GLOBALS["tbl_representative_grid"]->PensionerID->CurrentValue = $this->PensionerID->CurrentValue;
					$GLOBALS["tbl_representative_grid"]->PensionerID->setSessionValue($GLOBALS["tbl_representative_grid"]->PensionerID->CurrentValue);
				}
			}
			if (in_array("tbl_support", $DetailTblVar)) {
				if (!isset($GLOBALS["tbl_support_grid"]))
					$GLOBALS["tbl_support_grid"] = new ctbl_support_grid;
				if ($GLOBALS["tbl_support_grid"]->DetailAdd) {
					if ($this->CopyRecord)
						$GLOBALS["tbl_support_grid"]->CurrentMode = "copy";
					else
						$GLOBALS["tbl_support_grid"]->CurrentMode = "add";
					$GLOBALS["tbl_support_grid"]->CurrentAction = "gridadd";

					// Save current master table to detail table
					$GLOBALS["tbl_support_grid"]->setCurrentMasterTable($this->TableVar);
					$GLOBALS["tbl_support_grid"]->setStartRecordNumber(1);
					$GLOBALS["tbl_support_grid"]->PensionerID->FldIsDetailKey = TRUE;
					$GLOBALS["tbl_support_grid"]->PensionerID->CurrentValue = $this->PensionerID->CurrentValue;
					$GLOBALS["tbl_support_grid"]->PensionerID->setSessionValue($GLOBALS["tbl_support_grid"]->PensionerID->CurrentValue);
				}
			}
			if (in_array("tbl_updates", $DetailTblVar)) {
				if (!isset($GLOBALS["tbl_updates_grid"]))
					$GLOBALS["tbl_updates_grid"] = new ctbl_updates_grid;
				if ($GLOBALS["tbl_updates_grid"]->DetailAdd) {
					if ($this->CopyRecord)
						$GLOBALS["tbl_updates_grid"]->CurrentMode = "copy";
					else
						$GLOBALS["tbl_updates_grid"]->CurrentMode = "add";
					$GLOBALS["tbl_updates_grid"]->CurrentAction = "gridadd";

					// Save current master table to detail table
					$GLOBALS["tbl_updates_grid"]->setCurrentMasterTable($this->TableVar);
					$GLOBALS["tbl_updates_grid"]->setStartRecordNumber(1);
					$GLOBALS["tbl_updates_grid"]->PensionerID->FldIsDetailKey = TRUE;
					$GLOBALS["tbl_updates_grid"]->PensionerID->CurrentValue = $this->PensionerID->CurrentValue;
					$GLOBALS["tbl_updates_grid"]->PensionerID->setSessionValue($GLOBALS["tbl_updates_grid"]->PensionerID->CurrentValue);
				}
			}
		}
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$PageCaption = $this->TableCaption();
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "tbl_pensionerlist.php", $this->TableVar);
		$PageCaption = ($this->CurrentAction == "C") ? $Language->Phrase("Copy") : $Language->Phrase("Add");
		$Breadcrumb->Add("add", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", ew_CurrentUrl(), $this->TableVar);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'tbl_pensioner';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (add page)
	function WriteAuditTrailOnAdd(&$rs) {
		if (!$this->AuditTrailOnAdd) return;
		$table = 'tbl_pensioner';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['SeniorID'];

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
if (!isset($tbl_pensioner_add)) $tbl_pensioner_add = new ctbl_pensioner_add();

// Page init
$tbl_pensioner_add->Page_Init();

// Page main
$tbl_pensioner_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$tbl_pensioner_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var tbl_pensioner_add = new ew_Page("tbl_pensioner_add");
tbl_pensioner_add.PageID = "add"; // Page ID
var EW_PAGE_ID = tbl_pensioner_add.PageID; // For backward compatibility

// Form object
var ftbl_pensioneradd = new ew_Form("ftbl_pensioneradd");

// Validate form
ftbl_pensioneradd.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_InclusionDate");
			if (elm && !ew_CheckUSDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($tbl_pensioner->InclusionDate->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_DateIssued");
			if (elm && !ew_CheckUSDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($tbl_pensioner->DateIssued->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_firstname");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($tbl_pensioner->firstname->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_lastname");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($tbl_pensioner->lastname->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_Birthdate");
			if (elm && !ew_CheckUSDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($tbl_pensioner->Birthdate->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_sex");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($tbl_pensioner->sex->FldCaption()) ?>");
			/*elm = this.GetElements("x" + infix + "_affliationID");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($tbl_pensioner->affliationID->FldCaption()) ?>");*/
			elm = this.GetElements("x" + infix + "_psgc_region");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($tbl_pensioner->psgc_region->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_psgc_province");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($tbl_pensioner->psgc_province->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_psgc_municipality");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($tbl_pensioner->psgc_municipality->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_psgc_brgy");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($tbl_pensioner->psgc_brgy->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_Status");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($tbl_pensioner->Status->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_is_4ps");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($tbl_pensioner->is_4ps->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_abandoned");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($tbl_pensioner->abandoned->FldCaption()) ?>");

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
ftbl_pensioneradd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftbl_pensioneradd.ValidateRequired = true;
<?php } else { ?>
ftbl_pensioneradd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
ftbl_pensioneradd.Lists["x_MaritalID"] = {"LinkField":"x_MaritalID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensioneradd.Lists["x_affliationID"] = {"LinkField":"x_affliationID","Ajax":true,"AutoFill":false,"DisplayFields":["x_aff_description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensioneradd.Lists["x_psgc_region"] = {"LinkField":"x_region_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_region_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensioneradd.Lists["x_psgc_province"] = {"LinkField":"x_prov_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_prov_name","","",""],"ParentFields":["x_psgc_region"],"FilterFields":["x_region_code"],"Options":[]};
ftbl_pensioneradd.Lists["x_psgc_municipality"] = {"LinkField":"x_city_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_city_name","","",""],"ParentFields":["x_psgc_province"],"FilterFields":["x_prov_code"],"Options":[]};
ftbl_pensioneradd.Lists["x_psgc_brgy"] = {"LinkField":"x_brgy_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_brgy_name","","",""],"ParentFields":["x_psgc_municipality"],"FilterFields":["x_city_code"],"Options":[]};
ftbl_pensioneradd.Lists["x_Status"] = {"LinkField":"x_statusID","Ajax":true,"AutoFill":false,"DisplayFields":["x_status","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensioneradd.Lists["x_paymentmodeID"] = {"LinkField":"x_paymentmodeID","Ajax":true,"AutoFill":false,"DisplayFields":["x_description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensioneradd.Lists["x_ArrangementID"] = {"LinkField":"x_ArrangementID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php //$Breadcrumb->Render(); ?>
<?php $tbl_pensioner_add->ShowPageHeader(); ?>
<?php
$tbl_pensioner_add->ShowMessage();
?>
<div id="user-profile-1" class="user-profile row">
<form name="ftbl_pensioneradd" id="ftbl_pensioneradd" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="tbl_pensioner">
<input type="hidden" name="a_add" id="a_add" value="A">
<div class="col-xs-12 col-sm-3 center">
	<div>
		<span class="profile-picture">
				<span id="el_tbl_pensioner_picture" class="control-group">
					<?php if ($tbl_pensioner->picture->Visible) { // picture ?>
						<span id="el_tbl_pensioner_picture" class="control-group">
						<span id="fd_x_picture">
						<span class="btn btn-small fileinput-button">
							<span><?php echo $Language->Phrase("ChooseFile") ?></span>
							<input type="file" data-field="x_picture" name="x_picture" id="x_picture">
						</span>
						<input type="hidden" name="fn_x_picture" id= "fn_x_picture" value="<?php echo $tbl_pensioner->picture->Upload->FileName ?>">
						<input type="hidden" name="fa_x_picture" id= "fa_x_picture" value="0">
						<input type="hidden" name="fs_x_picture" id= "fs_x_picture" value="0">
							<div id="ft_x_picture"></div>
						</span>
							<?php echo $tbl_pensioner->picture->CustomMsg ?>
						</span>
					<?php } ?>

				</span>
		</span>

		<div class="space-4"></div>

		<div class="width-80 label label-info label-xlg arrowed-in arrowed-in-right">
			<div class="inline position-relative">
			</div>
		</div>
	</div>

	<div class="space-6"></div>

	<div class="hr hr12 dotted"></div>

	<div class="clearfix">
		<div class="grid2">
			<span class="bigger-175 blue">Php0.00</span>

			<br>
			Total Benefits Received
		</div>

		<div class="grid2">
			<span class="bigger-175 blue">Php0.00</span>

			<br>
			Benefits to be Received
		</div>
	</div>

	<div class="hr hr16 dotted"></div>
</div>
<div class="col-xs-12 col-sm-9">
	<div class="table-header">Profile</div>
	<div class="profile-user-info profile-user-info-striped">
		<?php if ($tbl_pensioner->firstname->Visible) { // firstname ?>
			<div class="profile-info-row">
				<div class="profile-info-name"> <?php echo $tbl_pensioner->firstname->FldCaption() ?> </div>
				<div class="profile-info-value">
					<input type="text" data-field="x_firstname" name="x_firstname" id="x_firstname" size="30" maxlength="40" placeholder="<?php echo $tbl_pensioner->firstname->PlaceHolder ?>" value="<?php echo $tbl_pensioner->firstname->EditValue ?>"<?php echo $tbl_pensioner->firstname->EditAttributes() ?>>
					<?php echo $tbl_pensioner->firstname->CustomMsg ?>
				</div>
			</div>
		<?php } ?>
		<?php if ($tbl_pensioner->middlename->Visible) { // middlename ?>
			<div class="profile-info-row">
				<div class="profile-info-name"> <?php echo $tbl_pensioner->middlename->FldCaption() ?> </div>
				<div class="profile-info-value">
					<input type="text" data-field="x_middlename" name="x_middlename" id="x_middlename" size="30" maxlength="40" placeholder="<?php echo $tbl_pensioner->middlename->PlaceHolder ?>" value="<?php echo $tbl_pensioner->middlename->EditValue ?>"<?php echo $tbl_pensioner->middlename->EditAttributes() ?>>
					<?php echo $tbl_pensioner->middlename->CustomMsg ?>
				</div>
			</div>
		<?php } ?>
		<?php if ($tbl_pensioner->lastname->Visible) { // lastname ?>
			<div class="profile-info-row">
				<div class="profile-info-name"> <?php echo $tbl_pensioner->lastname->FldCaption() ?> </div>
				<div class="profile-info-value">
					<input type="text" data-field="x_lastname" name="x_lastname" id="x_lastname" size="30" maxlength="40" placeholder="<?php echo $tbl_pensioner->lastname->PlaceHolder ?>" value="<?php echo $tbl_pensioner->lastname->EditValue ?>"<?php echo $tbl_pensioner->lastname->EditAttributes() ?>>
					<?php echo $tbl_pensioner->lastname->CustomMsg ?>
				</div>
			</div>
		<?php } ?>
		<?php if ($tbl_pensioner->extname->Visible) { // extname ?>
			<div class="profile-info-row">
				<div class="profile-info-name"> <?php echo $tbl_pensioner->extname->FldCaption() ?> </div>
				<div class="profile-info-value">
					<input type="text" data-field="x_extname" name="x_extname" id="x_extname" size="30" maxlength="20" placeholder="<?php echo $tbl_pensioner->extname->PlaceHolder ?>" value="<?php echo $tbl_pensioner->extname->EditValue ?>"<?php echo $tbl_pensioner->extname->EditAttributes() ?>>
					<?php echo $tbl_pensioner->extname->CustomMsg ?>
				</div>
			</div>
		<?php } ?>
		<?php if ($tbl_pensioner->InclusionDate->Visible) { // InclusionDate ?>
			<div class="profile-info-row">
				<div class="profile-info-name"> <?php echo $tbl_pensioner->InclusionDate->FldCaption() ?> </div>
				<div class="profile-info-value">
					<input type="text" data-field="x_InclusionDate" name="x_InclusionDate" id="x_InclusionDate" placeholder="<?php echo $tbl_pensioner->InclusionDate->PlaceHolder ?>" value="<?php echo $tbl_pensioner->InclusionDate->EditValue ?>"<?php echo $tbl_pensioner->InclusionDate->EditAttributes() ?>>
					<?php if (!$tbl_pensioner->InclusionDate->ReadOnly && !$tbl_pensioner->InclusionDate->Disabled && @$tbl_pensioner->InclusionDate->EditAttrs["readonly"] == "" && @$tbl_pensioner->InclusionDate->EditAttrs["disabled"] == "") { ?>
						<button id="cal_x_InclusionDate" name="cal_x_InclusionDate" class="btn" type="button"><img src="phpimages/calendar.png" id="cal_x_InclusionDate" alt="<?php echo $Language->Phrase("PickDate") ?>" title="<?php echo $Language->Phrase("PickDate") ?>" style="border: 0;"></button><script type="text/javascript">
							ew_CreateCalendar("ftbl_pensioneradd", "x_InclusionDate", "%m/%d/%Y");
						</script>
					<?php } ?>
					<?php echo $tbl_pensioner->InclusionDate->CustomMsg ?>
				</div>
			</div>
		<?php } ?>
		<?php if ($tbl_pensioner->is_4ps->Visible) { // is_4ps ?>
			<div class="profile-info-row">
				<div class="profile-info-name"> <?php echo $tbl_pensioner->is_4ps->FldCaption() ?> </div>
				<div class="profile-info-value">
					<select data-field="x_is_4ps" id="x_is_4ps" name="x_is_4ps"<?php echo $tbl_pensioner->is_4ps->EditAttributes() ?>>
						<?php
						if (is_array($tbl_pensioner->is_4ps->EditValue)) {
							$arwrk = $tbl_pensioner->is_4ps->EditValue;
							$rowswrk = count($arwrk);
							$emptywrk = TRUE;
							for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
								$selwrk = (strval($tbl_pensioner->is_4ps->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
					<?php echo $tbl_pensioner->is_4ps->CustomMsg ?>
				</div>
			</div>
		<?php } ?>
		<?php if ($tbl_pensioner->hh_id->Visible) { // hh_id ?>
			<div class="profile-info-row">
				<div class="profile-info-name"> <?php echo $tbl_pensioner->hh_id->FldCaption() ?> </div>
				<div class="profile-info-value">
					<input type="text" data-field="x_hh_id" name="x_hh_id" id="x_hh_id" size="30" maxlength="40" placeholder="<?php echo $tbl_pensioner->hh_id->PlaceHolder ?>" value="<?php echo $tbl_pensioner->hh_id->EditValue ?>"<?php echo $tbl_pensioner->hh_id->EditAttributes() ?>>
					<?php echo $tbl_pensioner->hh_id->CustomMsg ?>
				</div>
			</div>
		<?php } ?>
		<?php if ($tbl_pensioner->osca_ID->Visible) { // osca_ID ?>
			<div class="profile-info-row">
				<div class="profile-info-name"> <?php echo $tbl_pensioner->osca_ID->FldCaption() ?> </div>
				<div class="profile-info-value">
					<input type="text" data-field="x_osca_ID" name="x_osca_ID" id="x_osca_ID" size="30" maxlength="40" placeholder="<?php echo $tbl_pensioner->osca_ID->PlaceHolder ?>" value="<?php echo $tbl_pensioner->osca_ID->EditValue ?>"<?php echo $tbl_pensioner->osca_ID->EditAttributes() ?>>
					<?php echo $tbl_pensioner->osca_ID->CustomMsg ?>
				</div>
			</div>
		<?php } ?>
		<?php if ($tbl_pensioner->PlaceIssued->Visible) { // PlaceIssued ?>
			<div class="profile-info-row">
				<div class="profile-info-name"> <?php echo $tbl_pensioner->PlaceIssued->FldCaption() ?> </div>
				<div class="profile-info-value">
					<input type="text" data-field="x_PlaceIssued" name="x_PlaceIssued" id="x_PlaceIssued" size="30" maxlength="120" placeholder="<?php echo $tbl_pensioner->PlaceIssued->PlaceHolder ?>" value="<?php echo $tbl_pensioner->PlaceIssued->EditValue ?>"<?php echo $tbl_pensioner->PlaceIssued->EditAttributes() ?>>
					<?php echo $tbl_pensioner->PlaceIssued->CustomMsg ?>
				</div>
			</div>
		<?php } ?>
		<?php if ($tbl_pensioner->DateIssued->Visible) { // DateIssued ?>
			<div class="profile-info-row">
				<div class="profile-info-name"> <?php echo $tbl_pensioner->DateIssued->FldCaption() ?> </div>
				<div class="profile-info-value">
					<input type="text" data-field="x_DateIssued" name="x_DateIssued" id="x_DateIssued" placeholder="<?php echo $tbl_pensioner->DateIssued->PlaceHolder ?>" value="<?php echo $tbl_pensioner->DateIssued->EditValue ?>"<?php echo $tbl_pensioner->DateIssued->EditAttributes() ?>>
					<?php if (!$tbl_pensioner->DateIssued->ReadOnly && !$tbl_pensioner->DateIssued->Disabled && @$tbl_pensioner->DateIssued->EditAttrs["readonly"] == "" && @$tbl_pensioner->DateIssued->EditAttrs["disabled"] == "") { ?>
						<button id="cal_x_DateIssued" name="cal_x_DateIssued" class="btn" type="button"><img src="phpimages/calendar.png" id="cal_x_DateIssued" alt="<?php echo $Language->Phrase("PickDate") ?>" title="<?php echo $Language->Phrase("PickDate") ?>" style="border: 0;"></button><script type="text/javascript">
							ew_CreateCalendar("ftbl_pensioneradd", "x_DateIssued", "%m/%d/%Y");
						</script>
					<?php } ?>
					<?php echo $tbl_pensioner->DateIssued->CustomMsg ?>
				</div>
			</div>
		<?php } ?>
		<?php if ($tbl_pensioner->Birthdate->Visible) { // Birthdate ?>
			<div class="profile-info-row">
				<div class="profile-info-name"> <?php echo $tbl_pensioner->Birthdate->FldCaption() ?> </div>
				<div class="profile-info-value">
					<input type="text" data-field="x_Birthdate" name="x_Birthdate" id="x_Birthdate" placeholder="<?php echo $tbl_pensioner->Birthdate->PlaceHolder ?>" value="<?php echo $tbl_pensioner->Birthdate->EditValue ?>"<?php echo $tbl_pensioner->Birthdate->EditAttributes() ?>>
					<?php echo $tbl_pensioner->Birthdate->CustomMsg ?>
				</div>
			</div>
		<?php } ?>
		<?php if ($tbl_pensioner->sex->Visible) { // sex ?>
			<div class="profile-info-row">
				<div class="profile-info-name"> <?php echo $tbl_pensioner->sex->FldCaption() ?> </div>
				<div class="profile-info-value">
					<select data-field="x_sex" id="x_sex" name="x_sex"<?php echo $tbl_pensioner->sex->EditAttributes() ?>>
						<?php
						if (is_array($tbl_pensioner->sex->EditValue)) {
							$arwrk = $tbl_pensioner->sex->EditValue;
							$rowswrk = count($arwrk);
							$emptywrk = TRUE;
							for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
								$selwrk = (strval($tbl_pensioner->sex->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
					<?php echo $tbl_pensioner->sex->CustomMsg ?>
				</div>
			</div>
		<?php } ?>
		<?php if ($tbl_pensioner->MaritalID->Visible) { // MaritalID ?>
			<div class="profile-info-row">
				<div class="profile-info-name"> <?php echo $tbl_pensioner->MaritalID->FldCaption() ?> </div>
				<div class="profile-info-value">
					<select data-field="x_MaritalID" id="x_MaritalID" name="x_MaritalID"<?php echo $tbl_pensioner->MaritalID->EditAttributes() ?>>
						<?php
						if (is_array($tbl_pensioner->MaritalID->EditValue)) {
							$arwrk = $tbl_pensioner->MaritalID->EditValue;
							$rowswrk = count($arwrk);
							$emptywrk = TRUE;
							for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
								$selwrk = (strval($tbl_pensioner->MaritalID->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
					$sSqlWrk = "SELECT `MaritalID`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_civilstatus`";
					$sWhereWrk = "";

					// Call Lookup selecting
					$tbl_pensioner->Lookup_Selecting($tbl_pensioner->MaritalID, $sWhereWrk);
					if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
					$sSqlWrk .= " ORDER BY `MaritalID` ASC";
					?>
					<input type="hidden" name="s_x_MaritalID" id="s_x_MaritalID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`MaritalID` = {filter_value}"); ?>&t0=3">
					<?php echo $tbl_pensioner->MaritalID->CustomMsg ?>
				</div>
			</div>
		<?php } ?>
		<?php if ($tbl_pensioner->affliationID->Visible) { // affliationID ?>
			<div class="profile-info-row">
				<div class="profile-info-name"> <?php echo $tbl_pensioner->affliationID->FldCaption() ?> </div>
				<div class="profile-info-value">
					<select data-field="x_affliationID" id="x_affliationID" name="x_affliationID"<?php echo $tbl_pensioner->affliationID->EditAttributes() ?>>
						<?php
						if (is_array($tbl_pensioner->affliationID->EditValue)) {
							$arwrk = $tbl_pensioner->affliationID->EditValue;
							$rowswrk = count($arwrk);
							$emptywrk = TRUE;
							for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
								$selwrk = (strval($tbl_pensioner->affliationID->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
					$sSqlWrk = "SELECT `affliationID`, `aff_description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_affliation`";
					$sWhereWrk = "";

					// Call Lookup selecting
					$tbl_pensioner->Lookup_Selecting($tbl_pensioner->affliationID, $sWhereWrk);
					if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
					$sSqlWrk .= " ORDER BY `affliationID` ASC";
					?>
					<input type="hidden" name="s_x_affliationID" id="s_x_affliationID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`affliationID` = {filter_value}"); ?>&t0=3">
					<?php echo $tbl_pensioner->affliationID->CustomMsg ?>
				</div>
			</div>
		<?php } ?>
		<?php if ($tbl_pensioner->psgc_region->Visible) { // psgc_region ?>
			<div class="profile-info-row">
				<div class="profile-info-name"> <?php echo $tbl_pensioner->psgc_region->FldCaption() ?> </div>
				<div class="profile-info-value">
					<?php $tbl_pensioner->psgc_region->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x_psgc_province']); " . @$tbl_pensioner->psgc_region->EditAttrs["onchange"]; ?>
					<select data-field="x_psgc_region" id="x_psgc_region" name="x_psgc_region"<?php echo $tbl_pensioner->psgc_region->EditAttributes() ?>>
						<?php
						if (is_array($tbl_pensioner->psgc_region->EditValue)) {
							$arwrk = $tbl_pensioner->psgc_region->EditValue;
							$rowswrk = count($arwrk);
							$emptywrk = TRUE;
							for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
								$selwrk = (strval($tbl_pensioner->psgc_region->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
					$tbl_pensioner->Lookup_Selecting($tbl_pensioner->psgc_region, $sWhereWrk);
					if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
					$sSqlWrk .= " ORDER BY `region_code` ASC";
					?>
					<input type="hidden" name="s_x_psgc_region" id="s_x_psgc_region" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`region_code` = {filter_value}"); ?>&t0=21">
					<?php echo $tbl_pensioner->psgc_region->CustomMsg ?>
				</div>
			</div>
		<?php } ?>
		<?php if ($tbl_pensioner->psgc_province->Visible) { // psgc_province ?>
			<div class="profile-info-row">
				<div class="profile-info-name"> <?php echo $tbl_pensioner->psgc_province->FldCaption() ?> </div>
				<div class="profile-info-value">
					<?php $tbl_pensioner->psgc_province->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x_psgc_municipality']); " . @$tbl_pensioner->psgc_province->EditAttrs["onchange"]; ?>
					<select data-field="x_psgc_province" id="x_psgc_province" name="x_psgc_province"<?php echo $tbl_pensioner->psgc_province->EditAttributes() ?>>
						<?php
						if (is_array($tbl_pensioner->psgc_province->EditValue)) {
							$arwrk = $tbl_pensioner->psgc_province->EditValue;
							$rowswrk = count($arwrk);
							$emptywrk = TRUE;
							for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
								$selwrk = (strval($tbl_pensioner->psgc_province->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
					$tbl_pensioner->Lookup_Selecting($tbl_pensioner->psgc_province, $sWhereWrk);
					if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
					$sSqlWrk .= " ORDER BY `prov_name` ASC";
					?>
					<input type="hidden" name="s_x_psgc_province" id="s_x_psgc_province" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`prov_code` = {filter_value}"); ?>&t0=21&f1=<?php echo ew_Encrypt("`region_code` IN ({filter_value})"); ?>&t1=21">
					<?php echo $tbl_pensioner->psgc_province->CustomMsg ?>
				</div>
			</div>
		<?php } ?>
		<?php if ($tbl_pensioner->psgc_municipality->Visible) { // psgc_municipality ?>
			<div class="profile-info-row">
				<div class="profile-info-name"> <?php echo $tbl_pensioner->psgc_municipality->FldCaption() ?> </div>
				<div class="profile-info-value">
					<?php $tbl_pensioner->psgc_municipality->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x_psgc_brgy']); " . @$tbl_pensioner->psgc_municipality->EditAttrs["onchange"]; ?>
					<select data-field="x_psgc_municipality" id="x_psgc_municipality" name="x_psgc_municipality"<?php echo $tbl_pensioner->psgc_municipality->EditAttributes() ?>>
						<?php
						if (is_array($tbl_pensioner->psgc_municipality->EditValue)) {
							$arwrk = $tbl_pensioner->psgc_municipality->EditValue;
							$rowswrk = count($arwrk);
							$emptywrk = TRUE;
							for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
								$selwrk = (strval($tbl_pensioner->psgc_municipality->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
					$tbl_pensioner->Lookup_Selecting($tbl_pensioner->psgc_municipality, $sWhereWrk);
					if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
					$sSqlWrk .= " ORDER BY `city_name` ASC";
					?>
					<input type="hidden" name="s_x_psgc_municipality" id="s_x_psgc_municipality" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`city_code` = {filter_value}"); ?>&t0=21&f1=<?php echo ew_Encrypt("`prov_code` IN ({filter_value})"); ?>&t1=21">
					<?php echo $tbl_pensioner->psgc_municipality->CustomMsg ?>
				</div>
			</div>
		<?php } ?>
		<?php if ($tbl_pensioner->psgc_brgy->Visible) { // psgc_brgy ?>
			<div class="profile-info-row">
				<div class="profile-info-name"> <?php echo $tbl_pensioner->psgc_brgy->FldCaption() ?> </div>
				<div class="profile-info-value">
					<select data-field="x_psgc_brgy" id="x_psgc_brgy" name="x_psgc_brgy"<?php echo $tbl_pensioner->psgc_brgy->EditAttributes() ?>>
						<?php
						if (is_array($tbl_pensioner->psgc_brgy->EditValue)) {
							$arwrk = $tbl_pensioner->psgc_brgy->EditValue;
							$rowswrk = count($arwrk);
							$emptywrk = TRUE;
							for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
								$selwrk = (strval($tbl_pensioner->psgc_brgy->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
					$tbl_pensioner->Lookup_Selecting($tbl_pensioner->psgc_brgy, $sWhereWrk);
					if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
					$sSqlWrk .= " ORDER BY `brgy_name` ASC";
					?>
					<input type="hidden" name="s_x_psgc_brgy" id="s_x_psgc_brgy" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`brgy_code` = {filter_value}"); ?>&t0=21&f1=<?php echo ew_Encrypt("`city_code` IN ({filter_value})"); ?>&t1=21">
					<?php echo $tbl_pensioner->psgc_brgy->CustomMsg ?>
				</div>
			</div>
		<?php } ?>
		<?php if ($tbl_pensioner->given_add->Visible) { // given_add ?>
			<div class="profile-info-row">
				<div class="profile-info-name"> <?php echo $tbl_pensioner->given_add->FldCaption() ?> </div>
				<div class="profile-info-value">
					<input type="text" data-field="x_given_add" name="x_given_add" id="x_given_add" size="30" maxlength="255" placeholder="<?php echo $tbl_pensioner->given_add->PlaceHolder ?>" value="<?php echo $tbl_pensioner->given_add->EditValue ?>"<?php echo $tbl_pensioner->given_add->EditAttributes() ?>>
					<?php echo $tbl_pensioner->given_add->CustomMsg ?>
				</div>
			</div>
		<?php } ?>
		<?php if ($tbl_pensioner->Status->Visible) { // Status ?>
			<div class="profile-info-row">
				<div class="profile-info-name"> <?php echo $tbl_pensioner->Status->FldCaption() ?> </div>
				<div class="profile-info-value">
					<select data-field="x_Status" id="x_Status" name="x_Status"<?php echo $tbl_pensioner->Status->EditAttributes() ?>>
						<?php
						if (is_array($tbl_pensioner->Status->EditValue)) {
							$arwrk = $tbl_pensioner->Status->EditValue;
							$rowswrk = count($arwrk);
							$emptywrk = TRUE;
							for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
								$selwrk = (strval($tbl_pensioner->Status->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
					$sSqlWrk = "SELECT `statusID`, `status` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_status`";
					$sWhereWrk = "";

					// Call Lookup selecting
					$tbl_pensioner->Lookup_Selecting($tbl_pensioner->Status, $sWhereWrk);
					if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
					$sSqlWrk .= " ORDER BY `statusID` ASC";
					?>
					<input type="hidden" name="s_x_Status" id="s_x_Status" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`statusID` = {filter_value}"); ?>&t0=3">
					<?php echo $tbl_pensioner->Status->CustomMsg ?>
				</div>
			</div>
		<?php } ?>
		<?php if ($tbl_pensioner->paymentmodeID->Visible) { // paymentmodeID ?>
			<div class="profile-info-row">
				<div class="profile-info-name"> <?php echo $tbl_pensioner->paymentmodeID->FldCaption() ?> </div>
				<div class="profile-info-value">
					<select data-field="x_paymentmodeID" id="x_paymentmodeID" name="x_paymentmodeID"<?php echo $tbl_pensioner->paymentmodeID->EditAttributes() ?>>
						<?php
						if (is_array($tbl_pensioner->paymentmodeID->EditValue)) {
							$arwrk = $tbl_pensioner->paymentmodeID->EditValue;
							$rowswrk = count($arwrk);
							$emptywrk = TRUE;
							for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
								$selwrk = (strval($tbl_pensioner->paymentmodeID->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
					$tbl_pensioner->Lookup_Selecting($tbl_pensioner->paymentmodeID, $sWhereWrk);
					if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
					$sSqlWrk .= " ORDER BY `paymentmodeID` ASC";
					?>
					<input type="hidden" name="s_x_paymentmodeID" id="s_x_paymentmodeID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`paymentmodeID` = {filter_value}"); ?>&t0=3">
					<?php echo $tbl_pensioner->paymentmodeID->CustomMsg ?>
				</div>
			</div>
		<?php } ?>
		<?php if ($tbl_pensioner->ArrangementID->Visible) { // ArrangementID ?>
			<div class="profile-info-row">
				<div class="profile-info-name"> <?php echo $tbl_pensioner->ArrangementID->FldCaption() ?> </div>
				<div class="profile-info-value">
					<select data-field="x_ArrangementID" id="x_ArrangementID" name="x_ArrangementID"<?php echo $tbl_pensioner->ArrangementID->EditAttributes() ?>>
						<?php
						if (is_array($tbl_pensioner->ArrangementID->EditValue)) {
							$arwrk = $tbl_pensioner->ArrangementID->EditValue;
							$rowswrk = count($arwrk);
							$emptywrk = TRUE;
							for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
								$selwrk = (strval($tbl_pensioner->ArrangementID->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
					$sSqlWrk = "SELECT `ArrangementID`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_arrangement`";
					$sWhereWrk = "";

					// Call Lookup selecting
					$tbl_pensioner->Lookup_Selecting($tbl_pensioner->ArrangementID, $sWhereWrk);
					if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
					$sSqlWrk .= " ORDER BY `ArrangementID` ASC";
					?>
					<input type="hidden" name="s_x_ArrangementID" id="s_x_ArrangementID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`ArrangementID` = {filter_value}"); ?>&t0=3">
					<?php echo $tbl_pensioner->ArrangementID->CustomMsg ?>
				</div>
			</div>
		<?php } ?>
		<?php if ($tbl_pensioner->abandoned->Visible) { // abandoned ?>
			<div class="profile-info-row">
				<div class="profile-info-name"> <?php echo $tbl_pensioner->abandoned->FldCaption() ?> </div>
				<div class="profile-info-value">
					<select data-field="x_abandoned" id="x_abandoned" name="x_abandoned"<?php echo $tbl_pensioner->abandoned->EditAttributes() ?>>
						<?php
						if (is_array($tbl_pensioner->abandoned->EditValue)) {
							$arwrk = $tbl_pensioner->abandoned->EditValue;
							$rowswrk = count($arwrk);
							$emptywrk = TRUE;
							for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
								$selwrk = (strval($tbl_pensioner->abandoned->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
					<?php echo $tbl_pensioner->abandoned->CustomMsg ?>
				</div>
			</div>
		<?php } ?>
		</div> <!-- ./profile-user-info profile-user-info-striped -->
</div> <!-- ./col-xs-12 col-sm-9 -->
<?php
	if (in_array("tbl_representative", explode(",", $tbl_pensioner->getCurrentDetailTable())) && $tbl_representative->DetailAdd) {
?>
<?php include_once "tbl_representativegrid.php" ?>
<?php } ?>
<?php
	if (in_array("tbl_support", explode(",", $tbl_pensioner->getCurrentDetailTable())) && $tbl_support->DetailAdd) {
?>
<?php include_once "tbl_supportgrid.php" ?>
<?php } ?>
<?php
	if (in_array("tbl_updates", explode(",", $tbl_pensioner->getCurrentDetailTable())) && $tbl_updates->DetailAdd) {
?>
<?php include_once "tbl_updatesgrid.php" ?>
<?php } ?>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
</form>
</div> <!-- ./user-profile row -->
<script type="text/javascript">
ftbl_pensioneradd.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$tbl_pensioner_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$tbl_pensioner_add->Page_Terminate();
?>
