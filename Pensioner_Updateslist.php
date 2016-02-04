<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "Pensioner_Updatesinfo.php" ?>
<?php include_once "tbl_userinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$Pensioner_Updates_list = NULL; // Initialize page object first

class cPensioner_Updates_list extends cPensioner_Updates {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{6A2D1166-E78D-4185-AF54-9032030AE3DF}";

	// Table name
	var $TableName = 'Pensioner Updates';

	// Page object name
	var $PageObjName = 'Pensioner_Updates_list';

	// Grid form hidden field names
	var $FormName = 'fPensioner_Updateslist';
	var $FormActionName = 'k_action';
	var $FormKeyName = 'k_key';
	var $FormOldKeyName = 'k_oldkey';
	var $FormBlankRowName = 'k_blankrow';
	var $FormKeyCountName = 'key_count';

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

	// Page URLs
	var $AddUrl;
	var $EditUrl;
	var $CopyUrl;
	var $DeleteUrl;
	var $ViewUrl;
	var $ListUrl;

	// Export URLs
	var $ExportPrintUrl;
	var $ExportHtmlUrl;
	var $ExportExcelUrl;
	var $ExportWordUrl;
	var $ExportXmlUrl;
	var $ExportCsvUrl;
	var $ExportPdfUrl;

	// Update URLs
	var $InlineAddUrl;
	var $InlineCopyUrl;
	var $InlineEditUrl;
	var $GridAddUrl;
	var $GridEditUrl;
	var $MultiDeleteUrl;
	var $MultiApproveUrl;
	var $MultiDisapproveUrl;
	var $MultiUpdateUrl;

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
		$hidden = TRUE;
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

