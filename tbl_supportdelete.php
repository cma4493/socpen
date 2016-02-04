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

$tbl_support_delete = NULL; // Initialize page object first

class ctbl_support_delete extends ctbl_support {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Table name
	var $TableName = 'tbl_support';

	// Page object name
	var $PageObjName = 'tbl_support_delete';

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
			define("EW_PAGE_ID", 'delete', TRUE);

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
		if (!$Security->CanDelete()) {
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
			$this->Page_Terminate("tbl_supportlist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in tbl_support class, tbl_supportinfo.php

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

			// CreatedBy
			$this->CreatedBy->LinkCustomAttributes = "";
			$this->CreatedBy->HrefValue = "";
			$this->CreatedBy->TooltipValue = "";

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
				$sThisKey .= $row['supportID'];
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
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "tbl_supportlist.php", $this->TableVar);
		$PageCaption = $Language->Phrase("delete");
		$Breadcrumb->Add("delete", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", ew_CurrentUrl(), $this->TableVar);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'tbl_support';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (delete page)
	function WriteAuditTrailOnDelete(&$rs) {
		if (!$this->AuditTrailOnDelete) return;
		$table = 'tbl_support';

		// Get key value
		$key = "";
		if ($key <> "")
			$key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['supportID'];

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
if (!isset($tbl_support_delete)) $tbl_support_delete = new ctbl_support_delete();

// Page init
$tbl_support_delete->Page_Init();

// Page main
$tbl_support_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$tbl_support_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var tbl_support_delete = new ew_Page("tbl_support_delete");
tbl_support_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = tbl_support_delete.PageID; // For backward compatibility

// Form object
var ftbl_supportdelete = new ew_Form("ftbl_supportdelete");

// Form_CustomValidate event
ftbl_supportdelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftbl_supportdelete.ValidateRequired = true;
<?php } else { ?>
ftbl_supportdelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
ftbl_supportdelete.Lists["x_KindSupID"] = {"LinkField":"x_SupportID","Ajax":true,"AutoFill":false,"DisplayFields":["x_SupportKind","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_supportdelete.Lists["x_disabilityID"] = {"LinkField":"x_disabilityID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_supportdelete.Lists["x_assistiveID"] = {"LinkField":"x_assistiveID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Device","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_supportdelete.Lists["x_illnessID"] = {"LinkField":"x_illnessID","Ajax":true,"AutoFill":false,"DisplayFields":["x_description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_supportdelete.Lists["x_physconditionID"] = {"LinkField":"x_physconditionID","Ajax":true,"AutoFill":false,"DisplayFields":["x_physconditionName","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($tbl_support_delete->Recordset = $tbl_support_delete->LoadRecordset())
	$tbl_support_deleteTotalRecs = $tbl_support_delete->Recordset->RecordCount(); // Get record count
if ($tbl_support_deleteTotalRecs <= 0) { // No record found, exit
	if ($tbl_support_delete->Recordset)
		$tbl_support_delete->Recordset->Close();
	$tbl_support_delete->Page_Terminate("tbl_supportlist.php"); // Return to list
}
?>
<?php //$Breadcrumb->Render(); ?>
<?php $tbl_support_delete->ShowPageHeader(); ?>
<?php
$tbl_support_delete->ShowMessage();
?>
<form name="ftbl_supportdelete" id="ftbl_supportdelete" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="tbl_support">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($tbl_support_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_tbl_supportdelete" class="ewTable ewTableSeparate">
<?php echo $tbl_support->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($tbl_support->supportID->Visible) { // supportID ?>
		<td><span id="elh_tbl_support_supportID" class="tbl_support_supportID"><?php echo $tbl_support->supportID->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_support->PensionerID->Visible) { // PensionerID ?>
		<td><span id="elh_tbl_support_PensionerID" class="tbl_support_PensionerID"><?php echo $tbl_support->PensionerID->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_support->family_support->Visible) { // family_support ?>
		<td><span id="elh_tbl_support_family_support" class="tbl_support_family_support"><?php echo $tbl_support->family_support->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_support->KindSupID->Visible) { // KindSupID ?>
		<td><span id="elh_tbl_support_KindSupID" class="tbl_support_KindSupID"><?php echo $tbl_support->KindSupID->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_support->meals->Visible) { // meals ?>
		<td><span id="elh_tbl_support_meals" class="tbl_support_meals"><?php echo $tbl_support->meals->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_support->disability->Visible) { // disability ?>
		<td><span id="elh_tbl_support_disability" class="tbl_support_disability"><?php echo $tbl_support->disability->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_support->disabilityID->Visible) { // disabilityID ?>
		<td><span id="elh_tbl_support_disabilityID" class="tbl_support_disabilityID"><?php echo $tbl_support->disabilityID->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_support->immobile->Visible) { // immobile ?>
		<td><span id="elh_tbl_support_immobile" class="tbl_support_immobile"><?php echo $tbl_support->immobile->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_support->assistiveID->Visible) { // assistiveID ?>
		<td><span id="elh_tbl_support_assistiveID" class="tbl_support_assistiveID"><?php echo $tbl_support->assistiveID->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_support->preEx_illness->Visible) { // preEx_illness ?>
		<td><span id="elh_tbl_support_preEx_illness" class="tbl_support_preEx_illness"><?php echo $tbl_support->preEx_illness->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_support->illnessID->Visible) { // illnessID ?>
		<td><span id="elh_tbl_support_illnessID" class="tbl_support_illnessID"><?php echo $tbl_support->illnessID->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_support->physconditionID->Visible) { // physconditionID ?>
		<td><span id="elh_tbl_support_physconditionID" class="tbl_support_physconditionID"><?php echo $tbl_support->physconditionID->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_support->CreatedBy->Visible) { // CreatedBy ?>
		<td><span id="elh_tbl_support_CreatedBy" class="tbl_support_CreatedBy"><?php echo $tbl_support->CreatedBy->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_support->CreatedDate->Visible) { // CreatedDate ?>
		<td><span id="elh_tbl_support_CreatedDate" class="tbl_support_CreatedDate"><?php echo $tbl_support->CreatedDate->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_support->UpdatedBy->Visible) { // UpdatedBy ?>
		<td><span id="elh_tbl_support_UpdatedBy" class="tbl_support_UpdatedBy"><?php echo $tbl_support->UpdatedBy->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_support->UpdatedDate->Visible) { // UpdatedDate ?>
		<td><span id="elh_tbl_support_UpdatedDate" class="tbl_support_UpdatedDate"><?php echo $tbl_support->UpdatedDate->FldCaption() ?></span></td>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$tbl_support_delete->RecCnt = 0;
$i = 0;
while (!$tbl_support_delete->Recordset->EOF) {
	$tbl_support_delete->RecCnt++;
	$tbl_support_delete->RowCnt++;

	// Set row properties
	$tbl_support->ResetAttrs();
	$tbl_support->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$tbl_support_delete->LoadRowValues($tbl_support_delete->Recordset);

	// Render row
	$tbl_support_delete->RenderRow();
?>
	<tr<?php echo $tbl_support->RowAttributes() ?>>
<?php if ($tbl_support->supportID->Visible) { // supportID ?>
		<td<?php echo $tbl_support->supportID->CellAttributes() ?>>
<span id="el<?php echo $tbl_support_delete->RowCnt ?>_tbl_support_supportID" class="control-group tbl_support_supportID">
<span<?php echo $tbl_support->supportID->ViewAttributes() ?>>
<?php echo $tbl_support->supportID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_support->PensionerID->Visible) { // PensionerID ?>
		<td<?php echo $tbl_support->PensionerID->CellAttributes() ?>>
<span id="el<?php echo $tbl_support_delete->RowCnt ?>_tbl_support_PensionerID" class="control-group tbl_support_PensionerID">
<span<?php echo $tbl_support->PensionerID->ViewAttributes() ?>>
<?php echo $tbl_support->PensionerID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_support->family_support->Visible) { // family_support ?>
		<td<?php echo $tbl_support->family_support->CellAttributes() ?>>
<span id="el<?php echo $tbl_support_delete->RowCnt ?>_tbl_support_family_support" class="control-group tbl_support_family_support">
<span<?php echo $tbl_support->family_support->ViewAttributes() ?>>
<?php echo $tbl_support->family_support->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_support->KindSupID->Visible) { // KindSupID ?>
		<td<?php echo $tbl_support->KindSupID->CellAttributes() ?>>
<span id="el<?php echo $tbl_support_delete->RowCnt ?>_tbl_support_KindSupID" class="control-group tbl_support_KindSupID">
<span<?php echo $tbl_support->KindSupID->ViewAttributes() ?>>
<?php echo $tbl_support->KindSupID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_support->meals->Visible) { // meals ?>
		<td<?php echo $tbl_support->meals->CellAttributes() ?>>
<span id="el<?php echo $tbl_support_delete->RowCnt ?>_tbl_support_meals" class="control-group tbl_support_meals">
<span<?php echo $tbl_support->meals->ViewAttributes() ?>>
<?php echo $tbl_support->meals->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_support->disability->Visible) { // disability ?>
		<td<?php echo $tbl_support->disability->CellAttributes() ?>>
<span id="el<?php echo $tbl_support_delete->RowCnt ?>_tbl_support_disability" class="control-group tbl_support_disability">
<span<?php echo $tbl_support->disability->ViewAttributes() ?>>
<?php echo $tbl_support->disability->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_support->disabilityID->Visible) { // disabilityID ?>
		<td<?php echo $tbl_support->disabilityID->CellAttributes() ?>>
<span id="el<?php echo $tbl_support_delete->RowCnt ?>_tbl_support_disabilityID" class="control-group tbl_support_disabilityID">
<span<?php echo $tbl_support->disabilityID->ViewAttributes() ?>>
<?php echo $tbl_support->disabilityID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_support->immobile->Visible) { // immobile ?>
		<td<?php echo $tbl_support->immobile->CellAttributes() ?>>
<span id="el<?php echo $tbl_support_delete->RowCnt ?>_tbl_support_immobile" class="control-group tbl_support_immobile">
<span<?php echo $tbl_support->immobile->ViewAttributes() ?>>
<?php echo $tbl_support->immobile->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_support->assistiveID->Visible) { // assistiveID ?>
		<td<?php echo $tbl_support->assistiveID->CellAttributes() ?>>
<span id="el<?php echo $tbl_support_delete->RowCnt ?>_tbl_support_assistiveID" class="control-group tbl_support_assistiveID">
<span<?php echo $tbl_support->assistiveID->ViewAttributes() ?>>
<?php echo $tbl_support->assistiveID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_support->preEx_illness->Visible) { // preEx_illness ?>
		<td<?php echo $tbl_support->preEx_illness->CellAttributes() ?>>
<span id="el<?php echo $tbl_support_delete->RowCnt ?>_tbl_support_preEx_illness" class="control-group tbl_support_preEx_illness">
<span<?php echo $tbl_support->preEx_illness->ViewAttributes() ?>>
<?php echo $tbl_support->preEx_illness->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_support->illnessID->Visible) { // illnessID ?>
		<td<?php echo $tbl_support->illnessID->CellAttributes() ?>>
<span id="el<?php echo $tbl_support_delete->RowCnt ?>_tbl_support_illnessID" class="control-group tbl_support_illnessID">
<span<?php echo $tbl_support->illnessID->ViewAttributes() ?>>
<?php echo $tbl_support->illnessID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_support->physconditionID->Visible) { // physconditionID ?>
		<td<?php echo $tbl_support->physconditionID->CellAttributes() ?>>
<span id="el<?php echo $tbl_support_delete->RowCnt ?>_tbl_support_physconditionID" class="control-group tbl_support_physconditionID">
<span<?php echo $tbl_support->physconditionID->ViewAttributes() ?>>
<?php echo $tbl_support->physconditionID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_support->CreatedBy->Visible) { // CreatedBy ?>
		<td<?php echo $tbl_support->CreatedBy->CellAttributes() ?>>
<span id="el<?php echo $tbl_support_delete->RowCnt ?>_tbl_support_CreatedBy" class="control-group tbl_support_CreatedBy">
<span<?php echo $tbl_support->CreatedBy->ViewAttributes() ?>>
<?php echo $tbl_support->CreatedBy->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_support->CreatedDate->Visible) { // CreatedDate ?>
		<td<?php echo $tbl_support->CreatedDate->CellAttributes() ?>>
<span id="el<?php echo $tbl_support_delete->RowCnt ?>_tbl_support_CreatedDate" class="control-group tbl_support_CreatedDate">
<span<?php echo $tbl_support->CreatedDate->ViewAttributes() ?>>
<?php echo $tbl_support->CreatedDate->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_support->UpdatedBy->Visible) { // UpdatedBy ?>
		<td<?php echo $tbl_support->UpdatedBy->CellAttributes() ?>>
<span id="el<?php echo $tbl_support_delete->RowCnt ?>_tbl_support_UpdatedBy" class="control-group tbl_support_UpdatedBy">
<span<?php echo $tbl_support->UpdatedBy->ViewAttributes() ?>>
<?php echo $tbl_support->UpdatedBy->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_support->UpdatedDate->Visible) { // UpdatedDate ?>
		<td<?php echo $tbl_support->UpdatedDate->CellAttributes() ?>>
<span id="el<?php echo $tbl_support_delete->RowCnt ?>_tbl_support_UpdatedDate" class="control-group tbl_support_UpdatedDate">
<span<?php echo $tbl_support->UpdatedDate->ViewAttributes() ?>>
<?php echo $tbl_support->UpdatedDate->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$tbl_support_delete->Recordset->MoveNext();
}
$tbl_support_delete->Recordset->Close();
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
ftbl_supportdelete.Init();
</script>
<?php
$tbl_support_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$tbl_support_delete->Page_Terminate();
?>
