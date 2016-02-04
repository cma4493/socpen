<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "tbl_pension_payrollclaimed_info.php" ?>
<?php include_once "tbl_userinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php include_once "model/DAO.php" ?>
<?php include_once "model/PensionerIDCustom.php" ?>
<?php

//
// Page class
//

$tbl_pension_payroll_list = NULL; // Initialize page object first

class ctbl_pension_payroll_list extends ctbl_pension_payroll {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Table name
	var $TableName = 'tbl_pension_payroll';

	// Page object name
	var $PageObjName = 'tbl_pension_payroll_list';

	// Grid form hidden field names
	var $FormName = 'ftbl_pension_payrolllist';
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

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "tbl_pension_payrolladd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "tbl_pension_payrolldelete.php";
		$this->MultiUpdateUrl = "tbl_pension_payrollupdate.php";

		// Table object (tbl_user)
		if (!isset($GLOBALS['tbl_user'])) $GLOBALS['tbl_user'] = new ctbl_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'tbl_pension_payroll', TRUE);

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
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up curent action

		// Get grid add count
		$gridaddcnt = @$_GET[EW_TABLE_GRID_ADD_ROW_COUNT];
		if (is_numeric($gridaddcnt) && $gridaddcnt > 0)
			$this->GridAddRowCount = $gridaddcnt;

		// Set up list options
		$this->SetupListOptions();
		$this->PayrollID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();
		$this->Createdby->Visible = !$this->IsAddOrEdit();
		$this->CreatedDate->Visible = !$this->IsAddOrEdit();
		$this->UpdatedBy->Visible = !$this->IsAddOrEdit();
		$this->UpdatedDate->Visible = !$this->IsAddOrEdit();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Setup other options
		$this->SetupOtherOptions();

		// Set "checkbox" visible
		if (count($this->CustomActions) > 0)
			$this->ListOptions->Items["checkbox"]->Visible = TRUE;
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
			$this->PayrollID->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->PayrollID->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Advanced search WHERE clause based on QueryString
	function AdvancedSearchWhere() {
		global $Security;
		$sWhere = "";
		if (!$Security->CanSearch()) return "";
		$this->BuildSearchSql($sWhere, $this->PayrollID, FALSE); // PayrollID
		$this->BuildSearchSql($sWhere, $this->PensionerID, FALSE); // PensionerID
		$this->BuildSearchSql($sWhere, $this->PayrollYear, FALSE); // PayrollYear
		$this->BuildSearchSql($sWhere, $this->cMonth, FALSE); // cMonth
		$this->BuildSearchSql($sWhere, $this->amount, FALSE); // amount
		$this->BuildSearchSql($sWhere, $this->paymentmodeID, FALSE); // paymentmodeID
		$this->BuildSearchSql($sWhere, $this->Approved, FALSE); // Approved
		$this->BuildSearchSql($sWhere, $this->Claimed, FALSE); // Claimed
		$this->BuildSearchSql($sWhere, $this->Createdby, FALSE); // Createdby
		$this->BuildSearchSql($sWhere, $this->CreatedDate, FALSE); // CreatedDate
		$this->BuildSearchSql($sWhere, $this->UpdatedBy, FALSE); // UpdatedBy
		$this->BuildSearchSql($sWhere, $this->UpdatedDate, FALSE); // UpdatedDate

		// Set up search parm
		if ($sWhere <> "") {
			$this->Command = "search";
		}
		if ($this->Command == "search") {
			$this->PayrollID->AdvancedSearch->Save(); // PayrollID
			$this->PensionerID->AdvancedSearch->Save(); // PensionerID
			$this->PayrollYear->AdvancedSearch->Save(); // PayrollYear
			$this->cMonth->AdvancedSearch->Save(); // cMonth
			$this->amount->AdvancedSearch->Save(); // amount
			$this->paymentmodeID->AdvancedSearch->Save(); // paymentmodeID
			$this->Approved->AdvancedSearch->Save(); // Approved
			$this->Claimed->AdvancedSearch->Save(); // Claimed
			$this->Createdby->AdvancedSearch->Save(); // Createdby
			$this->CreatedDate->AdvancedSearch->Save(); // CreatedDate
			$this->UpdatedBy->AdvancedSearch->Save(); // UpdatedBy
			$this->UpdatedDate->AdvancedSearch->Save(); // UpdatedDate
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

	// Check if search parm exists
	function CheckSearchParms() {
		if ($this->PayrollID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->PensionerID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->PayrollYear->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->cMonth->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->amount->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->paymentmodeID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Approved->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Claimed->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Createdby->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->CreatedDate->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->UpdatedBy->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->UpdatedDate->AdvancedSearch->IssetSession())
			return TRUE;
		return FALSE;
	}

	// Clear all search parameters
	function ResetSearchParms() {

		// Clear search WHERE clause
		$this->SearchWhere = "";
		$this->setSearchWhere($this->SearchWhere);

		// Clear advanced search parameters
		$this->ResetAdvancedSearchParms();
	}

	// Load advanced search default values
	function LoadAdvancedSearchDefault() {
		return FALSE;
	}

	// Clear all advanced search parameters
	function ResetAdvancedSearchParms() {
		$this->PayrollID->AdvancedSearch->UnsetSession();
		$this->PensionerID->AdvancedSearch->UnsetSession();
		$this->PayrollYear->AdvancedSearch->UnsetSession();
		$this->cMonth->AdvancedSearch->UnsetSession();
		$this->amount->AdvancedSearch->UnsetSession();
		$this->paymentmodeID->AdvancedSearch->UnsetSession();
		$this->Approved->AdvancedSearch->UnsetSession();
		$this->Claimed->AdvancedSearch->UnsetSession();
		$this->Createdby->AdvancedSearch->UnsetSession();
		$this->CreatedDate->AdvancedSearch->UnsetSession();
		$this->UpdatedBy->AdvancedSearch->UnsetSession();
		$this->UpdatedDate->AdvancedSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore advanced search values
		$this->PayrollID->AdvancedSearch->Load();
		$this->PensionerID->AdvancedSearch->Load();
		$this->PayrollYear->AdvancedSearch->Load();
		$this->cMonth->AdvancedSearch->Load();
		$this->amount->AdvancedSearch->Load();
		$this->paymentmodeID->AdvancedSearch->Load();
		$this->Approved->AdvancedSearch->Load();
		$this->Claimed->AdvancedSearch->Load();
		$this->Createdby->AdvancedSearch->Load();
		$this->CreatedDate->AdvancedSearch->Load();
		$this->UpdatedBy->AdvancedSearch->Load();
		$this->UpdatedDate->AdvancedSearch->Load();
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->PayrollID); // PayrollID
			$this->UpdateSort($this->PensionerID); // PensionerID
			$this->UpdateSort($this->PayrollYear); // PayrollYear
			$this->UpdateSort($this->cMonth); // cMonth
			$this->UpdateSort($this->amount); // amount
			$this->UpdateSort($this->paymentmodeID); // paymentmodeID
			$this->UpdateSort($this->Approved); // Approved
			$this->UpdateSort($this->Claimed); // Claimed
			$this->UpdateSort($this->Createdby); // Createdby
			$this->UpdateSort($this->CreatedDate); // CreatedDate
			$this->UpdateSort($this->UpdatedBy); // UpdatedBy
			$this->UpdateSort($this->UpdatedDate); // UpdatedDate
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
				$this->PayrollID->setSort("");
				$this->PensionerID->setSort("");
				$this->PayrollYear->setSort("");
				$this->cMonth->setSort("");
				$this->amount->setSort("");
				$this->paymentmodeID->setSort("");
				$this->Approved->setSort("");
				$this->Claimed->setSort("");
				$this->Createdby->setSort("");
				$this->CreatedDate->setSort("");
				$this->UpdatedBy->setSort("");
				$this->UpdatedDate->setSort("");
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
		$item->Visible = $Security->CanView();
		$item->OnLeft = TRUE;

		// "edit"
		$item = &$this->ListOptions->Add("edit");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = false and $Security->CanEdit();
		$item->OnLeft = TRUE;

		// "copy"
		$item = &$this->ListOptions->Add("copy");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = false and $Security->CanAdd();
		$item->OnLeft = TRUE;

		// "checkbox"
		$item = &$this->ListOptions->Add("checkbox");
		$item->Visible = false and $Security->CanDelete();
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

		$PensionerIDCustom = new PensionerIDCustom();

		// "view"
		$oListOpt = &$this->ListOptions->Items["view"];
		if ($Security->CanView())
			$oListOpt->Body = "<a tar class=\"blue\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("ViewLink")) . "\" href=\"tbl_pensionerview.php?showdetail=&SeniorID=" . $PensionerIDCustom->getSeniorID($this->PayrollID->CurrentValue) . "\">" . $Language->Phrase("ViewLink") . "<i class=\"icon-zoom-in bigger-130\"></i></a>";
		else
			$oListOpt->Body = "";

		// "edit"
		$oListOpt = &$this->ListOptions->Items["edit"];
		if ($Security->CanEdit()) {
			$oListOpt->Body = "<a class=\"green\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("EditLink")) . "\" href=\"" . ew_HtmlEncode($this->EditUrl) . "\">" . $Language->Phrase("EditLink") . "<i class=\"icon-pencil bigger-130\"></i></a>";
		} else {
			$oListOpt->Body = "";
		}

		// "copy"
		$oListOpt = &$this->ListOptions->Items["copy"];
		if ($Security->CanAdd()) {
			$oListOpt->Body = "<a class=\"blue\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("CopyLink")) . "\" href=\"" . ew_HtmlEncode($this->CopyUrl) . "\">" . $Language->Phrase("CopyLink") . "<i class=\"icon-copy bigger-130\"></i></a>";
		} else {
			$oListOpt->Body = "";
		}

		// "checkbox"
		$oListOpt = &$this->ListOptions->Items["checkbox"];
		$oListOpt->Body = "<label class=\"checkbox\"><input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->PayrollID->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event, this);'></label>";
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
		$item->Visible = false and ($this->AddUrl <> "" && $Security->CanAdd());
		$option = $options["action"];

		// Add multi delete
		$item = &$option->Add("multidelete");
		$item->Body = "<a class=\"btn btn-purple btn-sm\" href=\"\" onclick=\"ew_SubmitSelected(document.ftbl_pension_payrolllist, '" . $this->MultiDeleteUrl . "', ewLanguage.Phrase('DeleteMultiConfirmMsg'));return false;\">" . $Language->Phrase("DeleteSelectedLink") . "</a>";
		$item->Visible = false and ($Security->CanDelete());

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
				$item->Body = "<a class=\"btn btn-warning btn-sm\" href=\"\" onclick=\"ew_SubmitSelected(document.ftbl_pension_payrolllist, '" . ew_CurrentUrl() . "', null, '" . $action . "');return false;\">" . $name . "</a>";
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

	//  Load search values for validation
	function LoadSearchValues() {
		global $objForm;

		// Load search values
		// PayrollID

		$this->PayrollID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_PayrollID"]);
		if ($this->PayrollID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->PayrollID->AdvancedSearch->SearchOperator = @$_GET["z_PayrollID"];

		// PensionerID
		$this->PensionerID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_PensionerID"]);
		if ($this->PensionerID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->PensionerID->AdvancedSearch->SearchOperator = @$_GET["z_PensionerID"];

		// PayrollYear
		$this->PayrollYear->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_PayrollYear"]);
		if ($this->PayrollYear->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->PayrollYear->AdvancedSearch->SearchOperator = @$_GET["z_PayrollYear"];

		// cMonth
		$this->cMonth->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_cMonth"]);
		if ($this->cMonth->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->cMonth->AdvancedSearch->SearchOperator = @$_GET["z_cMonth"];

		// amount
		$this->amount->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_amount"]);
		if ($this->amount->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->amount->AdvancedSearch->SearchOperator = @$_GET["z_amount"];

		// paymentmodeID
		$this->paymentmodeID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_paymentmodeID"]);
		if ($this->paymentmodeID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->paymentmodeID->AdvancedSearch->SearchOperator = @$_GET["z_paymentmodeID"];

		// Approved
		$this->Approved->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Approved"]);
		if ($this->Approved->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Approved->AdvancedSearch->SearchOperator = @$_GET["z_Approved"];

		// Claimed
		$this->Claimed->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Claimed"]);
		if ($this->Claimed->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Claimed->AdvancedSearch->SearchOperator = @$_GET["z_Claimed"];

		// Createdby
		$this->Createdby->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Createdby"]);
		if ($this->Createdby->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Createdby->AdvancedSearch->SearchOperator = @$_GET["z_Createdby"];

		// CreatedDate
		$this->CreatedDate->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_CreatedDate"]);
		if ($this->CreatedDate->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->CreatedDate->AdvancedSearch->SearchOperator = @$_GET["z_CreatedDate"];

		// UpdatedBy
		$this->UpdatedBy->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_UpdatedBy"]);
		if ($this->UpdatedBy->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->UpdatedBy->AdvancedSearch->SearchOperator = @$_GET["z_UpdatedBy"];

		// UpdatedDate
		$this->UpdatedDate->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_UpdatedDate"]);
		if ($this->UpdatedDate->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->UpdatedDate->AdvancedSearch->SearchOperator = @$_GET["z_UpdatedDate"];
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
		$this->ViewUrl = $this->GetViewUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->InlineEditUrl = $this->GetInlineEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->InlineCopyUrl = $this->GetInlineCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();

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
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// PayrollID
			$this->PayrollID->EditCustomAttributes = "";
			$this->PayrollID->EditValue = ew_HtmlEncode($this->PayrollID->AdvancedSearch->SearchValue);
			$this->PayrollID->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->PayrollID->FldCaption()));

			// PensionerID
			$this->PensionerID->EditCustomAttributes = "";
			if (trim(strval($this->PensionerID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`PensionerID`" . ew_SearchString("=", $this->PensionerID->AdvancedSearch->SearchValue, EW_DATATYPE_STRING);
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
			if (trim(strval($this->PayrollYear->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Year`" . ew_SearchString("=", $this->PayrollYear->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER);
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
			if (trim(strval($this->cMonth->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`MonthID`" . ew_SearchString("=", $this->cMonth->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER);
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
			$this->amount->EditValue = ew_HtmlEncode($this->amount->AdvancedSearch->SearchValue);
			$this->amount->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->amount->FldCaption()));

			// paymentmodeID
			$this->paymentmodeID->EditCustomAttributes = "";
			if (trim(strval($this->paymentmodeID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`paymentmodeID`" . ew_SearchString("=", $this->paymentmodeID->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER);
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
			$this->Createdby->EditCustomAttributes = "";
			$this->Createdby->EditValue = ew_HtmlEncode($this->Createdby->AdvancedSearch->SearchValue);
			$this->Createdby->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->Createdby->FldCaption()));

			// CreatedDate
			$this->CreatedDate->EditCustomAttributes = "";
			$this->CreatedDate->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->CreatedDate->AdvancedSearch->SearchValue, 6), 6));
			$this->CreatedDate->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->CreatedDate->FldCaption()));

			// UpdatedBy
			$this->UpdatedBy->EditCustomAttributes = "";
			$this->UpdatedBy->EditValue = ew_HtmlEncode($this->UpdatedBy->AdvancedSearch->SearchValue);
			$this->UpdatedBy->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->UpdatedBy->FldCaption()));

			// UpdatedDate
			$this->UpdatedDate->EditCustomAttributes = "";
			$this->UpdatedDate->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->UpdatedDate->AdvancedSearch->SearchValue, 6), 6));
			$this->UpdatedDate->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->UpdatedDate->FldCaption()));
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
		$this->PayrollID->AdvancedSearch->Load();
		$this->PensionerID->AdvancedSearch->Load();
		$this->PayrollYear->AdvancedSearch->Load();
		$this->cMonth->AdvancedSearch->Load();
		$this->amount->AdvancedSearch->Load();
		$this->paymentmodeID->AdvancedSearch->Load();
		$this->Approved->AdvancedSearch->Load();
		$this->Claimed->AdvancedSearch->Load();
		$this->Createdby->AdvancedSearch->Load();
		$this->CreatedDate->AdvancedSearch->Load();
		$this->UpdatedBy->AdvancedSearch->Load();
		$this->UpdatedDate->AdvancedSearch->Load();
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
		$table = 'tbl_pension_payroll';
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
if (!isset($tbl_pension_payroll_list)) $tbl_pension_payroll_list = new ctbl_pension_payroll_list();

// Page init
$tbl_pension_payroll_list->Page_Init();

// Page main
$tbl_pension_payroll_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$tbl_pension_payroll_list->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var tbl_pension_payroll_list = new ew_Page("tbl_pension_payroll_list");
tbl_pension_payroll_list.PageID = "list"; // Page ID
var EW_PAGE_ID = tbl_pension_payroll_list.PageID; // For backward compatibility

// Form object
var ftbl_pension_payrolllist = new ew_Form("ftbl_pension_payrolllist");
ftbl_pension_payrolllist.FormKeyCountName = '<?php echo $tbl_pension_payroll_list->FormKeyCountName ?>';

// Form_CustomValidate event
ftbl_pension_payrolllist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftbl_pension_payrolllist.ValidateRequired = true;
<?php } else { ?>
ftbl_pension_payrolllist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
ftbl_pension_payrolllist.Lists["x_PensionerID"] = {"LinkField":"x_PensionerID","Ajax":true,"AutoFill":false,"DisplayFields":["x_lastname","x_firstname","x_middlename","x_extname"],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pension_payrolllist.Lists["x_PayrollYear"] = {"LinkField":"x_Year","Ajax":true,"AutoFill":false,"DisplayFields":["x_Year","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pension_payrolllist.Lists["x_cMonth"] = {"LinkField":"x_MonthID","Ajax":true,"AutoFill":false,"DisplayFields":["x_desc","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pension_payrolllist.Lists["x_paymentmodeID"] = {"LinkField":"x_paymentmodeID","Ajax":true,"AutoFill":false,"DisplayFields":["x_description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
var ftbl_pension_payrolllistsrch = new ew_Form("ftbl_pension_payrolllistsrch");

// Validate function for search
ftbl_pension_payrolllistsrch.Validate = function(fobj) {
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
ftbl_pension_payrolllistsrch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftbl_pension_payrolllistsrch.ValidateRequired = true; // Use JavaScript validation
<?php } else { ?>
ftbl_pension_payrolllistsrch.ValidateRequired = false; // No JavaScript validation
<?php } ?>

// Dynamic selection lists
ftbl_pension_payrolllistsrch.Lists["x_PensionerID"] = {"LinkField":"x_PensionerID","Ajax":true,"AutoFill":false,"DisplayFields":["x_lastname","x_firstname","x_middlename","x_extname"],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pension_payrolllistsrch.Lists["x_PayrollYear"] = {"LinkField":"x_Year","Ajax":true,"AutoFill":false,"DisplayFields":["x_Year","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pension_payrolllistsrch.Lists["x_cMonth"] = {"LinkField":"x_MonthID","Ajax":true,"AutoFill":false,"DisplayFields":["x_desc","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_pension_payrolllistsrch.Lists["x_paymentmodeID"] = {"LinkField":"x_paymentmodeID","Ajax":true,"AutoFill":false,"DisplayFields":["x_description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php //$Breadcrumb->Render(); ?>
<?php if ($tbl_pension_payroll_list->ExportOptions->Visible()) { ?>
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
		$tbl_pension_payroll_list->TotalRecs = $tbl_pension_payroll->SelectRecordCount();
	} else {
		if ($tbl_pension_payroll_list->Recordset = $tbl_pension_payroll_list->LoadRecordset())
			$tbl_pension_payroll_list->TotalRecs = $tbl_pension_payroll_list->Recordset->RecordCount();
	}
	$tbl_pension_payroll_list->StartRec = 1;
	if ($tbl_pension_payroll_list->DisplayRecs <= 0 || ($tbl_pension_payroll->Export <> "" && $tbl_pension_payroll->ExportAll)) // Display all records
		$tbl_pension_payroll_list->DisplayRecs = $tbl_pension_payroll_list->TotalRecs;
	if (!($tbl_pension_payroll->Export <> "" && $tbl_pension_payroll->ExportAll))
		$tbl_pension_payroll_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$tbl_pension_payroll_list->Recordset = $tbl_pension_payroll_list->LoadRecordset($tbl_pension_payroll_list->StartRec-1, $tbl_pension_payroll_list->DisplayRecs);
$tbl_pension_payroll_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($tbl_pension_payroll->Export == "" && $tbl_pension_payroll->CurrentAction == "") { ?>
<form name="ftbl_pension_payrolllistsrch" id="ftbl_pension_payrolllistsrch" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>">
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
	<div id="ftbl_pension_payrolllistsrch_SearchPanel">
		<input type="hidden" name="cmd" value="search">
		<input type="hidden" name="t" value="tbl_pension_payroll">
		<div class="ewBasicSearch">
<?php
if ($gsSearchError == "")
	$tbl_pension_payroll_list->LoadAdvancedSearch(); // Load advanced search

// Render for search
$tbl_pension_payroll->RowType = EW_ROWTYPE_SEARCH;

// Render row
$tbl_pension_payroll->ResetAttrs();
$tbl_pension_payroll_list->RenderRow();
?>
<div id="xsr_1" class="ewRow">
<?php if ($tbl_pension_payroll->PensionerID->Visible) { // PensionerID ?>
	<span id="xsc_PensionerID" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_pension_payroll->PensionerID->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_PensionerID" id="z_PensionerID" value="LIKE"></span>
		<span class="control-group ewSearchField">
<select data-field="x_PensionerID" id="x_PensionerID" name="x_PensionerID"<?php echo $tbl_pension_payroll->PensionerID->EditAttributes() ?>>
<?php
if (is_array($tbl_pension_payroll->PensionerID->EditValue)) {
	$arwrk = $tbl_pension_payroll->PensionerID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_pension_payroll->PensionerID->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
	</span>
<?php } ?>
</div>
<div id="xsr_2" class="ewRow">
<?php if ($tbl_pension_payroll->PayrollYear->Visible) { // PayrollYear ?>
	<span id="xsc_PayrollYear" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_pension_payroll->PayrollYear->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_PayrollYear" id="z_PayrollYear" value="="></span>
		<span class="control-group ewSearchField">
<select data-field="x_PayrollYear" id="x_PayrollYear" name="x_PayrollYear"<?php echo $tbl_pension_payroll->PayrollYear->EditAttributes() ?>>
<?php
if (is_array($tbl_pension_payroll->PayrollYear->EditValue)) {
	$arwrk = $tbl_pension_payroll->PayrollYear->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_pension_payroll->PayrollYear->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
	</span>
<?php } ?>
</div>
<div id="xsr_3" class="ewRow">
<?php if ($tbl_pension_payroll->cMonth->Visible) { // cMonth ?>
	<span id="xsc_cMonth" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_pension_payroll->cMonth->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_cMonth" id="z_cMonth" value="="></span>
		<span class="control-group ewSearchField">
<select data-field="x_cMonth" id="x_cMonth" name="x_cMonth"<?php echo $tbl_pension_payroll->cMonth->EditAttributes() ?>>
<?php
if (is_array($tbl_pension_payroll->cMonth->EditValue)) {
	$arwrk = $tbl_pension_payroll->cMonth->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_pension_payroll->cMonth->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
	</span>
<?php } ?>
</div>
<div id="xsr_4" class="ewRow">
<?php if ($tbl_pension_payroll->paymentmodeID->Visible) { // paymentmodeID ?>
	<span id="xsc_paymentmodeID" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_pension_payroll->paymentmodeID->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_paymentmodeID" id="z_paymentmodeID" value="="></span>
		<span class="control-group ewSearchField">
<select data-field="x_paymentmodeID" id="x_paymentmodeID" name="x_paymentmodeID"<?php echo $tbl_pension_payroll->paymentmodeID->EditAttributes() ?>>
<?php
if (is_array($tbl_pension_payroll->paymentmodeID->EditValue)) {
	$arwrk = $tbl_pension_payroll->paymentmodeID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_pension_payroll->paymentmodeID->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
	</span>
<?php } ?>
</div>
<div id="xsr_5" class="ewRow">
<?php if ($tbl_pension_payroll->Approved->Visible) { // Approved ?>
	<span id="xsc_Approved" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_pension_payroll->Approved->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Approved" id="z_Approved" value="="></span>
		<span class="control-group ewSearchField">
<select data-field="x_Approved" id="x_Approved" name="x_Approved"<?php echo $tbl_pension_payroll->Approved->EditAttributes() ?>>
<?php
if (is_array($tbl_pension_payroll->Approved->EditValue)) {
	$arwrk = $tbl_pension_payroll->Approved->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_pension_payroll->Approved->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<div id="xsr_6" class="ewRow">
<?php if ($tbl_pension_payroll->Claimed->Visible) { // Claimed ?>
	<span id="xsc_Claimed" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_pension_payroll->Claimed->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Claimed" id="z_Claimed" value="="></span>
		<span class="control-group ewSearchField">
<select data-field="x_Claimed" id="x_Claimed" name="x_Claimed"<?php echo $tbl_pension_payroll->Claimed->EditAttributes() ?>>
<?php
if (is_array($tbl_pension_payroll->Claimed->EditValue)) {
	$arwrk = $tbl_pension_payroll->Claimed->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_pension_payroll->Claimed->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<div id="xsr_7" class="row">
	<div class="col-xs-12 col-sm-4">
	<span class="input-group-btn">
	<button class="btn btn-purple btn-sm" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $Language->Phrase("QuickSearchBtn") ?> <i class="icon-search icon-on-right bigger-110"></i></button>&nbsp;
	<a type="button" class="btn btn-success btn-sm" href="<?php echo $tbl_pension_payroll_list->PageUrl() ?>cmd=reset">ShowAll <i class="icon-refresh icon-on-right bigger-110"></i></a>
	</span>
	</div>
	<div class="btn-group ewButtonGroup">
	<!--<a class="btn ewShowAll" href="<?php echo $tbl_pension_payroll_list->PageUrl() ?>cmd=reset"><?php echo $Language->Phrase("ShowAll") ?></a> -->
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
<?php $tbl_pension_payroll_list->ShowPageHeader(); ?>
<?php
$tbl_pension_payroll_list->ShowMessage();
?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridUpperPanel">
<?php if ($tbl_pension_payroll->CurrentAction <> "gridadd" && $tbl_pension_payroll->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($tbl_pension_payroll_list->Pager)) $tbl_pension_payroll_list->Pager = new cNumericPager($tbl_pension_payroll_list->StartRec, $tbl_pension_payroll_list->DisplayRecs, $tbl_pension_payroll_list->TotalRecs, $tbl_pension_payroll_list->RecRange) ?>
<?php if ($tbl_pension_payroll_list->Pager->RecordCount > 0) { ?>
<table cellspacing="0" class="ewStdTable"><tbody><tr><td>
<div class="pagination"><ul>
	<?php if ($tbl_pension_payroll_list->Pager->FirstButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_pension_payroll_list->PageUrl() ?>start=<?php echo $tbl_pension_payroll_list->Pager->FirstButton->Start ?>"><?php echo $Language->Phrase("PagerFirst") ?></a></li>
	<?php } ?>
	<?php if ($tbl_pension_payroll_list->Pager->PrevButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_pension_payroll_list->PageUrl() ?>start=<?php echo $tbl_pension_payroll_list->Pager->PrevButton->Start ?>"><?php echo $Language->Phrase("PagerPrevious") ?></a></li>
	<?php } ?>
	<?php foreach ($tbl_pension_payroll_list->Pager->Items as $PagerItem) { ?>
		<li<?php if (!$PagerItem->Enabled) { echo " class=\" active\""; } ?>><a href="<?php if ($PagerItem->Enabled) { echo $tbl_pension_payroll_list->PageUrl() . "start=" . $PagerItem->Start; } else { echo "#"; } ?>"><?php echo $PagerItem->Text ?></a></li>
	<?php } ?>
	<?php if ($tbl_pension_payroll_list->Pager->NextButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_pension_payroll_list->PageUrl() ?>start=<?php echo $tbl_pension_payroll_list->Pager->NextButton->Start ?>"><?php echo $Language->Phrase("PagerNext") ?></a></li>
	<?php } ?>
	<?php if ($tbl_pension_payroll_list->Pager->LastButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_pension_payroll_list->PageUrl() ?>start=<?php echo $tbl_pension_payroll_list->Pager->LastButton->Start ?>"><?php echo $Language->Phrase("PagerLast") ?></a></li>
	<?php } ?>
</ul></div>
</td>
<td>
	<?php if ($tbl_pension_payroll_list->Pager->ButtonCount > 0) { ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php } ?>
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $tbl_pension_payroll_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $tbl_pension_payroll_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $tbl_pension_payroll_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($tbl_pension_payroll_list->SearchWhere == "0=101") { ?>
	<p><?php echo $Language->Phrase("EnterSearchCriteria") ?></p>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoRecord") ?></p>
	<?php } ?>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoPermission") ?></p>
	<?php } ?>
<?php } ?>
</td>
<?php if ($tbl_pension_payroll_list->TotalRecs > 0) { ?>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
<input type="hidden" name="t" value="tbl_pension_payroll">
<select name="<?php echo EW_TABLE_REC_PER_PAGE ?>" class="input-small" onchange="this.form.submit();">
<option value="20"<?php if ($tbl_pension_payroll_list->DisplayRecs == 20) { ?> selected="selected"<?php } ?>>20</option>
<option value="40"<?php if ($tbl_pension_payroll_list->DisplayRecs == 40) { ?> selected="selected"<?php } ?>>40</option>
<option value="60"<?php if ($tbl_pension_payroll_list->DisplayRecs == 60) { ?> selected="selected"<?php } ?>>60</option>
<option value="ALL"<?php if ($tbl_pension_payroll->getRecordsPerPage() == -1) { ?> selected="selected"<?php } ?>><?php echo $Language->Phrase("AllRecords") ?></option>
</select>
</td>
<?php } ?>
</tr></table>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($tbl_pension_payroll_list->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
</div>
<form name="ftbl_pension_payrolllist" id="ftbl_pension_payrolllist" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="tbl_pension_payroll">
<div id="gmp_tbl_pension_payroll" class="ewGridMiddlePanel">
<?php if ($tbl_pension_payroll_list->TotalRecs > 0) { ?>
<table id="tbl_tbl_pension_payrolllist" class="ewTable ewTableSeparate">
<?php echo $tbl_pension_payroll->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$tbl_pension_payroll_list->RenderListOptions();

// Render list options (header, left)
$tbl_pension_payroll_list->ListOptions->Render("header", "left");
?>
<?php if ($tbl_pension_payroll->PayrollID->Visible) { // PayrollID ?>
	<?php if ($tbl_pension_payroll->SortUrl($tbl_pension_payroll->PayrollID) == "") { ?>
		<td><div id="elh_tbl_pension_payroll_PayrollID" class="tbl_pension_payroll_PayrollID"><div class="ewTableHeaderCaption"><?php echo $tbl_pension_payroll->PayrollID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pension_payroll->SortUrl($tbl_pension_payroll->PayrollID) ?>',1);"><div id="elh_tbl_pension_payroll_PayrollID" class="tbl_pension_payroll_PayrollID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pension_payroll->PayrollID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pension_payroll->PayrollID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pension_payroll->PayrollID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pension_payroll->PensionerID->Visible) { // PensionerID ?>
	<?php if ($tbl_pension_payroll->SortUrl($tbl_pension_payroll->PensionerID) == "") { ?>
		<td><div id="elh_tbl_pension_payroll_PensionerID" class="tbl_pension_payroll_PensionerID"><div class="ewTableHeaderCaption"><?php echo $tbl_pension_payroll->PensionerID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pension_payroll->SortUrl($tbl_pension_payroll->PensionerID) ?>',1);"><div id="elh_tbl_pension_payroll_PensionerID" class="tbl_pension_payroll_PensionerID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pension_payroll->PensionerID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pension_payroll->PensionerID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pension_payroll->PensionerID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pension_payroll->PayrollYear->Visible) { // PayrollYear ?>
	<?php if ($tbl_pension_payroll->SortUrl($tbl_pension_payroll->PayrollYear) == "") { ?>
		<td><div id="elh_tbl_pension_payroll_PayrollYear" class="tbl_pension_payroll_PayrollYear"><div class="ewTableHeaderCaption"><?php echo $tbl_pension_payroll->PayrollYear->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pension_payroll->SortUrl($tbl_pension_payroll->PayrollYear) ?>',1);"><div id="elh_tbl_pension_payroll_PayrollYear" class="tbl_pension_payroll_PayrollYear">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pension_payroll->PayrollYear->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pension_payroll->PayrollYear->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pension_payroll->PayrollYear->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pension_payroll->cMonth->Visible) { // cMonth ?>
	<?php if ($tbl_pension_payroll->SortUrl($tbl_pension_payroll->cMonth) == "") { ?>
		<td><div id="elh_tbl_pension_payroll_cMonth" class="tbl_pension_payroll_cMonth"><div class="ewTableHeaderCaption"><?php echo $tbl_pension_payroll->cMonth->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pension_payroll->SortUrl($tbl_pension_payroll->cMonth) ?>',1);"><div id="elh_tbl_pension_payroll_cMonth" class="tbl_pension_payroll_cMonth">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pension_payroll->cMonth->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pension_payroll->cMonth->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pension_payroll->cMonth->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pension_payroll->amount->Visible) { // amount ?>
	<?php if ($tbl_pension_payroll->SortUrl($tbl_pension_payroll->amount) == "") { ?>
		<td><div id="elh_tbl_pension_payroll_amount" class="tbl_pension_payroll_amount"><div class="ewTableHeaderCaption"><?php echo $tbl_pension_payroll->amount->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pension_payroll->SortUrl($tbl_pension_payroll->amount) ?>',1);"><div id="elh_tbl_pension_payroll_amount" class="tbl_pension_payroll_amount">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pension_payroll->amount->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pension_payroll->amount->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pension_payroll->amount->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pension_payroll->paymentmodeID->Visible) { // paymentmodeID ?>
	<?php if ($tbl_pension_payroll->SortUrl($tbl_pension_payroll->paymentmodeID) == "") { ?>
		<td><div id="elh_tbl_pension_payroll_paymentmodeID" class="tbl_pension_payroll_paymentmodeID"><div class="ewTableHeaderCaption"><?php echo $tbl_pension_payroll->paymentmodeID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pension_payroll->SortUrl($tbl_pension_payroll->paymentmodeID) ?>',1);"><div id="elh_tbl_pension_payroll_paymentmodeID" class="tbl_pension_payroll_paymentmodeID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pension_payroll->paymentmodeID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pension_payroll->paymentmodeID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pension_payroll->paymentmodeID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pension_payroll->Approved->Visible) { // Approved ?>
	<?php if ($tbl_pension_payroll->SortUrl($tbl_pension_payroll->Approved) == "") { ?>
		<td><div id="elh_tbl_pension_payroll_Approved" class="tbl_pension_payroll_Approved"><div class="ewTableHeaderCaption"><?php echo $tbl_pension_payroll->Approved->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pension_payroll->SortUrl($tbl_pension_payroll->Approved) ?>',1);"><div id="elh_tbl_pension_payroll_Approved" class="tbl_pension_payroll_Approved">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pension_payroll->Approved->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pension_payroll->Approved->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pension_payroll->Approved->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pension_payroll->Claimed->Visible) { // Claimed ?>
	<?php if ($tbl_pension_payroll->SortUrl($tbl_pension_payroll->Claimed) == "") { ?>
		<td><div id="elh_tbl_pension_payroll_Claimed" class="tbl_pension_payroll_Claimed"><div class="ewTableHeaderCaption"><?php echo $tbl_pension_payroll->Claimed->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pension_payroll->SortUrl($tbl_pension_payroll->Claimed) ?>',1);"><div id="elh_tbl_pension_payroll_Claimed" class="tbl_pension_payroll_Claimed">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pension_payroll->Claimed->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pension_payroll->Claimed->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pension_payroll->Claimed->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pension_payroll->Createdby->Visible) { // Createdby ?>
	<?php if ($tbl_pension_payroll->SortUrl($tbl_pension_payroll->Createdby) == "") { ?>
		<td><div id="elh_tbl_pension_payroll_Createdby" class="tbl_pension_payroll_Createdby"><div class="ewTableHeaderCaption"><?php echo $tbl_pension_payroll->Createdby->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pension_payroll->SortUrl($tbl_pension_payroll->Createdby) ?>',1);"><div id="elh_tbl_pension_payroll_Createdby" class="tbl_pension_payroll_Createdby">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pension_payroll->Createdby->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pension_payroll->Createdby->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pension_payroll->Createdby->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pension_payroll->CreatedDate->Visible) { // CreatedDate ?>
	<?php if ($tbl_pension_payroll->SortUrl($tbl_pension_payroll->CreatedDate) == "") { ?>
		<td><div id="elh_tbl_pension_payroll_CreatedDate" class="tbl_pension_payroll_CreatedDate"><div class="ewTableHeaderCaption"><?php echo $tbl_pension_payroll->CreatedDate->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pension_payroll->SortUrl($tbl_pension_payroll->CreatedDate) ?>',1);"><div id="elh_tbl_pension_payroll_CreatedDate" class="tbl_pension_payroll_CreatedDate">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pension_payroll->CreatedDate->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pension_payroll->CreatedDate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pension_payroll->CreatedDate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pension_payroll->UpdatedBy->Visible) { // UpdatedBy ?>
	<?php if ($tbl_pension_payroll->SortUrl($tbl_pension_payroll->UpdatedBy) == "") { ?>
		<td><div id="elh_tbl_pension_payroll_UpdatedBy" class="tbl_pension_payroll_UpdatedBy"><div class="ewTableHeaderCaption"><?php echo $tbl_pension_payroll->UpdatedBy->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pension_payroll->SortUrl($tbl_pension_payroll->UpdatedBy) ?>',1);"><div id="elh_tbl_pension_payroll_UpdatedBy" class="tbl_pension_payroll_UpdatedBy">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pension_payroll->UpdatedBy->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pension_payroll->UpdatedBy->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pension_payroll->UpdatedBy->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_pension_payroll->UpdatedDate->Visible) { // UpdatedDate ?>
	<?php if ($tbl_pension_payroll->SortUrl($tbl_pension_payroll->UpdatedDate) == "") { ?>
		<td><div id="elh_tbl_pension_payroll_UpdatedDate" class="tbl_pension_payroll_UpdatedDate"><div class="ewTableHeaderCaption"><?php echo $tbl_pension_payroll->UpdatedDate->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_pension_payroll->SortUrl($tbl_pension_payroll->UpdatedDate) ?>',1);"><div id="elh_tbl_pension_payroll_UpdatedDate" class="tbl_pension_payroll_UpdatedDate">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_pension_payroll->UpdatedDate->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_pension_payroll->UpdatedDate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_pension_payroll->UpdatedDate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$tbl_pension_payroll_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($tbl_pension_payroll->ExportAll && $tbl_pension_payroll->Export <> "") {
	$tbl_pension_payroll_list->StopRec = $tbl_pension_payroll_list->TotalRecs;
} else {

	// Set the last record to display
	if ($tbl_pension_payroll_list->TotalRecs > $tbl_pension_payroll_list->StartRec + $tbl_pension_payroll_list->DisplayRecs - 1)
		$tbl_pension_payroll_list->StopRec = $tbl_pension_payroll_list->StartRec + $tbl_pension_payroll_list->DisplayRecs - 1;
	else
		$tbl_pension_payroll_list->StopRec = $tbl_pension_payroll_list->TotalRecs;
}
$tbl_pension_payroll_list->RecCnt = $tbl_pension_payroll_list->StartRec - 1;
if ($tbl_pension_payroll_list->Recordset && !$tbl_pension_payroll_list->Recordset->EOF) {
	$tbl_pension_payroll_list->Recordset->MoveFirst();
	if (!$bSelectLimit && $tbl_pension_payroll_list->StartRec > 1)
		$tbl_pension_payroll_list->Recordset->Move($tbl_pension_payroll_list->StartRec - 1);
} elseif (!$tbl_pension_payroll->AllowAddDeleteRow && $tbl_pension_payroll_list->StopRec == 0) {
	$tbl_pension_payroll_list->StopRec = $tbl_pension_payroll->GridAddRowCount;
}

// Initialize aggregate
$tbl_pension_payroll->RowType = EW_ROWTYPE_AGGREGATEINIT;
$tbl_pension_payroll->ResetAttrs();
$tbl_pension_payroll_list->RenderRow();
while ($tbl_pension_payroll_list->RecCnt < $tbl_pension_payroll_list->StopRec) {
	$tbl_pension_payroll_list->RecCnt++;
	if (intval($tbl_pension_payroll_list->RecCnt) >= intval($tbl_pension_payroll_list->StartRec)) {
		$tbl_pension_payroll_list->RowCnt++;

		// Set up key count
		$tbl_pension_payroll_list->KeyCount = $tbl_pension_payroll_list->RowIndex;

		// Init row class and style
		$tbl_pension_payroll->ResetAttrs();
		$tbl_pension_payroll->CssClass = "";
		if ($tbl_pension_payroll->CurrentAction == "gridadd") {
		} else {
			$tbl_pension_payroll_list->LoadRowValues($tbl_pension_payroll_list->Recordset); // Load row values
		}
		$tbl_pension_payroll->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$tbl_pension_payroll->RowAttrs = array_merge($tbl_pension_payroll->RowAttrs, array('data-rowindex'=>$tbl_pension_payroll_list->RowCnt, 'id'=>'r' . $tbl_pension_payroll_list->RowCnt . '_tbl_pension_payroll', 'data-rowtype'=>$tbl_pension_payroll->RowType));

		// Render row
		$tbl_pension_payroll_list->RenderRow();

		// Render list options
		$tbl_pension_payroll_list->RenderListOptions();
?>
	<tr<?php echo $tbl_pension_payroll->RowAttributes() ?>>
<?php

// Render list options (body, left)
$tbl_pension_payroll_list->ListOptions->Render("body", "left", $tbl_pension_payroll_list->RowCnt);
?>
	<?php if ($tbl_pension_payroll->PayrollID->Visible) { // PayrollID ?>
		<td<?php echo $tbl_pension_payroll->PayrollID->CellAttributes() ?>>
<span<?php echo $tbl_pension_payroll->PayrollID->ViewAttributes() ?>>
<?php echo $tbl_pension_payroll->PayrollID->ListViewValue() ?></span>
<a id="<?php echo $tbl_pension_payroll_list->PageObjName . "_row_" . $tbl_pension_payroll_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pension_payroll->PensionerID->Visible) { // PensionerID ?>
		<td<?php echo $tbl_pension_payroll->PensionerID->CellAttributes() ?>>
<span<?php echo $tbl_pension_payroll->PensionerID->ViewAttributes() ?>>
<?php echo $tbl_pension_payroll->PensionerID->ListViewValue() ?></span>
<a id="<?php echo $tbl_pension_payroll_list->PageObjName . "_row_" . $tbl_pension_payroll_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pension_payroll->PayrollYear->Visible) { // PayrollYear ?>
		<td<?php echo $tbl_pension_payroll->PayrollYear->CellAttributes() ?>>
<span<?php echo $tbl_pension_payroll->PayrollYear->ViewAttributes() ?>>
<?php echo $tbl_pension_payroll->PayrollYear->ListViewValue() ?></span>
<a id="<?php echo $tbl_pension_payroll_list->PageObjName . "_row_" . $tbl_pension_payroll_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pension_payroll->cMonth->Visible) { // cMonth ?>
		<td<?php echo $tbl_pension_payroll->cMonth->CellAttributes() ?>>
<span<?php echo $tbl_pension_payroll->cMonth->ViewAttributes() ?>>
<?php echo $tbl_pension_payroll->cMonth->ListViewValue() ?></span>
<a id="<?php echo $tbl_pension_payroll_list->PageObjName . "_row_" . $tbl_pension_payroll_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pension_payroll->amount->Visible) { // amount ?>
		<td<?php echo $tbl_pension_payroll->amount->CellAttributes() ?>>
<span<?php echo $tbl_pension_payroll->amount->ViewAttributes() ?>>
<?php echo $tbl_pension_payroll->amount->ListViewValue() ?></span>
<a id="<?php echo $tbl_pension_payroll_list->PageObjName . "_row_" . $tbl_pension_payroll_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pension_payroll->paymentmodeID->Visible) { // paymentmodeID ?>
		<td<?php echo $tbl_pension_payroll->paymentmodeID->CellAttributes() ?>>
<span<?php echo $tbl_pension_payroll->paymentmodeID->ViewAttributes() ?>>
<?php echo $tbl_pension_payroll->paymentmodeID->ListViewValue() ?></span>
<a id="<?php echo $tbl_pension_payroll_list->PageObjName . "_row_" . $tbl_pension_payroll_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pension_payroll->Approved->Visible) { // Approved ?>
		<td<?php echo $tbl_pension_payroll->Approved->CellAttributes() ?>>
<span<?php echo $tbl_pension_payroll->Approved->ViewAttributes() ?>>
<?php echo $tbl_pension_payroll->Approved->ListViewValue() ?></span>
<a id="<?php echo $tbl_pension_payroll_list->PageObjName . "_row_" . $tbl_pension_payroll_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pension_payroll->Claimed->Visible) { // Claimed ?>
		<td<?php echo $tbl_pension_payroll->Claimed->CellAttributes() ?>>
<span<?php echo $tbl_pension_payroll->Claimed->ViewAttributes() ?>>
<?php echo $tbl_pension_payroll->Claimed->ListViewValue() ?></span>
<a id="<?php echo $tbl_pension_payroll_list->PageObjName . "_row_" . $tbl_pension_payroll_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pension_payroll->Createdby->Visible) { // Createdby ?>
		<td<?php echo $tbl_pension_payroll->Createdby->CellAttributes() ?>>
<span<?php echo $tbl_pension_payroll->Createdby->ViewAttributes() ?>>
<?php echo $tbl_pension_payroll->Createdby->ListViewValue() ?></span>
<a id="<?php echo $tbl_pension_payroll_list->PageObjName . "_row_" . $tbl_pension_payroll_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pension_payroll->CreatedDate->Visible) { // CreatedDate ?>
		<td<?php echo $tbl_pension_payroll->CreatedDate->CellAttributes() ?>>
<span<?php echo $tbl_pension_payroll->CreatedDate->ViewAttributes() ?>>
<?php echo $tbl_pension_payroll->CreatedDate->ListViewValue() ?></span>
<a id="<?php echo $tbl_pension_payroll_list->PageObjName . "_row_" . $tbl_pension_payroll_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pension_payroll->UpdatedBy->Visible) { // UpdatedBy ?>
		<td<?php echo $tbl_pension_payroll->UpdatedBy->CellAttributes() ?>>
<span<?php echo $tbl_pension_payroll->UpdatedBy->ViewAttributes() ?>>
<?php echo $tbl_pension_payroll->UpdatedBy->ListViewValue() ?></span>
<a id="<?php echo $tbl_pension_payroll_list->PageObjName . "_row_" . $tbl_pension_payroll_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_pension_payroll->UpdatedDate->Visible) { // UpdatedDate ?>
		<td<?php echo $tbl_pension_payroll->UpdatedDate->CellAttributes() ?>>
<span<?php echo $tbl_pension_payroll->UpdatedDate->ViewAttributes() ?>>
<?php echo $tbl_pension_payroll->UpdatedDate->ListViewValue() ?></span>
<a id="<?php echo $tbl_pension_payroll_list->PageObjName . "_row_" . $tbl_pension_payroll_list->RowCnt ?>"></a></td>
	<?php } ?>
<?php

// Render list options (body, right)
$tbl_pension_payroll_list->ListOptions->Render("body", "right", $tbl_pension_payroll_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($tbl_pension_payroll->CurrentAction <> "gridadd")
		$tbl_pension_payroll_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($tbl_pension_payroll->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($tbl_pension_payroll_list->Recordset)
	$tbl_pension_payroll_list->Recordset->Close();
?>
<?php if ($tbl_pension_payroll_list->TotalRecs > 0) { ?>
<div class="ewGridLowerPanel">
<?php if ($tbl_pension_payroll->CurrentAction <> "gridadd" && $tbl_pension_payroll->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($tbl_pension_payroll_list->Pager)) $tbl_pension_payroll_list->Pager = new cNumericPager($tbl_pension_payroll_list->StartRec, $tbl_pension_payroll_list->DisplayRecs, $tbl_pension_payroll_list->TotalRecs, $tbl_pension_payroll_list->RecRange) ?>
<?php if ($tbl_pension_payroll_list->Pager->RecordCount > 0) { ?>
<table cellspacing="0" class="ewStdTable"><tbody><tr><td>
<div class="pagination"><ul>
	<?php if ($tbl_pension_payroll_list->Pager->FirstButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_pension_payroll_list->PageUrl() ?>start=<?php echo $tbl_pension_payroll_list->Pager->FirstButton->Start ?>"><?php echo $Language->Phrase("PagerFirst") ?></a></li>
	<?php } ?>
	<?php if ($tbl_pension_payroll_list->Pager->PrevButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_pension_payroll_list->PageUrl() ?>start=<?php echo $tbl_pension_payroll_list->Pager->PrevButton->Start ?>"><?php echo $Language->Phrase("PagerPrevious") ?></a></li>
	<?php } ?>
	<?php foreach ($tbl_pension_payroll_list->Pager->Items as $PagerItem) { ?>
		<li<?php if (!$PagerItem->Enabled) { echo " class=\" active\""; } ?>><a href="<?php if ($PagerItem->Enabled) { echo $tbl_pension_payroll_list->PageUrl() . "start=" . $PagerItem->Start; } else { echo "#"; } ?>"><?php echo $PagerItem->Text ?></a></li>
	<?php } ?>
	<?php if ($tbl_pension_payroll_list->Pager->NextButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_pension_payroll_list->PageUrl() ?>start=<?php echo $tbl_pension_payroll_list->Pager->NextButton->Start ?>"><?php echo $Language->Phrase("PagerNext") ?></a></li>
	<?php } ?>
	<?php if ($tbl_pension_payroll_list->Pager->LastButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_pension_payroll_list->PageUrl() ?>start=<?php echo $tbl_pension_payroll_list->Pager->LastButton->Start ?>"><?php echo $Language->Phrase("PagerLast") ?></a></li>
	<?php } ?>
</ul></div>
</td>
<td>
	<?php if ($tbl_pension_payroll_list->Pager->ButtonCount > 0) { ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php } ?>
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $tbl_pension_payroll_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $tbl_pension_payroll_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $tbl_pension_payroll_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($tbl_pension_payroll_list->SearchWhere == "0=101") { ?>
	<p><?php echo $Language->Phrase("EnterSearchCriteria") ?></p>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoRecord") ?></p>
	<?php } ?>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoPermission") ?></p>
	<?php } ?>
<?php } ?>
</td>
<?php if ($tbl_pension_payroll_list->TotalRecs > 0) { ?>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
<input type="hidden" name="t" value="tbl_pension_payroll">
<select name="<?php echo EW_TABLE_REC_PER_PAGE ?>" class="input-small" onchange="this.form.submit();">
<option value="20"<?php if ($tbl_pension_payroll_list->DisplayRecs == 20) { ?> selected="selected"<?php } ?>>20</option>
<option value="40"<?php if ($tbl_pension_payroll_list->DisplayRecs == 40) { ?> selected="selected"<?php } ?>>40</option>
<option value="60"<?php if ($tbl_pension_payroll_list->DisplayRecs == 60) { ?> selected="selected"<?php } ?>>60</option>
<option value="ALL"<?php if ($tbl_pension_payroll->getRecordsPerPage() == -1) { ?> selected="selected"<?php } ?>><?php echo $Language->Phrase("AllRecords") ?></option>
</select>
</td>
<?php } ?>
</tr></table>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($tbl_pension_payroll_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
</div>
<?php } ?>
</td></tr></table>
<script type="text/javascript">
ftbl_pension_payrolllistsrch.Init();
ftbl_pension_payrolllist.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$tbl_pension_payroll_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$tbl_pension_payroll_list->Page_Terminate();
?>
