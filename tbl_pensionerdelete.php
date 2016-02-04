<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "tbl_pensionerinfo.php" ?>
<?php include_once "tbl_userinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$tbl_pensioner_delete = NULL; // Initialize page object first

class ctbl_pensioner_delete extends ctbl_pensioner {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Table name
	var $TableName = 'tbl_pensioner';

	// Page object name
	var $PageObjName = 'tbl_pensioner_delete';

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
	var $AuditTrailOnDelete = TRUE;

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
			define("EW_PAGE_ID", 'delete', TRUE);

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
		if (!$Security->CanDelete()) {
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
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up curent action
		$this->SeniorID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
	var $TotalRecs = 0;
	var $RecCnt;
	var $RecKeys = array();
	var $Recordset;
	var $StartRowCnt = 1;
	var $RowCnt = 0;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Load key parameters
		$this->RecKeys = $this->GetRecordKeys(); // Load record keys
		$sFilter = $this->GetKeyFilter();
		if ($sFilter == "")
			$this->Page_Terminate("tbl_pensionerlist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in tbl_pensioner class, tbl_pensionerinfo.php

		$this->CurrentFilter = $sFilter;

		// Get action
		if (@$_POST["a_delete"] <> "") {
			$this->CurrentAction = $_POST["a_delete"];
		} else {
			$this->CurrentAction = "D"; // Delete record directly
		}
		switch ($this->CurrentAction) {
			case "D": // Delete
				$this->SendEmail = TRUE; // Send email on delete success
				if ($this->DeleteRows()) { // Delete rows
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("DeleteSuccess")); // Set up success message
					$this->Page_Terminate($this->getReturnUrl()); // Return to caller
				}
		}
	}

// No functions
	// Load recordset
	function LoadRecordset($offset = -1, $rowcnt = -1) {
		global $conn;

		// Call Recordset Selecting event
		$this->Recordset_Selecting($this->CurrentFilter);

		// Load List page SQL
		$sSql = $this->SelectSQL();
		if ($offset > -1 && $rowcnt > -1)
			$sSql .= " LIMIT $rowcnt OFFSET $offset";

		// Load recordset
		$rs = ew_LoadRecordset($sSql);

		// Call Recordset Selected event
		$this->Recordset_Selected($rs);
		return $rs;
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

			// SeniorID
			$this->SeniorID->LinkCustomAttributes = "";
			$this->SeniorID->HrefValue = "";
			$this->SeniorID->TooltipValue = "";

			// PensionerID
			$this->PensionerID->LinkCustomAttributes = "";
			$this->PensionerID->HrefValue = "";
			$this->PensionerID->TooltipValue = "";

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

			// approved
			$this->approved->LinkCustomAttributes = "";
			$this->approved->HrefValue = "";
			$this->approved->TooltipValue = "";

			// approvedby
			$this->approvedby->LinkCustomAttributes = "";
			$this->approvedby->HrefValue = "";
			$this->approvedby->TooltipValue = "";

			// DateApproved
			$this->DateApproved->LinkCustomAttributes = "";
			$this->DateApproved->HrefValue = "";
			$this->DateApproved->TooltipValue = "";

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

			// UpdatedBy
			$this->UpdatedBy->LinkCustomAttributes = "";
			$this->UpdatedBy->HrefValue = "";
			$this->UpdatedBy->TooltipValue = "";

			// UpdatedDate
			$this->UpdatedDate->LinkCustomAttributes = "";
			$this->UpdatedDate->HrefValue = "";
			$this->UpdatedDate->TooltipValue = "";

			// UpdateRemarks
			$this->UpdateRemarks->LinkCustomAttributes = "";
			$this->UpdateRemarks->HrefValue = "";
			$this->UpdateRemarks->TooltipValue = "";

			// codeGen
			$this->codeGen->LinkCustomAttributes = "";
			$this->codeGen->HrefValue = "";
			$this->codeGen->TooltipValue = "";
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	//
	// Delete records based on current filter
	//
	function DeleteRows() {
		global $conn, $Language, $Security;
		if (!$Security->CanDelete()) {
			$this->setFailureMessage($Language->Phrase("NoDeletePermission")); // No delete permission
			return FALSE;
		}
		$DeleteRows = TRUE;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = 'ew_ErrorFn';
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE) {
			return FALSE;
		} elseif ($rs->EOF) {
			$this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
			$rs->Close();
			return FALSE;

		//} else {
		//	$this->LoadRowValues($rs); // Load row values

		}
		$conn->BeginTrans();
		if ($this->AuditTrailOnDelete) $this->WriteAuditTrailDummy($Language->Phrase("BatchDeleteBegin")); // Batch delete begin

		// Clone old rows
		$rsold = ($rs) ? $rs->GetRows() : array();
		if ($rs)
			$rs->Close();

		// Call row deleting event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$DeleteRows = $this->Row_Deleting($row);
				if (!$DeleteRows) break;
			}
		}
		if ($DeleteRows) {
			$sKey = "";
			foreach ($rsold as $row) {
				$sThisKey = "";
				if ($sThisKey <> "") $sThisKey .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
				$sThisKey .= $row['SeniorID'];
				$conn->raiseErrorFn = 'ew_ErrorFn';
				$DeleteRows = $this->Delete($row); // Delete
				$conn->raiseErrorFn = '';
				if ($DeleteRows === FALSE)
					break;
				if ($sKey <> "") $sKey .= ", ";
				$sKey .= $sThisKey;
			}
		} else {

			// Set up error message
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("DeleteCancelled"));
			}
		}
		if ($DeleteRows) {
			$conn->CommitTrans(); // Commit the changes
			if ($DeleteRows) {
				foreach ($rsold as $row)
					$this->WriteAuditTrailOnDelete($row);
			}
			if ($this->AuditTrailOnDelete) $this->WriteAuditTrailDummy($Language->Phrase("BatchDeleteSuccess")); // Batch delete success
		} else {
			$conn->RollbackTrans(); // Rollback changes
			if ($this->AuditTrailOnDelete) $this->WriteAuditTrailDummy($Language->Phrase("BatchDeleteRollback")); // Batch delete rollback
		}

		// Call Row Deleted event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$this->Row_Deleted($row);
			}
		}
		return $DeleteRows;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$PageCaption = $this->TableCaption();
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "tbl_pensionerlist.php", $this->TableVar);
		$PageCaption = $Language->Phrase("delete");
		$Breadcrumb->Add("delete", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", ew_CurrentUrl(), $this->TableVar);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'tbl_pensioner';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (delete page)
	function WriteAuditTrailOnDelete(&$rs) {
		if (!$this->AuditTrailOnDelete) return;
		$table = 'tbl_pensioner';

		// Get key value
		$key = "";
		if ($key <> "")
			$key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['SeniorID'];

		// Write Audit Trail
		$dt = ew_StdCurrentDateTime();
		$id = ew_ScriptName();
	  $curUser = CurrentUserID();
		foreach (array_keys($rs) as $fldname) {
			if (array_key_exists($fldname, $this->fields) && $this->fields[$fldname]->FldDataType <> EW_DATATYPE_BLOB) { // Ignore BLOB fields
				if ($this->fields[$fldname]->FldDataType == EW_DATATYPE_MEMO) {
					if (EW_AUDIT_TRAIL_TO_DATABASE)
						$oldvalue = $rs[$fldname];
					else
						$oldvalue = "[MEMO]"; // Memo field
				} elseif ($this->fields[$fldname]->FldDataType == EW_DATATYPE_XML) {
					$oldvalue = "[XML]"; // XML field
				} else {
					$oldvalue = $rs[$fldname];
				}
				ew_WriteAuditTrail("log", $dt, $id, $curUser, "D", $table, $fldname, $key, $oldvalue, "");
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
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($tbl_pensioner_delete)) $tbl_pensioner_delete = new ctbl_pensioner_delete();

// Page init
$tbl_pensioner_delete->Page_Init();

// Page main
$tbl_pensioner_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$tbl_pensioner_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var tbl_pensioner_delete = new ew_Page("tbl_pensioner_delete");
tbl_pensioner_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = tbl_pensioner_delete.PageID; // For backward compatibility

// Form object
var ftbl_pensionerdelete = new ew_Form("ftbl_pensionerdelete");

// Form_CustomValidate event
ftbl_pensionerdelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftbl_pensionerdelete.ValidateRequired = true;
<?php } else { ?>
ftbl_pensionerdelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
ftbl_pensionerdelete.Lists["x_MaritalID"] = {"LinkField":"x_MaritalID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensionerdelete.Lists["x_affliationID"] = {"LinkField":"x_affliationID","Ajax":true,"AutoFill":false,"DisplayFields":["x_aff_description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensionerdelete.Lists["x_psgc_region"] = {"LinkField":"x_region_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_region_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensionerdelete.Lists["x_psgc_province"] = {"LinkField":"x_prov_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_prov_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensionerdelete.Lists["x_psgc_municipality"] = {"LinkField":"x_city_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_city_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensionerdelete.Lists["x_psgc_brgy"] = {"LinkField":"x_brgy_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_brgy_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensionerdelete.Lists["x_Status"] = {"LinkField":"x_statusID","Ajax":true,"AutoFill":false,"DisplayFields":["x_status","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensionerdelete.Lists["x_paymentmodeID"] = {"LinkField":"x_paymentmodeID","Ajax":true,"AutoFill":false,"DisplayFields":["x_description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pensionerdelete.Lists["x_ArrangementID"] = {"LinkField":"x_ArrangementID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($tbl_pensioner_delete->Recordset = $tbl_pensioner_delete->LoadRecordset())
	$tbl_pensioner_deleteTotalRecs = $tbl_pensioner_delete->Recordset->RecordCount(); // Get record count
if ($tbl_pensioner_deleteTotalRecs <= 0) { // No record found, exit
	if ($tbl_pensioner_delete->Recordset)
		$tbl_pensioner_delete->Recordset->Close();
	$tbl_pensioner_delete->Page_Terminate("tbl_pensionerlist.php"); // Return to list
}
?>
<?php //$Breadcrumb->Render(); ?>
<?php $tbl_pensioner_delete->ShowPageHeader(); ?>
<?php
$tbl_pensioner_delete->ShowMessage();
?>
<form name="ftbl_pensionerdelete" id="ftbl_pensionerdelete" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="tbl_pensioner">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($tbl_pensioner_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_tbl_pensionerdelete" class="ewTable ewTableSeparate">
<?php echo $tbl_pensioner->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($tbl_pensioner->SeniorID->Visible) { // SeniorID ?>
		<td><span id="elh_tbl_pensioner_SeniorID" class="tbl_pensioner_SeniorID"><?php echo $tbl_pensioner->SeniorID->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->PensionerID->Visible) { // PensionerID ?>
		<td><span id="elh_tbl_pensioner_PensionerID" class="tbl_pensioner_PensionerID"><?php echo $tbl_pensioner->PensionerID->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->InclusionDate->Visible) { // InclusionDate ?>
		<td><span id="elh_tbl_pensioner_InclusionDate" class="tbl_pensioner_InclusionDate"><?php echo $tbl_pensioner->InclusionDate->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->hh_id->Visible) { // hh_id ?>
		<td><span id="elh_tbl_pensioner_hh_id" class="tbl_pensioner_hh_id"><?php echo $tbl_pensioner->hh_id->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->osca_ID->Visible) { // osca_ID ?>
		<td><span id="elh_tbl_pensioner_osca_ID" class="tbl_pensioner_osca_ID"><?php echo $tbl_pensioner->osca_ID->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->PlaceIssued->Visible) { // PlaceIssued ?>
		<td><span id="elh_tbl_pensioner_PlaceIssued" class="tbl_pensioner_PlaceIssued"><?php echo $tbl_pensioner->PlaceIssued->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->DateIssued->Visible) { // DateIssued ?>
		<td><span id="elh_tbl_pensioner_DateIssued" class="tbl_pensioner_DateIssued"><?php echo $tbl_pensioner->DateIssued->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->firstname->Visible) { // firstname ?>
		<td><span id="elh_tbl_pensioner_firstname" class="tbl_pensioner_firstname"><?php echo $tbl_pensioner->firstname->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->middlename->Visible) { // middlename ?>
		<td><span id="elh_tbl_pensioner_middlename" class="tbl_pensioner_middlename"><?php echo $tbl_pensioner->middlename->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->lastname->Visible) { // lastname ?>
		<td><span id="elh_tbl_pensioner_lastname" class="tbl_pensioner_lastname"><?php echo $tbl_pensioner->lastname->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->extname->Visible) { // extname ?>
		<td><span id="elh_tbl_pensioner_extname" class="tbl_pensioner_extname"><?php echo $tbl_pensioner->extname->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->Birthdate->Visible) { // Birthdate ?>
		<td><span id="elh_tbl_pensioner_Birthdate" class="tbl_pensioner_Birthdate"><?php echo $tbl_pensioner->Birthdate->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->sex->Visible) { // sex ?>
		<td><span id="elh_tbl_pensioner_sex" class="tbl_pensioner_sex"><?php echo $tbl_pensioner->sex->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->MaritalID->Visible) { // MaritalID ?>
		<td><span id="elh_tbl_pensioner_MaritalID" class="tbl_pensioner_MaritalID"><?php echo $tbl_pensioner->MaritalID->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->affliationID->Visible) { // affliationID ?>
		<td><span id="elh_tbl_pensioner_affliationID" class="tbl_pensioner_affliationID"><?php echo $tbl_pensioner->affliationID->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->psgc_region->Visible) { // psgc_region ?>
		<td><span id="elh_tbl_pensioner_psgc_region" class="tbl_pensioner_psgc_region"><?php echo $tbl_pensioner->psgc_region->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->psgc_province->Visible) { // psgc_province ?>
		<td><span id="elh_tbl_pensioner_psgc_province" class="tbl_pensioner_psgc_province"><?php echo $tbl_pensioner->psgc_province->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->psgc_municipality->Visible) { // psgc_municipality ?>
		<td><span id="elh_tbl_pensioner_psgc_municipality" class="tbl_pensioner_psgc_municipality"><?php echo $tbl_pensioner->psgc_municipality->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->psgc_brgy->Visible) { // psgc_brgy ?>
		<td><span id="elh_tbl_pensioner_psgc_brgy" class="tbl_pensioner_psgc_brgy"><?php echo $tbl_pensioner->psgc_brgy->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->given_add->Visible) { // given_add ?>
		<td><span id="elh_tbl_pensioner_given_add" class="tbl_pensioner_given_add"><?php echo $tbl_pensioner->given_add->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->Status->Visible) { // Status ?>
		<td><span id="elh_tbl_pensioner_Status" class="tbl_pensioner_Status"><?php echo $tbl_pensioner->Status->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->paymentmodeID->Visible) { // paymentmodeID ?>
		<td><span id="elh_tbl_pensioner_paymentmodeID" class="tbl_pensioner_paymentmodeID"><?php echo $tbl_pensioner->paymentmodeID->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->approved->Visible) { // approved ?>
		<td><span id="elh_tbl_pensioner_approved" class="tbl_pensioner_approved"><?php echo $tbl_pensioner->approved->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->approvedby->Visible) { // approvedby ?>
		<td><span id="elh_tbl_pensioner_approvedby" class="tbl_pensioner_approvedby"><?php echo $tbl_pensioner->approvedby->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->DateApproved->Visible) { // DateApproved ?>
		<td><span id="elh_tbl_pensioner_DateApproved" class="tbl_pensioner_DateApproved"><?php echo $tbl_pensioner->DateApproved->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->ArrangementID->Visible) { // ArrangementID ?>
		<td><span id="elh_tbl_pensioner_ArrangementID" class="tbl_pensioner_ArrangementID"><?php echo $tbl_pensioner->ArrangementID->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->is_4ps->Visible) { // is_4ps ?>
		<td><span id="elh_tbl_pensioner_is_4ps" class="tbl_pensioner_is_4ps"><?php echo $tbl_pensioner->is_4ps->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->abandoned->Visible) { // abandoned ?>
		<td><span id="elh_tbl_pensioner_abandoned" class="tbl_pensioner_abandoned"><?php echo $tbl_pensioner->abandoned->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->Createdby->Visible) { // Createdby ?>
		<td><span id="elh_tbl_pensioner_Createdby" class="tbl_pensioner_Createdby"><?php echo $tbl_pensioner->Createdby->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->CreatedDate->Visible) { // CreatedDate ?>
		<td><span id="elh_tbl_pensioner_CreatedDate" class="tbl_pensioner_CreatedDate"><?php echo $tbl_pensioner->CreatedDate->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->UpdatedBy->Visible) { // UpdatedBy ?>
		<td><span id="elh_tbl_pensioner_UpdatedBy" class="tbl_pensioner_UpdatedBy"><?php echo $tbl_pensioner->UpdatedBy->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->UpdatedDate->Visible) { // UpdatedDate ?>
		<td><span id="elh_tbl_pensioner_UpdatedDate" class="tbl_pensioner_UpdatedDate"><?php echo $tbl_pensioner->UpdatedDate->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->UpdateRemarks->Visible) { // UpdateRemarks ?>
		<td><span id="elh_tbl_pensioner_UpdateRemarks" class="tbl_pensioner_UpdateRemarks"><?php echo $tbl_pensioner->UpdateRemarks->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_pensioner->codeGen->Visible) { // codeGen ?>
		<td><span id="elh_tbl_pensioner_codeGen" class="tbl_pensioner_codeGen"><?php echo $tbl_pensioner->codeGen->FldCaption() ?></span></td>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$tbl_pensioner_delete->RecCnt = 0;
$i = 0;
while (!$tbl_pensioner_delete->Recordset->EOF) {
	$tbl_pensioner_delete->RecCnt++;
	$tbl_pensioner_delete->RowCnt++;

	// Set row properties
	$tbl_pensioner->ResetAttrs();
	$tbl_pensioner->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$tbl_pensioner_delete->LoadRowValues($tbl_pensioner_delete->Recordset);

	// Render row
	$tbl_pensioner_delete->RenderRow();
?>
	<tr<?php echo $tbl_pensioner->RowAttributes() ?>>
<?php if ($tbl_pensioner->SeniorID->Visible) { // SeniorID ?>
		<td<?php echo $tbl_pensioner->SeniorID->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_SeniorID" class="control-group tbl_pensioner_SeniorID">
<span<?php echo $tbl_pensioner->SeniorID->ViewAttributes() ?>>
<?php echo $tbl_pensioner->SeniorID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->PensionerID->Visible) { // PensionerID ?>
		<td<?php echo $tbl_pensioner->PensionerID->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_PensionerID" class="control-group tbl_pensioner_PensionerID">
<span<?php echo $tbl_pensioner->PensionerID->ViewAttributes() ?>>
<?php echo $tbl_pensioner->PensionerID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->InclusionDate->Visible) { // InclusionDate ?>
		<td<?php echo $tbl_pensioner->InclusionDate->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_InclusionDate" class="control-group tbl_pensioner_InclusionDate">
<span<?php echo $tbl_pensioner->InclusionDate->ViewAttributes() ?>>
<?php echo $tbl_pensioner->InclusionDate->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->hh_id->Visible) { // hh_id ?>
		<td<?php echo $tbl_pensioner->hh_id->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_hh_id" class="control-group tbl_pensioner_hh_id">
<span<?php echo $tbl_pensioner->hh_id->ViewAttributes() ?>>
<?php echo $tbl_pensioner->hh_id->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->osca_ID->Visible) { // osca_ID ?>
		<td<?php echo $tbl_pensioner->osca_ID->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_osca_ID" class="control-group tbl_pensioner_osca_ID">
<span<?php echo $tbl_pensioner->osca_ID->ViewAttributes() ?>>
<?php echo $tbl_pensioner->osca_ID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->PlaceIssued->Visible) { // PlaceIssued ?>
		<td<?php echo $tbl_pensioner->PlaceIssued->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_PlaceIssued" class="control-group tbl_pensioner_PlaceIssued">
<span<?php echo $tbl_pensioner->PlaceIssued->ViewAttributes() ?>>
<?php echo $tbl_pensioner->PlaceIssued->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->DateIssued->Visible) { // DateIssued ?>
		<td<?php echo $tbl_pensioner->DateIssued->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_DateIssued" class="control-group tbl_pensioner_DateIssued">
<span<?php echo $tbl_pensioner->DateIssued->ViewAttributes() ?>>
<?php echo $tbl_pensioner->DateIssued->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->firstname->Visible) { // firstname ?>
		<td<?php echo $tbl_pensioner->firstname->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_firstname" class="control-group tbl_pensioner_firstname">
<span<?php echo $tbl_pensioner->firstname->ViewAttributes() ?>>
<?php echo $tbl_pensioner->firstname->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->middlename->Visible) { // middlename ?>
		<td<?php echo $tbl_pensioner->middlename->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_middlename" class="control-group tbl_pensioner_middlename">
<span<?php echo $tbl_pensioner->middlename->ViewAttributes() ?>>
<?php echo $tbl_pensioner->middlename->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->lastname->Visible) { // lastname ?>
		<td<?php echo $tbl_pensioner->lastname->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_lastname" class="control-group tbl_pensioner_lastname">
<span<?php echo $tbl_pensioner->lastname->ViewAttributes() ?>>
<?php echo $tbl_pensioner->lastname->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->extname->Visible) { // extname ?>
		<td<?php echo $tbl_pensioner->extname->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_extname" class="control-group tbl_pensioner_extname">
<span<?php echo $tbl_pensioner->extname->ViewAttributes() ?>>
<?php echo $tbl_pensioner->extname->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->Birthdate->Visible) { // Birthdate ?>
		<td<?php echo $tbl_pensioner->Birthdate->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_Birthdate" class="control-group tbl_pensioner_Birthdate">
<span<?php echo $tbl_pensioner->Birthdate->ViewAttributes() ?>>
<?php echo $tbl_pensioner->Birthdate->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->sex->Visible) { // sex ?>
		<td<?php echo $tbl_pensioner->sex->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_sex" class="control-group tbl_pensioner_sex">
<span<?php echo $tbl_pensioner->sex->ViewAttributes() ?>>
<?php echo $tbl_pensioner->sex->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->MaritalID->Visible) { // MaritalID ?>
		<td<?php echo $tbl_pensioner->MaritalID->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_MaritalID" class="control-group tbl_pensioner_MaritalID">
<span<?php echo $tbl_pensioner->MaritalID->ViewAttributes() ?>>
<?php echo $tbl_pensioner->MaritalID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->affliationID->Visible) { // affliationID ?>
		<td<?php echo $tbl_pensioner->affliationID->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_affliationID" class="control-group tbl_pensioner_affliationID">
<span<?php echo $tbl_pensioner->affliationID->ViewAttributes() ?>>
<?php echo $tbl_pensioner->affliationID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->psgc_region->Visible) { // psgc_region ?>
		<td<?php echo $tbl_pensioner->psgc_region->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_psgc_region" class="control-group tbl_pensioner_psgc_region">
<span<?php echo $tbl_pensioner->psgc_region->ViewAttributes() ?>>
<?php echo $tbl_pensioner->psgc_region->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->psgc_province->Visible) { // psgc_province ?>
		<td<?php echo $tbl_pensioner->psgc_province->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_psgc_province" class="control-group tbl_pensioner_psgc_province">
<span<?php echo $tbl_pensioner->psgc_province->ViewAttributes() ?>>
<?php echo $tbl_pensioner->psgc_province->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->psgc_municipality->Visible) { // psgc_municipality ?>
		<td<?php echo $tbl_pensioner->psgc_municipality->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_psgc_municipality" class="control-group tbl_pensioner_psgc_municipality">
<span<?php echo $tbl_pensioner->psgc_municipality->ViewAttributes() ?>>
<?php echo $tbl_pensioner->psgc_municipality->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->psgc_brgy->Visible) { // psgc_brgy ?>
		<td<?php echo $tbl_pensioner->psgc_brgy->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_psgc_brgy" class="control-group tbl_pensioner_psgc_brgy">
<span<?php echo $tbl_pensioner->psgc_brgy->ViewAttributes() ?>>
<?php echo $tbl_pensioner->psgc_brgy->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->given_add->Visible) { // given_add ?>
		<td<?php echo $tbl_pensioner->given_add->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_given_add" class="control-group tbl_pensioner_given_add">
<span<?php echo $tbl_pensioner->given_add->ViewAttributes() ?>>
<?php echo $tbl_pensioner->given_add->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->Status->Visible) { // Status ?>
		<td<?php echo $tbl_pensioner->Status->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_Status" class="control-group tbl_pensioner_Status">
<span<?php echo $tbl_pensioner->Status->ViewAttributes() ?>>
<?php echo $tbl_pensioner->Status->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->paymentmodeID->Visible) { // paymentmodeID ?>
		<td<?php echo $tbl_pensioner->paymentmodeID->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_paymentmodeID" class="control-group tbl_pensioner_paymentmodeID">
<span<?php echo $tbl_pensioner->paymentmodeID->ViewAttributes() ?>>
<?php echo $tbl_pensioner->paymentmodeID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->approved->Visible) { // approved ?>
		<td<?php echo $tbl_pensioner->approved->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_approved" class="control-group tbl_pensioner_approved">
<span<?php echo $tbl_pensioner->approved->ViewAttributes() ?>>
<?php echo $tbl_pensioner->approved->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->approvedby->Visible) { // approvedby ?>
		<td<?php echo $tbl_pensioner->approvedby->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_approvedby" class="control-group tbl_pensioner_approvedby">
<span<?php echo $tbl_pensioner->approvedby->ViewAttributes() ?>>
<?php echo $tbl_pensioner->approvedby->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->DateApproved->Visible) { // DateApproved ?>
		<td<?php echo $tbl_pensioner->DateApproved->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_DateApproved" class="control-group tbl_pensioner_DateApproved">
<span<?php echo $tbl_pensioner->DateApproved->ViewAttributes() ?>>
<?php echo $tbl_pensioner->DateApproved->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->ArrangementID->Visible) { // ArrangementID ?>
		<td<?php echo $tbl_pensioner->ArrangementID->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_ArrangementID" class="control-group tbl_pensioner_ArrangementID">
<span<?php echo $tbl_pensioner->ArrangementID->ViewAttributes() ?>>
<?php echo $tbl_pensioner->ArrangementID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->is_4ps->Visible) { // is_4ps ?>
		<td<?php echo $tbl_pensioner->is_4ps->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_is_4ps" class="control-group tbl_pensioner_is_4ps">
<span<?php echo $tbl_pensioner->is_4ps->ViewAttributes() ?>>
<?php echo $tbl_pensioner->is_4ps->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->abandoned->Visible) { // abandoned ?>
		<td<?php echo $tbl_pensioner->abandoned->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_abandoned" class="control-group tbl_pensioner_abandoned">
<span<?php echo $tbl_pensioner->abandoned->ViewAttributes() ?>>
<?php echo $tbl_pensioner->abandoned->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->Createdby->Visible) { // Createdby ?>
		<td<?php echo $tbl_pensioner->Createdby->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_Createdby" class="control-group tbl_pensioner_Createdby">
<span<?php echo $tbl_pensioner->Createdby->ViewAttributes() ?>>
<?php echo $tbl_pensioner->Createdby->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->CreatedDate->Visible) { // CreatedDate ?>
		<td<?php echo $tbl_pensioner->CreatedDate->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_CreatedDate" class="control-group tbl_pensioner_CreatedDate">
<span<?php echo $tbl_pensioner->CreatedDate->ViewAttributes() ?>>
<?php echo $tbl_pensioner->CreatedDate->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->UpdatedBy->Visible) { // UpdatedBy ?>
		<td<?php echo $tbl_pensioner->UpdatedBy->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_UpdatedBy" class="control-group tbl_pensioner_UpdatedBy">
<span<?php echo $tbl_pensioner->UpdatedBy->ViewAttributes() ?>>
<?php echo $tbl_pensioner->UpdatedBy->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->UpdatedDate->Visible) { // UpdatedDate ?>
		<td<?php echo $tbl_pensioner->UpdatedDate->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_UpdatedDate" class="control-group tbl_pensioner_UpdatedDate">
<span<?php echo $tbl_pensioner->UpdatedDate->ViewAttributes() ?>>
<?php echo $tbl_pensioner->UpdatedDate->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->UpdateRemarks->Visible) { // UpdateRemarks ?>
		<td<?php echo $tbl_pensioner->UpdateRemarks->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_UpdateRemarks" class="control-group tbl_pensioner_UpdateRemarks">
<span<?php echo $tbl_pensioner->UpdateRemarks->ViewAttributes() ?>>
<?php echo $tbl_pensioner->UpdateRemarks->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_pensioner->codeGen->Visible) { // codeGen ?>
		<td<?php echo $tbl_pensioner->codeGen->CellAttributes() ?>>
<span id="el<?php echo $tbl_pensioner_delete->RowCnt ?>_tbl_pensioner_codeGen" class="control-group tbl_pensioner_codeGen">
<span<?php echo $tbl_pensioner->codeGen->ViewAttributes() ?>>
<?php echo $tbl_pensioner->codeGen->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$tbl_pensioner_delete->Recordset->MoveNext();
}
$tbl_pensioner_delete->Recordset->Close();
?>
</tbody>
</table>
</div>
</td></tr></table>
<div class="btn-group ewButtonGroup">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("DeleteBtn") ?></button>
</div>
</form>
<script type="text/javascript">
ftbl_pensionerdelete.Init();
</script>
<?php
$tbl_pensioner_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$tbl_pensioner_delete->Page_Terminate();
?>
