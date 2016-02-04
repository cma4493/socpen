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

$tbl_representative_delete = NULL; // Initialize page object first

class ctbl_representative_delete extends ctbl_representative {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Table name
	var $TableName = 'tbl_representative';

	// Page object name
	var $PageObjName = 'tbl_representative_delete';

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
			define("EW_PAGE_ID", 'delete', TRUE);

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
		if (!$Security->CanDelete()) {
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
			$this->Page_Terminate("tbl_representativelist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in tbl_representative class, tbl_representativeinfo.php

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
				$sThisKey .= $row['authID'];
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
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "tbl_representativelist.php", $this->TableVar);
		$PageCaption = $Language->Phrase("delete");
		$Breadcrumb->Add("delete", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", ew_CurrentUrl(), $this->TableVar);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'tbl_representative';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (delete page)
	function WriteAuditTrailOnDelete(&$rs) {
		if (!$this->AuditTrailOnDelete) return;
		$table = 'tbl_representative';

		// Get key value
		$key = "";
		if ($key <> "")
			$key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['authID'];

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
if (!isset($tbl_representative_delete)) $tbl_representative_delete = new ctbl_representative_delete();

// Page init
$tbl_representative_delete->Page_Init();

// Page main
$tbl_representative_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$tbl_representative_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var tbl_representative_delete = new ew_Page("tbl_representative_delete");
tbl_representative_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = tbl_representative_delete.PageID; // For backward compatibility

// Form object
var ftbl_representativedelete = new ew_Form("ftbl_representativedelete");

// Form_CustomValidate event
ftbl_representativedelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftbl_representativedelete.ValidateRequired = true;
<?php } else { ?>
ftbl_representativedelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
ftbl_representativedelete.Lists["x_relToPensioner"] = {"LinkField":"x_RelationID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_representativedelete.Lists["x_auth_Region"] = {"LinkField":"x_region_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_region_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_representativedelete.Lists["x_auth_prov"] = {"LinkField":"x_prov_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_prov_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_representativedelete.Lists["x_auth_city"] = {"LinkField":"x_city_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_city_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_representativedelete.Lists["x_auth_brgy"] = {"LinkField":"x_brgy_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_brgy_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($tbl_representative_delete->Recordset = $tbl_representative_delete->LoadRecordset())
	$tbl_representative_deleteTotalRecs = $tbl_representative_delete->Recordset->RecordCount(); // Get record count
if ($tbl_representative_deleteTotalRecs <= 0) { // No record found, exit
	if ($tbl_representative_delete->Recordset)
		$tbl_representative_delete->Recordset->Close();
	$tbl_representative_delete->Page_Terminate("tbl_representativelist.php"); // Return to list
}
?>
<?php //$Breadcrumb->Render(); ?>
<?php $tbl_representative_delete->ShowPageHeader(); ?>
<?php
$tbl_representative_delete->ShowMessage();
?>
<form name="ftbl_representativedelete" id="ftbl_representativedelete" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="tbl_representative">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($tbl_representative_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_tbl_representativedelete" class="ewTable ewTableSeparate">
<?php echo $tbl_representative->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($tbl_representative->authID->Visible) { // authID ?>
		<td><span id="elh_tbl_representative_authID" class="tbl_representative_authID"><?php echo $tbl_representative->authID->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_representative->PensionerID->Visible) { // PensionerID ?>
		<td><span id="elh_tbl_representative_PensionerID" class="tbl_representative_PensionerID"><?php echo $tbl_representative->PensionerID->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_representative->fname->Visible) { // fname ?>
		<td><span id="elh_tbl_representative_fname" class="tbl_representative_fname"><?php echo $tbl_representative->fname->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_representative->mname->Visible) { // mname ?>
		<td><span id="elh_tbl_representative_mname" class="tbl_representative_mname"><?php echo $tbl_representative->mname->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_representative->lname->Visible) { // lname ?>
		<td><span id="elh_tbl_representative_lname" class="tbl_representative_lname"><?php echo $tbl_representative->lname->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_representative->relToPensioner->Visible) { // relToPensioner ?>
		<td><span id="elh_tbl_representative_relToPensioner" class="tbl_representative_relToPensioner"><?php echo $tbl_representative->relToPensioner->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_representative->ContactNo->Visible) { // ContactNo ?>
		<td><span id="elh_tbl_representative_ContactNo" class="tbl_representative_ContactNo"><?php echo $tbl_representative->ContactNo->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_representative->auth_Region->Visible) { // auth_Region ?>
		<td><span id="elh_tbl_representative_auth_Region" class="tbl_representative_auth_Region"><?php echo $tbl_representative->auth_Region->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_representative->auth_prov->Visible) { // auth_prov ?>
		<td><span id="elh_tbl_representative_auth_prov" class="tbl_representative_auth_prov"><?php echo $tbl_representative->auth_prov->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_representative->auth_city->Visible) { // auth_city ?>
		<td><span id="elh_tbl_representative_auth_city" class="tbl_representative_auth_city"><?php echo $tbl_representative->auth_city->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_representative->auth_brgy->Visible) { // auth_brgy ?>
		<td><span id="elh_tbl_representative_auth_brgy" class="tbl_representative_auth_brgy"><?php echo $tbl_representative->auth_brgy->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_representative->houseNo->Visible) { // houseNo ?>
		<td><span id="elh_tbl_representative_houseNo" class="tbl_representative_houseNo"><?php echo $tbl_representative->houseNo->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_representative->CreatedBy->Visible) { // CreatedBy ?>
		<td><span id="elh_tbl_representative_CreatedBy" class="tbl_representative_CreatedBy"><?php echo $tbl_representative->CreatedBy->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_representative->CreatedDate->Visible) { // CreatedDate ?>
		<td><span id="elh_tbl_representative_CreatedDate" class="tbl_representative_CreatedDate"><?php echo $tbl_representative->CreatedDate->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_representative->UpdatedBy->Visible) { // UpdatedBy ?>
		<td><span id="elh_tbl_representative_UpdatedBy" class="tbl_representative_UpdatedBy"><?php echo $tbl_representative->UpdatedBy->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tbl_representative->UpdatedDate->Visible) { // UpdatedDate ?>
		<td><span id="elh_tbl_representative_UpdatedDate" class="tbl_representative_UpdatedDate"><?php echo $tbl_representative->UpdatedDate->FldCaption() ?></span></td>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$tbl_representative_delete->RecCnt = 0;
$i = 0;
while (!$tbl_representative_delete->Recordset->EOF) {
	$tbl_representative_delete->RecCnt++;
	$tbl_representative_delete->RowCnt++;

	// Set row properties
	$tbl_representative->ResetAttrs();
	$tbl_representative->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$tbl_representative_delete->LoadRowValues($tbl_representative_delete->Recordset);

	// Render row
	$tbl_representative_delete->RenderRow();
?>
	<tr<?php echo $tbl_representative->RowAttributes() ?>>
<?php if ($tbl_representative->authID->Visible) { // authID ?>
		<td<?php echo $tbl_representative->authID->CellAttributes() ?>>
<span id="el<?php echo $tbl_representative_delete->RowCnt ?>_tbl_representative_authID" class="control-group tbl_representative_authID">
<span<?php echo $tbl_representative->authID->ViewAttributes() ?>>
<?php echo $tbl_representative->authID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_representative->PensionerID->Visible) { // PensionerID ?>
		<td<?php echo $tbl_representative->PensionerID->CellAttributes() ?>>
<span id="el<?php echo $tbl_representative_delete->RowCnt ?>_tbl_representative_PensionerID" class="control-group tbl_representative_PensionerID">
<span<?php echo $tbl_representative->PensionerID->ViewAttributes() ?>>
<?php echo $tbl_representative->PensionerID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_representative->fname->Visible) { // fname ?>
		<td<?php echo $tbl_representative->fname->CellAttributes() ?>>
<span id="el<?php echo $tbl_representative_delete->RowCnt ?>_tbl_representative_fname" class="control-group tbl_representative_fname">
<span<?php echo $tbl_representative->fname->ViewAttributes() ?>>
<?php echo $tbl_representative->fname->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_representative->mname->Visible) { // mname ?>
		<td<?php echo $tbl_representative->mname->CellAttributes() ?>>
<span id="el<?php echo $tbl_representative_delete->RowCnt ?>_tbl_representative_mname" class="control-group tbl_representative_mname">
<span<?php echo $tbl_representative->mname->ViewAttributes() ?>>
<?php echo $tbl_representative->mname->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_representative->lname->Visible) { // lname ?>
		<td<?php echo $tbl_representative->lname->CellAttributes() ?>>
<span id="el<?php echo $tbl_representative_delete->RowCnt ?>_tbl_representative_lname" class="control-group tbl_representative_lname">
<span<?php echo $tbl_representative->lname->ViewAttributes() ?>>
<?php echo $tbl_representative->lname->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_representative->relToPensioner->Visible) { // relToPensioner ?>
		<td<?php echo $tbl_representative->relToPensioner->CellAttributes() ?>>
<span id="el<?php echo $tbl_representative_delete->RowCnt ?>_tbl_representative_relToPensioner" class="control-group tbl_representative_relToPensioner">
<span<?php echo $tbl_representative->relToPensioner->ViewAttributes() ?>>
<?php echo $tbl_representative->relToPensioner->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_representative->ContactNo->Visible) { // ContactNo ?>
		<td<?php echo $tbl_representative->ContactNo->CellAttributes() ?>>
<span id="el<?php echo $tbl_representative_delete->RowCnt ?>_tbl_representative_ContactNo" class="control-group tbl_representative_ContactNo">
<span<?php echo $tbl_representative->ContactNo->ViewAttributes() ?>>
<?php echo $tbl_representative->ContactNo->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_representative->auth_Region->Visible) { // auth_Region ?>
		<td<?php echo $tbl_representative->auth_Region->CellAttributes() ?>>
<span id="el<?php echo $tbl_representative_delete->RowCnt ?>_tbl_representative_auth_Region" class="control-group tbl_representative_auth_Region">
<span<?php echo $tbl_representative->auth_Region->ViewAttributes() ?>>
<?php echo $tbl_representative->auth_Region->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_representative->auth_prov->Visible) { // auth_prov ?>
		<td<?php echo $tbl_representative->auth_prov->CellAttributes() ?>>
<span id="el<?php echo $tbl_representative_delete->RowCnt ?>_tbl_representative_auth_prov" class="control-group tbl_representative_auth_prov">
<span<?php echo $tbl_representative->auth_prov->ViewAttributes() ?>>
<?php echo $tbl_representative->auth_prov->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_representative->auth_city->Visible) { // auth_city ?>
		<td<?php echo $tbl_representative->auth_city->CellAttributes() ?>>
<span id="el<?php echo $tbl_representative_delete->RowCnt ?>_tbl_representative_auth_city" class="control-group tbl_representative_auth_city">
<span<?php echo $tbl_representative->auth_city->ViewAttributes() ?>>
<?php echo $tbl_representative->auth_city->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_representative->auth_brgy->Visible) { // auth_brgy ?>
		<td<?php echo $tbl_representative->auth_brgy->CellAttributes() ?>>
<span id="el<?php echo $tbl_representative_delete->RowCnt ?>_tbl_representative_auth_brgy" class="control-group tbl_representative_auth_brgy">
<span<?php echo $tbl_representative->auth_brgy->ViewAttributes() ?>>
<?php echo $tbl_representative->auth_brgy->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_representative->houseNo->Visible) { // houseNo ?>
		<td<?php echo $tbl_representative->houseNo->CellAttributes() ?>>
<span id="el<?php echo $tbl_representative_delete->RowCnt ?>_tbl_representative_houseNo" class="control-group tbl_representative_houseNo">
<span<?php echo $tbl_representative->houseNo->ViewAttributes() ?>>
<?php echo $tbl_representative->houseNo->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_representative->CreatedBy->Visible) { // CreatedBy ?>
		<td<?php echo $tbl_representative->CreatedBy->CellAttributes() ?>>
<span id="el<?php echo $tbl_representative_delete->RowCnt ?>_tbl_representative_CreatedBy" class="control-group tbl_representative_CreatedBy">
<span<?php echo $tbl_representative->CreatedBy->ViewAttributes() ?>>
<?php echo $tbl_representative->CreatedBy->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_representative->CreatedDate->Visible) { // CreatedDate ?>
		<td<?php echo $tbl_representative->CreatedDate->CellAttributes() ?>>
<span id="el<?php echo $tbl_representative_delete->RowCnt ?>_tbl_representative_CreatedDate" class="control-group tbl_representative_CreatedDate">
<span<?php echo $tbl_representative->CreatedDate->ViewAttributes() ?>>
<?php echo $tbl_representative->CreatedDate->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_representative->UpdatedBy->Visible) { // UpdatedBy ?>
		<td<?php echo $tbl_representative->UpdatedBy->CellAttributes() ?>>
<span id="el<?php echo $tbl_representative_delete->RowCnt ?>_tbl_representative_UpdatedBy" class="control-group tbl_representative_UpdatedBy">
<span<?php echo $tbl_representative->UpdatedBy->ViewAttributes() ?>>
<?php echo $tbl_representative->UpdatedBy->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tbl_representative->UpdatedDate->Visible) { // UpdatedDate ?>
		<td<?php echo $tbl_representative->UpdatedDate->CellAttributes() ?>>
<span id="el<?php echo $tbl_representative_delete->RowCnt ?>_tbl_representative_UpdatedDate" class="control-group tbl_representative_UpdatedDate">
<span<?php echo $tbl_representative->UpdatedDate->ViewAttributes() ?>>
<?php echo $tbl_representative->UpdatedDate->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$tbl_representative_delete->Recordset->MoveNext();
}
$tbl_representative_delete->Recordset->Close();
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
ftbl_representativedelete.Init();
</script>
<?php
$tbl_representative_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$tbl_representative_delete->Page_Terminate();
?>
