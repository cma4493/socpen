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

$tbl_support_list = NULL; // Initialize page object first

class ctbl_support_list extends ctbl_support {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Table name
	var $TableName = 'tbl_support';

	// Page object name
	var $PageObjName = 'tbl_support_list';

	// Grid form hidden field names
	var $FormName = 'ftbl_supportlist';
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

		// Table object (tbl_support)
		if (!isset($GLOBALS["tbl_support"])) {
			$GLOBALS["tbl_support"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["tbl_support"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "tbl_supportadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "tbl_supportdelete.php";
		$this->MultiUpdateUrl = "tbl_supportupdate.php";

		// Table object (tbl_pensioner)
		if (!isset($GLOBALS['tbl_pensioner'])) $GLOBALS['tbl_pensioner'] = new ctbl_pensioner();

		// Table object (tbl_user)
		if (!isset($GLOBALS['tbl_user'])) $GLOBALS['tbl_user'] = new ctbl_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'tbl_support', TRUE);

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
		$this->supportID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();
		$this->CreatedBy->Visible = !$this->IsAddOrEdit();
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
			$this->supportID->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->supportID->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Advanced search WHERE clause based on QueryString
	function AdvancedSearchWhere() {
		global $Security;
		$sWhere = "";
		if (!$Security->CanSearch()) return "";
		$this->BuildSearchSql($sWhere, $this->supportID, FALSE); // supportID
		$this->BuildSearchSql($sWhere, $this->PensionerID, FALSE); // PensionerID
		$this->BuildSearchSql($sWhere, $this->family_support, FALSE); // family_support
		$this->BuildSearchSql($sWhere, $this->KindSupID, FALSE); // KindSupID
		$this->BuildSearchSql($sWhere, $this->meals, FALSE); // meals
		$this->BuildSearchSql($sWhere, $this->disability, FALSE); // disability
		$this->BuildSearchSql($sWhere, $this->disabilityID, FALSE); // disabilityID
		$this->BuildSearchSql($sWhere, $this->immobile, FALSE); // immobile
		$this->BuildSearchSql($sWhere, $this->assistiveID, FALSE); // assistiveID
		$this->BuildSearchSql($sWhere, $this->preEx_illness, FALSE); // preEx_illness
		$this->BuildSearchSql($sWhere, $this->illnessID, FALSE); // illnessID
		$this->BuildSearchSql($sWhere, $this->physconditionID, FALSE); // physconditionID
		$this->BuildSearchSql($sWhere, $this->CreatedBy, FALSE); // CreatedBy
		$this->BuildSearchSql($sWhere, $this->CreatedDate, FALSE); // CreatedDate
		$this->BuildSearchSql($sWhere, $this->UpdatedBy, FALSE); // UpdatedBy
		$this->BuildSearchSql($sWhere, $this->UpdatedDate, FALSE); // UpdatedDate

		// Set up search parm
		if ($sWhere <> "") {
			$this->Command = "search";
		}
		if ($this->Command == "search") {
			$this->supportID->AdvancedSearch->Save(); // supportID
			$this->PensionerID->AdvancedSearch->Save(); // PensionerID
			$this->family_support->AdvancedSearch->Save(); // family_support
			$this->KindSupID->AdvancedSearch->Save(); // KindSupID
			$this->meals->AdvancedSearch->Save(); // meals
			$this->disability->AdvancedSearch->Save(); // disability
			$this->disabilityID->AdvancedSearch->Save(); // disabilityID
			$this->immobile->AdvancedSearch->Save(); // immobile
			$this->assistiveID->AdvancedSearch->Save(); // assistiveID
			$this->preEx_illness->AdvancedSearch->Save(); // preEx_illness
			$this->illnessID->AdvancedSearch->Save(); // illnessID
			$this->physconditionID->AdvancedSearch->Save(); // physconditionID
			$this->CreatedBy->AdvancedSearch->Save(); // CreatedBy
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

	// Return basic search SQL
	function BasicSearchSQL($Keyword) {
		$sKeyword = ew_AdjustSql($Keyword);
		$sWhere = "";
		$this->BuildBasicSearchSQL($sWhere, $this->PensionerID, $Keyword);
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
		if ($this->supportID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->PensionerID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->family_support->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->KindSupID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->meals->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->disability->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->disabilityID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->immobile->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->assistiveID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->preEx_illness->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->illnessID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->physconditionID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->CreatedBy->AdvancedSearch->IssetSession())
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
		$this->supportID->AdvancedSearch->UnsetSession();
		$this->PensionerID->AdvancedSearch->UnsetSession();
		$this->family_support->AdvancedSearch->UnsetSession();
		$this->KindSupID->AdvancedSearch->UnsetSession();
		$this->meals->AdvancedSearch->UnsetSession();
		$this->disability->AdvancedSearch->UnsetSession();
		$this->disabilityID->AdvancedSearch->UnsetSession();
		$this->immobile->AdvancedSearch->UnsetSession();
		$this->assistiveID->AdvancedSearch->UnsetSession();
		$this->preEx_illness->AdvancedSearch->UnsetSession();
		$this->illnessID->AdvancedSearch->UnsetSession();
		$this->physconditionID->AdvancedSearch->UnsetSession();
		$this->CreatedBy->AdvancedSearch->UnsetSession();
		$this->CreatedDate->AdvancedSearch->UnsetSession();
		$this->UpdatedBy->AdvancedSearch->UnsetSession();
		$this->UpdatedDate->AdvancedSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore basic search values
		$this->BasicSearch->Load();

		// Restore advanced search values
		$this->supportID->AdvancedSearch->Load();
		$this->PensionerID->AdvancedSearch->Load();
		$this->family_support->AdvancedSearch->Load();
		$this->KindSupID->AdvancedSearch->Load();
		$this->meals->AdvancedSearch->Load();
		$this->disability->AdvancedSearch->Load();
		$this->disabilityID->AdvancedSearch->Load();
		$this->immobile->AdvancedSearch->Load();
		$this->assistiveID->AdvancedSearch->Load();
		$this->preEx_illness->AdvancedSearch->Load();
		$this->illnessID->AdvancedSearch->Load();
		$this->physconditionID->AdvancedSearch->Load();
		$this->CreatedBy->AdvancedSearch->Load();
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
			$this->UpdateSort($this->supportID); // supportID
			$this->UpdateSort($this->PensionerID); // PensionerID
			$this->UpdateSort($this->family_support); // family_support
			$this->UpdateSort($this->KindSupID); // KindSupID
			$this->UpdateSort($this->meals); // meals
			$this->UpdateSort($this->disability); // disability
			$this->UpdateSort($this->disabilityID); // disabilityID
			$this->UpdateSort($this->immobile); // immobile
			$this->UpdateSort($this->assistiveID); // assistiveID
			$this->UpdateSort($this->preEx_illness); // preEx_illness
			$this->UpdateSort($this->illnessID); // illnessID
			$this->UpdateSort($this->physconditionID); // physconditionID
			$this->UpdateSort($this->CreatedBy); // CreatedBy
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
				$this->supportID->setSort("");
				$this->PensionerID->setSort("");
				$this->family_support->setSort("");
				$this->KindSupID->setSort("");
				$this->meals->setSort("");
				$this->disability->setSort("");
				$this->disabilityID->setSort("");
				$this->immobile->setSort("");
				$this->assistiveID->setSort("");
				$this->preEx_illness->setSort("");
				$this->illnessID->setSort("");
				$this->physconditionID->setSort("");
				$this->CreatedBy->setSort("");
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
		$oListOpt->Body = "<label class=\"checkbox\"><input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->supportID->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event, this);'></label>";
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
		$item->Body = "<a class=\"btn btn-purple btn-sm\" href=\"\" onclick=\"ew_SubmitSelected(document.ftbl_supportlist, '" . $this->MultiDeleteUrl . "', ewLanguage.Phrase('DeleteMultiConfirmMsg'));return false;\">" . $Language->Phrase("DeleteSelectedLink") . "</a>";
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
				$item->Body = "<a class=\"btn btn-warning btn-sm\" href=\"\" onclick=\"ew_SubmitSelected(document.ftbl_supportlist, '" . ew_CurrentUrl() . "', null, '" . $action . "');return false;\">" . $name . "</a>";
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
		// supportID

		$this->supportID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_supportID"]);
		if ($this->supportID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->supportID->AdvancedSearch->SearchOperator = @$_GET["z_supportID"];

		// PensionerID
		$this->PensionerID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_PensionerID"]);
		if ($this->PensionerID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->PensionerID->AdvancedSearch->SearchOperator = @$_GET["z_PensionerID"];

		// family_support
		$this->family_support->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_family_support"]);
		if ($this->family_support->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->family_support->AdvancedSearch->SearchOperator = @$_GET["z_family_support"];

		// KindSupID
		$this->KindSupID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_KindSupID"]);
		if ($this->KindSupID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->KindSupID->AdvancedSearch->SearchOperator = @$_GET["z_KindSupID"];

		// meals
		$this->meals->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_meals"]);
		if ($this->meals->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->meals->AdvancedSearch->SearchOperator = @$_GET["z_meals"];

		// disability
		$this->disability->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_disability"]);
		if ($this->disability->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->disability->AdvancedSearch->SearchOperator = @$_GET["z_disability"];

		// disabilityID
		$this->disabilityID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_disabilityID"]);
		if ($this->disabilityID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->disabilityID->AdvancedSearch->SearchOperator = @$_GET["z_disabilityID"];

		// immobile
		$this->immobile->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_immobile"]);
		if ($this->immobile->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->immobile->AdvancedSearch->SearchOperator = @$_GET["z_immobile"];

		// assistiveID
		$this->assistiveID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_assistiveID"]);
		if ($this->assistiveID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->assistiveID->AdvancedSearch->SearchOperator = @$_GET["z_assistiveID"];

		// preEx_illness
		$this->preEx_illness->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_preEx_illness"]);
		if ($this->preEx_illness->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->preEx_illness->AdvancedSearch->SearchOperator = @$_GET["z_preEx_illness"];

		// illnessID
		$this->illnessID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_illnessID"]);
		if ($this->illnessID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->illnessID->AdvancedSearch->SearchOperator = @$_GET["z_illnessID"];

		// physconditionID
		$this->physconditionID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_physconditionID"]);
		if ($this->physconditionID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->physconditionID->AdvancedSearch->SearchOperator = @$_GET["z_physconditionID"];

		// CreatedBy
		$this->CreatedBy->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_CreatedBy"]);
		if ($this->CreatedBy->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->CreatedBy->AdvancedSearch->SearchOperator = @$_GET["z_CreatedBy"];

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

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("supportID")) <> "")
			$this->supportID->CurrentValue = $this->getKey("supportID"); // supportID
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
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// supportID
			$this->supportID->EditCustomAttributes = "";
			$this->supportID->EditValue = ew_HtmlEncode($this->supportID->AdvancedSearch->SearchValue);
			$this->supportID->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->supportID->FldCaption()));

			// PensionerID
			$this->PensionerID->EditCustomAttributes = "";
			$this->PensionerID->EditValue = ew_HtmlEncode($this->PensionerID->AdvancedSearch->SearchValue);
			$this->PensionerID->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->PensionerID->FldCaption()));

			// family_support
			$this->family_support->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->family_support->FldTagValue(1), $this->family_support->FldTagCaption(1) <> "" ? $this->family_support->FldTagCaption(1) : $this->family_support->FldTagValue(1));
			$arwrk[] = array($this->family_support->FldTagValue(2), $this->family_support->FldTagCaption(2) <> "" ? $this->family_support->FldTagCaption(2) : $this->family_support->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->family_support->EditValue = $arwrk;

			// KindSupID
			$this->KindSupID->EditCustomAttributes = "";
			if (trim(strval($this->KindSupID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`SupportID`" . ew_SearchString("=", $this->KindSupID->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `SupportID`, `SupportKind` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_support`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->KindSupID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `SupportID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->KindSupID->EditValue = $arwrk;

			// meals
			$this->meals->EditCustomAttributes = "";
			$this->meals->EditValue = ew_HtmlEncode($this->meals->AdvancedSearch->SearchValue);
			$this->meals->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->meals->FldCaption()));

			// disability
			$this->disability->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->disability->FldTagValue(1), $this->disability->FldTagCaption(1) <> "" ? $this->disability->FldTagCaption(1) : $this->disability->FldTagValue(1));
			$arwrk[] = array($this->disability->FldTagValue(2), $this->disability->FldTagCaption(2) <> "" ? $this->disability->FldTagCaption(2) : $this->disability->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->disability->EditValue = $arwrk;

			// disabilityID
			$this->disabilityID->EditCustomAttributes = "";
			if (trim(strval($this->disabilityID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`disabilityID`" . ew_SearchString("=", $this->disabilityID->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `disabilityID`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_disability`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->disabilityID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `disabilityID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->disabilityID->EditValue = $arwrk;

			// immobile
			$this->immobile->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->immobile->FldTagValue(1), $this->immobile->FldTagCaption(1) <> "" ? $this->immobile->FldTagCaption(1) : $this->immobile->FldTagValue(1));
			$arwrk[] = array($this->immobile->FldTagValue(2), $this->immobile->FldTagCaption(2) <> "" ? $this->immobile->FldTagCaption(2) : $this->immobile->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->immobile->EditValue = $arwrk;

			// assistiveID
			$this->assistiveID->EditCustomAttributes = "";
			if (trim(strval($this->assistiveID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`assistiveID`" . ew_SearchString("=", $this->assistiveID->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `assistiveID`, `Device` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_assistive`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->assistiveID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `assistiveID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->assistiveID->EditValue = $arwrk;

			// preEx_illness
			$this->preEx_illness->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->preEx_illness->FldTagValue(1), $this->preEx_illness->FldTagCaption(1) <> "" ? $this->preEx_illness->FldTagCaption(1) : $this->preEx_illness->FldTagValue(1));
			$arwrk[] = array($this->preEx_illness->FldTagValue(2), $this->preEx_illness->FldTagCaption(2) <> "" ? $this->preEx_illness->FldTagCaption(2) : $this->preEx_illness->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->preEx_illness->EditValue = $arwrk;

			// illnessID
			$this->illnessID->EditCustomAttributes = "";
			if (trim(strval($this->illnessID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`illnessID`" . ew_SearchString("=", $this->illnessID->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `illnessID`, `description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_illness`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->illnessID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `illnessID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->illnessID->EditValue = $arwrk;

			// physconditionID
			$this->physconditionID->EditCustomAttributes = "";
			if (trim(strval($this->physconditionID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`physconditionID`" . ew_SearchString("=", $this->physconditionID->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER);
			}
			$sSqlWrk = "SELECT `physconditionID`, `physconditionName` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `lib_physical_condition`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->physconditionID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `physconditionID` ASC";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->physconditionID->EditValue = $arwrk;

			// CreatedBy
			$this->CreatedBy->EditCustomAttributes = "";
			$this->CreatedBy->EditValue = ew_HtmlEncode($this->CreatedBy->AdvancedSearch->SearchValue);
			$this->CreatedBy->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->CreatedBy->FldCaption()));

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
		$this->supportID->AdvancedSearch->Load();
		$this->PensionerID->AdvancedSearch->Load();
		$this->family_support->AdvancedSearch->Load();
		$this->KindSupID->AdvancedSearch->Load();
		$this->meals->AdvancedSearch->Load();
		$this->disability->AdvancedSearch->Load();
		$this->disabilityID->AdvancedSearch->Load();
		$this->immobile->AdvancedSearch->Load();
		$this->assistiveID->AdvancedSearch->Load();
		$this->preEx_illness->AdvancedSearch->Load();
		$this->illnessID->AdvancedSearch->Load();
		$this->physconditionID->AdvancedSearch->Load();
		$this->CreatedBy->AdvancedSearch->Load();
		$this->CreatedDate->AdvancedSearch->Load();
		$this->UpdatedBy->AdvancedSearch->Load();
		$this->UpdatedDate->AdvancedSearch->Load();
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
		$table = 'tbl_support';
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
if (!isset($tbl_support_list)) $tbl_support_list = new ctbl_support_list();

// Page init
$tbl_support_list->Page_Init();

// Page main
$tbl_support_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$tbl_support_list->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var tbl_support_list = new ew_Page("tbl_support_list");
tbl_support_list.PageID = "list"; // Page ID
var EW_PAGE_ID = tbl_support_list.PageID; // For backward compatibility

// Form object
var ftbl_supportlist = new ew_Form("ftbl_supportlist");
ftbl_supportlist.FormKeyCountName = '<?php echo $tbl_support_list->FormKeyCountName ?>';

// Form_CustomValidate event
ftbl_supportlist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftbl_supportlist.ValidateRequired = true;
<?php } else { ?>
ftbl_supportlist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
ftbl_supportlist.Lists["x_KindSupID"] = {"LinkField":"x_SupportID","Ajax":true,"AutoFill":false,"DisplayFields":["x_SupportKind","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_supportlist.Lists["x_disabilityID"] = {"LinkField":"x_disabilityID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_supportlist.Lists["x_assistiveID"] = {"LinkField":"x_assistiveID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Device","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_supportlist.Lists["x_illnessID"] = {"LinkField":"x_illnessID","Ajax":true,"AutoFill":false,"DisplayFields":["x_description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_supportlist.Lists["x_physconditionID"] = {"LinkField":"x_physconditionID","Ajax":true,"AutoFill":false,"DisplayFields":["x_physconditionName","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
var ftbl_supportlistsrch = new ew_Form("ftbl_supportlistsrch");

// Validate function for search
ftbl_supportlistsrch.Validate = function(fobj) {
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
ftbl_supportlistsrch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftbl_supportlistsrch.ValidateRequired = true; // Use JavaScript validation
<?php } else { ?>
ftbl_supportlistsrch.ValidateRequired = false; // No JavaScript validation
<?php } ?>

// Dynamic selection lists
ftbl_supportlistsrch.Lists["x_KindSupID"] = {"LinkField":"x_SupportID","Ajax":true,"AutoFill":false,"DisplayFields":["x_SupportKind","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_supportlistsrch.Lists["x_disabilityID"] = {"LinkField":"x_disabilityID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_supportlistsrch.Lists["x_assistiveID"] = {"LinkField":"x_assistiveID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Device","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_supportlistsrch.Lists["x_illnessID"] = {"LinkField":"x_illnessID","Ajax":true,"AutoFill":false,"DisplayFields":["x_description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_supportlistsrch.Lists["x_physconditionID"] = {"LinkField":"x_physconditionID","Ajax":true,"AutoFill":false,"DisplayFields":["x_physconditionName","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php //$Breadcrumb->Render(); ?>
<?php if ($tbl_support->getCurrentMasterTable() == "" && $tbl_support_list->ExportOptions->Visible()) { ?>
<div class="ewListExportOptions"><?php $tbl_support_list->ExportOptions->Render("body") ?></div>
<?php } ?>
<?php if (($tbl_support->Export == "") || (EW_EXPORT_MASTER_RECORD && $tbl_support->Export == "print")) { ?>
<?php
$gsMasterReturnUrl = "tbl_pensionerlist.php";
if ($tbl_support_list->DbMasterFilter <> "" && $tbl_support->getCurrentMasterTable() == "tbl_pensioner") {
	if ($tbl_support_list->MasterRecordExists) {
		if ($tbl_support->getCurrentMasterTable() == $tbl_support->TableVar) $gsMasterReturnUrl .= "?" . EW_TABLE_SHOW_MASTER . "=";
?>
<?php if ($tbl_support_list->ExportOptions->Visible()) { ?>
<div class="ewListExportOptions"><?php $tbl_support_list->ExportOptions->Render("body") ?></div>
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
		$tbl_support_list->TotalRecs = $tbl_support->SelectRecordCount();
	} else {
		if ($tbl_support_list->Recordset = $tbl_support_list->LoadRecordset())
			$tbl_support_list->TotalRecs = $tbl_support_list->Recordset->RecordCount();
	}
	$tbl_support_list->StartRec = 1;
	if ($tbl_support_list->DisplayRecs <= 0 || ($tbl_support->Export <> "" && $tbl_support->ExportAll)) // Display all records
		$tbl_support_list->DisplayRecs = $tbl_support_list->TotalRecs;
	if (!($tbl_support->Export <> "" && $tbl_support->ExportAll))
		$tbl_support_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$tbl_support_list->Recordset = $tbl_support_list->LoadRecordset($tbl_support_list->StartRec-1, $tbl_support_list->DisplayRecs);
$tbl_support_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($tbl_support->Export == "" && $tbl_support->CurrentAction == "") { ?>
<form name="ftbl_supportlistsrch" id="ftbl_supportlistsrch" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>">
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
	<div id="ftbl_supportlistsrch_SearchPanel">
		<input type="hidden" name="cmd" value="search">
		<input type="hidden" name="t" value="tbl_support">
		<div class="ewBasicSearch">
<?php
if ($gsSearchError == "")
	$tbl_support_list->LoadAdvancedSearch(); // Load advanced search

// Render for search
$tbl_support->RowType = EW_ROWTYPE_SEARCH;

// Render row
$tbl_support->ResetAttrs();
$tbl_support_list->RenderRow();
?>
<div id="xsr_1" class="ewRow">
<?php if ($tbl_support->family_support->Visible) { // family_support ?>
	<span id="xsc_family_support" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_support->family_support->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_family_support" id="z_family_support" value="="></span>
		<span class="control-group ewSearchField">
<select data-field="x_family_support" id="x_family_support" name="x_family_support"<?php echo $tbl_support->family_support->EditAttributes() ?>>
<?php
if (is_array($tbl_support->family_support->EditValue)) {
	$arwrk = $tbl_support->family_support->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->family_support->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<div id="xsr_2" class="ewRow">
<?php if ($tbl_support->KindSupID->Visible) { // KindSupID ?>
	<span id="xsc_KindSupID" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_support->KindSupID->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_KindSupID" id="z_KindSupID" value="="></span>
		<span class="control-group ewSearchField">
<select data-field="x_KindSupID" id="x_KindSupID" name="x_KindSupID"<?php echo $tbl_support->KindSupID->EditAttributes() ?>>
<?php
if (is_array($tbl_support->KindSupID->EditValue)) {
	$arwrk = $tbl_support->KindSupID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->KindSupID->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
$sSqlWrk = "SELECT `SupportID`, `SupportKind` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_support`";
$sWhereWrk = "";

// Call Lookup selecting
$tbl_support->Lookup_Selecting($tbl_support->KindSupID, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `SupportID` ASC";
?>
<input type="hidden" name="s_x_KindSupID" id="s_x_KindSupID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`SupportID` = {filter_value}"); ?>&t0=3">
</span>
	</span>
<?php } ?>
</div>
<div id="xsr_3" class="ewRow">
<?php if ($tbl_support->disability->Visible) { // disability ?>
	<span id="xsc_disability" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_support->disability->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_disability" id="z_disability" value="="></span>
		<span class="control-group ewSearchField">
<select data-field="x_disability" id="x_disability" name="x_disability"<?php echo $tbl_support->disability->EditAttributes() ?>>
<?php
if (is_array($tbl_support->disability->EditValue)) {
	$arwrk = $tbl_support->disability->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->disability->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<div id="xsr_4" class="ewRow">
<?php if ($tbl_support->disabilityID->Visible) { // disabilityID ?>
	<span id="xsc_disabilityID" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_support->disabilityID->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_disabilityID" id="z_disabilityID" value="="></span>
		<span class="control-group ewSearchField">
<select data-field="x_disabilityID" id="x_disabilityID" name="x_disabilityID"<?php echo $tbl_support->disabilityID->EditAttributes() ?>>
<?php
if (is_array($tbl_support->disabilityID->EditValue)) {
	$arwrk = $tbl_support->disabilityID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->disabilityID->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
$sSqlWrk = "SELECT `disabilityID`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_disability`";
$sWhereWrk = "";

// Call Lookup selecting
$tbl_support->Lookup_Selecting($tbl_support->disabilityID, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `disabilityID` ASC";
?>
<input type="hidden" name="s_x_disabilityID" id="s_x_disabilityID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`disabilityID` = {filter_value}"); ?>&t0=3">
</span>
	</span>
<?php } ?>
</div>
<div id="xsr_5" class="ewRow">
<?php if ($tbl_support->immobile->Visible) { // immobile ?>
	<span id="xsc_immobile" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_support->immobile->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_immobile" id="z_immobile" value="="></span>
		<span class="control-group ewSearchField">
<select data-field="x_immobile" id="x_immobile" name="x_immobile"<?php echo $tbl_support->immobile->EditAttributes() ?>>
<?php
if (is_array($tbl_support->immobile->EditValue)) {
	$arwrk = $tbl_support->immobile->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->immobile->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php if ($tbl_support->assistiveID->Visible) { // assistiveID ?>
	<span id="xsc_assistiveID" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_support->assistiveID->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_assistiveID" id="z_assistiveID" value="="></span>
		<span class="control-group ewSearchField">
<select data-field="x_assistiveID" id="x_assistiveID" name="x_assistiveID"<?php echo $tbl_support->assistiveID->EditAttributes() ?>>
<?php
if (is_array($tbl_support->assistiveID->EditValue)) {
	$arwrk = $tbl_support->assistiveID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->assistiveID->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
$sSqlWrk = "SELECT `assistiveID`, `Device` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_assistive`";
$sWhereWrk = "";

// Call Lookup selecting
$tbl_support->Lookup_Selecting($tbl_support->assistiveID, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `assistiveID` ASC";
?>
<input type="hidden" name="s_x_assistiveID" id="s_x_assistiveID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`assistiveID` = {filter_value}"); ?>&t0=3">
</span>
	</span>
<?php } ?>
</div>
<div id="xsr_7" class="ewRow">
<?php if ($tbl_support->preEx_illness->Visible) { // preEx_illness ?>
	<span id="xsc_preEx_illness" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_support->preEx_illness->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_preEx_illness" id="z_preEx_illness" value="="></span>
		<span class="control-group ewSearchField">
<select data-field="x_preEx_illness" id="x_preEx_illness" name="x_preEx_illness"<?php echo $tbl_support->preEx_illness->EditAttributes() ?>>
<?php
if (is_array($tbl_support->preEx_illness->EditValue)) {
	$arwrk = $tbl_support->preEx_illness->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->preEx_illness->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<div id="xsr_8" class="ewRow">
<?php if ($tbl_support->illnessID->Visible) { // illnessID ?>
	<span id="xsc_illnessID" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_support->illnessID->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_illnessID" id="z_illnessID" value="="></span>
		<span class="control-group ewSearchField">
<select data-field="x_illnessID" id="x_illnessID" name="x_illnessID"<?php echo $tbl_support->illnessID->EditAttributes() ?>>
<?php
if (is_array($tbl_support->illnessID->EditValue)) {
	$arwrk = $tbl_support->illnessID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->illnessID->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
$sSqlWrk = "SELECT `illnessID`, `description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_illness`";
$sWhereWrk = "";

// Call Lookup selecting
$tbl_support->Lookup_Selecting($tbl_support->illnessID, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `illnessID` ASC";
?>
<input type="hidden" name="s_x_illnessID" id="s_x_illnessID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`illnessID` = {filter_value}"); ?>&t0=3">
</span>
	</span>
<?php } ?>
</div>
<div id="xsr_9" class="ewRow">
<?php if ($tbl_support->physconditionID->Visible) { // physconditionID ?>
	<span id="xsc_physconditionID" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_support->physconditionID->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_physconditionID" id="z_physconditionID" value="="></span>
		<span class="control-group ewSearchField">
<select data-field="x_physconditionID" id="x_physconditionID" name="x_physconditionID"<?php echo $tbl_support->physconditionID->EditAttributes() ?>>
<?php
if (is_array($tbl_support->physconditionID->EditValue)) {
	$arwrk = $tbl_support->physconditionID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_support->physconditionID->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
$sSqlWrk = "SELECT `physconditionID`, `physconditionName` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `lib_physical_condition`";
$sWhereWrk = "";

// Call Lookup selecting
$tbl_support->Lookup_Selecting($tbl_support->physconditionID, $sWhereWrk);
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `physconditionID` ASC";
?>
<input type="hidden" name="s_x_physconditionID" id="s_x_physconditionID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&f0=<?php echo ew_Encrypt("`physconditionID` = {filter_value}"); ?>&t0=3">
</span>
	</span>
<?php } ?>
</div>
<div id="xsr_10" class="row">
	<div class="col-xs-12 col-sm-4">
	<div class="input-group">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="form-control search-query" value="<?php echo ew_HtmlEncode($tbl_support_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo $Language->Phrase("Search") ?>">
	<span class="input-group-btn">
	<button class="btn btn-purple btn-sm" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $Language->Phrase("QuickSearchBtn") ?> <i class="icon-search icon-on-right bigger-110"></i></button>&nbsp;
	<a type="button" class="btn btn-success btn-sm" href="<?php echo $tbl_support_list->PageUrl() ?>cmd=reset">ShowAll <i class="icon-refresh icon-on-right bigger-110"></i></a>
	</span>
	</div>
	</div>
	<div class="btn-group ewButtonGroup">
	<!--<a class="btn ewShowAll" href="<?php echo $tbl_support_list->PageUrl() ?>cmd=reset"><?php echo $Language->Phrase("ShowAll") ?></a> -->
</div>
<div id="xsr_11" class="radio">
	<label><input class="ace" type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="="<?php if ($tbl_support_list->BasicSearch->getType() == "=") { ?> checked="checked"<?php } ?>><span class="lbl"><?php echo $Language->Phrase("ExactPhrase") ?></span></label>
	<label><input class="ace" type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="AND"<?php if ($tbl_support_list->BasicSearch->getType() == "AND") { ?> checked="checked"<?php } ?>><span class="lbl"><?php echo $Language->Phrase("AllWord") ?></span></label>
	<label><input class="ace" type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="OR"<?php if ($tbl_support_list->BasicSearch->getType() == "OR") { ?> checked="checked"<?php } ?>><span class="lbl"><?php echo $Language->Phrase("AnyWord") ?></span></label>
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
<?php $tbl_support_list->ShowPageHeader(); ?>
<?php
$tbl_support_list->ShowMessage();
?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridUpperPanel">
<?php if ($tbl_support->CurrentAction <> "gridadd" && $tbl_support->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($tbl_support_list->Pager)) $tbl_support_list->Pager = new cNumericPager($tbl_support_list->StartRec, $tbl_support_list->DisplayRecs, $tbl_support_list->TotalRecs, $tbl_support_list->RecRange) ?>
<?php if ($tbl_support_list->Pager->RecordCount > 0) { ?>
<table cellspacing="0" class="ewStdTable"><tbody><tr><td>
<div class="pagination"><ul>
	<?php if ($tbl_support_list->Pager->FirstButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_support_list->PageUrl() ?>start=<?php echo $tbl_support_list->Pager->FirstButton->Start ?>"><?php echo $Language->Phrase("PagerFirst") ?></a></li>
	<?php } ?>
	<?php if ($tbl_support_list->Pager->PrevButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_support_list->PageUrl() ?>start=<?php echo $tbl_support_list->Pager->PrevButton->Start ?>"><?php echo $Language->Phrase("PagerPrevious") ?></a></li>
	<?php } ?>
	<?php foreach ($tbl_support_list->Pager->Items as $PagerItem) { ?>
		<li<?php if (!$PagerItem->Enabled) { echo " class=\" active\""; } ?>><a href="<?php if ($PagerItem->Enabled) { echo $tbl_support_list->PageUrl() . "start=" . $PagerItem->Start; } else { echo "#"; } ?>"><?php echo $PagerItem->Text ?></a></li>
	<?php } ?>
	<?php if ($tbl_support_list->Pager->NextButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_support_list->PageUrl() ?>start=<?php echo $tbl_support_list->Pager->NextButton->Start ?>"><?php echo $Language->Phrase("PagerNext") ?></a></li>
	<?php } ?>
	<?php if ($tbl_support_list->Pager->LastButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_support_list->PageUrl() ?>start=<?php echo $tbl_support_list->Pager->LastButton->Start ?>"><?php echo $Language->Phrase("PagerLast") ?></a></li>
	<?php } ?>
</ul></div>
</td>
<td>
	<?php if ($tbl_support_list->Pager->ButtonCount > 0) { ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php } ?>
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $tbl_support_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $tbl_support_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $tbl_support_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($tbl_support_list->SearchWhere == "0=101") { ?>
	<p><?php echo $Language->Phrase("EnterSearchCriteria") ?></p>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoRecord") ?></p>
	<?php } ?>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoPermission") ?></p>
	<?php } ?>
<?php } ?>
</td>
<?php if ($tbl_support_list->TotalRecs > 0) { ?>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
<input type="hidden" name="t" value="tbl_support">
<select name="<?php echo EW_TABLE_REC_PER_PAGE ?>" class="input-small" onchange="this.form.submit();">
<option value="20"<?php if ($tbl_support_list->DisplayRecs == 20) { ?> selected="selected"<?php } ?>>20</option>
<option value="40"<?php if ($tbl_support_list->DisplayRecs == 40) { ?> selected="selected"<?php } ?>>40</option>
<option value="60"<?php if ($tbl_support_list->DisplayRecs == 60) { ?> selected="selected"<?php } ?>>60</option>
<option value="ALL"<?php if ($tbl_support->getRecordsPerPage() == -1) { ?> selected="selected"<?php } ?>><?php echo $Language->Phrase("AllRecords") ?></option>
</select>
</td>
<?php } ?>
</tr></table>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($tbl_support_list->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
</div>
<form name="ftbl_supportlist" id="ftbl_supportlist" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="tbl_support">
<div id="gmp_tbl_support" class="ewGridMiddlePanel">
<?php if ($tbl_support_list->TotalRecs > 0) { ?>
<table id="tbl_tbl_supportlist" class="ewTable ewTableSeparate">
<?php echo $tbl_support->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$tbl_support_list->RenderListOptions();

// Render list options (header, left)
$tbl_support_list->ListOptions->Render("header", "left");
?>
<?php if ($tbl_support->supportID->Visible) { // supportID ?>
	<?php if ($tbl_support->SortUrl($tbl_support->supportID) == "") { ?>
		<td><div id="elh_tbl_support_supportID" class="tbl_support_supportID"><div class="ewTableHeaderCaption"><?php echo $tbl_support->supportID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_support->SortUrl($tbl_support->supportID) ?>',1);"><div id="elh_tbl_support_supportID" class="tbl_support_supportID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->supportID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->supportID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->supportID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_support->PensionerID->Visible) { // PensionerID ?>
	<?php if ($tbl_support->SortUrl($tbl_support->PensionerID) == "") { ?>
		<td><div id="elh_tbl_support_PensionerID" class="tbl_support_PensionerID"><div class="ewTableHeaderCaption"><?php echo $tbl_support->PensionerID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_support->SortUrl($tbl_support->PensionerID) ?>',1);"><div id="elh_tbl_support_PensionerID" class="tbl_support_PensionerID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->PensionerID->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->PensionerID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->PensionerID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_support->family_support->Visible) { // family_support ?>
	<?php if ($tbl_support->SortUrl($tbl_support->family_support) == "") { ?>
		<td><div id="elh_tbl_support_family_support" class="tbl_support_family_support"><div class="ewTableHeaderCaption"><?php echo $tbl_support->family_support->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_support->SortUrl($tbl_support->family_support) ?>',1);"><div id="elh_tbl_support_family_support" class="tbl_support_family_support">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->family_support->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->family_support->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->family_support->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_support->KindSupID->Visible) { // KindSupID ?>
	<?php if ($tbl_support->SortUrl($tbl_support->KindSupID) == "") { ?>
		<td><div id="elh_tbl_support_KindSupID" class="tbl_support_KindSupID"><div class="ewTableHeaderCaption"><?php echo $tbl_support->KindSupID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_support->SortUrl($tbl_support->KindSupID) ?>',1);"><div id="elh_tbl_support_KindSupID" class="tbl_support_KindSupID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->KindSupID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->KindSupID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->KindSupID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_support->meals->Visible) { // meals ?>
	<?php if ($tbl_support->SortUrl($tbl_support->meals) == "") { ?>
		<td><div id="elh_tbl_support_meals" class="tbl_support_meals"><div class="ewTableHeaderCaption"><?php echo $tbl_support->meals->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_support->SortUrl($tbl_support->meals) ?>',1);"><div id="elh_tbl_support_meals" class="tbl_support_meals">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->meals->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->meals->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->meals->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_support->disability->Visible) { // disability ?>
	<?php if ($tbl_support->SortUrl($tbl_support->disability) == "") { ?>
		<td><div id="elh_tbl_support_disability" class="tbl_support_disability"><div class="ewTableHeaderCaption"><?php echo $tbl_support->disability->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_support->SortUrl($tbl_support->disability) ?>',1);"><div id="elh_tbl_support_disability" class="tbl_support_disability">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->disability->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->disability->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->disability->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_support->disabilityID->Visible) { // disabilityID ?>
	<?php if ($tbl_support->SortUrl($tbl_support->disabilityID) == "") { ?>
		<td><div id="elh_tbl_support_disabilityID" class="tbl_support_disabilityID"><div class="ewTableHeaderCaption"><?php echo $tbl_support->disabilityID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_support->SortUrl($tbl_support->disabilityID) ?>',1);"><div id="elh_tbl_support_disabilityID" class="tbl_support_disabilityID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->disabilityID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->disabilityID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->disabilityID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_support->immobile->Visible) { // immobile ?>
	<?php if ($tbl_support->SortUrl($tbl_support->immobile) == "") { ?>
		<td><div id="elh_tbl_support_immobile" class="tbl_support_immobile"><div class="ewTableHeaderCaption"><?php echo $tbl_support->immobile->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_support->SortUrl($tbl_support->immobile) ?>',1);"><div id="elh_tbl_support_immobile" class="tbl_support_immobile">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->immobile->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->immobile->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->immobile->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_support->assistiveID->Visible) { // assistiveID ?>
	<?php if ($tbl_support->SortUrl($tbl_support->assistiveID) == "") { ?>
		<td><div id="elh_tbl_support_assistiveID" class="tbl_support_assistiveID"><div class="ewTableHeaderCaption"><?php echo $tbl_support->assistiveID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_support->SortUrl($tbl_support->assistiveID) ?>',1);"><div id="elh_tbl_support_assistiveID" class="tbl_support_assistiveID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->assistiveID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->assistiveID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->assistiveID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_support->preEx_illness->Visible) { // preEx_illness ?>
	<?php if ($tbl_support->SortUrl($tbl_support->preEx_illness) == "") { ?>
		<td><div id="elh_tbl_support_preEx_illness" class="tbl_support_preEx_illness"><div class="ewTableHeaderCaption"><?php echo $tbl_support->preEx_illness->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_support->SortUrl($tbl_support->preEx_illness) ?>',1);"><div id="elh_tbl_support_preEx_illness" class="tbl_support_preEx_illness">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->preEx_illness->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->preEx_illness->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->preEx_illness->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_support->illnessID->Visible) { // illnessID ?>
	<?php if ($tbl_support->SortUrl($tbl_support->illnessID) == "") { ?>
		<td><div id="elh_tbl_support_illnessID" class="tbl_support_illnessID"><div class="ewTableHeaderCaption"><?php echo $tbl_support->illnessID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_support->SortUrl($tbl_support->illnessID) ?>',1);"><div id="elh_tbl_support_illnessID" class="tbl_support_illnessID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->illnessID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->illnessID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->illnessID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_support->physconditionID->Visible) { // physconditionID ?>
	<?php if ($tbl_support->SortUrl($tbl_support->physconditionID) == "") { ?>
		<td><div id="elh_tbl_support_physconditionID" class="tbl_support_physconditionID"><div class="ewTableHeaderCaption"><?php echo $tbl_support->physconditionID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_support->SortUrl($tbl_support->physconditionID) ?>',1);"><div id="elh_tbl_support_physconditionID" class="tbl_support_physconditionID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->physconditionID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->physconditionID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->physconditionID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_support->CreatedBy->Visible) { // CreatedBy ?>
	<?php if ($tbl_support->SortUrl($tbl_support->CreatedBy) == "") { ?>
		<td><div id="elh_tbl_support_CreatedBy" class="tbl_support_CreatedBy"><div class="ewTableHeaderCaption"><?php echo $tbl_support->CreatedBy->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_support->SortUrl($tbl_support->CreatedBy) ?>',1);"><div id="elh_tbl_support_CreatedBy" class="tbl_support_CreatedBy">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->CreatedBy->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->CreatedBy->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->CreatedBy->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_support->CreatedDate->Visible) { // CreatedDate ?>
	<?php if ($tbl_support->SortUrl($tbl_support->CreatedDate) == "") { ?>
		<td><div id="elh_tbl_support_CreatedDate" class="tbl_support_CreatedDate"><div class="ewTableHeaderCaption"><?php echo $tbl_support->CreatedDate->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_support->SortUrl($tbl_support->CreatedDate) ?>',1);"><div id="elh_tbl_support_CreatedDate" class="tbl_support_CreatedDate">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->CreatedDate->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->CreatedDate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->CreatedDate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_support->UpdatedBy->Visible) { // UpdatedBy ?>
	<?php if ($tbl_support->SortUrl($tbl_support->UpdatedBy) == "") { ?>
		<td><div id="elh_tbl_support_UpdatedBy" class="tbl_support_UpdatedBy"><div class="ewTableHeaderCaption"><?php echo $tbl_support->UpdatedBy->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_support->SortUrl($tbl_support->UpdatedBy) ?>',1);"><div id="elh_tbl_support_UpdatedBy" class="tbl_support_UpdatedBy">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->UpdatedBy->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->UpdatedBy->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->UpdatedBy->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_support->UpdatedDate->Visible) { // UpdatedDate ?>
	<?php if ($tbl_support->SortUrl($tbl_support->UpdatedDate) == "") { ?>
		<td><div id="elh_tbl_support_UpdatedDate" class="tbl_support_UpdatedDate"><div class="ewTableHeaderCaption"><?php echo $tbl_support->UpdatedDate->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_support->SortUrl($tbl_support->UpdatedDate) ?>',1);"><div id="elh_tbl_support_UpdatedDate" class="tbl_support_UpdatedDate">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_support->UpdatedDate->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_support->UpdatedDate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_support->UpdatedDate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$tbl_support_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($tbl_support->ExportAll && $tbl_support->Export <> "") {
	$tbl_support_list->StopRec = $tbl_support_list->TotalRecs;
} else {

	// Set the last record to display
	if ($tbl_support_list->TotalRecs > $tbl_support_list->StartRec + $tbl_support_list->DisplayRecs - 1)
		$tbl_support_list->StopRec = $tbl_support_list->StartRec + $tbl_support_list->DisplayRecs - 1;
	else
		$tbl_support_list->StopRec = $tbl_support_list->TotalRecs;
}
$tbl_support_list->RecCnt = $tbl_support_list->StartRec - 1;
if ($tbl_support_list->Recordset && !$tbl_support_list->Recordset->EOF) {
	$tbl_support_list->Recordset->MoveFirst();
	if (!$bSelectLimit && $tbl_support_list->StartRec > 1)
		$tbl_support_list->Recordset->Move($tbl_support_list->StartRec - 1);
} elseif (!$tbl_support->AllowAddDeleteRow && $tbl_support_list->StopRec == 0) {
	$tbl_support_list->StopRec = $tbl_support->GridAddRowCount;
}

// Initialize aggregate
$tbl_support->RowType = EW_ROWTYPE_AGGREGATEINIT;
$tbl_support->ResetAttrs();
$tbl_support_list->RenderRow();
while ($tbl_support_list->RecCnt < $tbl_support_list->StopRec) {
	$tbl_support_list->RecCnt++;
	if (intval($tbl_support_list->RecCnt) >= intval($tbl_support_list->StartRec)) {
		$tbl_support_list->RowCnt++;

		// Set up key count
		$tbl_support_list->KeyCount = $tbl_support_list->RowIndex;

		// Init row class and style
		$tbl_support->ResetAttrs();
		$tbl_support->CssClass = "";
		if ($tbl_support->CurrentAction == "gridadd") {
		} else {
			$tbl_support_list->LoadRowValues($tbl_support_list->Recordset); // Load row values
		}
		$tbl_support->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$tbl_support->RowAttrs = array_merge($tbl_support->RowAttrs, array('data-rowindex'=>$tbl_support_list->RowCnt, 'id'=>'r' . $tbl_support_list->RowCnt . '_tbl_support', 'data-rowtype'=>$tbl_support->RowType));

		// Render row
		$tbl_support_list->RenderRow();

		// Render list options
		$tbl_support_list->RenderListOptions();
?>
	<tr<?php echo $tbl_support->RowAttributes() ?>>
<?php

// Render list options (body, left)
$tbl_support_list->ListOptions->Render("body", "left", $tbl_support_list->RowCnt);
?>
	<?php if ($tbl_support->supportID->Visible) { // supportID ?>
		<td<?php echo $tbl_support->supportID->CellAttributes() ?>>
<span<?php echo $tbl_support->supportID->ViewAttributes() ?>>
<?php echo $tbl_support->supportID->ListViewValue() ?></span>
<a id="<?php echo $tbl_support_list->PageObjName . "_row_" . $tbl_support_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_support->PensionerID->Visible) { // PensionerID ?>
		<td<?php echo $tbl_support->PensionerID->CellAttributes() ?>>
<span<?php echo $tbl_support->PensionerID->ViewAttributes() ?>>
<?php echo $tbl_support->PensionerID->ListViewValue() ?></span>
<a id="<?php echo $tbl_support_list->PageObjName . "_row_" . $tbl_support_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_support->family_support->Visible) { // family_support ?>
		<td<?php echo $tbl_support->family_support->CellAttributes() ?>>
<span<?php echo $tbl_support->family_support->ViewAttributes() ?>>
<?php echo $tbl_support->family_support->ListViewValue() ?></span>
<a id="<?php echo $tbl_support_list->PageObjName . "_row_" . $tbl_support_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_support->KindSupID->Visible) { // KindSupID ?>
		<td<?php echo $tbl_support->KindSupID->CellAttributes() ?>>
<span<?php echo $tbl_support->KindSupID->ViewAttributes() ?>>
<?php echo $tbl_support->KindSupID->ListViewValue() ?></span>
<a id="<?php echo $tbl_support_list->PageObjName . "_row_" . $tbl_support_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_support->meals->Visible) { // meals ?>
		<td<?php echo $tbl_support->meals->CellAttributes() ?>>
<span<?php echo $tbl_support->meals->ViewAttributes() ?>>
<?php echo $tbl_support->meals->ListViewValue() ?></span>
<a id="<?php echo $tbl_support_list->PageObjName . "_row_" . $tbl_support_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_support->disability->Visible) { // disability ?>
		<td<?php echo $tbl_support->disability->CellAttributes() ?>>
<span<?php echo $tbl_support->disability->ViewAttributes() ?>>
<?php echo $tbl_support->disability->ListViewValue() ?></span>
<a id="<?php echo $tbl_support_list->PageObjName . "_row_" . $tbl_support_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_support->disabilityID->Visible) { // disabilityID ?>
		<td<?php echo $tbl_support->disabilityID->CellAttributes() ?>>
<span<?php echo $tbl_support->disabilityID->ViewAttributes() ?>>
<?php echo $tbl_support->disabilityID->ListViewValue() ?></span>
<a id="<?php echo $tbl_support_list->PageObjName . "_row_" . $tbl_support_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_support->immobile->Visible) { // immobile ?>
		<td<?php echo $tbl_support->immobile->CellAttributes() ?>>
<span<?php echo $tbl_support->immobile->ViewAttributes() ?>>
<?php echo $tbl_support->immobile->ListViewValue() ?></span>
<a id="<?php echo $tbl_support_list->PageObjName . "_row_" . $tbl_support_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_support->assistiveID->Visible) { // assistiveID ?>
		<td<?php echo $tbl_support->assistiveID->CellAttributes() ?>>
<span<?php echo $tbl_support->assistiveID->ViewAttributes() ?>>
<?php echo $tbl_support->assistiveID->ListViewValue() ?></span>
<a id="<?php echo $tbl_support_list->PageObjName . "_row_" . $tbl_support_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_support->preEx_illness->Visible) { // preEx_illness ?>
		<td<?php echo $tbl_support->preEx_illness->CellAttributes() ?>>
<span<?php echo $tbl_support->preEx_illness->ViewAttributes() ?>>
<?php echo $tbl_support->preEx_illness->ListViewValue() ?></span>
<a id="<?php echo $tbl_support_list->PageObjName . "_row_" . $tbl_support_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_support->illnessID->Visible) { // illnessID ?>
		<td<?php echo $tbl_support->illnessID->CellAttributes() ?>>
<span<?php echo $tbl_support->illnessID->ViewAttributes() ?>>
<?php echo $tbl_support->illnessID->ListViewValue() ?></span>
<a id="<?php echo $tbl_support_list->PageObjName . "_row_" . $tbl_support_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_support->physconditionID->Visible) { // physconditionID ?>
		<td<?php echo $tbl_support->physconditionID->CellAttributes() ?>>
<span<?php echo $tbl_support->physconditionID->ViewAttributes() ?>>
<?php echo $tbl_support->physconditionID->ListViewValue() ?></span>
<a id="<?php echo $tbl_support_list->PageObjName . "_row_" . $tbl_support_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_support->CreatedBy->Visible) { // CreatedBy ?>
		<td<?php echo $tbl_support->CreatedBy->CellAttributes() ?>>
<span<?php echo $tbl_support->CreatedBy->ViewAttributes() ?>>
<?php echo $tbl_support->CreatedBy->ListViewValue() ?></span>
<a id="<?php echo $tbl_support_list->PageObjName . "_row_" . $tbl_support_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_support->CreatedDate->Visible) { // CreatedDate ?>
		<td<?php echo $tbl_support->CreatedDate->CellAttributes() ?>>
<span<?php echo $tbl_support->CreatedDate->ViewAttributes() ?>>
<?php echo $tbl_support->CreatedDate->ListViewValue() ?></span>
<a id="<?php echo $tbl_support_list->PageObjName . "_row_" . $tbl_support_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_support->UpdatedBy->Visible) { // UpdatedBy ?>
		<td<?php echo $tbl_support->UpdatedBy->CellAttributes() ?>>
<span<?php echo $tbl_support->UpdatedBy->ViewAttributes() ?>>
<?php echo $tbl_support->UpdatedBy->ListViewValue() ?></span>
<a id="<?php echo $tbl_support_list->PageObjName . "_row_" . $tbl_support_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_support->UpdatedDate->Visible) { // UpdatedDate ?>
		<td<?php echo $tbl_support->UpdatedDate->CellAttributes() ?>>
<span<?php echo $tbl_support->UpdatedDate->ViewAttributes() ?>>
<?php echo $tbl_support->UpdatedDate->ListViewValue() ?></span>
<a id="<?php echo $tbl_support_list->PageObjName . "_row_" . $tbl_support_list->RowCnt ?>"></a></td>
	<?php } ?>
<?php

// Render list options (body, right)
$tbl_support_list->ListOptions->Render("body", "right", $tbl_support_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($tbl_support->CurrentAction <> "gridadd")
		$tbl_support_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($tbl_support->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($tbl_support_list->Recordset)
	$tbl_support_list->Recordset->Close();
?>
<?php if ($tbl_support_list->TotalRecs > 0) { ?>
<div class="ewGridLowerPanel">
<?php if ($tbl_support->CurrentAction <> "gridadd" && $tbl_support->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($tbl_support_list->Pager)) $tbl_support_list->Pager = new cNumericPager($tbl_support_list->StartRec, $tbl_support_list->DisplayRecs, $tbl_support_list->TotalRecs, $tbl_support_list->RecRange) ?>
<?php if ($tbl_support_list->Pager->RecordCount > 0) { ?>
<table cellspacing="0" class="ewStdTable"><tbody><tr><td>
<div class="pagination"><ul>
	<?php if ($tbl_support_list->Pager->FirstButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_support_list->PageUrl() ?>start=<?php echo $tbl_support_list->Pager->FirstButton->Start ?>"><?php echo $Language->Phrase("PagerFirst") ?></a></li>
	<?php } ?>
	<?php if ($tbl_support_list->Pager->PrevButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_support_list->PageUrl() ?>start=<?php echo $tbl_support_list->Pager->PrevButton->Start ?>"><?php echo $Language->Phrase("PagerPrevious") ?></a></li>
	<?php } ?>
	<?php foreach ($tbl_support_list->Pager->Items as $PagerItem) { ?>
		<li<?php if (!$PagerItem->Enabled) { echo " class=\" active\""; } ?>><a href="<?php if ($PagerItem->Enabled) { echo $tbl_support_list->PageUrl() . "start=" . $PagerItem->Start; } else { echo "#"; } ?>"><?php echo $PagerItem->Text ?></a></li>
	<?php } ?>
	<?php if ($tbl_support_list->Pager->NextButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_support_list->PageUrl() ?>start=<?php echo $tbl_support_list->Pager->NextButton->Start ?>"><?php echo $Language->Phrase("PagerNext") ?></a></li>
	<?php } ?>
	<?php if ($tbl_support_list->Pager->LastButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_support_list->PageUrl() ?>start=<?php echo $tbl_support_list->Pager->LastButton->Start ?>"><?php echo $Language->Phrase("PagerLast") ?></a></li>
	<?php } ?>
</ul></div>
</td>
<td>
	<?php if ($tbl_support_list->Pager->ButtonCount > 0) { ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php } ?>
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $tbl_support_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $tbl_support_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $tbl_support_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($tbl_support_list->SearchWhere == "0=101") { ?>
	<p><?php echo $Language->Phrase("EnterSearchCriteria") ?></p>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoRecord") ?></p>
	<?php } ?>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoPermission") ?></p>
	<?php } ?>
<?php } ?>
</td>
<?php if ($tbl_support_list->TotalRecs > 0) { ?>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
<input type="hidden" name="t" value="tbl_support">
<select name="<?php echo EW_TABLE_REC_PER_PAGE ?>" class="input-small" onchange="this.form.submit();">
<option value="20"<?php if ($tbl_support_list->DisplayRecs == 20) { ?> selected="selected"<?php } ?>>20</option>
<option value="40"<?php if ($tbl_support_list->DisplayRecs == 40) { ?> selected="selected"<?php } ?>>40</option>
<option value="60"<?php if ($tbl_support_list->DisplayRecs == 60) { ?> selected="selected"<?php } ?>>60</option>
<option value="ALL"<?php if ($tbl_support->getRecordsPerPage() == -1) { ?> selected="selected"<?php } ?>><?php echo $Language->Phrase("AllRecords") ?></option>
</select>
</td>
<?php } ?>
</tr></table>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($tbl_support_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
</div>
<?php } ?>
</td></tr></table>
<script type="text/javascript">
ftbl_supportlistsrch.Init();
ftbl_supportlist.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$tbl_support_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$tbl_support_list->Page_Terminate();
?>