		// Table object (Pensioner_Updates)
		if (!isset($GLOBALS["Pensioner_Updates"])) {
			$GLOBALS["Pensioner_Updates"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["Pensioner_Updates"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "Pensioner_Updatesadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "Pensioner_Updatesdelete.php";
		$this->MultiApproveUrl = "Pensioner_Updatesapprove.php";
		$this->MultiDisapproveUrl = "Pensioner_Updatesdisapprove.php";
		$this->MultiUpdateUrl = "Pensioner_Updatesupdate.php";

		// Table object (tbl_user)
		if (!isset($GLOBALS['tbl_user'])) $GLOBALS['tbl_user'] = new ctbl_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'Pensioner Updates', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();

		// List options
		$this->ListOptions = new cListOptions();
		$this->ListOptions->TableVar = $this->TableVar;

		// Export options
		$this->ExportOptions = new cListOptions();
		$this->ExportOptions->Tag = "span";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Other options
		$this->OtherOptions['addedit'] = new cListOptions();
		$this->OtherOptions['addedit']->Tag = "span";
		$this->OtherOptions['addedit']->TagClassName = "ewAddEditOption";
		$this->OtherOptions['detail'] = new cListOptions();
		$this->OtherOptions['detail']->Tag = "span";
		$this->OtherOptions['detail']->TagClassName = "ewDetailOption";
		$this->OtherOptions['action'] = new cListOptions();
		$this->OtherOptions['action']->Tag = "span";
		$this->OtherOptions['action']->TagClassName = "ewActionOption";
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
		if (!$Security->CanList()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate("login.php");
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

		// Get export parameters
		if (@$_GET["export"] <> "") {
			$this->Export = $_GET["export"];
		} elseif (ew_IsHttpPost()) {
			if (@$_POST["exporttype"] <> "")
				$this->Export = $_POST["exporttype"];
		} else {
			$this->setExportReturnUrl(ew_CurrentUrl());
		}
		$gsExport = $this->Export; // Get export parameter, used in header
		$gsExportFile = $this->TableVar; // Get export file, used in header
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up curent action

		// Get grid add count
		$gridaddcnt = @$_GET[EW_TABLE_GRID_ADD_ROW_COUNT];
		if (is_numeric($gridaddcnt) && $gridaddcnt > 0)
			$this->GridAddRowCount = $gridaddcnt;

		// Set up list options
		$this->SetupListOptions();

		// Setup export options
		$this->SetupExportOptions();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Setup other options
		$this->SetupOtherOptions();

		// Set "checkbox" visible
		if (count($this->CustomActions) > 0)
			$this->ListOptions->Items["checkbox"]->Visible = TRUE;

		// Update url if printer friendly for Pdf
		if ($this->PrinterFriendlyForPdf)
			$this->ExportOptions->Items["pdf"]->Body = str_replace($this->ExportPdfUrl, $this->ExportPrintUrl . "&pdf=1", $this->ExportOptions->Items["pdf"]->Body);
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $conn;

		// Page Unload event
		$this->Page_Unload();
		if ($this->Export == "print" && @$_GET["pdf"] == "1") { // Printer friendly version and with pdf=1 in URL parameters
			$pdf = new cExportPdf($GLOBALS["Table"]);
			$pdf->Text = ob_get_contents(); // Set the content as the HTML of current page (printer friendly version)
			ob_end_clean();
			$pdf->Export();
		}

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

	// Class variables
	var $ListOptions; // List options
	var $ExportOptions; // Export options
	var $OtherOptions = array(); // Other options
	var $DisplayRecs = 20;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $Pager;
	var $SearchWhere = ""; // Search WHERE clause
	var $RecCnt = 0; // Record count
	var $EditRowCnt;
	var $StartRowCnt = 1;
	var $RowCnt = 0;
	var $Attrs = array(); // Row attributes and cell attributes
	var $RowIndex = 0; // Row index
	var $KeyCount = 0; // Key count
	var $RowAction = ""; // Row action
	var $RowOldKey = ""; // Row old key (for copy)
	var $RecPerRow = 0;
	var $ColCnt = 0;
	var $DbMasterFilter = ""; // Master filter
	var $DbDetailFilter = ""; // Detail filter
	var $MasterRecordExists;	
	var $MultiSelectKey;
	var $Command;
	var $RestoreSearch = FALSE;
	var $Recordset;
	var $OldRecordset;

	//
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError, $gsSearchError, $Security;

		// Search filters
		$sSrchAdvanced = ""; // Advanced search filter
		$sSrchBasic = ""; // Basic search filter
		$sFilter = "";

		// Get command
		$this->Command = strtolower(@$_GET["cmd"]);
		if ($this->IsPageRequest()) { // Validate request

			// Process custom action first
			$this->ProcessCustomAction();

			// Set up records per page
			$this->SetUpDisplayRecs();

			// Handle reset command
			$this->ResetCmd();

			// Set up Breadcrumb
			$this->SetupBreadcrumb();

			// Hide list options
			if ($this->Export <> "") {
				$this->ListOptions->HideAllOptions(array("sequence"));
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			} elseif ($this->CurrentAction == "gridadd" || $this->CurrentAction == "gridedit") {
				$this->ListOptions->HideAllOptions();
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			}

			// Hide export options
			if ($this->Export <> "" || $this->CurrentAction <> "")
				$this->ExportOptions->HideAllOptions();

			// Hide other options
			if ($this->Export <> "") {
				foreach ($this->OtherOptions as &$option)
					$option->HideAllOptions();
			}

			// Get basic search values
			$this->LoadBasicSearchValues();

			// Get and validate search values for advanced search
			$this->LoadSearchValues(); // Get search values
			if (!$this->ValidateSearch())
				$this->setFailureMessage($gsSearchError);

			// Restore search parms from Session if not searching / reset
			if ($this->Command <> "search" && $this->Command <> "reset" && $this->Command <> "resetall" && $this->CheckSearchParms())
				$this->RestoreSearchParms();

			// Call Recordset SearchValidated event
			$this->Recordset_SearchValidated();

			// Set up sorting order
			$this->SetUpSortOrder();

			// Get basic search criteria
			if ($gsSearchError == "")
				$sSrchBasic = $this->BasicSearchWhere();

			// Get search criteria for advanced search
			if ($gsSearchError == "")
				$sSrchAdvanced = $this->AdvancedSearchWhere();
		}

		// Restore display records
		if ($this->getRecordsPerPage() <> "") {
			$this->DisplayRecs = $this->getRecordsPerPage(); // Restore from Session
		} else {
			$this->DisplayRecs = 20; // Load default
		}

		// Load Sorting Order
		$this->LoadSortOrder();

		// Load search default if no existing search criteria
		if (!$this->CheckSearchParms()) {

			// Load basic search from default
			$this->BasicSearch->LoadDefault();
			if ($this->BasicSearch->Keyword != "")
				$sSrchBasic = $this->BasicSearchWhere();

			// Load advanced search from default
			if ($this->LoadAdvancedSearchDefault()) {
				$sSrchAdvanced = $this->AdvancedSearchWhere();
			}
		}

		// Build search criteria
		ew_AddFilter($this->SearchWhere, $sSrchAdvanced);
		ew_AddFilter($this->SearchWhere, $sSrchBasic);

		// Call Recordset_Searching event
		$this->Recordset_Searching($this->SearchWhere);

		// Save search criteria
		if ($this->Command == "search" && !$this->RestoreSearch) {
			$this->setSearchWhere($this->SearchWhere); // Save to Session
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} else {
			$this->SearchWhere = $this->getSearchWhere();
		}

		// Build filter
		$sFilter = "";
		if (!$Security->CanList())
			$sFilter = "(0=1)"; // Filter all records
		ew_AddFilter($sFilter, $this->DbDetailFilter);
		ew_AddFilter($sFilter, $this->SearchWhere);

		// Set up filter in session
		$this->setSessionWhere($sFilter);
		$this->CurrentFilter = "";

		// Export data only
		if (in_array($this->Export, array("html","word","excel","xml","csv","email","pdf"))) {
			$this->ExportData();
			$this->Page_Terminate(); // Terminate response
			exit();
		}
	}

	// Set up number of records displayed per page
	function SetUpDisplayRecs() {
		$sWrk = @$_GET[EW_TABLE_REC_PER_PAGE];
		if ($sWrk <> "") {
			if (is_numeric($sWrk)) {
				$this->DisplayRecs = intval($sWrk);
			} else {
				if (strtolower($sWrk) == "all") { // Display all records
					$this->DisplayRecs = -1;
				} else {
					$this->DisplayRecs = 20; // Non-numeric, load default
				}
			}
			$this->setRecordsPerPage($this->DisplayRecs); // Save to Session

			// Reset start position
			$this->StartRec = 1;
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Build filter for all keys
	function BuildKeyFilter() {
		global $objForm;
		$sWrkFilter = "";

		// Update row index and get row key
		$rowindex = 1;
		$objForm->Index = $rowindex;
		$sThisKey = strval($objForm->GetValue("k_key"));
		while ($sThisKey <> "") {
			if ($this->SetupKeyValues($sThisKey)) {
				$sFilter = $this->KeyFilter();
				if ($sWrkFilter <> "") $sWrkFilter .= " OR ";
				$sWrkFilter .= $sFilter;
			} else {
				$sWrkFilter = "0=1";
				break;
			}

			// Update row index and get row key
			$rowindex++; // Next row
			$objForm->Index = $rowindex;
			$sThisKey = strval($objForm->GetValue("k_key"));
		}
		return $sWrkFilter;
	}

	// Set up key values
	function SetupKeyValues($key) {
		$arrKeyFlds = explode($GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"], $key);
		if (count($arrKeyFlds) >= 1) {
			$this->updatesID->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->updatesID->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Advanced search WHERE clause based on QueryString
	function AdvancedSearchWhere() {
		global $Security;
		$sWhere = "";
		if (!$Security->CanSearch()) return "";
		$this->BuildSearchSql($sWhere, $this->updatesID, FALSE); // updatesID
		$this->BuildSearchSql($sWhere, $this->PensionerID, FALSE); // PensionerID
		$this->BuildSearchSql($sWhere, $this->status, FALSE); // status
		$this->BuildSearchSql($sWhere, $this->_field, FALSE); // field
		$this->BuildSearchSql($sWhere, $this->new_value, FALSE); // new_value
		$this->BuildSearchSql($sWhere, $this->old_value, FALSE); // old_value
		$this->BuildSearchSql($sWhere, $this->dateUpdated, FALSE); // dateUpdated
		$this->BuildSearchSql($sWhere, $this->approved, FALSE); // approved
		$this->BuildSearchSql($sWhere, $this->deathDate, FALSE); // deathDate
		$this->BuildSearchSql($sWhere, $this->paymentmodeID, FALSE); // paymentmodeID
		$this->BuildSearchSql($sWhere, $this->UpdatedBy, FALSE); // UpdatedBy
		$this->BuildSearchSql($sWhere, $this->UpdatedDate, FALSE); // UpdatedDate
		$this->BuildSearchSql($sWhere, $this->Createdby, FALSE); // Createdby
		$this->BuildSearchSql($sWhere, $this->CreatedDate, FALSE); // CreatedDate
		$this->BuildSearchSql($sWhere, $this->Remarks, FALSE); // Remarks

		// Set up search parm
		if ($sWhere <> "") {
			$this->Command = "search";
		}
		if ($this->Command == "search") {
			$this->updatesID->AdvancedSearch->Save(); // updatesID
			$this->PensionerID->AdvancedSearch->Save(); // PensionerID
			$this->status->AdvancedSearch->Save(); // status
			$this->_field->AdvancedSearch->Save(); // field
			$this->new_value->AdvancedSearch->Save(); // new_value
			$this->old_value->AdvancedSearch->Save(); // old_value
			$this->dateUpdated->AdvancedSearch->Save(); // dateUpdated
			$this->approved->AdvancedSearch->Save(); // approved
			$this->deathDate->AdvancedSearch->Save(); // deathDate
			$this->paymentmodeID->AdvancedSearch->Save(); // paymentmodeID
			$this->UpdatedBy->AdvancedSearch->Save(); // UpdatedBy
			$this->UpdatedDate->AdvancedSearch->Save(); // UpdatedDate
			$this->Createdby->AdvancedSearch->Save(); // Createdby
			$this->CreatedDate->AdvancedSearch->Save(); // CreatedDate
			$this->Remarks->AdvancedSearch->Save(); // Remarks
		}
		return $sWhere;
	}

	// Build search SQL
	function BuildSearchSql(&$Where, &$Fld, $MultiValue) {
		$FldParm = substr($Fld->FldVar, 2);
		$FldVal = $Fld->AdvancedSearch->SearchValue; // @$_GET["x_$FldParm"]
		$FldOpr = $Fld->AdvancedSearch->SearchOperator; // @$_GET["z_$FldParm"]
		$FldCond = $Fld->AdvancedSearch->SearchCondition; // @$_GET["v_$FldParm"]
		$FldVal2 = $Fld->AdvancedSearch->SearchValue2; // @$_GET["y_$FldParm"]
		$FldOpr2 = $Fld->AdvancedSearch->SearchOperator2; // @$_GET["w_$FldParm"]
		$sWrk = "";

		//$FldVal = ew_StripSlashes($FldVal);
		if (is_array($FldVal)) $FldVal = implode(",", $FldVal);

		//$FldVal2 = ew_StripSlashes($FldVal2);
		if (is_array($FldVal2)) $FldVal2 = implode(",", $FldVal2);
		$FldOpr = strtoupper(trim($FldOpr));
		if ($FldOpr == "") $FldOpr = "=";
		$FldOpr2 = strtoupper(trim($FldOpr2));
		if ($FldOpr2 == "") $FldOpr2 = "=";
		if (EW_SEARCH_MULTI_VALUE_OPTION == 1 || $FldOpr <> "LIKE" ||
			($FldOpr2 <> "LIKE" && $FldVal2 <> ""))
			$MultiValue = FALSE;
		if ($MultiValue) {
			$sWrk1 = ($FldVal <> "") ? ew_GetMultiSearchSql($Fld, $FldOpr, $FldVal) : ""; // Field value 1
			$sWrk2 = ($FldVal2 <> "") ? ew_GetMultiSearchSql($Fld, $FldOpr2, $FldVal2) : ""; // Field value 2
			$sWrk = $sWrk1; // Build final SQL
			if ($sWrk2 <> "")
				$sWrk = ($sWrk <> "") ? "($sWrk) $FldCond ($sWrk2)" : $sWrk2;
		} else {
			$FldVal = $this->ConvertSearchValue($Fld, $FldVal);
			$FldVal2 = $this->ConvertSearchValue($Fld, $FldVal2);
			$sWrk = ew_GetSearchSql($Fld, $FldVal, $FldOpr, $FldCond, $FldVal2, $FldOpr2);
		}
		ew_AddFilter($Where, $sWrk);
	}

	// Convert search value
	function ConvertSearchValue(&$Fld, $FldVal) {
		if ($FldVal == EW_NULL_VALUE || $FldVal == EW_NOT_NULL_VALUE)
			return $FldVal;
		$Value = $FldVal;
		if ($Fld->FldDataType == EW_DATATYPE_BOOLEAN) {
			if ($FldVal <> "") $Value = ($FldVal == "1" || strtolower(strval($FldVal)) == "y" || strtolower(strval($FldVal)) == "t") ? $Fld->TrueValue : $Fld->FalseValue;
		} elseif ($Fld->FldDataType == EW_DATATYPE_DATE) {
			if ($FldVal <> "") $Value = ew_UnFormatDateTime($FldVal, $Fld->FldDateTimeFormat);
		}
		return $Value;
	}

	// Return basic search SQL
	function BasicSearchSQL($Keyword) {
		$sKeyword = ew_AdjustSql($Keyword);
		$sWhere = "";
		$this->BuildBasicSearchSQL($sWhere, $this->_field, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->new_value, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->old_value, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->Remarks, $Keyword);
		return $sWhere;
	}

	// Build basic search SQL
	function BuildBasicSearchSql(&$Where, &$Fld, $Keyword) {
		if ($Keyword == EW_NULL_VALUE) {
			$sWrk = $Fld->FldExpression . " IS NULL";
		} elseif ($Keyword == EW_NOT_NULL_VALUE) {
			$sWrk = $Fld->FldExpression . " IS NOT NULL";
		} else {
			$sFldExpression = ($Fld->FldVirtualExpression <> $Fld->FldExpression) ? $Fld->FldVirtualExpression : $Fld->FldBasicSearchExpression;
			$sWrk = $sFldExpression . ew_Like(ew_QuotedValue("%" . $Keyword . "%", EW_DATATYPE_STRING));
		}
		if ($Where <> "") $Where .= " OR ";
		$Where .= $sWrk;
	}

	// Return basic search WHERE clause based on search keyword and type
	function BasicSearchWhere() {
		global $Security;
		$sSearchStr = "";
		if (!$Security->CanSearch()) return "";
		$sSearchKeyword = $this->BasicSearch->Keyword;
		$sSearchType = $this->BasicSearch->Type;
		if ($sSearchKeyword <> "") {
			$sSearch = trim($sSearchKeyword);
			if ($sSearchType <> "=") {
				while (strpos($sSearch, "  ") !== FALSE)
					$sSearch = str_replace("  ", " ", $sSearch);
				$arKeyword = explode(" ", trim($sSearch));
				foreach ($arKeyword as $sKeyword) {
					if ($sSearchStr <> "") $sSearchStr .= " " . $sSearchType . " ";
					$sSearchStr .= "(" . $this->BasicSearchSQL($sKeyword) . ")";
				}
			} else {
				$sSearchStr = $this->BasicSearchSQL($sSearch);
			}
			$this->Command = "search";
		}
		if ($this->Command == "search") {
			$this->BasicSearch->setKeyword($sSearchKeyword);
			$this->BasicSearch->setType($sSearchType);
		}
		return $sSearchStr;
	}

	// Check if search parm exists
	function CheckSearchParms() {

		// Check basic search
		if ($this->BasicSearch->IssetSession())
			return TRUE;
		if ($this->updatesID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->PensionerID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->status->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->_field->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->new_value->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->old_value->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->dateUpdated->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->approved->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->deathDate->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->paymentmodeID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->UpdatedBy->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->UpdatedDate->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Createdby->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->CreatedDate->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Remarks->AdvancedSearch->IssetSession())
			return TRUE;
		return FALSE;
	}

	// Clear all search parameters
	function ResetSearchParms() {

		// Clear search WHERE clause
		$this->SearchWhere = "";
		$this->setSearchWhere($this->SearchWhere);

		// Clear basic search parameters
		$this->ResetBasicSearchParms();

		// Clear advanced search parameters
		$this->ResetAdvancedSearchParms();
	}

	// Load advanced search default values
	function LoadAdvancedSearchDefault() {
		return FALSE;
	}

	// Clear all basic search parameters
	function ResetBasicSearchParms() {
		$this->BasicSearch->UnsetSession();
	}

	// Clear all advanced search parameters
	function ResetAdvancedSearchParms() {
		$this->updatesID->AdvancedSearch->UnsetSession();
		$this->PensionerID->AdvancedSearch->UnsetSession();
		$this->status->AdvancedSearch->UnsetSession();
		$this->_field->AdvancedSearch->UnsetSession();
		$this->new_value->AdvancedSearch->UnsetSession();
		$this->old_value->AdvancedSearch->UnsetSession();
		$this->dateUpdated->AdvancedSearch->UnsetSession();
		$this->approved->AdvancedSearch->UnsetSession();
		$this->deathDate->AdvancedSearch->UnsetSession();
		$this->paymentmodeID->AdvancedSearch->UnsetSession();
		$this->UpdatedBy->AdvancedSearch->UnsetSession();
		$this->UpdatedDate->AdvancedSearch->UnsetSession();
		$this->Createdby->AdvancedSearch->UnsetSession();
		$this->CreatedDate->AdvancedSearch->UnsetSession();
		$this->Remarks->AdvancedSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore basic search values
		$this->BasicSearch->Load();

		// Restore advanced search values
		$this->updatesID->AdvancedSearch->Load();
		$this->PensionerID->AdvancedSearch->Load();
		$this->status->AdvancedSearch->Load();
		$this->_field->AdvancedSearch->Load();
		$this->new_value->AdvancedSearch->Load();
		$this->old_value->AdvancedSearch->Load();
		$this->dateUpdated->AdvancedSearch->Load();
		$this->approved->AdvancedSearch->Load();
		$this->deathDate->AdvancedSearch->Load();
		$this->paymentmodeID->AdvancedSearch->Load();
		$this->UpdatedBy->AdvancedSearch->Load();
		$this->UpdatedDate->AdvancedSearch->Load();
		$this->Createdby->AdvancedSearch->Load();
		$this->CreatedDate->AdvancedSearch->Load();
		$this->Remarks->AdvancedSearch->Load();
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->setStartRecordNumber(1); // Reset start position
		}
	}

	// Load sort order parameters
	function LoadSortOrder() {
		$sOrderBy = $this->getSessionOrderBy(); // Get ORDER BY from Session
		if ($sOrderBy == "") {
			if ($this->SqlOrderBy() <> "") {
				$sOrderBy = $this->SqlOrderBy();
				$this->setSessionOrderBy($sOrderBy);
			}
		}
	}

	// Reset command
	// - cmd=reset (Reset search parameters)
	// - cmd=resetall (Reset search and master/detail parameters)
	// - cmd=resetsort (Reset sort parameters)
	function ResetCmd() {

		// Check if reset command
		if (substr($this->Command,0,5) == "reset") {

			// Reset search criteria
			if ($this->Command == "reset" || $this->Command == "resetall")
				$this->ResetSearchParms();

			// Reset sorting order
			if ($this->Command == "resetsort") {
				$sOrderBy = "";
				$this->setSessionOrderBy($sOrderBy);
			}

			// Reset start position
			$this->StartRec = 1;
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Set up list options
	function SetupListOptions() {
		global $Security, $Language;

		// Add group option item
		$item = &$this->ListOptions->Add($this->ListOptions->GroupOptionName);
		$item->Body = "";
		$item->OnLeft = TRUE;
		$item->Visible = FALSE;

		// "view"
		$item = &$this->ListOptions->Add("view");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = FALSE AND $Security->CanView();
		$item->OnLeft = TRUE;

		// "edit"
		$item = &$this->ListOptions->Add("edit");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = FALSE AND $Security->CanEdit();
		$item->OnLeft = TRUE;

		// "checkbox"
		$item = &$this->ListOptions->Add("checkbox");
		// $item->Visible = $Security->CanDelete();
		$item->Visible = $Security->AllowList(CurrentProjectID() . 'updates_approval');
		$item->OnLeft = TRUE;
		$item->Header = "<label class=\"checkbox\"><input type=\"checkbox\" name=\"key\" id=\"key\" onclick=\"ew_SelectAllKey(this);\"></label>";
		$item->MoveTo(0);
		$item->ShowInDropDown = FALSE;
		$item->ShowInButtonGroup = FALSE;

		// Drop down button for ListOptions
		$this->ListOptions->UseDropDownButton = FALSE;
		$this->ListOptions->DropDownButtonPhrase = $Language->Phrase("ButtonListOptions");
		$this->ListOptions->UseButtonGroup = FALSE;
		$this->ListOptions->ButtonClass = "btn-sm"; // Class for button group tolits "btn-small"

		// Call ListOptions_Load event
		$this->ListOptions_Load();
		$item = &$this->ListOptions->GetItem($this->ListOptions->GroupOptionName);
		$item->Visible = $this->ListOptions->GroupOptionVisible();
	}

	// Render list options
	function RenderListOptions() {
		global $Security, $Language, $objForm;
		$this->ListOptions->LoadDefault();

		// "view"
		$oListOpt = &$this->ListOptions->Items["view"];
		if ($Security->CanView())
			$oListOpt->Body = "<a class=\"blue\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("ViewLink")) . "\" href=\"" . ew_HtmlEncode($this->ViewUrl) . "\">" . $Language->Phrase("ViewLink") . "<i class=\"icon-zoom-in bigger-130\"></i></a>";
		else
			$oListOpt->Body = "";

		// "edit"
		$oListOpt = &$this->ListOptions->Items["edit"];
		if ($Security->CanEdit()) {
			$oListOpt->Body = "<a class=\"green\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("EditLink")) . "\" href=\"" . ew_HtmlEncode($this->EditUrl) . "\">" . $Language->Phrase("EditLink") . "<i class=\"icon-pencil bigger-130\"></i></a>";
		} else {
			$oListOpt->Body = "";
		}

		// "checkbox"
		$oListOpt = &$this->ListOptions->Items["checkbox"];
		$oListOpt->Body = "<label class=\"checkbox\"><input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->updatesID->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event, this);'></label>";
		$this->RenderListOptionsExt();

		// Call ListOptions_Rendered event
		$this->ListOptions_Rendered();
	}

	// Set up other options
	function SetupOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
		$option = $options["addedit"];

		// Add
		$item = &$option->Add("add");
		$item->Body = "<a class=\"btn btn-purple btn-sm\" href=\"" . ew_HtmlEncode($this->AddUrl) . "\">" . "<i class='icon-file align-middle bigger-125'></i> ". $Language->Phrase("AddLink")."</a>";
		$item->Visible = FALSE AND ($this->AddUrl <> "" && $Security->CanAdd());
		$option = $options["action"];

		// Add multi delete
		$item = &$option->Add("multidelete");
		$item->Body = "<a class=\"btn btn-purple btn-sm\" href=\"\" onclick=\"ew_SubmitSelected(document.fPensioner_Updateslist, '" . $this->MultiDeleteUrl . "', ewLanguage.Phrase('DeleteMultiConfirmMsg'));return false;\">" . $Language->Phrase("DeleteSelectedLink") . "</a>";
		$item->Visible = FALSE AND ($Security->CanDelete());

		// Add multi approve
		$item = &$option->Add("multiapprove");
		$item->Body = "<a class=\"btn btn-success btn-sm\" href=\"\" onclick=\"ew_SubmitSelected(document.fPensioner_Updateslist, '" . $this->MultiApproveUrl . "', ewLanguage.Phrase('ApproveMultiConfirmMsg'));return false;\">" . "Approve" . "</a>";
		$item->Visible = $Security->AllowList(CurrentProjectID() . 'updates_approval');

		// Add multi disapprove
		$item = &$option->Add("multidisapprove");
		$item->Body = "<a class=\"btn btn-danger btn-sm\" href=\"\" onclick=\"ew_SubmitSelected(document.fPensioner_Updateslist, '" . $this->MultiDisapproveUrl . "', ewLanguage.Phrase('DisapproveMultiConfirmMsg'));return false;\">" . "Disapprove" . "</a>";
		$item->Visible = $Security->AllowList(CurrentProjectID() . 'updates_approval');

		// Set up options default
		foreach ($options as &$option) {
			$option->UseDropDownButton = FALSE;
			$option->UseButtonGroup = TRUE;
			$option->ButtonClass = "btn-sm"; // Class for button group
			$item = &$option->Add($option->GroupOptionName);
			$item->Body = "";
			$item->Visible = FALSE;
		}
		$options["addedit"]->DropDownButtonPhrase = $Language->Phrase("ButtonAddEdit");
		$options["detail"]->DropDownButtonPhrase = $Language->Phrase("ButtonDetails");
		$options["action"]->DropDownButtonPhrase = $Language->Phrase("ButtonActions");
	}

	// Render other options
	function RenderOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
			$option = &$options["action"];
			foreach ($this->CustomActions as $action => $name) {

				// Add custom action
				$item = &$option->Add("custom_" . $action);
				$item->Body = "<a class=\"btn btn-warning btn-sm\" href=\"\" onclick=\"ew_SubmitSelected(document.fPensioner_Updateslist, '" . ew_CurrentUrl() . "', null, '" . $action . "');return false;\">" . $name . "</a>";
			}

			// Hide grid edit, multi-delete and multi-update
			if ($this->TotalRecs <= 0) {
				$option = &$options["addedit"];
				$item = &$option->GetItem("gridedit");
				if ($item) $item->Visible = FALSE;
				$option = &$options["action"];
				$item = &$option->GetItem("multidelete");
				if ($item) $item->Visible = FALSE;
				$item = &$option->GetItem("multiupdate");
				if ($item) $item->Visible = FALSE;
			}
	}

	// Process custom action
	function ProcessCustomAction() {
		global $conn, $Language, $Security;
		$sFilter = $this->GetKeyFilter();
		$UserAction = @$_POST["useraction"];
		if ($sFilter <> "" && $UserAction <> "") {
			$this->CurrentFilter = $sFilter;
			$sSql = $this->SQL();
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$rs = $conn->Execute($sSql);
			$conn->raiseErrorFn = '';
			$rsuser = ($rs) ? $rs->GetRows() : array();
			if ($rs)
				$rs->Close();

			// Call row custom action event
			if (count($rsuser) > 0) {
				$conn->BeginTrans();
				foreach ($rsuser as $row) {
					$Processed = $this->Row_CustomAction($UserAction, $row);
					if (!$Processed) break;
				}
				if ($Processed) {
					$conn->CommitTrans(); // Commit the changes
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage(str_replace('%s', $UserAction, $Language->Phrase("CustomActionCompleted"))); // Set up success message
				} else {
					$conn->RollbackTrans(); // Rollback changes

					// Set up error message
					if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

						// Use the message, do nothing
					} elseif ($this->CancelMessage <> "") {
						$this->setFailureMessage($this->CancelMessage);
						$this->CancelMessage = "";
					} else {
						$this->setFailureMessage(str_replace('%s', $UserAction, $Language->Phrase("CustomActionCancelled")));
					}
				}
			}
		}
	}

	function RenderListOptionsExt() {
		global $Security, $Language;
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

	// Load basic search values
	function LoadBasicSearchValues() {
		$this->BasicSearch->Keyword = @$_GET[EW_TABLE_BASIC_SEARCH];
		if ($this->BasicSearch->Keyword <> "") $this->Command = "search";
		$this->BasicSearch->Type = @$_GET[EW_TABLE_BASIC_SEARCH_TYPE];
	}

	//  Load search values for validation
	function LoadSearchValues() {
		global $objForm;

		// Load search values
		// updatesID

		$this->updatesID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_updatesID"]);
		if ($this->updatesID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->updatesID->AdvancedSearch->SearchOperator = @$_GET["z_updatesID"];

		// PensionerID
		$this->PensionerID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_PensionerID"]);
		if ($this->PensionerID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->PensionerID->AdvancedSearch->SearchOperator = @$_GET["z_PensionerID"];

		// status
		$this->status->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_status"]);
		if ($this->status->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->status->AdvancedSearch->SearchOperator = @$_GET["z_status"];

		// field
		$this->_field->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x__field"]);
		if ($this->_field->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->_field->AdvancedSearch->SearchOperator = @$_GET["z__field"];

		// new_value
		$this->new_value->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_new_value"]);
		if ($this->new_value->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->new_value->AdvancedSearch->SearchOperator = @$_GET["z_new_value"];

		// old_value
		$this->old_value->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_old_value"]);
		if ($this->old_value->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->old_value->AdvancedSearch->SearchOperator = @$_GET["z_old_value"];

		// dateUpdated
		$this->dateUpdated->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_dateUpdated"]);
		if ($this->dateUpdated->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->dateUpdated->AdvancedSearch->SearchOperator = @$_GET["z_dateUpdated"];

		// approved
		$this->approved->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_approved"]);
		if ($this->approved->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->approved->AdvancedSearch->SearchOperator = @$_GET["z_approved"];

		// deathDate
		$this->deathDate->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_deathDate"]);
		if ($this->deathDate->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->deathDate->AdvancedSearch->SearchOperator = @$_GET["z_deathDate"];

		// paymentmodeID
		$this->paymentmodeID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_paymentmodeID"]);
		if ($this->paymentmodeID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->paymentmodeID->AdvancedSearch->SearchOperator = @$_GET["z_paymentmodeID"];

		// UpdatedBy
		$this->UpdatedBy->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_UpdatedBy"]);
		if ($this->UpdatedBy->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->UpdatedBy->AdvancedSearch->SearchOperator = @$_GET["z_UpdatedBy"];

		// UpdatedDate
		$this->UpdatedDate->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_UpdatedDate"]);
		if ($this->UpdatedDate->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->UpdatedDate->AdvancedSearch->SearchOperator = @$_GET["z_UpdatedDate"];

		// Createdby
		$this->Createdby->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Createdby"]);
		if ($this->Createdby->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Createdby->AdvancedSearch->SearchOperator = @$_GET["z_Createdby"];

		// CreatedDate
		$this->CreatedDate->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_CreatedDate"]);
		if ($this->CreatedDate->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->CreatedDate->AdvancedSearch->SearchOperator = @$_GET["z_CreatedDate"];

		// Remarks
		$this->Remarks->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Remarks"]);
		if ($this->Remarks->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Remarks->AdvancedSearch->SearchOperator = @$_GET["z_Remarks"];
	}

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
		$this->updatesID->setDbValue($rs->fields('updatesID'));
		$this->PensionerID->setDbValue($rs->fields('PensionerID'));
		$this->status->setDbValue($rs->fields('status'));
		$this->_field->setDbValue($rs->fields('field'));
		$this->new_value->setDbValue($rs->fields('new_value'));
		$this->old_value->setDbValue($rs->fields('old_value'));
		$this->dateUpdated->setDbValue($rs->fields('dateUpdated'));
		$this->approved->setDbValue($rs->fields('approved'));
		$this->deathDate->setDbValue($rs->fields('deathDate'));
		$this->paymentmodeID->setDbValue($rs->fields('paymentmodeID'));
		$this->UpdatedBy->setDbValue($rs->fields('UpdatedBy'));
		$this->UpdatedDate->setDbValue($rs->fields('UpdatedDate'));
		$this->Createdby->setDbValue($rs->fields('Createdby'));
		$this->CreatedDate->setDbValue($rs->fields('CreatedDate'));
		$this->Remarks->setDbValue($rs->fields('Remarks'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->updatesID->DbValue = $row['updatesID'];
		$this->PensionerID->DbValue = $row['PensionerID'];
		$this->status->DbValue = $row['status'];
		$this->_field->DbValue = $row['field'];
		$this->new_value->DbValue = $row['new_value'];
		$this->old_value->DbValue = $row['old_value'];
		$this->dateUpdated->DbValue = $row['dateUpdated'];
		$this->approved->DbValue = $row['approved'];
		$this->deathDate->DbValue = $row['deathDate'];
		$this->paymentmodeID->DbValue = $row['paymentmodeID'];
		$this->UpdatedBy->DbValue = $row['UpdatedBy'];
		$this->UpdatedDate->DbValue = $row['UpdatedDate'];
		$this->Createdby->DbValue = $row['Createdby'];
		$this->CreatedDate->DbValue = $row['CreatedDate'];
		$this->Remarks->DbValue = $row['Remarks'];
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
		$this->ViewUrl = $this->GetViewUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->InlineEditUrl = $this->GetInlineEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->InlineCopyUrl = $this->GetInlineCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();

		// Call Row_Rendering event
		$this->Row_Rendering();

		// Common render codes for all row types
		// updatesID
		// PensionerID
		// status
		// field
		// new_value
		// old_value
		// dateUpdated
		// approved
		// deathDate
		// paymentmodeID
		// UpdatedBy
		// UpdatedDate
		// Createdby
		// CreatedDate
		// Remarks

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// updatesID
			$this->updatesID->ViewValue = $this->updatesID->CurrentValue;
			$this->updatesID->ViewCustomAttributes = "";

			// PensionerID
			if (strval($this->PensionerID->CurrentValue) <> "") {
				$sFilterWrk = "`PensionerID`" . ew_SearchString("=", $this->PensionerID->CurrentValue, EW_DATATYPE_STRING);
			$sSqlWrk = "SELECT `PensionerID`, `PensionerID` AS `DispFld`, `lastname` AS `Disp2Fld`, `firstname` AS `Disp3Fld`, `middlename` AS `Disp4Fld` FROM `tbl_pensioner`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->PensionerID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
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

			// status
			if (strval($this->status->CurrentValue) <> "") {
				$sFilterWrk = "`statusID`" . ew_SearchString("=", $this->status->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `statusID`, `status` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_status`";
			$sWhereWrk = "";
			$lookuptblfilter = "`statusID`!='0' AND `statusID`!='1'";
			if (strval($lookuptblfilter) <> "") {
				ew_AddFilter($sWhereWrk, $lookuptblfilter);
			}
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->status, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->status->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->status->ViewValue = $this->status->CurrentValue;
				}
			} else {
				$this->status->ViewValue = NULL;
			}
			$this->status->ViewCustomAttributes = "";

			// field
			$this->_field->ViewValue = $this->_field->CurrentValue;
			$this->_field->ViewCustomAttributes = "";

			// new_value
			$this->new_value->ViewValue = $this->new_value->CurrentValue;
			$this->new_value->ViewCustomAttributes = "";

			// old_value
			$this->old_value->ViewValue = $this->old_value->CurrentValue;
			$this->old_value->ViewCustomAttributes = "";

			// dateUpdated
			$this->dateUpdated->ViewValue = $this->dateUpdated->CurrentValue;
			$this->dateUpdated->ViewValue = ew_FormatDateTime($this->dateUpdated->ViewValue, 5);
			$this->dateUpdated->ViewCustomAttributes = "";

			// approved
			$this->approved->ViewValue = $this->approved->CurrentValue;
			$this->approved->ViewCustomAttributes = "";

			// deathDate
			$this->deathDate->ViewValue = $this->deathDate->CurrentValue;
			$this->deathDate->ViewValue = ew_FormatDateTime($this->deathDate->ViewValue, 5);
			$this->deathDate->ViewCustomAttributes = "";

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

			// UpdatedBy
			$this->UpdatedBy->ViewValue = $this->UpdatedBy->CurrentValue;
			$this->UpdatedBy->ViewCustomAttributes = "";

			// UpdatedDate
			$this->UpdatedDate->ViewValue = $this->UpdatedDate->CurrentValue;
			$this->UpdatedDate->ViewValue = ew_FormatDateTime($this->UpdatedDate->ViewValue, 5);
			$this->UpdatedDate->ViewCustomAttributes = "";

			// Createdby
			if (strval($this->Createdby->CurrentValue) <> "") {
				$sFilterWrk = "`uid`" . ew_SearchString("=", $this->Createdby->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `uid`, `firstname` AS `DispFld`, `surname` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `tbl_user`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Createdby, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `firstname` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->Createdby->ViewValue = $rswrk->fields('DispFld');
					$this->Createdby->ViewValue .= ew_ValueSeparator(1,$this->Createdby) . $rswrk->fields('Disp2Fld');
					$rswrk->Close();
				} else {
					$this->Createdby->ViewValue = $this->Createdby->CurrentValue;
				}
			} else {
				$this->Createdby->ViewValue = NULL;
			}
			$this->Createdby->ViewCustomAttributes = "";

			// CreatedDate
			$this->CreatedDate->ViewValue = $this->CreatedDate->CurrentValue;
			$this->CreatedDate->ViewValue = ew_FormatDateTime($this->CreatedDate->ViewValue, 5);
			$this->CreatedDate->ViewCustomAttributes = "";

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

			// dateUpdated
			$this->dateUpdated->LinkCustomAttributes = "";
			$this->dateUpdated->HrefValue = "";
			$this->dateUpdated->TooltipValue = "";

			// approved
			if (strval($this->approved->CurrentValue) <> "") {
				switch ($this->approved->CurrentValue) {
					case $this->approved->FldTagValue(1):
						$this->approved->ViewValue = $this->approved->FldTagCaption(1) <> "" ? $this->approved->FldTagCaption(1) : $this->approved->CurrentValue;
						break;
					case $this->approved->FldTagValue(2):
						$this->approved->ViewValue = $this->approved->FldTagCaption(2) <> "" ? $this->approved->FldTagCaption(2) : $this->approved->CurrentValue;
						break;
					case $this->approved->FldTagValue(3):
						$this->approved->ViewValue = $this->approved->FldTagCaption(3) <> "" ? $this->approved->FldTagCaption(3) : $this->approved->CurrentValue;
						break;
					default:
						$this->approved->ViewValue = $this->approved->CurrentValue;
				}
			} else {
				$this->approved->ViewValue = NULL;
			}
			$this->approved->ViewCustomAttributes = "";

			// Createdby
			$this->Createdby->ViewValue = $this->Createdby->CurrentValue;
			$this->Createdby->ViewCustomAttributes = "";

			// Createdby
			$this->Createdby->LinkCustomAttributes = "";
			$this->Createdby->HrefValue = "";
			$this->Createdby->TooltipValue = "";

			// CreatedDate
			$this->CreatedDate->LinkCustomAttributes = "";
			$this->CreatedDate->HrefValue = "";
			$this->CreatedDate->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row
			// PensionerID
			$this->PensionerID->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `PensionerID`, `PensionerID` AS `DispFld`, `lastname` AS `Disp2Fld`, `firstname` AS `Disp3Fld`, `middlename` AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `tbl_pensioner`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->PensionerID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->PensionerID->EditValue = $arwrk;

			// field
			$this->_field->EditCustomAttributes = "";
			$this->_field->EditValue = ew_HtmlEncode($this->_field->AdvancedSearch->SearchValue);
			$this->_field->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->_field->FldCaption()));

			// new_value
			$this->new_value->EditCustomAttributes = "";
			$this->new_value->EditValue = $this->new_value->AdvancedSearch->SearchValue;
			$this->new_value->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->new_value->FldCaption()));

			// old_value
			$this->old_value->EditCustomAttributes = "";
			$this->old_value->EditValue = $this->old_value->AdvancedSearch->SearchValue;
			$this->old_value->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->old_value->FldCaption()));

			// dateUpdated
			$this->dateUpdated->EditCustomAttributes = "";
			$this->dateUpdated->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->dateUpdated->AdvancedSearch->SearchValue, 5), 5));
			$this->dateUpdated->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->dateUpdated->FldCaption()));

			// approved
			$this->approved->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->approved->FldTagValue(1), $this->approved->FldTagCaption(1) <> "" ? $this->approved->FldTagCaption(1) : $this->approved->FldTagValue(1));
			$arwrk[] = array($this->approved->FldTagValue(2), $this->approved->FldTagCaption(2) <> "" ? $this->approved->FldTagCaption(2) : $this->approved->FldTagValue(2));
			$arwrk[] = array($this->approved->FldTagValue(3), $this->approved->FldTagCaption(3) <> "" ? $this->approved->FldTagCaption(3) : $this->approved->FldTagValue(3));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->approved->EditValue = $arwrk;

			// Createdby
			$this->Createdby->EditCustomAttributes = "";
			$this->Createdby->EditValue = ew_HtmlEncode($this->Createdby->AdvancedSearch->SearchValue);
			$this->Createdby->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->Createdby->FldCaption()));

			// CreatedDate
			$this->CreatedDate->EditCustomAttributes = "";
			$this->CreatedDate->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->CreatedDate->AdvancedSearch->SearchValue, 5), 5));
			$this->CreatedDate->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->CreatedDate->FldCaption()));
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

	// Validate search
	function ValidateSearch() {
		global $gsSearchError;

		// Initialize
		$gsSearchError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return TRUE;

		// Return validate result
		$ValidateSearch = ($gsSearchError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateSearch = $ValidateSearch && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsSearchError, $sFormCustomError);
		}
		return $ValidateSearch;
	}

	// Load advanced search
	function LoadAdvancedSearch() {
		$this->updatesID->AdvancedSearch->Load();
		$this->PensionerID->AdvancedSearch->Load();
		$this->status->AdvancedSearch->Load();
		$this->_field->AdvancedSearch->Load();
		$this->new_value->AdvancedSearch->Load();
		$this->old_value->AdvancedSearch->Load();
		$this->dateUpdated->AdvancedSearch->Load();
		$this->approved->AdvancedSearch->Load();
		$this->deathDate->AdvancedSearch->Load();
		$this->paymentmodeID->AdvancedSearch->Load();
		$this->UpdatedBy->AdvancedSearch->Load();
		$this->UpdatedDate->AdvancedSearch->Load();
		$this->Createdby->AdvancedSearch->Load();
		$this->CreatedDate->AdvancedSearch->Load();
		$this->Remarks->AdvancedSearch->Load();
	}

	// Set up export options
	function SetupExportOptions() {
		global $Language;

		// Printer friendly
		$item = &$this->ExportOptions->Add("print");
		$item->Body = "<a href=\"" . $this->ExportPrintUrl . "\" class=\"ewExportLink ewPrint\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("PrinterFriendlyText")) . "\">" . $Language->Phrase("PrinterFriendly") . "</a>";
		$item->Visible = TRUE;

		// Export to Excel
		$item = &$this->ExportOptions->Add("excel");
		$item->Body = "<a href=\"" . $this->ExportExcelUrl . "\" class=\"ewExportLink ewExcel\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToExcelText")) . "\">" . $Language->Phrase("ExportToExcel") . "</a>";
		$item->Visible = TRUE;

		// Export to Word
		$item = &$this->ExportOptions->Add("word");
		$item->Body = "<a href=\"" . $this->ExportWordUrl . "\" class=\"ewExportLink ewWord\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToWordText")) . "\">" . $Language->Phrase("ExportToWord") . "</a>";
		$item->Visible = TRUE;

		// Export to Html
		$item = &$this->ExportOptions->Add("html");
		$item->Body = "<a href=\"" . $this->ExportHtmlUrl . "\" class=\"ewExportLink ewHtml\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToHtmlText")) . "\">" . $Language->Phrase("ExportToHtml") . "</a>";
		$item->Visible = TRUE;

		// Export to Xml
		$item = &$this->ExportOptions->Add("xml");
		$item->Body = "<a href=\"" . $this->ExportXmlUrl . "\" class=\"ewExportLink ewXml\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToXmlText")) . "\">" . $Language->Phrase("ExportToXml") . "</a>";
		$item->Visible = TRUE;

		// Export to Csv
		$item = &$this->ExportOptions->Add("csv");
		$item->Body = "<a href=\"" . $this->ExportCsvUrl . "\" class=\"ewExportLink ewCsv\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToCsvText")) . "\">" . $Language->Phrase("ExportToCsv") . "</a>";
		$item->Visible = TRUE;

		// Export to Pdf
		$item = &$this->ExportOptions->Add("pdf");
		$item->Body = "<a href=\"" . $this->ExportPdfUrl . "\" class=\"ewExportLink ewPdf\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToPDFText")) . "\">" . $Language->Phrase("ExportToPDF") . "</a>";
		$item->Visible = FALSE;

		// Export to Email
		$item = &$this->ExportOptions->Add("email");
		$item->Body = "<a id=\"emf_Pensioner_Updates\" href=\"javascript:void(0);\" class=\"ewExportLink ewEmail\" data-caption=\"" . $Language->Phrase("ExportToEmailText") . "\" onclick=\"ew_EmailDialogShow({lnk:'emf_Pensioner_Updates',hdr:ewLanguage.Phrase('ExportToEmail'),f:document.fPensioner_Updateslist,sel:false});\">" . $Language->Phrase("ExportToEmail") . "</a>";
		$item->Visible = TRUE;

		// Drop down button for export
		$this->ExportOptions->UseDropDownButton = FALSE;
		$this->ExportOptions->DropDownButtonPhrase = $Language->Phrase("ButtonExport");

		// Add group option item
		$item = &$this->ExportOptions->Add($this->ExportOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;
	}

	// Export data in HTML/CSV/Word/Excel/XML/Email/PDF format
	function ExportData() {
		$utf8 = (strtolower(EW_CHARSET) == "utf-8");
		$bSelectLimit = EW_SELECT_LIMIT;

		// Load recordset
		if ($bSelectLimit) {
			$this->TotalRecs = $this->SelectRecordCount();
		} else {
			if ($rs = $this->LoadRecordset())
				$this->TotalRecs = $rs->RecordCount();
		}
		$this->StartRec = 1;

		// Export all
		if ($this->ExportAll) {
			set_time_limit(EW_EXPORT_ALL_TIME_LIMIT);
			$this->DisplayRecs = $this->TotalRecs;
			$this->StopRec = $this->TotalRecs;
		} else { // Export one page only
			$this->SetUpStartRec(); // Set up start record position

			// Set the last record to display
			if ($this->DisplayRecs <= 0) {
				$this->StopRec = $this->TotalRecs;
			} else {
				$this->StopRec = $this->StartRec + $this->DisplayRecs - 1;
			}
		}
		if ($bSelectLimit)
			$rs = $this->LoadRecordset($this->StartRec-1, $this->DisplayRecs <= 0 ? $this->TotalRecs : $this->DisplayRecs);
		if (!$rs) {
			header("Content-Type:"); // Remove header
			header("Content-Disposition:");
			$this->ShowMessage();
			return;
		}
		$ExportDoc = ew_ExportDocument($this, "h");
		$ParentTable = "";
		if ($bSelectLimit) {
			$StartRec = 1;
			$StopRec = $this->DisplayRecs <= 0 ? $this->TotalRecs : $this->DisplayRecs;
		} else {
			$StartRec = $this->StartRec;
			$StopRec = $this->StopRec;
		}
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		$ExportDoc->Text .= $sHeader;
		$this->ExportDocument($ExportDoc, $rs, $StartRec, $StopRec, "");
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		$ExportDoc->Text .= $sFooter;

		// Close recordset
		$rs->Close();

		// Export header and footer
		$ExportDoc->ExportHeaderAndFooter();

		// Clean output buffer
		if (!EW_DEBUG_ENABLED && ob_get_length())
			ob_end_clean();

		// Write debug message if enabled
		if (EW_DEBUG_ENABLED)
			echo ew_DebugMsg();

		// Output data
		if ($this->Export == "email") {
			echo $this->ExportEmail($ExportDoc->Text);
		} else {
			$ExportDoc->Export();
		}
	}

	// Export email
	function ExportEmail($EmailContent) {
		global $gTmpImages, $Language;
		$sSender = @$_GET["sender"];
		$sRecipient = @$_GET["recipient"];
		$sCc = @$_GET["cc"];
		$sBcc = @$_GET["bcc"];
		$sContentType = @$_GET["contenttype"];

		// Subject
		$sSubject = ew_StripSlashes(@$_GET["subject"]);
		$sEmailSubject = $sSubject;

		// Message
		$sContent = ew_StripSlashes(@$_GET["message"]);
		$sEmailMessage = $sContent;

		// Check sender
		if ($sSender == "") {
			return "<p class=\"text-error\">" . $Language->Phrase("EnterSenderEmail") . "</p>";
		}
		if (!ew_CheckEmail($sSender)) {
			return "<p class=\"text-error\">" . $Language->Phrase("EnterProperSenderEmail") . "</p>";
		}

		// Check recipient
		if (!ew_CheckEmailList($sRecipient, EW_MAX_EMAIL_RECIPIENT)) {
			return "<p class=\"text-error\">" . $Language->Phrase("EnterProperRecipientEmail") . "</p>";
		}

		// Check cc
		if (!ew_CheckEmailList($sCc, EW_MAX_EMAIL_RECIPIENT)) {
			return "<p class=\"text-error\">" . $Language->Phrase("EnterProperCcEmail") . "</p>";
		}

		// Check bcc
		if (!ew_CheckEmailList($sBcc, EW_MAX_EMAIL_RECIPIENT)) {
			return "<p class=\"text-error\">" . $Language->Phrase("EnterProperBccEmail") . "</p>";
		}

		// Check email sent count
		if (!isset($_SESSION[EW_EXPORT_EMAIL_COUNTER]))
			$_SESSION[EW_EXPORT_EMAIL_COUNTER] = 0;
		if (intval($_SESSION[EW_EXPORT_EMAIL_COUNTER]) > EW_MAX_EMAIL_SENT_COUNT) {
			return "<p class=\"text-error\">" . $Language->Phrase("ExceedMaxEmailExport") . "</p>";
		}

		// Send email
		$Email = new cEmail();
		$Email->Sender = $sSender; // Sender
		$Email->Recipient = $sRecipient; // Recipient
		$Email->Cc = $sCc; // Cc
		$Email->Bcc = $sBcc; // Bcc
		$Email->Subject = $sEmailSubject; // Subject
		$Email->Format = ($sContentType == "url") ? "text" : "html";
		$Email->Charset = EW_EMAIL_CHARSET;
		if ($sEmailMessage <> "") {
			$sEmailMessage = ew_RemoveXSS($sEmailMessage);
			$sEmailMessage .= ($sContentType == "url") ? "\r\n\r\n" : "<br><br>";
		}
		if ($sContentType == "url") {
			$sUrl = ew_ConvertFullUrl(ew_CurrentPage() . "?" . $this->ExportQueryString());
			$sEmailMessage .= $sUrl; // Send URL only
		} else {
			foreach ($gTmpImages as $tmpimage)
				$Email->AddEmbeddedImage($tmpimage);
			$sEmailMessage .= $EmailContent; // Send HTML
		}
		$Email->Content = $sEmailMessage; // Content
		$EventArgs = array();
		$bEmailSent = FALSE;
		if ($this->Email_Sending($Email, $EventArgs))
			$bEmailSent = $Email->Send();

		// Check email sent status
		if ($bEmailSent) {

			// Update email sent count
			$_SESSION[EW_EXPORT_EMAIL_COUNTER]++;

			// Sent email success
			return "<p class=\"text-success\">" . $Language->Phrase("SendEmailSuccess") . "</p>"; // Set up success message
		} else {

			// Sent email failure
			return "<p class=\"text-error\">" . $Email->SendErrDescription . "</p>";
		}
	}

	// Export QueryString
	function ExportQueryString() {

		// Initialize
		$sQry = "export=html";

		// Build QueryString for search
		if ($this->BasicSearch->getKeyword() <> "") {
			$sQry .= "&" . EW_TABLE_BASIC_SEARCH . "=" . urlencode($this->BasicSearch->getKeyword()) . "&" . EW_TABLE_BASIC_SEARCH_TYPE . "=" . urlencode($this->BasicSearch->getType());
		}
		$this->AddSearchQueryString($sQry, $this->updatesID); // updatesID
		$this->AddSearchQueryString($sQry, $this->PensionerID); // PensionerID
		$this->AddSearchQueryString($sQry, $this->status); // status
		$this->AddSearchQueryString($sQry, $this->_field); // field
		$this->AddSearchQueryString($sQry, $this->new_value); // new_value
		$this->AddSearchQueryString($sQry, $this->old_value); // old_value
		$this->AddSearchQueryString($sQry, $this->dateUpdated); // dateUpdated
		$this->AddSearchQueryString($sQry, $this->approved); // approved
		$this->AddSearchQueryString($sQry, $this->deathDate); // deathDate
		$this->AddSearchQueryString($sQry, $this->paymentmodeID); // paymentmodeID
		$this->AddSearchQueryString($sQry, $this->UpdatedBy); // UpdatedBy
		$this->AddSearchQueryString($sQry, $this->UpdatedDate); // UpdatedDate
		$this->AddSearchQueryString($sQry, $this->Createdby); // Createdby
		$this->AddSearchQueryString($sQry, $this->CreatedDate); // CreatedDate
		$this->AddSearchQueryString($sQry, $this->Remarks); // Remarks

		// Build QueryString for pager
		$sQry .= "&" . EW_TABLE_REC_PER_PAGE . "=" . urlencode($this->getRecordsPerPage()) . "&" . EW_TABLE_START_REC . "=" . urlencode($this->getStartRecordNumber());
		return $sQry;
	}

	// Add search QueryString
	function AddSearchQueryString(&$Qry, &$Fld) {
		$FldSearchValue = $Fld->AdvancedSearch->getValue("x");
		$FldParm = substr($Fld->FldVar,2);
		if (strval($FldSearchValue) <> "") {
			$Qry .= "&x_" . $FldParm . "=" . urlencode($FldSearchValue) .
				"&z_" . $FldParm . "=" . urlencode($Fld->AdvancedSearch->getValue("z"));
		}
		$FldSearchValue2 = $Fld->AdvancedSearch->getValue("y");
		if (strval($FldSearchValue2) <> "") {
			$Qry .= "&v_" . $FldParm . "=" . urlencode($Fld->AdvancedSearch->getValue("v")) .
				"&y_" . $FldParm . "=" . urlencode($FldSearchValue2) .
				"&w_" . $FldParm . "=" . urlencode($Fld->AdvancedSearch->getValue("w"));
		}
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$PageCaption = $this->TableCaption();
		$url = ew_CurrentUrl();
		$url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", $url, $this->TableVar);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'Pensioner Updates';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
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

	// ListOptions Load event
	function ListOptions_Load() {

		// Example:
		//$opt = &$this->ListOptions->Add("new");
		//$opt->Header = "xxx";
		//$opt->OnLeft = TRUE; // Link on left
		//$opt->MoveTo(0); // Move to first column

	}

	// ListOptions Rendered event
	function ListOptions_Rendered() {

		// Example: 
		//$this->ListOptions->Items["new"]->Body = "xxx";

	}

	// Row Custom Action event
	function Row_CustomAction($action, $row) {

		// Return FALSE to abort
		return TRUE;
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($Pensioner_Updates_list)) $Pensioner_Updates_list = new cPensioner_Updates_list();

// Page init
$Pensioner_Updates_list->Page_Init();

// Page main
$Pensioner_Updates_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$Pensioner_Updates_list->Page_Render();
?>
<?php include_once "header.php" ?>
<?php if ($Pensioner_Updates->Export == "") { ?>
<script type="text/javascript">

// Page object
var Pensioner_Updates_list = new ew_Page("Pensioner_Updates_list");
Pensioner_Updates_list.PageID = "list"; // Page ID
var EW_PAGE_ID = Pensioner_Updates_list.PageID; // For backward compatibility

// Form object
var fPensioner_Updateslist = new ew_Form("fPensioner_Updateslist");
fPensioner_Updateslist.FormKeyCountName = '<?php echo $Pensioner_Updates_list->FormKeyCountName ?>';

// Form_CustomValidate event
fPensioner_Updateslist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fPensioner_Updateslist.ValidateRequired = true;
<?php } else { ?>
fPensioner_Updateslist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

var fPensioner_Updateslistsrch = new ew_Form("fPensioner_Updateslistsrch");

// Validate function for search
fPensioner_Updateslistsrch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	this.PostAutoSuggest();
	var infix = "";

	// Set up row object
	ew_ElementsToRow(fobj);

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}

// Form_CustomValidate event
fPensioner_Updateslistsrch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fPensioner_Updateslistsrch.ValidateRequired = true; // Use JavaScript validation
<?php } else { ?>
fPensioner_Updateslistsrch.ValidateRequired = false; // No JavaScript validation
<?php } ?>

// Dynamic selection lists
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php if ($Pensioner_Updates->Export == "") { ?>
<?php //$Breadcrumb->Render(); ?>
<?php } ?>
<?php if ($Pensioner_Updates_list->ExportOptions->Visible()) { ?>
	<!-- Tolits expot removed original script Export -->
		<div class="row">
			<div class="col-xs-12">
				<p>
					<a class="btn btn-sm" href="<?php echo ew_CurrentPage();?>?export=pdf">
						<i class="icon-file-text align-top bigger-125"></i>
							PDF
					</a>		
					<a class="btn btn-primary btn-sm" href="<?php echo ew_CurrentPage();?>?export=csv">
						<i class="icon-table align-top bigger-125"></i>
							CSV
					</a>
					<a class="btn btn-info btn-sm" href="<?php echo ew_CurrentPage();?>?export=excel">
						<i class="icon-list align-top bigger-125"></i>
							Excel
					</a>
					<a class="btn btn-success btn-sm" href="<?php echo ew_CurrentPage();?>?export=print">
						<i class="icon-print align-top bigger-125"></i>
							Print
					</a>
					<a class="btn btn-warning btn-sm" href="<?php echo ew_CurrentPage();?>?export=html">
						<i class="icon-th-list align-top bigger-125"></i>
							HTML
					</a>
				</p>
			</div>
		</div>	
	<!-- End -->
<?php } ?>
<?php
	$bSelectLimit = EW_SELECT_LIMIT;
	if ($bSelectLimit) {
		$Pensioner_Updates_list->TotalRecs = $Pensioner_Updates->SelectRecordCount();
	} else {
		if ($Pensioner_Updates_list->Recordset = $Pensioner_Updates_list->LoadRecordset())
			$Pensioner_Updates_list->TotalRecs = $Pensioner_Updates_list->Recordset->RecordCount();
	}
	$Pensioner_Updates_list->StartRec = 1;
	if ($Pensioner_Updates_list->DisplayRecs <= 0 || ($Pensioner_Updates->Export <> "" && $Pensioner_Updates->ExportAll)) // Display all records
		$Pensioner_Updates_list->DisplayRecs = $Pensioner_Updates_list->TotalRecs;
	if (!($Pensioner_Updates->Export <> "" && $Pensioner_Updates->ExportAll))
		$Pensioner_Updates_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$Pensioner_Updates_list->Recordset = $Pensioner_Updates_list->LoadRecordset($Pensioner_Updates_list->StartRec-1, $Pensioner_Updates_list->DisplayRecs);
$Pensioner_Updates_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($Pensioner_Updates->Export == "" && $Pensioner_Updates->CurrentAction == "") { ?>
<form name="fPensioner_Updateslistsrch" id="fPensioner_Updateslistsrch" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>">
<!--<table class="ewSearchTable"><tr><td>-->
<div class="row">
	<div class="col-xs-12">
		<div class="widget-box collapsed">
			<div class="widget-header header-color-pink">
				<h5 class="lighter">Advanced Search</h5>
				<div class="widget-toolbar">
					<a href="#" data-action="collapse">
					<i class="1 icon-chevron-down bigger-125"></i>
					</a>
					<a href="#" data-action="reload">
					<i class="1 icon-refresh bigger-125"></i>
					</a>
				</div>
			</div>
		<div class="widget-body">
	<div class="widget-main">
	<div id="fPensioner_Updateslistsrch_SearchPanel">
		<input type="hidden" name="cmd" value="search">
		<input type="hidden" name="t" value="Pensioner_Updates">
		<div class="ewBasicSearch">
<?php
if ($gsSearchError == "")
	$Pensioner_Updates_list->LoadAdvancedSearch(); // Load advanced search

// Render for search
$Pensioner_Updates->RowType = EW_ROWTYPE_SEARCH;

// Render row
$Pensioner_Updates->ResetAttrs();
$Pensioner_Updates_list->RenderRow();
?>
<div id="xsr_1" class="ewRow">
<?php if ($Pensioner_Updates->PensionerID->Visible) { // PensionerID ?>
	<span id="xsc_PensionerID" class="ewCell">
		<span class="ewSearchCaption"><?php echo $Pensioner_Updates->PensionerID->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_PensionerID" id="z_PensionerID" value="="></span>
		<span class="control-group ewSearchField">
<select data-field="x_PensionerID" id="x_PensionerID" name="x_PensionerID"<?php echo $Pensioner_Updates->PensionerID->EditAttributes() ?>>
<?php
if (is_array($Pensioner_Updates->PensionerID->EditValue)) {
	$arwrk = $Pensioner_Updates->PensionerID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($Pensioner_Updates->PensionerID->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
<?php if ($arwrk[$rowcntwrk][2] <> "") { ?>
<?php echo ew_ValueSeparator(1,$Pensioner_Updates->PensionerID) ?><?php echo $arwrk[$rowcntwrk][2] ?>
<?php } ?>
<?php if ($arwrk[$rowcntwrk][3] <> "") { ?>
<?php echo ew_ValueSeparator(2,$Pensioner_Updates->PensionerID) ?><?php echo $arwrk[$rowcntwrk][3] ?>
<?php } ?>
<?php if ($arwrk[$rowcntwrk][4] <> "") { ?>
<?php echo ew_ValueSeparator(3,$Pensioner_Updates->PensionerID) ?><?php echo $arwrk[$rowcntwrk][4] ?>
<?php } ?>
</option>
<?php
	}
}
?>
</select>
<script type="text/javascript">
fPensioner_Updateslistsrch.Lists["x_PensionerID"].Options = <?php echo (is_array($Pensioner_Updates->PensionerID->EditValue)) ? ew_ArrayToJson($Pensioner_Updates->PensionerID->EditValue, 1) : "[]" ?>;
</script>
</span>
	</span>
<?php } ?>
</div>
<div id="xsr_2" class="ewRow">
<?php if ($Pensioner_Updates->approved->Visible) { // approved ?>
	<span id="xsc_approved" class="ewCell">
		<span class="ewSearchCaption"><?php echo $Pensioner_Updates->approved->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_approved" id="z_approved" value="="></span>
		<span class="control-group ewSearchField">
<select data-field="x_approved" id="x_approved" name="x_approved"<?php echo $Pensioner_Updates->approved->EditAttributes() ?>>
<?php
if (is_array($Pensioner_Updates->approved->EditValue)) {
	$arwrk = $Pensioner_Updates->approved->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($Pensioner_Updates->approved->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
	</span>
<?php } ?>
</div>
<div id="xsr_3" class="row">
	<div class="col-xs-12 col-sm-4">
	<div class="input-group">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="form-control search-query" value="<?php echo ew_HtmlEncode($Pensioner_Updates_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo $Language->Phrase("Search") ?>">
	<span class="input-group-btn">
	<button class="btn btn-purple btn-sm" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $Language->Phrase("QuickSearchBtn") ?> <i class="icon-search icon-on-right bigger-110"></i></button>&nbsp;
	<a type="button" class="btn btn-success btn-sm" href="<?php echo $Pensioner_Updates_list->PageUrl() ?>cmd=reset">ShowAll <i class="icon-refresh icon-on-right bigger-110"></i></a>
	</span>
	</div>
	</div>
	<div class="btn-group ewButtonGroup">
	<!--<a class="btn ewShowAll" href="<?php echo $Pensioner_Updates_list->PageUrl() ?>cmd=reset"><?php echo $Language->Phrase("ShowAll") ?></a> -->
</div>
<div id="xsr_4" class="radio">
	<label><input class="ace" type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="="<?php if ($Pensioner_Updates_list->BasicSearch->getType() == "=") { ?> checked="checked"<?php } ?>><span class="lbl"><?php echo $Language->Phrase("ExactPhrase") ?></span></label>
	<label><input class="ace" type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="AND"<?php if ($Pensioner_Updates_list->BasicSearch->getType() == "AND") { ?> checked="checked"<?php } ?>><span class="lbl"><?php echo $Language->Phrase("AllWord") ?></span></label>
	<label><input class="ace" type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="OR"<?php if ($Pensioner_Updates_list->BasicSearch->getType() == "OR") { ?> checked="checked"<?php } ?>><span class="lbl"><?php echo $Language->Phrase("AnyWord") ?></span></label>
</div>
</div>
</div>
			</div>
		</div>
	</div>
</div>
<!--</td></tr></table>-->
</form>
<?php } ?>
<?php } ?>
<?php $Pensioner_Updates_list->ShowPageHeader(); ?>
<?php
$Pensioner_Updates_list->ShowMessage();
?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<?php if ($Pensioner_Updates->Export == "") { ?>
<div class="ewGridUpperPanel">
<?php if ($Pensioner_Updates->CurrentAction <> "gridadd" && $Pensioner_Updates->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($Pensioner_Updates_list->Pager)) $Pensioner_Updates_list->Pager = new cNumericPager($Pensioner_Updates_list->StartRec, $Pensioner_Updates_list->DisplayRecs, $Pensioner_Updates_list->TotalRecs, $Pensioner_Updates_list->RecRange) ?>
<?php if ($Pensioner_Updates_list->Pager->RecordCount > 0) { ?>
<table cellspacing="0" class="ewStdTable"><tbody><tr><td>
<div class="pagination"><ul>
	<?php if ($Pensioner_Updates_list->Pager->FirstButton->Enabled) { ?>
	<li><a href="<?php echo $Pensioner_Updates_list->PageUrl() ?>start=<?php echo $Pensioner_Updates_list->Pager->FirstButton->Start ?>"><?php echo $Language->Phrase("PagerFirst") ?></a></li>
	<?php } ?>
	<?php if ($Pensioner_Updates_list->Pager->PrevButton->Enabled) { ?>
	<li><a href="<?php echo $Pensioner_Updates_list->PageUrl() ?>start=<?php echo $Pensioner_Updates_list->Pager->PrevButton->Start ?>"><?php echo $Language->Phrase("PagerPrevious") ?></a></li>
	<?php } ?>
	<?php foreach ($Pensioner_Updates_list->Pager->Items as $PagerItem) { ?>
		<li<?php if (!$PagerItem->Enabled) { echo " class=\" active\""; } ?>><a href="<?php if ($PagerItem->Enabled) { echo $Pensioner_Updates_list->PageUrl() . "start=" . $PagerItem->Start; } else { echo "#"; } ?>"><?php echo $PagerItem->Text ?></a></li>
	<?php } ?>
	<?php if ($Pensioner_Updates_list->Pager->NextButton->Enabled) { ?>
	<li><a href="<?php echo $Pensioner_Updates_list->PageUrl() ?>start=<?php echo $Pensioner_Updates_list->Pager->NextButton->Start ?>"><?php echo $Language->Phrase("PagerNext") ?></a></li>
	<?php } ?>
	<?php if ($Pensioner_Updates_list->Pager->LastButton->Enabled) { ?>
	<li><a href="<?php echo $Pensioner_Updates_list->PageUrl() ?>start=<?php echo $Pensioner_Updates_list->Pager->LastButton->Start ?>"><?php echo $Language->Phrase("PagerLast") ?></a></li>
	<?php } ?>
</ul></div>
</td>
<td>
	<?php if ($Pensioner_Updates_list->Pager->ButtonCount > 0) { ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php } ?>
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $Pensioner_Updates_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $Pensioner_Updates_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $Pensioner_Updates_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($Pensioner_Updates_list->SearchWhere == "0=101") { ?>
	<p><?php echo $Language->Phrase("EnterSearchCriteria") ?></p>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoRecord") ?></p>
	<?php } ?>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoPermission") ?></p>
	<?php } ?>
<?php } ?>
</td>
<?php if ($Pensioner_Updates_list->TotalRecs > 0) { ?>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
<input type="hidden" name="t" value="Pensioner_Updates">
<select name="<?php echo EW_TABLE_REC_PER_PAGE ?>" class="input-small" onchange="this.form.submit();">
<option value="20"<?php if ($Pensioner_Updates_list->DisplayRecs == 20) { ?> selected="selected"<?php } ?>>20</option>
<option value="50"<?php if ($Pensioner_Updates_list->DisplayRecs == 50) { ?> selected="selected"<?php } ?>>50</option>
<option value="70"<?php if ($Pensioner_Updates_list->DisplayRecs == 70) { ?> selected="selected"<?php } ?>>70</option>
<option value="ALL"<?php if ($Pensioner_Updates->getRecordsPerPage() == -1) { ?> selected="selected"<?php } ?>><?php echo $Language->Phrase("AllRecords") ?></option>
</select>
</td>
<?php } ?>
</tr></table>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($Pensioner_Updates_list->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
</div>
<?php } ?>
<form name="fPensioner_Updateslist" id="fPensioner_Updateslist" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="Pensioner_Updates">
<div id="gmp_Pensioner_Updates" class="ewGridMiddlePanel">
<?php if ($Pensioner_Updates_list->TotalRecs > 0) { ?>
<table id="tbl_Pensioner_Updateslist" class="ewTable ewTableSeparate">
<?php echo $Pensioner_Updates->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$Pensioner_Updates_list->RenderListOptions();

// Render list options (header, left)
$Pensioner_Updates_list->ListOptions->Render("header", "left");
?>
<?php if ($Pensioner_Updates->PensionerID->Visible) { // PensionerID ?>
	<?php if ($Pensioner_Updates->SortUrl($Pensioner_Updates->PensionerID) == "") { ?>
		<td><div id="elh_Pensioner_Updates_PensionerID" class="Pensioner_Updates_PensionerID"><div class="ewTableHeaderCaption"><?php echo $Pensioner_Updates->PensionerID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_Pensioner_Updates_PensionerID" class="Pensioner_Updates_PensionerID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $Pensioner_Updates->PensionerID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($Pensioner_Updates->PensionerID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Pensioner_Updates->PensionerID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>	
<?php if ($Pensioner_Updates->_field->Visible) { // field ?>
	<?php if ($Pensioner_Updates->SortUrl($Pensioner_Updates->_field) == "") { ?>
		<td><div id="elh_Pensioner_Updates__field" class="Pensioner_Updates__field"><div class="ewTableHeaderCaption"><?php echo $Pensioner_Updates->_field->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_Pensioner_Updates__field" class="Pensioner_Updates__field">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $Pensioner_Updates->_field->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($Pensioner_Updates->_field->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Pensioner_Updates->_field->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($Pensioner_Updates->new_value->Visible) { // new_value ?>
	<?php if ($Pensioner_Updates->SortUrl($Pensioner_Updates->new_value) == "") { ?>
		<td><div id="elh_Pensioner_Updates_new_value" class="Pensioner_Updates_new_value"><div class="ewTableHeaderCaption"><?php echo $Pensioner_Updates->new_value->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_Pensioner_Updates_new_value" class="Pensioner_Updates_new_value">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $Pensioner_Updates->new_value->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($Pensioner_Updates->new_value->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Pensioner_Updates->new_value->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($Pensioner_Updates->old_value->Visible) { // old_value ?>
	<?php if ($Pensioner_Updates->SortUrl($Pensioner_Updates->old_value) == "") { ?>
		<td><div id="elh_Pensioner_Updates_old_value" class="Pensioner_Updates_old_value"><div class="ewTableHeaderCaption"><?php echo $Pensioner_Updates->old_value->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_Pensioner_Updates_old_value" class="Pensioner_Updates_old_value">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $Pensioner_Updates->old_value->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($Pensioner_Updates->old_value->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Pensioner_Updates->old_value->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($Pensioner_Updates->dateUpdated->Visible) { // dateUpdated ?>
	<?php if ($Pensioner_Updates->SortUrl($Pensioner_Updates->dateUpdated) == "") { ?>
		<td><div id="elh_Pensioner_Updates_dateUpdated" class="Pensioner_Updates_dateUpdated"><div class="ewTableHeaderCaption"><?php echo $Pensioner_Updates->dateUpdated->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_Pensioner_Updates_dateUpdated" class="Pensioner_Updates_dateUpdated">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $Pensioner_Updates->dateUpdated->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($Pensioner_Updates->dateUpdated->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Pensioner_Updates->dateUpdated->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($Pensioner_Updates->approved->Visible) { // approved ?>
	<?php if ($Pensioner_Updates->SortUrl($Pensioner_Updates->approved) == "") { ?>
		<td><div id="elh_Pensioner_Updates_approved" class="Pensioner_Updates_approved"><div class="ewTableHeaderCaption"><?php echo $Pensioner_Updates->approved->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_Pensioner_Updates_approved" class="Pensioner_Updates_approved">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $Pensioner_Updates->approved->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($Pensioner_Updates->approved->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Pensioner_Updates->approved->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($Pensioner_Updates->Createdby->Visible) { // Createdby ?>
	<?php if ($Pensioner_Updates->SortUrl($Pensioner_Updates->Createdby) == "") { ?>
		<td><div id="elh_Pensioner_Updates_Createdby" class="Pensioner_Updates_Createdby"><div class="ewTableHeaderCaption"><?php echo $Pensioner_Updates->Createdby->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_Pensioner_Updates_Createdby" class="Pensioner_Updates_Createdby">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $Pensioner_Updates->Createdby->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($Pensioner_Updates->Createdby->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Pensioner_Updates->Createdby->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($Pensioner_Updates->CreatedDate->Visible) { // CreatedDate ?>
	<?php if ($Pensioner_Updates->SortUrl($Pensioner_Updates->CreatedDate) == "") { ?>
		<td><div id="elh_Pensioner_Updates_CreatedDate" class="Pensioner_Updates_CreatedDate"><div class="ewTableHeaderCaption"><?php echo $Pensioner_Updates->CreatedDate->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div><div id="elh_Pensioner_Updates_CreatedDate" class="Pensioner_Updates_CreatedDate">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $Pensioner_Updates->CreatedDate->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($Pensioner_Updates->CreatedDate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Pensioner_Updates->CreatedDate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$Pensioner_Updates_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($Pensioner_Updates->ExportAll && $Pensioner_Updates->Export <> "") {
	$Pensioner_Updates_list->StopRec = $Pensioner_Updates_list->TotalRecs;
} else {

	// Set the last record to display
	if ($Pensioner_Updates_list->TotalRecs > $Pensioner_Updates_list->StartRec + $Pensioner_Updates_list->DisplayRecs - 1)
		$Pensioner_Updates_list->StopRec = $Pensioner_Updates_list->StartRec + $Pensioner_Updates_list->DisplayRecs - 1;
	else
		$Pensioner_Updates_list->StopRec = $Pensioner_Updates_list->TotalRecs;
}
$Pensioner_Updates_list->RecCnt = $Pensioner_Updates_list->StartRec - 1;
if ($Pensioner_Updates_list->Recordset && !$Pensioner_Updates_list->Recordset->EOF) {
	$Pensioner_Updates_list->Recordset->MoveFirst();
	if (!$bSelectLimit && $Pensioner_Updates_list->StartRec > 1)
		$Pensioner_Updates_list->Recordset->Move($Pensioner_Updates_list->StartRec - 1);
} elseif (!$Pensioner_Updates->AllowAddDeleteRow && $Pensioner_Updates_list->StopRec == 0) {
	$Pensioner_Updates_list->StopRec = $Pensioner_Updates->GridAddRowCount;
}

// Initialize aggregate
$Pensioner_Updates->RowType = EW_ROWTYPE_AGGREGATEINIT;
$Pensioner_Updates->ResetAttrs();
$Pensioner_Updates_list->RenderRow();
while ($Pensioner_Updates_list->RecCnt < $Pensioner_Updates_list->StopRec) {
	$Pensioner_Updates_list->RecCnt++;
	if (intval($Pensioner_Updates_list->RecCnt) >= intval($Pensioner_Updates_list->StartRec)) {
		$Pensioner_Updates_list->RowCnt++;

		// Set up key count
		$Pensioner_Updates_list->KeyCount = $Pensioner_Updates_list->RowIndex;

		// Init row class and style
		$Pensioner_Updates->ResetAttrs();
		$Pensioner_Updates->CssClass = "";
		if ($Pensioner_Updates->CurrentAction == "gridadd") {
		} else {
			$Pensioner_Updates_list->LoadRowValues($Pensioner_Updates_list->Recordset); // Load row values
		}
		$Pensioner_Updates->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$Pensioner_Updates->RowAttrs = array_merge($Pensioner_Updates->RowAttrs, array('data-rowindex'=>$Pensioner_Updates_list->RowCnt, 'id'=>'r' . $Pensioner_Updates_list->RowCnt . '_Pensioner_Updates', 'data-rowtype'=>$Pensioner_Updates->RowType));

		// Render row
		$Pensioner_Updates_list->RenderRow();

		// Render list options
		$Pensioner_Updates_list->RenderListOptions();
?>
	<tr<?php echo $Pensioner_Updates->RowAttributes() ?>>
<?php

// Render list options (body, left)
$Pensioner_Updates_list->ListOptions->Render("body", "left", $Pensioner_Updates_list->RowCnt);
?>
	<?php if ($Pensioner_Updates->PensionerID->Visible) { // PensionerID ?>
		<td<?php echo $Pensioner_Updates->PensionerID->CellAttributes() ?>>
<span<?php echo $Pensioner_Updates->PensionerID->ViewAttributes() ?>>
<?php echo $Pensioner_Updates->PensionerID->ListViewValue() ?></span>
<a id="<?php echo $Pensioner_Updates_list->PageObjName . "_row_" . $Pensioner_Updates_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($Pensioner_Updates->_field->Visible) { // field ?>
		<td<?php echo $Pensioner_Updates->_field->CellAttributes() ?>>
<span<?php echo $Pensioner_Updates->_field->ViewAttributes() ?>>
<?php echo $Pensioner_Updates->_field->ListViewValue() ?></span>
<a id="<?php echo $Pensioner_Updates_list->PageObjName . "_row_" . $Pensioner_Updates_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($Pensioner_Updates->new_value->Visible) { // new_value ?>
		<td<?php echo $Pensioner_Updates->new_value->CellAttributes() ?>>
<span<?php echo $Pensioner_Updates->new_value->ViewAttributes() ?>>
<?php echo $Pensioner_Updates->new_value->ListViewValue() ?></span>
<a id="<?php echo $Pensioner_Updates_list->PageObjName . "_row_" . $Pensioner_Updates_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($Pensioner_Updates->old_value->Visible) { // old_value ?>
		<td<?php echo $Pensioner_Updates->old_value->CellAttributes() ?>>
<span<?php echo $Pensioner_Updates->old_value->ViewAttributes() ?>>
<?php echo $Pensioner_Updates->old_value->ListViewValue() ?></span>
<a id="<?php echo $Pensioner_Updates_list->PageObjName . "_row_" . $Pensioner_Updates_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($Pensioner_Updates->dateUpdated->Visible) { // dateUpdated ?>
		<td<?php echo $Pensioner_Updates->dateUpdated->CellAttributes() ?>>
<span<?php echo $Pensioner_Updates->dateUpdated->ViewAttributes() ?>>
<?php echo $Pensioner_Updates->dateUpdated->ListViewValue() ?></span>
<a id="<?php echo $Pensioner_Updates_list->PageObjName . "_row_" . $Pensioner_Updates_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($Pensioner_Updates->approved->Visible) { // approved ?>
		<td<?php echo $Pensioner_Updates->approved->CellAttributes() ?>>
<span<?php echo $Pensioner_Updates->approved->ViewAttributes() ?>>
<?php echo $Pensioner_Updates->approved->ListViewValue() ?></span>
<a id="<?php echo $Pensioner_Updates_list->PageObjName . "_row_" . $Pensioner_Updates_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($Pensioner_Updates->Createdby->Visible) { // Createdby ?>
		<td<?php echo $Pensioner_Updates->Createdby->CellAttributes() ?>>
<span<?php echo $Pensioner_Updates->Createdby->ViewAttributes() ?>>
<?php echo $Pensioner_Updates->Createdby->ListViewValue() ?></span>
<a id="<?php echo $Pensioner_Updates_list->PageObjName . "_row_" . $Pensioner_Updates_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($Pensioner_Updates->CreatedDate->Visible) { // CreatedDate ?>
		<td<?php echo $Pensioner_Updates->CreatedDate->CellAttributes() ?>>
<span<?php echo $Pensioner_Updates->CreatedDate->ViewAttributes() ?>>
<?php echo $Pensioner_Updates->CreatedDate->ListViewValue() ?></span>
<a id="<?php echo $Pensioner_Updates_list->PageObjName . "_row_" . $Pensioner_Updates_list->RowCnt ?>"></a></td>
	<?php } ?>
<?php

// Render list options (body, right)
$Pensioner_Updates_list->ListOptions->Render("body", "right", $Pensioner_Updates_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($Pensioner_Updates->CurrentAction <> "gridadd")
		$Pensioner_Updates_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($Pensioner_Updates->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($Pensioner_Updates_list->Recordset)
	$Pensioner_Updates_list->Recordset->Close();
?>
<?php if ($Pensioner_Updates_list->TotalRecs > 0) { ?>
<?php if ($Pensioner_Updates->Export == "") { ?>
<div class="ewGridLowerPanel">
<?php if ($Pensioner_Updates->CurrentAction <> "gridadd" && $Pensioner_Updates->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($Pensioner_Updates_list->Pager)) $Pensioner_Updates_list->Pager = new cNumericPager($Pensioner_Updates_list->StartRec, $Pensioner_Updates_list->DisplayRecs, $Pensioner_Updates_list->TotalRecs, $Pensioner_Updates_list->RecRange) ?>
<?php if ($Pensioner_Updates_list->Pager->RecordCount > 0) { ?>
<table cellspacing="0" class="ewStdTable"><tbody><tr><td>
<div class="pagination"><ul>
	<?php if ($Pensioner_Updates_list->Pager->FirstButton->Enabled) { ?>
	<li><a href="<?php echo $Pensioner_Updates_list->PageUrl() ?>start=<?php echo $Pensioner_Updates_list->Pager->FirstButton->Start ?>"><?php echo $Language->Phrase("PagerFirst") ?></a></li>
	<?php } ?>
	<?php if ($Pensioner_Updates_list->Pager->PrevButton->Enabled) { ?>
	<li><a href="<?php echo $Pensioner_Updates_list->PageUrl() ?>start=<?php echo $Pensioner_Updates_list->Pager->PrevButton->Start ?>"><?php echo $Language->Phrase("PagerPrevious") ?></a></li>
	<?php } ?>
	<?php foreach ($Pensioner_Updates_list->Pager->Items as $PagerItem) { ?>
		<li<?php if (!$PagerItem->Enabled) { echo " class=\" active\""; } ?>><a href="<?php if ($PagerItem->Enabled) { echo $Pensioner_Updates_list->PageUrl() . "start=" . $PagerItem->Start; } else { echo "#"; } ?>"><?php echo $PagerItem->Text ?></a></li>
	<?php } ?>
	<?php if ($Pensioner_Updates_list->Pager->NextButton->Enabled) { ?>
	<li><a href="<?php echo $Pensioner_Updates_list->PageUrl() ?>start=<?php echo $Pensioner_Updates_list->Pager->NextButton->Start ?>"><?php echo $Language->Phrase("PagerNext") ?></a></li>
	<?php } ?>
	<?php if ($Pensioner_Updates_list->Pager->LastButton->Enabled) { ?>
	<li><a href="<?php echo $Pensioner_Updates_list->PageUrl() ?>start=<?php echo $Pensioner_Updates_list->Pager->LastButton->Start ?>"><?php echo $Language->Phrase("PagerLast") ?></a></li>
	<?php } ?>
</ul></div>
</td>
<td>
	<?php if ($Pensioner_Updates_list->Pager->ButtonCount > 0) { ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php } ?>
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $Pensioner_Updates_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $Pensioner_Updates_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $Pensioner_Updates_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($Pensioner_Updates_list->SearchWhere == "0=101") { ?>
	<p><?php echo $Language->Phrase("EnterSearchCriteria") ?></p>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoRecord") ?></p>
	<?php } ?>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoPermission") ?></p>
	<?php } ?>
<?php } ?>
</td>
<?php if ($Pensioner_Updates_list->TotalRecs > 0) { ?>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
<input type="hidden" name="t" value="Pensioner_Updates">
<select name="<?php echo EW_TABLE_REC_PER_PAGE ?>" class="input-small" onchange="this.form.submit();">
<option value="20"<?php if ($Pensioner_Updates_list->DisplayRecs == 20) { ?> selected="selected"<?php } ?>>20</option>
<option value="50"<?php if ($Pensioner_Updates_list->DisplayRecs == 50) { ?> selected="selected"<?php } ?>>50</option>
<option value="70"<?php if ($Pensioner_Updates_list->DisplayRecs == 70) { ?> selected="selected"<?php } ?>>70</option>
<option value="ALL"<?php if ($Pensioner_Updates->getRecordsPerPage() == -1) { ?> selected="selected"<?php } ?>><?php echo $Language->Phrase("AllRecords") ?></option>
</select>
</td>
<?php } ?>
</tr></table>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($Pensioner_Updates_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
</div>
<?php } ?>
<?php } ?>
</td></tr></table>
<?php if ($Pensioner_Updates->Export == "") { ?>
<script type="text/javascript">
fPensioner_Updateslistsrch.Init();
fPensioner_Updateslist.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php } ?>
<?php
$Pensioner_Updates_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($Pensioner_Updates->Export == "") { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$Pensioner_Updates_list->Page_Terminate();
?>
