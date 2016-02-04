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

$tbl_representative_list = NULL; // Initialize page object first

class ctbl_representative_list extends ctbl_representative {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{AC00512B-B959-4ABC-B03E-21192746C63D}";

	// Table name
	var $TableName = 'tbl_representative';

	// Page object name
	var $PageObjName = 'tbl_representative_list';

	// Grid form hidden field names
	var $FormName = 'ftbl_representativelist';
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

		// Table object (tbl_representative)
		if (!isset($GLOBALS["tbl_representative"])) {
			$GLOBALS["tbl_representative"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["tbl_representative"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "tbl_representativeadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "tbl_representativedelete.php";
		$this->MultiUpdateUrl = "tbl_representativeupdate.php";

		// Table object (tbl_pensioner)
		if (!isset($GLOBALS['tbl_pensioner'])) $GLOBALS['tbl_pensioner'] = new ctbl_pensioner();

		// Table object (tbl_user)
		if (!isset($GLOBALS['tbl_user'])) $GLOBALS['tbl_user'] = new ctbl_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'tbl_representative', TRUE);

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
		$this->authID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();
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
			$this->authID->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->authID->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Advanced search WHERE clause based on QueryString
	function AdvancedSearchWhere() {
		global $Security;
		$sWhere = "";
		if (!$Security->CanSearch()) return "";
		$this->BuildSearchSql($sWhere, $this->authID, FALSE); // authID
		$this->BuildSearchSql($sWhere, $this->PensionerID, FALSE); // PensionerID
		$this->BuildSearchSql($sWhere, $this->fname, FALSE); // fname
		$this->BuildSearchSql($sWhere, $this->mname, FALSE); // mname
		$this->BuildSearchSql($sWhere, $this->lname, FALSE); // lname
		$this->BuildSearchSql($sWhere, $this->relToPensioner, FALSE); // relToPensioner
		$this->BuildSearchSql($sWhere, $this->ContactNo, FALSE); // ContactNo
		$this->BuildSearchSql($sWhere, $this->auth_Region, FALSE); // auth_Region
		$this->BuildSearchSql($sWhere, $this->auth_prov, FALSE); // auth_prov
		$this->BuildSearchSql($sWhere, $this->auth_city, FALSE); // auth_city
		$this->BuildSearchSql($sWhere, $this->auth_brgy, FALSE); // auth_brgy
		$this->BuildSearchSql($sWhere, $this->houseNo, FALSE); // houseNo
		$this->BuildSearchSql($sWhere, $this->CreatedBy, FALSE); // CreatedBy
		$this->BuildSearchSql($sWhere, $this->CreatedDate, FALSE); // CreatedDate
		$this->BuildSearchSql($sWhere, $this->UpdatedBy, FALSE); // UpdatedBy
		$this->BuildSearchSql($sWhere, $this->UpdatedDate, FALSE); // UpdatedDate

		// Set up search parm
		if ($sWhere <> "") {
			$this->Command = "search";
		}
		if ($this->Command == "search") {
			$this->authID->AdvancedSearch->Save(); // authID
			$this->PensionerID->AdvancedSearch->Save(); // PensionerID
			$this->fname->AdvancedSearch->Save(); // fname
			$this->mname->AdvancedSearch->Save(); // mname
			$this->lname->AdvancedSearch->Save(); // lname
			$this->relToPensioner->AdvancedSearch->Save(); // relToPensioner
			$this->ContactNo->AdvancedSearch->Save(); // ContactNo
			$this->auth_Region->AdvancedSearch->Save(); // auth_Region
			$this->auth_prov->AdvancedSearch->Save(); // auth_prov
			$this->auth_city->AdvancedSearch->Save(); // auth_city
			$this->auth_brgy->AdvancedSearch->Save(); // auth_brgy
			$this->houseNo->AdvancedSearch->Save(); // houseNo
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
		$this->BuildBasicSearchSQL($sWhere, $this->fname, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->mname, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->lname, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->ContactNo, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->houseNo, $Keyword);
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
		if ($this->authID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->PensionerID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->fname->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->mname->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->lname->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->relToPensioner->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->ContactNo->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->auth_Region->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->auth_prov->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->auth_city->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->auth_brgy->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->houseNo->AdvancedSearch->IssetSession())
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
		$this->authID->AdvancedSearch->UnsetSession();
		$this->PensionerID->AdvancedSearch->UnsetSession();
		$this->fname->AdvancedSearch->UnsetSession();
		$this->mname->AdvancedSearch->UnsetSession();
		$this->lname->AdvancedSearch->UnsetSession();
		$this->relToPensioner->AdvancedSearch->UnsetSession();
		$this->ContactNo->AdvancedSearch->UnsetSession();
		$this->auth_Region->AdvancedSearch->UnsetSession();
		$this->auth_prov->AdvancedSearch->UnsetSession();
		$this->auth_city->AdvancedSearch->UnsetSession();
		$this->auth_brgy->AdvancedSearch->UnsetSession();
		$this->houseNo->AdvancedSearch->UnsetSession();
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
		$this->authID->AdvancedSearch->Load();
		$this->PensionerID->AdvancedSearch->Load();
		$this->fname->AdvancedSearch->Load();
		$this->mname->AdvancedSearch->Load();
		$this->lname->AdvancedSearch->Load();
		$this->relToPensioner->AdvancedSearch->Load();
		$this->ContactNo->AdvancedSearch->Load();
		$this->auth_Region->AdvancedSearch->Load();
		$this->auth_prov->AdvancedSearch->Load();
		$this->auth_city->AdvancedSearch->Load();
		$this->auth_brgy->AdvancedSearch->Load();
		$this->houseNo->AdvancedSearch->Load();
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
			$this->UpdateSort($this->authID); // authID
			$this->UpdateSort($this->PensionerID); // PensionerID
			$this->UpdateSort($this->fname); // fname
			$this->UpdateSort($this->mname); // mname
			$this->UpdateSort($this->lname); // lname
			$this->UpdateSort($this->relToPensioner); // relToPensioner
			$this->UpdateSort($this->ContactNo); // ContactNo
			$this->UpdateSort($this->auth_Region); // auth_Region
			$this->UpdateSort($this->auth_prov); // auth_prov
			$this->UpdateSort($this->auth_city); // auth_city
			$this->UpdateSort($this->auth_brgy); // auth_brgy
			$this->UpdateSort($this->houseNo); // houseNo
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
				$this->authID->setSort("");
				$this->PensionerID->setSort("");
				$this->fname->setSort("");
				$this->mname->setSort("");
				$this->lname->setSort("");
				$this->relToPensioner->setSort("");
				$this->ContactNo->setSort("");
				$this->auth_Region->setSort("");
				$this->auth_prov->setSort("");
				$this->auth_city->setSort("");
				$this->auth_brgy->setSort("");
				$this->houseNo->setSort("");
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
		$oListOpt->Body = "<label class=\"checkbox\"><input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->authID->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event, this);'></label>";
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
		$item->Body = "<a class=\"btn btn-purple btn-sm\" href=\"\" onclick=\"ew_SubmitSelected(document.ftbl_representativelist, '" . $this->MultiDeleteUrl . "', ewLanguage.Phrase('DeleteMultiConfirmMsg'));return false;\">" . $Language->Phrase("DeleteSelectedLink") . "</a>";
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
				$item->Body = "<a class=\"btn btn-warning btn-sm\" href=\"\" onclick=\"ew_SubmitSelected(document.ftbl_representativelist, '" . ew_CurrentUrl() . "', null, '" . $action . "');return false;\">" . $name . "</a>";
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
		// authID

		$this->authID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_authID"]);
		if ($this->authID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->authID->AdvancedSearch->SearchOperator = @$_GET["z_authID"];

		// PensionerID
		$this->PensionerID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_PensionerID"]);
		if ($this->PensionerID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->PensionerID->AdvancedSearch->SearchOperator = @$_GET["z_PensionerID"];

		// fname
		$this->fname->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_fname"]);
		if ($this->fname->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->fname->AdvancedSearch->SearchOperator = @$_GET["z_fname"];

		// mname
		$this->mname->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_mname"]);
		if ($this->mname->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->mname->AdvancedSearch->SearchOperator = @$_GET["z_mname"];

		// lname
		$this->lname->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_lname"]);
		if ($this->lname->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->lname->AdvancedSearch->SearchOperator = @$_GET["z_lname"];

		// relToPensioner
		$this->relToPensioner->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_relToPensioner"]);
		if ($this->relToPensioner->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->relToPensioner->AdvancedSearch->SearchOperator = @$_GET["z_relToPensioner"];

		// ContactNo
		$this->ContactNo->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_ContactNo"]);
		if ($this->ContactNo->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->ContactNo->AdvancedSearch->SearchOperator = @$_GET["z_ContactNo"];

		// auth_Region
		$this->auth_Region->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_auth_Region"]);
		if ($this->auth_Region->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->auth_Region->AdvancedSearch->SearchOperator = @$_GET["z_auth_Region"];

		// auth_prov
		$this->auth_prov->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_auth_prov"]);
		if ($this->auth_prov->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->auth_prov->AdvancedSearch->SearchOperator = @$_GET["z_auth_prov"];

		// auth_city
		$this->auth_city->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_auth_city"]);
		if ($this->auth_city->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->auth_city->AdvancedSearch->SearchOperator = @$_GET["z_auth_city"];

		// auth_brgy
		$this->auth_brgy->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_auth_brgy"]);
		if ($this->auth_brgy->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->auth_brgy->AdvancedSearch->SearchOperator = @$_GET["z_auth_brgy"];

		// houseNo
		$this->houseNo->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_houseNo"]);
		if ($this->houseNo->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->houseNo->AdvancedSearch->SearchOperator = @$_GET["z_houseNo"];

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
		$this->ViewUrl = $this->GetViewUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->InlineEditUrl = $this->GetInlineEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->InlineCopyUrl = $this->GetInlineCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();

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
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// authID
			$this->authID->EditCustomAttributes = "";
			$this->authID->EditValue = ew_HtmlEncode($this->authID->AdvancedSearch->SearchValue);
			$this->authID->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->authID->FldCaption()));

			// PensionerID
			$this->PensionerID->EditCustomAttributes = "";
			$this->PensionerID->EditValue = ew_HtmlEncode($this->PensionerID->AdvancedSearch->SearchValue);
			$this->PensionerID->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->PensionerID->FldCaption()));

			// fname
			$this->fname->EditCustomAttributes = "";
			$this->fname->EditValue = ew_HtmlEncode($this->fname->AdvancedSearch->SearchValue);
			$this->fname->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->fname->FldCaption()));

			// mname
			$this->mname->EditCustomAttributes = "";
			$this->mname->EditValue = ew_HtmlEncode($this->mname->AdvancedSearch->SearchValue);
			$this->mname->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->mname->FldCaption()));

			// lname
			$this->lname->EditCustomAttributes = "";
			$this->lname->EditValue = ew_HtmlEncode($this->lname->AdvancedSearch->SearchValue);
			$this->lname->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->lname->FldCaption()));

			// relToPensioner
			$this->relToPensioner->EditCustomAttributes = "";

			// ContactNo
			$this->ContactNo->EditCustomAttributes = "";
			$this->ContactNo->EditValue = ew_HtmlEncode($this->ContactNo->AdvancedSearch->SearchValue);
			$this->ContactNo->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->ContactNo->FldCaption()));

			// auth_Region
			$this->auth_Region->EditCustomAttributes = "";
			if (trim(strval($this->auth_Region->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`region_code`" . ew_SearchString("=", $this->auth_Region->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER);
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
			if (trim(strval($this->auth_prov->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`prov_code`" . ew_SearchString("=", $this->auth_prov->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER);
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
			if (trim(strval($this->auth_city->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`city_code`" . ew_SearchString("=", $this->auth_city->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER);
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
			if (trim(strval($this->auth_brgy->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`brgy_code`" . ew_SearchString("=", $this->auth_brgy->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER);
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
			$this->houseNo->EditValue = ew_HtmlEncode($this->houseNo->AdvancedSearch->SearchValue);
			$this->houseNo->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->houseNo->FldCaption()));

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
		$this->authID->AdvancedSearch->Load();
		$this->PensionerID->AdvancedSearch->Load();
		$this->fname->AdvancedSearch->Load();
		$this->mname->AdvancedSearch->Load();
		$this->lname->AdvancedSearch->Load();
		$this->relToPensioner->AdvancedSearch->Load();
		$this->ContactNo->AdvancedSearch->Load();
		$this->auth_Region->AdvancedSearch->Load();
		$this->auth_prov->AdvancedSearch->Load();
		$this->auth_city->AdvancedSearch->Load();
		$this->auth_brgy->AdvancedSearch->Load();
		$this->houseNo->AdvancedSearch->Load();
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
		$table = 'tbl_representative';
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
if (!isset($tbl_representative_list)) $tbl_representative_list = new ctbl_representative_list();

// Page init
$tbl_representative_list->Page_Init();

// Page main
$tbl_representative_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$tbl_representative_list->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var tbl_representative_list = new ew_Page("tbl_representative_list");
tbl_representative_list.PageID = "list"; // Page ID
var EW_PAGE_ID = tbl_representative_list.PageID; // For backward compatibility

// Form object
var ftbl_representativelist = new ew_Form("ftbl_representativelist");
ftbl_representativelist.FormKeyCountName = '<?php echo $tbl_representative_list->FormKeyCountName ?>';

// Form_CustomValidate event
ftbl_representativelist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftbl_representativelist.ValidateRequired = true;
<?php } else { ?>
ftbl_representativelist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
ftbl_representativelist.Lists["x_relToPensioner"] = {"LinkField":"x_RelationID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_representativelist.Lists["x_auth_Region"] = {"LinkField":"x_region_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_region_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_representativelist.Lists["x_auth_prov"] = {"LinkField":"x_prov_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_prov_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_representativelist.Lists["x_auth_city"] = {"LinkField":"x_city_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_city_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_representativelist.Lists["x_auth_brgy"] = {"LinkField":"x_brgy_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_brgy_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
var ftbl_representativelistsrch = new ew_Form("ftbl_representativelistsrch");

// Validate function for search
ftbl_representativelistsrch.Validate = function(fobj) {
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
ftbl_representativelistsrch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftbl_representativelistsrch.ValidateRequired = true; // Use JavaScript validation
<?php } else { ?>
ftbl_representativelistsrch.ValidateRequired = false; // No JavaScript validation
<?php } ?>

// Dynamic selection lists
ftbl_representativelistsrch.Lists["x_auth_Region"] = {"LinkField":"x_region_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_region_name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
ftbl_representativelistsrch.Lists["x_auth_prov"] = {"LinkField":"x_prov_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_prov_name","","",""],"ParentFields":["x_auth_Region"],"FilterFields":["x_region_code"],"Options":[]};
ftbl_representativelistsrch.Lists["x_auth_city"] = {"LinkField":"x_city_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_city_name","","",""],"ParentFields":["x_auth_prov"],"FilterFields":["x_prov_code"],"Options":[]};
ftbl_representativelistsrch.Lists["x_auth_brgy"] = {"LinkField":"x_brgy_code","Ajax":true,"AutoFill":false,"DisplayFields":["x_brgy_name","","",""],"ParentFields":["x_auth_city"],"FilterFields":["x_city_code"],"Options":[]};
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php //$Breadcrumb->Render(); ?>
<?php if ($tbl_representative->getCurrentMasterTable() == "" && $tbl_representative_list->ExportOptions->Visible()) { ?>
<div class="ewListExportOptions"><?php $tbl_representative_list->ExportOptions->Render("body") ?></div>
<?php } ?>
<?php if (($tbl_representative->Export == "") || (EW_EXPORT_MASTER_RECORD && $tbl_representative->Export == "print")) { ?>
<?php
$gsMasterReturnUrl = "tbl_pensionerlist.php";
if ($tbl_representative_list->DbMasterFilter <> "" && $tbl_representative->getCurrentMasterTable() == "tbl_pensioner") {
	if ($tbl_representative_list->MasterRecordExists) {
		if ($tbl_representative->getCurrentMasterTable() == $tbl_representative->TableVar) $gsMasterReturnUrl .= "?" . EW_TABLE_SHOW_MASTER . "=";
?>
<?php if ($tbl_representative_list->ExportOptions->Visible()) { ?>
<div class="ewListExportOptions"><?php $tbl_representative_list->ExportOptions->Render("body") ?></div>
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
		$tbl_representative_list->TotalRecs = $tbl_representative->SelectRecordCount();
	} else {
		if ($tbl_representative_list->Recordset = $tbl_representative_list->LoadRecordset())
			$tbl_representative_list->TotalRecs = $tbl_representative_list->Recordset->RecordCount();
	}
	$tbl_representative_list->StartRec = 1;
	if ($tbl_representative_list->DisplayRecs <= 0 || ($tbl_representative->Export <> "" && $tbl_representative->ExportAll)) // Display all records
		$tbl_representative_list->DisplayRecs = $tbl_representative_list->TotalRecs;
	if (!($tbl_representative->Export <> "" && $tbl_representative->ExportAll))
		$tbl_representative_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$tbl_representative_list->Recordset = $tbl_representative_list->LoadRecordset($tbl_representative_list->StartRec-1, $tbl_representative_list->DisplayRecs);
$tbl_representative_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($tbl_representative->Export == "" && $tbl_representative->CurrentAction == "") { ?>
<form name="ftbl_representativelistsrch" id="ftbl_representativelistsrch" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>">
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
	<div id="ftbl_representativelistsrch_SearchPanel">
		<input type="hidden" name="cmd" value="search">
		<input type="hidden" name="t" value="tbl_representative">
		<div class="ewBasicSearch">
<?php
if ($gsSearchError == "")
	$tbl_representative_list->LoadAdvancedSearch(); // Load advanced search

// Render for search
$tbl_representative->RowType = EW_ROWTYPE_SEARCH;

// Render row
$tbl_representative->ResetAttrs();
$tbl_representative_list->RenderRow();
?>
<div id="xsr_1" class="ewRow">
<?php if ($tbl_representative->auth_Region->Visible) { // auth_Region ?>
	<span id="xsc_auth_Region" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_representative->auth_Region->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_auth_Region" id="z_auth_Region" value="="></span>
		<span class="control-group ewSearchField">
<?php $tbl_representative->auth_Region->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x_auth_prov']); " . @$tbl_representative->auth_Region->EditAttrs["onchange"]; ?>
<select data-field="x_auth_Region" id="x_auth_Region" name="x_auth_Region"<?php echo $tbl_representative->auth_Region->EditAttributes() ?>>
<?php
if (is_array($tbl_representative->auth_Region->EditValue)) {
	$arwrk = $tbl_representative->auth_Region->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_representative->auth_Region->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
	</span>
<?php } ?>
</div>
<div id="xsr_2" class="ewRow">
<?php if ($tbl_representative->auth_prov->Visible) { // auth_prov ?>
	<span id="xsc_auth_prov" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_representative->auth_prov->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_auth_prov" id="z_auth_prov" value="="></span>
		<span class="control-group ewSearchField">
<?php $tbl_representative->auth_prov->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x_auth_city']); " . @$tbl_representative->auth_prov->EditAttrs["onchange"]; ?>
<select data-field="x_auth_prov" id="x_auth_prov" name="x_auth_prov"<?php echo $tbl_representative->auth_prov->EditAttributes() ?>>
<?php
if (is_array($tbl_representative->auth_prov->EditValue)) {
	$arwrk = $tbl_representative->auth_prov->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_representative->auth_prov->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
	</span>
<?php } ?>
</div>
<div id="xsr_3" class="ewRow">
<?php if ($tbl_representative->auth_city->Visible) { // auth_city ?>
	<span id="xsc_auth_city" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_representative->auth_city->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_auth_city" id="z_auth_city" value="="></span>
		<span class="control-group ewSearchField">
<?php $tbl_representative->auth_city->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x_auth_brgy']); " . @$tbl_representative->auth_city->EditAttrs["onchange"]; ?>
<select data-field="x_auth_city" id="x_auth_city" name="x_auth_city"<?php echo $tbl_representative->auth_city->EditAttributes() ?>>
<?php
if (is_array($tbl_representative->auth_city->EditValue)) {
	$arwrk = $tbl_representative->auth_city->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_representative->auth_city->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
	</span>
<?php } ?>
</div>
<div id="xsr_4" class="ewRow">
<?php if ($tbl_representative->auth_brgy->Visible) { // auth_brgy ?>
	<span id="xsc_auth_brgy" class="ewCell">
		<span class="ewSearchCaption"><?php echo $tbl_representative->auth_brgy->FldCaption() ?></span>
		<span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_auth_brgy" id="z_auth_brgy" value="="></span>
		<span class="control-group ewSearchField">
<select data-field="x_auth_brgy" id="x_auth_brgy" name="x_auth_brgy"<?php echo $tbl_representative->auth_brgy->EditAttributes() ?>>
<?php
if (is_array($tbl_representative->auth_brgy->EditValue)) {
	$arwrk = $tbl_representative->auth_brgy->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tbl_representative->auth_brgy->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
	</span>
<?php } ?>
</div>
<div id="xsr_5" class="row">
	<div class="col-xs-12 col-sm-4">
	<div class="input-group">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="form-control search-query" value="<?php echo ew_HtmlEncode($tbl_representative_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo $Language->Phrase("Search") ?>">
	<span class="input-group-btn">
	<button class="btn btn-purple btn-sm" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $Language->Phrase("QuickSearchBtn") ?> <i class="icon-search icon-on-right bigger-110"></i></button>&nbsp;
	<a type="button" class="btn btn-success btn-sm" href="<?php echo $tbl_representative_list->PageUrl() ?>cmd=reset">ShowAll <i class="icon-refresh icon-on-right bigger-110"></i></a>
	</span>
	</div>
	</div>
	<div class="btn-group ewButtonGroup">
	<!--<a class="btn ewShowAll" href="<?php echo $tbl_representative_list->PageUrl() ?>cmd=reset"><?php echo $Language->Phrase("ShowAll") ?></a> -->
</div>
<div id="xsr_6" class="radio">
	<label><input class="ace" type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="="<?php if ($tbl_representative_list->BasicSearch->getType() == "=") { ?> checked="checked"<?php } ?>><span class="lbl"><?php echo $Language->Phrase("ExactPhrase") ?></span></label>
	<label><input class="ace" type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="AND"<?php if ($tbl_representative_list->BasicSearch->getType() == "AND") { ?> checked="checked"<?php } ?>><span class="lbl"><?php echo $Language->Phrase("AllWord") ?></span></label>
	<label><input class="ace" type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="OR"<?php if ($tbl_representative_list->BasicSearch->getType() == "OR") { ?> checked="checked"<?php } ?>><span class="lbl"><?php echo $Language->Phrase("AnyWord") ?></span></label>
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
<?php $tbl_representative_list->ShowPageHeader(); ?>
<?php
$tbl_representative_list->ShowMessage();
?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridUpperPanel">
<?php if ($tbl_representative->CurrentAction <> "gridadd" && $tbl_representative->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($tbl_representative_list->Pager)) $tbl_representative_list->Pager = new cNumericPager($tbl_representative_list->StartRec, $tbl_representative_list->DisplayRecs, $tbl_representative_list->TotalRecs, $tbl_representative_list->RecRange) ?>
<?php if ($tbl_representative_list->Pager->RecordCount > 0) { ?>
<table cellspacing="0" class="ewStdTable"><tbody><tr><td>
<div class="pagination"><ul>
	<?php if ($tbl_representative_list->Pager->FirstButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_representative_list->PageUrl() ?>start=<?php echo $tbl_representative_list->Pager->FirstButton->Start ?>"><?php echo $Language->Phrase("PagerFirst") ?></a></li>
	<?php } ?>
	<?php if ($tbl_representative_list->Pager->PrevButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_representative_list->PageUrl() ?>start=<?php echo $tbl_representative_list->Pager->PrevButton->Start ?>"><?php echo $Language->Phrase("PagerPrevious") ?></a></li>
	<?php } ?>
	<?php foreach ($tbl_representative_list->Pager->Items as $PagerItem) { ?>
		<li<?php if (!$PagerItem->Enabled) { echo " class=\" active\""; } ?>><a href="<?php if ($PagerItem->Enabled) { echo $tbl_representative_list->PageUrl() . "start=" . $PagerItem->Start; } else { echo "#"; } ?>"><?php echo $PagerItem->Text ?></a></li>
	<?php } ?>
	<?php if ($tbl_representative_list->Pager->NextButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_representative_list->PageUrl() ?>start=<?php echo $tbl_representative_list->Pager->NextButton->Start ?>"><?php echo $Language->Phrase("PagerNext") ?></a></li>
	<?php } ?>
	<?php if ($tbl_representative_list->Pager->LastButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_representative_list->PageUrl() ?>start=<?php echo $tbl_representative_list->Pager->LastButton->Start ?>"><?php echo $Language->Phrase("PagerLast") ?></a></li>
	<?php } ?>
</ul></div>
</td>
<td>
	<?php if ($tbl_representative_list->Pager->ButtonCount > 0) { ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php } ?>
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $tbl_representative_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $tbl_representative_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $tbl_representative_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($tbl_representative_list->SearchWhere == "0=101") { ?>
	<p><?php echo $Language->Phrase("EnterSearchCriteria") ?></p>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoRecord") ?></p>
	<?php } ?>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoPermission") ?></p>
	<?php } ?>
<?php } ?>
</td>
<?php if ($tbl_representative_list->TotalRecs > 0) { ?>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
<input type="hidden" name="t" value="tbl_representative">
<select name="<?php echo EW_TABLE_REC_PER_PAGE ?>" class="input-small" onchange="this.form.submit();">
<option value="20"<?php if ($tbl_representative_list->DisplayRecs == 20) { ?> selected="selected"<?php } ?>>20</option>
<option value="40"<?php if ($tbl_representative_list->DisplayRecs == 40) { ?> selected="selected"<?php } ?>>40</option>
<option value="60"<?php if ($tbl_representative_list->DisplayRecs == 60) { ?> selected="selected"<?php } ?>>60</option>
<option value="ALL"<?php if ($tbl_representative->getRecordsPerPage() == -1) { ?> selected="selected"<?php } ?>><?php echo $Language->Phrase("AllRecords") ?></option>
</select>
</td>
<?php } ?>
</tr></table>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($tbl_representative_list->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
</div>
<form name="ftbl_representativelist" id="ftbl_representativelist" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="tbl_representative">
<div id="gmp_tbl_representative" class="ewGridMiddlePanel">
<?php if ($tbl_representative_list->TotalRecs > 0) { ?>
<table id="tbl_tbl_representativelist" class="ewTable ewTableSeparate">
<?php echo $tbl_representative->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$tbl_representative_list->RenderListOptions();

// Render list options (header, left)
$tbl_representative_list->ListOptions->Render("header", "left");
?>
<?php if ($tbl_representative->authID->Visible) { // authID ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->authID) == "") { ?>
		<td><div id="elh_tbl_representative_authID" class="tbl_representative_authID"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->authID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_representative->SortUrl($tbl_representative->authID) ?>',1);"><div id="elh_tbl_representative_authID" class="tbl_representative_authID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->authID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->authID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->authID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_representative->PensionerID->Visible) { // PensionerID ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->PensionerID) == "") { ?>
		<td><div id="elh_tbl_representative_PensionerID" class="tbl_representative_PensionerID"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->PensionerID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_representative->SortUrl($tbl_representative->PensionerID) ?>',1);"><div id="elh_tbl_representative_PensionerID" class="tbl_representative_PensionerID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->PensionerID->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->PensionerID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->PensionerID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_representative->fname->Visible) { // fname ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->fname) == "") { ?>
		<td><div id="elh_tbl_representative_fname" class="tbl_representative_fname"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->fname->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_representative->SortUrl($tbl_representative->fname) ?>',1);"><div id="elh_tbl_representative_fname" class="tbl_representative_fname">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->fname->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->fname->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->fname->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_representative->mname->Visible) { // mname ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->mname) == "") { ?>
		<td><div id="elh_tbl_representative_mname" class="tbl_representative_mname"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->mname->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_representative->SortUrl($tbl_representative->mname) ?>',1);"><div id="elh_tbl_representative_mname" class="tbl_representative_mname">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->mname->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->mname->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->mname->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_representative->lname->Visible) { // lname ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->lname) == "") { ?>
		<td><div id="elh_tbl_representative_lname" class="tbl_representative_lname"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->lname->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_representative->SortUrl($tbl_representative->lname) ?>',1);"><div id="elh_tbl_representative_lname" class="tbl_representative_lname">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->lname->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->lname->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->lname->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_representative->relToPensioner->Visible) { // relToPensioner ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->relToPensioner) == "") { ?>
		<td><div id="elh_tbl_representative_relToPensioner" class="tbl_representative_relToPensioner"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->relToPensioner->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_representative->SortUrl($tbl_representative->relToPensioner) ?>',1);"><div id="elh_tbl_representative_relToPensioner" class="tbl_representative_relToPensioner">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->relToPensioner->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->relToPensioner->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->relToPensioner->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_representative->ContactNo->Visible) { // ContactNo ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->ContactNo) == "") { ?>
		<td><div id="elh_tbl_representative_ContactNo" class="tbl_representative_ContactNo"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->ContactNo->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_representative->SortUrl($tbl_representative->ContactNo) ?>',1);"><div id="elh_tbl_representative_ContactNo" class="tbl_representative_ContactNo">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->ContactNo->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->ContactNo->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->ContactNo->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_representative->auth_Region->Visible) { // auth_Region ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->auth_Region) == "") { ?>
		<td><div id="elh_tbl_representative_auth_Region" class="tbl_representative_auth_Region"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->auth_Region->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_representative->SortUrl($tbl_representative->auth_Region) ?>',1);"><div id="elh_tbl_representative_auth_Region" class="tbl_representative_auth_Region">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->auth_Region->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->auth_Region->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->auth_Region->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_representative->auth_prov->Visible) { // auth_prov ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->auth_prov) == "") { ?>
		<td><div id="elh_tbl_representative_auth_prov" class="tbl_representative_auth_prov"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->auth_prov->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_representative->SortUrl($tbl_representative->auth_prov) ?>',1);"><div id="elh_tbl_representative_auth_prov" class="tbl_representative_auth_prov">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->auth_prov->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->auth_prov->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->auth_prov->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_representative->auth_city->Visible) { // auth_city ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->auth_city) == "") { ?>
		<td><div id="elh_tbl_representative_auth_city" class="tbl_representative_auth_city"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->auth_city->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_representative->SortUrl($tbl_representative->auth_city) ?>',1);"><div id="elh_tbl_representative_auth_city" class="tbl_representative_auth_city">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->auth_city->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->auth_city->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->auth_city->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_representative->auth_brgy->Visible) { // auth_brgy ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->auth_brgy) == "") { ?>
		<td><div id="elh_tbl_representative_auth_brgy" class="tbl_representative_auth_brgy"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->auth_brgy->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_representative->SortUrl($tbl_representative->auth_brgy) ?>',1);"><div id="elh_tbl_representative_auth_brgy" class="tbl_representative_auth_brgy">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->auth_brgy->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->auth_brgy->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->auth_brgy->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_representative->houseNo->Visible) { // houseNo ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->houseNo) == "") { ?>
		<td><div id="elh_tbl_representative_houseNo" class="tbl_representative_houseNo"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->houseNo->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_representative->SortUrl($tbl_representative->houseNo) ?>',1);"><div id="elh_tbl_representative_houseNo" class="tbl_representative_houseNo">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->houseNo->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->houseNo->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->houseNo->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_representative->CreatedBy->Visible) { // CreatedBy ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->CreatedBy) == "") { ?>
		<td><div id="elh_tbl_representative_CreatedBy" class="tbl_representative_CreatedBy"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->CreatedBy->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_representative->SortUrl($tbl_representative->CreatedBy) ?>',1);"><div id="elh_tbl_representative_CreatedBy" class="tbl_representative_CreatedBy">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->CreatedBy->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->CreatedBy->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->CreatedBy->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_representative->CreatedDate->Visible) { // CreatedDate ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->CreatedDate) == "") { ?>
		<td><div id="elh_tbl_representative_CreatedDate" class="tbl_representative_CreatedDate"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->CreatedDate->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_representative->SortUrl($tbl_representative->CreatedDate) ?>',1);"><div id="elh_tbl_representative_CreatedDate" class="tbl_representative_CreatedDate">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->CreatedDate->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->CreatedDate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->CreatedDate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_representative->UpdatedBy->Visible) { // UpdatedBy ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->UpdatedBy) == "") { ?>
		<td><div id="elh_tbl_representative_UpdatedBy" class="tbl_representative_UpdatedBy"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->UpdatedBy->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_representative->SortUrl($tbl_representative->UpdatedBy) ?>',1);"><div id="elh_tbl_representative_UpdatedBy" class="tbl_representative_UpdatedBy">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->UpdatedBy->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->UpdatedBy->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->UpdatedBy->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tbl_representative->UpdatedDate->Visible) { // UpdatedDate ?>
	<?php if ($tbl_representative->SortUrl($tbl_representative->UpdatedDate) == "") { ?>
		<td><div id="elh_tbl_representative_UpdatedDate" class="tbl_representative_UpdatedDate"><div class="ewTableHeaderCaption"><?php echo $tbl_representative->UpdatedDate->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tbl_representative->SortUrl($tbl_representative->UpdatedDate) ?>',1);"><div id="elh_tbl_representative_UpdatedDate" class="tbl_representative_UpdatedDate">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tbl_representative->UpdatedDate->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tbl_representative->UpdatedDate->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tbl_representative->UpdatedDate->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$tbl_representative_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($tbl_representative->ExportAll && $tbl_representative->Export <> "") {
	$tbl_representative_list->StopRec = $tbl_representative_list->TotalRecs;
} else {

	// Set the last record to display
	if ($tbl_representative_list->TotalRecs > $tbl_representative_list->StartRec + $tbl_representative_list->DisplayRecs - 1)
		$tbl_representative_list->StopRec = $tbl_representative_list->StartRec + $tbl_representative_list->DisplayRecs - 1;
	else
		$tbl_representative_list->StopRec = $tbl_representative_list->TotalRecs;
}
$tbl_representative_list->RecCnt = $tbl_representative_list->StartRec - 1;
if ($tbl_representative_list->Recordset && !$tbl_representative_list->Recordset->EOF) {
	$tbl_representative_list->Recordset->MoveFirst();
	if (!$bSelectLimit && $tbl_representative_list->StartRec > 1)
		$tbl_representative_list->Recordset->Move($tbl_representative_list->StartRec - 1);
} elseif (!$tbl_representative->AllowAddDeleteRow && $tbl_representative_list->StopRec == 0) {
	$tbl_representative_list->StopRec = $tbl_representative->GridAddRowCount;
}

// Initialize aggregate
$tbl_representative->RowType = EW_ROWTYPE_AGGREGATEINIT;
$tbl_representative->ResetAttrs();
$tbl_representative_list->RenderRow();
while ($tbl_representative_list->RecCnt < $tbl_representative_list->StopRec) {
	$tbl_representative_list->RecCnt++;
	if (intval($tbl_representative_list->RecCnt) >= intval($tbl_representative_list->StartRec)) {
		$tbl_representative_list->RowCnt++;

		// Set up key count
		$tbl_representative_list->KeyCount = $tbl_representative_list->RowIndex;

		// Init row class and style
		$tbl_representative->ResetAttrs();
		$tbl_representative->CssClass = "";
		if ($tbl_representative->CurrentAction == "gridadd") {
		} else {
			$tbl_representative_list->LoadRowValues($tbl_representative_list->Recordset); // Load row values
		}
		$tbl_representative->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$tbl_representative->RowAttrs = array_merge($tbl_representative->RowAttrs, array('data-rowindex'=>$tbl_representative_list->RowCnt, 'id'=>'r' . $tbl_representative_list->RowCnt . '_tbl_representative', 'data-rowtype'=>$tbl_representative->RowType));

		// Render row
		$tbl_representative_list->RenderRow();

		// Render list options
		$tbl_representative_list->RenderListOptions();
?>
	<tr<?php echo $tbl_representative->RowAttributes() ?>>
<?php

// Render list options (body, left)
$tbl_representative_list->ListOptions->Render("body", "left", $tbl_representative_list->RowCnt);
?>
	<?php if ($tbl_representative->authID->Visible) { // authID ?>
		<td<?php echo $tbl_representative->authID->CellAttributes() ?>>
<span<?php echo $tbl_representative->authID->ViewAttributes() ?>>
<?php echo $tbl_representative->authID->ListViewValue() ?></span>
<a id="<?php echo $tbl_representative_list->PageObjName . "_row_" . $tbl_representative_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_representative->PensionerID->Visible) { // PensionerID ?>
		<td<?php echo $tbl_representative->PensionerID->CellAttributes() ?>>
<span<?php echo $tbl_representative->PensionerID->ViewAttributes() ?>>
<?php echo $tbl_representative->PensionerID->ListViewValue() ?></span>
<a id="<?php echo $tbl_representative_list->PageObjName . "_row_" . $tbl_representative_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_representative->fname->Visible) { // fname ?>
		<td<?php echo $tbl_representative->fname->CellAttributes() ?>>
<span<?php echo $tbl_representative->fname->ViewAttributes() ?>>
<?php echo $tbl_representative->fname->ListViewValue() ?></span>
<a id="<?php echo $tbl_representative_list->PageObjName . "_row_" . $tbl_representative_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_representative->mname->Visible) { // mname ?>
		<td<?php echo $tbl_representative->mname->CellAttributes() ?>>
<span<?php echo $tbl_representative->mname->ViewAttributes() ?>>
<?php echo $tbl_representative->mname->ListViewValue() ?></span>
<a id="<?php echo $tbl_representative_list->PageObjName . "_row_" . $tbl_representative_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_representative->lname->Visible) { // lname ?>
		<td<?php echo $tbl_representative->lname->CellAttributes() ?>>
<span<?php echo $tbl_representative->lname->ViewAttributes() ?>>
<?php echo $tbl_representative->lname->ListViewValue() ?></span>
<a id="<?php echo $tbl_representative_list->PageObjName . "_row_" . $tbl_representative_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_representative->relToPensioner->Visible) { // relToPensioner ?>
		<td<?php echo $tbl_representative->relToPensioner->CellAttributes() ?>>
<span<?php echo $tbl_representative->relToPensioner->ViewAttributes() ?>>
<?php echo $tbl_representative->relToPensioner->ListViewValue() ?></span>
<a id="<?php echo $tbl_representative_list->PageObjName . "_row_" . $tbl_representative_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_representative->ContactNo->Visible) { // ContactNo ?>
		<td<?php echo $tbl_representative->ContactNo->CellAttributes() ?>>
<span<?php echo $tbl_representative->ContactNo->ViewAttributes() ?>>
<?php echo $tbl_representative->ContactNo->ListViewValue() ?></span>
<a id="<?php echo $tbl_representative_list->PageObjName . "_row_" . $tbl_representative_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_representative->auth_Region->Visible) { // auth_Region ?>
		<td<?php echo $tbl_representative->auth_Region->CellAttributes() ?>>
<span<?php echo $tbl_representative->auth_Region->ViewAttributes() ?>>
<?php echo $tbl_representative->auth_Region->ListViewValue() ?></span>
<a id="<?php echo $tbl_representative_list->PageObjName . "_row_" . $tbl_representative_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_representative->auth_prov->Visible) { // auth_prov ?>
		<td<?php echo $tbl_representative->auth_prov->CellAttributes() ?>>
<span<?php echo $tbl_representative->auth_prov->ViewAttributes() ?>>
<?php echo $tbl_representative->auth_prov->ListViewValue() ?></span>
<a id="<?php echo $tbl_representative_list->PageObjName . "_row_" . $tbl_representative_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_representative->auth_city->Visible) { // auth_city ?>
		<td<?php echo $tbl_representative->auth_city->CellAttributes() ?>>
<span<?php echo $tbl_representative->auth_city->ViewAttributes() ?>>
<?php echo $tbl_representative->auth_city->ListViewValue() ?></span>
<a id="<?php echo $tbl_representative_list->PageObjName . "_row_" . $tbl_representative_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_representative->auth_brgy->Visible) { // auth_brgy ?>
		<td<?php echo $tbl_representative->auth_brgy->CellAttributes() ?>>
<span<?php echo $tbl_representative->auth_brgy->ViewAttributes() ?>>
<?php echo $tbl_representative->auth_brgy->ListViewValue() ?></span>
<a id="<?php echo $tbl_representative_list->PageObjName . "_row_" . $tbl_representative_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_representative->houseNo->Visible) { // houseNo ?>
		<td<?php echo $tbl_representative->houseNo->CellAttributes() ?>>
<span<?php echo $tbl_representative->houseNo->ViewAttributes() ?>>
<?php echo $tbl_representative->houseNo->ListViewValue() ?></span>
<a id="<?php echo $tbl_representative_list->PageObjName . "_row_" . $tbl_representative_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_representative->CreatedBy->Visible) { // CreatedBy ?>
		<td<?php echo $tbl_representative->CreatedBy->CellAttributes() ?>>
<span<?php echo $tbl_representative->CreatedBy->ViewAttributes() ?>>
<?php echo $tbl_representative->CreatedBy->ListViewValue() ?></span>
<a id="<?php echo $tbl_representative_list->PageObjName . "_row_" . $tbl_representative_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_representative->CreatedDate->Visible) { // CreatedDate ?>
		<td<?php echo $tbl_representative->CreatedDate->CellAttributes() ?>>
<span<?php echo $tbl_representative->CreatedDate->ViewAttributes() ?>>
<?php echo $tbl_representative->CreatedDate->ListViewValue() ?></span>
<a id="<?php echo $tbl_representative_list->PageObjName . "_row_" . $tbl_representative_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_representative->UpdatedBy->Visible) { // UpdatedBy ?>
		<td<?php echo $tbl_representative->UpdatedBy->CellAttributes() ?>>
<span<?php echo $tbl_representative->UpdatedBy->ViewAttributes() ?>>
<?php echo $tbl_representative->UpdatedBy->ListViewValue() ?></span>
<a id="<?php echo $tbl_representative_list->PageObjName . "_row_" . $tbl_representative_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tbl_representative->UpdatedDate->Visible) { // UpdatedDate ?>
		<td<?php echo $tbl_representative->UpdatedDate->CellAttributes() ?>>
<span<?php echo $tbl_representative->UpdatedDate->ViewAttributes() ?>>
<?php echo $tbl_representative->UpdatedDate->ListViewValue() ?></span>
<a id="<?php echo $tbl_representative_list->PageObjName . "_row_" . $tbl_representative_list->RowCnt ?>"></a></td>
	<?php } ?>
<?php

// Render list options (body, right)
$tbl_representative_list->ListOptions->Render("body", "right", $tbl_representative_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($tbl_representative->CurrentAction <> "gridadd")
		$tbl_representative_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($tbl_representative->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($tbl_representative_list->Recordset)
	$tbl_representative_list->Recordset->Close();
?>
<?php if ($tbl_representative_list->TotalRecs > 0) { ?>
<div class="ewGridLowerPanel">
<?php if ($tbl_representative->CurrentAction <> "gridadd" && $tbl_representative->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($tbl_representative_list->Pager)) $tbl_representative_list->Pager = new cNumericPager($tbl_representative_list->StartRec, $tbl_representative_list->DisplayRecs, $tbl_representative_list->TotalRecs, $tbl_representative_list->RecRange) ?>
<?php if ($tbl_representative_list->Pager->RecordCount > 0) { ?>
<table cellspacing="0" class="ewStdTable"><tbody><tr><td>
<div class="pagination"><ul>
	<?php if ($tbl_representative_list->Pager->FirstButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_representative_list->PageUrl() ?>start=<?php echo $tbl_representative_list->Pager->FirstButton->Start ?>"><?php echo $Language->Phrase("PagerFirst") ?></a></li>
	<?php } ?>
	<?php if ($tbl_representative_list->Pager->PrevButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_representative_list->PageUrl() ?>start=<?php echo $tbl_representative_list->Pager->PrevButton->Start ?>"><?php echo $Language->Phrase("PagerPrevious") ?></a></li>
	<?php } ?>
	<?php foreach ($tbl_representative_list->Pager->Items as $PagerItem) { ?>
		<li<?php if (!$PagerItem->Enabled) { echo " class=\" active\""; } ?>><a href="<?php if ($PagerItem->Enabled) { echo $tbl_representative_list->PageUrl() . "start=" . $PagerItem->Start; } else { echo "#"; } ?>"><?php echo $PagerItem->Text ?></a></li>
	<?php } ?>
	<?php if ($tbl_representative_list->Pager->NextButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_representative_list->PageUrl() ?>start=<?php echo $tbl_representative_list->Pager->NextButton->Start ?>"><?php echo $Language->Phrase("PagerNext") ?></a></li>
	<?php } ?>
	<?php if ($tbl_representative_list->Pager->LastButton->Enabled) { ?>
	<li><a href="<?php echo $tbl_representative_list->PageUrl() ?>start=<?php echo $tbl_representative_list->Pager->LastButton->Start ?>"><?php echo $Language->Phrase("PagerLast") ?></a></li>
	<?php } ?>
</ul></div>
</td>
<td>
	<?php if ($tbl_representative_list->Pager->ButtonCount > 0) { ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php } ?>
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $tbl_representative_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $tbl_representative_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $tbl_representative_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($tbl_representative_list->SearchWhere == "0=101") { ?>
	<p><?php echo $Language->Phrase("EnterSearchCriteria") ?></p>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoRecord") ?></p>
	<?php } ?>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoPermission") ?></p>
	<?php } ?>
<?php } ?>
</td>
<?php if ($tbl_representative_list->TotalRecs > 0) { ?>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
<input type="hidden" name="t" value="tbl_representative">
<select name="<?php echo EW_TABLE_REC_PER_PAGE ?>" class="input-small" onchange="this.form.submit();">
<option value="20"<?php if ($tbl_representative_list->DisplayRecs == 20) { ?> selected="selected"<?php } ?>>20</option>
<option value="40"<?php if ($tbl_representative_list->DisplayRecs == 40) { ?> selected="selected"<?php } ?>>40</option>
<option value="60"<?php if ($tbl_representative_list->DisplayRecs == 60) { ?> selected="selected"<?php } ?>>60</option>
<option value="ALL"<?php if ($tbl_representative->getRecordsPerPage() == -1) { ?> selected="selected"<?php } ?>><?php echo $Language->Phrase("AllRecords") ?></option>
</select>
</td>
<?php } ?>
</tr></table>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($tbl_representative_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
</div>
<?php } ?>
</td></tr></table>
<script type="text/javascript">
ftbl_representativelistsrch.Init();
ftbl_representativelist.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$tbl_representative_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$tbl_representative_list->Page_Terminate();
?>
