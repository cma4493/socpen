<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "tbl_updatesinfo.php" ?>
<?php include_once "tbl_pensionerinfo.php" ?>
<?php include_once "tbl_userinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$tbl_updates_list = NULL; // Initialize page object first

class ctbl_updates_list extends ctbl_updates {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Table name
	var $TableName = 'tbl_updates';

	// Page object name
	var $PageObjName = 'tbl_updates_list';

	// Grid form hidden field names
	var $FormName = 'ftbl_updateslist';
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

		// Table object (tbl_updates)
		if (!isset($GLOBALS["tbl_updates"])) {
			$GLOBALS["tbl_updates"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["tbl_updates"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "tbl_updatesadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "tbl_updatesdelete.php";
		$this->MultiUpdateUrl = "tbl_updatesupdate.php";

		// Table object (tbl_pensioner)
		if (!isset($GLOBALS['tbl_pensioner'])) $GLOBALS['tbl_pensioner'] = new ctbl_pensioner();

		// Table object (tbl_user)
		if (!isset($GLOBALS['tbl_user'])) $GLOBALS['tbl_user'] = new ctbl_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'tbl_updates', TRUE);

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
		$this->updatesID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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

			// Set up master detail parameters
			$this->SetUpMasterParms();

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

		// Restore master/detail filter
		$this->DbMasterFilter = $this->GetMasterFilter(); // Restore master filter
		$this->DbDetailFilter = $this->GetDetailFilter(); // Restore detail filter
		ew_AddFilter($sFilter, $this->DbDetailFilter);
		ew_AddFilter($sFilter, $this->SearchWhere);

		// Load master record
		if ($this->CurrentMode <> "add" && $this->GetMasterFilter() <> "" && $this->getCurrentMasterTable() == "tbl_pensioner") {
			global $tbl_pensioner;
			$rsmaster = $tbl_pensioner->LoadRs($this->DbMasterFilter);
			$this->MasterRecordExists = ($rsmaster && !$rsmaster->EOF);
			if (!$this->MasterRecordExists) {
				$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record found
				$this->Page_Terminate("tbl_pensionerlist.php"); // Return to master page
			} else {
				$tbl_pensioner->LoadListRowValues($rsmaster);
				$tbl_pensioner->RowType = EW_ROWTYPE_MASTER; // Master row
				$tbl_pensioner->RenderListRow();
				$rsmaster->Close();
			}
		}

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
			$this->updatesID->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->updatesID->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Return basic search SQL
	function BasicSearchSQL($Keyword) {
		$sKeyword = ew_AdjustSql($Keyword);
		$sWhere = "";
		$this->BuildBasicSearchSQL($sWhere, $this->PensionerID, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->Remarks, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->_field, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->new_value, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->old_value, $Keyword);
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
		return FALSE;
	}

	// Clear all search parameters
	function ResetSearchParms() {

		// Clear search WHERE clause
		$this->SearchWhere = "";
		$this->setSearchWhere($this->SearchWhere);

		// Clear basic search parameters
		$this->ResetBasicSearchParms();
	}

	// Load advanced search default values
	function LoadAdvancedSearchDefault() {
		return FALSE;
	}

	// Clear all basic search parameters
	function ResetBasicSearchParms() {
		$this->BasicSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore basic search values
		$this->BasicSearch->Load();
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->updatesID); // updatesID
			$this->UpdateSort($this->PensionerID); // PensionerID
			$this->UpdateSort($this->status); // status
			$this->UpdateSort($this->approved); // approved
			$this->UpdateSort($this->dateUpdated); // dateUpdated
			$this->UpdateSort($this->_field); // field
			$this->UpdateSort($this->paymentmodeID); // paymentmodeID
			$this->UpdateSort($this->deathDate); // deathDate
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

			// Reset master/detail keys
			if ($this->Command == "resetall") {
				$this->setCurrentMasterTable(""); // Clear master table
				$this->DbMasterFilter = "";
				$this->DbDetailFilter = "";
				$this->PensionerID->setSessionValue("");
			}

			// Reset sorting order
			if ($this->Command == "resetsort") {
				$sOrderBy = "";
				$this->setSessionOrderBy($sOrderBy);
				$this->updatesID->setSort("");
				$this->PensionerID->setSort("");
				$this->status->setSort("");
				$this->approved->setSort("");
				$this->dateUpdated->setSort("");
				$this->_field->setSort("");
				$this->paymentmodeID->setSort("");
				$this->deathDate->setSort("");
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

		// "copy"
		$oListOpt = &$this->ListOptions->Items["copy"];
		if ($Security->CanAdd()) {
			$oListOpt->Body = "<a class=\"blue\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("CopyLink")) . "\" href=\"" . ew_HtmlEncode($this->CopyUrl) . "\">" . $Language->Phrase("CopyLink") . "<i class=\"icon-copy bigger-130\"></i></a>";
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
		$item->Visible = ($this->AddUrl <> "" && $Security->CanAdd());
		$option = $options["action"];

		// Add multi delete
		$item = &$option->Add("multidelete");
		$item->Body = "<a class=\"btn btn-purple btn-sm\" href=\"\" onclick=\"ew_SubmitSelected(document.ftbl_updateslist, '" . $this->MultiDeleteUrl . "', ewLanguage.Phrase('DeleteMultiConfirmMsg'));return false;\">" . $Language->Phrase("DeleteSelectedLink") . "</a>";
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
				$item->Body = "<a class=\"btn btn-warning btn-sm\" href=\"\" onclick=\"ew_SubmitSelected(document.ftbl_updateslist, '" . ew_CurrentUrl() . "', null, '" . $action . "');return false;\">" . $name . "</a>";
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
		$this->Remarks->setDbValue($rs->fields('Remarks'));
		$this->approved->setDbValue($rs->fields('approved'));
		$this->dateUpdated->setDbValue($rs->fields('dateUpdated'));
		$this->_field->setDbValue($rs->fields('field'));
		$this->new_value->setDbValue($rs->fields('new_value'));
		$this->old_value->setDbValue($rs->fields('old_value'));
		$this->paymentmodeID->setDbValue($rs->fields('paymentmodeID'));
		$this->deathDate->setDbValue($rs->fields('deathDate'));
		$this->Createdby->setDbValue($rs->fields('Createdby'));
		$this->CreatedDate->setDbValue($rs->fields('CreatedDate'));
		$this->UpdatedBy->setDbValue($rs->fields('UpdatedBy'));
		$this->UpdatedDate->setDbValue($rs->fields('UpdatedDate'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->updatesID->DbValue = $row['updatesID'];
		$this->PensionerID->DbValue = $row['PensionerID'];
		$this->status->DbValue = $row['status'];
		$this->Remarks->DbValue = $row['Remarks'];
		$this->approved->DbValue = $row['approved'];
		$this->dateUpdated->DbValue = $row['dateUpdated'];
		$this->_field->DbValue = $row['field'];
		$this->new_value->DbValue = $row['new_value'];
		$this->old_value->DbValue = $row['old_value'];
		$this->paymentmodeID->DbValue = $row['paymentmodeID'];
		$this->deathDate->DbValue = $row['deathDate'];
		$this->Createdby->DbValue = $row['Createdby'];
		$this->CreatedDate->DbValue = $row['CreatedDate'];
		$this->UpdatedBy->DbValue = $row['UpdatedBy'];
		$this->UpdatedDate->DbValue = $row['UpdatedDate'];
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
		// Remarks
		// approved
		// dateUpdated
		// field
		// new_value
		// old_value
		// paymentmodeID
		// deathDate
		// Createdby
		// CreatedDate
		// UpdatedBy
		// UpdatedDate

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// updatesID
			$this->updatesID->ViewValue = $this->updatesID->CurrentValue;
			$this->updatesID->ViewCustomAttributes = "";

			// PensionerID
			$this->PensionerID->ViewValue = $this->PensionerID->CurrentValue;
			$this->PensionerID->ViewCustomAttributes = "";

			// status
			$this->status->ViewValue = $this->status->CurrentValue;
			$this->status->ViewCustomAttributes = "";

			// approved
			$this->approved->ViewValue = $this->approved->CurrentValue;
			$this->approved->ViewCustomAttributes = "";

			// dateUpdated
			$this->dateUpdated->ViewValue = $this->dateUpdated->CurrentValue;
			$this->dateUpdated->ViewValue = ew_FormatDateTime($this->dateUpdated->ViewValue, 6);
			$this->dateUpdated->ViewCustomAttributes = "";

			// field
			$this->_field->ViewValue = $this->_field->CurrentValue;
			$this->_field->ViewCustomAttributes = "";

			// paymentmodeID
			$this->paymentmodeID->ViewValue = $this->paymentmodeID->CurrentValue;
			$this->paymentmodeID->ViewCustomAttributes = "";

			// deathDate
			$this->deathDate->ViewValue = $this->deathDate->CurrentValue;
			$this->deathDate->ViewValue = ew_FormatDateTime($this->deathDate->ViewValue, 6);
			$this->deathDate->ViewCustomAttributes = "";

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

			// updatesID
			$this->updatesID->LinkCustomAttributes = "";
			$this->updatesID->HrefValue = "";
			$this->updatesID->TooltipValue = "";

			// PensionerID
			$this->PensionerID->LinkCustomAttributes = "";
			$this->PensionerID->HrefValue = "";
			$this->PensionerID->TooltipValue = "";

			// status
			$this->status->LinkCustomAttributes = "";
			$this->status->HrefValue = "";
			$this->status->TooltipValue = "";

			// approved
			$this->approved->LinkCustomAttributes = "";
			$this->approved->HrefValue = "";
			$this->approved->TooltipValue = "";

			// dateUpdated
			$this->dateUpdated->LinkCustomAttributes = "";
			$this->dateUpdated->HrefValue = "";
			$this->dateUpdated->TooltipValue = "";

			// field
			$this->_field->LinkCustomAttributes = "";
			$this->_field->HrefValue = "";
			$this->_field->TooltipValue = "";

			// paymentmodeID
			$this->paymentmodeID->LinkCustomAttributes = "";
			$this->paymentmodeID->HrefValue = "";
			$this->paymentmodeID->TooltipValue = "";

			// deathDate
			$this->deathDate->LinkCustomAttributes = "";
			$this->deathDate->HrefValue = "";
			$this->deathDate->TooltipValue = "";

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
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
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
		$url = ew_CurrentUrl();
		$url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", $url, $this->TableVar);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'tbl_updates';
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
if (!isset($tbl_updates_list)) $tbl_updates_list = new ctbl_updates_list();

// Page init
$tbl_updates_list->Page_Init();

// Page main
$tbl_updates_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$tbl_updates_list->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var tbl_updates_list = new ew_Page("tbl_updates_list");
tbl_updates_list.PageID = "list"; // Page ID
var EW_PAGE_ID = tbl_updates_list.PageID; // For backward compatibility

// Form object
var ftbl_updateslist = new ew_Form("ftbl_updateslist");
ftbl_updateslist.FormKeyCountName = '<?php echo $tbl_updates_list->FormKeyCountName ?>';

// Form_CustomValidate event
ftbl_updateslist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftbl_updateslist.ValidateRequired = true;
<?php } else { ?>
ftbl_updateslist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

var ftbl_updateslistsrch = new ew_Form("ftbl_updateslistsrch");
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php //$Breadcrumb->Render(); ?>
<?php if ($tbl_updates->getCurrentMasterTable() == "" && $tbl_updates_list->ExportOptions->Visible()) { ?>
<div class="ewListExportOptions"><?php $tbl_updates_list->ExportOptions->Render("body") ?></div>
<?php } ?>
<?php if (($tbl_updates->Export == "") || (EW_EXPORT_MASTER_RECORD && $tbl_updates->Export == "print")) { ?>
<?php
$gsMasterReturnUrl = "tbl_pensionerlist.php";
if ($tbl_updates_list->DbMasterFilter <> "" && $tbl_updates->getCurrentMasterTable() == "tbl_pensioner") {
	if ($tbl_updates_list->MasterRecordExists) {
		if ($tbl_updates->getCurrentMasterTable() == $tbl_updates->TableVar) $gsMasterReturnUrl .= "?" . EW_TABLE_SHOW_MASTER . "=";
?>
<?php if ($tbl_updates_list->ExportOptions->Visible()) { ?>
<div class="ewListExportOptions"><?php $tbl_updates_list->ExportOptions->Render("body") ?></div>
<?php } ?>
<?php include_once "tbl_pensionermaster.php" ?>
<?php
	}
}
?>
<?php } ?>
<?php
	$bSelectLimit = EW_SELECT_LIMIT;
	if ($bSelectLimit) {
		$tbl_updates_list->TotalRecs = $tbl_updates->SelectRecordCount();
	} else {
		if ($tbl_updates_list->Recordset = $tbl_updates_list->LoadRecordset())
			$tbl_updates_list->TotalRecs = $tbl_updates_list->Recordset->RecordCount();
	}
	$tbl_updates_list->StartRec = 1;
	if ($tbl_updates_list->DisplayRecs <= 0 || ($tbl_updates->Export <> "" && $tbl_updates->ExportAll)) // Display all records
		$tbl_updates_list->DisplayRecs = $tbl_updates_list->TotalRecs;
	if (!($tbl_updates->Export <> "" && $tbl_updates->ExportAll))
		$tbl_updates_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$tbl_updates_list->Recordset = $tbl_updates_list->LoadRecordset($tbl_updates_list->StartRec-1, $tbl_updates_list->DisplayRecs);
$tbl_updates_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($tbl_updates->Export == "" && $tbl_updates->CurrentAction == "") { ?>
<form name="ftbl_updateslistsrch" id="ftbl_updateslistsrch" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>">
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
	<div id="ftbl_updateslistsrch_SearchPanel">
		<input type="hidden" name="cmd" value="search">
		<input type="hidden" name="t" value="tbl_updates">
		<div class="ewBasicSearch">
<div id="xsr_1" class="row">
	<div class="col-xs-12 col-sm-4">
	<div class="input-group">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="form-control search-query" value="<?php echo ew_HtmlEncode($tbl_updates_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo $Language->Phrase("Search") ?>">
	<span class="input-group-btn">
	<button class="btn btn-purple btn-sm" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $Language->Phrase("QuickSearchBtn") ?> <i class="icon-search icon-on-right bigger-110"></i></button>&nbsp;
	<a type="button" class="btn btn-success btn-sm" href="<?php echo $tbl_updates_list->PageUrl() ?>cmd=reset">ShowAll <i class="icon-refresh icon-on-right bigger-110"></i></a>
	</span>
	</div>
	</div>
	<div class="btn-group ewButtonGroup">
	<!--<a class="btn ewShowAll" href="<?php echo $tbl_updates_list->PageUrl() ?>cmd=reset"><?php echo $Language->Phrase("ShowAll") ?></a> -->
</div>
<div id="xsr_2" class="radio">
	<label><input class="ace" type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="="<?php if ($tbl_updates_list->BasicSearch->getType() == "=") { ?> checked="checked"<?php } ?>><span class="lbl"><?php echo $Language->Phrase("ExactPhrase") ?></span></label>
	<label><input class="ace" type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="AND"<?php if ($tbl_updates_list->BasicSearch->getType() == "AND") { ?> checked="checked"<?php } ?>><span class="lbl"><?php echo $Language->Phrase("AllWord") ?></span></label>
	<label><input class="ace" type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="OR"<?php if ($tbl_updates_list->BasicSearch->getType() == "OR") { ?> checked="checked"<?php } ?>><span class="lbl"><?php echo $Language->Phrase("AnyWord") ?></span></label>
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
<?php $tbl_updates_list->ShowPageHeader(); ?>
<?php
$tbl_updates_list->ShowMessage();
?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridUpperPanel">
<?php if ($tbl_updates->CurrentAction <> "gridadd" && $tbl_updates->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($tbl_updates_list->Pager)) $tbl_updates_list->Pager = new cNumericPager($tbl_updates_list->StartRec, $tbl_updates_list->DisplayRecs, $tbl_updates_list->TotalRecs, $tbl_updates_list->RecRange) ?>
<?php if ($tbl_updates_list->Pager->RecordCount > 0) { ?>
<table cellspacing="0" class="ewStdTable"><tbody><tr><td>
<div class="pagination"><ul>
	<?php if ($tbl_updates_list->Pager->FirstButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_updates_list->PageUrl() ?>start=<?php echo $tbl_updates_list->Pager->FirstButton->Start ?>"><?php echo $Language->Phrase("PagerFirst") ?></a></li>
	<?php } ?>
	<?php if ($tbl_updates_list->Pager->PrevButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_updates_list->PageUrl() ?>start=<?php echo $tbl_updates_list->Pager->PrevButton->Start ?>"><?php echo $Language->Phrase("PagerPrevious") ?></a></li>
	<?php } ?>
	<?php foreach ($tbl_updates_list->Pager->Items as $PagerItem) { ?>
		<li<?php if (!$PagerItem->Enabled) { echo " class=\" active\""; } ?>><a href="<?php if ($PagerItem->Enabled) { echo $tbl_updates_list->PageUrl() . "start=" . $PagerItem->Start; } else { echo "#"; } ?>"><?php echo $PagerItem->Text ?></a></li>
	<?php } ?>
	<?php if ($tbl_updates_list->Pager->NextButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_updates_list->PageUrl() ?>start=<?php echo $tbl_updates_list->Pager->NextButton->Start ?>"><?php echo $Language->Phrase("PagerNext") ?></a></li>
	<?php } ?>
	<?php if ($tbl_updates_list->Pager->LastButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_updates_list->PageUrl() ?>start=<?php echo $tbl_updates_list->Pager->LastButton->Start ?>"><?php echo $Language->Phrase("PagerLast") ?></a></li>
	<?php } ?>
</ul></div>
</td>
<td>
	<?php if ($tbl_updates_list->Pager->ButtonCount > 0) { ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php } ?>
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $tbl_updates_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $tbl_updates_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $tbl_updates_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($tbl_updates_list->SearchWhere == "0=101") { ?>
	<p><?php echo $Language->Phrase("EnterSearchCriteria") ?></p>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoRecord") ?></p>
	<?php } ?>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoPermission") ?></p>
	<?php } ?>
<?php } ?>
</td>
<?php if ($tbl_updates_list->TotalRecs > 0) { ?>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
<input type="hidden" name="t" value="tbl_updates">
<select name="<?php echo EW_TABLE_REC_PER_PAGE ?>" class="input-small" onchange="this.form.submit();">
<option value="20"<?php if ($tbl_updates_list->DisplayRecs == 20) { ?> selected="selected"<?php } ?>>20</option>
<option value="40"<?php if ($tbl_updates_list->DisplayRecs == 40) { ?> selected="selected"<?php } ?>>40</option>
<option value="60"<?php if ($tbl_updates_list->DisplayRecs == 60) { ?> selected="selected"<?php } ?>>60</option>
<option value="ALL"<?php if ($tbl_updates->getRecordsPerPage() == -1) { ?> selected="selected"<?php } ?>><?php echo $Language->Phrase("AllRecords") ?></option>
</select>
</td>
<?php } ?>
</tr></table>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($tbl_updates_list->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
</div>
<form name="ftbl_updateslist" id="ftbl_updateslist" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="tbl_updates">
<div id="gmp_tbl_updates" class="ewGridMiddlePanel">
<?php if ($tbl_updates_list->TotalRecs > 0) { ?>
<table id="tbl_tbl_updateslist" class="ewTable ewTableSeparate">
<?php echo $tbl_updates->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$tbl_updates_list->RenderListOptions();

// Render list options (header, left)
$tbl_updates_list->ListOptions->Render("header", "left");
?>
<?php if ($tbl_updates->updatesID->Visible) { // updatesID ?>
	<?php if ($tbl_updates->SortUrl($tbl_updates->updatesID) == "") { ?>
		<td><div id="elh_tbl_updates_updatesID" class="tbl_updates_updatesID"><div class="ewTableHeaderCaption"><?php echo $tbl_updates->updatesID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_updates->SortUrl($tbl_updates->updatesID) ?>',1);"><div id="elh_tbl_updates_updatesID" class="tbl_updates_updatesID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_updates->updatesID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_updates->updatesID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_updates->updatesID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_updates->PensionerID->Visible) { // PensionerID ?>
	<?php if ($tbl_updates->SortUrl($tbl_updates->PensionerID) == "") { ?>
		<td><div id="elh_tbl_updates_PensionerID" class="tbl_updates_PensionerID"><div class="ewTableHeaderCaption"><?php echo $tbl_updates->PensionerID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_updates->SortUrl($tbl_updates->PensionerID) ?>',1);"><div id="elh_tbl_updates_PensionerID" class="tbl_updates_PensionerID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_updates->PensionerID->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($tbl_updates->PensionerID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_updates->PensionerID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_updates->status->Visible) { // status ?>
	<?php if ($tbl_updates->SortUrl($tbl_updates->status) == "") { ?>
		<td><div id="elh_tbl_updates_status" class="tbl_updates_status"><div class="ewTableHeaderCaption"><?php echo $tbl_updates->status->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_updates->SortUrl($tbl_updates->status) ?>',1);"><div id="elh_tbl_updates_status" class="tbl_updates_status">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_updates->status->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_updates->status->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_updates->status->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_updates->approved->Visible) { // approved ?>
	<?php if ($tbl_updates->SortUrl($tbl_updates->approved) == "") { ?>
		<td><div id="elh_tbl_updates_approved" class="tbl_updates_approved"><div class="ewTableHeaderCaption"><?php echo $tbl_updates->approved->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_updates->SortUrl($tbl_updates->approved) ?>',1);"><div id="elh_tbl_updates_approved" class="tbl_updates_approved">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_updates->approved->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_updates->approved->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_updates->approved->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_updates->dateUpdated->Visible) { // dateUpdated ?>
	<?php if ($tbl_updates->SortUrl($tbl_updates->dateUpdated) == "") { ?>
		<td><div id="elh_tbl_updates_dateUpdated" class="tbl_updates_dateUpdated"><div class="ewTableHeaderCaption"><?php echo $tbl_updates->dateUpdated->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_updates->SortUrl($tbl_updates->dateUpdated) ?>',1);"><div id="elh_tbl_updates_dateUpdated" class="tbl_updates_dateUpdated">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_updates->dateUpdated->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_updates->dateUpdated->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_updates->dateUpdated->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_updates->_field->Visible) { // field ?>
	<?php if ($tbl_updates->SortUrl($tbl_updates->_field) == "") { ?>
		<td><div id="elh_tbl_updates__field" class="tbl_updates__field"><div class="ewTableHeaderCaption"><?php echo $tbl_updates->_field->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_updates->SortUrl($tbl_updates->_field) ?>',1);"><div id="elh_tbl_updates__field" class="tbl_updates__field">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_updates->_field->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($tbl_updates->_field->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_updates->_field->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_updates->paymentmodeID->Visible) { // paymentmodeID ?>
	<?php if ($tbl_updates->SortUrl($tbl_updates->paymentmodeID) == "") { ?>
		<td><div id="elh_tbl_updates_paymentmodeID" class="tbl_updates_paymentmodeID"><div class="ewTableHeaderCaption"><?php echo $tbl_updates->paymentmodeID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_updates->SortUrl($tbl_updates->paymentmodeID) ?>',1);"><div id="elh_tbl_updates_paymentmodeID" class="tbl_updates_paymentmodeID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_updates->paymentmodeID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_updates->paymentmodeID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_updates->paymentmodeID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_updates->deathDate->Visible) { // deathDate ?>
	<?php if ($tbl_updates->SortUrl($tbl_updates->deathDate) == "") { ?>
		<td><div id="elh_tbl_updates_deathDate" class="tbl_updates_deathDate"><div class="ewTableHeaderCaption"><?php echo $tbl_updates->deathDate->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_updates->SortUrl($tbl_updates->deathDate) ?>',1);"><div id="elh_tbl_updates_deathDate" class="tbl_updates_deathDate">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_updates->deathDate->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_updates->deathDate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_updates->deathDate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_updates->Createdby->Visible) { // Createdby ?>
	<?php if ($tbl_updates->SortUrl($tbl_updates->Createdby) == "") { ?>
		<td><div id="elh_tbl_updates_Createdby" class="tbl_updates_Createdby"><div class="ewTableHeaderCaption"><?php echo $tbl_updates->Createdby->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_updates->SortUrl($tbl_updates->Createdby) ?>',1);"><div id="elh_tbl_updates_Createdby" class="tbl_updates_Createdby">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_updates->Createdby->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_updates->Createdby->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_updates->Createdby->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_updates->CreatedDate->Visible) { // CreatedDate ?>
	<?php if ($tbl_updates->SortUrl($tbl_updates->CreatedDate) == "") { ?>
		<td><div id="elh_tbl_updates_CreatedDate" class="tbl_updates_CreatedDate"><div class="ewTableHeaderCaption"><?php echo $tbl_updates->CreatedDate->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_updates->SortUrl($tbl_updates->CreatedDate) ?>',1);"><div id="elh_tbl_updates_CreatedDate" class="tbl_updates_CreatedDate">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_updates->CreatedDate->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_updates->CreatedDate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_updates->CreatedDate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_updates->UpdatedBy->Visible) { // UpdatedBy ?>
	<?php if ($tbl_updates->SortUrl($tbl_updates->UpdatedBy) == "") { ?>
		<td><div id="elh_tbl_updates_UpdatedBy" class="tbl_updates_UpdatedBy"><div class="ewTableHeaderCaption"><?php echo $tbl_updates->UpdatedBy->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_updates->SortUrl($tbl_updates->UpdatedBy) ?>',1);"><div id="elh_tbl_updates_UpdatedBy" class="tbl_updates_UpdatedBy">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_updates->UpdatedBy->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_updates->UpdatedBy->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_updates->UpdatedBy->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_updates->UpdatedDate->Visible) { // UpdatedDate ?>
	<?php if ($tbl_updates->SortUrl($tbl_updates->UpdatedDate) == "") { ?>
		<td><div id="elh_tbl_updates_UpdatedDate" class="tbl_updates_UpdatedDate"><div class="ewTableHeaderCaption"><?php echo $tbl_updates->UpdatedDate->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_updates->SortUrl($tbl_updates->UpdatedDate) ?>',1);"><div id="elh_tbl_updates_UpdatedDate" class="tbl_updates_UpdatedDate">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_updates->UpdatedDate->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_updates->UpdatedDate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_updates->UpdatedDate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$tbl_updates_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($tbl_updates->ExportAll && $tbl_updates->Export <> "") {
	$tbl_updates_list->StopRec = $tbl_updates_list->TotalRecs;
} else {

	// Set the last record to display
	if ($tbl_updates_list->TotalRecs > $tbl_updates_list->StartRec + $tbl_updates_list->DisplayRecs - 1)
		$tbl_updates_list->StopRec = $tbl_updates_list->StartRec + $tbl_updates_list->DisplayRecs - 1;
	else
		$tbl_updates_list->StopRec = $tbl_updates_list->TotalRecs;
}
$tbl_updates_list->RecCnt = $tbl_updates_list->StartRec - 1;
if ($tbl_updates_list->Recordset && !$tbl_updates_list->Recordset->EOF) {
	$tbl_updates_list->Recordset->MoveFirst();
	if (!$bSelectLimit && $tbl_updates_list->StartRec > 1)
		$tbl_updates_list->Recordset->Move($tbl_updates_list->StartRec - 1);
} elseif (!$tbl_updates->AllowAddDeleteRow && $tbl_updates_list->StopRec == 0) {
	$tbl_updates_list->StopRec = $tbl_updates->GridAddRowCount;
}

// Initialize aggregate
$tbl_updates->RowType = EW_ROWTYPE_AGGREGATEINIT;
$tbl_updates->ResetAttrs();
$tbl_updates_list->RenderRow();
while ($tbl_updates_list->RecCnt < $tbl_updates_list->StopRec) {
	$tbl_updates_list->RecCnt++;
	if (intval($tbl_updates_list->RecCnt) >= intval($tbl_updates_list->StartRec)) {
		$tbl_updates_list->RowCnt++;

		// Set up key count
		$tbl_updates_list->KeyCount = $tbl_updates_list->RowIndex;

		// Init row class and style
		$tbl_updates->ResetAttrs();
		$tbl_updates->CssClass = "";
		if ($tbl_updates->CurrentAction == "gridadd") {
		} else {
			$tbl_updates_list->LoadRowValues($tbl_updates_list->Recordset); // Load row values
		}
		$tbl_updates->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$tbl_updates->RowAttrs = array_merge($tbl_updates->RowAttrs, array('data-rowindex'=>$tbl_updates_list->RowCnt, 'id'=>'r' . $tbl_updates_list->RowCnt . '_tbl_updates', 'data-rowtype'=>$tbl_updates->RowType));

		// Render row
		$tbl_updates_list->RenderRow();

		// Render list options
		$tbl_updates_list->RenderListOptions();
?>
	<tr<?php echo $tbl_updates->RowAttributes() ?>>
<?php

// Render list options (body, left)
$tbl_updates_list->ListOptions->Render("body", "left", $tbl_updates_list->RowCnt);
?>
	<?php if ($tbl_updates->updatesID->Visible) { // updatesID ?>
		<td<?php echo $tbl_updates->updatesID->CellAttributes() ?>>
<span<?php echo $tbl_updates->updatesID->ViewAttributes() ?>>
<?php echo $tbl_updates->updatesID->ListViewValue() ?></span>
<a id="<?php echo $tbl_updates_list->PageObjName . "_row_" . $tbl_updates_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_updates->PensionerID->Visible) { // PensionerID ?>
		<td<?php echo $tbl_updates->PensionerID->CellAttributes() ?>>
<span<?php echo $tbl_updates->PensionerID->ViewAttributes() ?>>
<?php echo $tbl_updates->PensionerID->ListViewValue() ?></span>
<a id="<?php echo $tbl_updates_list->PageObjName . "_row_" . $tbl_updates_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_updates->status->Visible) { // status ?>
		<td<?php echo $tbl_updates->status->CellAttributes() ?>>
<span<?php echo $tbl_updates->status->ViewAttributes() ?>>
<?php echo $tbl_updates->status->ListViewValue() ?></span>
<a id="<?php echo $tbl_updates_list->PageObjName . "_row_" . $tbl_updates_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_updates->approved->Visible) { // approved ?>
		<td<?php echo $tbl_updates->approved->CellAttributes() ?>>
<span<?php echo $tbl_updates->approved->ViewAttributes() ?>>
<?php echo $tbl_updates->approved->ListViewValue() ?></span>
<a id="<?php echo $tbl_updates_list->PageObjName . "_row_" . $tbl_updates_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_updates->dateUpdated->Visible) { // dateUpdated ?>
		<td<?php echo $tbl_updates->dateUpdated->CellAttributes() ?>>
<span<?php echo $tbl_updates->dateUpdated->ViewAttributes() ?>>
<?php echo $tbl_updates->dateUpdated->ListViewValue() ?></span>
<a id="<?php echo $tbl_updates_list->PageObjName . "_row_" . $tbl_updates_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_updates->_field->Visible) { // field ?>
		<td<?php echo $tbl_updates->_field->CellAttributes() ?>>
<span<?php echo $tbl_updates->_field->ViewAttributes() ?>>
<?php echo $tbl_updates->_field->ListViewValue() ?></span>
<a id="<?php echo $tbl_updates_list->PageObjName . "_row_" . $tbl_updates_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_updates->paymentmodeID->Visible) { // paymentmodeID ?>
		<td<?php echo $tbl_updates->paymentmodeID->CellAttributes() ?>>
<span<?php echo $tbl_updates->paymentmodeID->ViewAttributes() ?>>
<?php echo $tbl_updates->paymentmodeID->ListViewValue() ?></span>
<a id="<?php echo $tbl_updates_list->PageObjName . "_row_" . $tbl_updates_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_updates->deathDate->Visible) { // deathDate ?>
		<td<?php echo $tbl_updates->deathDate->CellAttributes() ?>>
<span<?php echo $tbl_updates->deathDate->ViewAttributes() ?>>
<?php echo $tbl_updates->deathDate->ListViewValue() ?></span>
<a id="<?php echo $tbl_updates_list->PageObjName . "_row_" . $tbl_updates_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_updates->Createdby->Visible) { // Createdby ?>
		<td<?php echo $tbl_updates->Createdby->CellAttributes() ?>>
<span<?php echo $tbl_updates->Createdby->ViewAttributes() ?>>
<?php echo $tbl_updates->Createdby->ListViewValue() ?></span>
<a id="<?php echo $tbl_updates_list->PageObjName . "_row_" . $tbl_updates_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_updates->CreatedDate->Visible) { // CreatedDate ?>
		<td<?php echo $tbl_updates->CreatedDate->CellAttributes() ?>>
<span<?php echo $tbl_updates->CreatedDate->ViewAttributes() ?>>
<?php echo $tbl_updates->CreatedDate->ListViewValue() ?></span>
<a id="<?php echo $tbl_updates_list->PageObjName . "_row_" . $tbl_updates_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_updates->UpdatedBy->Visible) { // UpdatedBy ?>
		<td<?php echo $tbl_updates->UpdatedBy->CellAttributes() ?>>
<span<?php echo $tbl_updates->UpdatedBy->ViewAttributes() ?>>
<?php echo $tbl_updates->UpdatedBy->ListViewValue() ?></span>
<a id="<?php echo $tbl_updates_list->PageObjName . "_row_" . $tbl_updates_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_updates->UpdatedDate->Visible) { // UpdatedDate ?>
		<td<?php echo $tbl_updates->UpdatedDate->CellAttributes() ?>>
<span<?php echo $tbl_updates->UpdatedDate->ViewAttributes() ?>>
<?php echo $tbl_updates->UpdatedDate->ListViewValue() ?></span>
<a id="<?php echo $tbl_updates_list->PageObjName . "_row_" . $tbl_updates_list->RowCnt ?>"></a></td>
	<?php } ?>
<?php

// Render list options (body, right)
$tbl_updates_list->ListOptions->Render("body", "right", $tbl_updates_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($tbl_updates->CurrentAction <> "gridadd")
		$tbl_updates_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($tbl_updates->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($tbl_updates_list->Recordset)
	$tbl_updates_list->Recordset->Close();
?>
<?php if ($tbl_updates_list->TotalRecs > 0) { ?>
<div class="ewGridLowerPanel">
<?php if ($tbl_updates->CurrentAction <> "gridadd" && $tbl_updates->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($tbl_updates_list->Pager)) $tbl_updates_list->Pager = new cNumericPager($tbl_updates_list->StartRec, $tbl_updates_list->DisplayRecs, $tbl_updates_list->TotalRecs, $tbl_updates_list->RecRange) ?>
<?php if ($tbl_updates_list->Pager->RecordCount > 0) { ?>
<table cellspacing="0" class="ewStdTable"><tbody><tr><td>
<div class="pagination"><ul>
	<?php if ($tbl_updates_list->Pager->FirstButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_updates_list->PageUrl() ?>start=<?php echo $tbl_updates_list->Pager->FirstButton->Start ?>"><?php echo $Language->Phrase("PagerFirst") ?></a></li>
	<?php } ?>
	<?php if ($tbl_updates_list->Pager->PrevButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_updates_list->PageUrl() ?>start=<?php echo $tbl_updates_list->Pager->PrevButton->Start ?>"><?php echo $Language->Phrase("PagerPrevious") ?></a></li>
	<?php } ?>
	<?php foreach ($tbl_updates_list->Pager->Items as $PagerItem) { ?>
		<li<?php if (!$PagerItem->Enabled) { echo " class=\" active\""; } ?>><a href="<?php if ($PagerItem->Enabled) { echo $tbl_updates_list->PageUrl() . "start=" . $PagerItem->Start; } else { echo "#"; } ?>"><?php echo $PagerItem->Text ?></a></li>
	<?php } ?>
	<?php if ($tbl_updates_list->Pager->NextButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_updates_list->PageUrl() ?>start=<?php echo $tbl_updates_list->Pager->NextButton->Start ?>"><?php echo $Language->Phrase("PagerNext") ?></a></li>
	<?php } ?>
	<?php if ($tbl_updates_list->Pager->LastButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_updates_list->PageUrl() ?>start=<?php echo $tbl_updates_list->Pager->LastButton->Start ?>"><?php echo $Language->Phrase("PagerLast") ?></a></li>
	<?php } ?>
</ul></div>
</td>
<td>
	<?php if ($tbl_updates_list->Pager->ButtonCount > 0) { ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php } ?>
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $tbl_updates_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $tbl_updates_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $tbl_updates_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($tbl_updates_list->SearchWhere == "0=101") { ?>
	<p><?php echo $Language->Phrase("EnterSearchCriteria") ?></p>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoRecord") ?></p>
	<?php } ?>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoPermission") ?></p>
	<?php } ?>
<?php } ?>
</td>
<?php if ($tbl_updates_list->TotalRecs > 0) { ?>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
<input type="hidden" name="t" value="tbl_updates">
<select name="<?php echo EW_TABLE_REC_PER_PAGE ?>" class="input-small" onchange="this.form.submit();">
<option value="20"<?php if ($tbl_updates_list->DisplayRecs == 20) { ?> selected="selected"<?php } ?>>20</option>
<option value="40"<?php if ($tbl_updates_list->DisplayRecs == 40) { ?> selected="selected"<?php } ?>>40</option>
<option value="60"<?php if ($tbl_updates_list->DisplayRecs == 60) { ?> selected="selected"<?php } ?>>60</option>
<option value="ALL"<?php if ($tbl_updates->getRecordsPerPage() == -1) { ?> selected="selected"<?php } ?>><?php echo $Language->Phrase("AllRecords") ?></option>
</select>
</td>
<?php } ?>
</tr></table>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($tbl_updates_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
</div>
<?php } ?>
</td></tr></table>
<script type="text/javascript">
ftbl_updateslistsrch.Init();
ftbl_updateslist.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$tbl_updates_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$tbl_updates_list->Page_Terminate();
?>
