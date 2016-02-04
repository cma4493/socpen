<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "tbl_userinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$tbl_user_list = NULL; // Initialize page object first

class ctbl_user_list extends ctbl_user {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Table name
	var $TableName = 'tbl_user';

	// Page object name
	var $PageObjName = 'tbl_user_list';

	// Grid form hidden field names
	var $FormName = 'ftbl_userlist';
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

		// Table object (tbl_user)
		if (!isset($GLOBALS["tbl_user"])) {
			$GLOBALS["tbl_user"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["tbl_user"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "tbl_useradd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "tbl_userdelete.php";
		$this->MultiUpdateUrl = "tbl_userupdate.php";

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'tbl_user', TRUE);

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
		if ($Security->IsLoggedIn() && strval($Security->CurrentUserID()) == "") {
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate();
		}

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
		$this->uid->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
			$this->uid->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->uid->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Advanced search WHERE clause based on QueryString
	function AdvancedSearchWhere() {
		global $Security;
		$sWhere = "";
		if (!$Security->CanSearch()) return "";
		$this->BuildSearchSql($sWhere, $this->uid, FALSE); // uid
		$this->BuildSearchSql($sWhere, $this->username, FALSE); // username
		$this->BuildSearchSql($sWhere, $this->password, FALSE); // password
		$this->BuildSearchSql($sWhere, $this->_email, FALSE); // email
		$this->BuildSearchSql($sWhere, $this->firstname, FALSE); // firstname
		$this->BuildSearchSql($sWhere, $this->middlename, FALSE); // middlename
		$this->BuildSearchSql($sWhere, $this->surname, FALSE); // surname
		$this->BuildSearchSql($sWhere, $this->extensionname, FALSE); // extensionname
		$this->BuildSearchSql($sWhere, $this->position, FALSE); // position
		$this->BuildSearchSql($sWhere, $this->designation, FALSE); // designation
		$this->BuildSearchSql($sWhere, $this->region_code, FALSE); // region_code
		$this->BuildSearchSql($sWhere, $this->user_level, FALSE); // user_level
		$this->BuildSearchSql($sWhere, $this->contact_no, FALSE); // contact_no
		$this->BuildSearchSql($sWhere, $this->activated, FALSE); // activated
		$this->BuildSearchSql($sWhere, $this->profile, FALSE); // profile

		// Set up search parm
		if ($sWhere <> "") {
			$this->Command = "search";
		}
		if ($this->Command == "search") {
			$this->uid->AdvancedSearch->Save(); // uid
			$this->username->AdvancedSearch->Save(); // username
			$this->password->AdvancedSearch->Save(); // password
			$this->_email->AdvancedSearch->Save(); // email
			$this->firstname->AdvancedSearch->Save(); // firstname
			$this->middlename->AdvancedSearch->Save(); // middlename
			$this->surname->AdvancedSearch->Save(); // surname
			$this->extensionname->AdvancedSearch->Save(); // extensionname
			$this->position->AdvancedSearch->Save(); // position
			$this->designation->AdvancedSearch->Save(); // designation
			$this->region_code->AdvancedSearch->Save(); // region_code
			$this->user_level->AdvancedSearch->Save(); // user_level
			$this->contact_no->AdvancedSearch->Save(); // contact_no
			$this->activated->AdvancedSearch->Save(); // activated
			$this->profile->AdvancedSearch->Save(); // profile
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
		$this->BuildBasicSearchSQL($sWhere, $this->username, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->password, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->_email, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->firstname, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->middlename, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->surname, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->extensionname, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->position, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->designation, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->contact_no, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->profile, $Keyword);
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
		if ($this->uid->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->username->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->password->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->_email->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->firstname->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->middlename->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->surname->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->extensionname->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->position->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->designation->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->region_code->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->user_level->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->contact_no->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->activated->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->profile->AdvancedSearch->IssetSession())
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
		$this->uid->AdvancedSearch->UnsetSession();
		$this->username->AdvancedSearch->UnsetSession();
		$this->password->AdvancedSearch->UnsetSession();
		$this->_email->AdvancedSearch->UnsetSession();
		$this->firstname->AdvancedSearch->UnsetSession();
		$this->middlename->AdvancedSearch->UnsetSession();
		$this->surname->AdvancedSearch->UnsetSession();
		$this->extensionname->AdvancedSearch->UnsetSession();
		$this->position->AdvancedSearch->UnsetSession();
		$this->designation->AdvancedSearch->UnsetSession();
		$this->region_code->AdvancedSearch->UnsetSession();
		$this->user_level->AdvancedSearch->UnsetSession();
		$this->contact_no->AdvancedSearch->UnsetSession();
		$this->activated->AdvancedSearch->UnsetSession();
		$this->profile->AdvancedSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore basic search values
		$this->BasicSearch->Load();

		// Restore advanced search values
		$this->uid->AdvancedSearch->Load();
		$this->username->AdvancedSearch->Load();
		$this->password->AdvancedSearch->Load();
		$this->_email->AdvancedSearch->Load();
		$this->firstname->AdvancedSearch->Load();
		$this->middlename->AdvancedSearch->Load();
		$this->surname->AdvancedSearch->Load();
		$this->extensionname->AdvancedSearch->Load();
		$this->position->AdvancedSearch->Load();
		$this->designation->AdvancedSearch->Load();
		$this->region_code->AdvancedSearch->Load();
		$this->user_level->AdvancedSearch->Load();
		$this->contact_no->AdvancedSearch->Load();
		$this->activated->AdvancedSearch->Load();
		$this->profile->AdvancedSearch->Load();
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->uid); // uid
			$this->UpdateSort($this->username); // username
			$this->UpdateSort($this->password); // password
			$this->UpdateSort($this->_email); // email
			$this->UpdateSort($this->firstname); // firstname
			$this->UpdateSort($this->middlename); // middlename
			$this->UpdateSort($this->surname); // surname
			$this->UpdateSort($this->extensionname); // extensionname
			$this->UpdateSort($this->position); // position
			$this->UpdateSort($this->designation); // designation
			$this->UpdateSort($this->region_code); // region_code
			$this->UpdateSort($this->user_level); // user_level
			$this->UpdateSort($this->contact_no); // contact_no
			$this->UpdateSort($this->activated); // activated
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
				$this->uid->setSort("");
				$this->username->setSort("");
				$this->password->setSort("");
				$this->_email->setSort("");
				$this->firstname->setSort("");
				$this->middlename->setSort("");
				$this->surname->setSort("");
				$this->extensionname->setSort("");
				$this->position->setSort("");
				$this->designation->setSort("");
				$this->region_code->setSort("");
				$this->user_level->setSort("");
				$this->contact_no->setSort("");
				$this->activated->setSort("");
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
		$item->Visible = $Security->CanEdit();
		$item->OnLeft = TRUE;

		// "copy"
		$item = &$this->ListOptions->Add("copy");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanAdd();
		$item->OnLeft = TRUE;

		// "checkbox"
		$item = &$this->ListOptions->Add("checkbox");
		$item->Visible = $Security->CanDelete();
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
		if ($Security->CanView() && $this->ShowOptionLink('view'))
			$oListOpt->Body = "<a class=\"blue\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("ViewLink")) . "\" href=\"" . ew_HtmlEncode($this->ViewUrl) . "\">" . $Language->Phrase("ViewLink") . "<i class=\"icon-zoom-in bigger-130\"></i></a>";
		else
			$oListOpt->Body = "";

		// "edit"
		$oListOpt = &$this->ListOptions->Items["edit"];
		if ($Security->CanEdit() && $this->ShowOptionLink('edit')) {
			$oListOpt->Body = "<a class=\"green\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("EditLink")) . "\" href=\"" . ew_HtmlEncode($this->EditUrl) . "\">" . $Language->Phrase("EditLink") . "<i class=\"icon-pencil bigger-130\"></i></a>";
		} else {
			$oListOpt->Body = "";
		}

		// "copy"
		$oListOpt = &$this->ListOptions->Items["copy"];
		if ($Security->CanAdd() && $this->ShowOptionLink('add')) {
			$oListOpt->Body = "<a class=\"blue\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("CopyLink")) . "\" href=\"" . ew_HtmlEncode($this->CopyUrl) . "\">" . $Language->Phrase("CopyLink") . "<i class=\"icon-copy bigger-130\"></i></a>";
		} else {
			$oListOpt->Body = "";
		}

		// "checkbox"
		$oListOpt = &$this->ListOptions->Items["checkbox"];
		$oListOpt->Body = "<label class=\"checkbox\"><input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->uid->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event, this);'></label>";
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
		$item->Visible = ($this->AddUrl <> "" && $Security->CanAdd());
		$option = $options["action"];

		// Add multi delete
		$item = &$option->Add("multidelete");
		$item->Body = "<a class=\"btn btn-purple btn-sm\" href=\"\" onclick=\"ew_SubmitSelected(document.ftbl_userlist, '" . $this->MultiDeleteUrl . "', ewLanguage.Phrase('DeleteMultiConfirmMsg'));return false;\">" . $Language->Phrase("DeleteSelectedLink") . "</a>";
		$item->Visible = ($Security->CanDelete());

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
				$item->Body = "<a class=\"btn btn-warning btn-sm\" href=\"\" onclick=\"ew_SubmitSelected(document.ftbl_userlist, '" . ew_CurrentUrl() . "', null, '" . $action . "');return false;\">" . $name . "</a>";
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
		// uid

		$this->uid->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_uid"]);
		if ($this->uid->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->uid->AdvancedSearch->SearchOperator = @$_GET["z_uid"];

		// username
		$this->username->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_username"]);
		if ($this->username->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->username->AdvancedSearch->SearchOperator = @$_GET["z_username"];

		// password
		$this->password->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_password"]);
		if ($this->password->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->password->AdvancedSearch->SearchOperator = @$_GET["z_password"];

		// email
		$this->_email->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x__email"]);
		if ($this->_email->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->_email->AdvancedSearch->SearchOperator = @$_GET["z__email"];

		// firstname
		$this->firstname->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_firstname"]);
		if ($this->firstname->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->firstname->AdvancedSearch->SearchOperator = @$_GET["z_firstname"];

		// middlename
		$this->middlename->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_middlename"]);
		if ($this->middlename->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->middlename->AdvancedSearch->SearchOperator = @$_GET["z_middlename"];

		// surname
		$this->surname->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_surname"]);
		if ($this->surname->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->surname->AdvancedSearch->SearchOperator = @$_GET["z_surname"];

		// extensionname
		$this->extensionname->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_extensionname"]);
		if ($this->extensionname->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->extensionname->AdvancedSearch->SearchOperator = @$_GET["z_extensionname"];

		// position
		$this->position->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_position"]);
		if ($this->position->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->position->AdvancedSearch->SearchOperator = @$_GET["z_position"];

		// designation
		$this->designation->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_designation"]);
		if ($this->designation->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->designation->AdvancedSearch->SearchOperator = @$_GET["z_designation"];

		// region_code
		$this->region_code->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_region_code"]);
		if ($this->region_code->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->region_code->AdvancedSearch->SearchOperator = @$_GET["z_region_code"];

		// user_level
		$this->user_level->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_user_level"]);
		if ($this->user_level->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->user_level->AdvancedSearch->SearchOperator = @$_GET["z_user_level"];

		// contact_no
		$this->contact_no->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_contact_no"]);
		if ($this->contact_no->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->contact_no->AdvancedSearch->SearchOperator = @$_GET["z_contact_no"];

		// activated
		$this->activated->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_activated"]);
		if ($this->activated->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->activated->AdvancedSearch->SearchOperator = @$_GET["z_activated"];

		// profile
		$this->profile->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_profile"]);
		if ($this->profile->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->profile->AdvancedSearch->SearchOperator = @$_GET["z_profile"];
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
		$this->uid->setDbValue($rs->fields('uid'));
		$this->username->setDbValue($rs->fields('username'));
		$this->password->setDbValue($rs->fields('password'));
		$this->_email->setDbValue($rs->fields('email'));
		$this->firstname->setDbValue($rs->fields('firstname'));
		$this->middlename->setDbValue($rs->fields('middlename'));
		$this->surname->setDbValue($rs->fields('surname'));
		$this->extensionname->setDbValue($rs->fields('extensionname'));
		$this->position->setDbValue($rs->fields('position'));
		$this->designation->setDbValue($rs->fields('designation'));
		$this->region_code->setDbValue($rs->fields('region_code'));
		$this->user_level->setDbValue($rs->fields('user_level'));
		$this->contact_no->setDbValue($rs->fields('contact_no'));
		$this->activated->setDbValue($rs->fields('activated'));
		$this->profile->setDbValue($rs->fields('profile'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->uid->DbValue = $row['uid'];
		$this->username->DbValue = $row['username'];
		$this->password->DbValue = $row['password'];
		$this->_email->DbValue = $row['email'];
		$this->firstname->DbValue = $row['firstname'];
		$this->middlename->DbValue = $row['middlename'];
		$this->surname->DbValue = $row['surname'];
		$this->extensionname->DbValue = $row['extensionname'];
		$this->position->DbValue = $row['position'];
		$this->designation->DbValue = $row['designation'];
		$this->region_code->DbValue = $row['region_code'];
		$this->user_level->DbValue = $row['user_level'];
		$this->contact_no->DbValue = $row['contact_no'];
		$this->activated->DbValue = $row['activated'];
		$this->profile->DbValue = $row['profile'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("uid")) <> "")
			$this->uid->CurrentValue = $this->getKey("uid"); // uid
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
		// uid
		// username
		// password
		// email
		// firstname
		// middlename
		// surname
		// extensionname
		// position
		// designation
		// region_code
		// user_level
		// contact_no
		// activated
		// profile

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// uid
			$this->uid->ViewValue = $this->uid->CurrentValue;
			$this->uid->ViewCustomAttributes = "";

			// username
			$this->username->ViewValue = $this->username->CurrentValue;
			$this->username->ViewCustomAttributes = "";

			// password
			$this->password->ViewValue = "********";
			$this->password->ViewCustomAttributes = "";

			// email
			$this->_email->ViewValue = $this->_email->CurrentValue;
			$this->_email->ViewCustomAttributes = "";

			// firstname
			$this->firstname->ViewValue = $this->firstname->CurrentValue;
			$this->firstname->ViewCustomAttributes = "";

			// middlename
			$this->middlename->ViewValue = $this->middlename->CurrentValue;
			$this->middlename->ViewCustomAttributes = "";

			// surname
			$this->surname->ViewValue = $this->surname->CurrentValue;
			$this->surname->ViewCustomAttributes = "";

			// extensionname
			$this->extensionname->ViewValue = $this->extensionname->CurrentValue;
			$this->extensionname->ViewCustomAttributes = "";

			// position
			$this->position->ViewValue = $this->position->CurrentValue;
			$this->position->ViewCustomAttributes = "";

			// designation
			$this->designation->ViewValue = $this->designation->CurrentValue;
			$this->designation->ViewCustomAttributes = "";

			// region_code
			if (strval($this->region_code->CurrentValue) <> "") {
				$sFilterWrk = "`region_code`" . ew_SearchString("=", $this->region_code->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `region_code`, `region_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_regions`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->region_code, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `region_code` ASC";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->region_code->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->region_code->ViewValue = $this->region_code->CurrentValue;
				}
			} else {
				$this->region_code->ViewValue = NULL;
			}
			$this->region_code->ViewCustomAttributes = "";

			// user_level
			if ($Security->CanAdmin()) { // System admin
			if (strval($this->user_level->CurrentValue) <> "") {
				$sFilterWrk = "`userlevelid`" . ew_SearchString("=", $this->user_level->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `userlevelid`, `userlevelname` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `userlevels`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->user_level, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->user_level->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->user_level->ViewValue = $this->user_level->CurrentValue;
				}
			} else {
				$this->user_level->ViewValue = NULL;
			}
			} else {
				$this->user_level->ViewValue = "********";
			}
			$this->user_level->ViewCustomAttributes = "";

			// contact_no
			$this->contact_no->ViewValue = $this->contact_no->CurrentValue;
			$this->contact_no->ViewCustomAttributes = "";

			// activated
			if (strval($this->activated->CurrentValue) <> "") {
				switch ($this->activated->CurrentValue) {
					case $this->activated->FldTagValue(1):
						$this->activated->ViewValue = $this->activated->FldTagCaption(1) <> "" ? $this->activated->FldTagCaption(1) : $this->activated->CurrentValue;
						break;
					case $this->activated->FldTagValue(2):
						$this->activated->ViewValue = $this->activated->FldTagCaption(2) <> "" ? $this->activated->FldTagCaption(2) : $this->activated->CurrentValue;
						break;
					default:
						$this->activated->ViewValue = $this->activated->CurrentValue;
				}
			} else {
				$this->activated->ViewValue = NULL;
			}
			$this->activated->ViewCustomAttributes = "";

			// uid
			$this->uid->LinkCustomAttributes = "";
			$this->uid->HrefValue = "";
			$this->uid->TooltipValue = "";

			// username
			$this->username->LinkCustomAttributes = "";
			$this->username->HrefValue = "";
			$this->username->TooltipValue = "";

			// password
			$this->password->LinkCustomAttributes = "";
			$this->password->HrefValue = "";
			$this->password->TooltipValue = "";

			// email
			$this->_email->LinkCustomAttributes = "";
			$this->_email->HrefValue = "";
			$this->_email->TooltipValue = "";

			// firstname
			$this->firstname->LinkCustomAttributes = "";
			$this->firstname->HrefValue = "";
			$this->firstname->TooltipValue = "";

			// middlename
			$this->middlename->LinkCustomAttributes = "";
			$this->middlename->HrefValue = "";
			$this->middlename->TooltipValue = "";

			// surname
			$this->surname->LinkCustomAttributes = "";
			$this->surname->HrefValue = "";
			$this->surname->TooltipValue = "";

			// extensionname
			$this->extensionname->LinkCustomAttributes = "";
			$this->extensionname->HrefValue = "";
			$this->extensionname->TooltipValue = "";

			// position
			$this->position->LinkCustomAttributes = "";
			$this->position->HrefValue = "";
			$this->position->TooltipValue = "";

			// designation
			$this->designation->LinkCustomAttributes = "";
			$this->designation->HrefValue = "";
			$this->designation->TooltipValue = "";

			// region_code
			$this->region_code->LinkCustomAttributes = "";
			$this->region_code->HrefValue = "";
			$this->region_code->TooltipValue = "";

			// user_level
			$this->user_level->LinkCustomAttributes = "";
			$this->user_level->HrefValue = "";
			$this->user_level->TooltipValue = "";

			// contact_no
			$this->contact_no->LinkCustomAttributes = "";
			$this->contact_no->HrefValue = "";
			$this->contact_no->TooltipValue = "";

			// activated
			$this->activated->LinkCustomAttributes = "";
			$this->activated->HrefValue = "";
			$this->activated->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// uid
			$this->uid->EditCustomAttributes = "";
			if (!$Security->IsAdmin() && $Security->IsLoggedIn()) { // Non system admin
			$sFilterWrk = "";
			$sFilterWrk = $GLOBALS["tbl_user"]->AddParentUserIDFilter("", $this->uid->CurrentValue);
			$sSqlWrk = "SELECT `uid`, `uid` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `tbl_user`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->uid, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->uid->EditValue = $arwrk;
			} elseif (!$Security->IsAdmin() && $Security->IsLoggedIn() && !$this->UserIDAllow($this->CurrentAction)) { // Non system admin
			$sFilterWrk = "";
			$sFilterWrk = $GLOBALS["tbl_user"]->AddUserIDFilter("");
			$sSqlWrk = "SELECT `uid`, `uid` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `tbl_user`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->uid, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->uid->EditValue = $arwrk;
			} else {
			$this->uid->EditValue = ew_HtmlEncode($this->uid->AdvancedSearch->SearchValue);
			$this->uid->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->uid->FldCaption()));
			}

			// username
			$this->username->EditCustomAttributes = "";
			$this->username->EditValue = ew_HtmlEncode($this->username->AdvancedSearch->SearchValue);
			$this->username->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->username->FldCaption()));

			// password
			$this->password->EditCustomAttributes = "";
			$this->password->EditValue = ew_HtmlEncode($this->password->AdvancedSearch->SearchValue);

			// email
			$this->_email->EditCustomAttributes = "";
			$this->_email->EditValue = ew_HtmlEncode($this->_email->AdvancedSearch->SearchValue);
			$this->_email->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->_email->FldCaption()));

			// firstname
			$this->firstname->EditCustomAttributes = "";
			$this->firstname->EditValue = ew_HtmlEncode($this->firstname->AdvancedSearch->SearchValue);
			$this->firstname->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->firstname->FldCaption()));

			// middlename
			$this->middlename->EditCustomAttributes = "";
			$this->middlename->EditValue = ew_HtmlEncode($this->middlename->AdvancedSearch->SearchValue);
			$this->middlename->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->middlename->FldCaption()));

			// surname
			$this->surname->EditCustomAttributes = "";
			$this->surname->EditValue = ew_HtmlEncode($this->surname->AdvancedSearch->SearchValue);
			$this->surname->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->surname->FldCaption()));

			// extensionname
			$this->extensionname->EditCustomAttributes = "";
			$this->extensionname->EditValue = ew_HtmlEncode($this->extensionname->AdvancedSearch->SearchValue);
			$this->extensionname->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->extensionname->FldCaption()));

			// position
			$this->position->EditCustomAttributes = "";
			$this->position->EditValue = ew_HtmlEncode($this->position->AdvancedSearch->SearchValue);
			$this->position->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->position->FldCaption()));

			// designation
			$this->designation->EditCustomAttributes = "";
			$this->designation->EditValue = ew_HtmlEncode($this->designation->AdvancedSearch->SearchValue);
			$this->designation->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->designation->FldCaption()));

			// region_code
			$this->region_code->EditCustomAttributes = "";
			if (trim(strval($this->region_code->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`region_code`" . ew_SearchString("=", $this->region_code->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `region_code`, `region_name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_regions`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->region_code, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `region_code` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->region_code->EditValue = $arwrk;

			// user_level
			$this->user_level->EditCustomAttributes = "";
			if (!$Security->CanAdmin()) { // System admin
				$this->user_level->EditValue = "********";
			} else {
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `userlevelid`, `userlevelname` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `userlevels`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->user_level, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->user_level->EditValue = $arwrk;
			}

			// contact_no
			$this->contact_no->EditCustomAttributes = "";
			$this->contact_no->EditValue = ew_HtmlEncode($this->contact_no->AdvancedSearch->SearchValue);
			$this->contact_no->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->contact_no->FldCaption()));

			// activated
			$this->activated->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->activated->FldTagValue(1), $this->activated->FldTagCaption(1) <> "" ? $this->activated->FldTagCaption(1) : $this->activated->FldTagValue(1));
			$arwrk[] = array($this->activated->FldTagValue(2), $this->activated->FldTagCaption(2) <> "" ? $this->activated->FldTagCaption(2) : $this->activated->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->activated->EditValue = $arwrk;
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
		$this->uid->AdvancedSearch->Load();
		$this->username->AdvancedSearch->Load();
		$this->password->AdvancedSearch->Load();
		$this->_email->AdvancedSearch->Load();
		$this->firstname->AdvancedSearch->Load();
		$this->middlename->AdvancedSearch->Load();
		$this->surname->AdvancedSearch->Load();
		$this->extensionname->AdvancedSearch->Load();
		$this->position->AdvancedSearch->Load();
		$this->designation->AdvancedSearch->Load();
		$this->region_code->AdvancedSearch->Load();
		$this->user_level->AdvancedSearch->Load();
		$this->contact_no->AdvancedSearch->Load();
		$this->activated->AdvancedSearch->Load();
		$this->profile->AdvancedSearch->Load();
	}

	// Show link optionally based on User ID
	function ShowOptionLink($id = "") {
		global $Security;
		if ($Security->IsLoggedIn() && !$Security->IsAdmin() && !$this->UserIDAllow($id))
			return $Security->IsValidUserID($this->uid->CurrentValue);
		return TRUE;
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
		$table = 'tbl_user';
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
if (!isset($tbl_user_list)) $tbl_user_list = new ctbl_user_list();

// Page init
$tbl_user_list->Page_Init();

// Page main
$tbl_user_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$tbl_user_list->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var tbl_user_list = new ew_Page("tbl_user_list");
tbl_user_list.PageID = "list"; // Page ID
var EW_PAGE_ID = tbl_user_list.PageID; // For backward compatibility

// Form object
var ftbl_userlist = new ew_Form("ftbl_userlist");
ftbl_userlist.FormKeyCountName = '<?php echo $tbl_user_list->FormKeyCountName ?>';

// Form_CustomValidate event
ftbl_userlist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftbl_userlist.ValidateRequired = true;
<?php } else { ?>
ftbl_userlist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
ftbl_userlist.Lists["x_region_code"] = {"LinkField":"x_region_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_region_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_userlist.Lists["x_user_level"] = {"LinkField":"x_userlevelid","Ajax":null,"AutoFill":false,"DisplayFields":["x_userlevelname","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
var ftbl_userlistsrch = new ew_Form("ftbl_userlistsrch");

// Validate function for search
ftbl_userlistsrch.Validate = function(fobj) {
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
ftbl_userlistsrch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftbl_userlistsrch.ValidateRequired = true; // Use JavaScript validation
<?php } else { ?>
ftbl_userlistsrch.ValidateRequired = false; // No JavaScript validation
<?php } ?>

// Dynamic selection lists
ftbl_userlistsrch.Lists["x_region_code"] = {"LinkField":"x_region_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_region_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_userlistsrch.Lists["x_user_level"] = {"LinkField":"x_userlevelid","Ajax":null,"AutoFill":false,"DisplayFields":["x_userlevelname","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php //$Breadcrumb->Render(); ?>
<?php if ($tbl_user_list->ExportOptions->Visible()) { ?>
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
		$tbl_user_list->TotalRecs = $tbl_user->SelectRecordCount();
	} else {
		if ($tbl_user_list->Recordset = $tbl_user_list->LoadRecordset())
			$tbl_user_list->TotalRecs = $tbl_user_list->Recordset->RecordCount();
	}
	$tbl_user_list->StartRec = 1;
	if ($tbl_user_list->DisplayRecs <= 0 || ($tbl_user->Export <> "" && $tbl_user->ExportAll)) // Display all records
		$tbl_user_list->DisplayRecs = $tbl_user_list->TotalRecs;
	if (!($tbl_user->Export <> "" && $tbl_user->ExportAll))
		$tbl_user_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$tbl_user_list->Recordset = $tbl_user_list->LoadRecordset($tbl_user_list->StartRec-1, $tbl_user_list->DisplayRecs);
$tbl_user_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($tbl_user->Export == "" && $tbl_user->CurrentAction == "") { ?>
<form name="ftbl_userlistsrch" id="ftbl_userlistsrch" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>">
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
	<div id="ftbl_userlistsrch_SearchPanel">
		<input type="hidden" name="cmd" value="search">
		<input type="hidden" name="t" value="tbl_user">
		<div class="ewBasicSearch">
<?php
if ($gsSearchError == "")
	$tbl_user_list->LoadAdvancedSearch(); // Load advanced search

// Render for search
$tbl_user->RowType = EW_ROWTYPE_SEARCH;

// Render row
$tbl_user->ResetAttrs();
$tbl_user_list->RenderRow();
?>
<div id="xsr_1" class="ewRow">
<?php if ($tbl_user->region_code->Visible) { // region_code ?>
	<span id="xsc_region_code" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_user->region_code->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_region_code" id="z_region_code" value="="></span>
		<span class="control-group ewSearchField">
<select data-field="x_region_code" id="x_region_code" name="x_region_code"<?php echo $tbl_user->region_code->EditAttributes() ?>>
<?php
if (is_array($tbl_user->region_code->EditValue)) {
	$arwrk = $tbl_user->region_code->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_user->region_code->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
$tbl_user->Lookup_Selecting($tbl_user->region_code, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `region_code` ASC";
?>
<input type="hidden" name="s_x_region_code" id="s_x_region_code" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`region_code` = {filter_value}"); ?>&t0=21">
</span>
	</span>
<?php } ?>
</div>
<div id="xsr_2" class="ewRow">
<?php if ($tbl_user->user_level->Visible) { // user_level ?>
	<span id="xsc_user_level" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_user->user_level->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_user_level" id="z_user_level" value="="></span>
		<span class="control-group ewSearchField">
<?php if (!$Security->IsAdmin() && $Security->IsLoggedIn()) { // Non system admin ?>
<?php echo $tbl_user->user_level->EditValue ?>
<?php } else { ?>
<select data-field="x_user_level" id="x_user_level" name="x_user_level"<?php echo $tbl_user->user_level->EditAttributes() ?>>
<?php
if (is_array($tbl_user->user_level->EditValue)) {
	$arwrk = $tbl_user->user_level->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_user->user_level->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<script type="text/javascript">
ftbl_userlistsrch.Lists["x_user_level"].Options = <?php echo (is_array($tbl_user->user_level->EditValue)) ? ew_ArrayToJson($tbl_user->user_level->EditValue, 1) : "[]" ?>;
</script>
<?php } ?>
</span>
	</span>
<?php } ?>
</div>
<div id="xsr_3" class="ewRow">
<?php if ($tbl_user->activated->Visible) { // activated ?>
	<span id="xsc_activated" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_user->activated->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_activated" id="z_activated" value="="></span>
		<span class="control-group ewSearchField">
<select data-field="x_activated" id="x_activated" name="x_activated"<?php echo $tbl_user->activated->EditAttributes() ?>>
<?php
if (is_array($tbl_user->activated->EditValue)) {
	$arwrk = $tbl_user->activated->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_user->activated->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<div id="xsr_4" class="row">
	<div class="col-xs-12 col-sm-4">
	<div class="input-group">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="form-control search-query" value="<?php echo ew_HtmlEncode($tbl_user_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo $Language->Phrase("Search") ?>">
	<span class="input-group-btn">
	<button class="btn btn-purple btn-sm" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $Language->Phrase("QuickSearchBtn") ?> <i class="icon-search icon-on-right bigger-110"></i></button>&nbsp;
	<a type="button" class="btn btn-success btn-sm" href="<?php echo $tbl_user_list->PageUrl() ?>cmd=reset">ShowAll <i class="icon-refresh icon-on-right bigger-110"></i></a>
	</span>
	</div>
	</div>
	<div class="btn-group ewButtonGroup">
	<!--<a class="btn ewShowAll" href="<?php echo $tbl_user_list->PageUrl() ?>cmd=reset"><?php echo $Language->Phrase("ShowAll") ?></a> -->
</div>
<div id="xsr_5" class="radio">
	<label><input class="ace" type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="="<?php if ($tbl_user_list->BasicSearch->getType() == "=") { ?> checked="checked"<?php } ?>><span class="lbl"><?php echo $Language->Phrase("ExactPhrase") ?></span></label>
	<label><input class="ace" type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="AND"<?php if ($tbl_user_list->BasicSearch->getType() == "AND") { ?> checked="checked"<?php } ?>><span class="lbl"><?php echo $Language->Phrase("AllWord") ?></span></label>
	<label><input class="ace" type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="OR"<?php if ($tbl_user_list->BasicSearch->getType() == "OR") { ?> checked="checked"<?php } ?>><span class="lbl"><?php echo $Language->Phrase("AnyWord") ?></span></label>
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
<?php $tbl_user_list->ShowPageHeader(); ?>
<?php
$tbl_user_list->ShowMessage();
?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridUpperPanel">
<?php if ($tbl_user->CurrentAction <> "gridadd" && $tbl_user->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($tbl_user_list->Pager)) $tbl_user_list->Pager = new cNumericPager($tbl_user_list->StartRec, $tbl_user_list->DisplayRecs, $tbl_user_list->TotalRecs, $tbl_user_list->RecRange) ?>
<?php if ($tbl_user_list->Pager->RecordCount > 0) { ?>
<table cellspacing="0" class="ewStdTable"><tbody><tr><td>
<div class="pagination"><ul>
	<?php if ($tbl_user_list->Pager->FirstButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_user_list->PageUrl() ?>start=<?php echo $tbl_user_list->Pager->FirstButton->Start ?>"><?php echo $Language->Phrase("PagerFirst") ?></a></li>
	<?php } ?>
	<?php if ($tbl_user_list->Pager->PrevButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_user_list->PageUrl() ?>start=<?php echo $tbl_user_list->Pager->PrevButton->Start ?>"><?php echo $Language->Phrase("PagerPrevious") ?></a></li>
	<?php } ?>
	<?php foreach ($tbl_user_list->Pager->Items as $PagerItem) { ?>
		<li<?php if (!$PagerItem->Enabled) { echo " class=\" active\""; } ?>><a href="<?php if ($PagerItem->Enabled) { echo $tbl_user_list->PageUrl() . "start=" . $PagerItem->Start; } else { echo "#"; } ?>"><?php echo $PagerItem->Text ?></a></li>
	<?php } ?>
	<?php if ($tbl_user_list->Pager->NextButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_user_list->PageUrl() ?>start=<?php echo $tbl_user_list->Pager->NextButton->Start ?>"><?php echo $Language->Phrase("PagerNext") ?></a></li>
	<?php } ?>
	<?php if ($tbl_user_list->Pager->LastButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_user_list->PageUrl() ?>start=<?php echo $tbl_user_list->Pager->LastButton->Start ?>"><?php echo $Language->Phrase("PagerLast") ?></a></li>
	<?php } ?>
</ul></div>
</td>
<td>
	<?php if ($tbl_user_list->Pager->ButtonCount > 0) { ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php } ?>
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $tbl_user_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $tbl_user_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $tbl_user_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($tbl_user_list->SearchWhere == "0=101") { ?>
	<p><?php echo $Language->Phrase("EnterSearchCriteria") ?></p>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoRecord") ?></p>
	<?php } ?>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoPermission") ?></p>
	<?php } ?>
<?php } ?>
</td>
<?php if ($tbl_user_list->TotalRecs > 0) { ?>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
<input type="hidden" name="t" value="tbl_user">
<select name="<?php echo EW_TABLE_REC_PER_PAGE ?>" class="input-small" onchange="this.form.submit();">
<option value="20"<?php if ($tbl_user_list->DisplayRecs == 20) { ?> selected="selected"<?php } ?>>20</option>
<option value="40"<?php if ($tbl_user_list->DisplayRecs == 40) { ?> selected="selected"<?php } ?>>40</option>
<option value="60"<?php if ($tbl_user_list->DisplayRecs == 60) { ?> selected="selected"<?php } ?>>60</option>
<option value="ALL"<?php if ($tbl_user->getRecordsPerPage() == -1) { ?> selected="selected"<?php } ?>><?php echo $Language->Phrase("AllRecords") ?></option>
</select>
</td>
<?php } ?>
</tr></table>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($tbl_user_list->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
</div>
<form name="ftbl_userlist" id="ftbl_userlist" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="tbl_user">
<div id="gmp_tbl_user" class="ewGridMiddlePanel">
<?php if ($tbl_user_list->TotalRecs > 0) { ?>
<table id="tbl_tbl_userlist" class="ewTable ewTableSeparate">
<?php echo $tbl_user->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$tbl_user_list->RenderListOptions();

// Render list options (header, left)
$tbl_user_list->ListOptions->Render("header", "left");
?>
<?php if ($tbl_user->uid->Visible) { // uid ?>
	<?php if ($tbl_user->SortUrl($tbl_user->uid) == "") { ?>
		<td><div id="elh_tbl_user_uid" class="tbl_user_uid"><div class="ewTableHeaderCaption"><?php echo $tbl_user->uid->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_user->SortUrl($tbl_user->uid) ?>',1);"><div id="elh_tbl_user_uid" class="tbl_user_uid">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_user->uid->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_user->uid->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_user->uid->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_user->username->Visible) { // username ?>
	<?php if ($tbl_user->SortUrl($tbl_user->username) == "") { ?>
		<td><div id="elh_tbl_user_username" class="tbl_user_username"><div class="ewTableHeaderCaption"><?php echo $tbl_user->username->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_user->SortUrl($tbl_user->username) ?>',1);"><div id="elh_tbl_user_username" class="tbl_user_username">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_user->username->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($tbl_user->username->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_user->username->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_user->password->Visible) { // password ?>
	<?php if ($tbl_user->SortUrl($tbl_user->password) == "") { ?>
		<td><div id="elh_tbl_user_password" class="tbl_user_password"><div class="ewTableHeaderCaption"><?php echo $tbl_user->password->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_user->SortUrl($tbl_user->password) ?>',1);"><div id="elh_tbl_user_password" class="tbl_user_password">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_user->password->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_user->password->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_user->password->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_user->_email->Visible) { // email ?>
	<?php if ($tbl_user->SortUrl($tbl_user->_email) == "") { ?>
		<td><div id="elh_tbl_user__email" class="tbl_user__email"><div class="ewTableHeaderCaption"><?php echo $tbl_user->_email->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_user->SortUrl($tbl_user->_email) ?>',1);"><div id="elh_tbl_user__email" class="tbl_user__email">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_user->_email->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($tbl_user->_email->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_user->_email->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_user->firstname->Visible) { // firstname ?>
	<?php if ($tbl_user->SortUrl($tbl_user->firstname) == "") { ?>
		<td><div id="elh_tbl_user_firstname" class="tbl_user_firstname"><div class="ewTableHeaderCaption"><?php echo $tbl_user->firstname->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_user->SortUrl($tbl_user->firstname) ?>',1);"><div id="elh_tbl_user_firstname" class="tbl_user_firstname">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_user->firstname->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($tbl_user->firstname->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_user->firstname->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_user->middlename->Visible) { // middlename ?>
	<?php if ($tbl_user->SortUrl($tbl_user->middlename) == "") { ?>
		<td><div id="elh_tbl_user_middlename" class="tbl_user_middlename"><div class="ewTableHeaderCaption"><?php echo $tbl_user->middlename->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_user->SortUrl($tbl_user->middlename) ?>',1);"><div id="elh_tbl_user_middlename" class="tbl_user_middlename">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_user->middlename->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($tbl_user->middlename->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_user->middlename->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_user->surname->Visible) { // surname ?>
	<?php if ($tbl_user->SortUrl($tbl_user->surname) == "") { ?>
		<td><div id="elh_tbl_user_surname" class="tbl_user_surname"><div class="ewTableHeaderCaption"><?php echo $tbl_user->surname->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_user->SortUrl($tbl_user->surname) ?>',1);"><div id="elh_tbl_user_surname" class="tbl_user_surname">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_user->surname->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($tbl_user->surname->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_user->surname->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_user->extensionname->Visible) { // extensionname ?>
	<?php if ($tbl_user->SortUrl($tbl_user->extensionname) == "") { ?>
		<td><div id="elh_tbl_user_extensionname" class="tbl_user_extensionname"><div class="ewTableHeaderCaption"><?php echo $tbl_user->extensionname->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_user->SortUrl($tbl_user->extensionname) ?>',1);"><div id="elh_tbl_user_extensionname" class="tbl_user_extensionname">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_user->extensionname->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($tbl_user->extensionname->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_user->extensionname->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_user->position->Visible) { // position ?>
	<?php if ($tbl_user->SortUrl($tbl_user->position) == "") { ?>
		<td><div id="elh_tbl_user_position" class="tbl_user_position"><div class="ewTableHeaderCaption"><?php echo $tbl_user->position->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_user->SortUrl($tbl_user->position) ?>',1);"><div id="elh_tbl_user_position" class="tbl_user_position">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_user->position->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($tbl_user->position->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_user->position->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_user->designation->Visible) { // designation ?>
	<?php if ($tbl_user->SortUrl($tbl_user->designation) == "") { ?>
		<td><div id="elh_tbl_user_designation" class="tbl_user_designation"><div class="ewTableHeaderCaption"><?php echo $tbl_user->designation->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_user->SortUrl($tbl_user->designation) ?>',1);"><div id="elh_tbl_user_designation" class="tbl_user_designation">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_user->designation->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($tbl_user->designation->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_user->designation->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_user->region_code->Visible) { // region_code ?>
	<?php if ($tbl_user->SortUrl($tbl_user->region_code) == "") { ?>
		<td><div id="elh_tbl_user_region_code" class="tbl_user_region_code"><div class="ewTableHeaderCaption"><?php echo $tbl_user->region_code->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_user->SortUrl($tbl_user->region_code) ?>',1);"><div id="elh_tbl_user_region_code" class="tbl_user_region_code">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_user->region_code->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_user->region_code->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_user->region_code->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_user->user_level->Visible) { // user_level ?>
	<?php if ($tbl_user->SortUrl($tbl_user->user_level) == "") { ?>
		<td><div id="elh_tbl_user_user_level" class="tbl_user_user_level"><div class="ewTableHeaderCaption"><?php echo $tbl_user->user_level->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_user->SortUrl($tbl_user->user_level) ?>',1);"><div id="elh_tbl_user_user_level" class="tbl_user_user_level">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_user->user_level->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_user->user_level->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_user->user_level->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_user->contact_no->Visible) { // contact_no ?>
	<?php if ($tbl_user->SortUrl($tbl_user->contact_no) == "") { ?>
		<td><div id="elh_tbl_user_contact_no" class="tbl_user_contact_no"><div class="ewTableHeaderCaption"><?php echo $tbl_user->contact_no->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_user->SortUrl($tbl_user->contact_no) ?>',1);"><div id="elh_tbl_user_contact_no" class="tbl_user_contact_no">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_user->contact_no->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($tbl_user->contact_no->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_user->contact_no->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_user->activated->Visible) { // activated ?>
	<?php if ($tbl_user->SortUrl($tbl_user->activated) == "") { ?>
		<td><div id="elh_tbl_user_activated" class="tbl_user_activated"><div class="ewTableHeaderCaption"><?php echo $tbl_user->activated->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_user->SortUrl($tbl_user->activated) ?>',1);"><div id="elh_tbl_user_activated" class="tbl_user_activated">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_user->activated->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_user->activated->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_user->activated->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$tbl_user_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($tbl_user->ExportAll && $tbl_user->Export <> "") {
	$tbl_user_list->StopRec = $tbl_user_list->TotalRecs;
} else {

	// Set the last record to display
	if ($tbl_user_list->TotalRecs > $tbl_user_list->StartRec + $tbl_user_list->DisplayRecs - 1)
		$tbl_user_list->StopRec = $tbl_user_list->StartRec + $tbl_user_list->DisplayRecs - 1;
	else
		$tbl_user_list->StopRec = $tbl_user_list->TotalRecs;
}
$tbl_user_list->RecCnt = $tbl_user_list->StartRec - 1;
if ($tbl_user_list->Recordset && !$tbl_user_list->Recordset->EOF) {
	$tbl_user_list->Recordset->MoveFirst();
	if (!$bSelectLimit && $tbl_user_list->StartRec > 1)
		$tbl_user_list->Recordset->Move($tbl_user_list->StartRec - 1);
} elseif (!$tbl_user->AllowAddDeleteRow && $tbl_user_list->StopRec == 0) {
	$tbl_user_list->StopRec = $tbl_user->GridAddRowCount;
}

// Initialize aggregate
$tbl_user->RowType = EW_ROWTYPE_AGGREGATEINIT;
$tbl_user->ResetAttrs();
$tbl_user_list->RenderRow();
while ($tbl_user_list->RecCnt < $tbl_user_list->StopRec) {
	$tbl_user_list->RecCnt++;
	if (intval($tbl_user_list->RecCnt) >= intval($tbl_user_list->StartRec)) {
		$tbl_user_list->RowCnt++;

		// Set up key count
		$tbl_user_list->KeyCount = $tbl_user_list->RowIndex;

		// Init row class and style
		$tbl_user->ResetAttrs();
		$tbl_user->CssClass = "";
		if ($tbl_user->CurrentAction == "gridadd") {
		} else {
			$tbl_user_list->LoadRowValues($tbl_user_list->Recordset); // Load row values
		}
		$tbl_user->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$tbl_user->RowAttrs = array_merge($tbl_user->RowAttrs, array('data-rowindex'=>$tbl_user_list->RowCnt, 'id'=>'r' . $tbl_user_list->RowCnt . '_tbl_user', 'data-rowtype'=>$tbl_user->RowType));

		// Render row
		$tbl_user_list->RenderRow();

		// Render list options
		$tbl_user_list->RenderListOptions();
?>
	<tr<?php echo $tbl_user->RowAttributes() ?>>
<?php

// Render list options (body, left)
$tbl_user_list->ListOptions->Render("body", "left", $tbl_user_list->RowCnt);
?>
	<?php if ($tbl_user->uid->Visible) { // uid ?>
		<td<?php echo $tbl_user->uid->CellAttributes() ?>>
<span<?php echo $tbl_user->uid->ViewAttributes() ?>>
<?php echo $tbl_user->uid->ListViewValue() ?></span>
<a id="<?php echo $tbl_user_list->PageObjName . "_row_" . $tbl_user_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_user->username->Visible) { // username ?>
		<td<?php echo $tbl_user->username->CellAttributes() ?>>
<span<?php echo $tbl_user->username->ViewAttributes() ?>>
<?php echo $tbl_user->username->ListViewValue() ?></span>
<a id="<?php echo $tbl_user_list->PageObjName . "_row_" . $tbl_user_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_user->password->Visible) { // password ?>
		<td<?php echo $tbl_user->password->CellAttributes() ?>>
<span<?php echo $tbl_user->password->ViewAttributes() ?>>
<?php echo $tbl_user->password->ListViewValue() ?></span>
<a id="<?php echo $tbl_user_list->PageObjName . "_row_" . $tbl_user_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_user->_email->Visible) { // email ?>
		<td<?php echo $tbl_user->_email->CellAttributes() ?>>
<span<?php echo $tbl_user->_email->ViewAttributes() ?>>
<?php echo $tbl_user->_email->ListViewValue() ?></span>
<a id="<?php echo $tbl_user_list->PageObjName . "_row_" . $tbl_user_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_user->firstname->Visible) { // firstname ?>
		<td<?php echo $tbl_user->firstname->CellAttributes() ?>>
<span<?php echo $tbl_user->firstname->ViewAttributes() ?>>
<?php echo $tbl_user->firstname->ListViewValue() ?></span>
<a id="<?php echo $tbl_user_list->PageObjName . "_row_" . $tbl_user_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_user->middlename->Visible) { // middlename ?>
		<td<?php echo $tbl_user->middlename->CellAttributes() ?>>
<span<?php echo $tbl_user->middlename->ViewAttributes() ?>>
<?php echo $tbl_user->middlename->ListViewValue() ?></span>
<a id="<?php echo $tbl_user_list->PageObjName . "_row_" . $tbl_user_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_user->surname->Visible) { // surname ?>
		<td<?php echo $tbl_user->surname->CellAttributes() ?>>
<span<?php echo $tbl_user->surname->ViewAttributes() ?>>
<?php echo $tbl_user->surname->ListViewValue() ?></span>
<a id="<?php echo $tbl_user_list->PageObjName . "_row_" . $tbl_user_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_user->extensionname->Visible) { // extensionname ?>
		<td<?php echo $tbl_user->extensionname->CellAttributes() ?>>
<span<?php echo $tbl_user->extensionname->ViewAttributes() ?>>
<?php echo $tbl_user->extensionname->ListViewValue() ?></span>
<a id="<?php echo $tbl_user_list->PageObjName . "_row_" . $tbl_user_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_user->position->Visible) { // position ?>
		<td<?php echo $tbl_user->position->CellAttributes() ?>>
<span<?php echo $tbl_user->position->ViewAttributes() ?>>
<?php echo $tbl_user->position->ListViewValue() ?></span>
<a id="<?php echo $tbl_user_list->PageObjName . "_row_" . $tbl_user_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_user->designation->Visible) { // designation ?>
		<td<?php echo $tbl_user->designation->CellAttributes() ?>>
<span<?php echo $tbl_user->designation->ViewAttributes() ?>>
<?php echo $tbl_user->designation->ListViewValue() ?></span>
<a id="<?php echo $tbl_user_list->PageObjName . "_row_" . $tbl_user_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_user->region_code->Visible) { // region_code ?>
		<td<?php echo $tbl_user->region_code->CellAttributes() ?>>
<span<?php echo $tbl_user->region_code->ViewAttributes() ?>>
<?php echo $tbl_user->region_code->ListViewValue() ?></span>
<a id="<?php echo $tbl_user_list->PageObjName . "_row_" . $tbl_user_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_user->user_level->Visible) { // user_level ?>
		<td<?php echo $tbl_user->user_level->CellAttributes() ?>>
<span<?php echo $tbl_user->user_level->ViewAttributes() ?>>
<?php echo $tbl_user->user_level->ListViewValue() ?></span>
<a id="<?php echo $tbl_user_list->PageObjName . "_row_" . $tbl_user_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_user->contact_no->Visible) { // contact_no ?>
		<td<?php echo $tbl_user->contact_no->CellAttributes() ?>>
<span<?php echo $tbl_user->contact_no->ViewAttributes() ?>>
<?php echo $tbl_user->contact_no->ListViewValue() ?></span>
<a id="<?php echo $tbl_user_list->PageObjName . "_row_" . $tbl_user_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_user->activated->Visible) { // activated ?>
		<td<?php echo $tbl_user->activated->CellAttributes() ?>>
<span<?php echo $tbl_user->activated->ViewAttributes() ?>>
<?php echo $tbl_user->activated->ListViewValue() ?></span>
<a id="<?php echo $tbl_user_list->PageObjName . "_row_" . $tbl_user_list->RowCnt ?>"></a></td>
	<?php } ?>
<?php

// Render list options (body, right)
$tbl_user_list->ListOptions->Render("body", "right", $tbl_user_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($tbl_user->CurrentAction <> "gridadd")
		$tbl_user_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($tbl_user->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($tbl_user_list->Recordset)
	$tbl_user_list->Recordset->Close();
?>
<?php if ($tbl_user_list->TotalRecs > 0) { ?>
<div class="ewGridLowerPanel">
<?php if ($tbl_user->CurrentAction <> "gridadd" && $tbl_user->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($tbl_user_list->Pager)) $tbl_user_list->Pager = new cNumericPager($tbl_user_list->StartRec, $tbl_user_list->DisplayRecs, $tbl_user_list->TotalRecs, $tbl_user_list->RecRange) ?>
<?php if ($tbl_user_list->Pager->RecordCount > 0) { ?>
<table cellspacing="0" class="ewStdTable"><tbody><tr><td>
<div class="pagination"><ul>
	<?php if ($tbl_user_list->Pager->FirstButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_user_list->PageUrl() ?>start=<?php echo $tbl_user_list->Pager->FirstButton->Start ?>"><?php echo $Language->Phrase("PagerFirst") ?></a></li>
	<?php } ?>
	<?php if ($tbl_user_list->Pager->PrevButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_user_list->PageUrl() ?>start=<?php echo $tbl_user_list->Pager->PrevButton->Start ?>"><?php echo $Language->Phrase("PagerPrevious") ?></a></li>
	<?php } ?>
	<?php foreach ($tbl_user_list->Pager->Items as $PagerItem) { ?>
		<li<?php if (!$PagerItem->Enabled) { echo " class=\" active\""; } ?>><a href="<?php if ($PagerItem->Enabled) { echo $tbl_user_list->PageUrl() . "start=" . $PagerItem->Start; } else { echo "#"; } ?>"><?php echo $PagerItem->Text ?></a></li>
	<?php } ?>
	<?php if ($tbl_user_list->Pager->NextButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_user_list->PageUrl() ?>start=<?php echo $tbl_user_list->Pager->NextButton->Start ?>"><?php echo $Language->Phrase("PagerNext") ?></a></li>
	<?php } ?>
	<?php if ($tbl_user_list->Pager->LastButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_user_list->PageUrl() ?>start=<?php echo $tbl_user_list->Pager->LastButton->Start ?>"><?php echo $Language->Phrase("PagerLast") ?></a></li>
	<?php } ?>
</ul></div>
</td>
<td>
	<?php if ($tbl_user_list->Pager->ButtonCount > 0) { ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php } ?>
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $tbl_user_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $tbl_user_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $tbl_user_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($tbl_user_list->SearchWhere == "0=101") { ?>
	<p><?php echo $Language->Phrase("EnterSearchCriteria") ?></p>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoRecord") ?></p>
	<?php } ?>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoPermission") ?></p>
	<?php } ?>
<?php } ?>
</td>
<?php if ($tbl_user_list->TotalRecs > 0) { ?>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
<input type="hidden" name="t" value="tbl_user">
<select name="<?php echo EW_TABLE_REC_PER_PAGE ?>" class="input-small" onchange="this.form.submit();">
<option value="20"<?php if ($tbl_user_list->DisplayRecs == 20) { ?> selected="selected"<?php } ?>>20</option>
<option value="40"<?php if ($tbl_user_list->DisplayRecs == 40) { ?> selected="selected"<?php } ?>>40</option>
<option value="60"<?php if ($tbl_user_list->DisplayRecs == 60) { ?> selected="selected"<?php } ?>>60</option>
<option value="ALL"<?php if ($tbl_user->getRecordsPerPage() == -1) { ?> selected="selected"<?php } ?>><?php echo $Language->Phrase("AllRecords") ?></option>
</select>
</td>
<?php } ?>
</tr></table>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($tbl_user_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
</div>
<?php } ?>
</td></tr></table>
<script type="text/javascript">
ftbl_userlistsrch.Init();
ftbl_userlist.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$tbl_user_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$tbl_user_list->Page_Terminate();
?>
